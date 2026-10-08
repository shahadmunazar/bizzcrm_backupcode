@extends('layouts.main');
@section('content')
@section('title', 'Salary Slip')

@push('styles')
<style>
    @media print {
        .page-wrapper {
            margin: 0;
            padding: 0;
            border: none;
        }
        .sidebar, .header, .head-icons, .modal, .btn, .ti-printer, .ti-refresh-dot, .ti-chevrons-up {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .Salary-slip-box {
            padding: 0 !important;
            margin: 0 !important;
        }
        .report-header-slip {
            margin-bottom: 20px !important;
        }
        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
    }
</style>
@endpush

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <div class="row">
            <div class="col-md-12">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-sm-8">
                            <h4 class="page-title">Salary Slip </h4>
                        </div>
                        <div class="col-sm-4 text-sm-end">
                            <div class="head-icons">
                                <a href="{{ request()->fullUrlWithQuery(['download_pdf' => 1]) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Download PDF"><i class="ti ti-file-type-pdf"></i></a>
                                <a href="javascript:void(0);" onclick="window.print();" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Print"><i class="ti ti-printer"></i></a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Refresh" onclick="window.location.reload();"><i class="ti ti-refresh-dot"></i></a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Collapse" id="collapse-header"><i
                                        class="ti ti-chevrons-up"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="card">

                    <div class="card-body">

                        <div class="report-header-slip text-center mb-4">
                            <h2 class="company-name-text mb-1" style="text-transform: uppercase; font-weight: bold;"></h2>
                            <h4 class="salary-period-text"></h4>
                        </div>

                        <div class="Salary-slip-box">
                            <div class="table-responsive">


                                <table class="table table-bordered" style="border-collapse:collapse;" cellspacing="0">
                                    <tr style="height:15pt">
                                        <td colspan="6">
                                            <p class="s1" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                                Company Name : <span id="company_name"></span>
                                            </p>
                                        </td>
                                        <td colspan="2" class="text-end">
                                            <p class="s2" style="padding-top: 1pt;padding-right: 2pt;text-indent: 0pt;text-align: right;">
                                                Date : <span id="salary_date"></span>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr style="height:14pt">
                                        <td style="width:80pt;"><p class="s1">Emp.Code</p></td>
                                        <td colspan="2"><p class="s2" id="emp_code"></p></td>
                                        <td style="width:60pt;"><p class="s1">Emp.Name</p></td>
                                        <td colspan="2"><p class="s2" id="emp_name"></p></td>
                                        <td colspan="2" class="text-end"><p class="s2">Dept. : <span id="emp_department"></span></p></td>
                                    </tr>
                                    <tr style="height:25pt; background-color: #f9f9f9;">
                                        <td colspan="8" style="padding: 5px 0;">
                                            <table style="width: 100%; border: none;">
                                                <tr id="dynamic_attendance_row">
                                                    <!-- Will be populated by JS -->
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr style="height:14pt">
                                        <td><p class="s1">Designation</p></td>
                                        <td colspan="3"><p class="s2" id="emp_designation"></p></td>
                                        <td colspan="4"><p class="s2">Father Name : <span id="emp_father"></span></p></td>
                                    </tr>
                                    
                                    <tr style="height:18pt; background-color: #eeeeee;">
                                        <td colspan="4" class="text-center" style="border-bottom: 1px solid #000;">
                                            <p class="s1" style="font-size: 10pt; text-align: center; margin:0;">SALARY ADDITIONS</p>
                                        </td>
                                        <td colspan="4" class="text-center" style="border-bottom: 1px solid #000;">
                                            <p class="s1" style="font-size: 10pt; text-align: center; margin:0;">SALARY DEDUCTIONS</p>
                                        </td>
                                    </tr>
                                    <tr style="height:14pt; background-color: #f5f5f5;">
                                        <td colspan="2"><p class="s1" style="padding-left:2px;">Salary Head</p></td>
                                        <td class="text-end"><p class="s1" style="text-align: right; padding-right:2px;">Rate</p></td>
                                        <td class="text-end"><p class="s1" style="text-align: right; padding-right:2px;">Amount</p></td>
                                        
                                        <td colspan="3"><p class="s1" style="padding-left:2px;">Deduction Head</p></td>
                                        <td class="text-end"><p class="s1" style="text-align: right; padding-right:2px;">Amount</p></td>
                                    </tr>
                                    <tbody id="dynamic_components_tbody">
                                        <!-- Will be populated by JS -->
                                    </tbody>
                                    
                                    <tr style="height:22pt">
                                        <td colspan="8"></td>
                                    </tr>
                                    <tr style="height:12pt">
                                        <td colspan="3" class="text-end"><p class="s1" style="text-align:right;">Total Earning</p></td>
                                        <td class="text-end"><p class="s2" style="text-align:right;" id="total_earning">0</p></td>
                                        <td colspan="3" class="text-end"><p class="s1" style="text-align:right;">Total Deduction</p></td>
                                        <td class="text-end"><p class="s2" style="text-align:right;" id="total_deduction">0</p></td>
                                    </tr>
                                    <tr style="height:15pt">
                                        <td colspan="4" rowspan="3" style="vertical-align: top; padding-top: 5pt;">
                                            <p class="s2">Total Amount in Word : <br><span id="amount_in_words"></span></p>
                                        </td>
                                        <td colspan="3" class="text-end"><p class="s1" style="color:#555; font-weight: normal; text-align:right;">Net Salary</p></td>
                                        <td class="text-end"><p class="s1" style="color:#555; font-weight: normal; text-align:right;" id="base_net_salary">0</p></td>
                                    </tr>
                                    <tr style="height:15pt">
                                        <td colspan="3" class="text-end"><p class="s1" style="color:#555; font-weight: normal; text-align:right;">Comp Off Amount</p></td>
                                        <td class="text-end"><p class="s1" style="color:#555; font-weight: normal; text-align:right;" id="comp_off_amount_bottom">0</p></td>
                                    </tr>
                                    <tr style="height:15pt">
                                        <td colspan="3" class="text-end"><p class="s1" style="text-align:right;">Total Salary</p></td>
                                        <td class="text-end"><p class="s1" style="text-align:right;" id="net_salary_text">0</p></td>
                                    </tr>
                                </table>

                            </div>



                        </div>


                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
<!-- /Page Wrapper -->

<!-- Add New Calls -->
<div class="modal fade" id="add_calls" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Call Reason</h5>
                <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form action="calls.html">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="col-form-label">Reason <span class="text-danger">*</span></label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="mb-0">
                        <label class="col-form-label">Status</label>
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                <input type="radio" class="status-radio" id="add-active" name="status"
                                    checked="">
                                <label for="add-active">Active</label>
                            </div>
                            <div>
                                <input type="radio" class="status-radio" id="add-inactive" name="status">
                                <label for="add-inactive">Inactive</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center justify-content-end m-0">
                        <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add New Calls -->

<!-- Edit Calls -->
<div class="modal fade" id="edit_calls" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Call Reason</h5>
                <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form action="calls.html">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="col-form-label">Reason <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="Busy">
                    </div>
                    <div class="mb-0">
                        <label class="col-form-label">Status</label>
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                <input type="radio" class="status-radio" id="edit-active" name="status"
                                    checked="">
                                <label for="edit-active">Active</label>
                            </div>
                            <div>
                                <input type="radio" class="status-radio" id="edit-inactive" name="status">
                                <label for="edit-inactive">Inactive</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center justify-content-end m-0">
                        <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Edit Calls -->

<!-- Delete Calls -->
<div class="modal fade" id="delete_calls" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                        <i class="ti ti-trash-x fs-36 text-danger"></i>
                    </div>
                    <h4 class="mb-2">Remove Call Reason?</h4>
                    <p class="mb-0">AAre you sure you want to remove it.</p>
                    <div class="d-flex align-items-center justify-content-center mt-4">
                        <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                        <a href="calls.html" class="btn btn-danger">Yes, Delete it</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Calls -->

@push('scripts')
    <script>
$(document).ready(function () {
    let data = null;

    @if(isset($report))
        data = @json($report);
    @else
        const slipData = localStorage.getItem('salarySlipData');
        if (slipData) {
            data = JSON.parse(slipData);
        }
    @endif

    if (!data) {
        console.error('No salary slip data found.');
        return;
    }

    console.log('Salary slip data:', data);

    // Fill fields
    const companyName = data.box?.name || data.box_user?.box?.name || 'N/A';
    $('#company_name').text(companyName);
    $('.company-name-text').text(companyName);
    
    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    const monthYearText = `SALARY SLIP FOR THE MONTH OF ${monthNames[data.month - 1].toUpperCase()} ${data.year}`;
    $('.salary-period-text').text(monthYearText);

    $('#salary_date').text(new Date().toLocaleDateString());
    $('#emp_code').text(data.box_user?.employee_id ?? 'N/A');
    $('#emp_name').text(data.box_user?.user?.name ?? 'N/A');
    $('#emp_department').text(data.box_user?.department_relation?.name ?? 'N/A');
    $('#emp_designation').text(data.box_user?.designation_relation?.name ?? 'N/A');
    $('#emp_father').text(data.box_user?.father_name ?? 'N/A');

    $('#present_days').text(data.earnings_breakdown?.present_days ?? data.total_working_days ?? 0);
    $('#absent_days').text(data.deductions_breakdown?.absent_days ?? data.deductions_breakdown?.absent_days_from_attendance ?? data.deductions_breakdown?.deduction_days ?? 0);
    $('#holidays').text(data.total_holidays ?? data.earnings_breakdown?.holiday_days ?? 0);
    $('#leave_days').text(data.leave_days ?? data.earnings_breakdown?.leave_days ?? 0);
    $('#weekly_offs').text(data.total_weekly_offs ?? data.earnings_breakdown?.weekly_off_days ?? 0);
    $('#comp_off_days').text(data.comp_off_days ?? data.earnings_breakdown?.comp_off_days ?? 0);
    $('#paid_days').text(data.dynamic_paid_days ?? data.earnings_breakdown?.dynamic_paid_days ?? 0);
    $('#standard_paid_days').text(data.calculation_base_days ?? data.earnings_breakdown?.calculation_base_days ?? 30);
    let prodHours = '00:00';
    let otHours = '00:00';
    @if(isset($totalProductionHours))
        prodHours = '{{ $totalProductionHours }}';
    @endif
    @if(isset($totalOTHours))
        otHours = '{{ $totalOTHours }}';
    @endif

    // Dynamically render attendance
    let attRows = [
        { label: 'Present', value: data.earnings_breakdown?.present_days ?? data.total_working_days ?? 0 },
        { label: 'Absent', value: data.deductions_breakdown?.absent_days ?? data.deductions_breakdown?.absent_days_from_attendance ?? data.deductions_breakdown?.deduction_days ?? 0 },
        { label: 'HoliDay', value: data.total_holidays ?? data.earnings_breakdown?.holiday_days ?? 0 },
        { label: 'Leave', value: data.leave_days ?? data.earnings_breakdown?.leave_days ?? 0 },
        { label: 'Week Off', value: data.total_weekly_offs ?? data.earnings_breakdown?.weekly_off_days ?? 0 },
        { label: 'Comp Off', value: data.comp_off_days ?? data.earnings_breakdown?.comp_off_days ?? 0 },
        { label: 'Paid Days', value: data.dynamic_paid_days ?? data.earnings_breakdown?.dynamic_paid_days ?? data.paid_days ?? data.earnings_breakdown?.paid_days ?? 0 },
        { label: 'Std. Days', value: data.calculation_base_days ?? data.earnings_breakdown?.calculation_base_days ?? 30 },
        { label: 'Prod. Hrs', value: prodHours },
        { label: 'OT Hrs', value: otHours },
    ];
    
    let attHtml = '';
    for (let i = 0; i < attRows.length; i++) {
        attHtml += `
            <td style="border: none; text-align: center; ${i < attRows.length - 1 ? 'border-right: 1px solid #ccc;' : ''}">
                <p class="s1" style="font-size: 7.5pt;">${attRows[i].label}</p>
                <p class="s2" style="font-size: 8.5pt; font-weight: bold; margin-top: 2px;">${attRows[i].value}</p>
            </td>
        `;
    }
    $('#dynamic_attendance_row').html(attHtml);

    // Dynamically render components
    let earnings = data.earnings_breakdown?.structure_earnings || {};
    let earningKeys = Object.keys(earnings);

    let deductions = data.earnings_breakdown?.structure_deductions || {};
    let deductionKeys = Object.keys(deductions);
    
    let totalRows = Math.max(earningKeys.length, deductionKeys.length, 1);
    let tbodyHtml = '';

    for (let i = 0; i < totalRows; i++) {
        let eKey = i < earningKeys.length ? earningKeys[i] : null;
        let eLabel = eKey ? eKey.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : '';
        let eRate = eKey && earnings[eKey]?.actual ? Number(earnings[eKey].actual).toFixed(2) : '';
        let ePayable = eKey && earnings[eKey]?.prorated ? Number(earnings[eKey].prorated).toFixed(2) : '';

        let dKey = i < deductionKeys.length ? deductionKeys[i] : null;
        let dLabel = dKey ? dKey.replace(/_/g, ' ').replace(/deductions/i, '').trim().toUpperCase() : '';
        let dAmount = dKey && deductions[dKey]?.prorated ? Number(deductions[dKey].prorated).toFixed(2) : '';

        tbodyHtml += `
            <tr style="height:14pt">
                <td colspan="2"><p class="s1" style="padding-top: 1pt;padding-left: 2px;text-indent: 0pt;text-align: left;">${eLabel}</p></td>
                <td class="text-end"><p class="s2" style="padding-top: 1pt;padding-right: 2px;text-indent: 0pt;text-align: right;">${eRate}</p></td>
                <td class="text-end"><p class="s2" style="padding-top: 1pt;padding-right: 2px;text-indent: 0pt;text-align: right;">${ePayable}</p></td>
                
                <td colspan="3"><p class="s1" style="padding-top: 1pt;padding-left: 2px;text-indent: 0pt;text-align: left;">${dLabel}</p></td>
                <td class="text-end"><p class="s2" style="padding-top: 1pt;padding-right: 2px;text-indent: 0pt;text-align: right;">${dAmount}</p></td>
            </tr>
        `;
    }
    
    $('#dynamic_components_tbody').html(tbodyHtml);


    // Calculation logic for dynamic fields
    const perDaySalary = data.earnings_breakdown?.per_day_salary || 0;
    $('#salary_rate').text(perDaySalary);

    // amt payable - using net salary
    $('#amount_payable').text(data.net_salary ?? 0);

    // other deductions
    const otherDeduction = data.deductions_breakdown?.other_deductions || 0;
    $('#other_deduction').text(otherDeduction);

    $('#pf_deduction').text(data.deductions_breakdown?.pf_deduction ?? 0);
    $('#esi_deduction').text(data.deductions_breakdown?.esi_deduction ?? 0);

    let totalEarnings = Number(data.total_earnings ?? data.gross_salary ?? 0);
    let compOffBottom = Number(data.earnings_breakdown?.comp_off_encash_amount || 0);
    $('#total_earning').text((totalEarnings - compOffBottom).toFixed(2));
    
    $('#total_deduction').text(data.total_deductions ?? 0);
    
    let netSalary = Number(data.net_salary || 0);
    $('#base_net_salary').text((netSalary - compOffBottom).toFixed(2));
    $('#comp_off_amount_bottom').text(compOffBottom.toFixed(2));
    $('#net_salary_text').text(netSalary.toFixed(2));

    function numberToWords(num) {
        var a = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven',
                 'Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
        var b = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
        if ((num = num.toString()).length > 9) return 'Overflow';
        var n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
        if (!n) return;
        var str = '';
        str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + ' Crore ' : '';
        str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + ' Lakh ' : '';
        str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + ' Thousand ' : '';
        str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + ' Hundred ' : '';
        str += (n[5] != 0) ? ((str != '') ? 'and ' : '') + (a[Number(n[5])] || b[n[5][0]] + ' ' + a[n[5][1]]) : '';
        return str.trim();
    }

    if (data.net_salary) {
        $('#amount_in_words').text('(' + numberToWords(Math.floor(data.net_salary)) + ' Rupees Only)');
    }
});
</script>
@endpush
@endsection
