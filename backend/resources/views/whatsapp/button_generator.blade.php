@extends('layouts.main')
@section('title', 'WhatsApp Button Generator')
@section('content')
<style>
.wa-generator-bg {
    background: #fcfcfc;
}
.wa-floating-btn-preview {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #4dc247;
    color: white;
    padding: 10px 20px;
    border-radius: 24px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    cursor: pointer;
    z-index: 9999;
    transition: all 0.3s ease;
}
.wa-floating-btn-preview img {
    width: 24px;
    height: 24px;
}
.wa-widget-preview {
    position: fixed;
    bottom: 90px;
    right: 30px;
    width: 320px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    overflow: hidden;
    z-index: 9999;
    display: none;
    flex-direction: column;
}
.wa-widget-header {
    background: #0A5F54;
    color: white;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.wa-widget-header img {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
}
.wa-widget-body {
    padding: 20px;
    background: #e5ddd5;
    height: 200px;
}
.wa-widget-message {
    background: white;
    padding: 12px;
    border-radius: 8px;
    border-top-left-radius: 0;
    font-size: 14px;
    color: #333;
    max-width: 85%;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}
.wa-widget-footer {
    padding: 15px;
    background: white;
    text-align: center;
}
.wa-widget-cta {
    display: inline-block;
    background: #4dc247;
    color: white;
    padding: 10px 20px;
    border-radius: 24px;
    text-decoration: none;
    font-weight: 500;
    width: 100%;
}
.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 5px;
}
.section-subtitle {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 20px;
}
.snippet-box {
    background: #2d2d2d;
    color: #f8f8f2;
    padding: 15px;
    border-radius: 8px;
    font-family: monospace;
    font-size: 13px;
    white-space: pre-wrap;
    max-height: 300px;
    overflow-y: auto;
}
@media (max-width: 768px) {
    .wa-widget-preview {
        width: calc(100vw - 40px);
        right: 20px !important;
        left: 20px !important;
    }
    .wa-floating-btn-preview {
        bottom: 20px !important;
        right: 20px !important;
        left: auto !important;
    }
    .wa-generator-bg .content {
        padding-bottom: 80px; /* Space so content isn't hidden behind widget */
    }
}
</style>

<div class="page-wrapper wa-generator-bg">
    <div class="content">
        
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-light border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <h3 class="text-success fw-bold mb-2">Free WhatsApp Button Generator by ZipZapGo</h3>
                        <p class="text-muted mb-2">Get your website leads to WhatsApp in a click by placing a free WhatsApp button on your website. Get the lead's names and numbers saved to your CRM and start building relationships effortlessly.</p>
                        <p class="fw-bold text-dark mb-0">Check live preview of your WhatsApp Button at Bottom-Right of this page</p>
                    </div>
                </div>
            </div>
        </div>

        <form id="buttonGeneratorForm">
            <!-- Section 1 -->
            <div class="row mb-5">
                <div class="col-md-4">
                    <h5 class="section-title">Configure WhatsApp Chat Button</h5>
                    <p class="section-subtitle">Personalize the pre-filled user message for your WhatsApp button</p>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium small">WhatsApp Phone Number <i class="ti ti-info-circle text-muted"></i></label>
                            <input type="text" class="form-control form-control-sm" id="btnPhone" placeholder="e.g. 919876543210" value="919876543210">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium small">Button Background <i class="ti ti-info-circle text-muted"></i></label>
                            <div class="input-group input-group-sm">
                                <input type="color" class="form-control form-control-color" id="btnColor" value="#4dc247" title="Choose color">
                                <input type="text" class="form-control" id="btnColorText" value="#4dc247">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium small">CTA Text <i class="ti ti-info-circle text-muted"></i></label>
                            <input type="text" class="form-control form-control-sm" id="btnCta" value="Chat with us">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-medium small">Margin Bottom(px) <i class="ti ti-info-circle text-muted"></i></label>
                            <input type="number" class="form-control form-control-sm" id="marginBottom" value="30">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-medium small">Margin Left(px) <i class="ti ti-info-circle text-muted"></i></label>
                            <input type="number" class="form-control form-control-sm" id="marginLeft" value="30">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-medium small">Margin Right(px) <i class="ti ti-info-circle text-muted"></i></label>
                            <input type="number" class="form-control form-control-sm" id="marginRight" value="30">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-medium small">Border Radius(px) <i class="ti ti-info-circle text-muted"></i></label>
                            <input type="number" class="form-control form-control-sm" id="borderRadius" value="24">
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-medium small">Default Pre-filled Message <i class="ti ti-info-circle text-muted"></i> 😋</label>
                            <textarea class="form-control form-control-sm" id="prefilledMessage" rows="3">Hi</textarea>
                            <small class="text-muted mt-1 d-block">You can use defaults - {{ '{' }}{{ '{' }}page_url{{ '}' }}{{ '}' }} & {{ '{' }}{{ '{' }}page_title{{ '}' }}{{ '}' }}</small>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-medium small d-block">Position <i class="ti ti-info-circle text-muted"></i></label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="btnPosition" id="posRight" value="right" checked>
                                <label class="form-check-label small" for="posRight">Bottom-Right</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="btnPosition" id="posLeft" value="left">
                                <label class="form-check-label small" for="posLeft">Bottom-Left</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Section 2 -->
            <div class="row my-5">
                <div class="col-md-4">
                    <h5 class="section-title">Chat Widget</h5>
                    <p class="section-subtitle">You can personalize the user message based on your website's URL</p>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium small">BrandName <i class="ti ti-info-circle text-muted"></i></label>
                            <input type="text" class="form-control form-control-sm" id="brandName" value="ZipZapGo">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium small">Brand Subtitle <i class="ti ti-info-circle text-muted"></i></label>
                            <input type="text" class="form-control form-control-sm" id="brandSubtitle" value="Typically replies instantly">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium small">Brand Color <i class="ti ti-info-circle text-muted"></i></label>
                            <div class="input-group input-group-sm">
                                <input type="color" class="form-control form-control-color" id="brandColor" value="#0A5F54" title="Choose color">
                                <input type="text" class="form-control" id="brandColorText" value="#0A5F54">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium small">Brand Image Url <i class="ti ti-info-circle text-muted"></i></label>
                            <input type="text" class="form-control form-control-sm" id="brandImage" placeholder="Enter image url" value="https://cdn-icons-png.flaticon.com/512/3135/3135715.png">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium small">Widget CTA Text <i class="ti ti-info-circle text-muted"></i></label>
                            <input type="text" class="form-control form-control-sm" id="widgetCta" value="Start chat">
                        </div>

                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-medium small">Default On-screen Message <i class="ti ti-info-circle text-muted"></i> 😋</label>
                            <textarea class="form-control form-control-sm" id="widgetMessage" rows="3">Hi,&#10;How can I help you ?</textarea>
                            <small class="text-muted mt-1 d-block">You can use defaults - {{ '{' }}{{ '{' }}page_url{{ '}' }}{{ '}' }} & {{ '{' }}{{ '{' }}page_title{{ '}' }}{{ '}' }}</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium small d-block">Open widget on Mobile screen <i class="ti ti-info-circle text-muted"></i></label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mobileWidget" id="mobYes" value="yes" checked>
                                <label class="form-check-label small" for="mobYes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mobileWidget" id="mobNo" value="no">
                                <label class="form-check-label small" for="mobNo">No</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Area -->
            <div class="row my-4">
                <div class="col-12 text-end">
                    <button type="button" class="btn btn-success px-5 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#snippetModal" onclick="generateSnippet()">Generate Snippet</button>
                </div>
            </div>

        </form>

        <!-- Saved Widgets Table -->
        @if(isset($savedWidgets) && $savedWidgets->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold">Saved Chat Widgets</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Brand</th>
                                        <th>Phone Number</th>
                                        <th>Configured Colors</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($savedWidgets as $savedWidget)
                                    @php $config = $savedWidget->config ?? []; @endphp
                                    <tr>
                                        <td class="text-muted small">{{ $savedWidget->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if(isset($config['brand_image']))
                                                    <img src="{{ $config['brand_image'] }}" width="24" height="24" class="rounded-circle me-2">
                                                @endif
                                                {{ $config['brand_name'] ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td>{{ $savedWidget->phone_number }}</td>
                                        <td>
                                            @if(isset($config['btn_color']))
                                                <span class="badge" style="background-color: {{ $config['btn_color'] }}">{{ $config['btn_color'] }}</span>
                                            @endif
                                            @if(isset($config['brand_color']))
                                                <span class="badge" style="background-color: {{ $config['brand_color'] }}">{{ $config['brand_color'] }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <!-- The script is stored in generated_url_or_code -->
                                            <button class="btn btn-sm btn-outline-primary" onclick="copyText(`{{ str_replace('`', '\`', $savedWidget->generated_url_or_code) }}`)"><i class="ti ti-copy"></i> Copy Code</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Snippet Modal -->
<div class="modal fade" id="snippetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Your WhatsApp Widget Snippet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Copy and paste this code right before the closing <code>&lt;/body&gt;</code> tag of your website.</p>
                <div class="snippet-box" id="codeSnippet"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="copySnippet()"><i class="ti ti-copy me-1"></i> Copy Code</button>
            </div>
        </div>
    </div>
</div>

<!-- Live Preview Elements -->
<div class="wa-floating-btn-preview" id="previewBtn" onclick="toggleWidget()">
    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WA">
    <span id="previewBtnText">Chat with us</span>
</div>

<div class="wa-widget-preview" id="previewWidget">
    <div class="wa-widget-header" id="previewWidgetHeader">
        <img src="" id="previewBrandImg" alt="Brand">
        <div>
            <div class="fw-bold" id="previewBrandName">ZipZapGo</div>
            <div class="small" id="previewBrandSub" style="opacity:0.8;">Typically replies instantly</div>
        </div>
        <div class="ms-auto" style="cursor:pointer;" onclick="toggleWidget()">
            <i class="ti ti-x"></i>
        </div>
    </div>
    <div class="wa-widget-body">
        <div class="wa-widget-message">
            <div class="fw-bold small text-muted mb-1" id="previewMsgBrand">ZipZapGo</div>
            <div id="previewMsgText" style="white-space:pre-wrap;">Hi, How can I help you?</div>
        </div>
    </div>
    <div class="wa-widget-footer">
        <a href="#" class="wa-widget-cta" id="previewWidgetCta">Start chat <i class="fa-solid fa-paper-plane ms-1"></i></a>
    </div>
</div>

<script>
    // Sync Color Pickers with Text Inputs
    document.getElementById('btnColor').addEventListener('input', function() {
        document.getElementById('btnColorText').value = this.value;
        updatePreview();
    });
    document.getElementById('btnColorText').addEventListener('input', function() {
        document.getElementById('btnColor').value = this.value;
        updatePreview();
    });
    document.getElementById('brandColor').addEventListener('input', function() {
        document.getElementById('brandColorText').value = this.value;
        updatePreview();
    });
    document.getElementById('brandColorText').addEventListener('input', function() {
        document.getElementById('brandColor').value = this.value;
        updatePreview();
    });

    // Add event listeners to all inputs to update preview live
    const inputs = document.querySelectorAll('#buttonGeneratorForm input, #buttonGeneratorForm textarea');
    inputs.forEach(input => {
        input.addEventListener('input', updatePreview);
        if(input.type === 'radio') input.addEventListener('change', updatePreview);
    });

    function toggleWidget() {
        const widget = document.getElementById('previewWidget');
        if (widget.style.display === 'flex') {
            widget.style.display = 'none';
        } else {
            widget.style.display = 'flex';
        }
    }

    function updatePreview() {
        // Button Configs
        const btnColor = document.getElementById('btnColor').value;
        const btnCta = document.getElementById('btnCta').value;
        const mb = document.getElementById('marginBottom').value;
        const ml = document.getElementById('marginLeft').value;
        const mr = document.getElementById('marginRight').value;
        const br = document.getElementById('borderRadius').value;
        const pos = document.querySelector('input[name="btnPosition"]:checked').value;

        const previewBtn = document.getElementById('previewBtn');
        const previewWidget = document.getElementById('previewWidget');

        // Apply Button Styles
        previewBtn.style.background = btnColor;
        document.getElementById('previewBtnText').innerText = btnCta;
        previewBtn.style.borderRadius = br + 'px';
        previewBtn.style.bottom = mb + 'px';
        
        if (pos === 'right') {
            previewBtn.style.right = mr + 'px';
            previewBtn.style.left = 'auto';
            previewWidget.style.right = mr + 'px';
            previewWidget.style.left = 'auto';
        } else {
            previewBtn.style.left = ml + 'px';
            previewBtn.style.right = 'auto';
            previewWidget.style.left = ml + 'px';
            previewWidget.style.right = 'auto';
        }

        // Widget Configs
        const bName = document.getElementById('brandName').value;
        const bSub = document.getElementById('brandSubtitle').value;
        const bColor = document.getElementById('brandColor').value;
        const bImg = document.getElementById('brandImage').value || 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
        const wCta = document.getElementById('widgetCta').value;
        const wMsg = document.getElementById('widgetMessage').value;

        // Apply Widget Styles
        document.getElementById('previewWidgetHeader').style.background = bColor;
        document.getElementById('previewBrandName').innerText = bName;
        document.getElementById('previewMsgBrand').innerText = bName;
        document.getElementById('previewBrandSub').innerText = bSub;
        document.getElementById('previewBrandImg').src = bImg;
        document.getElementById('previewWidgetCta').innerText = wCta;
        document.getElementById('previewWidgetCta').style.background = btnColor;
        document.getElementById('previewMsgText').innerText = wMsg;
        previewWidget.style.bottom = (parseInt(mb) + 60) + 'px';
    }

    function generateSnippet() {
        const phone = document.getElementById('btnPhone').value.replace(/\D/g,'');
        const message = document.getElementById('prefilledMessage').value.replace(/'/g, "\\'");
        
        const btnColor = document.getElementById('btnColor').value;
        const btnCta = document.getElementById('btnCta').value;
        const mb = document.getElementById('marginBottom').value;
        const ml = document.getElementById('marginLeft').value;
        const mr = document.getElementById('marginRight').value;
        const br = document.getElementById('borderRadius').value;
        const pos = document.querySelector('input[name="btnPosition"]:checked').value;

        const bName = document.getElementById('brandName').value.replace(/'/g, "\\'");
        const bSub = document.getElementById('brandSubtitle').value.replace(/'/g, "\\'");
        const bColor = document.getElementById('brandColor').value;
        const bImg = document.getElementById('brandImage').value;
        const wCta = document.getElementById('widgetCta').value;
        const wMsg = document.getElementById('widgetMessage').value.replace(/\n/g, "<br>").replace(/'/g, "\\'");
        const mobYes = document.querySelector('input[name="mobileWidget"]:checked').value === 'yes';

        let jsCode = `<!-- ZipZapGo WhatsApp Widget -->
<script>
(function() {
    var waPhone = "${phone}";
    var waMessage = "${message}";
    var btnPosition = "${pos}";
    var mobEnabled = ${mobYes};
    
    if(!mobEnabled && window.innerWidth <= 768) return;

    var style = document.createElement('style');
    style.innerHTML = \`
        .zzg-wa-btn { position: fixed; bottom: ${mb}px; \${btnPosition === 'right' ? 'right: ${mr}px;' : 'left: ${ml}px;'} background: ${btnColor}; color: white; padding: 10px 20px; border-radius: ${br}px; display: flex; align-items: center; gap: 8px; font-family: sans-serif; font-size: 14px; font-weight: 500; box-shadow: 0 4px 10px rgba(0,0,0,0.15); cursor: pointer; z-index: 2147483647; transition: all 0.3s ease; }
        .zzg-wa-btn img { width: 24px; height: 24px; }
        .zzg-wa-widget { position: fixed; bottom: ${parseInt(mb) + 60}px; \${btnPosition === 'right' ? 'right: ${mr}px;' : 'left: ${ml}px;'} width: 320px; background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); overflow: hidden; z-index: 2147483647; display: none; flex-direction: column; font-family: sans-serif; }
        .zzg-wa-widget-header { background: ${bColor}; color: white; padding: 15px; display: flex; align-items: center; gap: 12px; }
        .zzg-wa-widget-header img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; }
        .zzg-wa-widget-body { padding: 20px; background: #e5ddd5; min-height: 150px; }
        .zzg-wa-msg { background: white; padding: 12px; border-radius: 8px; border-top-left-radius: 0; font-size: 14px; color: #333; max-width: 85%; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
        .zzg-wa-widget-footer { padding: 15px; background: white; text-align: center; }
        .zzg-wa-cta { display: inline-block; background: ${btnColor}; color: white; padding: 10px 20px; border-radius: 24px; text-decoration: none; font-weight: 500; width: 100%; box-sizing: border-box; }
    \`;
    document.head.appendChild(style);

    var container = document.createElement('div');
    container.innerHTML = \`
        <div class="zzg-wa-btn" id="zzgWaBtn">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WA">
            <span>${btnCta}</span>
        </div>
        <div class="zzg-wa-widget" id="zzgWaWidget">
            <div class="zzg-wa-widget-header">
                <img src="${bImg}" alt="Brand">
                <div style="flex-grow:1;">
                    <div style="font-weight:bold; font-size:16px;">${bName}</div>
                    <div style="font-size:12px; opacity:0.8;">${bSub}</div>
                </div>
                <div style="cursor:pointer; font-size:20px; line-height:1;" id="zzgWaClose">×</div>
            </div>
            <div class="zzg-wa-widget-body">
                <div class="zzg-wa-msg">
                    <div style="font-weight:bold; font-size:12px; color:#999; margin-bottom:4px;">${bName}</div>
                    <div>${wMsg}</div>
                </div>
            </div>
            <div class="zzg-wa-widget-footer">
                <a href="#" class="zzg-wa-cta" id="zzgWaLink" target="_blank">${wCta}</a>
            </div>
        </div>
    \`;
    document.body.appendChild(container);

    var btn = document.getElementById('zzgWaBtn');
    var widget = document.getElementById('zzgWaWidget');
    var close = document.getElementById('zzgWaClose');
    var link = document.getElementById('zzgWaLink');

    btn.onclick = function() { widget.style.display = widget.style.display === 'flex' ? 'none' : 'flex'; };
    close.onclick = function() { widget.style.display = 'none'; };
    
    // Process variables in message
    var finalMessage = waMessage.replace('{{ '{' }}{{ '{' }}page_url{{ '}' }}{{ '}' }}', window.location.href).replace('{{ '{' }}{{ '{' }}page_title{{ '}' }}{{ '}' }}', document.title);
    link.href = 'https://wa.me/' + waPhone + '?text=' + encodeURIComponent(finalMessage);
})();
<\/script>
<!-- End ZipZapGo WhatsApp Widget -->`;

        document.getElementById('codeSnippet').textContent = jsCode;

        // Save to Database via AJAX
        fetch("{{ route('whatsapp.generator.save') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                _token: '{{ csrf_token() }}',
                box: '{{ isset($box) && $box ? $box->slug : "" }}',
                type: 'button_widget',
                name: 'Website Widget',
                phone_number: '+' + phone,
                custom_message: message,
                config: {
                    btn_color: btnColor,
                    btn_cta: btnCta,
                    margin_bottom: mb,
                    margin_left: ml,
                    margin_right: mr,
                    border_radius: br,
                    position: pos,
                    brand_name: bName,
                    brand_subtitle: bSub,
                    brand_color: bColor,
                    brand_image: bImg,
                    widget_cta: wCta,
                    widget_message: wMsg,
                    mobile_enabled: mobYes
                },
                generated_url_or_code: jsCode
            })
        }).then(res => res.json()).then(data => {
            if(data.status === 'success') {
                console.log('Widget saved to database.');
            }
        });
    }

    function copySnippet() {
        const code = document.getElementById('codeSnippet').textContent;
        copyText(code);
    }

    function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                if(typeof toastr !== 'undefined') toastr.success('Code copied!');
            });
        } else {
            const input = document.createElement('textarea');
            input.value = text;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            if(typeof toastr !== 'undefined') toastr.success('Code copied!');
        }
    }

    // Initialize
    updatePreview();
</script>
@endsection
