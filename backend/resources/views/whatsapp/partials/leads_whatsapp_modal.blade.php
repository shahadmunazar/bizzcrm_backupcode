<!-- Quick WhatsApp Template Modal -->
<div class="modal fade" id="quick_whatsapp_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" id="quick-wa-modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);">
                <h5 class="modal-title d-flex align-items-center">
                    <i class="ti ti-brand-whatsapp me-2 fs-20"></i> Send WhatsApp Template
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <form id="quickWhatsAppForm">
                    <div class="row g-0">
                        <!-- Left Side: Form Area -->
                        <div class="col-lg-7 p-4 border-end" id="quick-wa-form-column">
                            <input type="hidden" name="lead_id" id="quick_wa_lead_id">
                            
                            <div class="mb-4 text-center">
                                <div class="avatar avatar-lg bg-soft-success text-success mb-2">
                                    <i class="ti ti-user fs-24"></i>
                                </div>
                                <h6 id="quick_wa_lead_name" class="fw-bold mb-0">Lead Name</h6>
                                <p id="quick_wa_lead_phone" class="text-muted small mb-0">+91 0000000000</p>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Select Template <span class="text-danger">*</span></label>
                                <select class="form-select select2-quick-wa" name="template_id" id="quick_wa_template_id" required>
                                    <option value="">-- Choose Template --</option>
                                </select>
                                <div id="quick_selected_template_preview_inline" class="mt-3 d-none"></div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Attach Chatbot <span class="text-muted fw-normal">(Optional)</span></label>
                                <select class="form-select select2-quick-wa-chatbot" name="chatbot_id" id="quick_wa_chatbot_id">
                                    <option value="">-- No Chatbot --</option>
                                </select>
                                <div class="form-text text-muted">Automatically triggers when user replies to this message.</div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-success btn-lg shadow-sm">
                                    <i class="ti ti-send me-2"></i> Send Template Now
                                </button>
                            </div>
                        </div>

                        <!-- Right Side: Preview Area -->
                        <div class="col-lg-5 p-4 bg-light d-flex flex-column" id="quick_wa_preview_container" style="border-radius: 0 0 15px 0; border-left: 1px solid #eef2f7;">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <h6 class="fw-bold mb-0 text-dark"><i class="bx bx-show me-2 text-primary"></i>Live Template Preview</h6>
                                <span class="badge bg-soft-success text-success px-2 py-1 small rounded-pill d-flex align-items-center gap-1">
                                    <span class="d-inline-block rounded-circle bg-success animate-pulse" style="width: 6px; height: 6px;"></span> Active
                                </span>
                            </div>
                            
                            <!-- Premium Smartphone Mockup for WhatsApp Preview -->
                            <div class="phone-mockup shadow border rounded-4 overflow-hidden bg-white mx-auto d-flex flex-column w-100" style="max-width: 340px; height: 480px; border-color: #e0e0e0 !important;">
                                <!-- Phone Status Bar -->
                                <div class="bg-dark text-white px-3 py-1 d-flex justify-content-between align-items-center" style="font-size: 10px; font-family: sans-serif; letter-spacing: 0.5px; opacity: 0.9;">
                                    <span>9:41</span>
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="bx bx-wifi small"></i>
                                        <i class="bx bxs-battery-full small"></i>
                                    </div>
                                </div>
                                
                                <!-- WhatsApp Header -->
                                <div class="d-flex align-items-center justify-content-between px-3 py-2 text-white" style="background-color: #075e54;">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center fw-bold me-2 shadow-sm" style="width: 32px; height: 32px; font-size: 14px;">
                                            WA
                                        </div>
                                        <div>
                                            <div class="fw-bold small lh-1">WhatsApp Preview</div>
                                            <span style="font-size: 9px; opacity: 0.85;">Template Preview</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2" style="font-size: 16px;">
                                        <i class="bx bxs-video"></i>
                                        <i class="bx bxs-phone"></i>
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </div>
                                </div>
                                
                                <!-- WhatsApp Chat Body with Background Wallpaper -->
                                <div id="quick_wa_preview_content" class="flex-grow-1 p-3 overflow-auto d-flex flex-column justify-content-start" style="background-color: #efeae2; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-size: cover; position: relative;">
                                    <div class="text-center text-muted small my-auto bg-white bg-opacity-75 p-3 rounded shadow-sm border border-light mx-2">
                                        <i class="ti ti-pointer fs-3 mb-2 text-primary"></i><br>
                                        Select a template to preview it here
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- WhatsApp Campaign Modal -->
<div class="modal fade" id="whatsapp_campaign_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" id="wa-campaign-modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);">
                <h5 class="modal-title d-flex align-items-center">
                    <i class="ti ti-brand-whatsapp me-2 fs-20"></i> WhatsApp Campaign
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <form id="whatsappCampaignForm">
                    <div class="row g-0">
                        <!-- Left Side: Form Area -->
                        <div class="col-lg-7 p-4 border-end" id="wa-campaign-form-column">
                            <input type="hidden" name="lead_ids" id="wa_campaign_lead_ids">
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Campaign Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-speakerphone text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0" name="campaign_name" id="wa_campaign_name" required placeholder="e.g. Summer Sale 2024">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Select Template <span class="text-danger">*</span></label>
                                <select class="form-select select2-wa" name="template_id" id="wa_template_id" required>
                                    <option value="">-- Choose Template --</option>
                                </select>
                                <div class="form-text text-muted">Only approved templates are shown.</div>
                                <div id="wa_selected_template_preview_inline" class="mt-3 d-none"></div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Attach Chatbot <span class="text-muted fw-normal">(Optional)</span></label>
                                <select class="form-select select2-wa-chatbot" name="chatbot_id" id="wa_chatbot_id">
                                    <option value="">-- No Chatbot --</option>
                                </select>
                                <div class="form-text text-muted">Automatically triggers when user replies to this campaign.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">When to Start? <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div class="form-check form-check-inline custom-radio">
                                        <input class="form-check-input" type="radio" name="schedule_type" id="wa_now" value="now" checked>
                                        <label class="form-check-label" for="wa_now">Send Now</label>
                                    </div>
                                    <div class="form-check form-check-inline custom-radio">
                                        <input class="form-check-input" type="radio" name="schedule_type" id="wa_later" value="later">
                                        <label class="form-check-label" for="wa_later">Schedule for Later</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4" id="wa_schedule_time_container" style="display: none;">
                                <label class="form-label fw-bold">Schedule Date & Time <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-calendar-event text-muted"></i></span>
                                    <input type="datetime-local" class="form-control border-start-0" name="schedule_time" id="wa_schedule_time">
                                </div>
                            </div>

                            <div class="alert alert-soft-success d-flex align-items-center" role="alert">
                                <i class="ti ti-info-circle me-2 fs-18"></i>
                                <div id="wa_selected_count_text">
                                    You have selected 0 leads.
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-lg text-white" style="background: #25D366; border-radius: 8px;">
                                     Start Campaigning <i class="ti ti-arrow-right ms-2"></i>
                                </button>
                                <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>

                        <!-- Right Side: Preview Area -->
                        <div class="col-lg-5 p-4 bg-light d-flex flex-column" id="wa_template_preview_container" style="border-radius: 0 0 15px 0; border-left: 1px solid #eef2f7;">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <h6 class="fw-bold mb-0 text-dark"><i class="bx bx-show me-2 text-primary"></i>Live Template Preview</h6>
                                <span class="badge bg-soft-success text-success px-2 py-1 small rounded-pill d-flex align-items-center gap-1">
                                    <span class="d-inline-block rounded-circle bg-success animate-pulse" style="width: 6px; height: 6px;"></span> Active
                                </span>
                            </div>
                            
                            <!-- Premium Smartphone Mockup for WhatsApp Preview -->
                            <div class="phone-mockup shadow border rounded-4 overflow-hidden bg-white mx-auto d-flex flex-column w-100" style="max-width: 340px; height: 480px; border-color: #e0e0e0 !important;">
                                <!-- Phone Status Bar -->
                                <div class="bg-dark text-white px-3 py-1 d-flex justify-content-between align-items-center" style="font-size: 10px; font-family: sans-serif; letter-spacing: 0.5px; opacity: 0.9;">
                                    <span>9:41</span>
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="bx bx-wifi small"></i>
                                        <i class="bx bxs-battery-full small"></i>
                                    </div>
                                </div>
                                
                                <!-- WhatsApp Header -->
                                <div class="d-flex align-items-center justify-content-between px-3 py-2 text-white" style="background-color: #075e54;">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center fw-bold me-2 shadow-sm" style="width: 32px; height: 32px; font-size: 14px;">
                                            WA
                                        </div>
                                        <div>
                                            <div class="fw-bold small lh-1">WhatsApp Preview</div>
                                            <span style="font-size: 9px; opacity: 0.85;">Template Preview</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2" style="font-size: 16px;">
                                        <i class="bx bxs-video"></i>
                                        <i class="bx bxs-phone"></i>
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </div>
                                </div>
                                
                                <!-- WhatsApp Chat Body with Background Wallpaper -->
                                <div id="wa_template_preview_content" class="flex-grow-1 p-3 overflow-auto d-flex flex-column justify-content-start" style="background-color: #efeae2; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-size: cover; position: relative;">
                                    <div class="text-center text-muted small my-auto bg-white bg-opacity-75 p-3 rounded shadow-sm border border-light mx-2">
                                        <i class="ti ti-pointer fs-3 mb-2 text-primary"></i><br>
                                        Select a template to preview it here
                                    </div>
                                </div>
                            </div>

                            <div id="wa_template_params_container" class="mt-3">
                                <!-- Dynamic parameters will be injected here if needed -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /WhatsApp Campaign Modal -->
