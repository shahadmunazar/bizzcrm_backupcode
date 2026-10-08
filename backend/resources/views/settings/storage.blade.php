@extends('layouts.main')
@section('content')
@section('title','Storage')
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
										<a href="storage.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
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

								<!-- Settings Info -->
								<div class="card">
									<div class="card-body">
										<div class="mb-4 d-flex justify-content-between align-items-center">
											<h4 class="fs-20">Storage Settings</h4>
											@php
											    function formatBytes($bytes) {
											        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
											        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
											        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
											        return $bytes . ' bytes';
											    }
											    $totalBytes = $box->storage_used_bytes ?? 0;
											    $localBytes = $box->local_storage_used_bytes ?? 0;
											    $awsBytes = $box->aws_storage_used_bytes ?? 0;
											@endphp
											<span class="badge bg-primary p-2 fs-14">Total Used: {{ formatBytes($totalBytes) }}</span>
										</div>
										<div class="row">

											<!-- Storage -->
											<div class="col-xxl-4 col-sm-6">
												<div class="border rounded p-3 d-flex align-items-center justify-content-between mb-4">
													<div class="d-flex align-items-center">
														<span class="avatar avatar-lg bg-light-100 border flex-shrink-0 me-2">
															<img src="assets/img/icons/storage-icon-01.svg" class="w-auto h-auto" alt="">
														</span>
														<div>
															<h6 class="fw-semibold mb-1">Local Storage</h6>
															<span class="fs-12 text-muted">{{ formatBytes($localBytes) }} Used</span>
														</div>
													</div>
													<div class="d-flex align-items-center">
														<a href="javascript:void(0);" class="me-2"><i class="ti ti-settings fs-20"></i></a>
														<div class="status-toggle">
															<input type="checkbox" id="loc" class="check" checked="">
															<label for="loc" class="checktoggle"> </label>
														</div>
													</div>
												</div>
											</div>
											<!-- /Storage -->

											<!-- Storage -->
											<div class="col-xxl-4 col-sm-6">
												<div class="border rounded p-3 d-flex align-items-center justify-content-between mb-4">
													<div class="d-flex align-items-center">
														<span class="avatar avatar-lg bg-light-100 border flex-shrink-0 me-2">
															<img src="assets/img/icons/storage-icon-02.svg" class="w-auto h-auto" alt="">
														</span>
														<div>
															<h6 class="fw-semibold mb-1">AWS</h6>
															<span class="fs-12 text-muted">{{ formatBytes($awsBytes) }} Used</span>
														</div>
													</div>
													<div class="d-flex align-items-center">
														<a href="javascript:void(0);" class="me-2" data-bs-toggle="modal" data-bs-target="#add_settings"><i class="ti ti-settings fs-20"></i></a>
														<div class="status-toggle">
															<input type="checkbox" id="aws" class="check" checked="">
															<label for="aws" class="checktoggle"></label>
														</div>
													</div>
												</div>
											</div>
											<!-- /Storage -->

										</div>
										<div class="d-flex align-items-center">
											<a href="#" class="btn btn-light me-2">Cancel</a>
											<button type="submit" class="btn btn-primary">Save Changes</button>
										</div>
									</div>
								</div>
								<!-- /Settings Info -->

							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- AWS Settings -->
		<div class="modal custom-modal fade" id="add_settings" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">AWS Settings</h5>
						<div class="d-flex align-items-center mod-toggle">
							<div class="status-toggle">
								<input type="checkbox" id="toggle" class="check" checked="">
								<label for="toggle" class="checktoggle"></label>
							</div>
							<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
								<i class="ti ti-x"></i>
							</button>
						</div>
					</div>
					<div class="modal-body">
						<form action="storage.html">
							<div class="mb-3">
								<label class="col-form-label">AWS Access Key <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
							<div class="mb-3">
								<label class="col-form-label">Secret Key <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
							<div class="mb-3">
								<label class="col-form-label">Bucket Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
							<div class="mb-3">
								<label class="col-form-label">Region <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
							<div class="mb-3">
								<label class="col-form-label">Base URL <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
							<div class="modal-btn">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-primary">Save</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /AWS Settings -->
        @endsection
