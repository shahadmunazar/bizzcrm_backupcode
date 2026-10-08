@extends('layouts.main')
@section('content')
@section('title','Companies Grid')

		<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-4">
									<h4 class="page-title">Companies<span class="count-title">{{ $totalCompanies ?? 0 }}</span></h4>
								</div>
								<div class="col-8 text-end">
									<div class="head-icons">
										<a href="companies-grid.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
									</div>
								</div>
							</div>
						</div>
						@include('settings.partials.nav')

						<div class="row">
							<div class="col-xl-3 col-lg-12 theiaStickySidebar">
								@include('settings.partials.sidebar')
							</div>
							<div class="col-xl-9 col-lg-12">
								<div class="card">
							<div class="card-header">
								<!-- Search -->
								<div class="row align-items-center">
									<div class="col-sm-4">
										<div class="icon-form mb-3 mb-sm-0">
											<span class="form-icon"><i class="ti ti-search"></i></span>
											<input type="text" class="form-control" placeholder="Search Contacts">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
											<div class="dropdown me-2">
												<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
												<div class="dropdown-menu  dropdown-menu-end">
													<ul>
														<li>
															<a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-file-type-pdf text-danger me-1"></i>Export
																as PDF</a>
														</li>
														<li>
															<a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-file-type-xls text-green me-1"></i>Export
																as Excel </a>
														</li>
													</ul>
												</div>
											</div>
											<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-square-rounded-plus me-2"></i>Add Company</a>
										</div>
									</div>
								</div>
								<!-- /Search -->
							</div>
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<div class="dropdown me-2">
											<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-sort-ascending-2 me-1"></i>Sort </a>
											<div class="dropdown-menu  dropdown-menu-start">
												<ul>
													<li>
														<a href="javascript:void(0);" class="dropdown-item">
															<i class="ti ti-circle-chevron-right me-1"></i>Ascending
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item">
															<i class="ti ti-circle-chevron-right me-1"></i>Descending
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item">
															<i class="ti ti-circle-chevron-right me-1"></i>Recently
															Viewed
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item">
															<i class="ti ti-circle-chevron-right me-1"></i>Recently
															Added
														</a>
													</li>
												</ul>
											</div>
										</div>
										<div class="icon-form me-2">
											<span class="form-icon"><i class="ti ti-calendar"></i></span>
											<input type="text" class="form-control bookingrange" placeholder="">
										</div>
										<div class="form-sorts dropdown me-2">
											<a href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-filter-share"></i>Filter</a>
											<div class="filter-dropdown-menu dropdown-menu  dropdown-menu-md-end p-3">
												<div class="filter-set-view">
													<div class="filter-set-head">
														<h4><i class="ti ti-filter-share"></i>Filter</h4>
													</div>
													<div class="accordion" id="accordionExample">
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">Company Name</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse show" id="collapseTwo" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<div class="mb-2 icon-form">
																		<span class="form-icon"><i class="ti ti-search"></i></span>
																		<input type="text" class="form-control" placeholder="Search Company">
																	</div>
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					NovaWave LLC
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					BlueSky Industries
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					SilverSpire
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					SummitPeak
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					RiverStone Ventur
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">Location</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="collapseThree" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<div class="mb-2 icon-form">
																		<span class="form-icon"><i class="ti ti-search"></i></span>
																		<input type="text" class="form-control" placeholder="Search Location">
																	</div>
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Germany
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					USA
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Canada
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					India
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					China
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">Rating</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="collapseOne" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					<span class="rating">
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<span>5.0</span>
																					</span>
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					<span class="rating">
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star"></i>
																						<span>4.0</span>
																					</span>
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					<span class="rating">
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<span>3.0</span>
																					</span>
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					<span class="rating">
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<span>2.0</span>
																					</span>
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					<span class="rating">
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<span>1.0</span>
																					</span>
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapsefour" aria-expanded="false" aria-controls="collapsefour">Owner</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="collapsefour" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<div class="mb-2 icon-form">
																		<span class="form-icon"><i class="ti ti-search"></i></span>
																		<input type="text" class="form-control" placeholder="Search Owner">
																	</div>
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Hendry
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Guilherme
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					J_S_S_S
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Alexandrefer
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					John_A
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapsefive" aria-expanded="false" aria-controls="collapsefive">Status</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="collapsefive" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Active
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Inactive
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapsesix" aria-expanded="false" aria-controls="collapsesix">Tags</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="collapsesix" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Collab
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Calls
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
													</div>
													<div class="filter-reset-btns">
														<div class="row">
															<div class="col-6">
																<a href="#" class="btn btn-light">Reset</a>
															</div>
															<div class="col-6">
																<a href="companies-grid.html" class="btn btn-primary">Filter</a>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="view-icons">
											<a href="{{ box_route('settings.general.companies') }}"><i class="ti ti-list-tree"></i></a>
											<a href="{{ box_route('settings.general.companies_grid') }}" class="active"><i class="ti ti-grid-dots"></i></a>
										</div>
									</div>
								</div>
								<!-- Company Grid -->
								<div class="row">

									@foreach($companies as $company)
									<div class="col-xxl-4 col-xl-4 col-md-6">
										<div class="card border">
											<div class="card-body">
												<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-3">
													<div class="d-flex align-items-center">
														<a href="{{ box_route('settings.general.companys_details', ['id' => $company->id]) }}" class="avatar avatar-lg border rounded flex-shrink-0 me-2">
															@php
																$logo = $company->params['pdf_logo'] ?? 'assets/img/icons/company-icon-01.svg';
																if(!str_contains($logo, 'assets/')) {
																	$logo = asset('storage/' . $logo);
																} else {
																	$logo = asset($logo);
																}
															@endphp
															<img src="{{ $logo }}" class="w-auto h-auto" alt="img">
														</a>
														<div>
															<h6><a href="{{ box_route('settings.general.companys_details', ['id' => $company->id]) }}" class="fw-medium">{{ $company->bill_name }}</a></h6>
															<div class="set-star text-default">
																<i class="fa fa-star filled me-1"></i>4.2 <!-- Dynamic rating not in schema yet -->
															</div>
														</div>
													</div>
													<div class="dropdown table-action">
														<a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false">
															<i class="fa fa-ellipsis-v"></i>
														</a>
														<div class="dropdown-menu dropdown-menu-right">
															<a class="dropdown-item edit-company" href="#" data-id="{{ $company->id }}" data-json='{{ json_encode($company) }}'><i class="ti ti-edit text-blue"></i> Edit</a>
															<a class="dropdown-item delete-company" href="#" data-id="{{ $company->id }}"><i class="ti ti-trash text-danger"></i> Delete</a>
															<a class="dropdown-item" href="{{ box_route('settings.general.companys_details', ['id' => $company->id]) }}"><i class="ti ti-eye text-blue-light"></i> Preview</a>
														</div>
													</div>
												</div>
												<div class="d-block">
													<div class="d-flex flex-column mb-3">
														<p class="text-default d-inline-flex align-items-center mb-2"><i class="ti ti-mail text-dark me-1"></i>{{ $company->email ?? 'N/A' }}
														</p>
														<p class="text-default d-inline-flex align-items-center mb-2"><i class="ti ti-phone text-dark me-1"></i>{{ $company->bill_phone ?? 'N/A' }}</p>
														<p class="text-default d-inline-flex align-items-center"><i class="ti ti-map-pin-pin text-dark me-1"></i>{{ $company->state->name ?? '' }}, {{ $company->city->name ?? '' }}</p>
													</div>
													<div class="d-flex align-items-center">
														@if($company->status == 'Enable')
															<span class="badge badge-pill bg-success-light me-2">Active</span>
														@else
															<span class="badge badge-pill bg-danger-light me-2">Inactive</span>
														@endif
													</div>
												</div>
												<div class="d-flex justify-content-between align-items-center flex-wrap row-gap-2 border-top pt-3 mt-3">
													<div class="avatar-list-stacked avatar-group-xs">
														<!-- Placeholder for members -->
														<span class="avatar border-0">
															<img src="{{ asset('assets/img/profiles/avatar-01.jpg') }}" class="rounded-circle" alt="img">
														</span>
													</div>
													<div class="d-flex align-items-center grid-social-links">
														<a href="#" class="avatar avatar-xs text-dark rounded-circle me-1"><i class="ti ti-mail fs-14"></i></a>
														<a href="#" class="avatar avatar-xs text-dark rounded-circle me-1"><i class="ti ti-phone-check fs-14"></i></a>
													</div>
												</div>
											</div>
										</div>
									</div>
									@endforeach
							</div>
						</div>

					</div>
				</div>

			</div>
		</div>

	</div>
</div>
		<!-- /Page Wrapper -->

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
                    <div class="tab-content">
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
                                            <option value="Enable">Active</option>
                                            <option value="Disable">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

					<div class="d-flex align-items-center justify-content-end mt-4">
						<button type="button" class="btn btn-light me-2" data-bs-dismiss="offcanvas">Cancel</button>
						<button type="submit" class="btn btn-primary" id="save-company-btn">Create Company</button>
					</div>
				</form>
			</div>
		</div>
		<!-- /Add Company -->

		<!-- Delete Stock -->
		<div class="modal fade" id="delete_contact" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Company?</h4>
							<p class="mb-0">Are you sure you want to remove it?</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="javascript:void(0);" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="javascript:void(0);" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Stock -->

    @push('scripts')
    <script>
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#' + previewId).attr('src', e.target.result).show();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function() {
            const routes = {
                store: "{{ box_route('settings.general.companies.store') }}",
                update: "{{ box_route('settings.general.companies.update') }}",
                delete: "{{ box_route('settings.general.companies.destroy') }}",
                states: "{{ box_route('company.states.select') }}",
                cities: "{{ box_route('company.city.select') }}"
            };

            // Load States
            $.post(routes.states, { _token: '{{ csrf_token() }}' }, function(data) {
                let options = '<option value=\"\">--Select--</option>';
                data.forEach(state => {
                    options += `<option value=\"${state.id}\">${state.name}</option>`;
                });
                $('#bill_state_id').html(options);
            });

            // Load Cities on State Change
            $('#bill_state_id').on('change', function() {
                const stateId = $(this).val();
                if(!stateId) {
                    $('#bill_city_id').html('<option value=\"\">--Select--</option>');
                    return;
                }
                $.post(routes.cities, { _token: '{{ csrf_token() }}', state_id: stateId }, function(data) {
                    let options = '<option value=\"\">--Select--</option>';
                    data.forEach(city => {
                        options += `<option value=\"${city.id}\">${city.name}</option>`;
                    });
                    $('#bill_city_id').html(options);
                });
            });

            // Handle Add/Edit Company
            const form = $('#add-company-form');
            let isEdit = false;
            let currentId = null;

            $(document).on('click', '.edit-company', function() {
                isEdit = true;
                const data = $(this).data('json');
                currentId = data.id;

                // Populate form
                form.find('[name=\"bill_name\"]').val(data.bill_name);
                form.find('[name=\"bill_phone\"]').val(data.bill_phone);
                form.find('[name=\"gstin\"]').val(data.gstin);
                form.find('[name=\"pan\"]').val(data.pan);
                form.find('[name=\"bill_pin_code\"]').val(data.bill_pin_code);
                form.find('[name=\"bill_address\"]').val(data.bill_address);
                form.find('[name=\"bill_state_id\"]').val(data.bill_state_id).trigger('change');
                
                // Wait for cities to load then set city
                setTimeout(() => {
                    form.find('[name=\"bill_city_id\"]').val(data.bill_city_id).trigger('change');
                }, 500);

                $('#status_select').val(data.status).trigger('change');
                
                $('#offcanvas_add h5').text('Edit Company');
                $('#save-company-btn').text('Update Company');
                $('#offcanvas_add').offcanvas('show');
            });

            $('#offcanvas_add').on('hidden.bs.offcanvas', function () {
                isEdit = false;
                currentId = null;
                form[0].reset();
                form.find('.select2').val('').trigger('change');
                $('#offcanvas_add h5').text('Add New Company');
                $('#save-company-btn').text('Create Company');
            });

            $('#save-company-btn').on('click', function(e) {
                e.preventDefault();
                const formData = new FormData(form[0]);
                if(isEdit) formData.append('id', currentId);
                formData.append('_token', '{{ csrf_token() }}');
                if(isEdit) formData.append('_method', 'PUT');

                $.ajax({
                    url: isEdit ? routes.update : routes.store,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong!');
                    }
                });
            });

            // Delete Company logic
            let deleteId = null;
            $(document).on('click', '.delete-company', function() {
                deleteId = $(this).data('id');
                $('#delete_contact').modal('show');
            });

            $('#delete_contact .btn-danger').on('click', function(e) {
                e.preventDefault();
                if(!deleteId) return;

                $.post(routes.delete, {
                    _token: '{{ csrf_token() }}',
                    id: deleteId
                }, function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                });
            });
        });
    </script>
@endpush
@endsection
