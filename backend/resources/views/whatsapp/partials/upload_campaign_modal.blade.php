<!-- Upload Campaign Modal -->
<div class="modal fade" id="upload-campaign-modal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-xl" id="upload-campaign-modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <form id="upload-campaign-form" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title text-white"><i class="bx bx-upload me-2"></i> Upload Excel Campaign</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <!-- Left Side: Form Area -->
                        <div class="col-lg-7 p-4 border-end" id="campaign-form-column">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Campaign Name <span class="text-danger">*</span></label>
                                <input type="text" name="campaign_name" class="form-control" placeholder="e.g., Summer Promo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Template <span class="text-danger">*</span></label>
                        <select name="template_id" id="campaign-template-select" class="form-control" required>
                            <option value="">Select Template</option>
                            @foreach($templates as $template)
                                @if(is_object($template))
                                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <div id="selected-template-preview-inline" class="mt-3 d-none"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Attach Chatbot <span class="text-muted fw-normal">(Optional)</span></label>
                        <select name="chatbot_id" id="campaign-chatbot-select" class="form-control">
                            <option value="">-- No Chatbot --</option>
                            @if(isset($chatbots))
                                @foreach($chatbots as $chatbot)
                                    @if(is_object($chatbot))
                                        <option value="{{ $chatbot->id }}">{{ $chatbot->name }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <div class="form-text text-muted">Automatically triggers when a user replies to this campaign.</div>
                    </div>

                    <!-- Template Variables Mapping Container -->
                    <div id="template-variables-mapping" class="mb-3 d-none">
                        <div class="p-3 bg-soft-primary rounded border border-primary border-opacity-25">
                            <h6 class="small fw-bold text-primary mb-2 d-flex align-items-center">
                                <i class="bx bx-list-check me-2"></i> Template Variables Mapping
                            </h6>
                            <div id="variables-mapping-fields" class="row g-2">
                                <!-- Dynamic fields will appear here -->
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="form-label fw-bold mb-2">Audience Selection Method</label>
                        <div class="d-flex gap-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="audience_method" id="method_excel" value="excel" checked>
                                <label class="form-check-label" for="method_excel">Upload Excel</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="audience_method" id="method_existing" value="existing">
                                <label class="form-check-label" for="method_existing">Existing Contacts</label>
                            </div>
                        </div>

                        <!-- Permanent Create Leads Switch -->
                        <div class="form-check form-switch border-top pt-2">
                            <input class="form-check-input" type="checkbox" name="create_leads" id="create-leads-switch" checked>
                            <label class="form-check-label small fw-bold" for="create-leads-switch">
                                Create leads in CRM for new numbers
                            </label>
                        </div>
                    </div>

                    <div id="audience-excel-section">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Upload Excel/CSV File <span class="text-danger">*</span></label>
                            <input type="file" name="excel_file" id="campaign_excel_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted d-block mt-1">Select a file to preview and map columns.</small>
                        </div>
                        
                        <!-- Progress Bar (Initially hidden) -->
                        <div id="campaign-upload-progress-area" style="display: none;" class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold text-primary" id="upload-status-text">Uploading contacts...</span>
                                <span class="small fw-bold text-primary" id="upload-percent">0%</span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 5px;">
                                <div id="campaign-upload-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                            </div>
                            <div class="text-center mt-2">
                                <small class="text-muted d-block" id="upload-count-text"></small>
                                <small class="text-primary fw-bold" id="upload-eta-text"></small>
                            </div>
                        </div>

                        <!-- Preview & Mapping Area -->
                        <div id="excel-preview-area" style="display: none;" class="mt-3 border rounded p-3 bg-light shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0 d-flex align-items-center">
                                    <i class="bx bx-table me-2 text-primary fs-5"></i> Column Mapping & Preview
                                </h6>
                                <span class="badge bg-soft-info text-info border border-info" id="excel-total-rows-label"></span>
                            </div>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Phone Number Column *</label>
                                    <select name="phone_column" id="excel-phone-col" class="form-select form-select-sm shadow-none" required>
                                        <option value="">-- Select Column --</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Contact Name Column</label>
                                    <select name="name_column" id="excel-name-col" class="form-select form-select-sm shadow-none">
                                        <option value="">-- Default (Phone) --</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="table-responsive border rounded bg-white shadow-none" style="max-height: 200px;">
                                <table class="table table-sm table-bordered small mb-0" id="excel-preview-table">
                                    <thead class="bg-light sticky-top">
                                        <tr></tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="mt-2 text-muted" style="font-size: 11px;">
                                <i class="bx bx-info-circle me-1"></i> First row is usually treated as headers.
                            </div>
                        </div>
                    </div>

                    <div id="audience-existing-section" style="display: none;">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold mb-0">Select Contacts</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="campaign-select-all">
                                    <label class="form-check-label small fw-bold text-primary cursor-pointer" for="campaign-select-all">Select All</label>
                                </div>
                            </div>
                            <div class="input-group mb-3 shadow-sm rounded">
                                <span class="input-group-text bg-white border-end-0"><i class="bx bx-search text-muted"></i></span>
                                <input type="text" id="campaign-contact-search" class="form-control border-start-0 ps-0" placeholder="Search contacts...">
                            </div>
                            <div id="campaign-contact-list" class="border rounded p-0" style="max-height: 250px; overflow-y: auto; background: #fff;">
                                <div class="text-center py-4 text-muted small">
                                    <i class="bx bx-loader-alt bx-spin fs-4 d-block mb-2"></i>
                                    Loading contacts...
                                </div>
                            </div>
                            <div class="mt-3 p-2 bg-light rounded d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-primary rounded-pill me-2" id="campaign-selected-count">0</div>
                                    <span class="small text-muted">Contacts Selected</span>
                                </div>
                                <a href="javascript:void(0);" id="clear-campaign-selection" class="btn btn-link btn-sm text-danger text-decoration-none fw-bold p-0">Clear All</a>
                            </div>
                        </div>
                    </div>
                </div>
                    
                    <!-- Right Side: Preview Area -->
                    <div class="col-lg-5 p-4 bg-light d-flex flex-column" id="campaign-preview-column" style="border-radius: 0 0 15px 0; border-left: 1px solid #eef2f7;">
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
                            <div id="template-preview-container" class="flex-grow-1 p-3 overflow-auto d-flex flex-column justify-content-start" style="background-color: #efeae2; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-size: cover; position: relative;">
                                <div class="text-center text-muted small my-auto bg-white bg-opacity-75 p-3 rounded shadow-sm border border-light mx-2">
                                    <i class="bx bx-pointer fs-3 mb-2 text-primary"></i><br>
                                    Hover or select a template to preview it here
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="modal-footer bg-white border-top">
                    <button type="button" class="btn btn-label-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="upload-campaign-submit" class="btn btn-primary fw-bold">
                        <i class="bx bx-paper-plane me-1"></i> Start Campaign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
