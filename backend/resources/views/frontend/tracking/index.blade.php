@extends('layouts.main')
@section('content');
<div class="page-wrapper cardhead">
			<div class="content">

				<!-- Page Header -->
				<div class="page-header">
					<div class="row">
						<div class="col-sm-12">
							<h3 class="page-title">Tracking Dashboard</h3>
						</div>
					</div>
				</div>
				<!-- /Page Header -->


				<div class="row flex-fill">
							 <div class="col-md-3 d-flex">
								 <div class="card flex-fill">
									 <div class="card-body">
										 <span class="avatar rounded-circle bg-primary mb-2">
											 <i class="ti ti-calendar-share fs-16"></i>
										 </span>
										 <h6 class="fs-13 fw-medium text-default mb-1">Attendance Overview </h6>
										 <h3 class="mb-3">120/154  <span class="fs-12 fw-medium text-success"><i class="fa-solid fa-caret-up me-1"></i>+2.1% </span></h3>
										 <a href="attendance-employee.html" class="link-default">View Details </a>
									 </div>
								 </div>
							 </div>
							 <div class="col-md-3 d-flex">
								 <div class="card flex-fill">
									 <div class="card-body">
										 <span class="avatar rounded-circle bg-secondary mb-2">
											 <i class="ti ti-browser fs-16"></i>
										 </span>
										 <h6 class="fs-13 fw-medium text-default mb-1">Total No of Project's </h6>
										 <h3 class="mb-3">90/125  <span class="fs-12 fw-medium text-danger"><i class="fa-solid fa-caret-down me-1"></i>-2.1% </span></h3>
										 <a href="projects.html" class="link-default">View All </a>
									 </div>
								 </div>
							 </div>
							 <div class="col-md-3 d-flex">
								 <div class="card flex-fill">
									 <div class="card-body">
										 <span class="avatar rounded-circle bg-info mb-2">
											 <i class="ti ti-users-group fs-16"></i>
										 </span>
										 <h6 class="fs-13 fw-medium text-default mb-1">Total No of Clients </h6>
										 <h3 class="mb-3">69/86  <span class="fs-12 fw-medium text-danger"><i class="fa-solid fa-caret-down me-1"></i>-11.2% </span></h3>
										 <a href="clients.html" class="link-default">View All </a>
									 </div>
								 </div>
							 </div>
							 <div class="col-md-3 d-flex">
								 <div class="card flex-fill">
									 <div class="card-body">
										 <span class="avatar rounded-circle bg-pink mb-2">
											 <i class="ti ti-checklist fs-16"></i>
										 </span>
										 <h6 class="fs-13 fw-medium text-default mb-1">Total No of Tasks </h6>
										 <h3 class="mb-3">225/28  <span class="fs-12 fw-medium text-success"><i class="fa-solid fa-caret-down me-1"></i>+11.2% </span></h3>
										 <a href="tasks.html" class="link-default">View All </a>
									 </div>
								 </div>
							 </div>
							 <div class="col-md-3 d-flex">
								 <div class="card flex-fill">
									 <div class="card-body">
										 <span class="avatar rounded-circle bg-purple mb-2">
											 <i class="ti ti-moneybag fs-16"></i>
										 </span>
										 <h6 class="fs-13 fw-medium text-default mb-1">Earnings </h6>
										 <h3 class="mb-3">$21445  <span class="fs-12 fw-medium text-success"><i class="fa-solid fa-caret-up me-1"></i>+10.2% </span></h3>
										 <a href="expenses.html" class="link-default">View All </a>
									 </div>
								 </div>
							 </div>
							 <div class="col-md-3 d-flex">
								 <div class="card flex-fill">
									 <div class="card-body">
										 <span class="avatar rounded-circle bg-danger mb-2">
											 <i class="ti ti-browser fs-16"></i>
										 </span>
										 <h6 class="fs-13 fw-medium text-default mb-1">Profit This Week </h6>
										 <h3 class="mb-3">$5,544  <span class="fs-12 fw-medium text-success"><i class="fa-solid fa-caret-up me-1"></i>+2.1% </span></h3>
										 <a href="purchase-transaction.html" class="link-default">View All </a>
									 </div>
								 </div>
							 </div>
							 <div class="col-md-3 d-flex">
								 <div class="card flex-fill">
									 <div class="card-body">
										 <span class="avatar rounded-circle bg-success mb-2">
											 <i class="ti ti-users-group fs-16"></i>
										 </span>
										 <h6 class="fs-13 fw-medium text-default mb-1">Job Applicants </h6>
										 <h3 class="mb-3">98  <span class="fs-12 fw-medium text-success"><i class="fa-solid fa-caret-up me-1"></i>+2.1% </span></h3>
										 <a href="job-list.html" class="link-default">View All </a>
									 </div>
								 </div>
							 </div>
							 <div class="col-md-3 d-flex">
								 <div class="card flex-fill">
									 <div class="card-body">
										 <span class="avatar rounded-circle bg-dark mb-2">
											 <i class="ti ti-user-star fs-16"></i>
										 </span>
										 <h6 class="fs-13 fw-medium text-default mb-1">New Hire </h6>
										 <h3 class="mb-3">45/48  <span class="fs-12 fw-medium text-danger"><i class="fa-solid fa-caret-down me-1"></i>-11.2% </span></h3>
										 <a href="candidates.html" class="link-default">View All </a>
									 </div>
								 </div>
							 </div>
						 </div>



				<div class="row">

					<!-- Chart -->
					<!-- <div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Bar Chart</div>
							</div>
							<div class="card-body">
								<div>
									<canvas id="chartBar1" class="h-300"></canvas>
								</div>
							</div>
						</div>
					</div> -->
					<!-- /Chart -->
					<div class="row">
					<!-- Chart -->
					<div class="col-md-8">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Visits</div>
							</div>
							<div class="card-body">
								<div class="chartjs-wrapper-demo">
									<canvas id="chartStacked1" class="h-600"></canvas>
								</div>
							</div>
						</div>
					</div>
					<!-- /Chart -->




					<!-- Chart -->
					<div class="col-md-4">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Attendance</div>
							</div>
							<div class="card-body">
								<div class="chartjs-wrapper-demo">
									<canvas id="chartPie2" class="h-300"></canvas>
								</div>
							</div>
						</div>
					</div>
					<!-- /Chart -->
					</div>





					<!-- Chart -->
					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Gradient Bar Chart</div>
							</div>
							<div class="card-body">
								<div>
									<canvas id="chartBar3" class="h-300"></canvas>
								</div>
							</div>
						</div>
					</div>
					<!-- /Chart -->


					<!-- Chart -->
					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Horizontal Bar Chart</div>
							</div>
							<div class="card-body">
								<div class="chartjs-wrapper-demo">
									<canvas id="chartBar4" class="h-300"></canvas>
								</div>
							</div>
						</div>
					</div>
					<!-- /Chart -->







					<div class="row">
					<!-- Chart -->
					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Zone Wise Visit</div>
							</div>
							<div class="card-body">
								<div class="chartjs-wrapper-demo">
									<canvas id="zonewise" class="h-300"></canvas>
								</div>
							</div>
						</div>
					</div>
					<!-- /Chart -->

					<!-- Chart -->
					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Call Champion</div>
							</div>
							<div class="card-body">
								<div class="chartjs-wrapper-demo">
									<canvas id="callchampion" class="h-300"></canvas>
								</div>
							</div>
						</div>
					</div>
					<!-- /Chart -->
					</div>


					<!-- Chart -->
					<div class="col-md-12">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Doctor Business</div>
							</div>
							<div class="card-body">
								<div class="chartjs-wrapper-demo">
									<canvas id="chartStacked3" class="h-600"></canvas>
								</div>
							</div>
						</div>
					</div>
					<!-- /Chart -->

					<div class="col-md-6">
					    <div class="card">
						    <div class="card-header">
								<div class="card-title">Field Activities</div>
							</div>
						    <div class="card-body">
						        <div class="table-responsive">
    <table class="table dataTable no-footer" id="activity-list" style="width: 1180px;">
        <thead class="thead-light">
            <tr>
                <th class="no-sort sorting sorting_asc" tabindex="0" aria-controls="activity-list" rowspan="1" colspan="1" aria-sort="ascending" aria-label="

												: activate to sort column descending" style="width: 35px;">
                    <label class="checkboxs">
                        <input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
                </th>
                <th class="sorting" tabindex="0" aria-controls="activity-list" rowspan="1" colspan="1" aria-label="Title: activate to sort column ascending" style="width: 326px;">Title</th>
                <th class="sorting" tabindex="0" aria-controls="activity-list" rowspan="1" colspan="1" aria-label="Activity Type: activate to sort column ascending" style="width: 95px;">Activity Type</th>
                <th class="sorting" tabindex="0" aria-controls="activity-list" rowspan="1" colspan="1" aria-label="Due Date: activate to sort column ascending" style="width: 158px;">Date</th>

            </tr>
        </thead>
        <tbody>
            <tr class="odd">
                <td class="sorting_1">
                    <label class="checkboxs">
                        <input type="checkbox"><span class="checkmarks"></span></label>
                </td>
                <td>We scheduled a meeting for next week</td>
                <td><span class="badge activity-badge bg-purple"><i class="ti ti-user-share"></i>Meeting</span></td>
                <td>25 Sep 2023, 12:12 pm</td>


            </tr>
            <tr class="even">
                <td class="sorting_1">
                    <label class="checkboxs">
                        <input type="checkbox"><span class="checkmarks"></span></label>
                </td>
                <td>Had conversation with Fred regarding task</td>
                <td><span class="badge activity-badge bg-green"><i class="ti ti-phone"></i>Calls</span></td>
                <td>29 Sep 2023, 04:12 pm</td>


            </tr>
            <tr class="odd">
                <td class="sorting_1">
                    <label class="checkboxs">
                        <input type="checkbox"><span class="checkmarks"></span></label>
                </td>
                <td>Analysing latest time estimation for new project</td>
                <td><span class="badge activity-badge bg-warning"><i class="ti ti-mail"></i>Email</span></td>
                <td>11 Oct 2023, 05:00 pm</td>


            </tr>
            <tr class="even">
                <td class="sorting_1">
                    <label class="checkboxs">
                        <input type="checkbox"><span class="checkmarks"></span></label>
                </td>
                <td>Store and manage contact data</td>
                <td><span class="badge activity-badge bg-blue"><i class="ti ti-subtask"></i>Task</span></td>
                <td>19 Oct 2023, 02:35 pm</td>


            </tr>
            <tr class="odd">
                <td class="sorting_1">
                    <label class="checkboxs">
                        <input type="checkbox"><span class="checkmarks"></span></label>
                </td>
                <td>Will have a meeting before project start</td>
                <td><span class="badge activity-badge bg-purple"><i class="ti ti-user-share"></i>Meeting</span></td>
                <td>27 Oct 2023, 12:30 pm</td>


            </tr>

        </tbody>
    </table>
</div>

				             </div>
						</div>
					</div>


<!-- Chart -->
					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Visits</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table mb-0">
										<thead>
											<tr>
												<th>Firstname</th>
												<th>Lastname</th>
												<th>Email</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>John</td>
												<td>Doe</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="72181d1a1c32170a131f021e175c111d1f">john@example.com</a></td>
											</tr>
											<tr>
												<td>Mary</td>
												<td>Moe</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="6a070b18132a0f120b071a060f44090507">john@example.com</a></td>
											</tr>
											<tr>
												<td>July</td>
												<td>Dooley</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f298879e8bb2978a939f829e97dc919d9f">john@example.com</a></td>
											</tr>
											<tr>
												<td>July</td>
												<td>Dooley</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f298879e8bb2978a939f829e97dc919d9f">john@example.com</a></td>
											</tr>
											<tr>
												<td>July</td>
												<td>Dooley</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f298879e8bb2978a939f829e97dc919d9f">john@example.com</a></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<!-- /Chart -->


<!-- Chart -->
					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Employee Attendance</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table mb-0">
										<thead>
											<tr>
												<th>Firstname</th>
												<th>Lastname</th>
												<th>Email</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>John</td>
												<td>Doe</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="72181d1a1c32170a131f021e175c111d1f">john@example.com</a></td>
											</tr>
											<tr>
												<td>Mary</td>
												<td>Moe</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="6a070b18132a0f120b071a060f44090507">john@example.com</a></td>
											</tr>
											<tr>
												<td>July</td>
												<td>Dooley</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f298879e8bb2978a939f829e97dc919d9f">john@example.com</a></td>
											</tr>
											<tr>
												<td>July</td>
												<td>Dooley</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f298879e8bb2978a939f829e97dc919d9f">john@example.com</a></td>
											</tr>
											<tr>
												<td>July</td>
												<td>Dooley</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f298879e8bb2978a939f829e97dc919d9f">john@example.com</a></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<!-- /Chart -->

					<!-- Chart -->
					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Leave Widget</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table mb-0">
										<thead>
											<tr>
												<th>Firstname</th>
												<th>Lastname</th>
												<th>Email</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>John</td>
												<td>Doe</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="72181d1a1c32170a131f021e175c111d1f">john@example.com</a></td>
											</tr>
											<tr>
												<td>Mary</td>
												<td>Moe</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="6a070b18132a0f120b071a060f44090507">john@example.com</a></td>
											</tr>
											<tr>
												<td>July</td>
												<td>Dooley</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f298879e8bb2978a939f829e97dc919d9f">john@example.com</a></td>
											</tr>
											<tr>
												<td>July</td>
												<td>Dooley</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f298879e8bb2978a939f829e97dc919d9f">john@example.com</a></td>
											</tr>
											<tr>
												<td>July</td>
												<td>Dooley</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f298879e8bb2978a939f829e97dc919d9f">john@example.com</a></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<!-- /Chart -->

					<div class="row">
					    <!-- Chart -->
					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Product Order Booking Details</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table mb-0">
										<thead>
											<tr>
												<th>Firstname</th>
												<th>Lastname</th>
												<th>Email</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>John</td>
												<td>Doe</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="72181d1a1c32170a131f021e175c111d1f">john@example.com</a></td>
											</tr>
											<tr>
												<td>Mary</td>
												<td>Moe</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="6a070b18132a0f120b071a060f44090507">john@example.com</a></td>
											</tr>
											<tr>
												<td>July</td>
												<td>Dooley</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f298879e8bb2978a939f829e97dc919d9f">john@example.com</a></td>
											</tr>
											<tr>
												<td>July</td>
												<td>Dooley</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f298879e8bb2978a939f829e97dc919d9f">john@example.com</a></td>
											</tr>
											<tr>
												<td>July</td>
												<td>Dooley</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f298879e8bb2978a939f829e97dc919d9f">john@example.com</a></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<!-- /Chart -->


					<!-- Chart -->
					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<div class="card-title">All Visits</div>
							</div>
							<div class="card-body">
								<div class="chartjs-wrapper-demo">
									<canvas id="allvisitor" class="h-300"></canvas>
								</div>
							</div>
						</div>
					</div>
					<!-- /Chart -->



					</div>


					<!-- Chart -->
					<div class="col-md-12">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Date Wise Calls</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table mb-0">
										<thead>
											<tr>
												<th>Firstname</th>
												<th>Lastname</th>
												<th>Email</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>John</td>
												<td>Doe</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="72181d1a1c32170a131f021e175c111d1f">john@example.com</a></td>
											</tr>
											<tr>
												<td>Mary</td>
												<td>Moe</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="6a070b18132a0f120b071a060f44090507">john@example.com</a></td>
											</tr>
											<tr>
												<td>July</td>
												<td>Dooley</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f298879e8bb2978a939f829e97dc919d9f">john@example.com</a></td>
											</tr>
											<tr>
												<td>July</td>
												<td>Dooley</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f298879e8bb2978a939f829e97dc919d9f">john@example.com</a></td>
											</tr>
											<tr>
												<td>July</td>
												<td>Dooley</td>
												<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f298879e8bb2978a939f829e97dc919d9f">john@example.com</a></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<!-- /Chart -->




					<div class="row">
					<!-- Chart -->
					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<div class="card-title">State/Zone & Designation Wise Employee</div>
							</div>
							<div class="card-body">
								<div class="chartjs-wrapper-demo">
									<canvas id="empwise" class="h-300"></canvas>
								</div>
							</div>
						</div>
					</div>
					<!-- /Chart -->

					<!-- Chart -->
					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Zone & Speciality Wise Doctor</div>
							</div>
							<div class="card-body">
								<div class="chartjs-wrapper-demo">
									<canvas id="docwise" class="h-300"></canvas>
								</div>
							</div>
						</div>
					</div>
					<!-- /Chart -->
					</div>








					<!-- Chart -->
					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Area Chart</div>
							</div>
							<div class="card-body">
								<div class="chartjs-wrapper-demo">
									<canvas id="chartArea1" class="h-300"></canvas>
								</div>
							</div>
						</div>
					</div>
					<!-- /Chart -->

				</div>

			</div>
		</div>
@endsection
