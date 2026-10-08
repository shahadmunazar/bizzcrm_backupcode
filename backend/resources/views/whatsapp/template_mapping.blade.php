@extends('layouts.main')
@section('title', 'Template Variable Mapping')
@section('content')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

<div class="page-wrapper">
    <div class="content">

        <div class="row">
            <div class="col-md-12">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <h4 class="page-title">Template Variable Mapping</h4>
                        </div>
                        <div class="col-8 text-end">
                            <div class="head-icons">
                                <a href="{{ box_route('whatsapp.create_template') }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Back">
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
                                <form id="mappingForm">
                                    @csrf
                                    <div>
                                        <div class="row">
                                            <div class="col-md-12 wapp-template-message">
                                                
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-4">
                                                            <label class="col-form-label fw-700">Template Category</label>
                                                            <div class="form-control bg-light">{{ $template->category }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-4">
                                                            <label class="col-form-label">Template Language</label>
                                                            <div class="form-control bg-light">{{ $template->language }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-4">
                                                    <label class="col-form-label">Template Name</label>
                                                    <div class="form-control bg-light">{{ $template->name }}</div>
                                                </div>

                                                <div class="mb-4">
                                                    <label class="col-form-label">Template Content</label>
                                                    <div class="p-3 bg-light rounded border msgdisarea">
                                                        @foreach($template->components as $component)
                                                            @if($component['type'] == 'HEADER')
                                                                <div class="fw-bold mb-1 dynamic-content-part" data-original="{{ $component['text'] ?? '' }}">{{ $component['text'] ?? '' }}</div>
                                                            @elseif($component['type'] == 'BODY')
                                                                <div class="mb-1 dynamic-content-part" style="white-space: pre-wrap;" data-original="{{ $component['text'] ?? '' }}">{{ $component['text'] ?? '' }}</div>
                                                            @elseif($component['type'] == 'FOOTER')
                                                                <div class="text-muted small mt-2 dynamic-content-part" data-original="{{ $component['text'] ?? '' }}">{{ $component['text'] ?? '' }}</div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <label class="col-form-label fw-700 mt-3 mb-3">Map Variables to CRM Fields</label>
                                                
                                                @php
                                                    $savedMapping = $template->params ?? [];
                                                @endphp

                                                @foreach($variables as $var)
                                                <div class="col-md-12">
                                                    <div class="mb-3 row align-items-center">
                                                        <label class="col-md-2 text-center"><strong class="variable-label" data-original="&#123;&#123;{{ $var }}&#125;&#125;">&#123;&#123;{{ $var }}&#125;&#125;</strong></label>
                                                        <div class="col-md-10">
                                                            <select class="form-control mapping-select" name="mapping[{{ $var }}]" data-selected="{{ $savedMapping[$var] ?? '' }}" required>
                                                                <option value="">Loading fields...</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-end mt-4">
                                        <button type="submit" class="btn btn-primary px-4" id="btnSaveMapping">Save Mappings</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-sm-4">
                                <div class="mb-3 sticky-top" style="top: 20px;">
                                    <label class="col-form-label fw-700">Template Preview</label>
                                    <p class="fs-13 mb-3 text-muted">Your template message preview. It will update as you fill in the values in the form</p>
                                    
                                    <div class="whatsapp-preview-container rounded-4 shadow-sm overflow-hidden border mb-3">
                                        <div class="preview-header bg-dark p-3 d-flex justify-content-between align-items-center" style="position: relative; height: 60px;">
                                            <h5 class="text-white mb-0 fw-bold">WhatsApp</h5>
                                            <div class="whatsapp-badge-icon" style="position: absolute; right: 15px; top: 10px;">
                                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border: 4px solid #fff;">
                                                    <i class="ti ti-brand-whatsapp text-white fs-30"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="preview-body p-4" style="background-color: #f0f2f5; min-height: 250px;">
                                            <div class="preview-bubble p-3 rounded-4 shadow-sm" style="background-color: #e9edef; position: relative;">
                                                <div id="previewContent" class="fs-16" style="white-space: pre-wrap; color: #111b21;">
                                                    @foreach($template->components as $component)
                                                        @if($component['type'] == 'HEADER' && isset($component['text']))
                                                            <div class="fw-bold mb-2 fs-18">{{ $component['text'] }}</div>
                                                        @endif
                                                        @if($component['type'] == 'BODY')
                                                            {{ $component['text'] ?? '' }}
                                                        @endif
                                                        @if($component['type'] == 'FOOTER' && isset($component['text']))
                                                            <div class="text-muted fs-12 mt-2">{{ $component['text'] }}</div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <div class="text-end mt-1">
                                                    <small class="text-muted" style="font-size: 10px;">{{ now()->format('H:i') }} <i class="ti ti-check-double text-info"></i></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-muted small px-1">Disclaimer: This is just a graphical representation of the message that will be delivered. Actual message will consist of media selected and may appear different.</p>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function() {
    // Configure toastr
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };
    // Show session toasts if they exist
    @if(Session::has('success'))
        toastr.success("{{ Session::get('success') }}");
    @endif
    @if(Session::has('error'))
        toastr.error("{{ Session::get('error') }}");
    @endif
    @if(Session::has('message'))
        toastr.info("{{ Session::get('message') }}");
    @endif
    var rawTemplateData = @json($template->components);
    
    function refreshDynamicPreview() {
        var bodyPart = '';
        var headerPart = '';
        var footerPart = '';

        rawTemplateData.forEach(function(comp) {
            if (comp.type === 'HEADER' && comp.text) {
                headerPart = comp.text;
            } else if (comp.type === 'BODY') {
                bodyPart = comp.text;
            } else if (comp.type === 'FOOTER') {
                footerPart = comp.text;
            }
        });

        $('.mapping-select').each(function() {
            var $sel = $(this);
            var m = $sel.attr('name').match(/\[(.*?)\]/);
            if (!m) return;
            
            var varNum = m[1];
            var searchStr = '{' + '{' + varNum + '}' + '}';
            var fieldLabel = $sel.find('option:selected').text();
            
            if ($sel.val()) {
                var badge = '<span class="badge bg-primary-transparent text-primary px-1">[' + fieldLabel + ']</span>';
                bodyPart = bodyPart.split(searchStr).join(badge);
                headerPart = headerPart.split(searchStr).join(badge);
            }
        });

        // Update preview bubble
        var previewHtml = '';
        if (headerPart) {
            previewHtml += '<div class="fw-bold mb-2 fs-18 border-bottom pb-1">' + headerPart + '</div>';
        }
        previewHtml += '<div class="body-text">' + bodyPart + '</div>';
        if (footerPart) {
            previewHtml += '<div class="text-muted fs-12 mt-2 border-top pt-1">' + footerPart + '</div>';
        }
        $('#previewContent').html(previewHtml);

        // Update static content display too
        $('.dynamic-content-part').each(function() {
            var $el = $(this);
            var content = $el.attr('data-original');
            
            $('.mapping-select').each(function() {
                var $s = $(this);
                var m = $s.attr('name').match(/\[(.*?)\]/);
                if (!m) return;
                var n = m[1];
                var target = '{' + '{' + n + '}' + '}';
                var label = $s.find('option:selected').text();
                
                if ($s.val()) {
                    var b = '<span class="badge bg-primary-transparent text-primary px-1">[' + label + ']</span>';
                    content = content.split(target).join(b);
                }
            });
            $el.html(content);
        });
    }

    function initMappingOptions() {
        $.ajax({
            url: "{{ box_route('whatsapp.business.template.fields') }}",
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var system = response.data.system;
                    var custom = response.data.custom;
                    
                    var optionsHtml = '<option value="">-- Select Field --</option>';
                    
                    if (system && system.length > 0) {
                        optionsHtml += '<optgroup label="System Fields">';
                        system.forEach(function(field) {
                            optionsHtml += '<option value="' + field.key + '">' + field.label + '</option>';
                        });
                        optionsHtml += '</optgroup>';
                    }
                    
                    if (custom && custom.length > 0) {
                        optionsHtml += '<optgroup label="Custom Fields">';
                        custom.forEach(function(field) {
                            optionsHtml += '<option value="' + field.key + '">' + field.label + '</option>';
                        });
                        optionsHtml += '</optgroup>';
                    }
                    
                    $('.mapping-select').each(function() {
                        var $s = $(this);
                        var selectedVal = $s.attr('data-selected');
                        $s.html(optionsHtml);
                        if (selectedVal) {
                            $s.val(selectedVal);
                        }
                    });

                    refreshDynamicPreview();
                } else {
                    toastr.error('Failed to load mapping fields.');
                }
            },
            error: function() {
                toastr.error('Error connecting to field API.');
            }
        });
    }

    initMappingOptions();

    $(document).on('change', '.mapping-select', function() {
        refreshDynamicPreview();
    });

    $('#mappingForm').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btnSaveMapping');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
        
        $.ajax({
            url: "{{ box_route('whatsapp.business.template.save_mapping', ['id' => $template->id]) }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Saved successfully');
                } else {
                    toastr.error(response.message || 'Failed to save');
                }
            },
            error: function() {
                toastr.error('Error connecting to save API.');
            },
            complete: function() {
                $btn.prop('disabled', false).html('Save Mappings');
            }
        });
    });
});
</script>
@endpush
