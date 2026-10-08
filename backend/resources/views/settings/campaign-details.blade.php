@extends('layouts.main')
@section('content')
@section('title', 'Integration')



		<div class="page-wrapper cardhead">
			<div class="content">

				<!-- Page Header -->
				<div class="page-header">
					<div class="row">
						<div class="col-sm-12">
							<h3 class="page-title">Campaign Distribution</h3>
						</div>
					</div>
				</div>
				<!-- /Page Header -->

				<div class="row">
				    <div class="col-md-4">
					    <div class="card">
							<div class="card-body p-3">
								<h6 class="mb-3 fw-semibold">Campaign Information</h6>
								<ul>
									<li class="row mb-3"><span class="d-block mb-1"><strong>Campaign Type</strong></span><span class="d-block">Broadcast</span></li>
									<li class="row mb-3"><span class="d-block mb-1"><strong>Message Type</strong></span><span class="col-6">Template (Text)</span></li>
									<li class="row mb-3"><span class="d-block mb-1"><strong>Template Name</strong></span><span class="col-6">pharmafranchise</span></li>
									<li class="row mb-3"><span class="d-block mb-1"><strong>Created At</strong></span><span class="col-6">27 Sep 2023, 11:45 pm</span></li>
									<li class="row mb-3"><span class="d-block mb-1"><strong>Completed At</strong></span><span class="col-6">28 Oct 2023, 09:45 am</span></li>
									<li class="row mb-3"><span class="d-block mb-1"><strong>Campaign Duration</strong></span><span class="col-6">Completed in 2 Minute</span></li>
								</ul>
								<hr>

							</div>
						</div>

						<div class="card">
							<div class="card-body p-3">
							    <h6 class="mb-3 fw-semibold">Import info</h6>
								<ul>
									<li class="row mb-3"><span class="col-6">sms list 10000 to 23085.csv</span><span class="col-6">4500</span></li>
									<li class="row mb-3"><span class="col-6">Contacts Created</span><span class="col-6">3400</span></li>
									<li class="row mb-3"><span class="col-6">Contacts Updated</span><span class="col-6">2</span></li>
									<li class="row mb-3"><span class="col-6">Contacts Invalid</span><span class="col-6">430</span></li>
									<li class="row mb-3"><span class="col-6">CSV Headers</span></li>
									<li class="row mb-3"><span class="col-6">user name</span><span class="col-6">Name</span></li>
									<li class="row mb-3"><span class="col-6">phone number</span><span class="col-6">Mobile</span></li>
								</ul>
							</div>
						</div>

						<div class="card">
							<div class="card-body p-3">
							    <h6 class="mb-3 fw-semibold">Template</h6>

							</div>
						</div>


					</div>
					<div class="col-md-8">
					    <div class="row flex-fill">
							 <div class="col-md-4 d-flex">
								 <div class="card flex-fill">
									 <div class="card-body">
										 <span class="avatar rounded-circle bg-primary mb-2">
											 <i class="ti ti-calendar-share fs-16"></i>
										 </span>
										 <h6 class="fs-13 fw-medium text-default mb-1">Status </h6>
										 <h3 class="mb-3">Sent  <span class="fs-12 fw-medium text-success"><i class="fa-solid fa-caret-up me-1"></i>+2.1% </span></h3>

									 </div>
								 </div>
							 </div>
							 <div class="col-md-4 d-flex">
								 <div class="card flex-fill">
									 <div class="card-body">
										 <span class="avatar rounded-circle bg-secondary mb-2">
											 <i class="ti ti-browser fs-16"></i>
										 </span>
										 <h6 class="fs-13 fw-medium text-default mb-1">Audience </h6>
										 <h3 class="mb-3">3500  <span class="fs-12 fw-medium text-danger"><i class="fa-solid fa-caret-down me-1"></i>-2.1% </span></h3>

									 </div>
								 </div>
							 </div>
							 <div class="col-md-4 d-flex">
								 <div class="card flex-fill">
									 <div class="card-body">
										 <span class="avatar rounded-circle bg-info mb-2">
											 <i class="ti ti-users-group fs-16"></i>
										 </span>
										 <h6 class="fs-13 fw-medium text-default mb-1">Total Credit Usage </h6>
										 <h3 class="mb-3">₹ 2500.50  <span class="fs-12 fw-medium text-danger"><i class="fa-solid fa-caret-down me-1"></i>-11.2% </span></h3>

									 </div>
								 </div>
							 </div>





							<div class="col-md-3 d-flex">
								 <div class="card flex-fill">
									 <div class="card-body">
										 <span class="avatar rounded-circle bg-pink mb-2">
											 <i class="ti ti-checklist fs-16"></i>
										 </span>
										 <h6 class="fs-13 fw-medium text-default mb-1">Sent </h6>
										 <h3 class="mb-3">2255  <span class="fs-12 fw-medium text-success"><i class="fa-solid fa-caret-down me-1"></i>+11.2% </span></h3>

									 </div>
								 </div>
							 </div>
							 <div class="col-md-3 d-flex">
								 <div class="card flex-fill">
									 <div class="card-body">
										 <span class="avatar rounded-circle bg-purple mb-2">
											 <i class="ti ti-moneybag fs-16"></i>
										 </span>
										 <h6 class="fs-13 fw-medium text-default mb-1">Delivered </h6>
										 <h3 class="mb-3">1445  <span class="fs-12 fw-medium text-success"><i class="fa-solid fa-caret-up me-1"></i>+10.2% </span></h3>

									 </div>
								 </div>
							 </div>
							 <div class="col-md-3 d-flex">
								 <div class="card flex-fill">
									 <div class="card-body">
										 <span class="avatar rounded-circle bg-danger mb-2">
											 <i class="ti ti-browser fs-16"></i>
										 </span>
										 <h6 class="fs-13 fw-medium text-default mb-1">Read </h6>
										 <h3 class="mb-3">544  <span class="fs-12 fw-medium text-success"><i class="fa-solid fa-caret-up me-1"></i>+2.1% </span></h3>

									 </div>
								 </div>
							 </div>
							 <div class="col-md-3 d-flex">
								 <div class="card flex-fill">
									 <div class="card-body">
										 <span class="avatar rounded-circle bg-success mb-2">
											 <i class="ti ti-users-group fs-16"></i>
										 </span>
										 <h6 class="fs-13 fw-medium text-default mb-1">Replied </h6>
										 <h3 class="mb-3">8  <span class="fs-12 fw-medium text-success"><i class="fa-solid fa-caret-up me-1"></i>+2.1% </span></h3>

									 </div>
								 </div>
							 </div>

				        </div>


					<!-- Chart -->
					<div class="col-md-12">
						<div class="card">
							<div class="card-header">
								<h5 class="card-title">Campaign Messages</h5>
							</div>
							<div class="card-body">
								<div id="s-line-area" class="chart-set"></div>
							</div>
						</div>
					</div>
					<!-- /Chart -->




				</div>







			</div>
		</div>
	</div>

@endsection
