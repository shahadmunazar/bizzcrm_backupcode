@extends('layouts.main')
@section('content')
@section('title', 'Account Ledger')
		<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">
				<div class="row">
					<div class="col-md-12">
						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-4">
									<h4 class="page-title">Account Ledger: <span class="text-primary">{{ $lead->name }} {{ $lead->last_name }}</span></h4>
								</div>
								<div class="col-8 text-end">
									<div class="head-icons">
										<a href="{{ box_route('accounts.ledger.index', ['company_id' => $company_id ?? '']) }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Back to Summary">
											<i class="ti ti-arrow-left"></i>
										</a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
											<i class="ti ti-chevrons-up"></i>
										</a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Page Header -->
						<div class="card ">
							<div class="card-header">
								<!-- Search -->
								<div class="row align-items-center">
									<div class="col-sm-4">
										<div class="icon-form mb-3 mb-sm-0">
											<span class="form-icon"><i class="ti ti-search"></i></span>
											<input type="text" class="form-control" id="search_global" placeholder="Search Transactions">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
											<div class="me-2">
												<div class="input-group">
													<span class="input-group-text"><i class="ti ti-calendar"></i></span>
													<input type="text" class="form-control bookingrange" placeholder="Select Date Range">
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- /Search -->
							</div>
							<div class="card-body">
								<div class="table-responsive custom-table">
									<table class="table" id="ledger_detail_table">
										<thead class="thead-light">
											<tr>
												<th>Date</th>
												<th>Particulars</th>
												<th>Voucher Type</th>
												<th>Voucher No</th>
												<th>Debit Amount</th>
												<th>Credit Amount</th>
                                                <th>Balance</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4" class="text-end"><strong>Totals</strong></th>
                                                <th id="footer_total_debit">0.00</th>
                                                <th id="footer_total_credit">0.00</th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <th colspan="4" class="text-end"><strong>Closing Balance</strong></th>
                                                <th colspan="2" id="footer_closing_balance" class="text-center font-weight-bold">0.00</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Page Wrapper -->

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var table = $('#ledger_detail_table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 100,
            ajax: {
                url: "{{ box_route('accounts.ledger.show', ['lead_id' => $lead->id, 'company_id' => $company_id ?? '']) }}",
                data: function (d) {
                    d.search_global = $('#search_global').val();
                    d.bookingrange = $('.bookingrange').val();
                }
            },
            order: [], // Let the server handle ordering natively for chronological view
            ordering: false, // Usually ledgers shouldn't be sorted by users otherwise running balance breaks
            columns: [
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'particulars', name: 'particulars' },
                { data: 'voucher_type', name: 'voucher_type' },
                { data: 'voucher_no', name: 'voucher_no' },
                { data: 'debit_amount', name: 'debit_amount' },
                { data: 'credit_amount', name: 'credit_amount' },
                { data: 'balance', name: 'balance' }
            ],
            drawCallback: function(settings) {
                var api = this.api();
                var json = api.ajax.json();
                if (json) {
                    $('#footer_total_debit').html('<strong>' + json.total_debit + '</strong>');
                    $('#footer_total_credit').html('<strong>' + json.total_credit + '</strong>');
                    $('#footer_closing_balance').html('<strong>' + json.closing_balance + '</strong>');
                }
            }
        });

        // Initialize Daterangepicker if not automatically initialized
        if($.fn.daterangepicker) {
            $('.bookingrange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });
            $('.bookingrange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                if (table) table.draw();
            });
            $('.bookingrange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                if (table) table.draw();
            });
        }

        // Global Search
        $('#search_global').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
@endsection
