<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Streamline your business with our advanced CRM template. Easily integrate and customize to manage sales, support, and customer interactions efficiently. Perfect for any business size">
    <meta name="keywords"
        content="Advanced CRM template, customer relationship management, business CRM, sales optimization, customer support software, CRM integration, customizable CRM, business tools, enterprise CRM solutions">
    <meta name="author" content="{{ config("app.name") }}">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title -->
    <title>Vendor Register | {{ config('app.name') }}</title>

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">

    <!-- Favicon -->
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 44px !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1f2937 !important;
            padding-left: 12px !important;
            font-size: 14px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px !important;
            right: 8px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af !important;
        }
        .select2-dropdown {
            border: 1px solid #e5e7eb !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
            z-index: 9999 !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #7c3aed !important;
        }
    </style>

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body class="account-page">

    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <div class="account-content">
            <div class="d-flex flex-wrap w-100 vh-100 overflow-hidden account-bg-02">
                <div
                    class="d-flex align-items-center justify-content-center flex-wrap vh-100 overflow-auto p-4 w-50 bg-backdrop">
                    <form id="registerForm" class="flex-fill" method="POST">
                        <div class="mx-auto mw-450">
                            <div class="text-center mb-4">
                                <img src="assets/img/logo.png" class="img-fluid" alt="Logo">
                            </div>
                            <div class="mb-4">
                                <h4 class="mb-2 fs-20">Register</h4>
                                <p>Create new Vendor account</p>
                            </div>
                            <div class="mb-3">
                                <label class="col-form-label">Name <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-user"></i>
                                    </span>
                                    <input type="text" value="{{ old('name', request('name')) }}" name="name" class="form-control" placeholder="Enter your full name" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="col-form-label">Email Address <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-mail"></i>
                                    </span>
                                    <input type="email" value="{{ old('email', $email ?? request('email')) }}" name="email" class="form-control" placeholder="Enter your email address" required>
                                </div>
                                @if($email ?? request('email'))
                                    <small class="text-success mt-1 d-block fw-semibold">
                                        <i class="ti ti-circle-check me-1"></i> Email address pre-filled from your invitation.
                                    </small>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label class="col-form-label">Phone No. <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-phone"></i>
                                    </span>
                                    <input type="text" value="{{ old('phone', request('phone')) }}" name="phone" class="form-control" placeholder="Enter mobile number" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="col-form-label">Company Name <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-building"></i>
                                    </span>
                                    <input type="text" value="{{ old('company_name', request('company')) }}" name="company_name" class="form-control" placeholder="Enter company name" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="col-form-label">Industry</label>
                                <div class="position-relative">
                                    <select class="form-control" name="industry" required>
                                        <option value="">Select Industry</option>
                                        @foreach($industries as $ind)
                                            <option value="{{ $ind->id }}">{{ $ind->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <!-- Country -->
                            <div class="mb-3">
                                <label class="col-form-label">Country</label>
                                <div class="position-relative">
                                    <span class="input-icon-addon"><i class="ti ti-globe"></i></span>
                                    <select id="countrySelect" class="form-control" name="country_id">
                                        <option value="">Select Country</option>
                                    </select>
                                </div>
                            </div>

                            <!-- State -->
                            <div class="mb-3">
                                <label class="col-form-label">State</label>
                                <div class="position-relative">
                                    <span class="input-icon-addon"><i class="ti ti-building"></i></span>
                                    <select id="stateSelect" class="form-control" name="state_id" disabled>
                                        <option value="">Select State</option>
                                    </select>
                                </div>
                            </div>

                            <!-- City -->
                            <div class="mb-3">
                                <label class="col-form-label">City</label>
                                <div class="position-relative">
                                    <span class="input-icon-addon"><i class="ti ti-building"></i></span>
                                    <select id="citySelect" class="form-control" name="city_id" disabled>
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                            </div>


                            <div class="mb-3">
                                <label class="col-form-label">Pin Code</label>
                                <div class="position-relative">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-building"></i>
                                    </span>
                                    <input type="text" value="{{ old('pincode') }}" name="pincode" id="pincodeInput" class="form-control" placeholder="Enter pin code">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="col-form-label">Address</label>
                                <div class="position-relative">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-map"></i>
                                    </span>
                                    <input type="text" value="{{ old('address') }}" name="address" id="addressInput" class="form-control" placeholder="Enter complete address">
                                </div>
                            </div>


                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="form-check form-check-md d-flex align-items-center">
                                    <input class="form-check-input" type="checkbox" value="" id="checkebox-md"
                                        checked="">
                                    <label class="form-check-label" for="checkebox-md">
                                        I agree to the <a href="javascript:void(0);"
                                            class="text-primary link-hover">Terms & Privacy</a>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary w-100">Sign Up</button>
                            </div>
                            <div class="mb-3">
                                <h6>Already have a vendor account? <a href="{{ route('superadmin.vendors.login') }}"
                                        class="text-purple link-hover"> Sign
                                        In Instead</a></h6>
                            </div>
                            <div class="mb-3">
                                <h6>Are you a user? <a href="{{ url('register') }}"
                                        class="text-purple link-hover">User Signup</a></h6>
                            </div>
                            <div class="form-set-login or-text mb-3">
                                <h4>OR</h4>
                            </div>
                            <div class="d-flex align-items-center justify-content-center flex-wrap mb-3 reg-with">
                                <div class="text-center me-2 flex-fill">
                                    <a href="{{ route('facebook.login', ['action' => 'register']) }}"
                                        class="br-10 p-2 px-4 btn bg-pending  d-flex align-items-center justify-content-center">
                                        <img class="img-fluid m-1" src="assets/img/icons/facebook-logo.svg"
                                            alt="Facebook">
                                    </a>
                                </div>
                                <div class="text-center me-2 flex-fill">
                                    <a href="{{ route('google.login', ['action' => 'register']) }}"
                                        class="br-10 p-2 px-4 btn bg-white d-flex align-items-center justify-content-center ggbtn">
                                        <img class="img-fluid  m-1" src="assets/img/icons/google-logo.svg"
                                            alt="Google">
                                    </a>
                                </div>
                                <div class="text-center flex-fill">
                                    <a href="javascript:void(0);"
                                        class="bg-dark br-10 p-2 px-4 btn btn-dark d-flex align-items-center justify-content-center">
                                        <img class="img-fluid  m-1" src="assets/img/icons/apple-logo.svg"
                                            alt="Apple">
                                    </a>
                                </div>
                            </div>
                            <div class="text-center">
                                <p class="fw-medium text-gray">Copyright &copy; 2018-{{ date("Y") }} - {{ config("app.name") }}</p>
                            </div>
                        </div>
                    </form>

                    <div id="registrationSuccess" class="mx-auto mw-450 text-center" style="display: none;">
                        <div class="mb-4">
                            <img src="assets/img/logo.png" class="img-fluid mb-4" alt="Logo">
                            <div class="mb-3">
                                <i class="ti ti-circle-check-filled text-success" style="font-size: 72px;"></i>
                            </div>
                            <h4 class="mb-2 fs-22">Vendor Account Created Successfully!</h4>
                            <div class="alert alert-warning border-0 shadow-sm text-start mb-3 p-3">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="ti ti-mail-fast fs-20 me-2 text-warning"></i>
                                    <strong class="text-dark">Email Verification Required</strong>
                                </div>
                                <p class="mb-0 text-muted small">
                                    Your vendor partner registration has been recorded. Please check your inbox and click the verification link before logging in.
                                </p>
                            </div>
                            <div class="card bg-light border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <p class="text-muted mb-3 small">
                                        After verifying your email address, you will be directed to the <strong>Vendor Login</strong> page to access your vendor panel.
                                    </p>
                                    <a href="{{ route('superadmin.vendors.login') }}" class="btn btn-primary w-100 mb-2">
                                        <i class="ti ti-login me-1"></i> Go to Vendor Login
                                    </a>
                                    <span class="text-muted small d-block">Redirecting to Vendor Login in <span id="countdownSecs">5</span> seconds...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

    <!-- Bootstrap -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>

    <!-- Feather -->
    <script src="{{ asset('assets/js/feather.min.js') }}" type="text/javascript"></script>

    <!-- Slimscroll -->
    <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}" type="text/javascript"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    {{-- <script src="{{ asset('cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js') }}" data-cf-settings="5cc6697ba11841a3d6262899-|49" defer=""></script> --}}

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


    <!-- Email Verification Modal -->
<div class="modal fade" id="emailVerificationModal" tabindex="-1" aria-labelledby="emailVerificationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="emailVerificationModalLabel">Verify Your Email</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Email already exists but is not verified.</p>
        <p>Would you like us to resend the verification email?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="resendVerificationBtn" class="btn btn-primary">Resend Email</button>
      </div>
    </div>
  </div>
</div>




</body>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        @if(session('error'))
            // Delay slightly to ensure toast DOM is ready
            setTimeout(() => showToast("{{ session('error') }}", 'error'), 100);
        @endif

        $('select[name="industry"]').select2({
            placeholder: "Select Industry",
            allowClear: true,
            width: '100%'
        });

        function showToast(message, type = 'success') {
            const $toast = $('#dynamicToast');
            const $message = $('#dynamicToastMessage');
            $message.text(message);
            $toast.removeClass('bg-success bg-danger bg-warning')
                .addClass(type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-warning');
            new bootstrap.Toast($toast[0]).show();
        }

        function populateSelect($select, data, placeholder = 'Select') {
            $select.empty().append(`<option value="">${placeholder}</option>`);
            if (!data.length) {
                $select.append('<option disabled>No data found</option>');
                return;
            }
            data.forEach(item => {
                $select.append(`<option value="${item.id}">${item.name}</option>`);
            });
            $select.prop('disabled', false);
        }

        // Load countries on page load
        $.get("{{ route('search.country') }}", {
            type: 'country'
        }, function(res) {
            populateSelect($('#countrySelect'), res.data, 'Select Country');
        });

        // When country changes
        $('#countrySelect').on('change', function() {
            const countryId = $(this).val();
            $('#stateSelect').prop('disabled', true).html('<option value="">Select State</option>');
            $('#citySelect').prop('disabled', true).html('<option value="">Select City</option>');

            if (!countryId) return;

            $.get("{{ route('search.country') }}", {
                type: 'state',
                country_id: countryId
            }, function(res) {
                populateSelect($('#stateSelect'), res.data, 'Select State');
            });
        });

        // When state changes
        $('#stateSelect').on('change', function() {
            const stateId = $(this).val();
            $('#citySelect').prop('disabled', true).html('<option value="">Select City</option>');

            if (!stateId) return;

            $.get("{{ route('search.country') }}", {
                type: 'city',
                state_id: stateId
            }, function(res) {
                populateSelect($('#citySelect'), res.data, 'Select City');
            });
        });

        // Form submission with toast feedback
        $('#registerForm').on('submit', function(e) {
            e.preventDefault();

            const $submitBtn = $(this).find(':submit');
            $submitBtn.prop('disabled', true);

            const formData = $(this).serialize();

            $.ajax({
                url: "{{ route('post.vendor.register') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#registerForm').fadeOut(300, function() {
                            $('#registrationSuccess').fadeIn(300);
                        });
                        showToast(response.message || 'Vendor account created successfully!', 'success');
                        let count = 5;
                        const timer = setInterval(function() {
                            count--;
                            $('#countdownSecs').text(count);
                            if (count <= 0) {
                                clearInterval(timer);
                                window.location.href = response.redirect || "{{ route('superadmin.vendors.login') }}";
                            }
                        }, 1000);
                    } else {
                        showToast(response.message || "Something went wrong.", 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = "An error occurred. Please check your inputs.";
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON
                        .status === 409) {
                        const email = $('#registerForm').find('input[name="email"]').val();
                        $('#resendVerificationBtn').data('email', email);
                        new bootstrap.Modal(document.getElementById(
                            'emailVerificationModal')).show();
                        return;
                    }
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors).flat().join('\n');
                    }
                    showToast(errorMessage, 'error');
                },
                complete: function() {
                    $submitBtn.prop('disabled', false);
                }
            });
        });

        $('#resendVerificationBtn').on('click', function() {
    const email = $(this).data('email');

    $.ajax({
        url: "{{ route('resend.verification') }}",
        method: 'POST',
        data: {
            email: email,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res) {
            $('#emailVerificationModal').modal('hide');
            showToast('Check your email inbox to verify your email, then come back here to Login.', 'success');
        },
        error: function() {
            showToast('Failed to resend verification email.', 'error');
        }
    });
});

    });
</script>


</html>
