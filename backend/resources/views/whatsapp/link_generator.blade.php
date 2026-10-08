@extends('layouts.main')
@section('title', 'WhatsApp Link Generator')
@section('content')
<style>
.wa-generator-bg {
    background: #fcfcfc;
}
.wa-preview-container {
    width: 320px;
    max-width: 100%;
    height: 580px;
    max-height: 80vh;
    background: #e5ddd5;
    border-radius: 30px;
    border: 10px solid #f8f9fa;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
    margin: 0 auto;
}
@media (max-width: 768px) {
    .wa-preview-container {
        height: 500px;
    }
}
.wa-preview-header {
    background: #f0f0f0;
    padding: 15px;
    display: flex;
    align-items: center;
    border-bottom: 1px solid #ddd;
}
.wa-preview-body {
    padding: 20px;
    height: calc(100% - 130px);
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
}
.wa-preview-footer {
    background: #f0f0f0;
    padding: 10px 15px;
    position: absolute;
    bottom: 0;
    width: 100%;
}
.wa-input-box {
    background: #fff;
    border-radius: 20px;
    padding: 8px 15px;
    color: #999;
    font-size: 13px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.wa-message-bubble {
    background: #dcf8c6;
    padding: 10px 15px;
    border-radius: 15px;
    border-top-right-radius: 0;
    font-size: 14px;
    color: #303030;
    max-width: 85%;
    align-self: flex-end;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    word-wrap: break-word;
    white-space: pre-wrap;
    display: none;
}
.wa-message-bubble.active {
    display: block;
}
</style>

<div class="page-wrapper wa-generator-bg">
    <div class="content">
        
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <div class="card bg-light border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 text-center">
                        <h3 class="text-success fw-bold mb-2">Free WhatsApp link generator by ZipZapGo</h3>
                        <p class="text-muted mb-0">Drive users to WhatsApp in a click! Create FREE WhatsApp Links & WhatsApp QR Codes for your business in 3 simple steps, share it & boost your business growth.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-6 mb-4">
                <div class="form-group mb-4">
                    <label class="fw-bold mb-2">WhatsApp Phone Number</label>
                    <p class="text-muted small mb-2">Select your country code & Type your WhatsApp phone number</p>
                    <div class="input-group">
                        <select class="form-select" id="countryCode" style="max-width: 120px;">
                            <option value="91">India (+91)</option>
                            <option value="1">USA (+1)</option>
                            <option value="44">UK (+44)</option>
                            <option value="971">UAE (+971)</option>
                            <option value="61">Australia (+61)</option>
                            <!-- Add more if needed -->
                        </select>
                        <input type="text" class="form-control" id="phoneNumber" placeholder="Your phone number goes in here" oninput="updatePreview()">
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="fw-bold mb-2">Custom Message 😋</label>
                    <p class="text-muted small mb-2">Type your custom message with emojis & WhatsApp text formatting</p>
                    <textarea class="form-control" id="customMessage" rows="5" placeholder="Add your custom message that users will send to you" oninput="updatePreview()"></textarea>
                </div>

                <div class="mb-4">
                    <button class="btn btn-success px-4 py-2 fw-bold" onclick="generateLink()">Generate Link</button>
                </div>

            </div>
            
            <div class="col-lg-5 col-md-6 d-flex justify-content-center">
                <div>
                    <p class="text-center text-muted mb-2">Message Preview</p>
                    <div class="wa-preview-container">
                        <div class="wa-preview-header">
                            <div class="avatar avatar-sm bg-secondary text-white rounded-circle me-2 d-flex align-items-center justify-content-center">
                                <i class="ti ti-user fs-16"></i>
                            </div>
                            <span class="fw-bold" id="previewPhone">+91</span>
                        </div>
                        <div class="wa-preview-body">
                            <div class="wa-message-bubble" id="previewMessage"></div>
                        </div>
                        <div class="wa-preview-footer">
                            <div class="wa-input-box">
                                <span>Type a message</span>
                                <i class="fa-solid fa-paper-plane text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saved Links Table -->
        @if(isset($savedLinks) && $savedLinks->count() > 0)
        <div class="row justify-content-center mt-5">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold">Recent Saved Links</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Phone Number</th>
                                        <th>Message</th>
                                        <th>Link</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($savedLinks as $savedLink)
                                    <tr>
                                        <td class="text-muted small">{{ $savedLink->created_at->format('M d, Y H:i') }}</td>
                                        <td>{{ $savedLink->phone_number }}</td>
                                        <td class="text-truncate" style="max-width: 200px;">{{ $savedLink->custom_message ?: '-' }}</td>
                                        <td><a href="{{ $savedLink->generated_url_or_code }}" target="_blank" class="text-success">{{ str_replace('https://', '', $savedLink->generated_url_or_code) }}</a></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="copyText('{{ $savedLink->generated_url_or_code }}')"><i class="ti ti-copy"></i> Copy</button>
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

<!-- Link Generator Modal -->
<div class="modal fade" id="linkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 p-3">
            <div class="modal-header border-0 pb-0">
                <h4 class="modal-title fw-bold text-dark">Here is your WhatsApp short link</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-2">
                <p class="text-muted text-start mb-4" style="font-size: 15px;">Copy and share it on your social media, website, emails or anywhere you want to be contacted instantly by your customers.</p>
                
                <div class="d-flex justify-content-center align-items-center mb-4">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WA" width="45" height="45" class="me-3">
                    <h4 class="text-success fw-bold mb-0" id="modalLinkText" style="word-break: break-all;">wa.me/...</h4>
                    <input type="hidden" id="generatedLink">
                </div>

                <div class="d-flex justify-content-center mb-4">
                    <div id="qrcode" class="p-3 border rounded shadow-sm bg-white d-inline-block"></div>
                </div>

                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-success px-4 py-2 fw-bold rounded-2" onclick="copyLink()">Copy URL</button>
                    <button type="button" class="btn btn-outline-success px-4 py-2 fw-bold rounded-2" onclick="downloadQR()">Download QR</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    function updatePreview() {
        const countryCode = document.getElementById('countryCode').value;
        const phone = document.getElementById('phoneNumber').value;
        const message = document.getElementById('customMessage').value;
        
        document.getElementById('previewPhone').innerText = '+' + countryCode + ' ' + phone;
        
        const previewMsg = document.getElementById('previewMessage');
        if (message.trim() !== '') {
            previewMsg.innerText = message;
            previewMsg.classList.add('active');
        } else {
            previewMsg.classList.remove('active');
        }
    }

    function generateLink() {
        const countryCode = document.getElementById('countryCode').value;
        const phoneInput = document.getElementById('phoneNumber').value;
        const phone = phoneInput.replace(/\D/g,'');
        const message = document.getElementById('customMessage').value;
        
        if (!phone) {
            if(typeof toastr !== 'undefined') {
                toastr.error('Please enter a valid phone number');
            } else {
                alert('Please enter a valid phone number');
            }
            return;
        }

        const encodedMessage = encodeURIComponent(message);
        const link = `https://wa.me/${countryCode}${phone}?text=${encodedMessage}`;
        
        document.getElementById('generatedLink').value = link;
        document.getElementById('modalLinkText').innerText = `wa.me/${countryCode}${phone}`;

        // Generate QR Code
        const qrContainer = document.getElementById('qrcode');
        qrContainer.innerHTML = "";
        
        if (typeof QRCode !== 'undefined') {
            new QRCode(qrContainer, {
                text: link,
                width: 180,
                height: 180,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        } else {
            qrContainer.innerHTML = "<small class='text-danger'>QR Code library failed to load.</small>";
        }

        // Show Modal
        var myModal = new bootstrap.Modal(document.getElementById('linkModal'));
        myModal.show();

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
                type: 'link',
                name: 'Generated Link',
                phone_number: '+' + countryCode + phone,
                custom_message: message,
                generated_url_or_code: link
            })
        }).then(res => res.json()).then(data => {
            if(data.status === 'success') {
                console.log('Link saved to database.');
            }
        });
    }

    function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                if(typeof toastr !== 'undefined') toastr.success('Link copied!');
            });
        } else {
            const input = document.createElement('textarea');
            input.value = text;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            if(typeof toastr !== 'undefined') toastr.success('Link copied!');
        }
    }

    function downloadQR() {
        const qrImg = document.querySelector('#qrcode img');
        const qrCanvas = document.querySelector('#qrcode canvas');
        
        let url = null;
        if (qrImg && qrImg.src) {
            url = qrImg.src;
        } else if (qrCanvas) {
            url = qrCanvas.toDataURL("image/png");
        }

        if (url) {
            const link = document.createElement('a');
            link.download = 'whatsapp-qr-code.png';
            link.href = url;
            link.click();
        } else {
            alert('QR Code not ready yet.');
        }
    }

    function copyLink() {
        const linkInput = document.getElementById('generatedLink');
        const linkText = linkInput.value;
        
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(linkText).then(() => {
                showSuccess();
            });
        } else {
            linkInput.select();
            linkInput.setSelectionRange(0, 99999);
            try {
                document.execCommand('copy');
                showSuccess();
            } catch (err) {
                alert('Failed to copy text.');
            }
        }
        
        function showSuccess() {
            if(typeof toastr !== 'undefined') {
                toastr.success('Link copied to clipboard!');
            } else {
                alert('Link copied to clipboard!');
            }
        }
    }

    // Initialize preview
    document.getElementById('countryCode').addEventListener('change', updatePreview);
    updatePreview();
</script>
@endsection
