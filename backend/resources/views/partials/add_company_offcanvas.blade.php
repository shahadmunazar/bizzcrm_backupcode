		<!-- Add Company -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Add New Company</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form id="add-company-form" onsubmit="return false;" enctype="multipart/form-data">
                        <!-- Company Tab -->
                        <div class="tab-pane fade show active" id="company_tab" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Company Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="bill_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Contact Number</label>
                                        <input type="text" class="form-control" name="bill_phone">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">GSTIN</label>
                                        <input type="text" class="form-control" name="gstin">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">PAN Number</label>
                                        <input type="text" class="form-control" name="pan">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Pin Code</label>
                                        <input type="text" class="form-control" name="bill_pin_code">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">State <span class="text-danger">*</span></label>
                                        <select class="select2 form-control" name="bill_state_id" id="bill_state_id">
                                            <option value="">--Select--</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Address</label>
                                        <textarea class="form-control" name="bill_address"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">City <span class="text-danger">*</span></label>
                                        <select class="select2 form-control" name="bill_city_id" id="bill_city_id">
                                            <option value="">--Select--</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Status <span class="text-danger">*</span></label>
                                        <select class="select" name="status" id="status_select">
                                            <option value="Enable">Enable</option>
                                            <option value="Disabled">Disable</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-start mt-3">
                        <button type="submit" class="btn btn-primary me-2" id="btn-save-company">Submit</button>
                        <button type="button" class="btn btn-outline-light border text-secondary" data-bs-dismiss="offcanvas">Reset</button>
                    </div>
                </form>
			</div>
		</div>
		<!-- /Add Company -->

