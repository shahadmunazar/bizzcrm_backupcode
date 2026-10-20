@once
@push('scripts')
<script>
    // Cache and load currencies (avoid re-fetching 1.4MB countries.json on every page load)
    if (typeof window.globalCurrencies === 'undefined') {
        window.globalCurrencies = [];

        function applyCurrencies(currenciesList) {
            window.globalCurrencies = currenciesList;
            $('select[name="currency"]').each(function() {
                const $select = $(this);
                if ($select.children('option').length <= 3) {
                    const currentVal = $select.val();
                    $select.empty().append('<option value="">Select Currency</option>');
                    currenciesList.forEach(curr => {
                        $select.append(`<option value="${curr.id}">${curr.name}</option>`);
                    });
                    $select.val(currentVal).trigger('change');
                }
            });
        }

        try {
            const cached = localStorage.getItem('crm_currencies_list');
            if (cached) {
                applyCurrencies(JSON.parse(cached));
            }
        } catch (e) {
            console.warn('Could not read cached currencies:', e);
        }

        if (!window.globalCurrencies || window.globalCurrencies.length === 0) {
            fetch("{{ asset('assets/json/countries.json') }}")
                .then(res => res.json())
                .then(data => {
                    const currencyMap = new Map();
                    data.forEach(country => {
                        if (country.currencies && Object.keys(country.currencies).length > 0) {
                            const code = Object.keys(country.currencies)[0];
                            const currency = country.currencies[code];
                            if (!currencyMap.has(code)) {
                                currencyMap.set(code, {
                                    id: code,
                                    name: `${code} (${currency.symbol || code}) - ${currency.name}`
                                });
                            }
                        }
                    });
                    const currenciesList = Array.from(currencyMap.values());
                    currenciesList.sort((a, b) => a.name.localeCompare(b.name));
                    applyCurrencies(currenciesList);
                    try {
                        localStorage.setItem('crm_currencies_list', JSON.stringify(currenciesList));
                    } catch (e) {}
                })
                .catch(err => console.error('Error loading RESTCountries API:', err));
        }
    }

    function renderLeadForm(resData, isEdit = false) {
        const prefix = isEdit ? 'edit' : 'add';
        let html = '';
        const fields = resData.mergedFields;
        const leadSources = resData.leadSources || [];
        const industries = resData.industries || [];
        const leadsStatus = resData.leadsStatus || [];
        const countries = resData.countries || [];
        const owners = resData.owners || [];
        const authId = "{{ auth()->id() }}";
        // Helper to escape html
        const escapeHtml = (text) => {
            if (!text) return '';
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
        };

        fields.forEach((field, loopIndex) => {
            const isCustom = field.is_custom;
            const fieldId = field.id;
            const fieldName = escapeHtml(field.name);
            const isRequired = field.is_required || field.required;
            const requiredAttr = isRequired ? 'data-required="true"' : '';
            const requiredStar = isRequired ? '<span class="text-danger">*</span>' : '';
            
            let wrapperId = '';
            if (!isCustom && (field.field === 'lead_sub_status_id' || field.field === 'lead_sub_status')) {
                wrapperId = `id="${prefix}_lead_sub_status_wrapper"`;
            }

            html += `<div class="mb-3 draggable-field border p-3 rounded position-relative" data-id="${fieldId}" data-type="${isCustom ? 'custom' : 'system'}" style="cursor: move;" ${wrapperId}>`;
            html += `<div class="drag-handle position-absolute top-0 start-0 p-2" style="cursor: grab;"><i class="ti ti-grip-vertical text-muted"></i></div>`;
            html += `<div class="error-msg text-danger fw-semibold mb-1" style="display:none; font-size: 13px; padding-left: 30px;"><i class="ti ti-alert-circle me-1"></i> <span class="error-text">This field is required</span></div>`;
            
            if (isCustom) {
                html += `<label class="col-form-label" style="padding-left: 30px;">${fieldName} ${requiredStar}</label>`;
                const fieldType = field.type || 'text';
                const colName = field.col_name;
                const params = typeof field.params === 'string' ? JSON.parse(field.params) : (field.params || {});
                const options = Array.isArray(params) ? params : (params.options || []);
                if (fieldType === 'textarea') {
                    html += `<textarea name="${colName}" class="form-control" rows="3" placeholder="${fieldName}" ${requiredAttr}></textarea>`;
                } else if (fieldType === 'select') {
                    html += `<select name="${colName}" class="form-select select" ${requiredAttr}><option value="">Select ${fieldName}</option>`;
                    options.forEach(opt => {
                        const val = escapeHtml(typeof opt === 'object' ? (opt.value || opt.id || '') : opt);
                        const label = escapeHtml(typeof opt === 'object' ? (opt.label || opt.name || val) : opt);
                        html += `<option value="${val}">${label}</option>`;
                    });
                    html += `</select>`;
                } else if (fieldType === 'radio') {
                    html += `<div class="mt-2" style="padding-left: 30px;">`;
                    options.forEach((opt, idx) => {
                        const val = escapeHtml(typeof opt === 'object' ? (opt.value || opt.id || '') : opt);
                        const label = escapeHtml(typeof opt === 'object' ? (opt.label || opt.name || val) : opt);
                        const idVal = String(val).replace(/[^a-zA-Z0-9]/g, '-').toLowerCase();
                        html += `<div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="${colName}" id="custom_${prefix}_${fieldId}_${idVal}" value="${val}" ${requiredAttr}>
                            <label class="form-check-label" for="custom_${prefix}_${fieldId}_${idVal}">${label}</label>
                        </div>`;
                    });
                    html += `</div>`;
                } else if (fieldType === 'checkbox' || fieldType === 'multi_checkbox') {
                    html += `<div class="mt-2" style="padding-left: 30px;">`;
                    options.forEach((opt, idx) => {
                        const val = escapeHtml(typeof opt === 'object' ? (opt.value || opt.id || '') : opt);
                        const label = escapeHtml(typeof opt === 'object' ? (opt.label || opt.name || val) : opt);
                        html += `<div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="${colName}[]" id="custom_${prefix}_all_${fieldId}_${idx}" value="${val}">
                            <label class="form-check-label" for="custom_${prefix}_all_${fieldId}_${idx}">${label}</label>
                        </div>`;
                    });
                    html += `</div>`;
                } else if (['date', 'month', 'number', 'email', 'tel', 'range', 'time'].includes(fieldType)) {
                    html += `<input type="${fieldType}" name="${colName}" class="form-control" placeholder="${fieldName}" ${requiredAttr}>`;
                } else {
                    html += `<input type="text" name="${colName}" class="form-control" placeholder="${fieldName}" ${requiredAttr}>`;
                }
            } else {
                html += `<label class="col-form-label" style="padding-left: 30px;">${fieldName} ${requiredStar}</label>`;
                const fieldKey = field.field;
                const fieldType = field.field_type;
                const placeholder = escapeHtml(field.placeholder || 'Choose');
                const options = field.options || [];
                let dynamicOptions = [];
                let dependent = '';

                switch (fieldKey) {
                    case 'lead_source_id': dynamicOptions = leadSources; break;
                    case 'industry': dynamicOptions = industries; break;
                    case 'owner_id':
                    case 'assigned_to': dynamicOptions = owners; break;
                    case 'lead_status':
                    case 'lead_status_id': dynamicOptions = leadsStatus; break;
                    case 'country_id': dynamicOptions = countries; dependent = 'state_id'; break;
                    case 'state_id': dependent = 'city_id'; break;
                    case 'lead_type': dynamicOptions = [
                        {id: 'Direct Lead', name: 'Direct Lead'},
                        {id: 'Buy Lead', name: 'Buy Lead'},
                        {id: 'Call Lead', name: 'Call Lead'},
                        {id: 'Catalogue View', name: 'Catalogue View'}
                    ]; break;
                    case 'city_id': break;
                    case 'currency':
                        dynamicOptions = window.globalCurrencies.length ? window.globalCurrencies : options;
                        break;
                    default: if (options.length) dynamicOptions = options; break;
                }

                if (fieldType === 'text' || fieldType === 'email') {
                    html += `<input type="${fieldType === 'email' ? 'email' : 'text'}" name="${fieldKey}" class="form-control" placeholder="${placeholder}" ${requiredAttr}>`;
                } else if (fieldType === 'textarea') {
                    html += `<textarea name="${fieldKey}" class="form-control" rows="5" placeholder="${placeholder}" ${requiredAttr}></textarea>`;
                } else if (fieldType === 'select') {
                    if (fieldKey === 'owner_id' || fieldKey === 'assigned_to') {
                        html += `
                        <div class="mb-3">
                            <label for="${prefix}_department_${fieldKey}" class="form-label">Department</label>
                            <select class="form-select select ${prefix}-department-select" id="${prefix}_department_${fieldKey}" data-target="${fieldKey}">
                                <option value="">Select Department</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="${prefix}_designation_visible_${fieldKey}" class="form-label">Designation</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="${prefix}_designation_visible_${fieldKey}" placeholder="Select Designation" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                <input type="hidden" id="${prefix}_designation_${fieldKey}" class="${prefix}-designation-select" data-target="${fieldKey}">
                                <button class="btn btn-outline-secondary" type="button" onclick="if($('#${prefix}_department_${fieldKey}').val()) { window.openDesignationTree($('#${prefix}_department_${fieldKey}').val(), '#${prefix}_designation_${fieldKey}'); } else { alert('Please select a department first'); }"><i class="ti ti-sitemap"></i> Select</button>
                            </div>
                        </div>`;
                    }
                    
                    const isMultiple = (fieldKey === 'owner_id' || fieldKey === 'assigned_to') ? 'multiple' : '';
                    const nameAttr = fieldKey + (isMultiple ? '[]' : '');
                    const classAttr = `form-select dynamic-select ${isMultiple ? (prefix + '-owner-select') : ''}`;
                    const elementId = isEdit ? `edit_${fieldKey}` : fieldKey;
                    
                    html += `<select name="${nameAttr}" class="${classAttr}" id="${elementId}" data-dependent="${dependent}" data-placeholder="${placeholder}" ${requiredAttr} ${isMultiple}>
                        <option value="">${placeholder}</option>`;
                        
                    dynamicOptions.forEach(opt => {
                        const optId = escapeHtml(typeof opt === 'object' ? (opt.id || opt.value || '') : opt);
                        const optName = escapeHtml(typeof opt === 'object' ? (opt.name || opt.label || '') : opt);
                        const isSelected = (!isEdit && isMultiple && optId == authId) ? 'selected' : '';
                        let dataAttrs = '';
                        if (isMultiple) {
                            const depId = escapeHtml(opt.department_id || '');
                            const desId = escapeHtml(opt.designation_id || '');
                            dataAttrs = `data-department-id="${depId}" data-designation-id="${desId}"`;
                        }
                        html += `<option value="${optId}" ${dataAttrs} ${isSelected}>${optName}</option>`;
                    });
                    html += `</select>`;
                } else if (fieldType === 'radio' && dynamicOptions.length > 0) {
                    html += `<div>`;
                    dynamicOptions.forEach(opt => {
                        const val = escapeHtml(opt.id || opt.value);
                        const label = escapeHtml(opt.name || opt.label);
                        const idVal = String(val).replace(/[^a-zA-Z0-9]/g, '-').toLowerCase();
                        html += `<div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="${fieldKey}" id="${prefix}_${fieldKey}_${idVal}" value="${val}" ${requiredAttr}>
                            <label class="form-check-label" for="${prefix}_${fieldKey}_${idVal}">${label}</label>
                        </div>`;
                    });
                    html += `</div>`;
                }
            }
            html += `</div>`;
        });
        
        return html;
    }

    function renderAddLeadForm(resData) {
        return renderLeadForm(resData, false);
    }

    function renderEditLeadForm(resData) {
        return renderLeadForm(resData, true);
    }

    function ensureEditFormLoaded(callback) {
        const container = document.getElementById('sortable-fields-edit');
        if (!container) {
            if (callback) callback();
            return;
        }
        if (container.dataset.loaded === 'true') {
            if (callback) callback();
            return;
        }
        
        fetch("{{ route('leads.add-form-fields', ['box_slug' => request()->route('box_slug')]) }}")
            .then(response => response.json())
            .then(res => {
                if (res.status && res.data) {
                    container.innerHTML = renderEditLeadForm(res.data);
                    container.dataset.loaded = 'true';
                    
                    // Re-initialize select2 if dynamic-select is present
                    if (typeof jQuery !== 'undefined' && $.fn.select2) {
                        $(container).find('.dynamic-select').each(function() {
                            const $sel = $(this);
                            $sel.select2({
                                dropdownParent: $sel.closest('.offcanvas')
                            });
                        });
                    }
                    if (callback) callback();
                } else {
                    console.error("Failed to load edit form fields");
                }
            })
            .catch(error => {
                console.error("Error loading edit form fields:", error);
                container.innerHTML = '<div class="alert alert-danger">Failed to load edit form fields.</div>';
            });
    }

    // Dynamic Form Department/Designation/Owners filtering logic
    function loadFormDepartments($container) {
        const $deptSelects = $container.find('.add-department-select, .edit-department-select');
        if ($deptSelects.length === 0) return;

        const boxSlug = "{{ request()->route('box_slug') }}";
        if (!boxSlug) return;

        $.ajax({
            url: '/' + boxSlug + '/manage-department',
            method: 'GET',
            success: function (response) {
                if (response.status && response.data) {
                    $deptSelects.each(function() {
                        const $select = $(this);
                        const currentValue = $select.val();
                        $select.empty().append('<option value="">Select Department</option>');
                        response.data.forEach(dept => {
                            $select.append(`<option value="${dept.id}">${dept.name}</option>`);
                        });
                        if (currentValue) {
                            $select.val(currentValue);
                        }
                        if (typeof jQuery !== 'undefined' && $.fn.select2) {
                            $select.trigger('change.select2');
                        }
                    });
                }
            },
            error: function (xhr) {
                console.error("Failed to load departments in dynamic form:", xhr.responseText);
            }
        });
    }

    // Automatically load departments when dynamic fields are injected
    if (typeof window.MutationObserver !== 'undefined') {
        const formFieldObserver = new MutationObserver(function(mutationsList, observer) {
            for (const mutation of mutationsList) {
                if (mutation.type === 'childList') {
                    const $addSelects = $('.add-department-select, .edit-department-select');
                    let needsLoading = false;
                    $addSelects.each(function() {
                        if ($(this).children('option').length <= 1) {
                            needsLoading = true;
                        }
                    });
                    if (needsLoading) {
                        const container = mutation.target;
                        loadFormDepartments($(container));
                    }
                }
            }
        });

        $(document).ready(function() {
            const addContainer = document.getElementById('sortable-fields-add');
            const editContainer = document.getElementById('sortable-fields-edit');
            if (addContainer) {
                formFieldObserver.observe(addContainer, { childList: true, subtree: true });
                if (addContainer.dataset.loaded === 'true') {
                    loadFormDepartments($(addContainer));
                }
            }
            if (editContainer) {
                formFieldObserver.observe(editContainer, { childList: true, subtree: true });
                if (editContainer.dataset.loaded === 'true') {
                    loadFormDepartments($(editContainer));
                }
            }
        });
    }

    // Event handlers for department/designation changes on details page
    if (window.location.pathname.includes('leads-details') || $('#quickEditForm').length > 0) {
        $(document).on('change', '.add-department-select, .edit-department-select', function () {
            var targetField = $(this).data('target');
            var prefix = $(this).hasClass('add-department-select') ? 'add_' : 'edit_';
            $('#' + prefix + 'designation_' + targetField).val('').trigger('change');
            $('#' + prefix + 'designation_visible_' + targetField).val('');
            
            filterFormOwnersDetail($(this).closest('form'), targetField);

            var val = $(this).val();
            if (val && typeof window.openDesignationTree === 'function') {
                window.openDesignationTree(val, '#' + prefix + 'designation_' + targetField);
            }
        });

        $(document).on('change', '.add-designation-select, .edit-designation-select', function () {
            var targetField = $(this).data('target');
            filterFormOwnersDetail($(this).closest('form'), targetField);
        });
    }

    function filterFormOwnersDetail($form, targetField) {
        var prefix = $form.attr('id') === 'leadsForm' ? 'add_' : 'edit_';
        const idTarget = targetField.replace('[]', '');
        const deptId = $('#' + prefix + 'department_' + idTarget).val();
        const desigId = $('#' + prefix + 'designation_' + idTarget).val();
        const $select = $form.find('select[name="' + targetField + '"], select[name="' + targetField + '[]"]');

        fetchAndPopulateUsersDetail($select, deptId, desigId);
    }

    function fetchAndPopulateUsersDetail($select, deptId, desigId, callback) {
        if (!$select || $select.length === 0) return;

        const url = "{{ route('get.users.list', ['box_slug' => request()->route('box_slug')]) }}";
        const data = {
            department_id: deptId || '',
            designation_id: desigId || ''
        };

        $.ajax({
            url: url,
            method: 'GET',
            data: data,
            success: function (response) {
                const currentValue = $select.val();
                $select.empty();
                if (!$select.attr('multiple')) {
                    $select.append('<option value="">Choose</option>');
                }
                if (response.status && response.data) {
                    response.data.forEach(function (user) {
                        $select.append(`<option value="${user.id}">${user.name}</option>`);
                    });
                }
                if (currentValue) {
                    $select.val(currentValue);
                }
                if (typeof jQuery !== 'undefined' && $.fn.select2) {
                    $select.trigger('change.select2');
                }
                if (callback) callback();
            },
            error: function () {
                console.error('Failed to fetch filtered users');
            }
        });
    }
</script>
@endpush
@endonce
