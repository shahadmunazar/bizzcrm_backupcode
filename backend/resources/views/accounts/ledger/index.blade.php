@extends('layouts.main')
@section('content')
@section('title', 'Ledger Summary')
		<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">
				<div class="row">
					<div class="col-md-12">
						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-4">
									<h4 class="page-title">Receipts and Payments Summary</h4>
								</div>
								<div class="col-8 text-end">
									<div class="head-icons">
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
											<input type="text" class="form-control" id="search_global" placeholder="Search Ledgers">
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
								<!-- Filter Header -->
                                <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
                                    <div class="d-flex align-items-center flex-wrap row-gap-2">
                                        <div class="dropdown me-2">
                                            <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-sort-ascending-2 me-2"></i>Sort </a>
                                            <div class="dropdown-menu  dropdown-menu-start">
                                                <ul>
                                                    <li><a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="particulars" data-dir="asc">Particulars Asc</a></li>
                                                    <li><a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="particulars" data-dir="desc">Particulars Desc</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="dropdown me-2">
                                            <a href="javascript:void(0);" class="btn bg-soft-purple text-purple reset-filters"><i class="ti ti-refresh me-2"></i>Reset Filter</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Filter Header -->

								<div class="table-responsive custom-table">
									<table class="table" id="ledger_table">
										<thead class="thead-light">
											<tr>
												<th>Particulars</th>
												<th>Receipts</th>
												<th>Payments</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
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
        var table = $('#ledger_table').DataTable({
            processing: true,
            serverSide: false, // Turned off to use custom JSON structure
            pageLength: 50,
            ajax: {
                url: "{{ box_route('accounts.ledger.index') }}",
                dataSrc: "data.sources",
                data: function (d) {
                    d.search_global = $('#search_global').val();
                    d.bookingrange = $('.bookingrange').val();
                    d.company_id = '{{ $company_id ?? "" }}';
                }
            },
            order: [[0, 'asc']], // Default sort by Particulars
            columns: [
                { 
                    data: 'name', 
                    name: 'name',
                    render: function(data, type, row) {
                        var url = '{{ box_route("accounts.ledger.show", ["lead_id" => ":id", "company_id" => ":company_id"]) }}'
                            .replace(':id', row.id)
                            .replace(':company_id', '{{ $company_id ?? "" }}');
                        return "<a href='"+url+"' class='text-primary font-weight-bold'>"+data+"</a>";
                    }
                },
                { 
                    data: 'receipts_amount', 
                    name: 'receipts_amount', 
                    render: function(data, type) { 
                        if (!data || parseFloat(data) === 0) return '';
                        if (type === 'display') {
                            return parseFloat(data).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                        return data;
                    } 
                },
                { 
                    data: 'invoices_amount', 
                    name: 'invoices_amount',
                    render: function(data, type) { 
                        if (!data || parseFloat(data) === 0) return '';
                        if (type === 'display') {
                            return parseFloat(data).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                        return data;
                    }
                }
            ]
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
                if (table) table.ajax.reload();
            });
            $('.bookingrange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                if (table) table.ajax.reload();
            });
        }

        // Global Search
        $('#search_global').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Sort items click
        $(document).on('click', '.sort-item', function() {
            var sortColStr = $(this).data('sort');
            var sortDir = $(this).data('dir');
            var colIdx = 0; // Particulars column
            if (table) table.order([colIdx, sortDir]).draw();
        });

        // Reset filters
        $('.reset-filters').on('click', function() {
            $('#search_global').val('');
            $('.bookingrange').val('');
            if (table) {
                table.search('').order([0, 'asc']);
                table.ajax.reload();
            }
        });
    });
</script>
@endsection
