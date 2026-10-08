<style>
    /* Make the hovered Select2 option red */
    .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected],
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #dc3545 !important;
        color: white !important;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // --- Bulk WhatsApp Logic ---
        window.openWhatsAppCampaignModal = function() {
            var uniqueIds = new Set();
            if (window.selectedLeadIds) {
                window.selectedLeadIds.forEach(function(id) {
                    if (id !== null && id !== undefined) {
                        var cleanId = String(id).trim();
                        if (cleanId !== '' && cleanId !== 'null' && cleanId !== 'undefined' && cleanId !== 'NaN' && cleanId !== 'on') {
                            uniqueIds.add(cleanId);
                        }
                    }
                });
            }
            var selectedIdsArray = Array.from(uniqueIds);
            if (selectedIdsArray.length === 0) {
                showToast('Please select at least one lead.', 'warning');
                return;
            }

            $('#wa_campaign_lead_ids').val(selectedIdsArray.join(','));
            $('#wa_selected_count_text').html('You have selected <strong>' + selectedIdsArray.length + '</strong> lead(s) for this campaign.');
            
            // Clear previous form data
            $('#whatsappCampaignForm')[0].reset();
            $('#wa_schedule_time_container').hide();
            
            // Load templates
            fetchWhatsAppTemplates();
            
            $('#whatsapp_campaign_modal').modal('show');
        };

        function fetchWhatsAppTemplates() {
            if (window.waTemplatesLoaded) return;
            
            const select = $('#wa_template_id');
            const chatbotSelect = $('#wa_chatbot_id');
            select.empty().append('<option value="">Loading templates...</option>');
            chatbotSelect.empty().append('<option value="">Loading chatbots...</option>');
            
            const boxSlug = "{{ request()->route('box_slug') }}";
            const url = "/"+boxSlug+"/whatsapp/template/list";
            
            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    const quickSelect = $('#quick_wa_template_id');
                    const quickChatbotSelect = $('#quick_wa_chatbot_id');
                    
                    select.empty().append('<option value="">-- Choose Template --</option>');
                    quickSelect.empty().append('<option value="">-- Choose Template --</option>');
                    
                    chatbotSelect.empty().append('<option value="">-- No Chatbot --</option>');
                    quickChatbotSelect.empty().append('<option value="">-- No Chatbot --</option>');
                    
                    if (response.success) {
                        if (response.templates) {
                            window.waTemplatesData = response.templates;
                            let firstTemplateId = null;
                            response.templates.forEach(t => {
                                if (t.whats_app_status === 'APPROVED') {
                                    if (!firstTemplateId) firstTemplateId = t.id;
                                    select.append(`<option value="${t.id}">${t.name} (${t.language})</option>`);
                                    quickSelect.append(`<option value="${t.id}">${t.name} (${t.language})</option>`);
                                }
                            });
                            
                            // Auto-select the first template
                            if (firstTemplateId) {
                                select.val(firstTemplateId);
                                quickSelect.val(firstTemplateId);
                            }
                        }
                        
                        if (response.chatbots) {
                            window.waChatbotsData = response.chatbots;
                            response.chatbots.forEach(c => {
                                chatbotSelect.append(`<option value="${c.id}">${c.name}</option>`);
                                quickChatbotSelect.append(`<option value="${c.id}">${c.name}</option>`);
                            });
                        }
                        
                        // Forcefully initialize Select2 so we guarantee it's not a native OS dropdown (which blocks hover events)
                        $('#wa_template_id').select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            dropdownParent: $('#whatsapp_campaign_modal')
                        });
                        
                        $('#quick_wa_template_id').select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            dropdownParent: $('#quick_whatsapp_modal')
                        });
                        
                        // Trigger change so the auto-selected template loads its preview!
                        $('#wa_template_id').trigger('change');
                        $('#quick_wa_template_id').trigger('change');
                        
                        // Re-bind Select2 events safely here
                        setTimeout(function() {
                            $('#wa_template_id').off('select2:open').on('select2:open', function() {
                                $('#wa_template_preview_container').removeClass('d-none');
                                $('#wa-campaign-form-column').removeClass('col-lg-12').addClass('col-lg-7').addClass('border-end');
                                $('#whatsapp_campaign_modal .modal-dialog').removeClass('modal-lg').addClass('modal-xl');
                            });

                            $('#wa_template_id').off('select2:close').on('select2:close', function() {
                                loadBulkTemplatePreview(null, false);
                                $('#wa_template_preview_container').addClass('d-none');
                                $('#wa-campaign-form-column').removeClass('col-lg-7').addClass('col-lg-12').removeClass('border-end');
                                $('#whatsapp_campaign_modal .modal-dialog').removeClass('modal-xl').addClass('modal-lg');
                            });

                            $('#quick_wa_template_id').off('select2:open').on('select2:open', function() {
                                $('#quick_wa_preview_container').removeClass('d-none');
                                $('#quick-wa-form-column').removeClass('col-lg-12').addClass('col-lg-7').addClass('border-end');
                                $('#quick_whatsapp_modal .modal-dialog').removeClass('modal-lg').addClass('modal-xl');
                            });

                            $('#quick_wa_template_id').off('select2:close').on('select2:close', function() {
                                loadQuickTemplatePreview(null, false);
                                $('#quick_wa_preview_container').addClass('d-none');
                                $('#quick-wa-form-column').removeClass('col-lg-7').addClass('col-lg-12').removeClass('border-end');
                                $('#quick_whatsapp_modal .modal-dialog').removeClass('modal-xl').addClass('modal-lg');
                            });
                        }, 50);
                    } else {
                        select.empty().append('<option value="">Error loading templates</option>');
                        chatbotSelect.empty().append('<option value="">Error loading chatbots</option>');
                    }
                },
                error: function() {
                    select.empty().append('<option value="">Error loading templates</option>');
                    chatbotSelect.empty().append('<option value="">Error loading chatbots</option>');
                }
            });
        }

        const waTemplateCache = {};
        const quickWaTemplateCache = {};

        function loadBulkTemplatePreview(templateId, isFinalSelection = false) {
            const previewContent = $('#wa_template_preview_content');
            if (!templateId) {
                if (isFinalSelection) {
                    $('#wa_selected_template_preview_inline').addClass('d-none').html('');
                }
                previewContent.html(`
                    <div class="text-center text-muted small my-auto bg-white bg-opacity-75 p-3 rounded shadow-sm border border-light mx-2">
                        <i class="bx bx-pointer fs-3 mb-2 text-primary"></i><br>
                        Hover or select a template to preview it here
                    </div>
                `);
                return;
            }

            if (waTemplateCache[templateId]) {
                if (isFinalSelection) {
                    if (waTemplateCache[templateId]) {
                        $('#wa_selected_template_preview_inline').removeClass('d-none').html(waTemplateCache[templateId]);
                    }
                    $('#wa_template_preview_container').addClass('d-none');
                    $('#wa-campaign-form-column').removeClass('col-lg-7').addClass('col-lg-12').removeClass('border-end');
                    $('#whatsapp_campaign_modal .modal-dialog').removeClass('modal-xl').addClass('modal-lg');
                } else {
                    previewContent.html(waTemplateCache[templateId]);
                }
                return;
            }

            const boxSlug = "{{ request()->route('box_slug') }}";
            $.ajax({
                url: `/${boxSlug}/whatsapp/template/${templateId}/variables`,
                type: 'GET',
                success: function(res) {
                    if (res.success && res.preview_html) {
                        waTemplateCache[templateId] = res.preview_html;
                        if (isFinalSelection) {
                            $('#wa_selected_template_preview_inline').removeClass('d-none').html(res.preview_html);
                            $('#wa_template_preview_container').addClass('d-none');
                            $('#wa-campaign-form-column').removeClass('col-lg-7').addClass('col-lg-12').removeClass('border-end');
                            $('#whatsapp_campaign_modal .modal-dialog').removeClass('modal-xl').addClass('modal-lg');
                        } else {
                            previewContent.html(res.preview_html);
                        }
                    }
                }
            });
        }

        function loadQuickTemplatePreview(templateId, isFinalSelection = false) {
            const previewContent = $('#quick_wa_preview_content');
            if (!templateId) {
                if (isFinalSelection) {
                    $('#quick_selected_template_preview_inline').addClass('d-none').html('');
                }
                previewContent.html(`
                    <div class="text-center text-muted small my-auto bg-white bg-opacity-75 p-3 rounded shadow-sm border border-light mx-2">
                        <i class="bx bx-pointer fs-3 mb-2 text-primary"></i><br>
                        Hover or select a template to preview it here
                    </div>
                `);
                return;
            }

            if (quickWaTemplateCache[templateId]) {
                if (isFinalSelection) {
                    if (quickWaTemplateCache[templateId]) {
                        $('#quick_selected_template_preview_inline').removeClass('d-none').html(quickWaTemplateCache[templateId]);
                    }
                    $('#quick_wa_preview_container').addClass('d-none');
                    $('#quick-wa-form-column').removeClass('col-lg-7').addClass('col-lg-12').removeClass('border-end');
                    $('#quick_whatsapp_modal .modal-dialog').removeClass('modal-xl').addClass('modal-lg');
                } else {
                    previewContent.html(quickWaTemplateCache[templateId]);
                }
                return;
            }

            const boxSlug = "{{ request()->route('box_slug') }}";
            $.ajax({
                url: `/${boxSlug}/whatsapp/template/${templateId}/variables`,
                type: 'GET',
                success: function(res) {
                    if (res.success && res.preview_html) {
                        quickWaTemplateCache[templateId] = res.preview_html;
                        if (isFinalSelection) {
                            $('#quick_selected_template_preview_inline').removeClass('d-none').html(res.preview_html);
                            $('#quick_wa_preview_container').addClass('d-none');
                            $('#quick-wa-form-column').removeClass('col-lg-7').addClass('col-lg-12').removeClass('border-end');
                            $('#quick_whatsapp_modal .modal-dialog').removeClass('modal-xl').addClass('modal-lg');
                        } else {
                            previewContent.html(res.preview_html);
                        }
                    }
                }
            });
        }

        // Show preview on hover globally for Select2 options in WhatsApp modals
        $(document).on('mouseenter', '#whatsapp_campaign_modal .select2-results__option, #quick_whatsapp_modal .select2-results__option', function(e) {
            let templateId = null;
            
            // Method 1: Try reading standard Select2 data object
            const data = $(this).data('data');
            if (data && data.id) {
                templateId = data.id;
            }
            
            // Method 2: Try extracting option value from the element ID attribute
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

            // Method 3: Fallback to text matching
            if (!templateId) {
                const text = $(this).text().trim();
                if (text) {
                    let matchedOption = $('#wa_template_id option').filter(function() { return $(this).text().trim() === text; });
                    if (matchedOption.length > 0) {
                        templateId = matchedOption.val();
                    } else {
                        matchedOption = $('#quick_wa_template_id option').filter(function() { return $(this).text().trim() === text; });
                        if (matchedOption.length > 0) templateId = matchedOption.val();
                    }

                    // Method 4: Search through global templates data directly if DOM match fails
                    if (!templateId && window.waTemplatesData) {
                        const match = window.waTemplatesData.find(t => `${t.name} (${t.language})` === text || t.name === text);
                        if (match) templateId = match.id;
                    }
                }
            }

            if (templateId) {
                // Update both preview containers to guarantee the visible one gets updated
                loadBulkTemplatePreview(templateId, false);
                loadQuickTemplatePreview(templateId, false);
            }
        });

        // Handle keyboard navigation for Select2
        $(document).on('keyup', '.select2-search__field', function(e) {
            // If up/down arrows are pressed
            if (e.which === 38 || e.which === 40) {
                setTimeout(function() {
                    $('.select2-results__option--highlighted').trigger('mouseenter');
                }, 50);
            }
        });

        // Template selection listener (Final Selection)
        $(document).on('change', '#wa_template_id', function() {
            loadBulkTemplatePreview($(this).val(), true);
        });

        $(document).on('change', '#quick_wa_template_id', function() {
            loadQuickTemplatePreview($(this).val(), true);
        });

        // Initial preview load when the modal is shown
        $('#whatsapp_campaign_modal').on('shown.bs.modal', function () {
            loadBulkTemplatePreview($('#wa_template_id').val(), true);
        });

        $('#quick_whatsapp_modal').on('shown.bs.modal', function () {
            loadQuickTemplatePreview($('#quick_wa_template_id').val(), true);
        });

        $(document).on('change', 'input[name="schedule_type"]', function() {
            if ($(this).val() === 'later') {
                $('#wa_schedule_time_container').fadeIn();
                $('#wa_schedule_time').attr('required', true);
            } else {
                $('#wa_schedule_time_container').fadeOut();
                $('#wa_schedule_time').removeAttr('required');
            }
        });

        $('#whatsappCampaignForm').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const btn = $form.find('button[type="submit"]');
            
            // Validate template variables
            const templateId = $('#wa_template_id').val();
            const template = (window.waTemplatesData || []).find(t => t.id == templateId);
            let hasUnmappedVariables = false;
            if (template) {
                try {
                    const components = typeof template.components === 'string' ? JSON.parse(template.components) : template.components;
                    const params = typeof template.params === 'string' ? JSON.parse(template.params) : (template.params || {});
                    if (Array.isArray(components)) {
                        for (const comp of components) {
                            if (comp.text) {
                                const regex = /\{\{(\d+)\}\}/g;
                                let match;
                                while ((match = regex.exec(comp.text)) !== null) {
                                    const varNumber = match[1];
                                    if (!params || !params[varNumber]) {
                                        hasUnmappedVariables = true;
                                        break;
                                    }
                                }
                            }
                            if (hasUnmappedVariables) break;
                        }
                    }
                } catch(err) {}
            }
            
            if (hasUnmappedVariables) {
                showToast('This template contains variables which are not mapped. Please map them or choose a different template.', 'error');
                return;
            }
            
            const originalHtml = btn.html();
            
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Starting...');
            
            const boxSlug = "{{ request()->route('box_slug') }}";
            const url = "/"+boxSlug+"/whatsapp/campaign/store";

            $.ajax({
                url: url,
                method: 'POST',
                data: $form.serialize(),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message || 'Campaign started successfully!', 'success');
                        $('#whatsapp_campaign_modal').modal('hide');
                        // Optionally refresh table or clear selection
                    } else {
                        showToast(response.message || 'Failed to start campaign.', 'danger');
                    }
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Error occurred.', 'danger');
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalHtml);
                }
            });
        });

        // --- Quick WhatsApp Logic ---
        $(document).on('click', '.btn-quick-whatsapp', function() {
            const leadId = $(this).data('lead-id');
            const leadName = $(this).data('lead-name');
            const leadPhone = $(this).data('lead-phone');
            
            $('#quick_wa_lead_id').val(leadId);
            $('#quick_wa_lead_name').text(leadName || 'Unknown Lead');
            $('#quick_wa_lead_phone').text(leadPhone || 'No Phone Number');
            
            // Ensure templates are loaded
            fetchWhatsAppTemplates();
            
            $('#quick_wa_preview_container').addClass('d-none');
            $('#quick_whatsapp_modal').modal('show');
        });

        $('#quickWhatsAppForm').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const btn = $form.find('button[type="submit"]');
            
            // Validate template variables
            const templateId = $('#quick_wa_template_id').val();
            const template = (window.waTemplatesData || []).find(t => t.id == templateId);
            let hasUnmappedVariables = false;
            if (template) {
                try {
                    const components = typeof template.components === 'string' ? JSON.parse(template.components) : template.components;
                    const params = typeof template.params === 'string' ? JSON.parse(template.params) : (template.params || {});
                    if (Array.isArray(components)) {
                        for (const comp of components) {
                            if (comp.text) {
                                const regex = /\{\{(\d+)\}\}/g;
                                let match;
                                while ((match = regex.exec(comp.text)) !== null) {
                                    const varNumber = match[1];
                                    if (!params || !params[varNumber]) {
                                        hasUnmappedVariables = true;
                                        break;
                                    }
                                }
                            }
                            if (hasUnmappedVariables) break;
                        }
                    }
                } catch(err) {}
            }
            
            if (hasUnmappedVariables) {
                showToast('This template contains variables which are not mapped. Please map them or choose a different template.', 'error');
                return;
            }
            
            const originalHtml = btn.html();
            
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Sending...');
            
            const boxSlug = "{{ request()->route('box_slug') }}";
            const url = "/"+boxSlug+"/whatsapp/campaign/store";
            
            // Map Quick fields to Campaign fields
            const data = {
                campaign_name: 'Quick Message to ' + $('#quick_wa_lead_name').text(),
                template_id: $('#quick_wa_template_id').val(),
                chatbot_id: $('#quick_wa_chatbot_id').val(),
                lead_ids: $('#quick_wa_lead_id').val(),
                schedule_type: 'now',
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        showToast(response.message || 'Template sent!', 'success');
                        $('#quick_whatsapp_modal').modal('hide');
                    } else {
                        showToast(response.message || 'Failed to send.', 'danger');
                    }
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Error occurred.', 'danger');
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalHtml);
                }
            });
        });
    });
</script>
