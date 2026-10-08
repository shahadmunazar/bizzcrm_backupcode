@extends('layouts.main')
@section('title', 'Invoice Details')
@section('content')
 <!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-4">
									<h4 class="page-title">Invoice Details</h4>
								</div>
								<div class="col-8 text-end">
									<div>
										<a href="{{ route('accounts.invoice_download', ['box_slug' => request()->segment(1), 'id' => $invoice->id]) }}" class="btn btn-primary"><i class="ti ti-download me-1"></i>Download</a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

						<!-- Invoices -->
						<div>
							<div class="row">
								<div class="col-sm-10 mx-auto">
									 <a href="invoice.html" class="back-icon d-flex align-items-center fs-12 fw-medium mb-3 d-inline-flex">
										<span class=" d-flex justify-content-center align-items-center rounded-circle me-2">
											<i class="ti ti-arrow-left"></i>
										</span>
										Back to List
									</a>
									<div class="card">
										<div class="card-body p-0" style="height: 100vh;">
											<iframe id="invoice-iframe" src="{{ route('accounts.invoice_print', ['box_slug' => request()->segment(1), 'id' => $invoice->id]) }}?preview=true" width="100%" height="100%" style="border: none;"></iframe>
										</div>
									</div>
									</div>
								</div>
							</div>
						</div>
						<!-- /Invoices -->

						<div class="d-flex justify-content-center align-items-center mb-4">
							<a href="#" class="btn btn-primary d-flex justify-content-center align-items-center me-2" onclick="event.preventDefault(); document.getElementById(\'invoice-iframe\').contentWindow.print()"><i class="ti ti-printer me-2"></i>Print Invoice</a>
						</div>

					</div>
				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->

		@if(request()->has('print'))
		<script>
			window.onload = function() {
				window.print();
			};
		</script>
		@endif
@endsection
