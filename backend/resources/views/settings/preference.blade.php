@extends('layouts.main')
@section('content')
@section('title','Preference')
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
										<a href="preference.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
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

								<!-- Prefixes -->
								<div class="card">
									<div class="card-body">
										<h4 class="fw-semibold mb-3">Preference</h4>
										<form action="preference.html">
											<div class="row">
												<div class="col-xxl-4 col-sm-6">
													<div class="card border mb-3">
														<div class="card-body d-flex align-items-center justify-content-between">
															<div class="d-flex align-items-center">
																<img src="assets/img/icons/preference-01.svg" alt="">
																<h6 class="fw-semibold ms-3">Contact</h6>
															</div>
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" role="switch" checked="">
															</div>
														</div>
													</div>
												</div>
												<div class="col-xxl-4 col-sm-6">
													<div class="card border mb-3">
														<div class="card-body d-flex align-items-center justify-content-between">
															<div class="d-flex align-items-center">
																<img src="assets/img/icons/preference-02.svg" alt="">
																<h6 class="fw-semibold ms-3">Deals</h6>
															</div>
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" role="switch" checked="">
															</div>
														</div>
													</div>
												</div>
												<div class="col-xxl-4 col-sm-6">
													<div class="card border mb-3">
														<div class="card-body d-flex align-items-center justify-content-between">
															<div class="d-flex align-items-center">
																<img src="assets/img/icons/preference-03.svg" alt="">
																<h6 class="fw-semibold ms-3">Leads</h6>
															</div>
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" role="switch" checked="">
															</div>
														</div>
													</div>
												</div>
												<div class="col-xxl-4 col-sm-6">
													<div class="card border mb-3">
														<div class="card-body d-flex align-items-center justify-content-between">
															<div class="d-flex align-items-center">
																<img src="assets/img/icons/preference-04.svg" alt="">
																<h6 class="fw-semibold ms-3">Pipelines</h6>
															</div>
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" role="switch" checked="">
															</div>
														</div>
													</div>
												</div>
												<div class="col-xxl-4 col-sm-6">
													<div class="card border mb-3">
														<div class="card-body d-flex align-items-center justify-content-between">
															<div class="d-flex align-items-center">
																<img src="assets/img/icons/preference-02.svg" alt="">
																<h6 class="fw-semibold ms-3">Campaign</h6>
															</div>
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" role="switch" checked="">
															</div>
														</div>
													</div>
												</div>
												<div class="col-xxl-4 col-sm-6">
													<div class="card border mb-3">
														<div class="card-body d-flex align-items-center justify-content-between">
															<div class="d-flex align-items-center">
																<img src="assets/img/icons/preference-06.svg" alt="">
																<h6 class="fw-semibold ms-3">Projects</h6>
															</div>
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" role="switch" checked="">
															</div>
														</div>
													</div>
												</div>
												<div class="col-xxl-4 col-sm-6">
													<div class="card border mb-3">
														<div class="card-body d-flex align-items-center justify-content-between">
															<div class="d-flex align-items-center">
																<img src="assets/img/icons/preference-07.svg" alt="">
																<h6 class="fw-semibold ms-3">Tasks</h6>
															</div>
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" role="switch" checked="">
															</div>
														</div>
													</div>
												</div>
												<div class="col-xxl-4 col-sm-6">
													<div class="card border mb-3">
														<div class="card-body d-flex align-items-center justify-content-between">
															<div class="d-flex align-items-center">
																<img src="assets/img/icons/preference-08.svg" alt="">
																<h6 class="fw-semibold ms-3">Acivities</h6>
															</div>
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" role="switch" checked="">
															</div>
														</div>
													</div>
												</div>
												<div class="col-xxl-4 col-sm-6">
													<div class="card border mb-3">
														<div class="card-body d-flex align-items-center justify-content-between">
															<div class="d-flex align-items-center">
																<img src="assets/img/icons/preference-09.svg" alt="">
																<h6 class="fw-semibold ms-3">Company</h6>
															</div>
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" role="switch" checked="">
															</div>
														</div>
													</div>
												</div>
												<div class="col-xxl-4 col-sm-6">
													<div class="card border mb-3">
														<div class="card-body d-flex align-items-center justify-content-between">
															<div class="d-flex align-items-center">
																<img src="assets/img/icons/preference-10.svg" alt="">
																<h6 class="fw-semibold ms-3">Analytics</h6>
															</div>
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" role="switch" checked="">
															</div>
														</div>
													</div>
												</div>
												<div class="col-xxl-4 col-sm-6">
													<div class="card border mb-3">
														<div class="card-body d-flex align-items-center justify-content-between">
															<div class="d-flex align-items-center">
																<img src="assets/img/icons/preference-11.svg" alt="">
																<h6 class="fw-semibold ms-3">Clients</h6>
															</div>
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" role="switch" checked="">
															</div>
														</div>
													</div>
												</div>
												<div class="col-xxl-4 col-sm-6">
													<div class="card border mb-3">
														<div class="card-body d-flex align-items-center justify-content-between">
															<div class="d-flex align-items-center">
																<img src="assets/img/icons/preference-12.svg" alt="">
																<h6 class="fw-semibold ms-3">Customers</h6>
															</div>
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" role="switch" checked="">
															</div>
														</div>
													</div>
												</div>
											</div>
											<div>
												<a href="#" class="btn btn-light me-2">Cancel</a>
												<button type="submit" class="btn btn-primary">Save Changes</button>
											</div>
										</form>
									</div>
								</div>
								<!-- /Prefixes -->

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Page Wrapper -->

    @endsection
