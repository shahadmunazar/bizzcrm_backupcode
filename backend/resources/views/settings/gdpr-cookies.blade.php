@extends('layouts.main')
@section('content')
@section('title','GDPR Cookies')
<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">
				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									<h4 class="page-title">Settings</h4>
								</div>
								<div class="col-4 text-end">
									<div class="head-icons">
										<a href="gdpr-cookies.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

						@include('settings.partials.nav')

						<div class="row">
							<div class="col-xl-3 col-lg-12 theiaStickySidebar">
								@include('settings.partials.sidebar')
							</div>
							<div class="col-xl-9 col-lg-12">

								<!-- GDPR Cookies -->
								<div class="card">
									<div class="card-body">
										<div class="mb-3">
											<h4 class="fs-20">GDPR Cookies</h4>
										</div>
										<form action="gdpr-cookies.html">
											<div class="row">
												<div class="col-md-6">
													<div class="mb-3">
														<h6 class="fw-medium">Cookies Content Text</h6>
														<p class="fs-12">You can configure the text here</p>
													</div>
												</div>
												<div class="col-md-6">
													<div class="mb-3">
														<div class="summernote"></div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<div class="mb-3">
														<h6 class="fw-medium">Cookies Position</h6>
														<p class="fs-12">You can configure the type</p>
													</div>
												</div>
												<div class="col-md-4">
													<div class="mb-3">
														<select class="select">
															<option selected="">Right</option>
															<option>Left</option>
														</select>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<div class="mb-3">
														<h6 class="fw-medium">Agree Button Text</h6>
														<p class="fs-12">You can configure the text here</p>
													</div>
												</div>
												<div class="col-md-4">
													<div class="mb-3">
														<input type="text" class="form-control" value="Manage">
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<div class="mb-3">
														<h6 class="fw-medium">Decline Button Text</h6>
														<p class="fs-12">You can configure the text here</p>
													</div>
												</div>
												<div class="col-md-4">
													<div class="mb-3">
														<input type="text" class="form-control" value="Manage">
													</div>
												</div>
											</div>
											<div class="row align-items-center">
												<div class="col-md-6">
													<div class="mb-3">
														<h6 class="fw-medium">Show Decline Button</h6>
														<p class="fs-12">To display decline button</p>
													</div>
												</div>
												<div class="col-md-4">
													<div class="mb-3">
														<div class="status-toggle">
															<input type="checkbox" id="sms1" class="check" checked="">
															<label for="sms1" class="checktoggle"> </label>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<div class="mb-3">
														<h6 class="fw-medium">Link for Cookies Page</h6>
														<p class="fs-12">You can configure the link here</p>
													</div>
												</div>
												<div class="col-md-4">
													<div class="mb-3">
														<input type="text" class="form-control">
													</div>
												</div>
											</div>
											<div class="d-flex align-items-center">
												<a href="#" class="btn btn-light me-2">Cancel</a>
												<button type="submit" class="btn btn-primary">Save Changes</button>
											</div>
										</form>
									</div>
								</div>
								<!-- /GDPR Cookies -->

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Page Wrapper -->
        @endsection
