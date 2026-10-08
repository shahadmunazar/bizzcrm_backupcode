<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Invoice - {{ $invoice->invoice_no ?? '#' . $invoice->id }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

<style>
body { background:#fff; color:#272626; font-size:14px; font-family: Helvetica, Arial, sans-serif; }
img a, img { border:0px; outline:none; }
* { margin:0; padding:0px; }
.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
	padding: 0px; line-height: 1.42857143; vertical-align: top; border-top: none;
}
table { width:100%; border-collapse: collapse; max-width: 100%; }
table tr td table tr td, table tr td table tr th { padding: 8px!important; border: 1px solid #333!important; }
table tr td table.packages tr td { border-top: none!important; border-bottom:none!important; }
table tr td table.packages tr.border td { border-top: 1px solid #333!important; border-bottom: 1px solid #333!important; }
.invoice-table table, .invoice-table table th, .invoice-table table td { margin: 0px; }
.invoice-table { max-width: 100%; margin: 0 auto; width: 100%; padding-left: 15px; padding-right: 15px; }
.pull-left { float:left; }
.pull-right { float:right; }
@media(min-width:320px) and (max-width:767px){
	.invoice-table { width:auto; }
	.table-responsive { width: 100%; margin-bottom: 15px; overflow-y: hidden; -ms-overflow-style: -ms-autohiding-scrollbar; border: 1px solid #ddd; }
}
body { font-family: "Courier New", Courier, monospace; }
@page { margin: 15px; }

    tr { page-break-inside: avoid; }
    table { page-break-inside: auto; }
    .invoice-table { page-break-inside: avoid; }

</style>
</head>
<body>
    <div class="invoice-table">
	    <center style="font-size: 18px;text-transform: uppercase;margin-bottom: 6px;margin-top: 10px;">Challan / Credit Note</center>
	    <div class="table-responsive">
					<table class="table" cellspacing="0" cellpadding="0" border="0">
						<tbody>
							<tr>
								<td>
									<table class="table " cellspacing="0" cellpadding="0" border="0" >
										<tbody>
											<tr  class="border">
												<td style="border-bottom:none!important;" rowspan="6" >
												<div class="company-details">
												    <img style="margin:0px 0 3px 0; max-width: {{ $settings->logo_width ?? 250 }}px; max-height: {{ $settings->logo_height ?? 80 }}px;" src="{{ isset($logoBase64) && !empty($logoBase64) ? $logoBase64 : ($settings->invoice_logo ? asset($settings->invoice_logo) : asset('assets/img/logo.svg')) }}"/><br>
												<strong>{{ $box->name ?? 'Company Name' }}</strong><br> {!! nl2br(e($box->address ?? 'Company Address')) !!}<br>
                                                <strong>E-Mail :</strong> {{ $box->company_email ?? 'N/A' }}<br>
                                                <strong>Phone :</strong> {{ $box->company_phone ?? $box->phone ?? 'N/A' }}
												</div>
												
												<div class="buyer" style="margin-top:12px; border-top:1px solid #333;padding-top: 10px;">
												    Buyer<br>
													<strong>{{ $invoice->company->bill_name ?? $invoice->company->name ?? 'N/A' }}</strong><br>
                                                    {!! nl2br(e($invoice->company->bill_address ?? $invoice->company->address ?? 'N/A')) !!} <br>
													<strong>E-Mail :</strong> {{ $invoice->lead->email ?? 'N/A' }}<br>
                                                    <strong>Phone :</strong> {{ $invoice->company->bill_phone ?? $invoice->lead->phone ?? 'N/A' }}
												</div>
												</td>
											</tr>
											<tr class="border">
												<td style="" >Invoice No <strong>{{ $invoice->invoice_no ?? '#' . $invoice->id }}</strong></td>
												<td >Dated<br><strong>{{ $invoice->invoice_date ? $invoice->invoice_date->format('d-M-Y') : 'N/A' }}</strong></td>
											</tr>
											<tr class="border">
												<td style="" >Due Date</td>
												<td >Mode/Terms of Payment<br><strong>{{ $invoice->payment_option ?? 'TRANSFER' }}</strong></td>
											</tr>
											<tr class="border">
												<td style="">Supplier's Ref.</td>
												<td>Other Reference(s)</td>
											</tr>
											<tr>
												<td style="border-bottom:none!important; ">Buyer's Order No.</td>
												<td style="border-bottom:none!important">Dated<br><strong>{{ $invoice->due_date ? $invoice->due_date->format('d-M-Y') : 'N/A' }}</strong></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table class="packages table" cellspacing="0" cellpadding="0" border="0">
										<tbody>
											<tr>
												<th>Sr. No.</th>
												<th style="width: 249px;">Particulars</th>
												<th>HSN/SAC</th>
												<th>Quantity</th>
												<th>Amount</th>  
												<th>Discount</th>
												<th>Total amount</th>
											</tr>
											<tr>
												<td align="center" style="border-top:none!important">1</td>
												<td align="center"><strong>Service / Product</strong></td>
												<td></td>
												<td>1</td>
												<td>{{ number_format($invoice->amount, 2) }}</td>
												<td>0%</td>
												<td align="right">{{ number_format($invoice->amount, 2) }}</td>
											</tr>
                                            <!-- Empty rows for spacing -->
											<tr><td align="center">2</td><td align="right"></td><td></td><td></td><td></td><td></td><td></td></tr>
											<tr><td align="center">3</td><td align="right"></td><td></td><td></td><td></td><td></td><td></td></tr>
											<tr><td align="center">4</td><td align="right"></td><td></td><td></td><td></td><td></td><td></td></tr>

											<tr class="border">
												<td style="width: 49px;"></td>
												<td style="width:249px;" align="right"><strong>Total</strong></td>
												<td style="width: 61px; border-left:none!important;border-right:none!important;"></td>
												<td style="border-left:none!important;border-right:none!important;"></td>
												<td style="border-left:none!important;border-right:none!important;"></td>
												<td style="border-left:none!important;border-right:none!important;"></td>
												<td style="width: 130px;" align="right"><strong>{{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}</strong></td>
											</tr>
										</tbody>
									</table>	
									
									<div class="tax-total">
									<table class="table packages tax-table" cellspacing="0" cellpadding="0" border="0">
										<tbody>
											<tr align="center"  class="border">
									            <td align="right" style="border-top: none !important; vertical-align: middle; width: 133px;" rowspan="2" colspan="3"><strong>Tax / Subtotal</strong></td>
												<td style="border-top: none !important; width:104px"  align="center"><strong>Paid Amount</strong></td>
												<td style="border-top: none !important; width:103px" align="center"><strong>Balance Due</strong></td>
												<td style="border-top: none !important; width:92px" align="center"><strong></strong></td>
					                            <td style="border-top: none !important;  width: 130px!important;" align="center"><strong></strong></td>
											</tr>
											
											<tr class="border">
												<td style="border-bottom:none!important" align="center">{{ number_format($invoice->paid_amount, 2) }}</td>
												<td style="border-bottom:none!important" align="center">{{ number_format($invoice->balance_amount, 2) }}</td>
												<td style="border-bottom:none!important" align="center"></td>
												<td align="right" style="border-top:none!important;"></td>
											</tr>
											<tr class="border" align="right">
												<td colspan="3" align="right" style="border-bottom:none!important; width:130px;"><strong>Grand Total</strong></td>
												<td style="border-right:none!important; border-bottom:none!important;"></td>
												<td style="border-right:none!important; border-left:none!important;border-bottom:none!important; "></td>
												<td style="border-left:none!important; border-bottom:none!important;"></td>
												<td style="border-bottom:none!important; " ><strong>{{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}</strong></td>
											</tr>
										</tbody>
									</table>
									</div>
									
									<table class="table packages" cellspacing="0" cellpadding="0" border="0">
										<tbody>
											<tr  class="border">
												<td colspan="7" style="border-top:none!important;"><center style="font-size: 18px;text-transform: uppercase;margin-bottom: 0px;margin-top: 0px;">Details</center></td>
											</tr>
											
											<tr>
												<td colspan="7">
                                                    <strong>Terms and Conditions:</strong><br>
                                                    {!! $settings->header_terms ?? 'Please pay within 15 days from the date of invoice.' !!}<br><br>
                                                    
                                                    <strong>Notes:</strong><br>
                                                    {{ $invoice->remarks ?? 'Please quote invoice number when remitting funds.' }}
                                                </td>
											</tr>
											<tr class="border">
												<td style="border-right:none!important" colspan="3">
                                                    {!! $settings->footer_terms ?? '' !!}
                                                </td>
												<td style="border-left:none!important"  colspan="4">
                                                    <strong>for {{ $box->name ?? 'Company Name' }}</strong>
                                                    <br><br><br>
                                                    <span class="pull-right">{{ Auth::user()->name ?? 'Authorised Signatory' }}</span>
                                                </td>
											</tr>
									    </tbody>
									</table>			
								</td>
							</tr>
						</tbody>
					</table>
			</div>
			<center style="margin-top:5px;">SUBJECT TO JURISDICTION</center>
		</div>
	</div>	

    @if(!request()->has('preview'))
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
    @endif
</body>
</html>