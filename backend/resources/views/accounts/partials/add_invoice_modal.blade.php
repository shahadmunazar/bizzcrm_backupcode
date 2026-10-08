		<!-- Add New Invoices -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add_invoice">
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
									@if(isset($prefill_lead))
										<option value="{{ $prefill_lead->id }}" selected>{{ $prefill_lead->name }}</option>
									@else
										<option value="">Choose Project</option>
									@endif
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
								<h5 class="mb-2">Invoice Items</h5>
								<div id="add-invoice-items-list" class="invoice-items-list">
									<!-- Rows will be added here -->
								</div>
								<button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="addInvoiceItemRow('add')"><i class="ti ti-plus"></i> Add Item</button>
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
