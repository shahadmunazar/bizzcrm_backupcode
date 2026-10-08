@extends('layouts.main')
@section('title', 'Invoices')
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
									<h4 class="page-title">Invoices<span class="count-title">123</span></h4>
								</div>
								<div class="col-8 text-end">
									<div class="head-icons">
										<a href="invoices.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
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

						<div class="card ">
							<div class="card-header">
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
											<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_invoice"><i class="ti ti-square-rounded-plus me-2"></i>Add New Invoices</a>
										</div>
									</div>
								</div>
								<!-- /Search -->
							</div>
							<div class="card-body">
								<!-- Filter -->
								<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<div class="sort-dropdown drop-down task-drops me-3">
											<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown">All Invoices </a>
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
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Estimations ID</p>
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
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Project</p>
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
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Amount</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-loc" class="check">
															<label for="col-loc" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Paid Amount</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-rate" class="check">
															<label for="col-rate" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Balance Amount</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-owner" class="check">
															<label for="col-owner" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Status</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-status" class="check">
															<label for="col-status" class="checktoggle"></label>
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
																<a href="invoices.html" class="btn btn-primary">Filter</a>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="view-icons">
											<a href="invoices.html" class="active"><i class="ti ti-list-tree"></i></a>
											<a href="invoice-grid.html"><i class="ti ti-grid-dots"></i></a>
										</div>
									</div>
								</div>
								<!-- /Filter -->

								<!-- Projects List -->
								<div class="table-responsive custom-table">
									<table class="table datatable">
										<thead class="thead-light">
											<tr>
												<th class="no-sort">
													<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
												</th>
												<th></th>
												<th>Invoice ID</th>
												<th>Client</th>
												<th>Project</th>
												<th>Products</th>
												<th>Created Date</th>
												<th>Due Date</th>
												<th>Amount</th>
												<th>Paid Amount</th>
												<th>Balance Amount</th>
												<th>Status</th>
												<th class="text-end">Action</th>
											</tr>
										</thead>
										<tbody id="invoices-table-body">
                                        </tbody>
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


		@include('accounts.partials.add_invoice_modal')


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
								<h5 class="mb-2">Invoice Items</h5>
								<div id="edit-invoice-items-list" class="invoice-items-list">
									<!-- Rows will be added here -->
								</div>
								<button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="addInvoiceItemRow('edit')"><i class="ti ti-plus"></i> Add Item</button>
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
							<input type="hidden" id="delete_invoice_id">
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<button type="button" class="btn btn-danger" id="btn-confirm-delete">Yes, Delete it</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Invoices -->

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

		<style>
		.item-row input:focus, .item-row select:focus {
			border-bottom: 2px solid #e02a11 !important;
			outline: none !important;
			box-shadow: none !important;
		}
		.item-row .select2-container .select2-selection--single {
			border: none !important;
			border-bottom: 1px solid #ced4da !important;
			border-radius: 0 !important;
			background: transparent !important;
		}
		.item-row .select2-container.select2-container--focus .select2-selection--single {
			border-bottom: 2px solid #e02a11 !important;
		}
		</style>
@endsection

@include('accounts.partials.invoice_scripts', ['onSuccess' => 'loadInvoices();'])
