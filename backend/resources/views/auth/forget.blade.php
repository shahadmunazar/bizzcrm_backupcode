<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Forgot Password | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Add your Laravel-friendly CSS links -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body class="account-page">

    <div class="main-wrapper">
        <div class="account-content">
            <div class="d-flex flex-wrap w-100 vh-100 overflow-hidden account-bg-03">
                <div class="d-flex align-items-center justify-content-center flex-wrap vh-100 overflow-auto p-4 w-50 bg-backdrop">
<form id="forgotPasswordForm" class="flex-fill">
    @csrf
    <div class="mx-auto mw-450">
        <div class="text-center mb-4">
            <img src="{{ asset('assets/img/logo.png') }}" class="img-fluid" alt="Logo">
        </div>

        <div class="mb-4">
            <h4 class="mb-2 fs-20">Forgot Password?</h4>
            <p>Enter your email, and we'll send you password reset instructions.</p>
        </div>

        <div id="statusMessage"></div> <!-- For success/error messages -->

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="position-relative">
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <button type="submit" id="submitBtn" class="btn btn-primary w-100">Send Reset Link</button>
            <button type="button" id="resendBtn" class="btn btn-secondary w-100" style="display:none;">Resend Email</button>
        </div>

        <div class="mb-3 text-center">
            <h6>Return to <a href="{{ route('login') }}" class="text-purple">Login</a></h6>
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

    <div class="toast-container position-fixed top-0 start-0 p-3" style="z-index: 1055;">
        <div id="dynamicToast" class="toast align-items-center text-white border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
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
    <script src="{{ asset('assets/js/script.js') }}"></script>


    <script>
$(document).ready(function () {
    const statusMessage = $('#statusMessage');
    const submitBtn = $('#submitBtn');
    const resendBtn = $('#resendBtn');
    let timer;

    function showToast(message, type = 'success') {
        const $toast = $('#dynamicToast');
        const $message = $('#dynamicToastMessage');
        $message.text(message);
        $toast.removeClass('bg-success bg-danger bg-warning')
            .addClass(type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-warning');
        new bootstrap.Toast($toast[0]).show();
    }

    function startResendTimer(duration = 300) {
        let timeRemaining = duration;

        submitBtn.prop('disabled', true)
            .removeClass('btn-primary btn-secondary btn-warning')
            .addClass('btn-success w-100')
            .text(`Please wait to Resend Email (${timeRemaining}s)`);

        resendBtn.hide();

        timer = setInterval(() => {
            timeRemaining--;
            if (timeRemaining > 0) {
                submitBtn.text(`Please wait to Resend Email (${timeRemaining}s)`);
            } else {
                clearInterval(timer);
                submitBtn.hide().prop('disabled', true);
                resendBtn.show()
                    .removeClass('btn-secondary btn-success')
                    .addClass('btn-warning w-100')
                    .prop('disabled', false)
                    .text('Resend Email');
                statusMessage.html('<p class="text-info">You can resend the email now.</p>');
            }
        }, 1000);
    }

    function resetFormState() {
        clearInterval(timer);
        submitBtn.prop('disabled', false)
            .show()
            .removeClass('btn-success btn-secondary btn-warning w-100')
            .addClass('btn-primary')
            .text('Send Reset Link');
        resendBtn.hide();
        statusMessage.html('');
        $('#emailVerificationMessage').remove();
    }

    function showEmailVerificationMessage(message) {
        if ($('#emailVerificationMessage').length === 0) {
            const html = `
                <div id="emailVerificationMessage" class="alert alert-warning">
                    <button type="button" id="resendVerificationBtn" class="btn btn-warning w-100">
                        <span>${message}</span>
                    </button>
                </div>`;
            $('#email').closest('.mb-3').before(html);
        }
    }

    function handleResendVerification(email) {
        if (!email) {
            showToast('Please enter a valid email address.', 'warning');
            return;
        }

        const $resendBtn = $('#resendVerificationBtn');
        $resendBtn.prop('disabled', true)
            .removeClass('btn-link btn-warning btn-success')
            .addClass('btn-secondary w-100')
            .text('Sending...');

        $.ajax({
            url: '{{ route("resend.verification") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                email: email
            },
            success: function (response) {
                showToast(response.message || 'Verification email has been sent.', 'success');
                $resendBtn.text('Sent')
                    .removeClass('btn-secondary')
                    .addClass('btn-success text-white w-100');
                startCooldownResendButton($resendBtn, 300, 'Click here to resend');
            },
            error: function (xhr) {
                let msg = 'Something went wrong.';
                if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
                showToast(msg, 'error');
                $resendBtn.prop('disabled', false)
                    .text('Click here to resend')
                    .removeClass('btn-success text-white btn-secondary')
                    .addClass('btn-warning w-100');
            }
        });
    }

    function startCooldownResendButton(button, duration, resetText) {
        let remaining = duration;
        button.prop('disabled', true)
            .removeClass('btn-warning btn-link')
            .addClass('btn-success text-white w-100');

        const interval = setInterval(() => {
            remaining--;
            if (remaining <= 0) {
                clearInterval(interval);
                button.prop('disabled', false)
                    .text(resetText)
                    .removeClass('btn-success text-white w-100')
                    .addClass('btn-warning w-100');
            } else {
                button.text(`Resend In (${remaining}s)`);
            }
        }, 1000);
    }

    $('#forgotPasswordForm').submit(function (e) {
        e.preventDefault();
        const email = $('#email').val();
        const token = $('input[name="_token"]').val();
        statusMessage.html('');
        $('#emailVerificationMessage').remove();

        $.ajax({
            url: "{{ route('forget.email') }}",
            type: "POST",
            data: {
                _token: token,
                email: email
            },
            beforeSend: function () {
                submitBtn.prop('disabled', true)
                    .removeClass('btn-primary btn-success btn-warning')
                    .addClass('btn-secondary')
                    .text('Sending...');
            },
            success: function (response) {
                statusMessage.html(`<p class="text-success">${response.message || 'Reset link sent successfully!'}</p>`);
                startResendTimer();
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false)
                    .removeClass('btn-secondary btn-success btn-warning')
                    .addClass('btn-primary')
                    .text('Send Reset Link');

                if (xhr.status === 403 && xhr.responseJSON?.message) {
                    showEmailVerificationMessage(xhr.responseJSON.message);
                } else if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors?.email) {
                        statusMessage.html(`<p class="text-danger">${errors.email[0]}</p>`);
                    }
                } else if (xhr.responseJSON?.message) {
                    statusMessage.html(`<p class="text-danger">${xhr.responseJSON.message}</p>`);
                } else {
                    statusMessage.html('<p class="text-danger">An error occurred. Please try again.</p>');
                }
            }
        });
    });

    resendBtn.click(function () {
        resetFormState();
    });

    $(document).on('click', '#resendVerificationBtn', function () {
        const email = $('#email').val();
        handleResendVerification(email);
    });
});

</script>



</body>

</html>
