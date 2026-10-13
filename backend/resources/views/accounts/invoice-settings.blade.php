@extends('layouts.main')
@section('title', 'Invoice Settings')
@section('content')
 
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
										<a href="invoice-settings.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

						<div class="row">
							<div class="col-md-12">

								<!-- Invoice Settings -->
								<div class="card">
									<div class="card-body">
										<h4 class="fw-semibold mb-3">Invoice Settings</h4>
										<form id="invoiceSettingsForm" action="{{ route('accounts.invoice_settings.save', ['box_slug' => request()->segment(1)]) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @if(request()->query('company_id'))
                                                <input type="hidden" name="company_id" value="{{ request()->query('company_id') }}">
                                            @endif
											<div class="row">
												<div class="col-md-6">
													<div class="mb-3">
														<h6 class="fw-medium">Invoice Logo</h6>
														<p>Upload logo of your company to display in invoice</p>
													</div>
												</div>
												<div class="col-md-6">
													<div class="mb-3">
														<div class="profile-upload">
															<div class="profile-upload-img" style="width: 250px; min-width: 250px; height: 80px;">
																<span style="display: {{ $settings->invoice_logo ? 'none' : 'flex' }};"><i class="ti ti-photo"></i></span>
																<img id="ImgPreview" src="{{ $settings->invoice_logo ? asset($settings->invoice_logo) : '' }}" alt="img" class="preview1" style="display: {{ $settings->invoice_logo ? 'block' : 'none' }}; max-width: 240px; max-height: 70px; object-fit: contain;">
																<button type="button" id="removeImage1" class="profile-remove" style="display: {{ $settings->invoice_logo ? 'block' : 'none' }};">
																	<i class="feather-x"></i>
																</button>
															</div>
															<div class="profile-upload-content">
																<label class="profile-upload-btn">
																	<i class="ti ti-file-broken"></i> Upload File
																	<input type="file" id="imag" class="input-img" accept="image/*">
																	<input type="hidden" name="invoice_logo_base64" id="invoice_logo_base64">
																</label>
																<p>Upload Logo of your company to display in invoice (JPG, PNG, WEBP).<br>
																	Max Dimensions: 400x200 pixels. Max File Size: 2MB.</p>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row mt-3">
												<div class="col-md-6">
													<div class="mb-3">
														<h6 class="fw-medium">Invoice Logo Dimensions</h6>
														<p>Set the dimensions for how the logo appears on the invoice (in pixels)</p>
													</div>
												</div>
												<div class="col-md-3">
													<div class="mb-3">
                                                        <label class="form-label">Width (px)</label>
														<input type="number" id="logo_width_input" name="logo_width" class="form-control" value="{{ old('logo_width', $settings->logo_width ?? 250) }}">
													</div>
												</div>
												<div class="col-md-3">
													<div class="mb-3">
                                                        <label class="form-label">Height (px)</label>
														<input type="number" id="logo_height_input" name="logo_height" class="form-control" value="{{ old('logo_height', $settings->logo_height ?? 80) }}">
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<div class="mb-3">
														<h6 class="fw-medium">Invoice Prefix</h6>
														<p>Add prefix to your invoice</p>
													</div>
												</div>
												<div class="col-md-4">
													<div class="mb-3">
														<input type="text" name="invoice_prefix" class="form-control" value="{{ old('invoice_prefix', $settings->invoice_prefix) }}">
													</div>
												</div>
											</div>
                                            
                                            <div class="row">
												<div class="col-md-6">
													<div class="mb-3">
														<h6 class="fw-medium">Financial Year</h6>
														<p>Set default financial year range for invoices</p>
													</div>
												</div>
												<div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-6 mb-3">
                                                            <input type="date" name="from_date" class="form-control" value="{{ old('from_date', $settings->from_date ? \Carbon\Carbon::parse($settings->from_date)->format('Y-m-d') : '') }}" placeholder="From Date">
                                                        </div>
                                                        <div class="col-6 mb-3">
                                                            <input type="date" name="to_date" class="form-control" value="{{ old('to_date', $settings->to_date ? \Carbon\Carbon::parse($settings->to_date)->format('Y-m-d') : '') }}" placeholder="To Date">
                                                        </div>
                                                    </div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<div class="mb-3">
														<h6 class="fw-medium">Invoice Due</h6>
														<p>Select due date to display in invoice</p>
													</div>
												</div>
												<div class="col-md-4">
													<div class="mb-3">
														<div class="d-flex align-items-center inv-days">
															<div class="select-drop">
																<select name="invoice_due_days" class="select">
																	<option value="5" {{ old('invoice_due_days', $settings->invoice_due_days) == 5 ? 'selected' : '' }}>5</option>
																	<option value="7" {{ old('invoice_due_days', $settings->invoice_due_days) == 7 ? 'selected' : '' }}>7</option>
																	<option value="10" {{ old('invoice_due_days', $settings->invoice_due_days) == 10 ? 'selected' : '' }}>10</option>
																	<option value="15" {{ old('invoice_due_days', $settings->invoice_due_days) == 15 ? 'selected' : '' }}>15</option>
																	<option value="30" {{ old('invoice_due_days', $settings->invoice_due_days) == 30 ? 'selected' : '' }}>30</option>
																</select>
															</div>
															<p>Days</p>
														</div>
													</div>
												</div>
											</div>
											<div class="row align-items-center">
												<div class="col-md-6">
													<div class="mb-3">
														<h6 class="fw-medium">Invoice Round Off</h6>
														<p>Value roundoff in invoice</p>
													</div>
												</div>
												<div class="col-md-4">
													<div class="mb-3">
														<div class="d-flex align-items-center">
															<div class="form-check form-switch me-2">
																<input name="enable_round_off" value="1" class="form-check-input" type="checkbox" role="switch" {{ old('enable_round_off', isset($settings->enable_round_off) ? $settings->enable_round_off : true) ? 'checked' : '' }}>
															</div>
															<div class="select-drop w-100">
																<select name="round_off_type" class="select">
																	<option value="Up" {{ old('round_off_type', $settings->round_off_type) == 'Up' ? 'selected' : '' }}>Roundoff Up</option>
																	<option value="Down" {{ old('round_off_type', $settings->round_off_type) == 'Down' ? 'selected' : '' }}>Roundoff Down</option>
																</select>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row align-items-center">
												<div class="col-md-6">
													<div class="mb-3">
														<h6 class="fw-medium">Show Company Details</h6>
														<p>Show/hide company details in invoice</p>
													</div>
												</div>
												<div class="col-md-6">
													<div class="mb-3">
														<div class="form-check form-switch">
															<input name="show_company_details" value="1" class="form-check-input" type="checkbox" role="switch" {{ old('show_company_details', $settings->show_company_details) ? 'checked' : '' }}>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<div class="mb-3">
														<h6 class="fw-medium">Invoice Header Terms</h6>
													</div>
												</div>
												<div class="col-md-6">
													<div class="mb-3">
														<textarea name="header_terms" class="summernote">{{ old('header_terms', $settings->header_terms) }}</textarea>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<div class="mb-3">
														<h6 class="fw-medium">Invoice Footer Terms</h6>
													</div>
												</div>
												<div class="col-md-6">
													<div class="mb-3">
														<textarea name="footer_terms" class="summernote">{{ old('footer_terms', $settings->footer_terms) }}</textarea>
													</div>
												</div>
												</div>
											</div>

											<div class="row border-bottom mb-4 pb-4">
												<div class="col-md-4">
													<div class="mb-3">
														<h6 class="fw-medium">Invoice Template Style</h6>
													</div>
												</div>
												<div class="col-md-6">
													<div class="mb-3">
														<select name="template_style" class="form-control select2">
															<option value="template_1" {{ (old('template_style', $settings->template_style) == 'template_1') ? 'selected' : '' }}>Template 1 (Tax Invoice)</option>
															<option value="template_2" {{ (old('template_style', $settings->template_style) == 'template_2') ? 'selected' : '' }}>Template 2 (Challan / Credit)</option>
															<option value="template_3" {{ (old('template_style', $settings->template_style) == 'template_3') ? 'selected' : '' }}>Template 3 (Purchase Order)</option>
														</select>
													</div>
												</div>
											</div>
											<div>
												<a href="#" class="btn btn-light me-2">Cancel</a>
												<button type="submit" class="btn btn-primary">Save Changes</button>
												<button type="button" class="btn btn-info ms-2 text-white" id="previewTemplateBtn"><i class="ti ti-eye me-1"></i> Preview Template</button>
											</div>
										</form>
									</div>
								</div>
								<!-- /Invoice Settings -->

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Page Wrapper -->
<!-- Cropper Modal -->
<div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crop Logo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="img-container" style="max-height: 500px; display: inline-block;">
                    <img id="imageToCrop" src="" alt="Picture" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="cropAndSaveBtn">Crop & Save</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
$(document).ready(function() {
    var cropper;
    var image = document.getElementById('imageToCrop');

    // Dynamically update preview box size
    function updatePreviewSize() {
        var w = $('#logo_width_input').val() || 250;
        var h = $('#logo_height_input').val() || 80;
        $('.profile-upload-img').css({
            'width': w + 'px',
            'min-width': w + 'px',
            'height': h + 'px'
        });
        $('#ImgPreview').css({
            'max-width': (w-10) + 'px',
            'max-height': (h-10) + 'px'
        });
    }
    
    $('#logo_width_input, #logo_height_input').on('input', updatePreviewSize);
    updatePreviewSize();

    // Show image preview and cropper modal
    $('#imag').on('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imageToCrop').attr('src', e.target.result);
                $('#cropperModal').modal('show');
            }
            reader.readAsDataURL(file);
            $(this).val(''); // Reset input so same file can be selected again
        }
    });

    $('#cropperModal').on('shown.bs.modal', function () {
        var w = parseInt($('#logo_width_input').val()) || 250;
        var h = parseInt($('#logo_height_input').val()) || 80;
        cropper = new Cropper(image, {
            aspectRatio: w / h,
            viewMode: 1,
            autoCropArea: 1,
        });
    }).on('hidden.bs.modal', function () {
        if(cropper) {
            cropper.destroy();
            cropper = null;
        }
    });

    $('#cropAndSaveBtn').on('click', function() {
        if (!cropper) return;
        
        var canvas = cropper.getCroppedCanvas();
        var base64 = canvas.toDataURL('image/png');
        
        $('#invoice_logo_base64').val(base64);
        $('#ImgPreview').attr('src', base64).show();
        $('.profile-upload-img span').hide();
        $('#removeImage1').show();
        
        $('#cropperModal').modal('hide');
        
        // Trigger form submit to save via AJAX instantly
        $('#invoiceSettingsForm').trigger('submit');
    });


    // Remove image preview
    $('#removeImage1').on('click', function() {
        $('#ImgPreview').attr('src', '').hide();
        $('.profile-upload-img span').show();
        $('#removeImage1').hide();
        $('#invoice_logo_base64').val('');
        $('#imag').val('');
    });

    // Preview Template
    $('#previewTemplateBtn').on('click', function() {
        var style = $('select[name="template_style"]').val();
        if(!style) style = 'template_1';
        var previewUrl = "{{ route('accounts.invoice_templates.preview', ['box_slug' => request()->segment(1), 'style' => 'STYLE_PLACEHOLDER']) }}";
        previewUrl = previewUrl.replace('STYLE_PLACEHOLDER', style);
        window.open(previewUrl, '_blank');
    });

    // Handle full form submission via AJAX
    $('#invoiceSettingsForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(form[0]);
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...').prop('disabled', true);
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Settings updated successfully!');
                    } else {
                        alert(response.message || 'Settings updated successfully!');
                    }
                }
                submitBtn.html(originalText).prop('disabled', false);
            },
            error: function(xhr) {
                var message = 'Error saving settings.';
                if(xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors)[0][0];
                } else if(xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                if (typeof toastr !== 'undefined') {
                    toastr.error(message);
                } else {
                    alert(message);
                }
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });
});
</script>
@endsection
