@extends('layouts.main')
@section('title', 'New Template Message')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <h4 class="page-title">New Template Message</h4>
                        </div>
                        <div class="col-8 text-end">
                            <div class="head-icons">
                                <a href="{{ box_route('whatsapp.create_template') }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Back to Templates">
                                    <i class="ti ti-arrow-left"></i>
                                </a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
                                    <i class="ti ti-chevrons-up"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-8">
                                <form id="createTemplateForm">
                                    @csrf
                                    <input type="hidden" name="template_id" value="{{ $template->id ?? '' }}">
                                    <div class="row">
                                        <div class="col-md-12 wapp-template-message">
                                            
                                            <div class="mb-4">
                                                <label class="col-form-label fw-700">WhatsApp Account <span class="text-danger">*</span></label>
                                                <p class="fs-13 mb-2">Select the account to register this template with.</p>
                                                <select class="form-control" name="waba_id" id="waba_account" required>
                                                    <option value="">Select Account</option>
                                                    @foreach($accounts as $account)
                                                        <option value="{{ $account->waba_id }}" 
                                                                {{ (isset($template) && $template->waba_id == $account->waba_id) ? 'selected' : '' }}
                                                                data-numbers="{{ json_encode($account->phoneNumbers) }}">
                                                            {{ $account->name }} ({{ $account->waba_id }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-4">
                                                <label class="col-form-label fw-700">WhatsApp Number <span class="text-danger">*</span></label>
                                                <p class="fs-13 mb-2">Select the phone number to associate with this template.</p>
                                                <select class="form-control" name="phone_number_id" id="waba_number" required>
                                                    <option value="">Select Number</option>
                                                </select>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-4">
                                                        <label class="col-form-label fw-700">Template Category <span class="text-danger">*</span></label>
                                                        <p class="fs-13 mb-2">Your template should fall under one of these categories.</p>
                                                        <select class="form-control templateCategory" name="category" required {{ (isset($template) && $template->whats_app_id) ? 'disabled' : '' }}>
                                                            <option value="">Select Template Category</option>
                                                            <option value="UTILITY" {{ (isset($template) && $template->category == 'UTILITY') ? 'selected' : '' }}>Utility</option>
                                                            <option value="MARKETING" {{ (isset($template) && $template->category == 'MARKETING') ? 'selected' : '' }}>Marketing</option>
                                                            <option value="AUTHENTICATION" {{ (isset($template) && $template->category == 'AUTHENTICATION') ? 'selected' : '' }}>Authentication</option>
                                                        </select>
                                                        @if(isset($template) && $template->whats_app_id)
                                                            <input type="hidden" name="category" value="{{ $template->category }}">
                                                            <small class="text-warning"><i class="ti ti-info-circle"></i> Category is locked for live templates.</small>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-4">
                                                        <label class="col-form-label">Template Language <span class="text-danger">*</span></label>
                                                        <p class="fs-13 mb-2">Specify the language for this message template.</p>
                                                        <select class="form-control" name="language" required {{ (isset($template) && $template->whats_app_id) ? 'disabled' : '' }}>
                                                            <option value="">Select Template Language</option>
                                                            @foreach([['en_US','English (US)'],['en_GB','English (UK)'],['hi','Hindi'],['ar','Arabic'],['es','Spanish'],['fr','French'],['pt_BR','Portuguese (Brazil)']] as $l)
                                                                <option value="{{ $l[0] }}" {{ (isset($template) && $template->language == $l[0]) ? 'selected' : '' }}>{{ $l[1] }}</option>
                                                            @endforeach
                                                        </select>
                                                        @if(isset($template) && $template->whats_app_id)
                                                            <input type="hidden" name="language" value="{{ $template->language }}">
                                                            <small class="text-warning"><i class="ti ti-info-circle"></i> Language is locked for live templates.</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-4 marketingSection" style="display: none;">
                                                <div class="border border-1 border-dashed p-3">

                                                    <div class="d-flex align-items-center flex-wrap mb-3">
																	    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#1ECBE2" class="bi bi-stars" viewBox="0 0 16 16">
																		  <path d="M7.657 6.247c.11-.33.576-.33.686 0l.645 1.937a2.89 2.89 0 0 0 1.829 1.828l1.936.645c.33.11.33.576 0 .686l-1.937.645a2.89 2.89 0 0 0-1.828 1.829l-.645 1.936a.361.361 0 0 1-.686 0l-.645-1.937a2.89 2.89 0 0 0-1.828-1.828l-1.937-.645a.361.361 0 0 1 0-.686l1.937-.645a2.89 2.89 0 0 0 1.828-1.828zM3.794 1.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387A1.73 1.73 0 0 0 4.593 5.69l-.387 1.162a.217.217 0 0 1-.412 0L3.407 5.69A1.73 1.73 0 0 0 2.31 4.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387A1.73 1.73 0 0 0 3.407 2.31zM10.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.16 1.16 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.16 1.16 0 0 0-.732-.732L9.1 2.137a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732z"/>
																		</svg>
																		<div class="aiheading ms-3">
																		    <h5 class="mb-1">Generate with AI</h5>
																			<p class="fs-13 mb-0">Create cutomized variations with AI</p>
																		</div>
																	</div>


                                                    <div class="d-flex align-items-center flex-wrap justify-content-between mb-3">
                                                    <label class="col-form-label pb-0">Write your prompt</label>
                                                    <button type="button" class="btn btn-light"><i class="fa-solid fa-arrow-rotate-right"></i> Previous prompts</button>
                                                    </div>
                                                    <textarea id="ai_prompt" class="form-control" rows="4" placeholder="eg. Please generate a promotional message to give a discount of 20% off on our product line." maxlength="500"></textarea>
                                                    <div class="text-end mt-1">
                                                        <small class="text-muted"><span id="ai_prompt_count">0</span> / 500</small>
                                                    </div>
                                                    
                                                    <div class="mb-4 mt-3">
                                                        <label class="col-form-label">Choose your message style:</label>
                                                        <div class="btn-list style-selector">
                                                            <button type="button" class="btn btn-outline-dark btn-sm active" data-style="Normal">😀 Normal</button>
                                                            <button type="button" class="btn btn-outline-dark btn-sm" data-style="Poetic">✍🏼 Poetic</button>
                                                            <button type="button" class="btn btn-outline-dark btn-sm" data-style="Exciting">🤩 Exciting</button>
                                                            <button type="button" class="btn btn-outline-dark btn-sm" data-style="Funny">😜 Funny</button>
                                                        </div>
                                                    </div>
                                                    <div class="mb-4 mt-3">
                                                        <label class="col-form-label">Optimize your message for:</label>
                                                        <div class="btn-list optimize-selector">
                                                            <button type="button" class="btn btn-outline-dark btn-sm" data-opt="Click Rate"><i class="fa-solid fa-arrow-pointer"></i> Click Rate</button>
                                                            <button type="button" class="btn btn-outline-dark btn-sm" data-opt="Reply Rate"><i class="fa-solid fa-reply"></i> Reply Rate</button>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="btn-list d-block">
                                                        <div class="d-grid gap-2 mb-4">
                                                            <button type="button" id="btnGenerateAI" class="btn btn-success" {{ $creditsLeft <= 0 ? 'disabled' : '' }}>
                                                                <i class="ti ti-sparkles me-1"></i> Generate (<span id="credits_count">{{ max(0, $creditsLeft) }}</span> free generations left)
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="aigeneratedtemplates mb-4" style="display: none;">
                                                        <div class="row" id="ai_results_container">
                                                            <!-- AI Suggestions will appear here -->
                                                        </div>
                                                    </div>







                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                 <label class="col-form-label">Template Name<span class="text-danger"> *</span></label>
                                                <p class="fs-13 mb-2">Name can only be in lowercase alphanumeric characters and underscores. Special characters and white-space are not allowed <br>e.g. - app_verification_code</p>
                                                <input class="form-control" type="text" name="name" placeholder="Enter Name" maxlength="512" value="{{ $template->name ?? '' }}" required {{ (isset($template) && $template->whats_app_id) ? 'readonly' : '' }}>
                                                @if(isset($template) && $template->whats_app_id)
                                                    <small class="text-warning"><i class="ti ti-info-circle"></i> Template name cannot be changed for live templates.</small>
                                                @endif
                                                <div class="text-end mt-1">
                                                    <small class="char-count text-muted" data-target="name">0 / 512</small>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="col-form-label">Template Type <span class="text-danger">*</span></label>
                                                <p class="fs-13 mb-2">Specify the type of message.</p>
                                                <select class="form-control messageType" name="message_type">
                                                    <option value="">Select Message Type</option>
																	<option value="Text">Text (Default)</option>
																	<option value="Image">Image</option>
																	<option value="Video">Video</option>
																	<option value="Document">Document</option>
																	<option value="Location">Location</option>
                                                </select>

                                            </div>
                                            <div class="mb-4 textSection" style="display: none;">
                                                <label class="col-form-label">Template Header Text (Optional)</label>
                                                <p class="fs-13 mb-2">Header text is optional and only upto 60 characters are allowed.</p>
                                                @php
                                                    $headerText = '';
                                                    if (isset($template) && is_array($template->components)) {
                                                        foreach ($template->components as $comp) {
                                                            if ($comp['type'] === 'HEADER' && isset($comp['text'])) {
                                                                $headerText = $comp['text'];
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <input class="form-control" type="text" name="header_text" placeholder="Enter header text here" maxlength="60" value="{{ $headerText }}">
                                                <div class="text-end mt-1">
                                                    <small class="char-count text-muted" data-target="header_text">0 / 60</small>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="col-form-label">Template Format <span class="text-danger">*</span></label>
                                                <p class="fs-13 mb-2">Use text formatting - *bold* , _italic_ & ~strikethrough~<br>
                                                Your message content. Upto 1024 characters are allowed.<br>
                                                e.g. - Hello @{{1}}, your code will expire in @{{2}} mins.</p>
                                                @php
                                                    $bodyText = '';
                                                    if (isset($template) && is_array($template->components)) {
                                                        foreach ($template->components as $comp) {
                                                            if ($comp['type'] === 'BODY' && isset($comp['text'])) {
                                                                $bodyText = $comp['text'];
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <textarea class="form-control" name="body_text" rows="4" placeholder="Enter Your Message..." maxlength="1024" required>{{ $bodyText }}</textarea>
                                                <div class="text-end mt-1">
                                                    <small class="char-count text-muted" data-target="body_text">0 / 1024</small>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="col-form-label">Template Footer (Optional)</label>
                                                <p class="fs-13 mb-2">Your message content. Upto 60 characters are allowed.</p>
                                                @php
                                                    $footerText = '';
                                                    if (isset($template) && is_array($template->components)) {
                                                        foreach ($template->components as $comp) {
                                                            if ($comp['type'] === 'FOOTER' && isset($comp['text'])) {
                                                                $footerText = $comp['text'];
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <input class="form-control" type="text" name="footer_text" placeholder="Enter footer text here" maxlength="60" value="{{ $footerText }}">
                                                <div class="text-end mt-1">
                                                    <small class="char-count text-muted" data-target="footer_text">0 / 60</small>
                                                </div>
                                            </div>        </div>

                                            <div class="col-lg-12 col-md-12">
                                                <div class="card-body d-sm-flex align-items-center ps-0">
                                                    <div class="form-check me-3">
                                                        <input class="form-check-input" type="radio" name="Buttons" id="BtnNone" checked>
                                                        <label class="form-check-label" for="BtnNone">None</label>
                                                    </div>
                                                    <div class="form-check me-3">
                                                        <input class="form-check-input radio-cta" type="radio" name="Buttons" id="BtnCTA">
                                                        <label class="form-check-label" for="BtnCTA">Call to Actions</label>
                                                    </div>
                                                    <div class="form-check me-3">
                                                        <input class="form-check-input radio-quick" type="radio" name="Buttons" id="BtnQR">
                                                        <label class="form-check-label" for="BtnQR">Quick Replies</label>
                                                    </div>
                                                </div>
                                            </div>



                                                       <div class="col-md-12 cta-section" style="display: none;">
															<div class="mb-3 row align-items-center">
																<label class="col-md-2">Call to Action 1 :</label>
																<div class="col-md-3">
																	<select class="form-control">
																		<option value="">Select Action Type</option>
																		<option value="Phone Number">Phone Number</option>
																		<option value="URL">URL</option>
																	</select>
																</div>
																<div class="col-md-3">
																	<input type="text" class="form-control" placeholder="Button Title">
																</div>
																<div class="col-md-3">
																	<input type="text" class="form-control" placeholder="Button Value">
																</div>
																<div class="col-md-1">
																	<i class="fa-solid fs-20 fa-xmark"></i>
																</div>
																<div class="btn-list mt-3">
																	<button type="button" class="btn btn-outline-dark btn-md">
																		<i class="fa-solid fa-plus"></i> Call to Action
																	</button>
																</div>
															</div>
														</div>
														
														<div class="col-md-12 quick-section" style="display: none;">
															<div class="mb-3 row align-items-center">
																<label class="col-md-2">Quick Reply 1 :</label>
																<div class="col-md-5">
																	<input type="text" class="form-control" placeholder="Button Title">
																</div>
																<div class="col-md-1">
																	<i class="fa-solid fs-20 fa-xmark"></i>
																</div>

																<div class="btn-list mt-3">
																	<button type="button" class="btn btn-outline-dark btn-md">
																		<i class="fa-solid fa-plus"></i> Quick Reply
																	</button>
																</div>
															</div>
														</div>










                                        </div>
                                    
                                    <div class="d-flex align-items-center justify-content-end mt-3 gap-2">
                                        <button type="button" class="btn btn-outline-primary" id="btnDraft">Save as Draft</button>
                                        <button type="submit" class="btn btn-primary" id="btnSubmit">Submit to Meta</button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="col-sm-4">
                                <div class="mb-3 sticky-top" style="top: 20px;">
                                    <label class="col-form-label fw-bold">Template Preview </label>
                                    <p class="fs-13 mb-3 text-muted">Real-time preview of your WhatsApp message.</p>    
                                    
                                    <div class="whatsapp-preview-container rounded-4 shadow-sm overflow-hidden border mb-3" style="max-width: 320px;">
                                        <div class="preview-header bg-dark p-2 d-flex justify-content-between align-items-center" style="position: relative; height: 50px;">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-chevron-left text-white me-2"></i>
                                                <div class="avatar avatar-sm rounded-circle bg-light me-2">
                                                    <i class="ti ti-brand-whatsapp text-success"></i>
                                                </div>
                                                <h6 class="text-white mb-0 fs-13">WhatsApp Business</h6>
                                            </div>
                                            <div class="text-white">
                                                <i class="ti ti-video me-2 fs-14"></i>
                                                <i class="ti ti-phone me-2 fs-14"></i>
                                                <i class="ti ti-dots-vertical fs-14"></i>
                                            </div>
                                        </div>
                                        
                                        <div class="preview-body p-3" style="background-color: #e5ddd5; min-height: 300px; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-size: cover;">
                                            <div class="preview-bubble bg-white rounded-3 shadow-sm p-0 overflow-hidden" style="position: relative; max-width: 90%;">
                                                <!-- Media Preview Placeholder -->
                                                <div id="previewMedia" class="bg-light border-bottom d-none" style="height: 150px; position: relative;">
                                                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                                                        <i class="ti ti-photo fs-30 mb-2" id="mediaIcon"></i>
                                                        <span class="small" id="mediaPlaceholder">Media Header</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="p-2 px-3">
                                                    <div id="previewHeader" class="fw-bold mb-1 fs-14 text-dark" style="word-break: break-all;"></div>
                                                    <div id="previewBody" class="fs-14 text-dark" style="white-space: pre-wrap; word-break: break-all;">Enter your message body...</div>
                                                    <div id="previewFooter" class="text-muted fs-11 mt-1" style="word-break: break-all;"></div>
                                                    
                                                    <div class="text-end mt-1">
                                                        <small class="text-muted" style="font-size: 10px;">{{ now()->format('H:i') }} <i class="ti ti-check-double text-info ms-1"></i></small>
                                                    </div>
                                                </div>

                                                <!-- Buttons Preview -->
                                                <div id="previewButtons" class="border-top d-none">
                                                    <div class="d-flex flex-column">
                                                        <!-- Buttons will be added here via JS -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-3 small text-muted"><i class="ti ti-info-circle me-1"></i> This is a graphical representation. Actual presentation might vary slightly on different devices.</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Trigger initial updates if editing
    $('input[name="name"], input[name="header_text"], textarea[name="body_text"], input[name="footer_text"]').each(function() {
        if ($(this).val()) {
            $(this).trigger('input');
        }
    });

    // Change handler for Account Select
    $("#waba_account").on("change", function () {
        let selectedOption = $(this).find('option:selected');
        let numbers = selectedOption.data('numbers') || [];
        let numberSelect = $("#waba_number");
        
        numberSelect.empty().append('<option value="">Select Number</option>');
        
        let savedNumber = "{{ $template->whatsapp_number_id ?? '' }}";
        
        numbers.forEach(num => {
            let selected = (savedNumber == num.whatsapp_id) ? 'selected' : '';
            let label = num.display_phone_number;
            if (num.name_status) {
                label += ` (${num.name_status})`;
            }
            numberSelect.append(`<option value="${num.whatsapp_id}" ${selected}>${label}</option>`);
        });
    });

    

    // Initial Trigger on Load
    if ($("#waba_account").val()) {
        $("#waba_account").trigger('change');
    }

    // Toggle Marketing Section
    $(".templateCategory").on("change", function () {
        let selectedValue = $(this).val();
        if (selectedValue === "MARKETING") {
            $(".marketingSection").slideDown();
        } else {
            $(".marketingSection").slideUp();
        }
    });

    // Character Counter Logic
    function updateCharCount(element) {
        let name = $(element).attr('name');
        let count = $(element).val().length;
        let limit = $(element).attr('maxlength');
        let counter = $(`.char-count[data-target="${name}"]`);
        
        counter.text(`${count} / ${limit}`);
        
        if (count >= limit) {
            counter.removeClass('text-muted').addClass('text-danger fw-bold');
        } else {
            counter.removeClass('text-danger fw-bold').addClass('text-muted');
        }
    }

    // Bind counters and preview to all relevant inputs
    $('input[name="header_text"], textarea[name="body_text"], input[name="footer_text"]').on('input', function() {
        updateCharCount(this);
        updatePreview();
    });
    
    $('input[name="name"]').on('input', function() {
        updateCharCount(this);
    });

    function updatePreview() {
        let headerText = $('input[name="header_text"]').val();
        let bodyText = $('textarea[name="body_text"]').val() || 'Enter your message body...';
        let footerText = $('input[name="footer_text"]').val();
        let type = $('.messageType').val();

        // Header Update
        if (type === 'Text' && headerText) {
            $('#previewHeader').text(headerText).removeClass('d-none');
        } else {
            $('#previewHeader').addClass('d-none');
        }

        // Body Update (with basic formatting)
        let formattedBody = bodyText
            .replace(/\*(.*?)\*/g, '<b>$1</b>')
            .replace(/_(.*?)_/g, '<i>$1</i>')
            .replace(/~(.*?)~/g, '<strike>$1</strike>')
            .replace(/\n/g, '<br>');
        $('#previewBody').html(formattedBody);

        // Footer Update
        if (footerText) {
            $('#previewFooter').text(footerText).removeClass('d-none');
        } else {
            $('#previewFooter').addClass('d-none');
        }

        // Media Preview Logic
        if (['Image', 'Video', 'Document'].includes(type)) {
            $('#previewMedia').removeClass('d-none');
            $('#mediaPlaceholder').text(type + ' Header');
            let icon = 'ti-photo';
            if (type === 'Video') icon = 'ti-video';
            if (type === 'Document') icon = 'ti-file-text';
            $('#mediaIcon').attr('class', 'ti ' + icon + ' fs-30 mb-2');
        } else {
            $('#previewMedia').addClass('d-none');
        }
    }

    // Handle existing template data
    function initExistingTemplate() {
        let type = 'Text';
        @if(isset($template) && is_array($template->components))
            @foreach($template->components as $comp)
                @if($comp['type'] === 'HEADER')
                    @php 
                        $fmt = $comp['format'] ?? 'TEXT';
                        $t = 'Text';
                        if($fmt == 'IMAGE') $t = 'Image';
                        else if($fmt == 'VIDEO') $t = 'Video';
                        else if($fmt == 'DOCUMENT') $t = 'Document';
                        else if($fmt == 'LOCATION') $t = 'Location';
                    @endphp
                    type = "{{ $t }}";
                @endif
            @endforeach
        @endif
        
        $('.messageType').val(type).trigger('change');
        
        // Initial trigger for preview
        updatePreview();
    }

    initExistingTemplate();


    // Form Submission
    $('#createTemplateForm').on('submit', function(e) {
        e.preventDefault();
        submitTemplate(false);
    });

    $('#btnDraft').on('click', function() {
        submitTemplate(true);
    });

    function submitTemplate(isDraft) {
        let btn = isDraft ? $('#btnDraft') : $('#btnSubmit');
        let originalText = btn.html();
        let url = "{{ box_route('whatsapp.business.template.store') }}";
        
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + (isDraft ? 'Saving...' : 'Submitting...'));

        let formData = $('#createTemplateForm').serialize();
        if (isDraft) {
            formData += '&save_as_draft=1';
        }

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            success: function(res) {
                btn.prop('disabled', false).html(originalText);
                if (res.success) {
                    showToast(res.message, 'success');
                    setTimeout(() => {
                        window.location.href = "{{ box_route('whatsapp.create_template') }}";
                    }, 1500);
                } else {
                    // Show the RAW message from the controller (Meta Error: ...)
                    showToast(res.message, 'danger');
                    console.error('Meta Response:', res.error);
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalText);
                let text = "An error occurred";
                if(xhr.responseJSON && xhr.responseJSON.message) text = xhr.responseJSON.message;
                showToast(text, 'danger');
            }
        });
    }
});
</script>


<script>
document.querySelector(".messageType").addEventListener("change", function () {
    let selectedValue = this.value;
    let textSection = document.querySelector(".textSection");

    if (selectedValue === "Text") {
        textSection.style.display = "block";
    } else {
        textSection.style.display = "none";
    }
});



const radios = document.querySelectorAll('.form-check-input');
const ctaRadio = document.querySelector('.radio-cta');
const ctaSection = document.querySelector('.cta-section');

radios.forEach(radio => {
    radio.addEventListener('change', () => {
        if (ctaRadio.checked) {
            ctaSection.style.display = 'block';
        } else {
            ctaSection.style.display = 'none';
        }
    });
});



document.querySelectorAll('.form-check-input').forEach(radio => {
    radio.addEventListener('change', function () {

        if (this.classList.contains('radio-quick') && this.checked) {
            document.querySelector('.quick-section').style.display = 'block';
        } else {
            document.querySelector('.quick-section').style.display = 'none';
        }

    });
});
</script>





<script>
$(document).ready(function() {
    let creditsLeft = {{ $creditsLeft }};

    // Style and Optimize selector toggles
    $('.style-selector .btn, .optimize-selector .btn').on('click', function() {
        $(this).parent().find('.btn').removeClass('active btn-dark').addClass('btn-outline-dark');
        $(this).removeClass('btn-outline-dark').addClass('active btn-dark');
    });

    // AI Prompt word count
    $('#ai_prompt').on('input', function() {
        $('#ai_prompt_count').text($(this).val().length);
    });

    $('#btnGenerateAI').on('click', function() {
        let prompt = $('#ai_prompt').val();
        let style = $('.style-selector .btn.active').data('style') || 'Normal';
        let optimize = $('.optimize-selector .btn.active').data('opt') || 'None';
        let language = $('select[name="language"]').val();
        let languageName = $('select[name="language"] option:selected').text();

        if (!prompt || prompt.trim().length < 5) {
            showToast("Please enter a professional prompt (min 5 characters) to generate content.", "warning");
            $('#ai_prompt').addClass('is-invalid').focus();
            return;
        }
        $('#ai_prompt').removeClass('is-invalid');

        if (!language) {
            showToast("Please select a Template Language from the dropdown above first.", "warning");
            $('select[name="language"]').addClass('is-invalid').focus();
            return;
        }
        $('select[name="language"]').removeClass('is-invalid');

        let btn = $(this);
        let originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generating Suggestions in ' + languageName + '...');

        $.ajax({
            url: "{{ box_route('whatsapp.ai_generate') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                prompt: prompt,
                style: style,
                optimize: optimize,
                language: languageName
            },
            success: function(res) {
                btn.html(originalHtml);
                if (res.success) {
                    creditsLeft = res.remaining;
                    $('#credits_count').text(creditsLeft);
                    
                    if (creditsLeft <= 0) {
                        btn.prop('disabled', true).addClass('btn-secondary').removeClass('btn-success');
                    } else {
                        btn.prop('disabled', false);
                    }

                    let container = $('#ai_results_container');
                    container.empty();
                    
                    res.results.forEach((text, index) => {
                        if(text && text.trim() !== "") {
                            let formattedText = text.replace(/\n/g, '<br>');
                            container.append(`
                                <div class="col-md-4 mb-3">
                                    <div class="generatedtemplatebox card h-100 shadow-sm border-0" style="border: 1px solid #1ECBE2 !important; transition: all 0.3s ease;">
                                        <div class="card-body d-flex flex-column p-3">
                                            <div class="suggestion-text mb-3" style="font-size: 0.85rem; line-height: 1.5; color: #444;">${formattedText}</div>
                                            <button type="button" class="btn btn-primary btn-sm btn-use-ai-template mt-auto w-100" data-text="${text.replace(/"/g, '&quot;')}">
                                                <i class="ti ti-check me-1"></i> Use This
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `);
                        }
                    });
                    $('.aigeneratedtemplates').slideDown();
                    showToast("3 Professional variations created!", "success");
                } else {
                    btn.prop('disabled', false);
                    showToast(res.message, "danger");
                }
            },
            error: function() {
                btn.prop('disabled', false).html(originalHtml);
                showToast("An error occurred during generation.", "danger");
            }
        });
    });

    $(document).on('click', '.btn-use-ai-template', function() {
        let text = $(this).data('text');
        $('textarea[name="body_text"]').val(text).trigger('input');
        showToast("Template applied!", "info");
        
        // Scroll to the body text section
        $('html, body').animate({
            scrollTop: $('textarea[name="body_text"]').offset().top - 100
        }, 500);
    });
});
</script>
@endpush
