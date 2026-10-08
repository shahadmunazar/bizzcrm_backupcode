<div id="sortable-fields-add" class="sortable-container">
    @foreach($mergedFields as $field)
        <div class="mb-3 draggable-field border p-3 rounded position-relative" data-id="{{ $field->id }}"
            data-type="{{ $field->is_custom ? 'custom' : 'system' }}" style="cursor: move;"
            id="{{ (!$field->is_custom && ($field->field == 'lead_sub_status_id' || $field->field == 'lead_sub_status')) ? 'lead_sub_status_wrapper' : '' }}">
            <div class="drag-handle position-absolute top-0 start-0 p-2" style="cursor: grab;">
                <i class="ti ti-grip-vertical text-muted"></i>
            </div>
            <div class="error-msg text-danger fw-semibold mb-1" style="display:none; font-size: 13px; padding-left: 30px;">
                <i class="ti ti-alert-circle me-1"></i> <span class="error-text">This field is required</span>
            </div>

            @if($field->is_custom)
                {{-- Custom Field Logic --}}
                <label class="col-form-label" style="padding-left: 30px;">
                    {{ $field->name }}
                    @if($field->required) <span class="text-danger">*</span> @endif
                </label>
                @php
                    $isRequired = $field->required ? 'data-required=true' : '';
                    $fieldType = $field->type ?? 'text';
                    $params = $field->params ?? [];
                    $options = is_array($params) && isset($params['options']) ? $params['options'] : (is_array($params) ? $params : []);
                @endphp

                @if($fieldType === 'textarea')
                    <textarea name="{{ $field->col_name }}" class="form-control" rows="3"
                        placeholder="{{ $field->name }}" {{ $isRequired }}></textarea>
                @elseif($fieldType === 'select')
                    <select name="{{ $field->col_name }}" class="form-select select" {{ $isRequired }}>
                        <option value="">Select {{ $field->name }}</option>
                        @foreach($options as $option)
                            @php
                                $val = is_array($option) ? ($option['value'] ?? $option['id'] ?? '') : $option;
                                $label = is_array($option) ? ($option['label'] ?? $option['name'] ?? $val) : $option;
                            @endphp
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                @elseif($fieldType === 'radio')
                    <div class="mt-2" style="padding-left: 30px;">
                        @foreach($options as $option)
                            @php
                                $val = is_array($option) ? ($option['value'] ?? $option['id'] ?? '') : $option;
                                $label = is_array($option) ? ($option['label'] ?? $option['name'] ?? $val) : $option;
                            @endphp
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="{{ $field->col_name }}"
                                    id="custom_add_all_{{ $field->id }}_{{ $loop->index }}" value="{{ $val }}" {{ $isRequired }}>
                                <label class="form-check-label"
                                    for="custom_add_all_{{ $field->id }}_{{ $loop->index }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                @elseif($fieldType === 'checkbox' || $fieldType === 'multi_checkbox')
                    <div class="mt-2" style="padding-left: 30px;">
                        @foreach($options as $option)
                            @php
                                $val = is_array($option) ? ($option['value'] ?? $option['id'] ?? '') : $option;
                                $label = is_array($option) ? ($option['label'] ?? $option['name'] ?? $val) : $option;
                            @endphp
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="{{ $field->col_name }}[]"
                                    id="custom_add_all_{{ $field->id }}_{{ $loop->index }}" value="{{ $val }}">
                                <label class="form-check-label"
                                    for="custom_add_all_{{ $field->id }}_{{ $loop->index }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                @elseif(in_array($fieldType, ['date', 'month', 'number', 'email', 'tel', 'range', 'time']))
                    <input type="{{ $fieldType }}" name="{{ $field->col_name }}" class="form-control"
                        placeholder="{{ $field->name }}" {{ $isRequired }}>
                @else
                    <input type="text" name="{{ $field->col_name }}" class="form-control"
                        placeholder="{{ $field->name }}" {{ $isRequired }}>
                @endif
            @else
                {{-- System Field Logic --}}
                <label class="col-form-label" style="padding-left: 30px;">
                    {{ $field->name }}
                    @if ($field->is_required) <span class="text-danger">*</span> @endif
                </label>

                @php
                    $options = $field->options;
                    $isRequired = $field->is_required ? 'data-required=true' : '';
                    $placeholder = $field->placeholder ?? 'Choose';
                    $dynamicOptions = [];

                    switch ($field->field) {
                        case 'lead_source_id':
                            $dynamicOptions = $leadSources ?? [];
                            break;
                        case 'industry':
                            $dynamicOptions = $industries ?? [];
                            break;
                        case 'owner_id':
                        case 'assigned_to':
                            $dynamicOptions = $owners ?? [];
                            break;
                        case 'lead_status':
                        case 'lead_status_id':
                            $dynamicOptions = $leadsStatus ?? [];
                            break;
                        case 'lead_sub_status':
                        case 'lead_sub_status_id':
                            $dynamicOptions = []; // Initially empty, populated by JS
                            break;
                        case 'country_id':
                            $dynamicOptions = $countries ?? [];
                            break;
                        case 'lead_type':
                            $dynamicOptions = [
                                ['id' => 'Direct Lead', 'name' => 'Direct Lead'],
                                ['id' => 'Buy Lead', 'name' => 'Buy Lead'],
                                ['id' => 'Call Lead', 'name' => 'Call Lead'],
                                ['id' => 'Catalogue View', 'name' => 'Catalogue View']
                            ];
                            break;
                        case 'state_id':
                        case 'city_id':
                            $dynamicOptions = [];
                            break;
                        default:
                            if (!empty($options)) {
                                $dynamicOptions = $options;
                            }
                            break;
                    }
                    $dependent = '';
                    if ($field->field == 'country_id') {
                        $dependent = 'state_id';
                    } elseif ($field->field == 'state_id') {
                        $dependent = 'city_id';
                    }
                @endphp

                @if ($field->field_type === 'text' || $field->field_type === 'email')
                    <input type="{{ $field->field_type === 'email' ? 'email' : 'text' }}" name="{{ $field->field }}"
                        class="form-control" placeholder="{{ $placeholder }}" {{ $isRequired }}>
                @elseif($field->field_type === 'textarea')
                    <textarea name="{{ $field->field }}" class="form-control" rows="5" placeholder="{{ $placeholder }}"
                        {{ $isRequired }}></textarea>
                @elseif($field->field_type === 'select')
                    @if($field->field === 'owner_id' || $field->field === 'assigned_to')
                        <div class="mb-3">
                            <label for="add_department_{{ $field->field }}" class="form-label">Department</label>
                            <select class="form-select select add-department-select" id="add_department_{{ $field->field }}"
                                data-target="{{ $field->field }}">
                                <option value="">Select Department</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="add_designation_visible_{{ $field->field }}" class="form-label">Designation</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="add_designation_visible_{{ $field->field }}"
                                    placeholder="Select Designation" readonly
                                    style="background-color: #f8f9fa; cursor: not-allowed;">
                                <input type="hidden" id="add_designation_{{ $field->field }}" class="add-designation-select"
                                    data-target="{{ $field->field }}">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="if($('#add_department_{{ $field->field }}').val()) { window.openDesignationTree($('#add_department_{{ $field->field }}').val(), '#add_designation_{{ $field->field }}'); } else { alert('Please select a department first'); }"><i
                                        class="ti ti-sitemap"></i> Select</button>
                            </div>
                        </div>
                    @endif
                    <select
                        name="{{ $field->field }}{{ ($field->field === 'owner_id' || $field->field === 'assigned_to') ? '[]' : '' }}"
                        class="form-select dynamic-select @if($field->field === 'owner_id' || $field->field === 'assigned_to') add-owner-select @endif"
                        id="{{ $field->field }}" data-dependent="{{ $dependent }}" data-placeholder="{{ $placeholder }}"
                        {{ $isRequired }} {{ ($field->field === 'owner_id' || $field->field === 'assigned_to') ? 'multiple' : '' }}>
                        <option value="">{{ $placeholder }}</option>
                        @foreach ($dynamicOptions as $option)
                            @php
                                $optId = is_object($option) ? $option->id : ($option['id'] ?? $option['value'] ?? '');
                                $isSelected = ($field->field === 'owner_id' || $field->field === 'assigned_to') && $optId == auth()->id() ? 'selected' : '';
                            @endphp
                            <option
                                value="{{ $optId }}"
                                @if($field->field === 'owner_id' || $field->field === 'assigned_to')
                                    data-department-id="{{ is_object($option) ? $option->department_id : ($option['department_id'] ?? '') }}"
                                    data-designation-id="{{ is_object($option) ? $option->designation_id : ($option['designation_id'] ?? '') }}"
                                @endif {{ $isSelected }}>
                                {{ is_object($option) ? $option->name : ($option['name'] ?? $option['label'] ?? '') }}
                            </option>
                        @endforeach
                    </select>
                @elseif($field->field_type === 'radio' && !empty($dynamicOptions))
                    <div>
                        @foreach ($dynamicOptions as $option)
                            @php
                                $value = $option['id'] ?? $option['value'];
                                $label = $option['name'] ?? $option['label'];
                            @endphp
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="{{ $field->field }}"
                                    id="{{ $field->field . '_' . $value }}" value="{{ $value }}" {{ $isRequired }}>
                                <label class="form-check-label"
                                    for="{{ $field->field . '_' . $value }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    @endforeach
</div>
