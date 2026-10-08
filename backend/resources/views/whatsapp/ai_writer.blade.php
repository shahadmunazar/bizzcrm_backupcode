@extends('layouts.main')
@section('title', 'AI Template Writer')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">AI Template Writer</h4>
                        </div>
                        <div class="col-4 text-end">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
                                <i class="ti ti-arrow-left"></i> Back to Creation
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <div id="lastGenContainer">
                    @if($latestLog)
                    <!-- Quick View: Last Generation -->
                    <div class="card bg-light-info border-info mb-4">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-info">Last Generation</span>
                                <small class="text-muted">{{ $latestLog->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-2 text-dark fs-14 fw-medium">{{ Str::limit($latestLog->result, 120) }}</p>
                            <div class="text-end">
                                <button class="btn btn-sm btn-link text-info p-0 fw-bold btn-use-history" data-text="{{ $latestLog->result }}">
                                    <i class="ti ti-arrow-back-up me-1"></i> Re-use this
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="border border-1 border-dashed p-4 rounded-3">
                            <div class="d-flex align-items-center flex-wrap justify-content-between mb-3">
                                <label class="col-form-label fw-bold fs-16">Write your prompt</label>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#historyModal">
                                        <i class="ti ti-history me-1"></i> History
                                    </button>
                                </div>
                            </div>
                            
                            <textarea id="aiPrompt" class="form-control fs-15 mb-4" rows="5" 
                                placeholder="eg. Please generate a promotional message to give a discount of 20% off on our product line."></textarea>
                            
                            <div class="mb-4 mt-3">
                                <label class="col-form-label fw-bold text-dark d-block">Target Language:</label>
                                <select id="aiLanguage" class="form-select fs-15">
                                    <option value="English" selected>English</option>
                                    <option value="Hindi">Hindi (हिन्दी)</option>
                                    <option value="Spanish">Spanish (Español)</option>
                                    <option value="French">French (Français)</option>
                                    <option value="Arabic">Arabic (العربية)</option>
                                    <option value="Portuguese">Portuguese (Português)</option>
                                    <option value="German">German (Deutsch)</option>
                                    <option value="Gujarati">Gujarati (ગુજરાતી)</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="col-form-label fw-bold text-dark d-block">Choose your message style:</label>
                                <div class="btn-list style-selector mt-2">
                                    <button type="button" class="btn btn-outline-dark btn-md active" data-style="Normal">😀 Normal</button>
                                    <button type="button" class="btn btn-outline-dark btn-md" data-style="Poetic">✍🏼 Poetic</button>
                                    <button type="button" class="btn btn-outline-dark btn-md" data-style="Exciting">🤩 Exciting</button>
                                    <button type="button" class="btn btn-outline-dark btn-md" data-style="Funny">😜 Funny</button>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="col-form-label fw-bold text-dark d-block">Optimize your message for:</label>
                                <div class="btn-list optimize-selector mt-2">
                                    <button type="button" class="btn btn-outline-dark btn-md" data-opt="Click Rate">
                                        <i class="ti ti-mouse me-1"></i> Click Rate
                                    </button>
                                    <button type="button" class="btn btn-outline-dark btn-md" data-opt="Reply Rate">
                                        <i class="ti ti-reload me-1"></i> Reply Rate
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mt-5">
                                <div class="d-grid gap-2">
                                    <button id="btnAiWrite" class="btn {{ $creditsLeft <= 0 ? 'btn-secondary' : 'btn-success' }} btn-lg fs-18 py-3" type="button" {{ $creditsLeft <= 0 ? 'disabled' : '' }}>
                                        <i class="ti ti-sparkles me-2"></i> Generate ({{ max(0, $creditsLeft) }} free generations left)
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Result Area -->
                        <div id="resultArea" class="mt-4" style="display: none;">
                            <hr>
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h5 class="fw-bold mb-0"><i class="ti ti-bulb text-warning me-2"></i>AI Suggestions</h5>
                                <button id="btnOptimize" class="btn btn-outline-info btn-sm">
                                    <i class="ti ti-wand me-1"></i> Regenerate More
                                </button>
                            </div>
                            
                            <div class="row" id="suggestionsRow">
                                <!-- Suggestions will be injected here via AJAX -->
                            </div>

                            <div class="alert alert-info py-2 px-3 border-0 rounded-3 mt-4">
                                <small><i class="ti ti-info-circle me-1"></i> You can use @{{1}}, @{{2}} etc. to map CRM fields later. Click "Use this" to apply a suggestion.</small>
                            </div>
                        </div>
                    </div>
                </div>        </div>

                <div class="text-center mt-4 text-muted">
                    <p class="small">Powered by Google Gemini AI • Generates WhatsApp compliant templates</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">AI Generation History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($logs->isEmpty())
                    <div class="text-center py-4">
                        <i class="ti ti-info-circle fs-40 text-muted mb-3"></i>
                        <p>No previous generations found.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Prompt</th>
                                    <th>Style</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody">
                                @foreach($logs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('M d, H:i') }}</td>
                                        <td title="{{ $log->prompt }}">{{ Str::limit($log->prompt, 40) }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $log->style }}</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary btn-use-history" data-id="{{ $log->id }}">
                                                Use
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function() {
    let freeGenerations = {{ $creditsLeft }};

    // Toggle button active states
    $('.style-selector .btn, .optimize-selector .btn').on('click', function() {
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
    });

    $('#btnAiWrite').on('click', function() {
        const promptText = $('#aiPrompt').val();
        if (!promptText) {
            toastr.error("Please enter a prompt first.");
            return;
        }

        const style = $('.style-selector .btn.active').data('style');
        const opt = $('.optimize-selector .btn.active').data('opt');
        const lang = $('#aiLanguage').val();
        const $btn = $(this);
        
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>AI is thinking in ' + lang + '...');

        // Real AI API Call to Backend
        $.post("{{ box_route('whatsapp.ai_generate') }}", {
            _token: "{{ csrf_token() }}",
            prompt: promptText,
            style: style,
            optimize: opt,
            language: lang
        }, function(response) {
            if (response.success) {
                freeGenerations = response.remaining;
                $btn.html(`<i class="ti ti-sparkles me-2"></i> Generate (${freeGenerations} free generations left)`);
                
                // UPDATE SUGGESTIONS ROW
                let suggestionsHtml = '';
                response.results.forEach((text, index) => {
                    if(!text) return;
                    suggestionsHtml += `
                        <div class="col-md-4 mb-3 d-flex">
                            <div class="card w-100 border-2 shadow-sm suggestion-card" style="border-radius: 15px; overflow: hidden;">
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-3 flex-grow-1">
                                        <pre class="mb-0 fs-15 text-dark" style="white-space: pre-wrap; font-family: inherit; line-height: 1.5;">${text}</pre>
                                    </div>
                                    <button class="btn btn-success w-100 py-2 fw-bold btn-apply-suggestion" data-text="${text.replace(/"/g, '&quot;')}">
                                        Use this
                                    </button>
                                </div>
                            </div>
                        </div>`;
                });
                $('#suggestionsRow').html(suggestionsHtml);
                $('#resultArea').fadeIn();
                toastr.success("3 Professional suggestions generated!");

                // UPDATE UI DYNAMICALLY (AJAX/JQUERY)
                if (response.latest_log) {
                    const log = response.latest_log;
                    
                    // 1. Update Quick View Card
                    const cardHtml = `
                        <div class="card bg-light-info border-info mb-4">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-items-between mb-2">
                                    <span class="badge bg-info">Last Generation</span>
                                    <small class="text-muted">${log.created_at_human}</small>
                                </div>
                                <p class="mb-2 text-dark fs-14 fw-medium">${log.result.substring(0, 120)}...</p>
                                <div class="text-end">
                                    <button class="btn btn-sm btn-link text-info p-0 fw-bold btn-use-history" data-text="${log.result.replace(/"/g, '&quot;')}">
                                        <i class="ti ti-arrow-back-up me-1"></i> Re-use this
                                    </button>
                                </div>
                            </div>
                        </div>`;
                    $('#lastGenContainer').html(cardHtml);

                    // 2. Prepend to History Table
                    const rowHtml = `
                        <tr>
                            <td>${log.date_short}</td>
                            <td title="${log.full_prompt}">${log.prompt}</td>
                            <td><span class="badge bg-light text-dark border">${log.style}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary btn-use-history" data-text="${log.result.replace(/"/g, '&quot;')}">
                                    Use
                                </button>
                            </td>
                        </tr>`;
                    $('#historyTableBody').prepend(rowHtml);
                }
            } else {
                toastr.error(response.message || "Failed to generate content.");
                $btn.html(`<i class="ti ti-sparkles me-2"></i> Generate (${freeGenerations} free generations left)`);
            }
        }).fail(function() {
            toastr.error("Server error occurred while connecting to AI.");
            $btn.html(`<i class="ti ti-sparkles me-2"></i> Generate (${freeGenerations} free generations left)`);
        }).always(function() {
            $btn.prop('disabled', (freeGenerations <= 0));
            if (freeGenerations <= 0) {
                $btn.removeClass('btn-success').addClass('btn-secondary');
            }
        });
    });

    // Apply Suggestion Button (The "Use this" button on cards)
    $(document).on('click', '.btn-apply-suggestion', function() {
        const text = $(this).data('text');
        localStorage.setItem('pending_ai_message', text);
        toastr.success("Template selected! Returning to editor...");
        
        setTimeout(() => {
            window.location.href = "{{ url()->previous() }}";
        }, 1200);
    });

    function loadResultIntoEditor(text) {
        // Since we now have multiple suggestions, loading from history will just show one suggestion card
        let suggestionsHtml = `
            <div class="col-md-6 offset-md-3 mb-3 d-flex">
                <div class="card w-100 border-2 border-primary shadow suggestion-card" style="border-radius: 15px;">
                    <div class="card-body d-flex flex-column">
                        <div class="mb-3 flex-grow-1">
                            <span class="badge bg-primary mb-2">Historical Template</span>
                            <pre class="mb-0 fs-15 text-dark" style="white-space: pre-wrap; font-family: inherit; line-height: 1.5;">${text}</pre>
                        </div>
                        <button class="btn btn-success w-100 py-2 fw-bold btn-apply-suggestion" data-text="${text.replace(/"/g, '&quot;')}">
                            Use this
                        </button>
                    </div>
                </div>
            </div>`;
        $('#suggestionsRow').html(suggestionsHtml);
        $('#resultArea').fadeIn();
        $('html, body').animate({
            scrollTop: $("#resultArea").offset().top - 100
        }, 500);
    }

    // Optimization Button
    $('#btnOptimize').on('click', function() {
        const currentText = $('#aiResultText').text();
        if (!currentText) return;

        const $btn = $(this);
        const originalHtml = $btn.html();
        
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Optimizing...');

        $.post("{{ box_route('whatsapp.ai_generate') }}", {
            _token: "{{ csrf_token() }}",
            prompt: "Please optimize and improve this WhatsApp message for better flow and higher engagement. Keep the placeholders like @{{1}} intact: " + currentText,
            style: "Improved",
            optimize: "Engagement"
        }, function(response) {
            if (response.success) {
                $('#aiResultText').text(response.result);
                toastr.success("Message optimized successfully!");
            } else {
                toastr.error(response.message || "Optimization failed.");
            }
        }).fail(function() {
            toastr.error("Server error during optimization.");
        }).always(function() {
            $btn.prop('disabled', false).html(originalHtml);
        });
    });
});
</script>
<style>
    .btn-list .btn.active {
        background-color: #000;
        color: #fff;
        border-color: #000;
    }
    .fs-15 { font-size: 15px; }
    .fs-18 { font-size: 18px; }
</style>
@endpush
