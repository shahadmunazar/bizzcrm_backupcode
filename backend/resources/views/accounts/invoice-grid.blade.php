@extends('layouts.main')
@section('title', 'Invoice Grid')
@section('content')
 
		<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									<h4 class="page-title">Invoices<span class="count-title">123</span></h4>
								</div>
								<div class="col-4 text-end">
									<div class="head-icons">
										<a href="invoice-grid.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

						<!-- Search -->
						<div class="row align-items-center">
							<div class="col-sm-4">
								<div class="icon-form mb-3 mb-sm-0">
									<span class="form-icon"><i class="ti ti-search"></i></span>
									<input type="text" class="form-control" placeholder="Search Invoices">
								</div>
							</div>
							<div class="col-sm-8">
								<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
									<div class="dropdown me-2">
										<a href="javascript:void(0);" class="dropdown-toggle bg-white" data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
										<div class="dropdown-menu  dropdown-menu-end">
											<ul>
												<li>
													<a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-file-type-pdf text-danger me-1"></i>Export as
														PDF</a>
												</li>
												<li>
													<a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-file-type-xls text-green me-1"></i>Export as
														Excel </a>
												</li>
											</ul>
										</div>
									</div>
									<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-square-rounded-plus me-2"></i>Add New Invoices</a>
								</div>
							</div>
						</div>
						<!-- /Search -->

						<!-- Filter -->
						<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 border-top mt-4 pt-4 mb-4">
							<div class="d-flex align-items-center flex-wrap row-gap-2">
								<div class="sort-dropdown drop-down task-drops me-3">
									<a href="javascript:void(0);" class="dropdown-toggle bg-transparent" data-bs-toggle="dropdown">All Invoices </a>
									<div class="dropdown-menu  dropdown-menu-start">
										<ul>
											<li>
												<a href="#">
													<i class="ti ti-dots-vertical"></i>All Invoices
												</a>
											</li>
											<li>
												<a href="#">
													<i class="ti ti-dots-vertical"></i>Paid
												</a>
											</li>
											<li>
												<a href="#">
													<i class="ti ti-dots-vertical"></i>Partially Paid
												</a>
											</li>
											<li>
												<a href="#">
													<i class="ti ti-dots-vertical"></i>Overdue
												</a>
											</li>
											<li>
												<a href="#">
													<i class="ti ti-dots-vertical"></i>Unpaid
												</a>
											</li>
										</ul>
									</div>
								</div>
								<div class="dropdown me-2">
									<a href="javascript:void(0);" class="dropdown-toggle bg-white" data-bs-toggle="dropdown"><i class="ti ti-sort-ascending-2 me-2"></i>Sort </a>
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
													<i class="ti ti-circle-chevron-right me-1"></i>Recently Viewed
												</a>
											</li>
											<li>
												<a href="javascript:void(0);" class="dropdown-item">
													<i class="ti ti-circle-chevron-right me-1"></i>Recently Added
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
								<div class="form-sorts dropdown me-2">
									<a href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-filter-share"></i>Filter</a>
									<div class="filter-dropdown-menu dropdown-menu  dropdown-menu-md-end p-4">
										<div class="filter-set-view">
											<div class="filter-set-head">
												<h4><i class="ti ti-filter-share"></i>Filter</h4>
											</div>
											<div class="accordion" id="accordionExample">

												<div class="filter-set-content">
													<div class="filter-set-content-head">
														<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#owner" aria-expanded="false" aria-controls="owner">Client</a>
													</div>
													<div class="filter-set-contents accordion-collapse collapse" id="owner" data-bs-parent="#accordionExample">
														<div class="filter-content-list">
															<div class="mb-2 icon-form">
																<span class="form-icon"><i class="ti ti-search"></i></span>
																<input type="text" class="form-control" placeholder="Search Client">
															</div>
															<ul>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox" checked="">
																			<span class="checkmarks"></span>
																			NovaWave LLC
																		</label>
																	</div>
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			Redwood Inc
																		</label>
																	</div>
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			HarborVie w
																		</label>
																	</div>
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			CoastalStar Co.
																		</label>
																	</div>
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			RiverStone Ventur
																		</label>
																	</div>
																</li>
															</ul>
														</div>
													</div>
												</div>


												<div class="filter-set-content">
													<div class="filter-set-content-head">
														<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">Project</a>
													</div>
													<div class="filter-set-contents accordion-collapse collapse" id="collapseThree" data-bs-parent="#accordionExample">
														<div class="filter-content-list">
															<ul>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox" checked="">
																			<span class="checkmarks"></span>
																			Truelysell
																		</label>
																	</div>
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			Dreamsports
																		</label>
																	</div>
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			Best@laundry
																		</label>
																	</div>
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			Doccure
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
																			$2,15,000
																		</label>
																	</div>
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			$1,45,000
																		</label>
																	</div>
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			$2,15,000
																		</label>
																	</div>
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			$4,80,380
																		</label>
																	</div>
																</li>
																<li>
																	<div class="filter-checks">
																		<label class="checkboxs">
																			<input type="checkbox">
																			<span class="checkmarks"></span>
																			$2,12,000
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
														<a href="invoice-grid.html" class="btn btn-primary">Filter</a>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="view-icons">
									<a href="invoices.html"><i class="ti ti-list-tree"></i></a>
									<a href="invoice-grid.html" class="active"><i class="ti ti-grid-dots"></i></a>
								</div>
							</div>
						</div>
						<!-- /Filter -->

						<!-- Invoice Grid -->
						<div class="row" id="invoice-grid-container">
                            <!-- Items will be loaded via AJAX -->
						</div>
						<!-- /Invoice Grid -->

						<div class="load-btn text-center pb-4">
							<a href="javascript:void(0);" class="btn btn-primary">Load More Invoices<i class="ti ti-loader"></i></a>
						</div>

					</div>
				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->

				<!-- Add New Invoices -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h4>Add New Invoice</h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form id="add-invoice-form">
					@csrf
					<div>
						<div class="row">
							<div class="col-md-6 mb-3">
								<div class="d-flex align-items-center justify-content-between">
									<label class="col-form-label">Client <span class="text-danger">*</span></label>
								</div>
								<select class="form-control custom-ajax-select" name="company_id" id="add_company_id">
									<option value="">Choose Client</option>
                                    @if(isset($company))
                                        <option value="{{ $company->id }}" selected>{{ $company->bill_name ?? $company->name }}</option>
                                    @endif
								</select>
							</div>
							<div class="col-md-6 mb-3">
								<div class="d-flex align-items-center justify-content-between">
									<label class="col-form-label">Project (Lead) <span class="text-danger">*</span></label>
								</div>
								<select class="form-control custom-ajax-select" name="lead_id" id="add_lead_id">
									<option value="">Choose Project</option>
									
								</select>
							</div>
							<div class="col-md-4 mb-3">
								<label class="col-form-label">Total Amount <span class="text-danger">*</span></label>
								<input class="form-control" type="number" step="0.01" name="amount" id="add_amount" required>
							</div>
							<div class="col-md-4 mb-3">
								<label class="col-form-label">Paid Amount</label>
								<input class="form-control" type="number" step="0.01" name="paid_amount" id="add_paid_amount" value="0">
							</div>
							<div class="col-md-4 mb-3">
								<label class="col-form-label">Balance Amount</label>
								<input class="form-control" type="number" step="0.01" name="balance_amount" id="add_balance_amount" readonly>
							</div>
							<div class="col-md-6 mb-3">
								<label class="col-form-label">Currency</label>
								<select class="select" name="currency" id="add_currency">
									<option value="USD">Dollar (USD)</option>
									<option value="EUR">Euro (EUR)</option>
									<option value="GBP">Pound (GBP)</option>
									<option value="INR">Rupee (INR)</option>
								</select>
							</div>
							<div class="col-md-6 mb-3">
								<label class="col-form-label">Invoice Date <span class="text-danger">*</span></label>
								<div class="icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="form-control datetimepicker" name="invoice_date" id="add_invoice_date" placeholder="" required>
								</div>
							</div>
							<div class="col-md-6 mb-3">
								<label class="col-form-label">Due Date</label>
								<div class="icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="form-control datetimepicker" name="due_date" id="add_due_date" placeholder="">
								</div>
							</div>
							<div class="col-md-6 mb-3">
								<label class="col-form-label">Payment Method</label>
								<select class="select" name="payment_method" id="add_payment_method">
									<option value="">Choose</option>
									<option value="Credit Card">Credit Card</option>
									<option value="Paypal">Paypal</option>
									<option value="Debit Card">Debit Card</option>
								</select>
							</div>
							<div class="col-md-6 mb-3">
								<label class="col-form-label">Status <span class="text-danger">*</span></label>
								<select class="select" name="status" id="add_status">
									<option value="Pending">Pending</option>
									<option value="Approved">Approved</option>
									<option value="DisApproved">DisApproved</option>
								</select>
							</div>
							<div class="col-md-12 mb-3">
								<label class="col-form-label">Description (Remarks)</label>
								<textarea class="form-control" name="remarks" id="add_remarks" rows="5"></textarea>
							</div>
						</div>
					</div>
					<div class="d-flex align-items-center justify-content-end mt-4">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary" id="btn-save-invoice">Save</button>
					</div>
				</form>
			</div>
		</div>
		<!-- /Add New Invoices -->

		<!-- Edit Invoices -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom">
				<h4>Edit Invoice</h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form id="edit-invoice-form">
					@csrf
					<input type="hidden" name="id" id="edit_invoice_id">
					<div>
						<div class="row">
							<div class="col-md-6 mb-3">
								<div class="d-flex align-items-center justify-content-between">
									<label class="col-form-label">Client <span class="text-danger">*</span></label>
								</div>
								<select class="form-control custom-ajax-select" name="company_id" id="edit_company_id">
									<option value="">Choose Client</option>
								</select>
							</div>
							<div class="col-md-6 mb-3">
								<div class="d-flex align-items-center justify-content-between">
									<label class="col-form-label">Project (Lead) <span class="text-danger">*</span></label>
								</div>
								<select class="form-control custom-ajax-select" name="lead_id" id="edit_lead_id">
									<option value="">Choose Project</option>
								</select>
							</div>
							<div class="col-md-4 mb-3">
								<label class="col-form-label">Total Amount <span class="text-danger">*</span></label>
								<input class="form-control" type="number" step="0.01" name="amount" id="edit_amount" required>
							</div>
							<div class="col-md-4 mb-3">
								<label class="col-form-label">Paid Amount</label>
								<input class="form-control" type="number" step="0.01" name="paid_amount" id="edit_paid_amount" value="0">
							</div>
							<div class="col-md-4 mb-3">
								<label class="col-form-label">Balance Amount</label>
								<input class="form-control" type="number" step="0.01" name="balance_amount" id="edit_balance_amount" readonly>
							</div>
							<div class="col-md-6 mb-3">
								<label class="col-form-label">Currency</label>
								<select class="select" name="currency" id="edit_currency">
									<option value="USD">Dollar (USD)</option>
									<option value="EUR">Euro (EUR)</option>
									<option value="GBP">Pound (GBP)</option>
									<option value="INR">Rupee (INR)</option>
								</select>
							</div>
							<div class="col-md-6 mb-3">
								<label class="col-form-label">Invoice Date <span class="text-danger">*</span></label>
								<div class="icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="form-control datetimepicker" name="invoice_date" id="edit_invoice_date" placeholder="" required>
								</div>
							</div>
							<div class="col-md-6 mb-3">
								<label class="col-form-label">Due Date</label>
								<div class="icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="form-control datetimepicker" name="due_date" id="edit_due_date" placeholder="">
								</div>
							</div>
							<div class="col-md-6 mb-3">
								<label class="col-form-label">Payment Method</label>
								<select class="select" name="payment_method" id="edit_payment_method">
									<option value="">Choose</option>
									<option value="Credit Card">Credit Card</option>
									<option value="Paypal">Paypal</option>
									<option value="Debit Card">Debit Card</option>
								</select>
							</div>
							<div class="col-md-6 mb-3">
								<label class="col-form-label">Status <span class="text-danger">*</span></label>
								<select class="select" name="status" id="edit_status">
									<option value="Pending">Pending</option>
									<option value="Approved">Approved</option>
									<option value="DisApproved">DisApproved</option>
								</select>
							</div>
							<div class="col-md-12 mb-3">
								<label class="col-form-label">Description (Remarks)</label>
								<textarea class="form-control" name="remarks" id="edit_remarks" rows="5"></textarea>
							</div>
						</div>
					</div>
					<div class="d-flex align-items-center justify-content-end mt-4">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary" id="btn-update-invoice">Update</button>
					</div>
				</form>
			</div>
		</div>
		<!-- /Edit Invoices -->

		<!-- Delete Invoices -->
		<div class="modal fade" id="delete_invoices" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Invoice?</h4>
							<p class="mb-0">Are you sure you want to remove <br> invoice you selected.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="invoice-grid.html" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Invoices -->

		<!-- Create Invoices -->
		<div class="modal custom-modal fade" id="create_success" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header border-0 m-0 justify-content-end">
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div class="success-message text-center">
							<div class="success-popup-icon bg-light-blue">
								<i class="ti ti-atom-2"></i>
							</div>
							<h3>Invoices Created Successfully!!!</h3>
							<p>View the details of invoices, created</p>
							<div class="col-lg-12 text-center modal-btn">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<a href="#" class="btn btn-primary">View Details</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Create Project -->

		<!-- Add New View -->
		<div class="modal custom-modal fade" id="add_deal" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="invoices.html">
							<div class="mb-3">
								<label class="col-form-label">Name</label>
								<input type="text" class="form-control" placeholder="Enter Name">
							</div>
							<div class="modal-btn text-end">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-danger">Save</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Add New View -->
@push('scripts')
<script>
    $(document).ready(function() {
        
        function getStatusBadge(status) {
            if(status == 'Paid') return '<span class="badge badge-success">Paid</span>';
            if(status == 'Partially Paid') return '<span class="badge badge-warning">Partially Paid</span>';
            if(status == 'Overdue') return '<span class="badge badge-pending">Overdue</span>';
            return '<span class="badge badge-danger">' + (status || 'Unpaid') + '</span>';
        }

        function loadInvoices() {
            var url = "{{ box_route('accounts.ajax_invoices') }}";
            $.ajax({
                url: url,
                type: 'GET',
                data: { company_id: '{{ $company_id ?? "" }}' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var html = '';
                        var invoices = response.data;
                        
                        var viewBaseUrl = "{{ box_route('accounts.invoice_details', ['id' => ':id']) }}";
                        var printBaseUrl = "{{ box_route('accounts.invoice_print', ['id' => ':id']) }}";
                        
                        $.each(invoices, function(index, invoice) {
                            var viewUrl = viewBaseUrl.replace(':id', invoice.id);
                            var printUrl = printBaseUrl.replace(':id', invoice.id);
                            
                            var leadInitial = invoice.lead_name ? invoice.lead_name.charAt(0) : 'P';
                            var companyInitial = invoice.company_name ? invoice.company_name.charAt(0) : 'C';
                            
                            var dueDateFormatted = 'N/A';
                            if (invoice.due_date) {
                                var dt = new Date(invoice.due_date);
                                var options = { day: 'numeric', month: 'short', year: 'numeric' };
                                dueDateFormatted = dt.toLocaleDateString('en-GB', options);
                            }
                            
                            html += `
                            <div class="col-xxl-3 col-xl-4 col-md-6">
								<div class="card border">
									<div class="card-body">
										<div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
											<div class="users-profile">
												${getStatusBadge(invoice.status)}
											</div>
											<div class="dropdown table-action">
												<a href="#" class="action-icon bg-white" data-bs-toggle="dropdown" aria-expanded="false">
													<i class="fa fa-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-right">
													<a class="dropdown-item edit-invoice-btn" href="#" data-invoice='${JSON.stringify(invoice).replace(/"/g, "&quot;")}' data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit"><i class="ti ti-edit text-blue"></i> Edit</a>
													<a class="dropdown-item delete-invoice-btn" href="#" data-id="${invoice.id}" data-bs-toggle="modal" data-bs-target="#delete_invoices"><i class="ti ti-trash text-danger"></i> Delete</a>
													<a class="dropdown-item" href="${viewUrl}"><i class="ti ti-clipboard-copy text-green"></i> View Invoice</a>
													<a class="dropdown-item" href="${printUrl}" target="_blank"><i class="ti ti-printer text-info"></i> Print</a>
												</div>
											</div>
										</div>
										<div class="d-block">
											<div class="d-flex align-items-center justify-content-between mb-3">
												<div class="d-flex align-items-center">
													<a href="${viewUrl}" class="avatar avatar-sm border flex-shrink-0 me-2 bg-danger rounded d-flex justify-content-center align-items-center text-white fw-bold fs-14 text-decoration-none">
														${leadInitial}
													</a>
													<div class="ms-1">
														<h6 class="mb-0"><a href="${viewUrl}" class="text-dark text-decoration-none">${invoice.lead_name}</a></h6>
													</div>
												</div>
												<div>
													<span class="badge badge-purple-light">${invoice.invoice_no}</span>
												</div>
											</div>
											<div class="mb-3">
												<p class="d-flex align-items-center mb-2"><span class="avatar avatar-xs border me-2 text-dark d-flex justify-content-center align-items-center rounded"><i class="ti ti-moneybag fs-12"></i></span>Total Value : ${invoice.amount}</p>
												<p class="d-flex align-items-center mb-2"><span class="avatar avatar-xs border me-2 text-dark d-flex justify-content-center align-items-center rounded"><i class="ti ti-calendar-event fs-12"></i></span>Due Date : ${dueDateFormatted}</p>
												<p class="d-flex align-items-center mb-2"><span class="avatar avatar-xs border me-2 text-dark d-flex justify-content-center align-items-center rounded"><i class="ti ti-calendar-stats fs-12"></i></span>Paid Amount : ${invoice.paid_amount}</p>
												<p class="d-flex align-items-center"><span class="avatar avatar-xs border me-2 text-dark d-flex justify-content-center align-items-center rounded"><i class="ti ti-calendar-stats fs-12"></i></span>Balance Amount : ${invoice.balance_amount}</p>
											</div>
										</div>
										<div class="bg-light-400 rounded p-2">
											<div class="d-flex align-items-center">
												<a href="javascript:void(0);" class="avatar avatar-sm bg-warning text-white border me-2 rounded d-flex justify-content-center align-items-center fw-bold fs-14 text-decoration-none">
													${companyInitial}
												</a>
												<div class="d-flex flex-column ms-1">
													<span class="d-block fs-12 text-muted">Client</span>
													<a href="javascript:void(0);" class="text-default fw-semibold text-decoration-none">${invoice.company_name}</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>`;
                        });
                        
                        $('#invoice-grid-container').html(html);
                    }
                }
            });
        }
        
        // Initial load
        loadInvoices();

        // Configure toastr
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000",
            };
        }

        // Handle Add Invoice Form Submission
        $('#add-invoice-form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            var submitBtn = $('#btn-save-invoice');
            var originalText = submitBtn.text();
            
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            
            $.ajax({
                url: "{{ box_route('accounts.invoices.store') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        if (typeof toastr !== 'undefined') toastr.success(response.message);
                        $('#offcanvas_add').offcanvas('hide');
                        $('#add-invoice-form')[0].reset();
                        loadInvoices(); // Reload grid
                    }
                },
                error: function(xhr) {
                    if (typeof toastr !== 'undefined') toastr.error(xhr.responseJSON?.message || 'An error occurred.');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });

        // Handle Edit Invoice Button Click (Populate Form)
        $(document).on('click', '.edit-invoice-btn', function() {
            var invoice = $(this).data('invoice');
            
            if (typeof invoice === 'string') {
                invoice = JSON.parse(invoice);
            }
            
            $('#edit_invoice_id').val(invoice.id);
            
            if (invoice.company_id) {
            if ($('#edit_company_id').find("option[value='" + invoice.company_id + "']").length == 0) {
                var newOption = new Option(invoice.company_name, invoice.company_id, true, true);
                $('#edit_company_id').append(newOption);
            }
            $('#edit_company_id').val(invoice.company_id).trigger('change');
        }
            if (invoice.lead_id) {
            if ($('#edit_lead_id').find("option[value='" + invoice.lead_id + "']").length == 0) {
                var newOption = new Option(invoice.lead_name, invoice.lead_id, true, true);
                $('#edit_lead_id').append(newOption);
            }
            $('#edit_lead_id').val(invoice.lead_id).trigger('change');
        }
            
            $('#edit_amount').val(invoice.amount);
            $('#edit_paid_amount').val(invoice.paid_amount || 0);
            $('#edit_balance_amount').val(invoice.balance_amount || 0);
            if (invoice.currency) $('#edit_currency').val(invoice.currency).trigger('change');
            
            if (invoice.invoice_date) {
                var dateObj = new Date(invoice.invoice_date);
                var dateStr = dateObj.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                $('#edit_invoice_date').val(dateStr);
            }
            
            if (invoice.due_date) {
                var dueObj = new Date(invoice.due_date);
                var dueDateStr = dueObj.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                $('#edit_due_date').val(dueDateStr);
            } else {
                $('#edit_due_date').val('');
            }
            
            if (invoice.payment_method) $('#edit_payment_method').val(invoice.payment_method).trigger('change');
            if (invoice.status) $('#edit_status').val(invoice.status).trigger('change');
            if (invoice.remarks) $('#edit_remarks').val(invoice.remarks);
        });

        // Handle Edit Invoice Form Submission
        $('#edit-invoice-form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            var invoiceId = $('#edit_invoice_id').val();
            var submitBtn = $('#btn-update-invoice');
            var originalText = submitBtn.text();
            
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
            
            $.ajax({
                url: "{{ box_route('accounts.invoices.update', ['id' => ':id']) }}".replace(':id', invoiceId),
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        if (typeof toastr !== 'undefined') toastr.success(response.message);
                        $('#offcanvas_edit').offcanvas('hide');
                        loadInvoices(); // Reload grid
                    }
                },
                error: function(xhr) {
                    if (typeof toastr !== 'undefined') toastr.error(xhr.responseJSON?.message || 'An error occurred.');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });

        // Handle Delete Button Click (Set ID)
        $(document).on('click', '.delete-invoice-btn', function() {
            var invoiceId = $(this).data('id');
            $('#delete_invoice_id').val(invoiceId);
        });

        // Handle Confirm Delete
        $('#btn-confirm-delete').on('click', function() {
            var invoiceId = $('#delete_invoice_id').val();
            var submitBtn = $(this);
            var originalText = submitBtn.text();
            
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');
            
            $.ajax({
                url: "{{ box_route('accounts.invoices.destroy', ['id' => ':id']) }}".replace(':id', invoiceId),
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        if (typeof toastr !== 'undefined') toastr.success(response.message);
                        $('#delete_invoices').modal('hide');
                        loadInvoices(); // Reload grid
                    }
                },
                error: function(xhr) {
                    if (typeof toastr !== 'undefined') toastr.error(xhr.responseJSON?.message || 'An error occurred.');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });

        // Auto-calculate balance amounts
        function calculateBalance(prefix) {
            var total = parseFloat($('#' + prefix + '_amount').val()) || 0;
            var paid = parseFloat($('#' + prefix + '_paid_amount').val()) || 0;
            $('#' + prefix + '_balance_amount').val((total - paid).toFixed(2));
        }
        
        $('#add_amount, #add_paid_amount').on('input', function() { calculateBalance('add'); });
        $('#edit_amount, #edit_paid_amount').on('input', function() { calculateBalance('edit'); });
    });

    
    function initAjaxSelects() {
        $('.custom-ajax-select').each(function() {
            var $this = $(this);
            var isClient = $this.attr('id').indexOf('company') !== -1;
            var parent = $this.closest('.offcanvas');
            
            $this.select2({
                dropdownParent: parent,
                placeholder: isClient ? 'Choose Client' : 'Choose Project',
                ajax: {
                    url: isClient ? "{{ box_route('accounts.ajax_clients') }}" : "{{ box_route('accounts.ajax_leads') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                }
            });
        });
    }

    
    $(document).ready(function() {
        initAjaxSelects();
    });
    
</script>
@endpush
@endsection
