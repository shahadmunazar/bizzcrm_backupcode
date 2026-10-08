@extends('layouts.main')
@section('title', 'Team Activity Details')
@section('content')
<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									                <div class="d-flex align-items-center">
														<div class="avatar avatar-sm me-2 avatar-rounded">
															<img src="assets/img/profiles/avatar-10.jpg" alt="img">
														</div>
														<div>
															<div class="lh-1">
																<h4 class="page-title">Team Activity Details</h4>
																<span class="count-title">TC - 16 May 2025</span>
															</div>
														</div>
													</div>
								</div>
								<div class="col-4 text-end">
									<div class="head-icons">
										<a href="companies.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
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
											<input type="text" class="form-control" placeholder="Search For Customer">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
											
											
											<div class="me-2">
											<input type="date" id="bs-rangepicker-single" class="form-control">
											</div>

											
										</div>
									</div>
								</div>
								<!-- /Search -->
							</div>
							
								</div>
								<!-- /Filter -->

								<!-- Contact List -->
								<div class="table-responsive custom-table">
									<table class="table datatable">
										<thead class="thead-light">
											<tr>
												<th>Customer Name</th>
												<th>Check In</th>
												<th>Check Out</th>
												<th>Duration</th>
												<th>Activity</th>
												<th>Location</th>
												<th>Comments</th>
												<th class="text-center">Action</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>
													<div class="d-flex align-items-center">
														<div class="avatar avatar-sm me-2 avatar-rounded">
															<img src="assets/img/profiles/avatar-10.jpg" alt="img">
														</div>
														<div>
															<div class="lh-1">
																<a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_cus_detail"><span>Customer 1</span> <span class="badge bg-soft-info">Tertiary Customer</span></a>
															</div>
														</div>
													</div>
												</td>
												<td>05:22 PM</td>
												<td>05:30 PM</td>
												<td>8 Mins</td>
												<td><strong>Create Customer</strong></td>
												<td>---</td>
												<td>---</td>
												<td>
													<div class="dropdown table-action">
														<a href="#" class="action-icon " data-bs-toggle="dropdown" aria-expanded="false">
															<i class="fa fa-ellipsis-v"></i>
														</a>
														<div class="dropdown-menu dropdown-menu-right">
															<a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_cus_detail">
																<i class="ti ti-edit text-blue"></i>View
															</a>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<div class="d-flex align-items-center">
														<div class="avatar avatar-sm me-2 avatar-rounded">
															<img src="assets/img/profiles/avatar-10.jpg" alt="img">
														</div>
														<div>
															<div class="lh-1">
																<span>Customer 2</span> <span class="badge bg-soft-warning">Primary Customer</span>
															</div>
														</div>
													</div>
												</td>
												<td>05:22 PM</td>
												<td>05:30 PM</td>
												<td>8 Mins</td>
												<td><strong>Order Follow Up</strong></td>
												<td>---</td>
												<td>---</td>
												<td>
													<div class="dropdown table-action">
														<a href="#" class="action-icon " data-bs-toggle="dropdown" aria-expanded="false">
															<i class="fa fa-ellipsis-v"></i>
														</a>
														<div class="dropdown-menu dropdown-menu-right">
															<a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_cus_detail">
																<i class="ti ti-edit text-blue"></i>View
															</a>
														</div>
													</div>
												</td>
											</tr>
											
											<tr>
												<td>
													<div class="d-flex align-items-center">
														<div class="avatar avatar-sm me-2 avatar-rounded">
															<img src="assets/img/profiles/avatar-10.jpg" alt="img">
														</div>
														<div>
															<div class="lh-1">
																<span>Customer 3</span> <span class="badge bg-soft-success">Secondary Customer</span>
															</div>
														</div>
													</div>
												</td>
												<td>05:22 PM</td>
												<td>05:30 PM</td>
												<td>8 Mins</td>
												<td><strong>Order Follow Up</strong></td>
												<td>---</td>
												<td>---</td>
												<td>
													<div class="dropdown table-action">
														<a href="#" class="action-icon " data-bs-toggle="dropdown" aria-expanded="false">
															<i class="fa fa-ellipsis-v"></i>
														</a>
														<div class="dropdown-menu dropdown-menu-right">
															<a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_cus_detail">
																<i class="ti ti-edit text-blue"></i>View
															</a>
														</div>
													</div>
												</td>
											</tr>
											
											
											
											
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
											<img src="assets/img/profiles/avatar-04.jpg" alt="img">
										</span>
									</div>
									<div class="">
										<div class="fs-15 fw-semibold">Customer 1</div>
										<span class="badge bg-soft-info">Tertiary Customer</span>
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
											 <p class="text-dark">Chandigarh </p>
										 </div>
										 <div class="d-flex align-items-center justify-content-between mb-2">
											 <span class="d-inline-flex align-items-center">
												 <i class="ti ti-star me-2"></i>
												Contact Person
											 </span>
											 <p class="text-dark">Ayaj Thakur </p>
										 </div>
										 

										 
										 <div class="row gx-2 mt-3">
											 <div class="col-6">
												 <div>
													 <a href="#" class="btn btn-dark w-100" data-bs-toggle="modal" data-bs-target="#edit_employee">Telephonic Order </a>
												 </div>
											 </div>
											 <div class="col-6">
												 <div>
													 <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#check-in-modal">Check In</button>
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
									<span class="avatar avatar-md rounded bg-dark mb-3">
										<i class="ti ti-building fs-16"></i>
									</span>
								</div>
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<h2 class="mb-1">0</h2>
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
									<span class="avatar avatar-md rounded bg-dark mb-3">
										<i class="ti ti-building fs-16"></i>
									</span>
								</div>
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<h2 class="mb-1">0</h2>
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
									<span class="avatar avatar-md rounded bg-dark mb-3">
										<i class="ti ti-building fs-16"></i>
									</span>
								</div>
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<h2 class="mb-1">0%</h2>
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
											 <p class="text-dark">14 May 2025 </p>
										 </div>
										 <div class="d-flex align-items-center justify-content-between mb-2">
											 <span class="d-inline-flex align-items-center">
												 <i class="fa fa-calendar me-2"></i>
												Last Order Date
											 </span>
											 <p class="text-dark">14 May 2025 </p>
										 </div>
										 <div class="d-flex align-items-center justify-content-between mb-2">
											 <span class="d-inline-flex align-items-center">
												 <i class="fa me-2">₹</i>
												Last Order Value
											 </span>
											 <p class="text-dark">₹ 0 </p>
										 </div>
										 <div class="d-flex align-items-center justify-content-between mb-2">
											 <span class="d-inline-flex align-items-center">
												 <i class="fa me-2">₹</i>
												Avg. Order Value Historic
											 </span>
											 <p class="text-dark">₹ 0 </p>
										 </div>

										
									 </div>
								</div>
									
									

									</div>
									<div class="tab-pane fade" id="sales-tab-pane" role="tabpanel" aria-labelledby="sales-tab" tabindex="0">
										
										Orders
										
									</div>
									<div class="tab-pane fade" id="profit-tab-pane" role="tabpanel" aria-labelledby="profit-tab" tabindex="0">
										
										<div class="card card-bg-light border border-dark">
											<div class="card-body">
											    <h5 class="mb-3">Create Customer</h5>
												<div class="d-flex align-items-center w-100">
													<div class="me-2">
														<span class="avatar avatar-rounded">
															<img src="assets/img/profiles/avatar-05.jpg" alt="img">
														</span>
													</div>
													<div class="">
														<div class="fs-15 fw-semibold">Shitender Kumar</div>
														<p class="mb-0 text-muted op-7 fs-12">16 May, 05:39 pm</p>
													</div>
													<div class="ms-auto">
														<a href="javascript:void(0);" class="text-default"><i class="fa-solid fa-ellipsis-vertical"></i></a>
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
		</div>
		
		
		
		
								<!-- Check In Modal -->
		                        <div id="check-in-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<h4 class="modal-title" id="standard-modalLabel">Check In</h4>
												<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
											</div>
											<div class="modal-body">
												<div class="card card-bg-light border border-dark">
													<div class="card-body">
														<div class="d-flex align-items-center w-100">
															<div class="me-2">
																<span class="avatar avatar-rounded">
																	<i class="fa-solid fa-location-dot" style="color: #222;font-size: 46px;"></i>
																</span>
															</div>
															<div class="">
																<div class="fs-15 fw-semibold">Customer 1</div>
																<p class="mb-0 text op-7 fs-12">Do you want to check in at: Customer 1</p>
																<p class="mb-0 text-dark op-7 fs-12"><strong><span style="color:red;">*</span> you wantt be able to check out from a customer if no activity is performed</strong></p>
															</div>
														</div>
													</div>
												</div>
												
												<div class="card card-bg-light border border-dark mb-0">
													<div class="card-body">
													    <h5 class="mb-3">Take a Photo of the store before Checking In</h5>
														
														
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
											<div class="modal-footer">
												<button type="button" class="btn btn-primary">Submit</button>
											</div>
										</div>
									</div>
								</div>
		
		
					
				
		
		
		
		

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
				<form action="company.html">
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
										<select class="select" multiple>
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

	</div>
	
@endsection
