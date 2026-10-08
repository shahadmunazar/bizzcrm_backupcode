@extends('layouts.main')
@section('title', 'Payment Admin')
@section('content')
<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									<h4 class="page-title">Payment Admin</h4>
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
								<div class="col-sm-9">
										<div class="icon-form mb-3 mb-sm-0">
											<span class="form-icon"><i class="ti ti-search"></i></span>
											<input type="text" class="form-control" placeholder="Search for Customer">
										</div>
									</div>
									<div class="col-sm-3">
										<div class="icon-form mb-3 mb-sm-0">
											<select class="select">
												<option selected>All</option>
										    </select>
										</div>
									</div>
									<!-- <div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
											<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_expenses"><i class="ti ti-square-rounded-plus me-2"></i> Add Expense</a>
										</div>
									</div> -->
								</div>
								<!-- /Search -->
							</div>
							<div class="card-body">
								<!-- Filter -->
								<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
									<div class="d-flex align-items-center flex-wrap row-gap-2">
									
											

									</div>
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										
										
									</div>
								</div>
								<!-- /Filter -->

								<!-- Contact List -->
								<div class="table-responsive custom-table">
									<table class="table datatable">
										<thead class="thead-light">
											<tr>
												<th>Customer Name</th>
												<th>Total Amount</th>
												<th>Entered By</th>
												<th>Transaction Date</th>
												<th>Status</th>
												<th class="text-end">Action</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>
													<div class="d-flex align-items-center">
														<a href="javascript:void(0);" class="d-flex flex-column fw-medium">Customer 1</a>
													</div>
												</td>
												<td>15,000</td>
												<td>Shefali Sharma</td>
												<td>16-May-2025</td>
												<td>
												    <select class="select"> 
													     <option selected disabled>Action</option>
													     <option>Approve</option>
													     <option>Reject</option>
													</select>
												</td>
												<td>
													<div class="dropdown table-action">
														<a href="#" class="action-icon " data-bs-toggle="dropdown" aria-expanded="false">
															<i class="fa fa-ellipsis-v"></i>
														</a>
														<div class="dropdown-menu dropdown-menu-right">
														    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#payments-modal">
																<i class="ti ti-eye text-danger"></i>View
															</a>
														
															<a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
																<i class="ti ti-edit text-blue"></i>Edit
															</a>
															<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_company">
																<i class="ti ti-trash text-danger"></i>Delete
															</a>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<div class="d-flex align-items-center">
														<a href="javascript:void(0);" class="d-flex flex-column fw-medium">Customer 2</a>
													</div>
												</td>
												<td>20,000</td>
												<td>Shitender Kumar</td>
												<td>16-May-2025</td>
												<td>
												    <select class="select"> 
													    <option selected disabled>Action</option>
													     <option>Approve</option>
													     <option>Reject</option>
													</select>
												</td>
												<td>
													<div class="dropdown table-action">
														<a href="#" class="action-icon " data-bs-toggle="dropdown" aria-expanded="false">
															<i class="fa fa-ellipsis-v"></i>
														</a>
														<div class="dropdown-menu dropdown-menu-right">
														    <a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_view_expensehead">
																<i class="ti ti-eye text-danger"></i>View
															</a>
															<a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
																<i class="ti ti-edit text-blue"></i>Edit
															</a>
															<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_company">
																<i class="ti ti-trash text-danger"></i>Delete
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
        
		
		
		
		
		
		<!-- Check In Modal -->
		                        <div id="payments-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<h4 class="modal-title" id="standard-modalLabel">Customer 1</h4>
												<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
											</div>
											<div class="modal-body">
											    <!-- <div class="card-body mt-3" style="display: inline-block;width: 100%;">
													<div class="row">
														<div class="col-md-6">
															<h2 class="mt-0">15,000 </h2>
														</div>
														<div class="col-md-6">
															<select class="select">
																<option>Pending</option>
																<option>Success</option>
															</select>
														</div>
													</div>
													
													
		
								                </div> -->
											
											
											    <div class="card card-bg-light border border-dark">
													<div class="card-body">
													    <div class="row">
																<div class="col-md-6">
																	<h2 class="mt-0">15,000 </h2>
																</div>
																<div class="col-md-6">
																	<select class="select">
																		<option>Pending</option>
																		<option>Success</option>
																	</select>
																</div>
														</div>
													
													
													</div>
												</div>
											
											    
												<div class="table-responsive custom-table">
													<table class="table">
													    <tr>
															<td>Date :</td>
															<td>16 May 2025</td>
														</tr>
														<tr>
															<td>Payment ID :</td>
															<td>75674768-6567</td>
														</tr>
														<tr>
															<td>Transaction Ref :</td>
															<td>954789587</td>
														</tr>
														<tr>
															<td>Date :</td>
															<td>16 May 2025</td>
														</tr>
														<tr>
															<td>Payment Mode :</td>
															<td>Cheque/DD</td>
														</tr>
														<tr>
															<td>Entered By :</td>
															<td>Shefali</td>
														</tr>
														<tr>
															<td>Note/Commen</td>
															<td>Payment Received</td>
														</tr>
														<tr>
															<td>Photos/Doc</td>
															<td><i class="ti ti-file-invoice"></i></td>
														</tr>
													</table>
												</div>
											
											
										
											</div>
										
										</div>
									</div>
								</div>
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
					
				
		
		
		
		
		
		

		
		
		
		
		
		
		
		<!-- Add Expenses -->
		<!-- <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Add Expense</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="">
					<div class="card">
						<div class="card-body">
							<div class="row">
							
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Employee Name</label>
										<input type="text" class="form-control" placeholder="Enter Name">
									</div>									
								</div>
						
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Expense Type</label>
										<select class="select">
										    <option>Travel</option>
										    <option>Accomodation</option>
										    <option>Food and Beverages</option>
										    <option>Office Supplies</option>
										    <option>Client Entertaintment</option>
										    <option>Mobile & Internet</option>
										    <option>Miscellaneous</option>
										</select>
									</div>									
								</div>
								
								
								
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Mode of Traveling </label>
										<select class="select">
										    <option>Bike</option>
										    <option>Bus</option>
										    <option>Car</option>
										    <option>Train</option>
										    <option>Cab</option>
										    <option>Auto</option>
										    <option>Flight</option>
										    <option>Personal Vehicle</option>
										</select>
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Expense Amount </label>
										<input type="text" class="form-control" placeholder="Enter Expense Amount">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Distance Travel Per Month / Day </label>
										<select class="select">
										    <option>Per Day</option>
										    <option>Per Month</option>
										</select>
										<input type="text" class="form-control mt-2" placeholder="Enter Distance">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Location </label>
										<input type="text" class="form-control" placeholder="Enter Location">
									</div>									
								</div>
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Start Date </label>
										<input type="date" class="form-control">
									</div>									
								</div>
								
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">End Date</label>
										<input type="date" class="form-control">
									</div>									
								</div>
								
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Remarks</label>
										<textarea class="form-control"></textarea>
									</div>									
								</div>
								
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Add Bill</label>
										<input type="file" class="form-control" multiple="">
									</div>									
								</div>
								
								
								<div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary">Create</button>
					</div>
								

							</div>
                        </div>
                    </div>						
					
					
			        
					</form>		
			</div>
		</div>
		
		
		
		
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Edit Expense</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="">
					<div class="card">
						<div class="card-body">
							<div class="row">
							
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Employee Name</label>
										<input type="text" class="form-control" placeholder="Enter Name">
									</div>									
								</div>
						
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Expense Type</label>
										<select class="select">
										    <option>Travel</option>
										    <option>Accomodation</option>
										    <option>Food and Beverages</option>
										    <option>Office Supplies</option>
										    <option>Client Entertaintment</option>
										    <option>Mobile & Internet</option>
										    <option>Miscellaneous</option>
										</select>
									</div>									
								</div>
								
								
								
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Mode of Traveling </label>
										<select class="select">
										    <option>Bike</option>
										    <option>Bus</option>
										    <option>Car</option>
										    <option>Train</option>
										    <option>Cab</option>
										    <option>Auto</option>
										    <option>Flight</option>
										    <option>Personal Vehicle</option>
										</select>
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Expense Amount </label>
										<input type="text" class="form-control" placeholder="Enter Expense Amount">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Distance Travel Per Month / Day </label>
										<select class="select">
										    <option>Per Day</option>
										    <option>Per Month</option>
										</select>
										<input type="text" class="form-control mt-2" placeholder="Enter Distance">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Location </label>
										<input type="text" class="form-control" placeholder="Enter Location">
									</div>									
								</div>
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Start Date </label>
										<input type="date" class="form-control">
									</div>									
								</div>
								
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">End Date</label>
										<input type="date" class="form-control">
									</div>									
								</div>
								
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Remarks</label>
										<textarea class="form-control"></textarea>
									</div>									
								</div>
								
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Add Bill</label>
										<input type="file" class="form-control" multiple="">
									</div>									
								</div>
								
								
								<div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary">Create</button>
					</div>
								

							</div>
                        </div>
                    </div>						
					
					
			        
					</form>		
			</div>
		</div>
		
		
		
		
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_view_expense">

			<div class="offcanvas-body">
						<div class="card border border-light">
						
						    <div class="offcanvas-header border-bottom">
								<h5 class="fw-semibold">View Expense</h5>
								<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
									<i class="ti ti-x"></i>
								</button>
							</div>
							<div class="card-body">
							    <div class="card-body border border-dark" style="display: inline-block;width: 100%;">
									<div class="d-flex align-items-center justify-content-between mb-2">
										<p class="fs-14 fw-semibold mb-3 lh-1">Chandigarh</p>
										<p class="fs-14 mb-3 lh-1">07 May 2025 - 08 May 2025</p>
									</div>
									<div class="d-flex align-items-center justify-content-between">
										<div class="d-flex align-items-center">
											<div class="avatar avatar-sm me-2 avatar-rounded">
												<img src="assets/img/profiles/avatar-10.jpg" alt="img">
											</div>
											<div>
												<div class="lh-1">
													<span>Shefali Sharma</span>
												</div>
											</div>
										</div>
										<h4 class="mt-0">0 </h4>
										<span class="badge badge-lg bg-info-transparent mb-2 text-nowrap">Active</span>
									</div>
									
									<h4 class="fs-16 mt-4 mb-2">Description </h4>
									<p class="fs-14 mb-3 lh-1">This is expense description</p>
									
														<div class="hstack gap-2 fs-15 float-end">
															<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-soft-info rounded-pill" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit"><i class="ti ti-edit text-blue"></i></a>
															<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-soft-danger rounded-pill" data-bs-toggle="modal" data-bs-target="#delete_company"><i class="ti ti-trash text-danger"></i></a>
														</div>
									
														
								</div>
								
								<div class="d-flex align-items-center justify-content-between mb-2">
								<h5>List of Expense</h5>
								<a href="javascript:void(0);" class="btn btn-dark mt-4" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-square-rounded-plus me-2"></i> Add Expense</a>
								</div>
								
							
								
								<div class="card-body border border-dark mt-3" style="display: inline-block;width: 100%;">
									<div class="d-flex align-items-center justify-content-between mb-2">
										<p class="fs-14 fw-semibold mb-3 lh-1">Expense 1</p>
										<p class="fs-14 mb-3 lh-1">07 May 2025</p>
									</div>
									<div class="d-flex align-items-center justify-content-between">
										<h4 class="mt-0">25,00 </h4>
									</div>
									
														<div class="hstack gap-2 fs-15 float-end">
															<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-soft-info rounded-pill" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit"><i class="ti ti-edit text-blue"></i></a>
															<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-soft-danger rounded-pill" data-bs-toggle="modal" data-bs-target="#delete_company"><i class="ti ti-trash text-danger"></i></a>
														</div>			
								</div>
								
								<div class="card-body border border-dark mt-3" style="display: inline-block;width: 100%;">
									<div class="d-flex align-items-center justify-content-between mb-2">
										<p class="fs-14 fw-semibold mb-3 lh-1">Expense 2</p>
										<p class="fs-14 mb-3 lh-1">09 May 2025</p>
									</div>
									<div class="d-flex align-items-center justify-content-between">
										<h4 class="mt-0">10,00 </h4>
									</div>
									
														<div class="hstack gap-2 fs-15 float-end">
															<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-soft-info rounded-pill" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit"><i class="ti ti-edit text-blue"></i></a>
															<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-soft-danger rounded-pill" data-bs-toggle="modal" data-bs-target="#delete_company"><i class="ti ti-trash text-danger"></i></a>
														</div>			
								</div>
								
								
								
								
								
							</div>
						</div>
						
						

			</div>
		</div> -->

		<!-- Delete Company -->
		<div class="modal fade" id="delete_company" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove?</h4>
							<p class="mb-0">Are you sure you want to remove.</p>
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
