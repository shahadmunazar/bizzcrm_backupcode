@extends('layouts.main')
@section('title', 'Payments List')
@section('content')
<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-4">
									<h4 class="page-title">Payments List</h4>
								</div>
								<div class="col-8 text-end">
									<div class="head-icons">
										<a href="payments.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
											<i class="ti ti-refresh-dot"></i>
										</a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
											<i class="ti ti-chevrons-up"></i>
										</a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

						<div class="card">

							<div class="card-header">
								<!-- Search -->
								<div class="row align-items-center">
									<div class="col-sm-4">
										<div class="icon-form mb-3 mb-sm-0">
											<span class="form-icon"><i class="ti ti-search"></i></span>
											<input type="text" class="form-control" placeholder="Search Payments">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end mb-3 mb-sm-0">
											<div class="dropdown me-2">
												<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
												<div class="dropdown-menu  dropdown-menu-end">
													<ul>
														<li>
															<a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-file-type-pdf text-danger me-1"></i>Export
																as PDF</a>
														</li>
														<li>
															<a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-file-type-xls text-green me-1"></i>Export
																as Excel </a>
														</li>
													</ul>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- /Search -->
							</div>

							<div class="card-body">

								<!-- Filter -->
								<div class="d-flex align-items-center justify-content-between flex-wrap mb-4 row-gap-2">
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<div class="dropdown me-2">
											<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-sort-ascending-2 me-2"></i>Sort </a>
											<div class="dropdown-menu  dropdown-menu-start">
												<ul>
													<li>
														<a href="javascript:void(0);" class="dropdown-item">
															<i class="ti ti-circle-chevron-right me-1"></i>Ascending
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item">
															<i class="ti ti-circle-chevron-right me-1"></i>Descending
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item">
															<i class="ti ti-circle-chevron-right me-1"></i>Recently
															Viewed
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item">
															<i class="ti ti-circle-chevron-right me-1"></i>Recently
															Added
														</a>
													</li>
												</ul>
											</div>
										</div>
										<div class="icon-form">
											<span class="form-icon"><i class="ti ti-calendar"></i></span>
											<input type="text" class="form-control bookingrange" placeholder="">
										</div>
									</div>
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<div class="dropdown me-2">
											<a href="javascript:void(0);" class="btn bg-soft-purple text-purple" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-columns-3 me-2"></i>Manage Columns</a>
											<div class="dropdown-menu  dropdown-menu-md-end dropdown-md p-3">
												<h4 class="mb-2 fw-semibold">Want to manage datatables?</h4>
												<p class="mb-3">Please drag and drop your column to reorder your table
													and enable see option as you want.</p>
												<div class="border-top pt-3">
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Invoice ID</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-name" class="check">
															<label for="col-name" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Client</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-phone" class="check">
															<label for="col-phone" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Amount</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-email" class="check">
															<label for="col-email" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Due Date</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-tag" class="check">
															<label for="col-tag" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Payment Method</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-loc" class="check">
															<label for="col-loc" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Transaction ID</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-rate" class="check">
															<label for="col-rate" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Action</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-action" class="check">
															<label for="col-action" class="checktoggle"></label>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="form-sorts dropdown">
											<a href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-filter-share"></i>Filter</a>
											<div class="filter-dropdown-menu dropdown-menu  dropdown-menu-md-end p-4">
												<div class="filter-set-view">
													<div class="filter-set-head">
														<h4><i class="ti ti-filter-share"></i>Filter</h4>
													</div>
													<div class="accordion" id="accordionExample">
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" data-bs-toggle="collapse" data-bs-target="#collapse" aria-expanded="true" aria-controls="collapse">Invoice ID</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse show" id="collapse" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<div class="mb-2 icon-form">
																		<span class="form-icon"><i class="ti ti-search"></i></span>
																		<input type="text" class="form-control" placeholder="Search ID">
																	</div>
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					#1254058
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					#1254057
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					#1254056
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					#1254055
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					#1254054
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseone" aria-expanded="false" aria-controls="collapseone">Due Date</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="collapseone" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
																					<span class="checkmarks"></span>
																					15 Oct 2023
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					19 Oct 2023
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					24 Oct 2023
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					10 Nov 2023
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					18 Nov 2023
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapsethree" aria-expanded="false" aria-controls="collapsethree">Amount</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="collapsethree" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<div class="mb-2 icon-form">
																		<span class="form-icon"><i class="ti ti-search"></i></span>
																		<input type="text" class="form-control" placeholder="Search Amount">
																	</div>
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
																					<span class="checkmarks"></span>
																					$5000
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					$4180
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					$3125
																				</label>
																			</div>

																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					$1230
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					$3500
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
													</div>
													<div class="filter-reset-btns">
														<div class="row">
															<div class="col-6">
																<a href="#" class="btn btn-light">Reset</a>
															</div>
															<div class="col-6">
																<a href="payments.html" class="btn btn-primary">Filter</a>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- /Filter -->
								
								
								
								
								<div class="row">
					 <div class="col-xl-3 col-md-6">
						 <div class="card bg-green-img">
							 <div class="card-body">
								 <div class="d-flex align-items-center justify-content-between">
									 <div class="d-flex align-items-center">
										 <div class="flex-shrink-0 me-2">
											 <span class="avatar avatar-md rounded-circle bg-white d-flex align-items-center justify-content-center">
												 <i class="ti ti-user-check text-success fs-18"></i>
											 </span>
										 </div>
									 </div>
                                     <div class="text-end">
                                          <p class="mb-1">Total Paid</p>
                                         <h4>₹{{ number_format($totalPaid, 2) }}</h4>
                                     </div>
								 </div>
							 </div>
						 </div>
					 </div>
					 <div class="col-xl-3 col-md-6">
						 <div class="card bg-pink-img">
							 <div class="card-body">
								 <div class="d-flex align-items-center justify-content-between">
									 <div class="d-flex align-items-center">
										 <div class="flex-shrink-0 me-2">
											 <span class="avatar avatar-md rounded-circle bg-white d-flex align-items-center justify-content-center">
												 <i class="ti ti-user-edit text-pink fs-18"></i>
											 </span>
										 </div>
									 </div>
                                     <div class="text-end">
                                         <p class="mb-1">Total Approved</p>
                                         <h4>₹{{ number_format($totalApproved, 2) }}</h4>
                                     </div>
								 </div>
							 </div>
						 </div>
					 </div>
					 <div class="col-xl-3 col-md-6">
						 <div class="card bg-yellow-img">
							 <div class="card-body">
								 <div class="d-flex align-items-center justify-content-between">
									 <div class="d-flex align-items-center">
										 <div class="flex-shrink-0 me-2">
											 <span class="avatar avatar-md rounded-circle bg-white d-flex align-items-center justify-content-center">
												 <i class="ti ti-user-exclamation text-warning fs-18"></i>
											 </span>
										 </div>
									 </div>
                                     <div class="text-end">
                                         <p class="mb-1">Total Pending</p>
                                         <h4>₹{{ number_format($totalPending, 2) }}</h4>
                                     </div>
								 </div>
							 </div>
						 </div>
					 </div>
					 <div class="col-xl-3 col-md-6">
						 <div class="card bg-blue-img">
							 <div class="card-body">
								 <div class="d-flex align-items-center justify-content-between">
									 <div class="d-flex align-items-center">
										 <div class="flex-shrink-0 me-2">
											 <span class="avatar avatar-md rounded-circle bg-white d-flex align-items-center justify-content-center">
												 <i class="ti ti-user-question text-info fs-18"></i>
											 </span>
										 </div>
									 </div>
                                     <div class="text-end">
                                         <p class="mb-1">Total Rejected</p>
                                         <h4>₹{{ number_format($totalRejected, 2) }}</h4>
                                     </div>
								 </div>
							 </div>
						 </div>
					 </div>					
				  </div>
								
								

								<!-- Projects List -->
								
								
								<div class="table-responsive custom-table">
									<table class="table custom-table" id="paymentsTable">
										<thead class="thead-light">
											<tr>
												<th>Employee</th>
												<th>Category</th>
												<th>Date</th>
												<th>Amount</th>
												<th>Status</th>
												<th>Approved By</th>
												<th>Processed By</th>
												<th>Payment Details</th>
												<th class="text-end">Action</th>
											</tr>
										</thead>
										<tbody></tbody>
										</table>
										</div>
								
								
								<div class="row align-items-center">
									<div class="col-md-6">
										<div class="datatable-length"></div>
									</div>
									<div class="col-md-6">
										<div class="datatable-paginate"></div>
									</div>
								</div>
								<!-- /Projects List -->
								
								
								
								
								
								

							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Preview Project -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom justify-content-between">
				<h4>Payment for Invoice<span class=" text-purple ms-2">#274738</span> </h4>
				<div class="d-flex align-items-center">
					<div class="toggle-header-popup">
						<div class="dropdown table-action me-2">
							<a href="#" class="btn btn-dropdowns dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
								Download
							</a>
							<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item " href="#">
									Download
								</a>
								<a class="dropdown-item " href="#">
									Download PDF
								</a>
							</div>
						</div>
					</div>
					<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
						<i class="ti ti-x"></i>
					</button>
				</div>

			</div>
			<div class="offcanvas-body p-4">
				<form action="payments.html">
					<div class="card">
						<div class="card-body">
							<div class="details-propsal">
								<h4>Proposal From & To</h4>
								<div class="row">
									<div class="col-lg-6 col-12">
										<div class="proposal-to">
											<h3>CRMS</h3>
											<span>3338 Marcus Street </span>
											<span>Birmingham, AL 35211</span>
											<span>Phone : +1 98789 78788 </span>
											<span>Email : <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="2b42454d446b4e534a465b474e05484446">[email&#160;protected]</a></span>
										</div>
									</div>
									<div class="col-lg-6 col-12">
										<div class="proposal-to">
											<h3>NovaWave LLC </h3>
											<span>994 Martine Ranch Suite 900</span>
											<span>Candacefort New Hampshire</span>
											<span>Phone : +1 58478 74646</span>
											<span>Email : <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="452c2b232a05203d24283529206b2b2031">[email&#160;protected]</a></span>
										</div>
									</div>
								</div>
							</div>
							<hr>
							<div class="details-propsal details-propsals">
								<h4>Payment Details</h4>
								<ul>
									<li>
										<h5>Payment Date</h5>
										<h6>13 May 2024</h6>
									</li>
									<li>
										<h5>Payment Method</h5>
										<h6>Cash</h6>
									</li>
									<li>
										<h5>Total Amount</h5>
										<h6>$96</h6>
									</li>
								</ul>
							</div>
							<div class="details-propsal">
								<div class="d-flex align-items-center justify-content-between">
									<h4>Invoice Details</h4>
									<h6 class="d-flex"><span class="text-danger"> Amount Due : </span> $100</h6>
								</div>
								<ul class="m-0 border-0">
									<li>
										<h5>Invoice Number</h5>
										<h6 class="mb-0"><span class="badge badge-purple-light d-inline-flex">#1254057</span></h6>
									</li>
									<li>
										<h5>Invoice Date</h5>
										<h6 class="mb-0">13 May 2024</h6>
									</li>
									<li>
										<h5>Invoice Amount</h5>
										<h6 class="mb-0">$196</h6>
									</li>
									<li>
										<h5>Payment Amount</h5>
										<h6 class="mb-0">$96</h6>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<!-- /Preview Project -->

		<!-- Delete Invoices -->
		<div class="modal fade" id="delete_payments" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Payments?</h4>
							<p class="mb-0">Are you sure you want to remove <br> invoice you selected.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="payments.html" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Invoices -->


	</div>
	
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#paymentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('fieldstracking.payments.data', $box->slug) }}",
        columns: [
            { data: 'employee', name: 'employee', orderable: false, searchable: false },
            { data: 'category', name: 'category', orderable: false, searchable: false },
            { data: 'date', name: 'expense_date' },
            { data: 'amount', name: 'amount' },
            { data: 'status', name: 'status' },
            { data: 'approved_by', name: 'approved_by', orderable: false, searchable: false },
            { data: 'processed_by', name: 'processed_by', orderable: false, searchable: false },
            { data: 'payment_details', name: 'payment_details', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: {
            search: '',
            searchPlaceholder: 'Search...'
        }
    });
});
</script>
@endpush
