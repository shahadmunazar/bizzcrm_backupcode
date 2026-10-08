<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description"
        content="Streamline your business with our advanced CRM template. Easily integrate and customize to manage sales, support, and customer interactions efficiently. Perfect for any business size" />
    <meta name="keywords"
        content="Advanced CRM template, customer relationship management, business CRM, sales optimization, customer support software, CRM integration, customizable CRM, business tools, enterprise CRM solutions" />
    <meta name="author" content="{{ config("app.name") }}" />
    <meta name="robots" content="index, follow" />

    <!-- Title -->
    <title>Login | {{ config('app.name') }}</title>

    <!-- CSRF Token for Forms -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}" />

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/img/favicon.png') }}" type="image/x-icon" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.css') }}" />

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
</head>

<body class="account-page">

    <!-- Success/Error messages from session -->
    <div id="sessionMessageContainer" class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1060; margin-top: 20px;">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <div class="main-wrapper">
        <div class="account-content">
            <div class="d-flex flex-wrap w-100 vh-100 overflow-hidden account-bg-01">
                <div
                    class="d-flex align-items-center justify-content-center flex-wrap vh-100 overflow-auto p-4 w-50 bg-backdrop">
                    <form id="loginForm" method="POST" class="flex-fill" novalidate>
                        @csrf
                        <div class="mx-auto mw-450">
                            <div class="text-center mb-4">
                                <img src="{{ asset('assets/img/logo.png') }}" class="img-fluid" alt="Logo" />
                            </div>
                            <div class="mb-4">
                                <h4 class="mb-2 fs-20">Sign In</h4>
                                <p>Access the CRMS panel using your email and passcode.</p>
                            </div>

                            <!-- Verification alert container -->
                            <div id="verificationAlertContainer" class="mt-2"></div>

                            <div class="mb-3">
                                <label class="col-form-label" for="emailInput">Email Address</label>
                                <div class="position-relative">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-mail"></i>
                                    </span>
                                    <input id="emailInput" type="email" name="email" value="{{ old('email') }}"
                                        class="form-control @error('email') is-invalid @enderror" required />
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="col-form-label" for="passwordInput">Password</label>
                                <div class="pass-group position-relative">
                                    <input id="passwordInput" type="password" name="password"
                                        class="pass-input form-control @error('password') is-invalid @enderror" required />
                                    <span class="ti toggle-password ti-eye-off" role="button"
                                        aria-label="Toggle password visibility" style="cursor:pointer;"></span>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="form-check form-check-md d-flex align-items-center">
                                    <input class="form-check-input" type="checkbox" name="remember" id="checkbox-md"
                                        {{ old('remember') ? 'checked' : '' }} />
                                    <label class="form-check-label" for="checkbox-md">Remember Me</label>
                                </div>
                                <div class="text-end">
                                    <a style="margin-right:5px;" href="{{ route('login.mobile') }}"
                                        class="text-primary fw-medium link-hover d-block">Login With OTP</a>
                                    <a style="margin-left:5px;" href="{{ route('forget.password') }}"
                                        class="text-primary fw-medium link-hover">Forgot Password?</a>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary w-100">Sign In</button>
                            </div>
                            <div class="mb-3">
                                <h6>New on our platform? <a href="{{ url('register') }}"
                                        class="text-purple link-hover">Create an account</a></h6>
                            </div>
                            <div class="mb-3">
                                <h6>Are you a vendor? <a href="{{ route('superadmin.vendors.login') }}"
                                        class="text-purple link-hover">Vendor Login</a></h6>
                            </div>
                            <div class="form-set-login or-text mb-3">
                                <h4>OR</h4>
                            </div>
                            <div
                                class="d-flex align-items-center justify-content-center flex-wrap mb-3 gap-2 gap-md-0 reg-with">
                                <div class="text-center me-2 flex-fill">
                                    <a href="{{ route('facebook.login', ['action' => 'login']) }}"
                                        class="br-10 p-2 px-4 btn bg-pending d-flex align-items-center justify-content-center">
                                        <img class="img-fluid m-1"
                                            src="{{ asset('assets/img/icons/facebook-logo.svg') }}" alt="Facebook" />
                                    </a>
                                </div>
                                <div class="text-center me-2 flex-fill">
                                    <a href="{{ route('google.login') }}"
                                        class="br-10 p-2 px-2 btn bg-white d-flex align-items-center justify-content-center ggbtn">
                                        <img class="img-fluid m-1"
                                            src="{{ asset('assets/img/icons/google-logo.svg') }}" alt="Google" />
                                    </a>
                                </div>
                                <div class="text-center flex-fill">
                                    <a href="#"
                                        class="bg-dark br-10 p-2 px-4 btn btn-dark d-flex align-items-center justify-content-center">
                                        <img class="img-fluid m-1"
                                            src="{{ asset('assets/img/icons/apple-logo.svg') }}" alt="Apple" />
                                    </a>
                                </div>
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

    <!-- Toast container -->
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

    <!-- JS dependencies -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>

    <script>
        $(document).ready(function () {
            const $form = $('#loginForm');
            const $verificationAlertContainer = $('#verificationAlertContainer');
            let resendTimer = null;
            let originalButtonText = '';

            // Check for CSRF error message from session storage after page reload
            if (typeof sessionStorage !== 'undefined') {
                const csrfError = sessionStorage.getItem('csrf_error');
                if (csrfError) {
                    sessionStorage.removeItem('csrf_error');
                    showToast(csrfError, 'error');
                    $verificationAlertContainer.html(
                        `<div class="alert alert-danger text-center">${csrfError}</div>`
                    );
                }
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // Global error handler for unhandled AJAX errors
                statusCode: {
                    419: function(xhr) {
                        handleCsrfMismatch();
                    },
                    401: function(xhr) {
                        handleSessionExpired();
                    },
                    403: function(xhr) {
                        const res = xhr.responseJSON;
                        const errorMsg = res?.error || res?.message || 'Access forbidden.';
                        showToast(errorMsg, 'error');
                        $verificationAlertContainer.html(
                            `<div class="alert alert-danger text-center">${errorMsg}</div>`
                        );
                    },
                    500: function(xhr) {
                        const res = xhr.responseJSON;
                        const errorMsg = res?.error || res?.message || 'Server error. Please try again later.';
                        showToast(errorMsg, 'error');
                        $verificationAlertContainer.html(
                            `<div class="alert alert-danger text-center">${errorMsg}</div>`
                        );
                    }
                }
            });

            // Auto-dismiss session messages after 5 seconds
            setTimeout(function() {
                $('#sessionMessageContainer .alert').fadeOut(function() {
                    $(this).alert('close');
                });
            }, 5000);

            function showToast(message, type = 'success') {
                const $toast = $('#dynamicToast');
                const $toastMessage = $('#dynamicToastMessage');
                $toastMessage.text(message);
                $toast.removeClass('bg-success bg-danger bg-warning')
                    .addClass(type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-warning');
                new bootstrap.Toast($toast[0]).show();
            }
            function startResendCooldown($btn, duration = 300) {
                let timeLeft = duration;
                $btn.prop('disabled', true)
                    .removeClass('btn-primary btn-warning btn-secondary')
                    .addClass('btn-success w-100 text-center d-flex justify-content-center align-items-center')
                    .text(`Resend in ${timeLeft}s`);

                resendTimer = setInterval(() => {
                    timeLeft--;
                    if (timeLeft > 0) {
                        $btn.text(`Resend in ${timeLeft}s`);
                    } else {
                        clearInterval(resendTimer);
                        resendTimer = null;

                        $btn.prop('disabled', false)
                            .removeClass('btn-success')
                            .addClass('btn-warning w-100 text-center d-flex justify-content-center align-items-center')
                            .text(originalButtonText || 'Resend Verification Email');
                    }
                }, 1000);
            }
            function clearVerificationMessage() {
                $verificationAlertContainer.empty();
                originalButtonText = '';
            }
            function refreshCsrfToken() {
                try {
                return $.getJSON('/refresh-csrf')
                    .done(function (data) {
                        if (data.token) {
                            $('meta[name="csrf-token"]').attr('content', data.token);
                            $('input[name="_token"]').val(data.token);
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': data.token
                                }
                            });
                        }
                        })
                        .fail(function(xhr) {
                            // If CSRF refresh fails, handle accordingly
                            if (xhr.status === 419) {
                                console.warn('CSRF token refresh failed. Reloading page...');
                                window.location.reload();
                            } else if (xhr.status === 401 || xhr.status === 403) {
                                console.warn('Session expired during CSRF refresh. Redirecting to login...');
                                handleSessionExpired();
                            }
                        });
                } catch (error) {
                    console.error('Error refreshing CSRF token:', error);
                    window.location.reload();
                }
            }

            function handleCsrfMismatch() {
                try {
                    console.warn('CSRF token mismatch detected. Reloading page...');
                    // Show error message before reload
                    const errorMsg = 'Session expired. Please refresh the page and try again.';
                    showToast(errorMsg, 'error');

                    // Store error message in session storage to show after reload
                    if (typeof sessionStorage !== 'undefined') {
                        sessionStorage.setItem('csrf_error', errorMsg);
                    }

                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } catch (error) {
                    console.error('Error handling CSRF mismatch:', error);
                    window.location.reload();
                }
            }

            function handleSessionExpired() {
                try {
                    console.warn('Session expired detected. Redirecting to login...');
                    showToast('Your session has expired. Redirecting to login...', 'warning');
                    setTimeout(function() {
                        // Check if we're already on login page
                        const currentPath = window.location.pathname;
                        const loginPath = "{{ route('login') }}";

                        // If already on login page, just reload
                        if (currentPath === loginPath || currentPath.includes('/login')) {
                            window.location.reload();
                        } else {
                            // Redirect to login page
                            window.location.href = loginPath;
                        }
                    }, 1500);
                } catch (error) {
                    console.error('Error handling session expiration:', error);
                    // Fallback: check if on login page, otherwise redirect
                    const currentPath = window.location.pathname;
                    const loginPath = "{{ route('login') }}";
                    if (currentPath === loginPath || currentPath.includes('/login')) {
                        window.location.reload();
                    } else {
                        window.location.href = loginPath;
                    }
                }
            }
            $form.submit(function (e) {
                e.preventDefault();
                try {
                clearVerificationMessage();
                const $signInBtn = $form.find('button[type="submit"]');
                $signInBtn.prop('disabled', true).text('Signing in...');

                refreshCsrfToken().then(() => {
                        try {
                    $.ajax({
                        url: "{{ route('post.login') }}",
                        method: 'POST',
                        data: $form.serialize(),
                        success: function (response) {
                                    try {
                            if (response.message) showToast(response.message, 'success');
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                            window.location.reload();
                                        }
                                    } catch (error) {
                                        console.error('Error in success handler:', error);
                                window.location.reload();
                            }
                        },
                        error: function (xhr) {
                                    try {
                                        // Check for CSRF token mismatch (419 status)
                                        if (xhr.status === 419) {
                                            handleCsrfMismatch();
                                            return;
                                        }

                                        // Check for session expiration (401 Unauthorized or 403 Forbidden)
                                        if (xhr.status === 401 || xhr.status === 403) {
                                            handleSessionExpired();
                                            return;
                                        }

                            $signInBtn.prop('disabled', false).text('Sign In');

                            // Clear old errors
                            $form.find('.invalid-feedback').remove();
                            $form.find('.is-invalid').removeClass('is-invalid');
                            $verificationAlertContainer.empty();

                            const res = xhr.responseJSON;
                            let errorMessage = '';

                            // Handle different error status codes
                            if (xhr.status === 422) {
                                // Validation errors
                                if (res && res.errors) {
                                    // Display validation errors
                                    const errorList = [];
                                    $.each(res.errors, function (field, messages) {
                                        const $input = $form.find(`[name="${field}"]`);
                                        $input.addClass('is-invalid');
                                        const msg = Array.isArray(messages) ? messages[0] : messages;
                                        $input.after(`<div class="invalid-feedback">${msg}</div>`);
                                        errorList.push(msg);
                                    });

                                    // Also show in alert container
                                    if (errorList.length > 0) {
                                        errorMessage = errorList.join('<br>');
                                        $verificationAlertContainer.html(
                                            `<div class="alert alert-danger">${errorMessage}</div>`
                                        );
                                    }
                                } else if (res && res.error) {
                                    // Custom error message (e.g. invalid password, user not found)
                                    errorMessage = res.error;
                                    $verificationAlertContainer.html(
                                        `<div class="alert alert-danger text-center">${errorMessage}</div>`
                                    );
                                    showToast(errorMessage, 'error');
                                } else if (res && res.message) {
                                    errorMessage = res.message;
                                    $verificationAlertContainer.html(
                                        `<div class="alert alert-danger text-center">${errorMessage}</div>`
                                    );
                                    showToast(errorMessage, 'error');
                                } else {
                                    errorMessage = 'Validation failed. Please check your input.';
                                    $verificationAlertContainer.html(
                                        `<div class="alert alert-danger text-center">${errorMessage}</div>`
                                    );
                                    showToast(errorMessage, 'error');
                                }
                            } else if (xhr.status === 409 && res && res.resend) {
                                // Email not verified, show resend button
                                originalButtonText = res.error || 'Please verify your email.';
                                const html = `
                                    <button id="resendVerificationBtn" type="button"
                                        class="btn btn-warning w-100 text-center d-flex justify-content-center align-items-center">
                                        ${originalButtonText}
                                    </button>
                                `;
                                $verificationAlertContainer.html(html);
                                showToast(originalButtonText, 'warning');
                            } else if (xhr.status === 403) {
                                // Forbidden errors (already handled above, but just in case)
                                errorMessage = res?.error || res?.message || 'Access forbidden. Please contact administrator.';
                                $verificationAlertContainer.html(
                                    `<div class="alert alert-danger text-center">${errorMessage}</div>`
                                );
                                showToast(errorMessage, 'error');
                            } else if (xhr.status === 404) {
                                // Not found errors
                                errorMessage = res?.error || res?.message || 'Resource not found.';
                                $verificationAlertContainer.html(
                                    `<div class="alert alert-danger text-center">${errorMessage}</div>`
                                );
                                showToast(errorMessage, 'error');
                            } else if (xhr.status === 500) {
                                // Server errors
                                errorMessage = res?.error || res?.message || 'Server error. Please try again later.';
                                $verificationAlertContainer.html(
                                    `<div class="alert alert-danger text-center">${errorMessage}</div>`
                                );
                                showToast(errorMessage, 'error');
                            } else {
                                // All other errors - try to extract error message
                                if (res) {
                                    errorMessage = res.error || res.message || res.errors || 'An error occurred. Please try again.';

                                    // Handle array of errors
                                    if (Array.isArray(errorMessage)) {
                                        errorMessage = errorMessage.join('<br>');
                                    }

                                    // Handle object of errors
                                    if (typeof errorMessage === 'object') {
                                        const errorList = [];
                                        $.each(errorMessage, function(key, val) {
                                            if (Array.isArray(val)) {
                                                errorList.push(val.join(', '));
                                            } else {
                                                errorList.push(val);
                                            }
                                        });
                                        errorMessage = errorList.join('<br>');
                                    }
                                } else {
                                    // No response JSON, try to get text
                                    errorMessage = xhr.responseText || 'Something went wrong. Please try again.';
                                }

                                $verificationAlertContainer.html(
                                    `<div class="alert alert-danger text-center">${errorMessage}</div>`
                                );
                                showToast(errorMessage, 'error');
                            }
                                    } catch (error) {
                                        console.error('Error in error handler:', error);
                                        const fallbackMsg = 'An unexpected error occurred. Please try again.';
                                        $verificationAlertContainer.html(
                                            `<div class="alert alert-danger text-center">${fallbackMsg}</div>`
                                        );
                                        showToast(fallbackMsg, 'error');

                                        if (xhr.status === 419) {
                                            handleCsrfMismatch();
                                        } else if (xhr.status === 401 || xhr.status === 403) {
                                            handleSessionExpired();
                                        }
                                    }
                                }
                            });
                        } catch (error) {
                            console.error('Error in AJAX call:', error);
                            $signInBtn.prop('disabled', false).text('Sign In');
                            handleCsrfMismatch();
                        }
                    }).catch(function(error) {
                        console.error('Error refreshing CSRF token:', error);
                        $signInBtn.prop('disabled', false).text('Sign In');
                        handleCsrfMismatch();
                    });
                } catch (error) {
                    console.error('Error in form submit handler:', error);
                    handleCsrfMismatch();
                }
            });
            $verificationAlertContainer.on('click', '#resendVerificationBtn', function () {
                try {
                const $btn = $(this);
                const email = $form.find('input[name="email"]').val().trim();

                if (!email) {
                    showToast('Please enter your email to resend verification.', 'warning');
                    return;
                }
                $btn.prop('disabled', true)
                    .removeClass('btn-warning btn-secondary btn-link text-white')
                    .addClass('btn-success w-100 d-flex justify-content-center align-items-center text-center')
                    .text('Sending...');

                refreshCsrfToken().then(() => {
                        try {
                    $.ajax({
                        url: '{{ route("resend.verification") }}',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            email: email
                        },
                        success: function (response) {
                                    try {
                            const successMessage = response.message || 'Verification email sent. Please check your inbox.';
                            showToast(successMessage, 'success');

                            originalButtonText = successMessage;
                            $btn.prop('disabled', true)
                                .removeClass('btn-warning btn-secondary')
                                .addClass('btn-success')
                                .text(successMessage);

                            startResendCooldown($btn, 300);
                                    } catch (error) {
                                        console.error('Error in resend success handler:', error);
                                        window.location.reload();
                                    }
                        },
                        error: function (xhr) {
                                    try {
                                        // Check for CSRF token mismatch (419 status)
                                        if (xhr.status === 419) {
                                            handleCsrfMismatch();
                                            return;
                                        }

                                        // Check for session expiration (401 Unauthorized or 403 Forbidden)
                                        if (xhr.status === 401 || xhr.status === 403) {
                                            handleSessionExpired();
                                            return;
                                        }

                            const res = xhr.responseJSON;
                            let errorMsg = '';

                            if (res) {
                                errorMsg = res.error || res.message || res.errors || 'Something went wrong. Please try again later.';

                                // Handle array of errors
                                if (Array.isArray(errorMsg)) {
                                    errorMsg = errorMsg.join('<br>');
                                }

                                // Handle object of errors
                                if (typeof errorMsg === 'object') {
                                    const errorList = [];
                                    $.each(errorMsg, function(key, val) {
                                        if (Array.isArray(val)) {
                                            errorList.push(val.join(', '));
                                        } else {
                                            errorList.push(val);
                                        }
                                    });
                                    errorMsg = errorList.join('<br>');
                                }
                            } else {
                                errorMsg = xhr.responseText || 'Something went wrong. Please try again later.';
                            }

                            showToast(errorMsg, 'error');
                            $verificationAlertContainer.html(
                                `<div class="alert alert-danger text-center">${errorMsg}</div>`
                            );
                            $btn.prop('disabled', false)
                                .removeClass('btn-secondary btn-success')
                                .addClass('btn-warning')
                                .text(originalButtonText || 'Resend Verification Email');
                                    } catch (error) {
                                        console.error('Error in resend error handler:', error);
                                        const fallbackMsg = 'An unexpected error occurred. Please try again.';
                                        showToast(fallbackMsg, 'error');
                                        $verificationAlertContainer.html(
                                            `<div class="alert alert-danger text-center">${fallbackMsg}</div>`
                                        );

                                        if (xhr.status === 419) {
                                            handleCsrfMismatch();
                                        } else if (xhr.status === 401 || xhr.status === 403) {
                                            handleSessionExpired();
                                        }
                                    }
                                }
                            });
                        } catch (error) {
                            console.error('Error in resend AJAX call:', error);
                            $btn.prop('disabled', false).addClass('btn-warning');
                            handleCsrfMismatch();
                        }
                    }).catch(function(error) {
                        console.error('Error refreshing CSRF token for resend:', error);
                        $btn.prop('disabled', false).addClass('btn-warning');
                        handleCsrfMismatch();
                    });
                } catch (error) {
                    console.error('Error in resend button click handler:', error);
                    handleCsrfMismatch();
                }
            });
            $form.find('input[name="email"], input[name="password"]').on('input', function () {
                clearVerificationMessage();
                $(this).removeClass('is-invalid').next('.invalid-feedback').remove();
            });
        });
    </script>
    @if(session('confirm_api_logout'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Logged Out',
                    text: 'You logged out from the mobile app. Do you want to stay logged in here?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Keep Me Logged In',
                    cancelButtonText: 'Logout',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({ url: '/cancel-api-logout', method: 'POST', data: { _token: '{{ csrf_token() }}' }, global: false, success: function() { window.location.href = '/'; }});
                    } else {
                        $.ajax({ url: '/logout', method: 'POST', data: { _token: '{{ csrf_token() }}' }, global: false, complete: function() { window.location.href = '{{ route("login") }}'; }});
                    }
                });
            }
        });
    </script>
    @endif
</body>

</html>
