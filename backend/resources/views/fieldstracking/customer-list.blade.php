@extends('layouts.main')
@section('title', 'Customer List')
@section('content')
<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									<h4 class="page-title">Customer List</h4>
								</div>
								<div class="col-4 text-end">
									<div class="head-icons">
										<a href="javascript:void(0);" id="refresh-table" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
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
											<input type="text" class="form-control" placeholder="Search for Customer">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">

										    
											<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_customer"><i class="ti ti-square-rounded-plus me-2"></i> Add Customer</a>
										</div>
									</div>
								</div>
								<!-- /Search -->
							</div>
							<div class="card-body">
								<!-- Filter -->
								<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<div class="dropdown me-2">
											<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-sort-ascending-2 me-2"></i>Sort </a>
											<div class="dropdown-menu  dropdown-menu-start">
												<ul>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="asc">
															<i class="ti ti-circle-chevron-right me-1"></i>Ascending
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="desc">
															<i class="ti ti-circle-chevron-right me-1"></i>Descending
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="recently_viewed">
															<i class="ti ti-circle-chevron-right me-1"></i>Recently Viewed
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="recently_added">
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
										<div class="dropdown me-2">
											<a href="javascript:void(0);" class="btn bg-soft-purple text-purple" data-bs-toggle="modal" data-bs-target="#manageColumnsModal"><i class="ti ti-columns-3 me-2"></i>Manage Columns</a>
										</div>

									</div>
								</div>
								<!-- /Filter -->

								<!-- Contact List -->
								<div class="table-responsive custom-table">
									<table class="table datatable" id="customerTable">
										<thead class="thead-light">
											<tr>
												<th>Customer Name</th>
												<th>Location Status</th>
												<th>Check Ins</th>
												<th>Check In</th>
												<th>Telephonic Order</th>
												<th>Contact Person</th>
												<th>Designation</th>
												<th>Whatsapp Number</th>
												<th>Mobile</th>
												<th>Phone</th>
												<th>Email</th>
												<th>Secondary Email</th>
												<th>GST Number</th>
												<th>Drug License</th>
												<th>PAN Number</th>
												<th>Address</th>
												<th>Location</th>
												<th>City</th>
												<th>State</th>
												<th>Country</th>
												<th>Postal Code</th>
												<th>Territory</th>
												<th>Experience</th>
												<th>Lead Source</th>
												<th>Query Notes</th>
												<th>Customer Level</th>
												<th>Parent Name</th>
												<th>Customer Type</th>
												<th>Payment Terms</th>
												<th>Credit Limit</th>
												<th>Outstanding Amount</th>
												<th>Assigned Staff</th>
												<th class="text-center">Action</th>
											</tr>
										</thead>
										<tbody>
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
								<!-- /Contact List -->

							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->
		
		
		
		
		<!-- Add New Customer -->
				<!-- Manage Columns Modal -->
				<div class="modal fade" id="manageColumnsModal" tabindex="-1" aria-labelledby="manageColumnsModalLabel" aria-hidden="true">
				    <div class="modal-dialog modal-dialog-centered">
				        <div class="modal-content">
				            <div class="modal-header">
				                <h5 class="modal-title" id="manageColumnsModalLabel">Manage Columns</h5>
				                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				            </div>
				            <div class="modal-body">
				                <div class="mb-3">
				                    <button type="button" class="btn btn-sm btn-outline-primary" id="showAllColumns">Show All</button>
				                    <button type="button" class="btn btn-sm btn-outline-secondary" id="hideAllColumns">Hide All</button>
				                </div>
				                <div class="list-group" id="columnsList">
				                    <!-- Columns will be populated dynamically -->
				                </div>
				            </div>
				            <div class="modal-footer">
				                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				                <button type="button" class="btn btn-primary" id="applyColumnChanges">Apply Changes</button>
				            </div>
				        </div>
				    </div>
				</div>
				<!-- /Manage Columns Modal -->

		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add_customer">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Add New Customer</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form id="addCustomerForm" enctype="multipart/form-data">
				@csrf
                <input type="hidden" name="existing_lead_id" id="existing_lead_id">
                <input type="hidden" name="customer_id" id="edit_customer_id">
                
                <div class="card bg-light" id="convert_lead_section">
                    <div class="card-body">
                        <h5 class="mb-2"><i class="ti ti-search text-primary me-2"></i>Convert Existing Lead</h5>
                        <p class="text-muted small">Search by Name, Email, Phone, or Company to quickly auto-fill this form and convert the lead.</p>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-0">
                                    <select class="form-control select2-search-leads" style="width: 100%;">
                                        <option value="">Search for a lead...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card bg-light" id="location_tracking_section">
                    <div class="card-body">
                        <h5 class="mb-2"><i class="ti ti-map-pin text-primary me-2"></i>Location Tracking</h5>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="save_current_location">
                            <label class="form-check-label fw-bold" for="save_current_location">Save Current Location (Latitude & Longitude)</label>
                        </div>
                        <div id="location_capture_status" class="mb-2" style="display:none;"></div>
                        <input type="hidden" name="latitude" id="form_latitude">
                        <input type="hidden" name="longitude" id="form_longitude">
                    </div>
                </div>

				     <div class="card">
						<div class="card-body">
						    <h5 class="mb-2">Business Details</h5>
							<div class="row">
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Business Name <span class="text-danger"> *</span></label>
										<input type="text" class="form-control" name="company_name" placeholder="Enter Business Name" required>
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">GST No. <span class="text-danger"> *</span></label>
										<input type="text" class="form-control" name="gstin_number" placeholder="Enter GST No.">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Drug License</label>
										<input type="text" class="form-control" name="drug_license" placeholder="Enter Drug License">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">PAN Number</label>
										<input type="text" class="form-control" name="pan_number" placeholder="Enter PAN Number">
									</div>									
								</div>
							</div>
                        </div>
                    </div>
					
					<div class="card">
						<div class="card-body">
							<div class="row">			
								<h5 class="mb-2">Company Image</h5>
								<div class="col-md-12">
									<div class="mb-3">
									    <label class="col-form-label">Upload Company Image</label>
										<input type="file" class="form-control" name="company_image" id="company_image_input">
										<div id="current_company_image_preview" class="mt-2" style="display: none;">
											<p class="mb-1 small text-muted">Current Image:</p>
											<img src="" alt="Company Image" class="img-thumbnail" style="max-height: 100px; max-width: 150px; object-fit: contain;">
										</div>
									</div>									
								</div>
					        </div>
                        </div>
                    </div>
					
					<div class="card">
						<div class="card-body">
						    <h5 class="mb-2">Customer Level & Type</h5>
							<div class="row">
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Customer Level <span class="text-danger"> *</span></label>
										<input type="text" class="form-control" name="customer_level" placeholder="Enter Customer Level">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Parent Name <span class="text-danger"> *</span></label>
										<input type="text" class="form-control" name="parent_name" placeholder="Enter Parent Name">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Whatsapp Number <span class="text-danger"> *</span></label>
										<input type="text" class="form-control" name="whatsapp_number" placeholder="Enter Whatsapp Number">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Customer Type <span class="text-danger"> *</span></label>
										<select class="select" name="customer_type">
											<option value="" disabled selected>Select Customer Type</option>
											<option>Primary Customer</option>
											<option>Secondary Customer</option>
											<option>Territory Customer</option>
										</select>
									</div>									
								</div>
							</div>
                        </div>
                    </div>
					
					<div class="card">
						<div class="card-body">
						    <h5 class="mb-2">Contact Person Details</h5>
							<div class="row">
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Name <span class="text-danger"> *</span></label>
										<input type="text" class="form-control" name="name" placeholder="Enter Name" required>
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Designation</label>
										<input type="text" class="form-control" name="designation" placeholder="Enter Designation">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Email <span class="text-danger"> *</span></label>
										<input type="email" class="form-control" name="email" placeholder="Enter Email">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Secondary Email</label>
										<input type="email" class="form-control" name="sec_email" placeholder="Enter Secondary Email">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Phone <span class="text-danger"> *</span></label>
										<input type="text" class="form-control" name="phone" placeholder="Enter Phone No." required>
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Mobile</label>
										<input type="text" class="form-control" name="mobile" placeholder="Enter Mobile No.">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Address</label>
										<input type="text" class="form-control" name="address" placeholder="Enter Address">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Experience</label>
										<input type="text" class="form-control" name="experience" placeholder="Enter Experience">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Pin Code <span class="text-danger"> *</span></label>
										<input type="text" class="form-control" name="postal_code" placeholder="Pin Code">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Select Country</label>
										<select class="select" name="country_id">
											<option value="" disabled selected>Select Country</option>
											<option value="101">India</option>
										</select>
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Select City <span class="text-danger"> *</span></label>
										<select class="select" name="city_id">
											<option value="" disabled selected>Select City</option>
											<option>Sarkaghat</option>
											<option>Mandi</option>
										</select>
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
									    <label class="col-form-label">Select State <span class="text-danger"> *</span></label>
										<select class="select" name="state_id">
											<option value="" disabled selected>Select State</option>
											<option>Himachal Pradesh</option>
											<option>Punjab</option>
										</select>
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Territory <span class="text-danger"> *</span></label>
										<input type="text" class="form-control" name="territory" placeholder="Enter Territory">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Location</label>
										<input type="text" class="form-control" name="location" placeholder="Enter Location">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Lead Source</label>
										<select class="select" name="lead_source_id">
											<option value="" disabled selected>Select Lead Source</option>
											<option value="1">Direct</option>
										</select>
									</div>									
								</div>
							</div>
                        </div>
                    </div>
					
					
                   					
					<div class="card">
						<div class="card-body">
							<div class="row">			
								<h5 class="mb-2">Assign</h5>
								<div class="col-md-12">
									<div class="mb-3">
									    <label class="col-form-label">Assing Staff <span class="text-danger"> *</span></label>
										<select class="select" name="assignees[]" multiple>
											<option value="Shitender Kumar">Shitender Kumar</option>
											<option value="Shefali Sharma">Shefali Sharma</option>
											<option value="Munish Malhotra">Munish Malhotra</option>
										</select>
									</div>									
								</div>
					        </div>
                        </div>
                    </div>
					
					
					
					<div class="card">
						<div class="card-body">
						    <h5 class="mb-2">Credit & Payment Terms</h5>
							<div class="row">
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Payment Terms</label>
										<input type="text" class="form-control" name="payment_terms" placeholder="Payment Terms">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Credit Limit </label>
										<input type="number" step="0.01" class="form-control" name="credit_limit" placeholder="Credit Limit">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Outstanding Amount </label>
										<input type="number" step="0.01" class="form-control" name="outstanding_amount" placeholder="Outstanding Amount">
									</div>									
								</div>
							</div>
                        </div>
                    </div>
					
					
					
					
					
                   
			        <div class="card">
						<div class="card-body">
						    <h5 class="mb-2">Other Notes</h5>
							<div class="row">
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Query / Notes</label>
										<textarea class="form-control" name="query_notes" rows="3" placeholder="Enter any notes here"></textarea>
									</div>									
								</div>
							</div>
                        </div>
                    </div>
					
			        <div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary">Submit</button>
					</div>
					</form>		
			</div>
		</div>
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		<!-- Add Customer Detail -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_cus_detail">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Customer Details</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
						<div class="card border border-info card-bg-light">
							<div class="card-body">
								<div class="d-flex align-items-center w-100">
									<div class="me-2">
										<span class="avatar avatar-rounded">
											<img id="view_customer_image" src="assets/img/profiles/avatar-04.jpg" alt="img">
										</span>
									</div>
									<div class="">
										<div class="fs-15 fw-semibold" id="view_customer_company_name">Customer 1</div>
										<span class="badge bg-soft-info" id="view_customer_level">Tertiary Customer</span>
									</div>
									<div class="ms-auto">
										<a href="javascript:void(0);" class="text-fixed-white"><i class="fa-solid fa-ellipsis-vertical"></i></a>
									</div>
								</div>
							</div>
						</div>
						
						<div class="text-center px-3 pb-3 border-bottom">
							
									 <div>
										 <div class="d-flex align-items-center justify-content-between mb-2">
											 <span class="d-inline-flex align-items-center">
												 <i class="fa fa-map-marker me-2"></i>
												Location
											 </span>
											 <p class="text-dark" id="view_customer_location">Chandigarh</p>
										 </div>
										 <div class="d-flex align-items-center justify-content-between mb-2">
											 <span class="d-inline-flex align-items-center">
												 <i class="ti ti-star me-2"></i>
												Contact Person
											 </span>
											 <p class="text-dark" id="view_contact_person">Ajay Thakur</p>
										 </div>
										 

										 
										 <div class="row gx-2 mt-3">
											 <div class="col-6">
												 <div>
													 <button type="button" class="btn bg-soft-purple text-dark w-100" id="btn-telephonic-order">Telephonic Order</button>
												 </div>
											 </div>
											 <div class="col-6">
												 <div>
													 <button type="button" class="btn bg-soft-purple text-dark w-100" id="btn-check-in">Check In</button>
												 </div>
											 </div>
											 <div class="col-6 mt-3">
												 <div>
													 <button type="button" class="btn bg-soft-purple text-dark w-100" id="btn-choose-activity">Choose Activity</button>
												 </div>
											 </div>
										 </div>
									 </div>
						</div>
			
			
			
				 <div class="card">
							 <div class="card-body p-0">
								 
								 
								 
								<div class="p-3 border-bottom">
								    <ul class="nav nav-tabs mb-3 tab-style-6" id="myTab-3" role="tablist">
									<li class="nav-item" role="presentation">
										<button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#products-tab-pane" type="button" role="tab" aria-controls="products-tab-pane" aria-selected="true"> <i class="fa fa-line-chart me-1 align-middle d-inline-block" aria-hidden="true" ></i> Insights</button>
									</li>
									<li class="nav-item" role="presentation">
										<button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales-tab-pane" type="button" role="tab" aria-controls="sales-tab-pane" aria-selected="false"> <i class="fa fa-archive align-middle d-inline-block" aria-hidden="true"></i> Orders</button>
									</li>
									<li class="nav-item" role="presentation">
										<button class="nav-link" id="profit-tab" data-bs-toggle="tab" data-bs-target="#profit-tab-pane" type="button" role="tab" aria-controls="profit-tab-pane" aria-selected="false"><i class="fa fa-pie-chart me-1 align-middle d-inline-block" aria-hidden="true"></i> Activity</button>
									</li>
								</ul>
								<div class="tab-content" id="myTabContent2">
									<div class="tab-pane fade show active p-0 border-bottom-0" id="products-tab-pane" role="tabpanel" aria-labelledby="products-tab" tabindex="0">
									
				<div class="row">
					
					<div class="col-xl-4">
						<div class="card border border-dark">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between">
									<span class="avatar avatar-md rounded bg-soft-purple text-purple mb-3">
										<i class="ti ti-building fs-16"></i>
									</span>
								</div>
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<h2 class="mb-1" id="view_insight_tc">0</h2>
										<p class="fs-13">TC</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-4">
						<div class="card border border-dark">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between">
									<span class="avatar avatar-md rounded bg-soft-purple text-purple mb-3">
										<i class="ti ti-building fs-16"></i>
									</span>
								</div>
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<h2 class="mb-1" id="view_insight_pc">0</h2>
										<p class="fs-13">PC</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-4">
						<div class="card border border-dark">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between">
									<span class="avatar avatar-md rounded bg-soft-purple text-purple mb-3">
										<i class="ti ti-building fs-16"></i>
									</span>
								</div>
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<h2 class="mb-1" id="view_insight_output">0%</h2>
										<p class="fs-13">Output</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
									
									
									    
							    <div class="text-center px-3 pb-3 border-bottom">

									 <div>
										 <div class="d-flex align-items-center justify-content-between mb-2">
											 <span class="d-inline-flex align-items-center">
												 <i class="fa fa-calendar me-2"></i>
												Last Visit Date
											 </span>
											 <p class="text-dark" id="view_insight_last_visit">14 May 2025 </p>
										 </div>
										 <div class="d-flex align-items-center justify-content-between mb-2">
											 <span class="d-inline-flex align-items-center">
												 <i class="fa fa-calendar me-2"></i>
												Last Order Date
											 </span>
											 <p class="text-dark" id="view_insight_last_order_date">14 May 2025 </p>
										 </div>
										 <div class="d-flex align-items-center justify-content-between mb-2">
											 <span class="d-inline-flex align-items-center">
												 <i class="fa me-2">₹</i>
												Last Order Value
											 </span>
											 <p class="text-dark" id="view_insight_last_order_value">₹ 0 </p>
										 </div>
										 <div class="d-flex align-items-center justify-content-between mb-2">
											 <span class="d-inline-flex align-items-center">
												 <i class="fa me-2">₹</i>
												Avg. Order Value Historic
											 </span>
											 <p class="text-dark" id="view_insight_avg_order_value">₹ 0 </p>
										 </div>

										
									 </div>
								</div>
									
									

									</div>
									<div class="tab-pane fade" id="sales-tab-pane" role="tabpanel" aria-labelledby="sales-tab" tabindex="0">
										
										Orders
										
									</div>
									<div class="tab-pane fade" id="profit-tab-pane" role="tabpanel" aria-labelledby="profit-tab" tabindex="0">
										<div id="customer-activities-container" class="mt-3">
											<!-- Activities will be loaded here dynamically -->
											<div class="text-center p-3">
												<div class="spinner-border text-primary" role="status">
													<span class="visually-hidden">Loading...</span>
												</div>
											</div>
										</div>
									</div>
									
								</div>
								</div>
								 
								 
								 
							 </div>
					</div>   


					
			</div>
		</div>

		                    
							
							
							<!-- Payment Modal -->
		                        <div id="payment-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<h4 class="modal-title" id="standard-modalLabel">Record Payment</h4>
												<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
											</div>
											<div class="modal-body">
												<form id="addCustomerForm" enctype="multipart/form-data">
				@csrf
													<div class="card">
														<div class="card-body">
															<div class="row">
															
																<div class="col-md-12">
																	<div class="mb-3">
																		<label class="col-form-label">Name</label>
																		<input type="text" class="form-control">
																	</div>									
																</div>
																<div class="col-md-12">
																	<div class="mb-3">
																		<label class="col-form-label">Amount <span class="text-danger"> *</span></label>
																		<input type="text" class="form-control">
																	</div>									
																</div>
																<div class="col-md-12">
																	<div class="mb-3">
																		<label class="col-form-label">Mode of Payment <span class="text-danger"> *</span></label>
																		<select class="select">
																			<option value="">Cash</option>
																			<option value="">Cheque/DD</option>
																			<option value="">Net Banking</option>
																			<option value="">UPI</option>
																			<option value="">Debit/Credit Card</option>
																			<option value="">Third-party</option>
																			<option value="">Other</option>
																		</select>
																	</div>									
																</div>
																<div class="col-md-12">
																	<div class="mb-3">
																		<label class="col-form-label">Transaction ID</label>
																		<input type="text" class="form-control">
																	</div>									
																</div>
																<div class="col-md-12">
																	<div class="mb-3">
																		<label class="col-form-label">Transaction Date</label>
																		<input type="text" class="form-control">
																	</div>									
																</div>
																<div class="col-md-12">
																	<div class="mb-3">
																		<label class="col-form-label">Comments</label>
																		<textarea class="form-control"></textarea>
																	</div>									
																</div>
																
																<div class="col-md-12">
																	<div class="mb-3">
																		<label class="col-form-label">Comments</label>
																		<textarea class="form-control"></textarea>
																	</div>									
																</div>
																
																
																
																
																<div class="col-md-12">
																	<div class="mb-3">
																		<label class="col-form-label">Upload Photo / Docs</label>
																		<div class="profile-upload">
																			<div class="profile-upload-img">
																				<span><i class="ti ti-photo"></i></span>
																				<img src="assets/img/profiles/avatar-20.jpg" alt="img" class="preview1">
																				<button type="button" class="profile-remove">
																					<i class="ti ti-x"></i>
																				</button>
																			</div>
																			<div class="profile-upload-content">
																				<label class="profile-upload-btn">
																					<i class="ti ti-file-broken"></i> Upload File
																					<input type="file" class="input-img">
																				</label>
																				<p>JPG, GIF or PNG. Max size of 800K</p>
																			</div>
																		</div>
																	</div>									
																</div>
															</div>
														</div>
													</div>						
													
													
													<div class="card">
						<div class="card-body">
						    <h5 class="mb-2">Other Notes</h5>
							<div class="row">
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Query / Notes</label>
										<textarea class="form-control" name="query_notes" rows="3" placeholder="Enter any notes here"></textarea>
									</div>									
								</div>
							</div>
                        </div>
                    </div>
					
			        <div class="d-flex align-items-center justify-content-end">
														<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
														<button type="submit" class="btn btn-primary">Save</button>
													</div>
															
											</div>
										</div>
													
												</form>
											</div>
		
										</div>
									</div>
								</div>
		
		
		
		
		
		
		
        
		
		
					
				
			</div>
		</div>
		<!-- /Add Company -->
		
		
		

		<!-- Edit Company -->
		<!-- Edit Company -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Edit Beat List</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form id="addCustomerForm" enctype="multipart/form-data">
				@csrf
					<div class="card">
						<div class="card-body">
							<div class="row">
							
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Beat Name <span class="text-danger"> *</span></label>
										<input type="text" class="form-control">
									</div>									
								</div>
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Add Town/City</label>
										<input type="email" class="form-control">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Add Territory</label>
										<input type="text" class="form-control">
									</div>									
								</div>
							</div>
                        </div>
                    </div>						
					<div class="card">
						<div class="card-body">
							<div class="row">			
								<h5 class="mb-2">Assign Staff</h5>
								<div class="col-md-12">
									<div class="mb-3">
										<select class="select" name="assignees[]" multiple>
											<option value="Shitender Kumar">Shitender Kumar</option>
											<option value="Shefali Sharma">Shefali Sharma</option>
											<option value="Munish Malhotra">Munish Malhotra</option>
										</select>
									</div>									
								</div>
					        </div>
                        </div>
                    </div>
					
					<div class="card">
						<div class="card-body">
							<div class="row">	
							
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Customer Level</label>
										<select class="select">
											<option disabled>Select Customer Level</option>
											<option>Primary Customer</option>
											<option>secondary Customer</option>
										</select>
									</div>
									<div class="mb-3">
									    <label class="col-form-label">Secondary Customer</label>
										<select class="select">
											<option value="" disabled>Select Secondary Customer</option>
											<option value="">Ganesh Tradors</option>
											<option>Medizia Biotech</option>
										</select>
									</div>
                                    <div class="mb-3">
										<label class="col-form-label">Add Customer</label>
										<select class="select">
											<option>0 Customer Selected</option>
											<option>0</option>
											<option>1</option>
										</select>
									</div> 
									<div class="mb-3">
										<label class="col-form-label">Add Lead</label>
										<select class="select">
											<option>0 Lead Selected</option>
											<option>0</option>
											<option>1</option>
										</select>
									</div> 


									
								</div>
								
							</div>
							
					    </div>
                    </div>
                    <div class="card">
						<div class="card-body">
								
                            <div class="row">   
								<h4 class="mb-2">Add Frequency</h4>
								<div class="col-md-6">
								    
									<div class="mb-3">
										<select class="select">
											<option value="Weekly">Weekly</option>
											<option value="Monthly">Monthly</option>
										</select>
									</div>									
								</div>

								<div class="col-md-6">
									<div class="mb-3">
										<input type="text" placeholder="Enter Frequency" class="form-control">
									</div>									
								</div>
								
								<div class="col-md-12">
									<div class="mb-3">
									    <input type="text" placeholder="Define TC in a day" class="form-control">
									</div>
								</div>
								
								
							</div>	
								
					</div>
					</div>
			        <div class="card">
						<div class="card-body">
						    <h5 class="mb-2">Other Notes</h5>
							<div class="row">
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Query / Notes</label>
										<textarea class="form-control" name="query_notes" rows="3" placeholder="Enter any notes here"></textarea>
									</div>									
								</div>
							</div>
                        </div>
                    </div>
					
			        <div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
							
			</div>
		</div>
					
				</form>
			</div>
		</div>
		<!-- /Edit Company -->
		<!-- /Edit Company -->
		 
		
		<!-- /Upgrade Plane -->

		<!-- Delete Company -->
		<div class="modal fade" id="delete_company" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Company?</h4>
							<p class="mb-0">Are you sure you want to remove <br> company you selected.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="company.html" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Company -->

		<!-- Company Detail -->
		<div class="modal fade custom-modal" id="company_detail">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Company Detail</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body pb-0">
						<div class="mb-3">
							<div class="d-flex justify-content-between align-items-center rounded bg-light-400 p-3">                                                
								<div class="file-name-icon d-flex align-items-center">
									<a href="#" class="avatar avatar-sm border rounded p-1 me-2 flex-shrink-0">
										<img src="assets/img/icons/company-icon-01.svg" class="w-auto h-auto" alt="img">
									</a> 
									<div>
										<p class="text-gray-9 fw-medium mb-0">NovaWave LLC</p>
									</div>
								</div>                                        
								<span class="badge badge-success">Active</span>
							</div>
						</div>
						<div>                                                
							<p class="text-gray-9 fw-medium">Basic Info</p>                                            
							<div class="border-bottom mb-3">
								<div class="row align-items-center">
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Account URL</p>
											<p class="text-gray-9">nw.example.com</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Phone Number</p>
											<p class="text-gray-9">+1 875455453</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Website</p>
											<p class="text-gray-9">www.exmple.com</p>
										</div>
									</div>
								</div>
								<div class="row align-items-center">
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Currency</p>
											<p class="text-gray-9">United Stated Dollar (USD)</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Language</p>
											<p class="text-gray-9">English</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Addresss</p>
											<p class="text-gray-9">3705 Lynn Avenue, Phelps, WI 54554</p>
										</div>
									</div>
								</div>
							</div>
							<p class="text-gray-9 fw-medium">Plan Details</p>                                            
							<div>
								<div class="row align-items-center">
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Plan Name</p>
											<p class="text-gray-9">Advanced</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Plan Type</p>
											<p class="text-gray-9">Monthly</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Price</p>
											<p class="text-gray-9">$200</p>
										</div>
									</div>
								</div>
								<div class="row align-items-center">
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Register Date</p>
											<p class="text-gray-9">12 Sep 2024</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Expiring On</p>
											<p class="text-gray-9">11 Oct 2024</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						
					</div>
					
					
				</div>
			</div>
		</div>
		<!-- /Company Detail -->
	<!-- Check In History Modal -->
    <div class="modal fade" id="check-in-history-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Check-In History: <span id="historyCustomerName" class="text-primary"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="checkInHistoryLoading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading check-in history...</p>
                    </div>
                    <div id="checkInHistoryContent" class="d-none">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="checkInHistoryTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Checked In By</th>
                                        <th>Location</th>
                                        <th>Remarks</th>
                                        <th>Images</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Populated via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Check In History Modal -->

	<!-- Check In Modal -->
	<div id="check-in-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="standard-modalLabel">Check In</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="checkInForm" enctype="multipart/form-data">
					@csrf
					<input type="hidden" name="customer_id" id="checkin_customer_id">
					<input type="hidden" name="latitude" id="checkin_latitude">
					<input type="hidden" name="longitude" id="checkin_longitude">
					<div class="modal-body">
						<div class="card card-bg-light border border-dark mb-3">
							<div class="card-body">
								<div class="d-flex align-items-center w-100">
									<div class="me-2">
										<span class="avatar avatar-rounded">
											<i class="fa-solid fa-location-dot" style="color: #222;font-size: 46px;"></i>
										</span>
									</div>
									<div class="">
										<div class="fs-15 fw-semibold" id="checkin_customer_name_display">Customer</div>
										<p class="mb-0 text op-7 fs-12">Do you want to check in at: <span id="checkin_customer_name_text">Customer</span></p>
										<p class="mb-0 text-dark op-7 fs-12"><strong><span style="color:red;">*</span> you won't be able to check out from a customer if no activity is performed</strong></p>
										<p id="detectedLocationDisplay" class="mb-0 text-success fw-bold fs-12 mt-2" style="display: none;"></p>
									</div>
								</div>
							</div>
						</div>
						
						<div class="card card-bg-light border border-dark mb-3">
							<div class="card-body">
								<h5 class="mb-3">Take a Photo of the store before Checking In</h5>
								<div class="mb-2">
										<label class="form-label">Upload File(s) (Max 10 Images)</label>
										<input type="file" name="images[]" id="checkInImages" class="form-control" multiple accept="image/*" capture="environment">
								</div>
							</div>
						</div>

						<div class="card card-bg-light border border-dark mb-0">
							<div class="card-body">
								<h5 class="mb-2">Remarks</h5>
								<textarea name="remarks" class="form-control" rows="3" placeholder="Enter remarks..."></textarea>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary" id="checkInSubmitBtn">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Add Activity Modal -->
	<div id="add-activity-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Record Activity: <span id="add-activity-type-name" class="text-primary"></span></h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="customerAddActivityForm">
						<input type="hidden" name="lead_id" id="activity_customer_id">
						<input type="hidden" name="type" value="call">
						<input type="hidden" name="activity_setting_id" id="activity_setting_id">
						<input type="hidden" name="activity_type" id="activity_type_id">
						<input type="hidden" name="activity_status_id" id="activity_status_id" value="">

						<div class="form-group mb-3">
							<label class="col-form-label">Activity Date <span class="text-danger">*</span></label>
							<input type="datetime-local" class="form-control" name="activity_date" id="activity_date" required>
						</div>

						<div class="form-group mb-3">
							<label class="col-form-label">Notes / Description</label>
							<textarea class="form-control" name="description" id="activity_description" rows="4"></textarea>
						</div>

						<div class="text-end">
							<button type="submit" class="btn btn-primary" id="customerSubmitActivityBtn">Save Activity</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Telephonic Order Modal -->
	<div id="telephonic-order-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Record Telephonic Order</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="telephonicOrderForm">
						<input type="hidden" name="type" value="general">
						<input type="hidden" name="activity_type" value="Telephonic Order">
						<div class="form-group mb-3">
							<label class="col-form-label">Order Details / Value <span class="text-danger">*</span></label>
							<textarea class="form-control" name="description" rows="4" placeholder="Enter order value or notes here" required></textarea>
						</div>
						<div class="text-end">
							<button type="submit" class="btn btn-primary" id="submitTelephonicOrderBtn">Save Order Activity</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Choose Activity Modal -->
	<div id="choose-activity-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="standard-modalLabel">Choose Activity</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="card card-bg-light">
						<div class="card-body" id="dynamic-activities-container">
							<div class="text-center p-4">
								<div class="spinner-border text-primary" role="status">
									<span class="visually-hidden">Loading...</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
@endsection

@section('scripts')

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCix326VJYzGXyMzF7_yTD_vbn5DBMHZL4&libraries=places,drawing,geometry&language=en" async defer></script>

<script>
$(document).ready(function() {
    $('#save_current_location').on('change', function() {
        const statusDiv = $('#location_capture_status');
        const latInput = $('#form_latitude');
        const lonInput = $('#form_longitude');

        if ($(this).is(':checked')) {
            if (navigator.geolocation) {
                statusDiv.html('<span class="text-info"><i class="ti ti-loader"></i> Requesting location...</span>').show();
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        if (position.coords.accuracy > 500) {
                            statusDiv.html('<span class="text-warning"><i class="ti ti-alert-triangle"></i> Location accuracy is too low (' + Math.round(position.coords.accuracy) + 'm). Try moving outdoors or turning on Wi-Fi.</span>').show();
                            latInput.val('');
                            lonInput.val('');
                            $('#save_current_location').prop('checked', false);
                        } else {
                            latInput.val(position.coords.latitude);
                            lonInput.val(position.coords.longitude);
                            statusDiv.html('<span class="text-success"><i class="ti ti-check"></i> Location captured! Accuracy: ' + Math.round(position.coords.accuracy) + 'm</span>').show();
                        }
                    },
                    function(error) {
                        // Fallback to Google Geolocation API
                        var apiKey = 'AIzaSyCix326VJYzGXyMzF7_yTD_vbn5DBMHZL4';
                        $.ajax({
                            url: 'https://www.googleapis.com/geolocation/v1/geolocate?key=' + apiKey,
                            type: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify({ considerIp: true }),
                            success: function(data) {
                                if(data && data.location) {
                                    latInput.val(data.location.lat);
                                    lonInput.val(data.location.lng);
                                    statusDiv.html('<span class="text-success"><i class="ti ti-check"></i> Location dynamically captured via Google API!</span>').show();
                                } else {
                                    statusDiv.html('<span class="text-warning"><i class="ti ti-alert-triangle"></i> Location access denied and fallback failed.</span>').show();
                                    $('#save_current_location').prop('checked', false);
                                    latInput.val('');
                                    lonInput.val('');
                                }
                            },
                            error: function() {
                                let msg = "Location error.";
                                switch(error.code) {
                                    case error.PERMISSION_DENIED: msg = "Location permission denied."; break;
                                    case error.POSITION_UNAVAILABLE: msg = "Location information unavailable."; break;
                                    case error.TIMEOUT: msg = "Location request timed out."; break;
                                }
                                statusDiv.html('<span class="text-danger"><i class="ti ti-x"></i> ' + msg + ' Fallback failed.</span>').show();
                                $('#save_current_location').prop('checked', false);
                                latInput.val('');
                                lonInput.val('');
                            }
                        });
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            } else {
                statusDiv.html('<span class="text-danger"><i class="ti ti-x"></i> Geolocation is not supported by this browser.</span>').show();
                $(this).prop('checked', false);
            }
        } else {
            statusDiv.hide();
            latInput.val('');
            lonInput.val('');
        }
    });
});

let currentCustomerId = null;
function fetchActivitiesForCustomer() {
    if (!currentCustomerId) return;
    const container = $('#customer-activities-container');
    container.html('<div class="text-center p-3"><div class="spinner-border text-primary" role="status"></div></div>');

    $.ajax({
        url: "{{ route('activities.get', ['box_slug' => request()->route('box_slug')]) }}",
        type: 'GET',
        cache: false,
        data: { lead_id: currentCustomerId },
        success: function(res) {
            if (res.success && res.data && res.data.length > 0) {
                renderCustomerActivities(res.data);
            } else {
                container.html('<div class="text-center py-5"><p class="text-muted">No activities found.</p></div>');
            }
        },
        error: function(xhr) {
            container.html('<div class="text-center py-5"><p class="text-danger">Failed to load activities.</p></div>');
        }
    });
}

function renderCustomerActivities(activities) {
    let html = '<div class="activity-timeline">';
    activities.forEach(function(activity) {
        const date = new Date(activity.activity_date || activity.created_at);
        const formattedDate = date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        
        let typeName = 'Activity';
        if (activity.activity_type_data && activity.activity_type_data.activity_type) {
            typeName = activity.activity_type_data.activity_type;
        } else if (activity.activity_type) {
            typeName = activity.activity_type;
        }
        
        const addedBy = activity.added_by_user ? activity.added_by_user.name : 'System';
        
        html += `
            <div class="card mb-3 shadow-sm border-0 border-start border-4 border-primary">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="mb-0 text-primary fw-semibold">${typeName}</h6>
                        <small class="text-muted">${formattedDate}</small>
                    </div>
                    <p class="mb-1 text-dark fs-14">${activity.description || 'No description provided.'}</p>
                    <div class="text-muted fs-12">
                        <i class="ti ti-user me-1"></i> ${addedBy}
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    $('#customer-activities-container').html(html);
}

$(document).ready(function() {

    // Initialize Select2 for Lead Search
    $('.select2-search-leads').select2({
        dropdownParent: $('#offcanvas_add_customer'),
        ajax: {
            url: '{{ route("fieldstracking.customer.searchLeads", ["box_slug" => request()->route("box_slug")]) }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { 
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data) {
                return { 
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        },
        placeholder: 'Search for a lead...'
    });

    // Auto-fill form on Lead selection
    $('.select2-search-leads').on('select2:select', function (e) {
        var data = e.params.data.lead_data;
        if(data) {
            $('#existing_lead_id').val(data.id);
            
            var readOnlyFields = ['company_name', 'gstin_number', 'name', 'designation', 'email', 'sec_email', 'phone', 'mobile', 'address', 'experience', 'postal_code', 'location', 'drug_license', 'pan_number'];
            readOnlyFields.forEach(function(field) {
                var value = data[field] ? data[field] : '';
                var inputEl = $('input[name="'+field+'"]');
                inputEl.val(value);
                if (value) {
                    inputEl.prop('readonly', true).css('background-color', '#e9ecef');
                } else {
                    inputEl.prop('readonly', false).css('background-color', '');
                }
            });
            
            var queryVal = data.query ? data.query : '';
            var queryEl = $('textarea[name="query_notes"]');
            queryEl.val(queryVal);
            if (queryVal) {
                queryEl.prop('readonly', true).css('background-color', '#e9ecef');
            } else {
                queryEl.prop('readonly', false).css('background-color', '');
            }

            var selectFields = [
                {field: 'country_id', relation: 'country', nameField: 'name'},
                {field: 'city_id', relation: 'city', nameField: 'name'},
                {field: 'state_id', relation: 'state', nameField: 'name'},
                {field: 'lead_source_id', relation: 'lead_source', nameField: 'name'}
            ];
            selectFields.forEach(function(item) {
                var value = data[item.field];
                var selectEl = $('select[name="'+item.field+'"]');
                if (value && data[item.relation]) {
                    var text = data[item.relation][item.nameField];
                    if(selectEl.find("option[value='" + value + "']").length === 0) {
                        var newOption = new Option(text, value, true, true);
                        selectEl.append(newOption);
                    }
                    selectEl.val(value).trigger('change');
                    selectEl.prop('disabled', true);
                } else {
                    selectEl.val('').trigger('change');
                    selectEl.prop('disabled', false);
                }
            });
            
            // Handle assignees
            if (data.assignees && data.assignees.length > 0) {
                var selectEl = $('select[name="assignees[]"]');
                var assigneeIds = data.assignees.map(function(a) { 
                    if(selectEl.find("option[value='" + a.id + "']").length === 0) {
                        var newOption = new Option(a.name, a.id, true, true);
                        selectEl.append(newOption);
                    }
                    return a.id; 
                });
                selectEl.val(assigneeIds).trigger('change');
            } else {
                $('select[name="assignees[]"]').val(null).trigger('change');
            }
            $('select[name="assignees[]"]').prop('disabled', true);
        }
    });

    // Reset hidden ID and select2 when form is reset
    $('#addCustomerForm').on('reset', function() {
        $('#existing_lead_id').val('');
        $('.select2-search-leads').val(null).trigger('change');
        
        // Remove readonly/disabled state from all fields
        $('#addCustomerForm input').prop('readonly', false).css('background-color', '');
        $('#addCustomerForm textarea').prop('readonly', false).css('background-color', '');
        $('#addCustomerForm select').prop('disabled', false);
    });
    
    // Column Definitions for Manage Columns feature
    const columnDefinitions = [
        { index: 0, name: 'Customer Name', visible: true },
        { index: 1, name: 'Location Status', visible: true },
        { index: 2, name: 'Check Ins', visible: true },
        { index: 3, name: 'Check In', visible: true },
        { index: 3, name: 'Telephonic Order', visible: true },
        { index: 4, name: 'Contact Person', visible: true },
        { index: 5, name: 'Designation', visible: true },
        { index: 6, name: 'Whatsapp Number', visible: true },
        { index: 7, name: 'Mobile', visible: true },
        { index: 8, name: 'Phone', visible: true },
        { index: 9, name: 'Email', visible: true },
        { index: 10, name: 'Secondary Email', visible: true },
        { index: 11, name: 'GST Number', visible: true },
        { index: 12, name: 'Drug License', visible: true },
        { index: 13, name: 'PAN Number', visible: true },
        { index: 14, name: 'Address', visible: true },
        { index: 15, name: 'Location', visible: true },
        { index: 16, name: 'City', visible: true },
        { index: 17, name: 'State', visible: true },
        { index: 18, name: 'Country', visible: true },
        { index: 19, name: 'Postal Code', visible: true },
        { index: 20, name: 'Territory', visible: true },
        { index: 21, name: 'Experience', visible: true },
        { index: 22, name: 'Lead Source', visible: true },
        { index: 23, name: 'Query Notes', visible: true },
        { index: 24, name: 'Customer Level', visible: true },
        { index: 25, name: 'Parent Name', visible: true },
        { index: 26, name: 'Customer Type', visible: true },
        { index: 27, name: 'Payment Terms', visible: true },
        { index: 28, name: 'Credit Limit', visible: true },
        { index: 29, name: 'Outstanding Amount', visible: true },
        { index: 30, name: 'Assigned Staff', visible: true },
        { index: 31, name: 'Action', visible: true }
    ];

    const pageIdentifier = 'customer-list';
    
    function loadColumnPreferences(callback) {
        $.ajax({
            url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug")]) }}',
            type: 'GET',
            data: { page_identifier: pageIdentifier },
            async: false,
            success: function(response) {
                if (response.success && response.data && Array.isArray(response.data) && response.data.length > 0) {
                    try {
                        const preferences = response.data;
                        preferences.forEach(pref => {
                            const prefIndex = parseInt(pref.index);
                            const col = columnDefinitions.find(c => c.index === prefIndex);
                            if (col) {
                                if (typeof pref.visible === 'string') col.visible = pref.visible === 'true' || pref.visible === '1';
                                else col.visible = pref.visible !== undefined ? Boolean(pref.visible) : true;
                            }
                        });
                        if (callback) callback();
                    } catch (e) {
                        console.error('Error loading column preferences:', e);
                        columnDefinitions.forEach(col => col.visible = true);
                        if (callback) callback();
                    }
                } else {
                    columnDefinitions.forEach(col => col.visible = true);
                    if (callback) callback();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading column preferences:', error);
                columnDefinitions.forEach(col => col.visible = true);
                if (callback) callback();
            }
        });
    }

    function saveColumnPreferences() {
        $.ajax({
            url: '{{ route("settings.column.preferences.save", ["box_slug" => request()->route("box_slug")]) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                page_identifier: pageIdentifier,
                preferences: columnDefinitions
            },
            success: function(response) {
                if (!response.success) console.error('Error saving column preferences:', response.message);
            },
            error: function(xhr, status, error) {
                console.error('Error saving column preferences:', error);
            }
        });
    }

    function applyColumnVisibility() {
        if (table) {
            columnDefinitions.forEach(col => {
                const column = table.column(col.index);
                if (column && typeof column.visible === 'function') {
                    column.visible(col.visible);
                }
            });
        }
    }

    function renderCheckboxes() {
        const listGroup = $('#columnsList');
        listGroup.empty();
        columnDefinitions.forEach(col => {
            const isChecked = col.visible ? 'checked' : '';
            listGroup.append(`
                <label class="list-group-item d-flex align-items-center">
                    <input class="form-check-input me-3 column-checkbox" type="checkbox" value="${col.index}" ${isChecked}>
                    ${col.name}
                </label>
            `);
        });
    }

    $('#manageColumnsModal').on('show.bs.modal', function () {
        renderCheckboxes();
    });

    $('#showAllColumns').on('click', function() {
        $('.column-checkbox').prop('checked', true);
    });

    $('#hideAllColumns').on('click', function() {
        $('.column-checkbox').prop('checked', false);
    });

    $('#applyColumnChanges').on('click', function() {
        $('.column-checkbox').each(function() {
            const index = parseInt($(this).val());
            const col = columnDefinitions.find(c => c.index === index);
            if (col) col.visible = $(this).is(':checked');
        });
        saveColumnPreferences();
        applyColumnVisibility();
        $('#manageColumnsModal').modal('hide');
    });

    // Re-initialize to prevent global script from auto-filling the input
    var start = moment().subtract(6, 'days');
    var end = moment();
    $('.bookingrange').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    // Clear value BEFORE initializing DataTables so the first request sends no dates
    $('.bookingrange').val('');

    var currentSort = '';

    // Initialize DataTable
	$(document).on('click', '#btn-telephonic-order', function() {
		try {
			var m = bootstrap.Modal.getInstance(document.getElementById('telephonic-order-modal')) || new bootstrap.Modal(document.getElementById('telephonic-order-modal'), { backdrop: true, keyboard: true });
			m.show();
		} catch (e) {
			console.error("Modal Error:", e);
			$('#telephonic-order-modal').addClass('show').css({ display: 'block', background: 'rgba(0,0,0,0.5)' });
		}
	});

	$(document).on('click', '#btn-check-in', function() {
		try {
			var m = bootstrap.Modal.getInstance(document.getElementById('check-in-modal')) || new bootstrap.Modal(document.getElementById('check-in-modal'), { backdrop: true, keyboard: true });
			m.show();
		} catch (e) {
			console.error("Modal Error:", e);
			$('#check-in-modal').addClass('show').css({ display: 'block', background: 'rgba(0,0,0,0.5)' });
		}
	});

	$(document).on('click', '#btn-choose-activity', function() {
		try {
			var m = bootstrap.Modal.getInstance(document.getElementById('choose-activity-modal')) || new bootstrap.Modal(document.getElementById('choose-activity-modal'), { backdrop: true, keyboard: true });
			m.show();
		} catch (e) {
			console.error("Modal Error:", e);
			$('#choose-activity-modal').addClass('show').css({ display: 'block', background: 'rgba(0,0,0,0.5)' });
		}
	});

	$('.modal .btn-close, .modal [data-bs-dismiss="modal"]').on('click', function() {
		var $modal = $(this).closest('.modal');
		try {
			var m = bootstrap.Modal.getInstance($modal[0]);
			if (m) m.hide();
			else $modal.removeClass('show').css('display', 'none');
		} catch(e) {
			$modal.removeClass('show').css('display', 'none');
		}
	});

    var table = $('#customerTable').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("fieldstracking.customer.data", ["box_slug" => request()->route("box_slug")]) }}',
            data: function (d) {
                var dateRange = $('.bookingrange').val();
                if (dateRange && dateRange.includes(' - ')) {
                    var dates = dateRange.split(' - ');
                    d.start_date = dates[0];
                    d.end_date = dates[1];
                }
                d.sort = currentSort;
            }
        },
        columns: [
            { data: 'customer_name', name: 'customer_name' },
            { data: 'location_status', name: 'location_status', orderable: false, searchable: false },
            { data: 'check_ins_count', name: 'check_ins_count', orderable: false, searchable: false },
            { data: 'check_in', name: 'check_in', orderable: false, searchable: false },
            { data: 'telephonic_order', name: 'telephonic_order', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'designation', name: 'designation' },
            { data: 'whatsapp_number', name: 'whatsapp_number', orderable: false, searchable: false },
            { data: 'mobile', name: 'mobile' },
            { data: 'phone', name: 'phone' },
            { data: 'email', name: 'email' },
            { data: 'sec_email', name: 'sec_email' },
            { data: 'gstin_number', name: 'gstin_number' },
            { data: 'drug_license', name: 'drug_license' },
            { data: 'pan_number', name: 'pan_number' },
            { data: 'address', name: 'address' },
            { data: 'location', name: 'location' },
            { data: 'city_name', name: 'city_name', orderable: false, searchable: false },
            { data: 'state_name', name: 'state_name', orderable: false, searchable: false },
            { data: 'country_name', name: 'country_name', orderable: false, searchable: false },
            { data: 'postal_code', name: 'postal_code' },
            { data: 'territory', name: 'territory', orderable: false, searchable: false },
            { data: 'experience', name: 'experience' },
            { data: 'lead_source_name', name: 'lead_source_name', orderable: false, searchable: false },
            { data: 'query', name: 'query' },
            { data: 'customer_level', name: 'customer_level', orderable: false, searchable: false },
            { data: 'parent_name', name: 'parent_name', orderable: false, searchable: false },
            { data: 'customer_type', name: 'customer_type', orderable: false, searchable: false },
            { data: 'payment_terms', name: 'payment_terms', orderable: false, searchable: false },
            { data: 'credit_limit', name: 'credit_limit', orderable: false, searchable: false },
            { data: 'outstanding_amount', name: 'outstanding_amount', orderable: false, searchable: false },
            { data: 'assigned_staff', name: 'assigned_staff', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search...",
        },
        initComplete: function() {
            applyColumnVisibility();
        }
    });

    // Refresh Table
    $('#refresh-table').on('click', function(e) {
        e.preventDefault();
        table.ajax.reload(null, false);
    });

    // Sort Dropdown
    $('.sort-item').on('click', function(e) {
        e.preventDefault();
        currentSort = $(this).data('sort');
        table.ajax.reload();
    });

    // Date Range Change
    $('.bookingrange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        table.ajax.reload();
    });
    
    $('.bookingrange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        table.ajax.reload();
    });

    $('#addCustomerForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let submitBtn = $(this).find('button[type="submit"]');
        let originalText = submitBtn.text();
        submitBtn.text('Submitting...').prop('disabled', true);
        $.ajax({
            url: '{{ route("fieldstracking.customer.store", ["box_slug" => request()->route("box_slug")]) }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if(response.success) {
                    toastr.success(response.message);
                    // Reload the DataTable and reset the form!
                    table.ajax.reload(null, false); 
                    $('#addCustomerForm')[0].reset();
                    // Close the offcanvas
                    var offcanvasElement = document.getElementById('offcanvas_add_customer');
                    if (offcanvasElement) {
                        var offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);
                        if (offcanvasInstance) offcanvasInstance.hide();
                    }
                    
                    submitBtn.text(originalText).prop('disabled', false);
                } else {
                    toastr.error(response.message || 'An error occurred while saving.');
                    submitBtn.text(originalText).prop('disabled', false);
                }
            },
            error: function(xhr) {
                submitBtn.text(originalText).prop('disabled', false);
                if(xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMsg = 'Validation Errors:<br>';
                    $.each(errors, function(key, val) {
                        errorMsg += '- ' + val[0] + '<br>';
                    });
                    toastr.error(errorMsg);
                } else {
                    toastr.error('An unexpected error occurred. Please try again.');
                }
            }
        });
    });

    // Telephonic Order Table Button Setup
    $('#customerTable').on('click', '.btn-dt-telephonic-order', function() {
        currentCustomerId = $(this).data('customer-id');
        
        $('#telephonicOrderForm')[0].reset();
        
        try {
            var m = bootstrap.Modal.getInstance(document.getElementById('telephonic-order-modal')) || new bootstrap.Modal(document.getElementById('telephonic-order-modal'), { backdrop: true, keyboard: true });
            m.show();
        } catch (e) {
            $('#telephonic-order-modal').addClass('show').css({ display: 'block', background: 'rgba(0,0,0,0.5)' });
        }
    });

    // Check In Modal Setup
    $('#customerTable').on('click', '.btn-dt-check-in', function() {
        var customerId = $(this).data('customer-id');
        var customerName = $(this).data('customer-name');
        
        $('#checkin_customer_id').val(customerId);
        $('#checkin_customer_name_display').text(customerName);
        $('#checkin_customer_name_text').text(customerName);
        
        // Reset form
        $('#checkInForm')[0].reset();
        $('#checkin_latitude').val('');
        $('#checkin_longitude').val('');
        $('#detectedLocationDisplay').hide().text('');
        
        try {
            var m = bootstrap.Modal.getInstance(document.getElementById('check-in-modal')) || new bootstrap.Modal(document.getElementById('check-in-modal'), { backdrop: true, keyboard: true });
            m.show();
        } catch (e) {
            $('#check-in-modal').addClass('show').css({ display: 'block', background: 'rgba(0,0,0,0.5)' });
        }

        let submitBtn = $('#checkInSubmitBtn');
        submitBtn.prop('disabled', true).text('Fetching Location...');
        
        // Geolocation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;
                    $('#checkin_latitude').val(lat);
                    $('#checkin_longitude').val(lng);
                    
                    // Fetch Address using Google Maps Geocoding API
                    if (typeof google !== 'undefined' && google.maps && google.maps.Geocoder) {
                        var geocoder = new google.maps.Geocoder();
                        var latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };
                        geocoder.geocode({ 'location': latlng }, function(results, status) {
                            if (status === 'OK' && results[0]) {
                                var address = results[0].formatted_address;
                                $('#detectedLocationDisplay').show().html('<i class="fa-solid fa-map-pin me-1"></i> Location: ' + address);
                            } else {
                                $('#detectedLocationDisplay').show().html('<i class="fa-solid fa-map-pin me-1"></i> Location Detected: ' + lat.toFixed(4) + ', ' + lng.toFixed(4));
                            }
                        });
                    } else {
                        $('#detectedLocationDisplay').show().html('<i class="fa-solid fa-map-pin me-1"></i> Location Detected: ' + lat.toFixed(4) + ', ' + lng.toFixed(4));
                    }

                    submitBtn.prop('disabled', false).text('Submit');
                    toastr.success('Location successfully captured!');
                }, 
                function(error) {
                    // Fallback to Google Geolocation API if CoreLocation/HTML5 fails
                    var apiKey = 'AIzaSyCix326VJYzGXyMzF7_yTD_vbn5DBMHZL4';
                    $.ajax({
                        url: 'https://www.googleapis.com/geolocation/v1/geolocate?key=' + apiKey,
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({ considerIp: true }),
                        success: function(data) {
                            if(data && data.location) {
                                var lat = data.location.lat;
                                var lng = data.location.lng;
                                $('#checkin_latitude').val(lat);
                                $('#checkin_longitude').val(lng);
                                
                                if (typeof google !== 'undefined' && google.maps && google.maps.Geocoder) {
                                    var geocoder = new google.maps.Geocoder();
                                    var latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };
                                    geocoder.geocode({ 'location': latlng }, function(results, status) {
                                        if (status === 'OK' && results[0]) {
                                            $('#detectedLocationDisplay').show().html('<i class="fa-solid fa-map-pin me-1"></i> Location: ' + results[0].formatted_address);
                                        } else {
                                            $('#detectedLocationDisplay').show().html('<i class="fa-solid fa-map-pin me-1"></i> Location: ' + lat.toFixed(4) + ', ' + lng.toFixed(4));
                                        }
                                    });
                                } else {
                                    $('#detectedLocationDisplay').show().html('<i class="fa-solid fa-map-pin me-1"></i> Location: ' + lat.toFixed(4) + ', ' + lng.toFixed(4));
                                }
                                
                                submitBtn.prop('disabled', false).text('Submit');
                                toastr.success('Location dynamically captured via Google API!');
                            } else {
                                toastr.warning('Location access denied and fallback failed.');
                                submitBtn.text('Location Required');
                            }
                        },
                        error: function() {
                            let errMsg = 'Location access denied.';
                            if (error.code === error.TIMEOUT) errMsg = 'Location request timed out.';
                            if (error.code === error.POSITION_UNAVAILABLE) errMsg = 'Location info unavailable.';
                            toastr.warning(errMsg + ' Please use a mobile device for accurate GPS location.');
                            submitBtn.text('Location Required');
                        }
                    });
                }
            );
        } else {
            toastr.error('Geolocation is not supported by this browser.');
            submitBtn.text('Location Unsupported');
        }
    });

    // Check In Form Submission
    $('#checkInForm').on('submit', function(e) {
        e.preventDefault();
        
        if(!$('#checkin_latitude').val() || !$('#checkin_longitude').val()) {
            toastr.error('Location is required. Please allow location access and try again.');
            return;
        }

        let formData = new FormData(this);
        let submitBtn = $('#checkInSubmitBtn');
        let originalText = submitBtn.text();
        submitBtn.text('Submitting...').prop('disabled', true);
        
        $.ajax({
            url: '{{ route("fieldstracking.customer.checkin.store", ["box_slug" => request()->route("box_slug")]) }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if(response.success) {
                    toastr.success(response.message);
                    $('#check-in-modal').find('.btn-close').click();
                    $('#checkInForm')[0].reset();
                } else {
                    toastr.error(response.message || 'An error occurred.');
                }
                submitBtn.text(originalText).prop('disabled', false);
            },
            error: function(xhr) {
                submitBtn.text(originalText).prop('disabled', false);
                let errorMsg = 'An unexpected error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        let res = JSON.parse(xhr.responseText);
                        if (res.message) errorMsg = res.message;
                    } catch (e) {}
                }
                toastr.error(errorMsg);
            }
        });
    });
    // Handle View Check-Ins History Click
    $('#customerTable').on('click', '.view-checkins-btn', function() {
        let customerId = $(this).data('customer-id');
        let customerName = $(this).data('customer-name');
        
        $('#historyCustomerName').text(customerName);
        $('#checkInHistoryLoading').removeClass('d-none');
        $('#checkInHistoryContent').addClass('d-none');
        $('#checkInHistoryTable tbody').empty();
        $('#check-in-history-modal').modal('show');

        // Fetch History
        $.ajax({
            url: '{{ route("fieldstracking.customer.checkin.history", ["box_slug" => request()->route("box_slug"), "customer_id" => "CUSTOMER_ID_PLACEHOLDER"]) }}'.replace('CUSTOMER_ID_PLACEHOLDER', customerId),
            type: 'GET',
            success: function(response) {
                $('#checkInHistoryLoading').addClass('d-none');
                $('#checkInHistoryContent').removeClass('d-none');
                
                if(response.success && response.data.length > 0) {
                    let rows = '';
                    response.data.forEach(function(checkIn) {
                        let dateObj = new Date(checkIn.created_at);
                        let formattedDate = dateObj.toLocaleString();
                        let creator = checkIn.creator ? checkIn.creator.name : 'Unknown';
                        
                        let imagesHtml = '<div class="d-flex align-items-center flex-wrap gap-1">';
                        if (checkIn.images && checkIn.images.length > 0) {
                            let firstImg = checkIn.images[0];
                            imagesHtml += `<a href="/storage/${firstImg}" target="_blank"><img src="/storage/${firstImg}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" alt="Check In Image"></a>`;
                            
                            if (checkIn.images.length > 1) {
                                imagesHtml += `<div class="more-images-container-${checkIn.id} d-none d-flex flex-wrap gap-1 ms-1">`;
                                for (let i = 1; i < checkIn.images.length; i++) {
                                    let img = checkIn.images[i];
                                    imagesHtml += `<a href="/storage/${img}" target="_blank"><img src="/storage/${img}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" alt="Check In Image"></a>`;
                                }
                                imagesHtml += `</div>`;
                                imagesHtml += `<button type="button" class="btn btn-sm btn-light border ms-1 show-more-images-btn" data-target=".more-images-container-${checkIn.id}">+${checkIn.images.length - 1}</button>`;
                            }
                        } else {
                            imagesHtml += '<span class="text-muted">No images</span>';
                        }
                        imagesHtml += '</div>';
                        
                        rows += `
                            <tr>
                                <td>${formattedDate}</td>
                                <td>${creator}</td>
                                <td>
                                    <a href="https://www.google.com/maps/search/?api=1&query=${checkIn.latitude},${checkIn.longitude}" target="_blank"><i class="fa-solid fa-map-location-dot me-1"></i> Map</a><br>
                                    <small class="text-muted check-in-address" data-lat="${checkIn.latitude}" data-lng="${checkIn.longitude}">
                                        ${parseFloat(checkIn.latitude).toFixed(4)}, ${parseFloat(checkIn.longitude).toFixed(4)} <br>(Fetching address...)
                                    </small>
                                </td>
                                <td>${checkIn.remarks || '-'}</td>
                                <td>${imagesHtml}</td>
                            </tr>
                        `;
                    });
                    $('#checkInHistoryTable tbody').html(rows);

                    // Reverse geocode addresses
                    if (typeof google !== 'undefined' && google.maps && google.maps.Geocoder) {
                        let geocoder = new google.maps.Geocoder();
                        $('.check-in-address').each(function() {
                            let el = $(this);
                            let lat = parseFloat(el.data('lat'));
                            let lng = parseFloat(el.data('lng'));
                            
                            geocoder.geocode({ location: { lat: lat, lng: lng } }, function(results, status) {
                                if (status === 'OK') {
                                    if (results[0]) {
                                        el.html(results[0].formatted_address);
                                    } else {
                                        el.html(`${lat.toFixed(4)}, ${lng.toFixed(4)}`);
                                    }
                                } else {
                                    el.html(`${lat.toFixed(4)}, ${lng.toFixed(4)}`);
                                }
                            });
                        });
                    }
                } else {
                    $('#checkInHistoryTable tbody').html('<tr><td colspan="5" class="text-center py-4 text-muted">No check-in history found.</td></tr>');
                }
            },
            error: function() {
                $('#checkInHistoryLoading').addClass('d-none');
                $('#checkInHistoryContent').removeClass('d-none');
                $('#checkInHistoryTable tbody').html('<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load history. Please try again.</td></tr>');
                toastr.error('Failed to load check-in history.');
            }
        });
    });

    // Validate max image selection
    $('#checkInImages').on('change', function() {
        if (this.files && this.files.length > 10) {
            toastr.error('You can only upload a maximum of 10 images.');
            $(this).val(''); // Clear the input
        }
    });

    // Handle show more images click
    $('#checkInHistoryTable').on('click', '.show-more-images-btn', function() {
        let target = $(this).data('target');
        $(target).removeClass('d-none');
        $(this).remove();
    });

    // Handle Edit Customer Click
    $('#customerTable').on('click', '.edit-customer-btn', function(e) {
        e.preventDefault();
        let customerId = $(this).data('id');
        let editUrl = '{{ route("fieldstracking.customer.edit", ["box_slug" => request()->route("box_slug"), "id" => "CUSTOMER_ID"]) }}'.replace('CUSTOMER_ID', customerId);

        // Change modal title and hide convert lead section
        $('#offcanvas_add_customer .fw-semibold').first().text('Edit Customer');
        $('#convert_lead_section').hide();
        $('#addCustomerForm')[0].reset();
        
        $.ajax({
            url: editUrl,
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    let data = response.data;
                    $('#edit_customer_id').val(data.id);
                    $('#existing_lead_id').val('');
                    
                    $('input[name="company_name"]').val(data.company_name);
                    $('input[name="gstin_number"]').val(data.gstin_number);
                    $('input[name="drug_license"]').val(data.drug_license);
                    $('input[name="pan_number"]').val(data.pan_number);
                    $('input[name="name"]').val(data.name);
                    $('input[name="designation"]').val(data.designation);
                    $('input[name="email"]').val(data.email);
                    $('input[name="sec_email"]').val(data.sec_email);
                    $('input[name="phone"]').val(data.phone);
                    $('input[name="mobile"]').val(data.mobile);
                    $('input[name="address"]').val(data.address);
                    $('input[name="experience"]').val(data.experience);
                    $('input[name="postal_code"]').val(data.postal_code);
                    var selectFields = [
                        {field: 'country_id', relation: 'country', nameField: 'name'},
                        {field: 'city_id', relation: 'city', nameField: 'name'},
                        {field: 'state_id', relation: 'state', nameField: 'name'},
                        {field: 'lead_source_id', relation: 'lead_source', nameField: 'name'}
                    ];
                    
                    selectFields.forEach(function(item) {
                        var value = data[item.field];
                        var selectEl = $('select[name="'+item.field+'"]');
                        if (value && data[item.relation]) {
                            var text = data[item.relation][item.nameField];
                            if(selectEl.find("option[value='" + value + "']").length === 0) {
                                var newOption = new Option(text, value, true, true);
                                selectEl.append(newOption);
                            }
                            selectEl.val(value).trigger('change');
                        } else {
                            selectEl.val('').trigger('change');
                        }
                    });

                    $('input[name="location"]').val(data.location);
                    $('textarea[name="query_notes"]').val(data.query);

                    if (data.customer_detail) {
                        $('input[name="customer_level"]').val(data.customer_detail.customer_level);
                        $('input[name="parent_name"]').val(data.customer_detail.parent_name);
                        $('input[name="whatsapp_number"]').val(data.customer_detail.whatsapp_number);
                        $('select[name="customer_type"]').val(data.customer_detail.customer_type).trigger('change');
                        $('input[name="territory"]').val(data.customer_detail.territory);
                        $('input[name="payment_terms"]').val(data.customer_detail.payment_terms);
                        $('input[name="credit_limit"]').val(data.customer_detail.credit_limit);
                        $('input[name="outstanding_amount"]').val(data.customer_detail.outstanding_amount);
                        
                        if (data.customer_detail.company_image) {
                            $('#current_company_image_preview img').attr('src', '{{ asset("storage") }}/' + data.customer_detail.company_image);
                            $('#current_company_image_preview').show();
                        } else {
                            $('#current_company_image_preview').hide();
                        }
                    } else {
                        $('#current_company_image_preview').hide();
                    }

                    if (data.assignees && data.assignees.length > 0) {
                        var selectEl = $('select[name="assignees[]"]');
                        var assigneeIds = data.assignees.map(function(a) { 
                            if(selectEl.find("option[value='" + a.id + "']").length === 0) {
                                var newOption = new Option(a.name, a.id, true, true);
                                selectEl.append(newOption);
                            }
                            return a.id; 
                        });
                        selectEl.val(assigneeIds).trigger('change');
                    } else {
                        $('select[name="assignees[]"]').val(null).trigger('change');
                    }
                    
                    if (data.customer_detail && data.customer_detail.latitude && data.customer_detail.longitude) {
                        $('#save_current_location').prop('checked', true).prop('disabled', true);
                        $('#location_capture_status').html('<span class="text-success"><i class="ti ti-check"></i> Location is already saved.</span>').show();
                        $('#form_latitude').val(data.customer_detail.latitude);
                        $('#form_longitude').val(data.customer_detail.longitude);
                    } else {
                        $('#save_current_location').prop('checked', false).prop('disabled', false);
                        $('#location_capture_status').hide();
                        $('#form_latitude').val('');
                        $('#form_longitude').val('');
                    }
                } else {
                    toastr.error('Failed to fetch customer data.');
                }
            },
            error: function() {
                toastr.error('Error fetching customer data.');
            }
        });
    });

    // Reset Add Customer form when clicking "Add Customer" button
    $('[data-bs-target="#offcanvas_add_customer"]').not('.edit-customer-btn').on('click', function() {
        $('#offcanvas_add_customer .fw-semibold').first().text('Add New Customer');
        $('#convert_lead_section').show();
        $('#addCustomerForm')[0].reset();
        $('#edit_customer_id').val('');
        $('#existing_lead_id').val('');
        $('#current_company_image_preview').hide();
        $('select').val(null).trigger('change');
        $('#save_current_location').prop('checked', false).prop('disabled', false);
        $('#location_capture_status').hide();
        $('#form_latitude').val('');
        $('#form_longitude').val('');
    });

    // View Customer Data
    $('#customerTable').on('click', '.view-customer-btn', function(e) {
        e.preventDefault();
        var customerId = $(this).data('id');
        currentCustomerId = customerId;
        
        // Fetch activities automatically when viewing customer
        fetchActivitiesForCustomer();
        var url = '{{ route("fieldstracking.customer.edit", ["box_slug" => request()->route("box_slug"), "id" => ":id"]) }}';
        url = url.replace(':id', customerId);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    
                    // Populate basic info
                    $('#view_customer_company_name').text(data.company_name || 'N/A');
                    $('#view_contact_person').text(data.name || 'N/A');
                    
                    // Populate location (City, State)
                    var locationStr = [];
                    if (data.city && data.city.name) locationStr.push(data.city.name);
                    if (data.state && data.state.name) locationStr.push(data.state.name);
                    $('#view_customer_location').text(locationStr.length > 0 ? locationStr.join(', ') : 'N/A');

                    // Populate Level and Image
                    if (data.customer_detail) {
                        $('#view_customer_level').text(data.customer_detail.customer_level || 'N/A');
                        
                        if (data.customer_detail.company_image) {
                            $('#view_customer_image').attr('src', '{{ asset("storage") }}/' + data.customer_detail.company_image);
                        } else {
                            $('#view_customer_image').attr('src', 'assets/img/profiles/avatar-04.jpg'); // Default
                        }
                    } else {
                        $('#view_customer_level').text('N/A');
                        $('#view_customer_image').attr('src', 'assets/img/profiles/avatar-04.jpg');
                    }

                    // Populate Insights
                    if (response.insights) {
                        $('#view_insight_tc').text(response.insights.tc);
                        $('#view_insight_pc').text(response.insights.pc);
                        $('#view_insight_output').text(response.insights.output_percentage + '%');
                        $('#view_insight_last_visit').text(response.insights.last_visit_date);
                        $('#view_insight_last_order_date').text(response.insights.last_order_date);
                        $('#view_insight_last_order_value').text('₹ ' + response.insights.last_order_value);
                        $('#view_insight_avg_order_value').text('₹ ' + response.insights.avg_order_value);
                    }
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });
    });

    // Handle Delete Customer Click
    $('#customerTable').on('click', '.delete-customer-btn', function(e) {
        e.preventDefault();
        let customerId = $(this).data('id');
        let deleteUrl = '{{ route("fieldstracking.customer.destroy", ["box_slug" => request()->route("box_slug"), "id" => "CUSTOMER_ID"]) }}'.replace('CUSTOMER_ID', customerId);

        if(confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if(response.success) {
                        toastr.success(response.message);
                        table.ajax.reload(null, false);
                    } else {
                        toastr.error(response.message || 'Failed to delete customer.');
                    }
                },
                error: function() {
                    toastr.error('Error deleting customer.');
                }
            });
        }
    });

	// Fetch dynamic activities when modal opens
	$('#choose-activity-modal').on('show.bs.modal', function () {
		const container = $('#dynamic-activities-container');
		container.html(`
			<div class="text-center p-4">
				<div class="spinner-border text-primary" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
			</div>
		`);

		$.get("{{ route('fieldstracking.common.activities', ['box_slug' => request()->route('box_slug')]) }}?target_type=customers&setting_name=Call")
			.done(function(res) {
				if (res.success && res.data.length > 0) {
					let html = '';
					let rowOpen = false;
					res.data.forEach((activity, index) => {
						if (index % 2 === 0) {
							html += '<div class="row gx-2 mt-3">';
							rowOpen = true;
						}
						// Hardcode payment-modal for New Payments, else close modal for now
						// Hardcode payment-modal for New Payments, else open add-activity-modal
						let targetModal = activity.activity_type.toLowerCase().includes('payment') ? '#payment-modal' : '#add-activity-modal';
						
						html += `
							<div class="col-6">
								<button type="button" data-target-modal="${targetModal}" class="btn btn-square btn-outline-dark w-100 activity-select-btn" data-activity-id="${activity.id}" data-setting-id="${activity.activity_setting_id}" data-activity-name="${activity.activity_type}">${activity.activity_type}</button>
							</div>
						`;
						if (index % 2 !== 0) {
							html += '</div>';
							rowOpen = false;
						}
					});
					if (rowOpen) {
						html += '</div>';
					}

                    // Add "Where Is" Tracker button at the bottom
                    html += `
                        <hr class="my-3">
                        <div class="row gx-2">
                            <div class="col-12">
                                <button type="button" class="btn btn-warning w-100" id="where-is-btn">
                                    <i class="fa fa-map-marker me-2"></i> Where Is (Track Location)
                                </button>
                            </div>
                        </div>
                    `;
                    
					container.html(html);
				} else {
					container.html('<div class="text-center p-3 text-muted">No activities found.</div>');
				}
			})
			.fail(function() {
				container.html('<div class="text-center p-3 text-danger">Failed to load activities.</div>');
			});
	});


	// Handle clicking a specific activity button (except payment)
	$(document).on('click', '.activity-select-btn', function() {
		const activityId = $(this).data('activity-id');
		const settingId = $(this).data('setting-id');
		const activityName = $(this).data('activity-name');
		const targetModalId = $(this).data('target-modal');

		// Populate Add Activity Modal
		$('#add-activity-type-name').text(activityName);
		$('#activity_type_id').val(activityId);
		$('#activity_setting_id').val(settingId);
		$('#activity_customer_id').val(currentCustomerId);

		// Hide Choose Activity Modal if it's open
		$('#choose-activity-modal').find('.btn-close').click();

		// Manually open the target modal to prevent BS5 backdrop crash
		if (targetModalId && $(targetModalId).length) {
			try {
				var targetElement = $(targetModalId)[0];
				var m = bootstrap.Modal.getInstance(targetElement) || new bootstrap.Modal(targetElement, { backdrop: true, keyboard: true });
				m.show();
			} catch (e) {
				console.error("Modal Error:", e);
				$(targetModalId).addClass('show').css({ display: 'block', background: 'rgba(0,0,0,0.5)' });
			}
		}
	});

	// Handle Add Activity Form Submission
	$('#customerAddActivityForm').on('submit', function(e) {
		e.preventDefault();

		if (!currentCustomerId) {
			showToast('Customer ID not found.', 'danger');
			return;
		}

		const formData = new FormData(this);
		formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

		const $submitBtn = $('#customerSubmitActivityBtn');
		$submitBtn.prop('disabled', true).text('Saving...');

		$.ajax({
			url: "{{ route('activities.store', ['box_slug' => request()->route('box_slug')]) }}",
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(res) {
				if (res.success || res.status === 200) {
					showToast('Activity recorded successfully!', 'success');
					$('#add-activity-modal').find('.btn-close').click();
					$('#customerAddActivityForm')[0].reset();
					// Optional: Reload checkins/timeline if there is a container for it on the customer page
				} else {
					showToast(res.message || 'Failed to save activity', 'danger');
				}
			},
			error: function(xhr) {
				let msg = 'Failed to save activity.';
				if (xhr.responseJSON && xhr.responseJSON.message) {
					msg = xhr.responseJSON.message;
				}
				showToast(msg, 'danger');
			},
			complete: function() {
				$submitBtn.prop('disabled', false).text('Save Activity');
			}
		});
	});

	// Handle Telephonic Order Submission
	$("#telephonicOrderForm").on("submit", function(e) {
		e.preventDefault();
		if (!currentCustomerId) {
			showToast("Customer ID not found.", "danger");
			return;
		}
		const formData = new FormData(this);
		formData.append("_token", $("meta[name=\"csrf-token\"]").attr("content"));
		formData.append("lead_id", currentCustomerId);
		const now = new Date();
		const offset = now.getTimezoneOffset() * 60000;
		const localISOTime = (new Date(now - offset)).toISOString().slice(0, 16);
		formData.append("activity_date", localISOTime);
		const $submitBtn = $("#submitTelephonicOrderBtn");
		$submitBtn.prop("disabled", true).text("Saving...");
		$.ajax({
			url: "{{ route('activities.store', ['box_slug' => request()->route('box_slug')]) }}",
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			success: function(res) {
				if (res.success || res.status === 200) {
					showToast("Telephonic Order recorded successfully!", "success");
					$("#telephonic-order-modal").find(".btn-close").click();
					$("#telephonicOrderForm")[0].reset();
					fetchActivitiesForCustomer();
				} else {
					showToast(res.message || "Failed to save order", "danger");
				}
			},
			error: function(xhr) {
				let msg = "Failed to save order.";
				if (xhr.responseJSON && xhr.responseJSON.message) {
					msg = xhr.responseJSON.message;
				}
				showToast(msg, "danger");
			},
			complete: function() {
				$submitBtn.prop("disabled", false).text("Save Order Activity");
			}
		});
	});

	// Handle "Where Is" tracking button click
	$(document).on('click', '#where-is-btn', function() {
		if (!currentCustomerId) {
			showToast('Customer ID not found.', 'danger');
			return;
		}

		const $btn = $(this);
		$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Locating...');

		if ("geolocation" in navigator) {
			navigator.geolocation.getCurrentPosition(
				async function(position) {
					const lat = position.coords.latitude;
					const lng = position.coords.longitude;
					const time = new Date().toLocaleString();
					let batteryInfo = "Unknown";
					let networkInfo = "Unknown";

					// Try to get Battery
					if ('getBattery' in navigator) {
						try {
							const battery = await navigator.getBattery();
							batteryInfo = Math.round(battery.level * 100) + '%';
						} catch(e) {}
					}

					// Try to get Network
					if ('connection' in navigator) {
						networkInfo = navigator.connection.effectiveType || "Unknown";
					}

					const description = `Location tracked successfully.\nLat: ${lat}\nLng: ${lng}\nTime: ${time}\nBattery: ${batteryInfo}\nNetwork: ${networkInfo}`;

					const formData = new FormData();
					formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
					formData.append('lead_id', currentCustomerId);
					formData.append('type', 'general');
					formData.append('activity_type', 'Where Is');
					formData.append('description', description);
					
					const now = new Date();
					const offset = now.getTimezoneOffset() * 60000;
					const localISOTime = (new Date(now - offset)).toISOString().slice(0, 16);
					formData.append('activity_date', localISOTime);

					$.ajax({
						url: "{{ route('activities.store', ['box_slug' => request()->route('box_slug')]) }}",
						type: 'POST',
						data: formData,
						processData: false,
						contentType: false,
						success: function(res) {
							if (res.success || res.status === 200) {
								showToast('Location tracked successfully!', 'success');
								$('#choose-activity-modal').find('.btn-close').click();
								fetchActivitiesForCustomer();
								
								// Also push to location tracking API for the dashboard
								const boxSlug = '{{ request()->route("box_slug") }}';
								$.ajax({
									url: "{{ route('fieldstracking.location.store', ['box_slug' => request()->route('box_slug')]) }}",
									type: 'POST',
									data: {
										latitude: lat,
										longitude: lng,
										accuracy: position.coords.accuracy || 10,
										timestamp: new Date().toISOString(),
										action: 'ping',
										battery_level: parseInt(batteryInfo) || null,
										network_status: 'online',
										network_type: networkInfo !== "Unknown" ? networkInfo : null,
										_token: $('meta[name="csrf-token"]').attr('content')
									}
								});

							} else {
								showToast(res.message || 'Failed to save location', 'danger');
							}
						},
						error: function() {
							showToast('Failed to save location activity.', 'danger');
						},
						complete: function() {
							$btn.prop('disabled', false).html('<i class="fa fa-map-marker me-2"></i> Where Is (Track Location)');
						}
					});
				},
				function(error) {
					$btn.prop('disabled', false).html('<i class="fa fa-map-marker me-2"></i> Where Is (Track Location)');
					showToast('Location access denied or unavailable. Please enable GPS.', 'danger');
				}
			);
		} else {
			$btn.prop('disabled', false).html('<i class="fa fa-map-marker me-2"></i> Where Is (Track Location)');
			showToast('Geolocation is not supported by your browser.', 'danger');
		}
	});

});
</script>
@endsection

