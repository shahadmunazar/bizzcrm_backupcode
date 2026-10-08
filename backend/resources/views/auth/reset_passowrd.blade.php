<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Reset Password | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>


<body class="account-page">
    <div class="main-wrapper">
        <div class="account-content">
            <div class="d-flex flex-wrap w-100 vh-100 overflow-hidden account-bg-03">
                <div class="d-flex align-items-center justify-content-center flex-wrap vh-100 overflow-auto p-4 w-50 bg-backdrop">
                    <form id="resetPasswordForm" class="flex-fill">
                        @csrf
                        <div class="mx-auto mw-450">
                            <div class="text-center mb-4">
                                <img src="{{ asset('assets/img/logo.png') }}" class="img-fluid" alt="Logo">
                            </div>
                            <div class="mb-4">
                                <h4 class="mb-2 fs-20">Reset Your Password</h4>
                                <p>Enter your new password below to reset it.</p>
                            </div>

                            <div id="statusMessage"></div>

                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <div class="position-relative">
                                    <input type="password" id="password" name="password" required class="form-control pe-5" minlength="8">
                                    <span class="toggle-password position-absolute top-50 end-0 translate-middle-y me-3" toggle="#password">
                                        <i class="fa fa-eye-slash cursor-pointer"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <div class="position-relative">
                                    <input type="password" id="password_confirmation" name="password_confirmation" required class="form-control pe-5" minlength="8">
                                    <span class="toggle-password position-absolute top-50 end-0 translate-middle-y me-3" toggle="#password_confirmation">
                                        <i class="fa fa-eye-slash cursor-pointer"></i>
                                    </span>
                                </div>
                            </div>




                            <div class="mb-3">
                                <button type="submit" id="submitBtn" class="btn btn-primary w-100">Reset Password</button>
                            </div>

                            <div class="mb-3 text-center">
    <button type="button" id="resendBtn" class="btn btn-outline-primary w-100" style="display:none;">
        Resend Reset Email
    </button>
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

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
   <script>
$(document).ready(function () {
    const urlToken = window.location.pathname.split('/forget-password/')[1]?.split('?')[0];
    if (urlToken) {
        localStorage.setItem('reset_token', urlToken);
    }



    const tokenFromStorage = localStorage.getItem('reset_token') || '';
    const submitBtn = $('#submitBtn');
    const resendBtn = $('#resendBtn');
    const statusMessage = $('#statusMessage');

    function resetFormState() {
        submitBtn.prop('disabled', false).show().text('Reset Password');
        resendBtn.hide();
        statusMessage.html('');
    }

    function startResendTimer(duration = 60) {
        let remaining = duration;
        submitBtn.prop('disabled', true).text(`Wait ${remaining}s...`);
        resendBtn.hide();
        const interval = setInterval(() => {
            remaining--;
            if (remaining <= 0) {
                clearInterval(interval);
                submitBtn.hide();
                resendBtn.show();
                statusMessage.append('<p class="text-info">You can now resend the reset email.</p>');
            } else {
                submitBtn.text(`Wait ${remaining}s...`);
            }
        }, 1000);
    }
$(document).on('click', '.toggle-password', function () {
    const input = $($(this).attr('toggle'));
    const icon = $(this).find('i');

    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    } else {
        input.attr('type', 'password');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    }
});

    $('#resetPasswordForm').submit(function (e) {
        e.preventDefault();
        const form = $(this);
        const csrfToken = form.find('input[name="_token"]').val();

        const formData = {
            _token: csrfToken,
            token: tokenFromStorage,
            password: form.find('input[name="password"]').val(),
            password_confirmation: form.find('input[name="password_confirmation"]').val()
        };

        submitBtn.prop('disabled', true).text('Resetting...');
        statusMessage.html('');

        $.ajax({
            url: "{{ route('reset.password.submit') }}",
            type: "POST",
            data: formData,
            success: function (response) {
                statusMessage.html(`<div class="alert alert-success">${response.message || 'Password reset successful!'}</div>`);
                localStorage.removeItem('reset_token');
                setTimeout(() => {
                    window.location.href = "{{ route('login') }}";
                }, 5000);
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false).text('Reset Password');

                if (xhr.status === 422 || xhr.status === 403) {
                    const message = xhr.responseJSON.message || 'Token expired.';
                    statusMessage.html(`<div class="alert alert-danger">${message}</div>`);

                    // If token expired or invalid, show resend button
                    if (message.toLowerCase().includes('expired') || message.toLowerCase().includes('invalid')) {
                        startResendTimer(60);
                    }

                } else {
                    statusMessage.html(`<div class="alert alert-danger">Something went wrong. Please try again.</div>`);
                }
            }
        });
    });

    resendBtn.click(function () {
        const emailFromUrl = new URLSearchParams(window.location.search).get('email');
        const csrfToken = $('input[name="_token"]').val();

        if (!emailFromUrl) {
            statusMessage.html(`<div class="alert alert-danger">Missing email. Cannot resend.</div>`);
            return;
        }

        resendBtn.prop('disabled', true).text('Resending...');

        $.ajax({
            url: "{{ route('forget.email') }}",
            type: "POST",
            data: {
                _token: csrfToken,
                email: emailFromUrl
            },
            success: function (response) {
                statusMessage.html(`<div class="alert alert-success">${response.message || 'Reset link resent successfully!'}</div>`);
                resendBtn.prop('disabled', false).text('Resend Reset Email').hide();
                submitBtn.show().prop('disabled', true).text('Reset link resent (wait)');
                startResendTimer(60);
            },
            error: function (xhr) {
                resendBtn.prop('disabled', false).text('Resend Reset Email');
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    statusMessage.html(`<div class="alert alert-danger">${xhr.responseJSON.message}</div>`);
                } else {
                    statusMessage.html(`<div class="alert alert-danger">Resend failed. Please try again later.</div>`);
                }
            }
        });
    });
});
</script>
<style>
.toggle-password i.fa-eye-slash, .toggle-password i.fa-eye{
    font-size:13px;
}
</style>


</body>

</html>
