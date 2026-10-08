@extends('layouts.main')

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Delete Account</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/') }}"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            Account
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Delete Account</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                <div class="ms-2 head-icons">
                    <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-original-title="Collapse" id="collapse-header">
                        <i class="ti ti-chevrons-up"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <!-- Dynamic Alerts -->
        <div id="dynamicAlert" class="alert d-none"></div>

        @php
            $status = $deletionRequest ? $deletionRequest->status : null;
        @endphp

        <!-- WARNING HEADER -->
        <div id="warningHeader" class="alert alert-warning d-flex align-items-center mb-4 {{ $status ? 'd-none' : '' }}" role="alert">
            <i class="ti ti-alert-triangle fs-4 me-2"></i>
            <div>
                <strong>Important Notice:</strong> Account deletion is not immediate. Your request will be reviewed by our Super Admin team and the deletion process may take up to 7 days after approval.
            </div>
        </div>

        <!-- SECTION: QUESTIONNAIRE (FORM) - Stepper Layout -->
        <div class="row {{ $status ? 'd-none' : '' }}" id="sectionQuestionnaire">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Account Deletion Request</h4>
                        <p class="text-muted mb-0">We're sorry to see you go. Before submitting your request, please review the information below.</p>
                    </div>
                    <div class="card-body">
                        
                        <form id="deletionForm">
                            @csrf

                            <!-- Step 1 -->
                            <div class="step-section" id="step1">
                                <h5 class="mb-4 text-primary">Step 1 of 4: Tell Us Why You're Leaving</h5>
                                <div class="mb-3">
                                    <label class="form-label">Why are you deleting your account? <span class="text-danger">*</span></label>
                                    @php
                                        $reasons = [
                                            'I no longer need the service',
                                            'I don\'t use my account anymore',
                                            'I\'m not satisfied with the service',
                                            'I\'m experiencing technical problems',
                                            'I\'m concerned about privacy',
                                            'I\'m moving to another service',
                                            'I created the account by mistake',
                                            'Other'
                                        ];
                                    @endphp
                                    <div class="row g-2 mt-2">
                                        @foreach($reasons as $reason)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="reason" id="reason_{{ $loop->index }}" value="{{ $reason }}" required>
                                                <label class="form-check-label" for="reason_{{ $loop->index }}">
                                                    {{ $reason }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="mb-3 d-none" id="otherReasonContainer">
                                    <label class="form-label">Please specify your reason</label>
                                    <textarea name="reason_other" class="form-control" rows="3" placeholder="Tell us more..."></textarea>
                                </div>
                                
                                <div class="text-end mt-4 pt-3 border-top">
                                    <button type="button" class="btn btn-primary btn-next" data-next="2">
                                        Continue <i class="ti ti-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="step-section d-none" id="step2">
                                <h5 class="mb-4 text-primary">Step 2 of 4: Help Us Improve</h5>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">How would you rate your overall experience?</label>
                                        <select name="experience" class="form-select">
                                            <option value="">Select an option (Optional)</option>
                                            <option value="Very dissatisfied">Very dissatisfied</option>
                                            <option value="Dissatisfied">Dissatisfied</option>
                                            <option value="Neutral">Neutral</option>
                                            <option value="Satisfied">Satisfied</option>
                                            <option value="Very satisfied">Very satisfied</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Did you experience any problems?</label>
                                    @php
                                        $problems = [
                                            'Technical problems',
                                            'Login/account problems',
                                            'Performance problems',
                                            'Payment problems',
                                            'Support problems',
                                            'Other',
                                            'No problems'
                                        ];
                                    @endphp
                                    <div class="row g-2 mt-1">
                                    @foreach($problems as $problem)
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="problems[]" id="prob_{{ $loop->index }}" value="{{ $problem }}">
                                                <label class="form-check-label" for="prob_{{ $loop->index }}">
                                                    {{ $problem }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tell us more about your experience. (Optional)</label>
                                    <textarea name="feedback" class="form-control" rows="3" placeholder="Your feedback helps us improve..."></textarea>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                    <button type="button" class="btn btn-light btn-prev" data-prev="1">
                                        <i class="ti ti-arrow-left me-1"></i> Back
                                    </button>
                                    <button type="button" class="btn btn-primary btn-next" data-next="3">
                                        Continue <i class="ti ti-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="step-section d-none" id="step3">
                                <h5 class="mb-4 text-primary">Step 3 of 4: Before You Continue</h5>
                                
                                <div class="row">
                                    <div class="col-lg-6 mb-4 mb-lg-0">
                                        <div class="alert alert-info border-0 mb-0">
                                            <h6 class="alert-heading fw-bold mb-3">What happens when you request deletion?</h6>
                                            <ul class="mb-0 ps-3 text-dark">
                                                <li class="mb-1"><strong>Step 1:</strong> Request Submitted</li>
                                                <li class="mb-1"><strong>Step 2:</strong> Email Verified via OTP</li>
                                                <li class="mb-1"><strong>Step 3:</strong> Super Admin Review</li>
                                                <li class="mb-1"><strong>Step 4:</strong> Deletion Processing (Up to 7 days)</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <h6 class="fw-bold mb-2">Data & Account Information</h6>
                                        <ul class="text-muted ps-3 mb-0">
                                            <li class="mb-2">Account access may be removed after deletion.</li>
                                            <li>Eligible personal information will be deleted or anonymized based on our data retention policy.</li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                    <button type="button" class="btn btn-light btn-prev" data-prev="2">
                                        <i class="ti ti-arrow-left me-1"></i> Back
                                    </button>
                                    <button type="button" class="btn btn-primary btn-next" data-next="4">
                                        Continue <i class="ti ti-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 4 -->
                            <div class="step-section d-none" id="step4">
                                <h5 class="mb-4 text-primary">Step 4 of 4: Required Confirmations</h5>
                                <p class="text-muted mb-3">Please read and acknowledge the following statements to proceed with your deletion request.</p>
                                
                                <div class="row g-3">
                                    @php
                                        $confirmations = [
                                            'I understand that this is a deletion request and my account will not be deleted immediately.',
                                            'I understand that the request must be reviewed and approved.',
                                            'I understand that the deletion process may take up to 7 days after approval.',
                                            'I understand that I may lose access to my account and associated services.',
                                            'I confirm that I want to request deletion of my account.'
                                        ];
                                    @endphp

                                    @foreach($confirmations as $confirm)
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input required-checkbox" type="checkbox" name="confirmations[]" id="confirm_{{ $loop->index }}" value="{{ $confirm }}">
                                            <label class="form-check-label text-dark" for="confirm_{{ $loop->index }}">
                                                {{ $confirm }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                    <button type="button" class="btn btn-light btn-prev" data-prev="3">
                                        <i class="ti ti-arrow-left me-1"></i> Back
                                    </button>
                                    <button type="submit" class="btn btn-danger" id="submitBtn" disabled>
                                        <i class="ti ti-send me-1"></i> Request OTP Verification
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION: OTP VERIFICATION -->
        <div class="row {{ $status === 'pending_verification' ? '' : 'd-none' }}" id="sectionOTP">
            <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
                <div class="card">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <i class="ti ti-mail-opened fs-1 text-primary"></i>
                        </div>
                        
                        <h4 class="mb-3">Verify Your Email</h4>
                        
                        <p class="text-muted mb-4">
                            To protect your account, we've sent a secure 6-digit verification code to your registered email address.<br>
                            <strong class="text-dark d-block mt-2" id="otpEmailDisplay">{{ Auth::user()->email }}</strong>
                        </p>

                        <form id="otpForm" class="mt-4">
                            @csrf
                            <div class="d-flex justify-content-center gap-1 gap-sm-2 mb-4 flex-wrap">
                                @for($i = 0; $i < 6; $i++)
                                    <input type="text" name="otp[]" class="form-control otp-input" maxlength="1" style="width: 45px; height: 55px; font-size: 24px; text-align: center; padding: 0; font-weight: bold; border-radius: 8px;" required {{ $i == 0 ? 'autofocus' : '' }}>
                                @endfor
                            </div>
                            <div class="text-center mb-4">
                                <button type="submit" class="btn btn-primary w-100" id="verifyOtpBtn">Verify Account</button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted small mb-1">Didn't receive the code?</p>
                            <button type="button" class="btn btn-link text-decoration-none p-0 fw-bold" id="resendBtn">Resend Verification Code</button>
                            <p class="mt-2 text-muted small">Expires in: <span id="timer" class="fw-bold text-danger"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION: SUCCESS & TIMELINE -->
        <div class="row {{ in_array($status, ['pending_review', 'approved', 'processing', 'completed', 'cancelled', 'rejected']) ? '' : 'd-none' }}" id="sectionSuccess">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-4 p-md-5 text-center">
                        
                        <div class="mb-4" id="successIcon">
                            @if($status == 'cancelled' || $status == 'rejected')
                                <i class="ti ti-circle-x fs-1 text-danger"></i>
                            @else
                                <i class="ti ti-circle-check fs-1 text-success"></i>
                            @endif
                        </div>
                        
                        <h3 class="mb-2" id="successTitle">
                            @if($status == 'cancelled') Request Cancelled
                            @elseif($status == 'rejected') Request Rejected
                            @else Request Submitted Successfully
                            @endif
                        </h3>
                        
                        <p class="text-muted mb-4" id="successDesc">
                            @if($status == 'cancelled') Your account deletion request was cancelled successfully.
                            @elseif($status == 'rejected') Your request was rejected by the Super Admin.
                            @else Your account deletion request has been verified and is currently awaiting Super Admin review.
                            @endif
                        </p>
                        
                        <div class="alert alert-light border mb-5 d-inline-block">
                            <strong>Request Tracking ID:</strong> <span class="text-primary ms-1" id="successReqId">#DEL-{{ $deletionRequest ? str_pad($deletionRequest->id, 6, '0', STR_PAD_LEFT) : '' }}</span>
                        </div>

                        <div class="text-start mx-auto border rounded p-4 bg-light" style="max-width: 500px;" id="timelineContainer">
                            <h5 class="mb-4 border-bottom pb-2">Status Timeline</h5>
                            
                            <div id="timelineItems">
                                @if($deletionRequest)
                                    <div class="d-flex mb-3 align-items-center text-success">
                                        <i class="ti ti-check me-2"></i>
                                        <div>
                                            <strong>Request Submitted</strong>
                                            <div class="small text-muted">{{ $deletionRequest->created_at->format('d M Y, h:i A') }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-3 align-items-center text-success">
                                        <i class="ti ti-check me-2"></i>
                                        <div>
                                            <strong>Email Verified</strong>
                                            <div class="small text-muted">{{ $deletionRequest->otp_verified_at ? \Carbon\Carbon::parse($deletionRequest->otp_verified_at)->format('d M Y, h:i A') : 'Completed' }}</div>
                                        </div>
                                    </div>
                                    
                                    @if($status == 'pending_review')
                                        <div class="d-flex mb-3 align-items-center text-primary">
                                            <i class="ti ti-hourglass-empty me-2"></i>
                                            <div>
                                                <strong>Awaiting Admin Review</strong>
                                                <div class="small text-muted">Currently under review.</div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="ti ti-settings me-2"></i>
                                            <div>
                                                <strong>Deletion Processing</strong>
                                            </div>
                                        </div>
                                    @elseif($status == 'approved' || $status == 'processing')
                                        <div class="d-flex mb-3 align-items-center text-success">
                                            <i class="ti ti-check me-2"></i>
                                            <div>
                                                <strong>Approved</strong>
                                                <div class="small text-muted">Scheduled for {{ \Carbon\Carbon::parse($deletionRequest->scheduled_deletion_at)->format('d M Y') }}</div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center text-primary">
                                            <i class="ti ti-settings me-2"></i>
                                            <div>
                                                <strong>Deletion Processing</strong>
                                                <div class="small text-muted">7-day period ongoing.</div>
                                            </div>
                                        </div>
                                    @elseif($status == 'completed')
                                        <div class="d-flex mb-3 align-items-center text-success">
                                            <i class="ti ti-check me-2"></i>
                                            <div>
                                                <strong>Approved</strong>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center text-success">
                                            <i class="ti ti-check-double me-2"></i>
                                            <div>
                                                <strong>Account Deleted</strong>
                                            </div>
                                        </div>
                                    @elseif($status == 'rejected')
                                        <div class="d-flex align-items-center text-danger">
                                            <i class="ti ti-x me-2"></i>
                                            <div>
                                                <strong>Request Rejected</strong>
                                                <div class="small text-muted">Reason: {{ $deletionRequest->rejection_reason }}</div>
                                            </div>
                                        </div>
                                    @elseif($status == 'cancelled')
                                        <div class="d-flex align-items-center text-secondary">
                                            <i class="ti ti-ban me-2"></i>
                                            <div>
                                                <strong>Request Cancelled</strong>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        @if($status == 'pending_review')
                        <div class="mt-4" id="cancelActionContainer">
                            <button type="button" class="btn btn-outline-danger" id="cancelRequestBtn">
                                <i class="ti ti-x me-1"></i> Cancel Deletion Request
                            </button>
                        </div>
                        @endif
                        
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancel Confirmation Modal -->
        <div class="modal fade" id="cancelConfirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cancel Deletion Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4 text-center">
                        <h5 class="text-dark mb-2">Are you sure you want to cancel your deletion request?</h5>
                        <p class="text-muted mb-0">This will stop the deletion process and keep your account active.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">No, Keep It</button>
                        <button type="button" class="btn btn-danger" id="confirmCancelBtn">Yes, Cancel Request</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    let otpExpiryTime = '{{ $deletionRequest && $deletionRequest->otp_expires_at ? $deletionRequest->otp_expires_at->toIso8601String() : "" }}';
    let timerInterval;

    function showAlert(type, message) {
        $('#dynamicAlert').removeClass('d-none alert-success alert-danger').addClass('alert-' + type).html(message);
        setTimeout(function(){
            $('#dynamicAlert').addClass('d-none');
        }, 6000);
        
        $('html, body').animate({
            scrollTop: $("#dynamicAlert").offset().top - 100
        }, 300);
    }

    // --- STEPPER LOGIC ---
    $('.btn-next').click(function() {
        let currentStep = $(this).closest('.step-section');
        let next = $(this).data('next');
        
        // Validation for radio button in Step 1
        if (next == 2 && !$('input[name=reason]:checked').val()) {
            showAlert('danger', 'Please select a reason before continuing.');
            return;
        }

        currentStep.addClass('d-none');
        $('#step' + next).removeClass('d-none');
        
        $('html, body').animate({
            scrollTop: $("#sectionQuestionnaire").offset().top - 100
        }, 300);
    });

    $('.btn-prev').click(function() {
        $(this).closest('.step-section').addClass('d-none');
        $('#step' + $(this).data('prev')).removeClass('d-none');
    });

    $('input[type=radio][name=reason]').change(function() {
        if (this.value === 'Other') {
            $('#otherReasonContainer').removeClass('d-none');
            $('textarea[name=reason_other]').attr('required', true);
        } else {
            $('#otherReasonContainer').addClass('d-none');
            $('textarea[name=reason_other]').removeAttr('required');
        }
    });

    $('.required-checkbox').change(function() {
        var allChecked = $('.required-checkbox:checked').length === $('.required-checkbox').length;
        $('#submitBtn').prop('disabled', !allChecked);
    });

    // --- 1. SUBMIT QUESTIONNAIRE (AJAX) ---
    $('#deletionForm').on('submit', function(e) {
        e.preventDefault();
        
        let formData = $(this).serializeArray();
        
        let reason = $('input[name=reason]:checked').val();
        if (!reason) {
            showAlert('danger', 'Please select a reason for leaving.');
            return;
        }

        if (reason === 'Other') {
            let otherVal = $('textarea[name=reason_other]').val();
            if (!otherVal) {
                showAlert('danger', 'Please specify your reason.');
                return;
            }
            $.each(formData, function(i, field) {
                if(field.name === 'reason') field.value = otherVal;
            });
        }

        let $btn = $('#submitBtn');
        let originalText = $btn.html();
        $btn.html('<i class="ti ti-loader fa-spin me-1"></i> Processing...').prop('disabled', true);

        $.ajax({
            url: '{{ box_route('account.delete.submit') }}',
            type: 'POST',
            data: formData,
            success: function(res) {
                if (res.status === 'success') {
                    $('#sectionQuestionnaire').addClass('d-none');
                    $('#warningHeader').addClass('d-none');
                    $('#sectionOTP').removeClass('d-none');
                    
                    otpExpiryTime = res.expiry;
                    startTimer();
                    
                    $('html, body').animate({
                        scrollTop: $("#sectionOTP").offset().top - 100
                    }, 300);
                } else {
                    showAlert('danger', res.message);
                    $btn.html(originalText).prop('disabled', false);
                }
            },
            error: function(xhr) {
                showAlert('danger', xhr.responseJSON?.message || 'An error occurred.');
                $btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // --- OTP INPUT LOGIC ---
    $('.otp-input').on('keyup', function(e) {
        if (e.key >= 0 && e.key <= 9) {
            $(this).next('.otp-input').focus();
        } else if (e.key === 'Backspace') {
            $(this).prev('.otp-input').focus();
        }
    });
    $('.otp-input').on('paste', function(e) {
        let paste = e.originalEvent.clipboardData.getData('text');
        if(paste.length === 6 && /^\d+$/.test(paste)) {
            let inputs = $('.otp-input');
            for(let i=0; i<6; i++) {
                $(inputs[i]).val(paste[i]);
            }
            $(inputs[5]).focus();
            e.preventDefault();
        }
    });

    function startTimer() {
        clearInterval(timerInterval);
        if(!otpExpiryTime) return;

        var expiry = new Date(otpExpiryTime).getTime();
        
        timerInterval = setInterval(function() {
            var now = new Date().getTime();
            var distance = expiry - now;

            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            $('#timer').text(minutes + "m " + seconds + "s ");

            if (distance < 0) {
                clearInterval(timerInterval);
                $('#timer').text("EXPIRED");
                $('.otp-input').prop('disabled', true);
            }
        }, 1000);
    }
    
    if ($('#sectionOTP').is(':visible')) {
        startTimer();
    }

    // --- 2. VERIFY OTP (AJAX) ---
    $('#otpForm').on('submit', function(e) {
        e.preventDefault();
        
        let $btn = $('#verifyOtpBtn');
        let originalText = $btn.html();
        $btn.html('<i class="ti ti-loader fa-spin me-1"></i> Verifying...').prop('disabled', true);

        let otpValue = '';
        $('.otp-input').each(function() {
            otpValue += $(this).val();
        });

        $.ajax({
            url: '{{ box_route('account.delete.verify') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                otp: otpValue
            },
            success: function(res) {
                if (res.status === 'success') {
                    $('#sectionOTP').addClass('d-none');
                    $('#sectionSuccess').removeClass('d-none');
                    $('#successReqId').text('#DEL-' + res.request_id);
                    
                    renderTimeline('pending_review');
                } else {
                    showAlert('danger', res.message);
                    $btn.html(originalText).prop('disabled', false);
                }
            },
            error: function(xhr) {
                showAlert('danger', xhr.responseJSON?.message || 'Invalid OTP.');
                $btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // --- 3. RESEND OTP (AJAX) ---
    $('#resendBtn').click(function() {
        let $btn = $(this);
        let originalText = $btn.text();
        $btn.text('Sending...').prop('disabled', true);

        $.ajax({
            url: '{{ box_route('account.delete.resend') }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                if (res.status === 'success') {
                    otpExpiryTime = res.expiry;
                    $('.otp-input').prop('disabled', false).val('');
                    startTimer();
                    $('#dynamicAlert').removeClass('d-none alert-danger').addClass('alert-success').html(res.message);
                } else {
                    showAlert('danger', res.message);
                }
                $btn.text(originalText).prop('disabled', false);
            },
            error: function(xhr) {
                showAlert('danger', 'Failed to resend code.');
                $btn.text(originalText).prop('disabled', false);
            }
        });
    });

    // --- 4. CANCEL REQUEST (AJAX) ---
    $('#cancelRequestBtn').click(function() {
        $('#cancelConfirmModal').modal('show');
    });

    $('#confirmCancelBtn').click(function() {
        let $btn = $(this);
        let originalText = $btn.html();
        $btn.html('<i class="ti ti-loader fa-spin me-1"></i> Cancelling...').prop('disabled', true);

        $.ajax({
            url: '{{ box_route('account.delete.cancel') }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                if (res.status === 'success') {
                    $('#cancelConfirmModal').modal('hide');

                    renderTimeline('cancelled');
                    $('#successIcon').html('<i class="ti ti-circle-x fs-1 text-danger"></i>');
                    $('#successTitle').text('Request Cancelled');
                    $('#successDesc').text('Your account deletion request was cancelled successfully.');
                    $('#cancelActionContainer').hide();
                } else {
                    showAlert('danger', res.message);
                    $btn.html(originalText).prop('disabled', false);
                }
            },
            error: function(xhr) {
                showAlert('danger', 'Failed to cancel request.');
                $btn.html(originalText).prop('disabled', false);
            }
        });
    });

    function renderTimeline(status) {
        let html = '';
        let now = new Date().toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute:'2-digit', hour12: true });

        if (status === 'cancelled') {
            html += `<div class="d-flex mb-3 align-items-center text-success">
                        <i class="ti ti-check me-2"></i>
                        <div><strong>Request Submitted</strong></div>
                    </div>`;
            html += `<div class="d-flex align-items-center text-secondary">
                        <i class="ti ti-ban me-2"></i>
                        <div>
                            <strong>Request Cancelled</strong>
                            <div class="small">${now}</div>
                        </div>
                    </div>`;
        } else if (status === 'pending_review') {
            html += `<div class="d-flex mb-3 align-items-center text-success">
                        <i class="ti ti-check me-2"></i>
                        <div><strong>Request Submitted</strong></div>
                    </div>`;
            html += `<div class="d-flex mb-3 align-items-center text-success">
                        <i class="ti ti-check me-2"></i>
                        <div>
                            <strong>Email Verified</strong>
                            <div class="small">${now}</div>
                        </div>
                    </div>`;
            html += `<div class="d-flex mb-3 align-items-center text-primary">
                        <i class="ti ti-hourglass-empty me-2"></i>
                        <div>
                            <strong>Awaiting Admin Review</strong>
                            <div class="small text-muted">Currently under review.</div>
                        </div>
                    </div>`;
            html += `<div class="d-flex align-items-center text-muted">
                        <i class="ti ti-settings me-2"></i>
                        <div><strong>Deletion Processing</strong></div>
                    </div>`;
            
            if(!$('#cancelActionContainer').length) {
                $('#sectionSuccess .card-body').append('<div class="mt-4" id="cancelActionContainer"><button type="button" class="btn btn-outline-danger" id="cancelRequestBtn"><i class="ti ti-x me-1"></i> Cancel Deletion Request</button></div>');
                bindCancel();
            } else {
                $('#cancelActionContainer').show();
            }
        }
        
        $('#timelineItems').html(html);
    }
    
    function bindCancel() {
        $('#cancelRequestBtn').off('click').on('click', function() {
            $('#cancelConfirmModal').modal('show');
        });
    }
</script>
@endsection
