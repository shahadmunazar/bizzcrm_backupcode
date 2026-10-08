            let campaignContactsOffset = 0;
            let campaignContactsHasMore = true;
            let campaignContactsLoading = false;
            let currentCampaignSearch = '';

            function loadCampaignContacts(search = '', append = false) {
                if (campaignContactsLoading) return;
                
                const $list = $('#campaign-contact-list');
                
                if (!append) {
                    campaignContactsOffset = 0;
                    campaignContactsHasMore = true;
                    currentCampaignSearch = search;
                    $list.html('<div class="text-center py-4 text-muted small"><i class="bx bx-loader-alt bx-spin fs-4 d-block mb-2"></i> Loading contacts...</div>');
                } else if (!campaignContactsHasMore) {
                    return;
                }

                campaignContactsLoading = true;
                
                if (append) {
                    $list.append('<div id="campaign-load-more-loader" class="text-center py-2"><i class="bx bx-loader-alt bx-spin"></i></div>');
                }

                $.ajax({
                    url: `/{{ $box->slug }}/whatsapp/fetch-contacts`,
                    type: 'GET',
                    data: { 
                        search: currentCampaignSearch,
                        offset: campaignContactsOffset
                    },
                    success: function(res) {
                        $('#campaign-load-more-loader').remove();
                        if (res.success) {
                            let html = '';
                            res.data.forEach(lead => {
                                const checked = selectedCampaignLeads.has(lead.id.toString()) ? 'checked' : '';
                                const initials = (lead.name || 'U').charAt(0).toUpperCase();
                                html += `
                                    <div class="contact-row d-flex align-items-center p-3 border-bottom hover-bg-light transition-all cursor-pointer" data-id="${lead.id}">
                                        <div class="form-check mb-0 me-3">
                                            <input class="form-check-input campaign-lead-checkbox" type="checkbox" value="${lead.id}" id="campaign-lead-${lead.id}" ${checked}>
                                        </div>
                                        <div class="avatar-sm me-3 d-flex align-items-center justify-content-center bg-soft-primary text-primary rounded-circle fw-bold" style="width: 40px; height: 40px; min-width: 40px; font-size: 14px;">
                                            ${initials}
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <label class="d-block mb-0 fw-bold text-dark text-truncate cursor-pointer" for="campaign-lead-${lead.id}">${lead.name}</label>
                                            <div class="small text-muted text-truncate">${lead.phone}</div>
                                        </div>
                                    </div>
                                `;
                            });
                            
                            if (append) {
                                $list.append(html);
                            } else {
                                $list.html(html || '<div class="text-center py-4 text-muted">No contacts found</div>');
                            }
                            
                            campaignContactsHasMore = res.has_more;
                            campaignContactsOffset += res.data.length;
                            updateCampaignSelectAllState();
                        }
                    },
                    complete: function() {
                        campaignContactsLoading = false;
                    }
                });
            }

            // Scroll listener for Infinite Scroll
            $('#campaign-contact-list').on('scroll', function() {
                const $this = $(this);
                if ($this.scrollTop() + $this.innerHeight() >= $this[0].scrollHeight - 50) {
                    if (campaignContactsHasMore && !campaignContactsLoading) {
                        loadCampaignContacts(currentCampaignSearch, true);
                    }
                }
            });

            function updateCampaignSelectAllState() {
                const totalVisible = $('.campaign-lead-checkbox').length;
                const totalChecked = $('.campaign-lead-checkbox:checked').length;
                $('#campaign-select-all').prop('checked', totalVisible > 0 && totalVisible === totalChecked);
            }

            // Campaign Audience Method Toggle
            $(document).on('change', 'input[name="audience_method"]', function() {
                if ($(this).val() === 'excel') {
                    $('#audience-excel-section').show();
                    $('#audience-existing-section').hide();
                    $('#campaign_excel_file').prop('required', true);
                } else {
                    $('#audience-excel-section').hide();
                    $('#audience-existing-section').show();
                    $('#campaign_excel_file').prop('required', false);
                    loadCampaignContacts(); // Automatically load all contacts when switched
                }
            });

            let selectedCampaignLeads = new Set();

            $(document).on('input', '#campaign-contact-search', function() {
                const search = $(this).val();
                loadCampaignContacts(search);
            });

            $(document).on('change', '.campaign-lead-checkbox', function() {
                const id = $(this).val();
                if ($(this).is(':checked')) {
                    selectedCampaignLeads.add(id.toString());
                } else {
                    selectedCampaignLeads.delete(id.toString());
                }
                $('#campaign-selected-count').text(selectedCampaignLeads.size);
                updateCampaignSelectAllState();
            });

            $(document).on('click', '.contact-row', function(e) {
                if ($(e.target).hasClass('campaign-lead-checkbox') || $(e.target).closest('.form-check').length) return;
                const cb = $(this).find('.campaign-lead-checkbox');
                cb.prop('checked', !cb.is(':checked')).trigger('change');
            });
            // Excel File Preview Logic
            $(document).on('change', '#campaign_excel_file', function(e) {
                const file = e.target.files[0];
                if (!file) {
                    $('#excel-preview-area').hide();
                    return;
                }

                // Client-side file size validation (50MB)
                const maxSize = 50 * 1024 * 1024;
                if (file.size > maxSize) {
                    showToast('File is too large. Maximum size allowed is 50MB.', 'error');
                    $(this).val('');
                    $('#excel-preview-area').hide();
                    return;
                }

                const formData = new FormData();
                formData.append('excel_file', file);
                formData.append('_token', '{{ csrf_token() }}');

                // Show loading and progress area for preview
                $('#excel-preview-area').show().html(`
                    <div class="text-center py-4">
                        <i class="bx bx-loader-alt bx-spin fs-4 text-primary d-block mb-2"></i> 
                        <div id="preview-progress-text">Uploading file for preview...</div>
                        <div class="progress mt-2 mx-auto" style="height: 6px; width: 200px; border-radius: 3px;">
                            <div id="preview-upload-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 0%"></div>
                        </div>
                    </div>
                `);

                $.ajax({
                    url: `{{ route('whatsapp.campaign.preview', ['box_slug' => $box->slug]) }}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                                $('#preview-upload-bar').css('width', percentComplete + '%');
                                if (percentComplete === 100) {
                                    $('#preview-progress-text').text('Processing file data...');
                                }
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(res) {
                        if (res.success) {
                            renderExcelPreview(res);
                        } else {
                            $('#excel-preview-area').html(`<div class="alert alert-danger small p-2 mb-0"><i class="bx bx-error me-1"></i> ${res.message}</div>`);
                        }
                    },
                    error: function(xhr) {
                        let errMsg = 'Failed to process file. Please ensure it is a valid Excel/CSV.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        } else if (xhr.status === 413) {
                            errMsg = 'File is too large for the server (PHP post_max_size exceeded).';
                        }
                        $('#excel-preview-area').html(`<div class="alert alert-danger small p-2 mb-0"><i class="bx bx-error me-1"></i> ${errMsg}</div>`);
                    }
                });
            });

            // Global variable to store contacts for chunking
            let campaignContacts = [];
            let campaignId = null;
            let totalToProcess = 0;
            let processedSoFar = 0;
            let currentTemplateVariables = [];
            let excelHeaders = [];

            function renderExcelPreview(data) {
                const headers = data.headers;
                excelHeaders = headers; // Store globally
                const samples = data.samples;
                const totalRows = data.total_rows;
                
                // Hide progress bar if it was visible
                $('#campaign-upload-progress-area').hide();

                let html = `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 d-flex align-items-center">
                            <i class="bx bx-table me-2 text-primary fs-5"></i> Column Mapping & Preview
                        </h6>
                        <span class="badge bg-soft-info text-info border border-info">${totalRows} Rows Detected</span>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Phone Number Column *</label>
                            <select name="phone_column" id="excel-phone-col" class="form-select form-select-sm shadow-none" required>
                                <option value="">-- Select Column --</option>
                                ${headers.map((h, i) => `<option value="${i}">${h || ('Column ' + (i+1))}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Contact Name Column</label>
                            <select name="name_column" id="excel-name-col" class="form-select form-select-sm shadow-none">
                                <option value="">-- Default (Phone) --</option>
                                ${headers.map((h, i) => `<option value="${i}">${h || ('Column ' + (i+1))}</option>`).join('')}
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive border rounded bg-white" style="max-height: 200px;">
                        <table class="table table-sm table-bordered small mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>${headers.map(h => `<th>${h || ''}</th>`).join('')}</tr>
                            </thead>
                            <tbody>
                                ${samples.map(row => `<tr>${row.map(cell => `<td>${cell || ''}</td>`).join('')}</tr>`).join('')}
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2 text-muted" style="font-size: 11px;">
                        <i class="bx bx-info-circle me-1"></i> Data will be imported as new leads before sending.
                    </div>
                `;
                $('#excel-preview-area').html(html);
                
                // Auto-select first column for phone if it contains numbers
                if (samples.length > 1) {
                    samples.some((row, i) => {
                        const cell = row[0];
                        if (cell && cell.replace(/[^0-9]/g, '').length > 7) {
                            $('#excel-phone-col').val(0);
                            return true;
                        }
                    });
                }

                // If template is already selected, render its variable mappers
                if (currentTemplateVariables.length > 0) {
                    renderVariableMappers();
                }
            }

            // Initialize Select2 for the campaign template select
            $('#campaign-template-select').select2({
                dropdownParent: $('#upload-campaign-modal'),
                placeholder: "Select Template",
                width: '100%',
                theme: 'default'
            });

            // Initialize Select2 for the chatbot select
            $('#campaign-chatbot-select').select2({
                dropdownParent: $('#upload-campaign-modal'),
                placeholder: "-- No Chatbot --",
                width: '100%',
                theme: 'default',
                allowClear: true
            });

            // Cache to avoid spamming the backend when hovering
            const templateCache = {};

            function loadTemplatePreview(templateId, isFinalSelection = false) {
                if (!templateId) {
                    if (isFinalSelection) {
                        $('#template-variables-mapping').addClass('d-none');
                        $('#selected-template-preview-inline').addClass('d-none').html('');
                        currentTemplateVariables = [];
                    }
                    $('#template-preview-container').html(`
                        <div class="text-center text-muted small my-auto bg-white bg-opacity-75 p-3 rounded shadow-sm border border-light mx-2">
                            <i class="bx bx-pointer fs-3 mb-2 text-primary"></i><br>
                            Hover or select a template to preview it here
                        </div>
                    `);
                    return;
                }

                if (templateCache[templateId]) {
                    if (isFinalSelection) {
                        if (templateCache[templateId].preview_html) {
                            $('#selected-template-preview-inline').removeClass('d-none').html(templateCache[templateId].preview_html);
                        }
                        // Hide right-side preview column and expand left form column to full width
                        $('#campaign-preview-column').addClass('d-none');
                        $('#campaign-form-column').removeClass('col-lg-7').addClass('col-lg-12').removeClass('border-end');
                        $('#upload-campaign-modal-dialog').removeClass('modal-xl').addClass('modal-lg');

                        currentTemplateVariables = templateCache[templateId].variables;
                        if (currentTemplateVariables.length > 0) {
                            $('#template-variables-mapping').removeClass('d-none');
                            renderVariableMappers();
                        } else {
                            $('#template-variables-mapping').addClass('d-none');
                        }
                    } else {
                        if (templateCache[templateId].preview_html) {
                            $('#template-preview-container').html(templateCache[templateId].preview_html);
                        }
                    }
                    return;
                }

                $.ajax({
                    url: `/{{ $box->slug }}/whatsapp/template/${templateId}/variables`,
                    type: 'GET',
                    success: function(res) {
                        if (res.success) {
                            templateCache[templateId] = res;
                            if (isFinalSelection) {
                                if (res.preview_html) {
                                    $('#selected-template-preview-inline').removeClass('d-none').html(res.preview_html);
                                }
                                // Hide right-side preview column and expand left form column to full width
                                $('#campaign-preview-column').addClass('d-none');
                                $('#campaign-form-column').removeClass('col-lg-7').addClass('col-lg-12').removeClass('border-end');
                                $('#upload-campaign-modal-dialog').removeClass('modal-xl').addClass('modal-lg');

                                currentTemplateVariables = res.variables;
                                if (currentTemplateVariables.length > 0) {
                                    $('#template-variables-mapping').removeClass('d-none');
                                    renderVariableMappers();
                                } else {
                                    $('#template-variables-mapping').addClass('d-none');
                                }
                            } else {
                                if (res.preview_html) {
                                    $('#template-preview-container').html(res.preview_html);
                                }
                            }
                        }
                    }
                });
            }

            // Show preview on hover directly in the preview container
            $(document).on('mouseenter', '#select2-campaign-template-select-results .select2-results__option', function(e) {
                let templateId = null;
                
                // Method 1: Try reading standard Select2 data object
                const data = $(this).data('data');
                if (data && data.id) {
                    templateId = data.id;
                }
                
                // Method 2: Try extracting option value from the element ID attribute (e.g., select2-campaign-template-select-result-xxxx-14)
                if (!templateId) {
                    const idAttr = $(this).attr('id');
                    if (idAttr) {
                        const parts = idAttr.split('-');
                        const lastPart = parts[parts.length - 1];
                        if (lastPart && !isNaN(lastPart)) {
                            templateId = lastPart;
                        }
                    }
                }
                
                // Method 3: Fallback to text matching with the original select options
                if (!templateId) {
                    const text = $(this).text().trim();
                    if (text) {
                        const matchedOption = $('#campaign-template-select option').filter(function() {
                            return $(this).text().trim() === text;
                        });
                        templateId = matchedOption.val();
                    }
                }

                if (templateId) {
                    const showPreview = (html) => {
                        $('#template-preview-container').html(html);
                    };

                    if (templateCache[templateId]) {
                        if (templateCache[templateId].preview_html) showPreview(templateCache[templateId].preview_html);
                    } else {
                        // Fetch on hover if not cached
                        $.ajax({
                            url: `/{{ $box->slug }}/whatsapp/template/${templateId}/variables`,
                            type: 'GET',
                            success: function(res) {
                                if (res.success) {
                                    templateCache[templateId] = res;
                                    // If still hovering/selected
                                    if (res.preview_html) showPreview(res.preview_html);
                                }
                            }
                        });
                    }
                }
            });

            // When select2 opens, expand modal and show the preview column on the right
            $('#campaign-template-select').on('select2:open', function () {
                $('#campaign-preview-column').removeClass('d-none');
                $('#campaign-form-column').removeClass('col-lg-12').addClass('col-lg-7').addClass('border-end');
                $('#upload-campaign-modal-dialog').removeClass('modal-lg').addClass('modal-xl');
            });

            // When select2 closes, hide the right-side preview column and shrink modal
            $('#campaign-template-select').on('select2:close', function () {
                loadTemplatePreview(null, false);
                $('#campaign-preview-column').addClass('d-none');
                $('#campaign-form-column').removeClass('col-lg-7').addClass('col-lg-12').removeClass('border-end');
                $('#upload-campaign-modal-dialog').removeClass('modal-xl').addClass('modal-lg');
            });

            // Template selection listener (Final Selection)
            $(document).on('change', '#campaign-template-select', function() {
                const templateId = $(this).val();
                loadTemplatePreview(templateId, true);
            });

            // Trigger auto-selection and initial preview load when the modal is shown
            $('#upload-campaign-modal').on('shown.bs.modal', function () {
                const currentVal = $('#campaign-template-select').val();
                if (!currentVal) {
                    // Find the first option that isn't empty (the placeholder)
                    const firstRealOption = $('#campaign-template-select option').filter(function() {
                        return $(this).val() !== "";
                    }).first().val();
                    
                    if (firstRealOption) {
                        $('#campaign-template-select').val(firstRealOption).trigger('change');
                    }
                } else {
                    loadTemplatePreview(currentVal, true);
                }
            });

            function renderVariableMappers() {
                if (excelHeaders.length === 0) return;
                
                let html = '';
                currentTemplateVariables.forEach(v => {
                    html += `
                        <div class="col-md-4">
                            <label class="form-label small fw-bold mb-1">Variable @{{${v}}}</label>
                            <select name="variable_map[${v}]" class="form-select form-select-sm shadow-none border-primary border-opacity-25" required>
                                <option value="">-- Map --</option>
                                ${excelHeaders.map((h, i) => `<option value="${i}">${h || ('Column ' + (i+1))}</option>`).join('')}
                            </select>
                        </div>
                    `;
                });
                $('#variables-mapping-fields').html(html);
            }
            $(document).on('change', '#campaign-select-all', function() {
                const isChecked = $(this).is(':checked');
                $('.campaign-lead-checkbox').each(function() {
                    $(this).prop('checked', isChecked).trigger('change');
                });
            });

            $(document).on('click', '#clear-campaign-selection', function() {
                selectedCampaignLeads.clear();
                $('#campaign-selected-count').text(0);
                $('.campaign-lead-checkbox').prop('checked', false);
                $('#campaign-select-all').prop('checked', false);
            });

            // Reset Campaign Modal on hidden
            $('#upload-campaign-modal').on('hidden.bs.modal', function () {
                $('#upload-campaign-form')[0].reset();
                $('#campaign-template-select').val('').trigger('change');
                $('#campaign-chatbot-select').val('').trigger('change');
                $('#selected-template-preview-inline').addClass('d-none').html('');
                selectedCampaignLeads.clear();
                $('#campaign-selected-count').text(0);
                $('#campaign-contact-list').html('<div class="text-center py-3 text-muted small">Type to search contacts</div>');
                $('#audience-existing-section').hide();
                $('#audience-excel-section').show(); // Reset back to default excel section
                $('#campaign_excel_file').prop('required', true);
                $('#campaign-contact-search').val('');
                
                // Clear Excel preview and progress
                $('#excel-preview-area').html('').hide();
                $('#campaign-upload-progress-area').hide();
                
                // Reset submit button
                $('#upload-campaign-submit').prop('disabled', false).html('<i class="bx bx-paper-plane me-1"></i> Start Campaign');
                
                // Reset global variables
                excelHeaders = [];
                currentTemplateVariables = [];
            });

            // Upload Excel Campaign Form with Polling
            $('#upload-campaign-form').on('submit', function(e) {
                const method = $('input[name="audience_method"]:checked').val();
                
                if (method === 'existing') {
                    e.preventDefault();
                    
                    if (selectedCampaignLeads.size === 0) {
                        showToast('Please select at least one contact.', 'error');
                        return;
                    }

                    const btn = $('#upload-campaign-submit');
                    const originalText = btn.html();
                    btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Processing...');

                    // Map the form data manually
                    const formData = new FormData(this);
                    formData.delete('excel_file'); // Not needed
                    formData.delete('phone_column');
                    formData.delete('name_column');
                    
                    formData.append('lead_ids', Array.from(selectedCampaignLeads).join(','));
                    formData.append('schedule_type', 'now');
                    
                    $.ajax({
                        url: '{{ route("whatsapp.campaign.store", ["box_slug" => $box->slug]) }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            if (res.success) {
                                showToast(res.message, 'success');
                                $('#upload-campaign-modal').modal('hide');
                                // Refresh datatable if exists
                                if (typeof fetchCampaigns === 'function') {
                                    fetchCampaigns();
                                } else {
                                    setTimeout(() => window.location.reload(), 1500);
                                }
                            } else {
                                btn.prop('disabled', false).html(originalText);
                                showToast(res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false).html(originalText);
                            showToast('Failed to create campaign.', 'error');
                        }
                    });
                    return;
                }

                if (method !== 'excel') return;

                e.preventDefault();
                
                const btn = $('#upload-campaign-submit');
                const originalText = btn.html();
                btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Initializing...');
                
                const formData = new FormData(this);
                
                // Show progress bar area immediately
                $('#campaign-upload-progress-area').fadeIn();
                $('#excel-preview-area').fadeOut();
                $('#upload-status-text').text('Uploading file to server...');
                $('#campaign-upload-bar').css('width', '0%').addClass('progress-bar-animated');
                $('#upload-percent').text('0%');

                $.ajax({
                    url: '{{ route("whatsapp.campaign.excel", ["box_slug" => $box->slug]) }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                                $('#campaign-upload-bar').css('width', percentComplete + '%');
                                $('#upload-percent').text(percentComplete + '%');
                                if (percentComplete === 100) {
                                    $('#upload-status-text').text('File uploaded! Starting background processor...');
                                }
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(res) {
                        if (res.success) {
                            const campaignId = res.campaign_id;
                            const redirectUrl = res.redirect;
                            
                            // Start polling for background job progress
                            startPollingProgress(campaignId, redirectUrl, btn, originalText);
                        } else {
                            btn.prop('disabled', false).html(originalText);
                            $('#campaign-upload-progress-area').hide();
                            $('#excel-preview-area').show();
                            showToast(res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html(originalText);
                        $('#campaign-upload-progress-area').hide();
                        $('#excel-preview-area').show();
                        let errMsg = 'Failed to start upload.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        } else if (xhr.status === 413) {
                            errMsg = 'Payload too large. Please increase post_max_size in php.ini.';
                        }
                        showToast(errMsg, 'error');
                    }
                });
            });

            let pollingStartTime = null;
            function startPollingProgress(campaignId, redirectUrl, btn, originalText) {
                pollingStartTime = Date.now();
                const interval = setInterval(function() {
                    $.ajax({
                        url: `/{{ $box->slug }}/whatsapp/campaign/${campaignId}/progress`,
                        type: 'GET',
                        success: function(res) {
                            if (res.success) {
                                const percent = res.percent;
                                const status = res.status;
                                const processed = res.processed;
                                const total = res.total;

                                $('#campaign-upload-bar').css('width', percent + '%');
                                $('#upload-percent').text(percent + '%');
                                $('#upload-status-text').text(`Status: ${status}...`);
                                $('#upload-count-text').text(`Processed ${processed} of ${total} contacts`);

                                // Calculate ETA
                                if (processed > 0 && total > processed) {
                                    const elapsed = (Date.now() - pollingStartTime) / 1000; // seconds
                                    const rate = processed / elapsed; // contacts per second
                                    
                                    if (rate > 0) {
                                        const remaining = total - processed;
                                        const etaSeconds = Math.round(remaining / rate);
                                        
                                        if (etaSeconds > 0) {
                                            let etaText = 'Estimating remaining time...';
                                            if (etaSeconds > 60) {
                                                const mins = Math.floor(etaSeconds / 60);
                                                const secs = etaSeconds % 60;
                                                etaText = `Estimated time remaining: ~${mins}m ${secs}s`;
                                            } else {
                                                etaText = `Estimated time remaining: ~${etaSeconds}s`;
                                            }
                                            $('#upload-eta-text').text(etaText);
                                        }
                                    } else {
                                        $('#upload-eta-text').text('Calculating remaining time...');
                                    }
                                } else if (processed === 0 && total > 0) {
                                    $('#upload-eta-text').text('Waiting for processing to start...');
                                }

                                if (res.is_completed) {
                                    clearInterval(interval);
                                    $('#upload-status-text').text('Processing complete!');
                                    $('#upload-eta-text').text('');
                                    $('#campaign-upload-bar').removeClass('progress-bar-animated').addClass('bg-success');
                                    setTimeout(() => {
                                        window.location.href = redirectUrl;
                                    }, 800);
                                } else if (status === 'FAILED') {
                                    clearInterval(interval);
                                    btn.prop('disabled', false).html(originalText);
                                    showToast('Background processing failed.', 'error');
                                }
                            }
                        },
                        error: function() {
                            // Don't stop on single error, could be temporary network glitch
                            console.error('Polling progress failed');
                        }
                    });
                }, 2000); // Poll every 2 seconds
            }
