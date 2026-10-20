<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login With Mobile | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        /* Center OTP input in modal */
        .modal-body {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .otp-box input {
            width: 40px;
            height: 45px;
            margin: 0 5px;
            font-size: 20px;
            text-align: center;
            border: 2px solid #ced4da;
            border-radius: 8px;
            transition: border-color 0.3s;
        }

        .otp-box input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        #resendOtpBtn {
            display: none;
        }

        #otpTimer.blinking {
            animation: blinker 1s linear infinite;
        }

        @keyframes blinker {
            50% {
                opacity: 0.5;
            }
        }
    </style>
</head>

<body class="account-page">
    <div class="main-wrapper">
        <div class="account-content">
            <div class="d-flex flex-wrap w-100 vh-100 overflow-hidden account-bg-03">
                <div
                    class="d-flex align-items-center justify-content-center flex-wrap vh-100 overflow-auto p-4 w-50 bg-backdrop">
                    <form id="mobileLoginForm" class="flex-fill">
                        @csrf
                        <div class="mx-auto mw-450">
                            <div class="text-center mb-4">
                                <img src="{{ asset('assets/img/logo.png') }}" class="img-fluid" alt="Logo">
                            </div>

                            <div class="mb-4">
                                <h4 class="mb-2 fs-20">Login via Mobile Or Email</h4>
                                <p>Enter your mobile number to receive an OTP.</p>
                            </div>

                            <div id="statusMessage"></div>

                            <div class="mb-3">
                                <label for="mobile" class="form-label">Email Mobile Number</label>
                                <input type="text" id="user_input" name="user_input" required class="form-control"
                                    placeholder="Enter Email Or mobile">
                            </div>

                            <div class="mb-3">
                                <button type="submit" id="sendOtpBtn" class="btn btn-primary w-100">Send OTP</button>
                            </div>

                            <div class="mb-3 text-center">
                                <h6>Return to <a href="{{ route('login') }}" class="text-purple">Email Login</a></h6>
                            </div>

                            <div class="text-center">
                                <p class="fw-medium text-gray">2018 - {{ date("Y") }} © {{ config("app.name") }}</p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- OTP Modal -->
    <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <form id="verifyOtpForm">
                    @csrf
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title">OTP Verification</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center py-4">
                        <div id="otpStatusMessage" class="mb-3"></div>

                        <label class="form-label fw-semibold mb-2">Enter the 6-digit OTP sent to your
                            number/email</label>

                        <div class="d-flex justify-content-center otp-box mb-3">
                            @for ($i = 1; $i <= 6; $i++)
                                <input type="text" class="form-control otp-digit" maxlength="1"
                                    id="otp{{ $i }}">
                            @endfor
                        </div>

                        <input type="hidden" id="hiddenOtp" name="otp">
                        <input type="hidden" id="hiddenMobile" name="mobile">

                        <p id="otpTimer" class="text-danger small fw-bold blinking"></p>
                    </div>

                    <div class="modal-footer flex-column">
                        <button type="submit" id="verifyOtpBtn" class="btn btn-success w-100 mb-2">
                            Verify OTP
                        </button>

                        <button type="button" id="resendOtpBtn" class="btn btn-outline-warning w-100">
                            Resend OTP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed top-0 start-0 p-3" style="z-index: 1055;">
        <div id="dynamicToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="dynamicToastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>


    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

   <script>
    $(document).ready(function () {
    let timerInterval = null;
    init();

    function init() {
        bindFormEvents();
        bindOtpInputEvents();
        bindResendOtpEvent();
    }

    function showToast(message, type = 'success') {
        const $toast = $('#dynamicToast');
        const $message = $('#dynamicToastMessage');
        $message.text(message);
        $toast.removeClass('bg-success bg-danger bg-warning')
            .addClass(type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-warning');
        new bootstrap.Toast($toast[0]).show();
    }

    function bindFormEvents() {
        $('#mobileLoginForm').on('submit', handleSendOtp);
        $('#verifyOtpForm').on('submit', handleVerifyOtp);
    }

    function bindResendOtpEvent() {
        $('#resendOtpBtn').on('click', handleResendOtp);
    }

    function bindOtpInputEvents() {
        $('.otp-digit').on('input', handleOtpInput);
        $('.otp-digit').on('keydown', handleOtpBackspace);
        $('.otp-digit').on('paste', handleOtpPaste);
    }

    function handleOtpInput() {
        if ($(this).val().length === 1) {
            $(this).next('.otp-digit').focus();
        }
        collectOtp();
    }

    function handleOtpBackspace(e) {
        if (e.key === 'Backspace' && !$(this).val()) {
            $(this).prev('.otp-digit').focus();
        }
    }

    function handleOtpPaste(e) {
        e.preventDefault();
        const paste = (e.originalEvent || e).clipboardData.getData('text');
        if (/^\d{6}$/.test(paste)) {
            $('.otp-digit').each((i, el) => $(el).val(paste[i]));
            collectOtp();
            $('.otp-digit').last().focus();
        }
    }

    function collectOtp() {
        const otp = $('.otp-digit').map(function () {
            return $(this).val();
        }).get().join('');
        $('#hiddenOtp').val(otp);
    }

    function handleSendOtp(e) {
        e.preventDefault();
        const user_input = $('#user_input').val();
        const token = $('input[name="_token"]').val();
        $('#statusMessage').html('');
        $('#sendOtpBtn').prop('disabled', true).text('Sent');
        $.post("{{ route('send.otp') }}", { _token: token, user_input })
            .done(response => {
                $('#statusMessage').html(`<p class="text-success">${response.message}</p>`);
                localStorage.setItem('user_input', user_input);
                $('#hiddenMobile').val(user_input);
                $('#otpModal').modal('show');
                $('.otp-digit').first().focus();
                startOtpTimer();
            })
            .fail(xhr => {
                $('#sendOtpBtn').prop('disabled', false).text('Send OTP');
                const res = xhr.responseJSON;
                if (res?.is_vendor) {
                    $('#statusMessage').html(`
                        <div class="alert alert-warning text-center fw-bold shadow-sm p-3">
                            <i class="ti ti-alert-triangle fs-20 me-1"></i> ${res.message}
                            <div class="mt-2"><a href="${res.redirect || '{{ route("superadmin.vendors.login") }}'}" class="btn btn-sm btn-primary">Go to Vendor Login</a></div>
                        </div>
                    `);
                    setTimeout(() => {
                        window.location.href = res.redirect || '{{ route("superadmin.vendors.login") }}';
                    }, 2000);
                    return;
                }
                if (xhr.status === 403 && res?.message) {
                    showUnverifiedEmailButton(res.message, user_input);
                } else {
                    $('#statusMessage').html(`<p class="text-danger">${res?.message || 'Failed to send OTP.'}</p>`);
                }
            });
    }

    function handleVerifyOtp(e) {
        e.preventDefault();
        const otp = $('#hiddenOtp').val();
        const user_input = localStorage.getItem('user_input');
        const token = $('input[name="_token"]').val();
        $('#verifyOtpBtn').prop('disabled', true).text('Verifying...');
        $('#otpStatusMessage').html('');
        $('#resendOtpBtn').hide();
        $.post("{{ route('verify.otp') }}", { _token: token, otp, user_input })
            .done(response => {
                $('#otpStatusMessage').html(`<p class="text-success">${response.message}</p>`);
                localStorage.removeItem('user_input');
                clearInterval(timerInterval);
                setTimeout(() => {
                    window.location.href = response.redirect_to || "{{ url('dashboard') }}";
                }, 1500);
            })
            .fail(xhr => {
                $('#verifyOtpBtn').prop('disabled', false).text('Verify OTP');
                const res = xhr.responseJSON;
                if (res?.type === 'expired_otp') {
                    $('#otpStatusMessage').html(`<p class="text-danger">${res.message}</p>`);
                    $('#resendOtpBtn').show();
                } else {
                    $('#otpStatusMessage').html(`<p class="text-danger">${res?.message || 'Invalid OTP.'}</p>`);
                }
            });
    }

    function handleResendOtp() {
        const token = $('input[name="_token"]').val();
        const user_input = localStorage.getItem('user_input');
        $('#resendOtpBtn').prop('disabled', true).text('Resending...');
        $.post("{{ route('send.otp') }}", { _token: token, user_input })
            .done(response => {
                $('#otpStatusMessage').html(`<p class="text-success">${response.message}</p>`);
                $('#resendOtpBtn').hide().prop('disabled', false).text('Resend OTP');
                startOtpTimer();
            })
            .fail(() => {
                $('#otpStatusMessage').html(`<p class="text-danger">Failed to resend OTP.</p>`);
                $('#resendOtpBtn').prop('disabled', false).text('Resend OTP');
            });
    }

    function startOtpTimer(duration = 600) {
        let timeRemaining = duration;
        clearInterval(timerInterval);
        $('#verifyOtpBtn').prop('disabled', false);
        $('#resendOtpBtn').hide();
        timerInterval = setInterval(() => {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = (timeRemaining % 60).toString().padStart(2, '0');
            $('#otpTimer').text(`OTP expires in: ${minutes}:${seconds}`);
            if (--timeRemaining < 0) {
                clearInterval(timerInterval);
                $('#otpTimer').text('OTP has expired.');
                $('#verifyOtpBtn').prop('disabled', true);
                $('#resendOtpBtn').show();
            }
        }, 1000);
    }

    function showUnverifiedEmailButton(message, email) {
        $('#statusMessage').html(`
            <div class="alert alert-warning d-flex flex-column align-items-start">
                <button type="button" class="btn btn-warning btn-sm text-start w-100 mb-2" id="resendVerificationBtn">
                    ${message}
                </button>
            </div>
        `);

        let cooldownTimer;
        const cooldownDuration = 300; // 5 minutes in seconds
        const $btn = $('#resendVerificationBtn');
        const originalMessage = message;

        $btn.on('click', function () {
            if (!email) {
                showToast('Please enter a valid email address.', 'warning');
                return;
            }

            // Indicate sending or resending
           const isResend = $btn.text().toLowerCase().includes('resend');
    $btn.prop('disabled', true)
        .removeClass('btn-warning btn-success btn-secondary')
        .addClass('btn-secondary d-flex justify-content-center align-items-center text-center')
        .text(isResend ? 'Resending...' : 'Sending...');

            $.post('{{ route("resend.verification") }}', {
                _token: '{{ csrf_token() }}',
                email: email
            })
                .done(response => {
                    showToast(response.message || 'Verification email has been sent.', 'success');

                    $btn
                        .text('Sent')
                        .removeClass('btn-secondary btn-warning')
                        .addClass('btn-success')
                        .prop('disabled', true);

                    startCooldown($btn, cooldownDuration);
                })
                .fail(xhr => {
                    let msg = 'Something went wrong.';
                    if (xhr.responseJSON?.message) {
                        msg = xhr.responseJSON.message;
                    }

                    showToast(msg, 'error');
                    $btn
                        .prop('disabled', false)
                        .removeClass('btn-secondary btn-success')
                        .addClass('btn-warning')
                        .text(originalMessage);
                });
        });

        function startCooldown(button, duration = 300) {
            let remaining = duration;

            button.prop('disabled', true)
                .removeClass('btn-warning btn-link btn-secondary')
                .addClass('btn-success w-100 d-flex justify-content-center align-items-center text-center');

            button.text(updateCooldownText(remaining));

            cooldownTimer = setInterval(() => {
                remaining--;
                if (remaining <= 0) {
                    clearInterval(cooldownTimer);
                    button.prop('disabled', false)
                        .removeClass('btn-success')
                        .addClass('btn-warning w-100 d-flex justify-content-center align-items-center text-center')
                        .text('Resend Verification Email');
                } else {
                    button.text(updateCooldownText(remaining));
                }
            }, 1000);
        }

        function updateCooldownText(secondsLeft) {
            const minutes = Math.floor(secondsLeft / 60);
            const seconds = secondsLeft % 60;
            return `You can resend the verification email in ${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
    }
});

</script>


</body>

</html>
