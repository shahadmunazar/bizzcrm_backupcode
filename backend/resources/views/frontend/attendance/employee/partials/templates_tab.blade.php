<div class="tab-pane" id="bottom-justified-tab4">
							<div class="card">

							<div class="card-body">
								<!-- Filter -->
								<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<h4 class="page-title">Letter Setting</h4>
									</div>

									<div class="col-sm-4">
											<div class="icon-form mb-3 mb-sm-0">
												<span class="form-icon"><i class="ti ti-search"></i></span>
												<input type="text" class="form-control" placeholder="Search...">
											</div>
										</div>
								</div>
								<!-- /Filter -->

								<!-- Campaign List -->
								<div class="table-responsive custom-table">
									<table class="table" id="">
										<thead class="thead-light">
										<tr>
												<th class="no-sort">
													<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
												</th>
												<th>Letter Type</th>
												<th>Template Name</th>
												<th>Authorized Signatory</th>
												<th>Status</th>
												<th class="text-start">Action</th>
										</tr>
										</thead>
										<tbody>
										@foreach($templates as $template)
										<tr class="odd">
											<td class="sorting_1">
												<label class="checkboxs">
													<input type="checkbox"><span class="checkmarks"></span></label>
											</td>
											<td>{{ $template->letter_type }}</td>
											<td>{{ $template->template_name }}</td>
											<td>{{ $template->signatory->user->name ?? '---' }}</td>
											<td>
												<span class="badge badge-linesuccess {{ $template->status == 'Active' ? 'badge-linesuccess' : 'badge-linedanger' }}">{{ $template->status }}</span>
											</td>
											<td>
												<div class="hstack gap-2 fs-15">
													<a class="btn btn-icon btn-sm btn-light" href="#" data-bs-toggle="modal" data-bs-target="#edit_template_{{ $template->id }}"><i class="ti ti-edit text-blue"></i></a>
												</div>
											</td>
										</tr>

											@endforeach
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
								<!-- /Campaign List -->

							</div>
						</div>

						<!-- Edit Template Modals -->
						@foreach($templates as $template)
						<div class="modal custom-modal fade" id="edit_template_{{ $template->id }}" role="dialog">
							<div class="modal-dialog modal-dialog-centered modal-lg">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title">Edit Template</h5>
										<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
											<i class="ti ti-x"></i>
										</button>
									</div>
									<div class="modal-body">
										<form action="{{ route('letter.template.update', ['box_slug' => request()->route('box_slug'), 'id' => $template->id]) }}" method="POST" class="ajax-form">
											@csrf
											<div class="row">
												<div class="col-md-6 mb-3">
													<label class="col-form-label">Template Name <span class="text-danger">*</span></label>
													<input type="text" class="form-control" name="template_name" value="{{ $template->template_name }}" required>
												</div>
												<div class="col-md-6 mb-3">
													<label class="col-form-label">Letter Type <span class="text-danger">*</span></label>
													<select class="form-control select" name="letter_type" required>
														<option disabled value="">Select Letter Type</option>
														@foreach($letterTypes as $type)
															<option value="{{ $type->name }}" {{ $template->letter_type == $type->name ? 'selected' : '' }}>{{ $type->name }}</option>
														@endforeach
													</select>
												</div>
												<div class="col-md-6 mb-3">
													<label class="col-form-label">Authorized Signatory <span class="text-danger">*</span></label>
													<select class="form-control select" name="signatory_id" required>
														<option disabled value="">Select Signatory</option>
														@foreach($signatories as $sig)
															<option value="{{ $sig->id }}" {{ $template->signatory_id == $sig->id ? 'selected' : '' }}>{{ $sig->user->name ?? '' }}</option>
														@endforeach
													</select>
												</div>
												<div class="col-md-6 mb-3">
													<label class="col-form-label">Status <span class="text-danger">*</span></label>
													<select class="form-control select" name="status" required>
														<option value="Active" {{ $template->status == 'Active' ? 'selected' : '' }}>Active</option>
														<option value="Inactive" {{ $template->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
													</select>
												</div>
												<div class="col-md-12 mb-3">
													<label class="col-form-label">Template Content <span class="text-danger">*</span></label>
													<textarea class="summernote" name="content" required>{{ $template->content }}</textarea>
												</div>
											</div>
											<div class="modal-btn text-end">
												<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
												<button type="submit" class="btn btn-danger">Update Template</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
						@endforeach
						<!-- /Edit Template Modals --></div>
