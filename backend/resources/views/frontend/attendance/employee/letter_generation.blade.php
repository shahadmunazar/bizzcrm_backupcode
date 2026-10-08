@extends('layouts.main')
@section('title', 'Letter Generation')
@section('content')



		<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-4">
									<h4 class="page-title">Letter Generation</h4>
								</div>
								<div class="col-8 text-end">
									<div class="head-icons">
										<a href="#" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
											<i class="ti ti-refresh-dot"></i>
										</a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
											<i class="ti ti-chevrons-up"></i>
										</a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Page Header -->



						<!--  <div class="card">
							<div class="card-header">

									<div class="row align-items-center">
										<div class="col-sm-4">
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
												<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-square-rounded-plus me-2"></i>Add New Campaign</a>
											</div>
										</div>
									</div>

							</div>
						</div>  -->





						<!-- Campaign Tab -->
						<div class="campaign-tab">
							<ul class="nav">
								<li class=""><a class="active" href="#bottom-justified-tab1" data-bs-toggle="tab">Generate Letter</a></li>
									<li class=""><a class="" href="#bottom-justified-tab2" data-bs-toggle="tab">Pending Letter</a></li>
									<li class=""><a class="" href="#bottom-justified-tab3" data-bs-toggle="tab">History</a></li>
									<li class=""><a class="" href="#bottom-justified-tab4" data-bs-toggle="tab">Letter Setting</a></li>
									<li class=""><a class="" href="#bottom-justified-tab5" data-bs-toggle="tab">Authorized Signatory</a></li>
									<li class=""><a class="" href="#bottom-justified-tab6" data-bs-toggle="tab">Letter Head</a></li>
									<li class=""><a class="" href="#bottom-justified-tab7" data-bs-toggle="tab">Letter Types</a></li>
							</ul>
						</div>
						<!-- Campaign Tab -->



					<div class="tab-content">
					<div class="tab-pane show active" id="bottom-justified-tab1">
						<div class="card">

							<div class="card-body">
								<div class="row">
								<div class="col-md-7 border-end">
								    <form action="{{ route('employee.letter.generate', request()->route('box_slug')) }}" method="POST" id="generate-letter-form" class="ajax-form">
										@csrf
									<div class="row">
										<div class="col-md-6">
											<div class="mb-3">
													<label class="col-form-label">Letter Type <span class="text-danger">*</span></label>
													<select class="form-control select" name="letter_type" id="letter_type_select" required>
														<option selected disabled value="">Select Letter Type</option>
														@foreach($letterTypes as $type)
															<option value="{{ $type->name }}">{{ $type->name }}</option>
														@endforeach
													</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
													<label class="col-form-label">Template Type <span class="text-danger">*</span></label>
													<select class="form-control select" name="template_id" id="template_id_select" required disabled>
														<option selected disabled value="">Select Template Type</option>
													</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
													<label class="col-form-label">Select Employee <span class="text-danger">*</span></label>
													<select class="form-control select" name="user_id" required>
														<option selected disabled value="">Select Employee</option>
														@foreach($employees as $emp)
															<option value="{{ $emp->id }}">{{ $emp->name }}</option>
														@endforeach
													</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3 mt-4 text-end">
													<button type="button" id="show_tags_button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#tags_modal"><i class="bx bx-link"></i> Map Variables</button>
											</div>
										</div>
									</div>

									<div class="row">
									    <div class="col-md-12">
										    <div class="mb-3">
												<textarea class="summernote" name="content" id="summernote_editor"></textarea>
											</div>
										</div>
									</div>
									<div class="btn-list">
										<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_template_modal">Add New Template</button>
										<button type="submit" class="btn btn-info">Generate</button>
									</div>
									</form>



								</div>
								
								<div class="col-md-5">
									<div class="card h-100 shadow-none border">
										<div class="card-header bg-light p-2">
											<h6 class="card-title mb-0 fs-14"><i class="ti ti-eye me-1"></i> Live Letter Preview</h6>
										</div>
										<div class="card-body p-3 bg-white" style="overflow-y: auto; max-height: 600px;" id="letter-preview-container">
											<div class="text-center text-muted small my-auto bg-light p-3 rounded border mx-2 mt-5" id="letter-preview-placeholder">
												<i class="ti ti-pointer fs-3 mb-2 text-primary"></i><br>
												Select a template, map variables, and choose an employee to see the preview here
											</div>
										</div>
									</div>
								</div>
								</div>

							</div>
						</div>



					</div>







						@include('frontend.attendance.employee.partials.pending_letters_tab')
						@include('frontend.attendance.employee.partials.history_letters_tab')
						@include('frontend.attendance.employee.partials.templates_tab')
						@include('frontend.attendance.employee.partials.signatories_tab')
						@include('frontend.attendance.employee.partials.letter_heads_tab')
						@include('frontend.attendance.employee.partials.letter_types_tab')













					</div>




					</div>
				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->


		<!-- /Delete Campaign -->



		<!-- Add New Signatory -->
		<div class="modal custom-modal fade" id="add_signatory" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add Authorized Signatory</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="{{ route('letter.signatory.store', request()->route('box_slug')) }}" method="POST" enctype="multipart/form-data" class="ajax-form">
							@csrf
							<div class="mb-3">
								<label class="col-form-label">Employee <span class="text-danger">*</span></label>
								<select name="user_id" class="form-control select" required>
									<option value="" selected disabled>Select Employee</option>
									@foreach($employees as $emp)
										<option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->id }})</option>
									@endforeach
								</select>
							</div>
							<div class="mb-3">
									<label class="col-form-label">Please Select File (Signature) <span class="text-danger">*</span></label>
									<div class="drag-attach">
										<input type="file" name="file" required>
										<div class="img-upload">
											<i class="ti ti-file-broken"></i>Upload Signature
										</div>
									</div>
								</div>
							<div class="mb-3">
								<label class="col-form-label">Status <span class="text-danger">*</span></label>
								<select class="form-control select" name="status" required>
									<option value="Active">Active</option>
									<option value="Inactive">Inactive</option>
								</select>
							</div>
							<div class="modal-btn text-end">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-danger">Submit</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- Add Letter Type Modal -->
		<div class="modal custom-modal fade" id="add_letter_type_modal" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add New Letter Type</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="{{ route('letter.type.store', request()->route('box_slug')) }}" method="POST" class="ajax-form">
							@csrf
							<div class="mb-3">
								<label class="col-form-label">Letter Type Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control" name="name" required>
							</div>
							<div class="mb-3">
								<label class="col-form-label">Status <span class="text-danger">*</span></label>
								<select class="form-control select" name="status" required>
									<option value="Active">Active</option>
									<option value="Inactive">Inactive</option>
								</select>
							</div>
							<div class="modal-btn text-end">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-danger">Submit</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Add Letter Type Modal -->
		<!-- Edit Letter Type Modal -->
		<div class="modal custom-modal fade" id="edit_letter_type_modal" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Edit Letter Type</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="" method="POST" class="ajax-form" id="edit_letter_type_form">
							@csrf
							<div class="mb-3">
								<label class="col-form-label">Letter Type Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control" name="name" required>
							</div>
							<div class="mb-3">
								<label class="col-form-label">Status <span class="text-danger">*</span></label>
								<select class="form-control select" name="status" required>
									<option value="Active">Active</option>
									<option value="Inactive">Inactive</option>
								</select>
							</div>
							<div class="modal-btn text-end">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-danger">Update</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Edit Letter Type Modal -->

		<!-- Add Letter Head Modal -->
		<div class="modal custom-modal fade" id="add_letter_head_modal" role="dialog">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add New Letter Head</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="{{ route('letter.head.store', request()->route('box_slug')) }}" method="POST" enctype="multipart/form-data" class="ajax-form">
							@csrf
							<div class="row">
								<div class="col-md-6 mb-3">
									<label class="col-form-label">Header Image <span class="text-danger">*</span></label>
									<div class="drag-attach">
										<input type="file" name="header_image" required>
										<div class="img-upload">
											<i class="ti ti-file-broken"></i>Upload Header Image
										</div>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<label class="col-form-label">Footer Image <span class="text-danger">*</span></label>
									<div class="drag-attach">
										<input type="file" name="footer_image" required>
										<div class="img-upload">
											<i class="ti ti-file-broken"></i>Upload Footer Image
										</div>
									</div>
								</div>
								<div class="col-md-12 mb-3">
									<label class="col-form-label">Status <span class="text-danger">*</span></label>
									<select class="form-control select" name="status" required>
										<option value="Active">Active</option>
										<option value="Inactive">Inactive</option>
									</select>
								</div>
							</div>
							<div class="modal-btn text-end">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-danger">Submit</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Add Letter Head Modal -->
		
		<!-- Edit Letter Head Modal -->
		<div class="modal custom-modal fade" id="edit_letter_head_modal" role="dialog">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Edit Letter Head</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="" method="POST" enctype="multipart/form-data" class="ajax-form">
							@csrf
							<div class="row">
								<div class="col-md-6 mb-3">
									<label class="col-form-label">Header Image</label>
									<div class="drag-attach">
										<input type="file" name="header_image">
										<div class="img-upload">
											<i class="ti ti-file-broken"></i>Upload New Header Image
										</div>
									</div>
									<div class="mt-2 text-center">
										<img id="edit_header_preview" src="" style="max-height: 80px; display: none;" class="rounded border p-1 bg-light">
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<label class="col-form-label">Footer Image</label>
									<div class="drag-attach">
										<input type="file" name="footer_image">
										<div class="img-upload">
											<i class="ti ti-file-broken"></i>Upload New Footer Image
										</div>
									</div>
									<div class="mt-2 text-center">
										<img id="edit_footer_preview" src="" style="max-height: 80px; display: none;" class="rounded border p-1 bg-light">
									</div>
								</div>
								<div class="col-md-12 mb-3">
									<label class="col-form-label">Status <span class="text-danger">*</span></label>
									<select class="form-control select" name="status" required>
										<option value="Active">Active</option>
										<option value="Inactive">Inactive</option>
									</select>
								</div>
							</div>
							<div class="modal-btn text-end">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-danger">Update</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Edit Letter Head Modal -->
		
		<!-- Edit Signatory Modal -->
		<div class="modal custom-modal fade" id="edit_signatory_modal" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Edit Authorized Signatory</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="" method="POST" enctype="multipart/form-data" class="ajax-form">
							@csrf
							<div class="mb-3">
								<label class="col-form-label">Employee <span class="text-danger">*</span></label>
								<select name="user_id" class="form-control select" required>
									<option value="" disabled>Select Employee</option>
									@foreach($employees as $emp)
										<option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->id }})</option>
									@endforeach
								</select>
							</div>
							<div class="mb-3">
								<label class="col-form-label">Please Select File (Signature)</label>
								<div class="drag-attach">
									<input type="file" name="file">
									<div class="img-upload">
										<i class="ti ti-file-broken"></i>Upload New Signature (Optional)
									</div>
								</div>
								<div class="mt-3 text-center">
									<p class="text-muted mb-1 fs-12">Current Signature:</p>
									<img id="edit_signatory_preview" src="" style="max-height: 120px; display: none;" class="rounded border bg-light p-1">
								</div>
							</div>
							<div class="mb-3">
								<label class="col-form-label">Status <span class="text-danger">*</span></label>
								<select class="form-control select" name="status" required>
									<option value="Active">Active</option>
									<option value="Inactive">Inactive</option>
								</select>
							</div>
							<div class="modal-btn text-end">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-danger">Update</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Edit Signatory Modal -->



		<!-- Tags Modal -->
		<div class="modal custom-modal fade" id="tags_modal" role="dialog">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Map Template Variables</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<p class="text-muted small mb-3">Please map the custom variables found in your template to the correct system data fields.</p>
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead class="thead-light">
								<tr>
									<th>Template Variable</th>
									<th>Map to System Data</th>
								</tr>
								</thead>
								<tbody id="tags_modal_tbody">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Add Template Modal -->
		<div class="modal custom-modal fade" id="add_template_modal" role="dialog">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add New Template</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="{{ route('letter.template.store', request()->route('box_slug')) }}" method="POST" class="ajax-form">
							@csrf
							<div class="row">
								<div class="col-md-6 mb-3">
									<label class="col-form-label">Template Name <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="template_name" required>
								</div>
								<div class="col-md-6 mb-3">
									<label class="col-form-label">Letter Type <span class="text-danger">*</span></label>
									<select class="form-control select" name="letter_type" required>
										<option selected disabled value="">Select Letter Type</option>
										@foreach($letterTypes as $type)
											<option value="{{ $type->name }}">{{ $type->name }}</option>
										@endforeach
									</select>
								</div>
								<div class="col-md-6 mb-3">
									<label class="col-form-label">Authorized Signatory <span class="text-danger">*</span></label>
									<select class="form-control select" name="signatory_id" required>
										<option selected disabled value="">Select Signatory</option>
										@foreach($signatories as $sig)
											<option value="{{ $sig->id }}">{{ $sig->user->name ?? '' }}</option>
										@endforeach
									</select>
								</div>
								<div class="col-md-6 mb-3">
									<label class="col-form-label">Status <span class="text-danger">*</span></label>
									<select class="form-control select" name="status" required>
										<option value="Active">Active</option>
										<option value="Inactive">Inactive</option>
									</select>
								</div>
								<div class="col-md-12 mb-3">
									<label class="col-form-label">Template Content <span class="text-danger">*</span></label>
									<textarea class="summernote" name="content" required></textarea>
								</div>
							</div>
							<div class="modal-btn text-end">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-danger">Save Template</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

<script>
function initLetterGeneration() {
	var letterTypeSelect = document.getElementById("letter_type_select");
	var templateIdSelect = document.getElementById("template_id_select");
	
	const systemVariables = {
		'employee_name': 'Employee Full Name',
		'first_name': 'Employee First Name',
		'last_name': 'Employee Last Name',
		'employee_id': 'Employee ID',
		'address': 'Employee Address',
		'date': 'Current Date',
		'designation': 'Employee Designation',
		'department': 'Department',
		'employee_type': 'Employee Type',
		'mobile_no': 'Mobile Number',
		'email_id': 'Email ID',
		'joining_date': 'Joining Date',
		'pan_number': 'PAN Number',
		'gender': 'Gender',
		'marital_status': 'Marital Status'
	};

	function getEditorContent() {
		try {
			var code = $('#summernote_editor').summernote('code');
			// Some summernote versions return '<p><br></p>' when empty
			return (code === '<p><br></p>') ? '' : (code || '');
		} catch(e) {}
		return $('#summernote_editor').val() || '';
	}

	function renderVariableMappers() {
		var content = getEditorContent();
		var regex = /\{\{([^}]+)\}\}/g;
		var matches = [];
		var match;
		while ((match = regex.exec(content)) !== null) {
			var tag = match[1].trim();
			if (tag.startsWith('@')) tag = tag.substring(1);
			if (!matches.includes(tag)) {
				matches.push(tag);
			}
		}

		if (matches.length > 0) {
			var tbodyHtml = '';
			matches.forEach(function(v) {
				var optionsHtml = '<option value="">-- Manual Input or Select --</option>';
				for (var key in systemVariables) {
					if (systemVariables.hasOwnProperty(key)) {
						var label = systemVariables[key];
						var selected = (v.toLowerCase() === key.toLowerCase()) ? 'selected' : '';
						optionsHtml += '<option value="' + key + '" ' + selected + '>' + label + '</option>';
					}
				}

				tbodyHtml += '<tr>' +
						'<td class="align-middle"><span class="text-primary fw-bold">@{{' + v + '}}</span></td>' +
						'<td>' +
						'<select name="variable_map[' + v + ']" class="form-select shadow-none border-primary border-opacity-25 template-variable-mapper">' +
							optionsHtml +
						'</select>' +
						'</td>' +
					'</tr>';
			});
			$('#tags_modal_tbody').html(tbodyHtml);
			if (typeof showToast === 'function') showToast('Mapped ' + matches.length + ' variables', 'success');
		} else {
			$('#tags_modal_tbody').html('<tr><td colspan="2" class="text-center text-muted">No variables found in this template.</td></tr>');
			if (typeof showToast === 'function') showToast('No variables matched. Content length: ' + content.length, 'warning');
		}
	}

	if(letterTypeSelect) {
		$(document).off("change", "#letter_type_select").on("change", "#letter_type_select", function() {
			var letterType = $(this).val();
			if (letterType) {
				var boxSlug = "{{ request()->route('box_slug') }}";
				var fetchUrl = "/" + boxSlug + "/letter-template/get?letter_type=" + encodeURIComponent(letterType);
				$.ajax({
					url: fetchUrl,
					type: "GET",
					dataType: "json",
					cache: false,
					success: function(data) {
						$(templateIdSelect).empty();
						$(templateIdSelect).append(new Option('Select Template Type', '', true, true));
						data.forEach(function(template) {
							$(templateIdSelect).append(new Option(template.template_name, template.id, false, false));
						});
						$(templateIdSelect).prop('disabled', false).trigger('change');
					},
					error: function() {
						showToast('Failed to load templates. Please try again.', 'error');
						$(templateIdSelect).prop('disabled', true).trigger('change');
					}
				});
			} else {
				$(templateIdSelect).empty().append(new Option('Select Template Type', '', true, true));
				$(templateIdSelect).prop('disabled', true).trigger('change');
			}
		});
	}

	if(templateIdSelect) {
		$(document).off("change", "#template_id_select").on("change", "#template_id_select", function() {
			var templateId = $(this).val();
			if (templateId) {
				var boxSlug = "{{ request()->route('box_slug') }}";
				var contentUrl = "/" + boxSlug + "/letter-template/content/" + templateId;
				$.ajax({
					url: contentUrl,
					type: "GET",
					dataType: "json",
					cache: false,
					success: function(data) {
						// Update summernote content safely
						if ($('#summernote_editor').next().hasClass('note-editor')) {
							$('#summernote_editor').summernote('code', data.content);
						} else {
							$('#summernote_editor').val(data.content);
						}
						renderVariableMappers();
						updateLivePreview();
					}
				});
			}
		});
	}

	function updateLivePreview() {
		var userId = $('select[name="user_id"]').val();
		var content = getEditorContent();
		var boxSlug = "{{ request()->route('box_slug') }}";
		
		if (!userId || !content || content === '<p><br></p>') {
			$('#letter-preview-container').html(`
				<div class="text-center text-muted small my-auto bg-light p-3 rounded border mx-2 mt-5" id="letter-preview-placeholder">
					<i class="ti ti-pointer fs-3 mb-2 text-primary"></i><br>
					Select a template, map variables, and choose an employee to see the preview here
				</div>
			`);
			return;
		}

		var variableMap = {};
		$('.template-variable-mapper').each(function() {
			var varName = $(this).attr('name').match(/\[(.*?)\]/)[1];
			if ($(this).val()) {
				variableMap[varName] = $(this).val();
			}
		});

		$('#letter-preview-container').html('<div class="text-center mt-5"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted small">Generating preview...</div></div>');

		$.ajax({
			url: "/" + boxSlug + "/employee-letter/preview",
			type: "POST",
			data: {
				_token: "{{ csrf_token() }}",
				user_id: userId,
				content: content,
				variable_map: variableMap
			},
			success: function(res) {
				if (res.success && res.preview_html) {
					$('#letter-preview-container').html(res.preview_html);
				}
			},
			error: function() {
				$('#letter-preview-container').html('<div class="text-danger text-center mt-5">Failed to load preview.</div>');
			}
		});
	}

	$(document).off('change', '.template-variable-mapper').on('change', '.template-variable-mapper', function() {
		updateLivePreview();
	});

	$(document).off('change', 'select[name="user_id"]').on('change', 'select[name="user_id"]', function() {
		updateLivePreview();
	});

	// Trigger preview on summernote change
	$(document).off('summernote.change', '#summernote_editor').on('summernote.change', '#summernote_editor', function(we, contents, $editable) {
		renderVariableMappers();
		clearTimeout(window.previewTimer);
		window.previewTimer = setTimeout(updateLivePreview, 500);
	});

	function showToast(message, type = 'success') {
		if (typeof toastr !== 'undefined') {
			if (type === 'success') toastr.success(message);
			if (type === 'error') toastr.error(message);
			if (type === 'warning') toastr.warning(message);
			if (type === 'info') toastr.info(message);
		} else {
			alert(message);
		}
	}
	window.showToast = showToast;

	$(document).off('click', '.delete-btn').on('click', '.delete-btn', function(e) {
		e.preventDefault();
		var link = $(this);
		if(confirm('Are you sure you want to delete this record?')) {
			$.ajax({
				url: link.attr('href'),
				type: 'GET',
				cache: false,
				success: function(response) {
					if(response.status) {
						showToast(response.message, 'success');
						refreshActiveTab(); // Note: refreshActiveTab needs to exist globally if used here.
					}
				},
				error: function(xhr) {
					showToast('An error occurred while deleting.', 'error');
				}
			});
		}
	});

}

if (typeof jQuery !== 'undefined') {
    initLetterGeneration();
} else {
    document.addEventListener("DOMContentLoaded", initLetterGeneration);
}
</script>

<script>
function initLetterGenerationPart2() {
	function loadLetterTypesData() {
		var boxSlug = "{{ request()->route('box_slug') }}";
		var url = "{{ url('') }}/" + boxSlug + "/letter-generation/api/letter-types";
		$.ajax({
			url: url,
			type: 'GET',
			cache: false,
			dataType: 'json',
			success: function(response) {
				console.log('Letter Types Data API Response:', response);
				if(response && response.status && response.data) {
					renderLetterTypesTable(response.data);
					updateLetterTypeDropdowns(response.data);
					showToast('Table instantly refreshed!', 'info');
				} else {
					console.error('Invalid response from Letter Types API', response);
				}
			},
			error: function(xhr, status, error) {
				console.error('Failed to load Letter Types Data:', error);
			}
		});
	}

	function loadLetterHeadsData() {
		var boxSlug = "{{ request()->route('box_slug') }}";
		var url = "{{ url('') }}/" + boxSlug + "/letter-generation/api/letter-heads";
		$.ajax({
			url: url,
			type: 'GET',
			cache: false,
			dataType: 'json',
			success: function(response) {
				if(response && response.status && response.data) {
					renderLetterHeadsList(response.data);
					showToast('Table instantly refreshed!', 'info');
				} else {
					console.error('Invalid response from Letter Heads API', response);
				}
			},
			error: function(xhr, status, error) {
				console.error('Failed to load Letter Heads Data:', error);
			}
		});
	}

	function loadSignatoriesData() {
		var boxSlug = "{{ request()->route('box_slug') }}";
		var url = "{{ url('') }}/" + boxSlug + "/letter-generation/api/signatories";
		$.ajax({
			url: url,
			type: 'GET',
			cache: false,
			dataType: 'json',
			success: function(response) {
				if(response && response.status && response.data) {
					renderSignatoriesTable(response.data);
					updateSignatoryDropdowns(response.data);
					showToast('Table instantly refreshed!', 'info');
				} else {
					console.error('Invalid response from Signatories API', response);
				}
			},
			error: function(xhr, status, error) {
				console.error('Failed to load Signatories Data:', error);
			}
		});
	}

	// Load the API-driven tabs instantly on page load via AJAX
	loadLetterTypesData();
	loadLetterHeadsData();
	loadSignatoriesData();

	function refreshActiveTab() {
		var activeNavLink = $('.campaign-tab .nav a.active');
		if(!activeNavLink.length) return;
		
		var tabId = activeNavLink.attr('href').replace('#', '');
		var activeTabPane = $('#' + tabId);

		var tabName = '';
		if(tabId === 'bottom-justified-tab2') tabName = 'pending_letters';
		else if(tabId === 'bottom-justified-tab3') tabName = 'history_letters';
		else if(tabId === 'bottom-justified-tab4') tabName = 'templates';
		else if(tabId === 'bottom-justified-tab5') tabName = 'signatories';
		else if(tabId === 'bottom-justified-tab6') tabName = 'letter_heads';
		else if(tabId === 'bottom-justified-tab7') tabName = 'letter_types';

		if(!tabName) return;

		if(tabName === 'letter_types') {
			loadLetterTypesData();
			return;
		} else if(tabName === 'letter_heads') {
			loadLetterHeadsData();
			return;
		} else if(tabName === 'signatories') {
			loadSignatoriesData();
			return;
		}

		var boxSlug = "{{ request()->route('box_slug') }}";
		var url = "{{ url('') }}/" + boxSlug + "/letter-generation/tab/" + tabName;

		// Show simple loading state
		activeTabPane.css('opacity', '0.5');

		$.ajax({
			url: url,
			type: 'GET',
			cache: false,
			success: function(response) {
				if(response.status) {
					// Properly hide any open modals using Bootstrap API
					var openModal = document.querySelector('.modal.show');
					if (openModal) {
						if (typeof bootstrap !== 'undefined') {
							var modalInstance = bootstrap.Modal.getOrCreateInstance(openModal);
							if (modalInstance) {
								modalInstance.hide();
							}
						}
						// Fallback if bootstrap object is missing or hide failed
						$(openModal).removeClass('show').css('display', 'none');
						$(openModal).find('[data-bs-dismiss="modal"]').click();
					}
					
					// Just to be absolutely sure for older bootstrap versions:
					try {
						$('.modal').modal('hide');
					} catch(e) {}
					
					setTimeout(function() {
						$('.modal-backdrop').remove();
						$('body').removeClass('modal-open').css({'padding-right': '', 'overflow': ''});
						
						// Legacy SSR approach for other tabs
						var tempDiv = $('<div>').html(response.html);
						var newContent = tempDiv.find('.tab-pane').length > 0 ? tempDiv.find('.tab-pane').html() : tempDiv.html();
						activeTabPane.html(newContent);
						activeTabPane.css('opacity', '1');

						// Clean up any orphaned modals left in the body by Bootstrap
						$('body > .modal').remove();

					// Reinitialize plugins if they exist
					if($('.select').length > 0) {
						$('.select').select2({
							minimumResultsForSearch: -1,
							width: '100%'
						});
					}
					if($('.summernote').length > 0) {
						$('.summernote').summernote({
							height: 300,
							minHeight: null,
							maxHeight: null,
							focus: true
						});
					}
					}, 150); // Small delay to allow modal animation to finish
				}
			},
			error: function() {
				activeTabPane.css('opacity', '1');
				showToast('Failed to refresh tab data.', 'error');
			}
		});
	}

	// CSR Render Functions
	function renderLetterTypesTable(data) {
		var tbody = $('#letter-types-tbody');
		tbody.empty();
		
		var boxSlug = "{{ request()->route('box_slug') }}";
		var baseUrl = "{{ url('') }}/" + boxSlug + "/letter-type";
		
		data.forEach(function(type) {
			var badgeClass = type.status === 'Active' ? 'badge-linesuccess' : 'badge-linedanger';
			var deleteUrl = baseUrl + "/delete/" + type.id;
			
			var tr = '<tr class="odd">' +
				'<td>' + type.name + '</td>' +
				'<td><span class="badge ' + badgeClass + '">' + type.status + '</span></td>' +
				'<td>' +
					'<div class="hstack gap-2 fs-15 text-end">' +
						'<a class="btn btn-icon btn-sm btn-light edit-letter-type-btn" href="#" ' +
						'data-bs-toggle="modal" data-bs-target="#edit_letter_type_modal" ' +
						'data-id="' + type.id + '" data-name="' + type.name + '" data-status="' + type.status + '">' +
							'<i class="ti ti-edit text-blue"></i>' +
						'</a>' +
						'<a class="btn btn-icon btn-sm btn-light ajax-delete" href="' + deleteUrl + '" data-confirm="Delete this letter type?"><i class="ti ti-trash text-danger"></i></a>' +
					'</div>' +
				'</td>' +
			'</tr>';
			tbody.append(tr);
		});
	}

	// Generic Edit Letter Type Handlers
	$(document).on('click', '.edit-letter-type-btn', function() {
		var btn = $(this);
		var id = btn.data('id');
		var name = btn.data('name');
		var status = btn.data('status');
		
		var modal = $('#edit_letter_type_modal');
		var form = modal.find('form');
		
		form.find('input[name="name"]').val(name);
		form.find('select[name="status"]').val(status).trigger('change');
		
		var actionUrl = "{{ url('') }}/" + "{{ request()->route('box_slug') }}" + "/letter-type/update/" + id;
		form.attr('action', actionUrl);
	});

	function renderLetterHeadsList(data) {
		var container = $('#letter-heads-container');
		container.empty();
		
		var boxSlug = "{{ request()->route('box_slug') }}";
		var baseUrl = "{{ url('') }}/" + boxSlug + "/letter-head";
		var storageUrl = "{{ Storage::url('') }}";
		
		data.forEach(function(lh) {
			var badgeClass = lh.status === 'Active' ? 'badge-linesuccess' : 'badge-linedanger';
			var deleteUrl = baseUrl + "/delete/" + lh.id;
			var imgUrl = storageUrl + lh.file_path;
			
			var html = '<div class="col-md-3">' +
				'<div class="card">' +
					'<img src="' + imgUrl + '" class="card-img-top" alt="Letter Head">' +
					'<div class="card-body text-center">' +
						'<span class="badge ' + badgeClass + ' d-block mb-2">' + lh.status + '</span>' +
						'<div class="hstack gap-2 justify-content-center">' +
							'<a href="#" class="btn btn-sm btn-light edit-letter-head-btn" ' +
							'data-bs-toggle="modal" data-bs-target="#edit_letter_head_modal" ' +
							'data-id="' + lh.id + '" data-status="' + lh.status + '" data-image="' + imgUrl + '">' +
								'<i class="ti ti-edit text-blue"></i>' +
							'</a>' +
							'<a href="' + deleteUrl + '" class="btn btn-sm btn-light ajax-delete" data-confirm="Delete this letter head?"><i class="ti ti-trash text-danger"></i></a>' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>';
			container.append(html);
		});
	}

	// Generic Edit Letter Head Handlers
	$(document).on('click', '.edit-letter-head-btn', function() {
		var btn = $(this);
		var id = btn.data('id');
		var status = btn.data('status');
		var imageUrl = btn.data('image');
		
		var modal = $('#edit_letter_head_modal');
		var form = modal.find('form');
		
		form.find('select[name="status"]').val(status).trigger('change');
		form.find('input[type="file"]').val(''); // Clear old file input
		
		var preview = $('#edit_letter_head_preview');
		if(imageUrl) {
			preview.attr('src', imageUrl).show();
		} else {
			preview.hide();
		}
		
		var actionUrl = "{{ url('') }}/" + "{{ request()->route('box_slug') }}" + "/letter-head/update/" + id;
		form.attr('action', actionUrl);
	});

	function renderSignatoriesTable(data) {
		var tbody = $('#signatories-tbody');
		tbody.empty();
		
		var boxSlug = "{{ request()->route('box_slug') }}";
		var baseUrl = "{{ url('') }}/" + boxSlug + "/letter-signatory";
		var storageUrl = "{{ Storage::url('') }}";
		
		data.forEach(function(sig) {
			var badgeClass = sig.status === 'Active' ? 'badge-linesuccess' : 'badge-linedanger';
			var deleteUrl = baseUrl + "/delete/" + sig.id;
			var empId = (sig.user && sig.user.id) ? sig.user.id : 'N/A';
			var empName = (sig.user && sig.user.name) ? sig.user.name : '---';
			var imgUrl = sig.signature_path ? (storageUrl + sig.signature_path) : '';
			
			var tr = '<tr class="odd">' +
				'<td class="sorting_1">' +
					'<label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label>' +
				'</td>' +
				'<td>' + empId + '</td>' +
				'<td>' + empName + '</td>' +
				'<td><span class="badge ' + badgeClass + '">' + sig.status + '</span></td>' +
				'<td>' +
					'<div class="hstack gap-2 fs-15">' +
						'<a class="btn btn-icon btn-sm btn-light edit-signatory-btn" href="#" ' +
						'data-bs-toggle="modal" data-bs-target="#edit_signatory_modal" ' +
						'data-id="' + sig.id + '" data-userid="' + sig.user_id + '" data-status="' + sig.status + '" data-image="' + imgUrl + '">' +
							'<i class="ti ti-edit text-blue"></i>' +
						'</a>' +
						'<a class="btn btn-icon btn-sm btn-light ajax-delete" href="' + deleteUrl + '" data-confirm="Are you sure?"><i class="ti ti-trash text-danger"></i></a>' +
					'</div>' +
				'</td>' +
			'</tr>';
			tbody.append(tr);
		});
	}

	// Generic Edit Signatory Handlers
	$(document).on('click', '.edit-signatory-btn', function() {
		var btn = $(this);
		var id = btn.data('id');
		var userId = btn.data('userid');
		var status = btn.data('status');
		var imageUrl = btn.data('image');
		
		var modal = $('#edit_signatory_modal');
		var form = modal.find('form');
		
		form.find('select[name="user_id"]').val(userId).trigger('change');
		form.find('select[name="status"]').val(status).trigger('change');
		form.find('input[type="file"]').val(''); // Clear old file input
		
		var preview = $('#edit_signatory_preview');
		if(imageUrl && imageUrl !== '') {
			preview.attr('src', imageUrl).show();
		} else {
			preview.hide();
		}
		
		var actionUrl = "{{ url('') }}/" + "{{ request()->route('box_slug') }}" + "/letter-signatory/update/" + id;
		form.attr('action', actionUrl);
	});

	// Global Dropdown Updaters
	function updateLetterTypeDropdowns(data) {
		var activeLetterTypes = data.filter(function(type) { return type.status === 'Active'; });
		var optionsHtml = '<option selected disabled value="">Select Letter Type</option>';
		activeLetterTypes.forEach(function(type) {
			optionsHtml += '<option value="' + type.name + '">' + type.name + '</option>';
		});

		$('select[name="letter_type"]').each(function() {
			var select = $(this);
			var currentVal = select.val();
			select.html(optionsHtml);
			if (currentVal) {
				select.val(currentVal);
			}
			select.trigger('change');
		});
	}

	function updateSignatoryDropdowns(data) {
		var optionsHtml = '<option selected disabled value="">Select Signatory</option>';
		data.forEach(function(sig) {
			var empName = (sig.user && sig.user.name) ? sig.user.name : '';
			var statusLabel = (sig.status === 'Inactive') ? ' (Inactive)' : '';
			optionsHtml += '<option value="' + sig.id + '">' + empName + statusLabel + '</option>';
		});

		$('select[name="signatory_id"]').each(function() {
			var select = $(this);
			var currentVal = select.val();
			select.html(optionsHtml);
			if (currentVal) {
				select.val(currentVal);
			}
			select.trigger('change');
		});
	}

	// AJAX Form submission
	$(document).on('submit', '.ajax-form', function(e) {
		e.preventDefault();
		var form = $(this);
		var formData = new FormData(this);
		var submitBtn = form.find('button[type="submit"]');
		var originalText = submitBtn.text();
		submitBtn.prop('disabled', true).text('Saving...');
		
		$.ajax({
			url: form.attr('action'),
			type: form.attr('method'),
			data: formData,
			processData: false,
			contentType: false,
			success: function(response) {
				submitBtn.prop('disabled', false).text(originalText);
				if(response.status) {
					// Hide the exact modal that contains this form
					var parentModal = form.closest('.modal');
					if(parentModal.length) {
						if (typeof bootstrap !== 'undefined') {
							var bsModal = bootstrap.Modal.getInstance(parentModal[0]);
							if (bsModal) bsModal.hide();
						}
						parentModal.removeClass('show').css('display', 'none');
						$('.modal-backdrop').remove();
						$('body').removeClass('modal-open').css({'padding-right': '', 'overflow': ''});
					}
					showToast(response.message, 'success');
					refreshActiveTab();
				}
			},
			error: function(xhr) {
				submitBtn.prop('disabled', false).text(originalText);
				showToast('An error occurred. Please try again.', 'error');
			}
		});
	});

	// AJAX Delete
	$(document).on('click', '.ajax-delete', function(e) {
		e.preventDefault();
		var link = $(this);
		var confirmMessage = link.data('confirm') || 'Are you sure?';
		if(confirm(confirmMessage)) {
			$.ajax({
				url: link.attr('href'),
				type: 'GET',
				cache: false,
				success: function(response) {
					if(response.status) {
						showToast(response.message, 'success');
						refreshActiveTab();
					}
				},
				error: function(xhr) {
					showToast('An error occurred while deleting.', 'error');
				}
			});
		}
	});

}
if (typeof jQuery !== 'undefined') {
    initLetterGenerationPart2();
} else {
    document.addEventListener("DOMContentLoaded", initLetterGenerationPart2);
}
</script>

@endsection
