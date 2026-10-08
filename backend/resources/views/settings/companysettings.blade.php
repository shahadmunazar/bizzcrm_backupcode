@extends('layouts.main')

@section('content')
@section('title', 'Company Settings')
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">Settings</h4>
                        </div>
                        <div class="col-4 text-end">
                            <div class="head-icons">
                                <a href="{{ url()->current() }}" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Refresh"><i class="ti ti-refresh-dot"></i></a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Settings Menu -->
                @include('settings.partials.nav')

                <div class="row">
                    <div class="col-xl-3 col-lg-12 theiaStickySidebar">
                        @include('settings.partials.sidebar')
                    </div>

                    <div class="col-xl-9 col-lg-12">
                        <!-- Company Settings -->
                        <div class="card">
                            <div class="card-body">
                                <h4 class="fw-semibold mb-3">Company Settings</h4>
                                <form id="company_settings" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="border-bottom mb-3 pb-3">
                                        <h5 class="fw-semibold mb-1">Company Information</h5>
                                        <p>Provide the company information below</p>
                                    </div>
                                    <div class="border-bottom mb-3 ">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Company Name</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ $box->name }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Company Email Address</label>
                                                <input type="text" name="company_email" class="form-control"
                                                    value="{{ $box->company_email }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Phone Number</label>
                                                <input type="text" name="company_phone" class="form-control"
                                                    value="{{ $box->company_phone }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Fax</label>
                                                <input type="text" name="fax_number" class="form-control"
                                                    value="{{ $box->fax_number }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Website</label>
                                                <input type="text" name="slug" class="form-control"
                                                    value="{{ $box->slug }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-bottom mb-3 pb-3">
                                        <h5 class="fw-semibold mb-1">Company Images</h5>
                                        <p>Provide the company images</p>
                                        <div class="mb-3" id="logo-preview-container" style="{{ empty($box->company_logo) ? 'display:none;' : '' }}">
                                            <label class="form-label d-block">Current Logo:</label>
                                            <div class="position-relative d-inline-block">
                                                <img id="company-logo-preview" src="{{ !empty($box->company_logo) ? asset('storage/' . $box->company_logo) : '' }}"
                                                    alt="Company Logo" style="max-height: 120px;"
                                                    class="img-thumbnail">
                                                <button type="button" id="remove-logo-btn" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" title="Remove Logo">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <input type="hidden" name="remove_logo" id="remove_logo_input" value="0">
                                            <div class="d-flex align-items-center">
                                                <input type="file" name="upload_image" id="upload_image_input" class="form-control me-2" style="{{ !empty($box->company_logo) ? 'display:none;' : '' }}">
                                                <button type="button" id="change-logo-btn" class="btn btn-secondary btn-sm" style="{{ empty($box->company_logo) ? 'display:none;' : '' }}">
                                                    Change Logo
                                                </button>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="border-bottom mb-3 pb-3">
                                        <h5 class="fw-semibold mb-1">Address</h5>
                                        <p>Please enter the company address details</p>
                                        <div class="mb-3">
                                            <label class="form-label">Address</label>
                                            <input type="text" name="address" class="form-control"
                                                value="{{ $box->address }}">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Country</label>
                                                <select id="country_select" name="country_id" class="form-select">
                                                    <option value="">Country</option>
                                                    @foreach ($countries as $id => $name)
                                                        <option value="{{ $id }}"
                                                            {{ $box->country_id == $id ? 'selected' : '' }}>
                                                            {{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">State</label>
                                                <select id="state_select" name="state_id" class="form-select">
                                                    <option value="">{{ $box->state->name ?? 'Select State' }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">City</label>
                                                <select id="city_select" name="city_id" class="form-select">
                                                    <option value="">{{ $box->city->name ?? 'Select City' }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Postal Code</label>
                                                <input type="text" name="pincode" class="form-control"
                                                    value="{{ $box->pincode }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <a href="#" class="btn btn-light me-2">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- /Company Settings -->
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
    <div id="dynamicToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="dynamicToastMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>


<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- jQuery -->
@push('scripts')
    <script>
        $(function() {
            const boxSlug = "{{ request()->route('box_slug') }}";
            const updateCompanySettingsUrl =
                "{{ route('website.settings.company.update', ['box_slug' => request()->route('box_slug')]) }}";

            function loadOptions(url, data, target, selected = null) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        let options = '<option value="">-- Select --</option>';
                        $.each(response, function(id, name) {
                            options +=
                                `<option value="${id}" ${selected == id ? 'selected' : ''}>${name}</option>`;
                        });
                        $(target).html(options);
                    },
                    error: function(xhr) {
                        console.error('Error fetching data:', xhr.responseText);
                    }
                });
            }

            $('#country_select').change(function() {
                const countryId = $(this).val();
                const url = "{{ route('company.states.select', ['box_slug' => request()->route('box_slug')]) }}";
                $('#state_select, #city_select').html('<option value="">Loading...</option>');
                loadOptions(url, {
                    country_id: countryId
                }, '#state_select');
                $('#city_select').html('<option value="">Select City</option>');
            });

            $('#state_select').change(function() {
                const stateId = $(this).val();
                const url = "{{ route('company.city.select', ['box_slug' => request()->route('box_slug')]) }}";
                $('#city_select').html('<option value="">Loading...</option>');
                loadOptions(url, {
                    state_id: stateId
                }, '#city_select');
            });

            $('#company_settings').submit(function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    url: updateCompanySettingsUrl,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.status == 200) {
                            if (res.data && res.data.logo_url) {
                                $('#company-logo-preview').attr('src', res.data.logo_url);
                                $('#logo-preview-container').show();
                                $('#change-logo-btn').show();
                                $('#upload_image_input').hide();
                                $('#remove_logo_input').val('0');
                            } else {
                                $('#logo-preview-container').hide();
                                $('#upload_image_input').show();
                                $('#change-logo-btn').hide();
                            }
                            showtoast(res.message || 'Company settings updated.');
                        } else {
                            showtoast(res.message || 'Error updating settings.', true);
                        }
                    },
                    error: function(xhr) {
                        showtoast(xhr.responseJSON?.message || 'Error saving settings.', true);
                    }
                });
            });

            function showtoast(message, isError = false) {
                $('#dynamicToastMessage').text(message);
                const toastElement = document.getElementById('dynamicToast');
                const toast = new bootstrap.Toast(toastElement);
                $('#dynamicToast').removeClass('bg-danger bg-success').addClass(isError ? 'bg-danger' :
                    'bg-success');
                toast.show();
            }

            $('#remove-logo-btn').click(function() {
                if (confirm('Are you sure you want to remove the company logo?')) {
                    $('#remove_logo_input').val('1');
                    $('#logo-preview-container').hide();
                    $('#upload_image_input').show();
                    $('#change-logo-btn').hide();
                }
            });

            $('#change-logo-btn').click(function() {
                $('#upload_image_input').click();
            });

            $('#upload_image_input').change(function() {
                if (this.files && this.files[0]) {
                    $('#remove_logo_input').val('0');
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#company-logo-preview').attr('src', e.target.result);
                        $('#logo-preview-container').show();
                        $('#change-logo-btn').show();
                        $('#upload_image_input').hide();
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });

            @if ($box->country_id)
                const preloadStateUrl = "{{ route('company.states.select', ['box_slug' => request()->route('box_slug')]) }}";
                loadOptions(preloadStateUrl, {
                    country_id: {{ $box->country_id }}
                }, '#state_select', {{ $box->state_id ?? 'null' }});
            @endif

            @if ($box->state_id)
                const preloadCityUrl = "{{ route('company.city.select', ['box_slug' => request()->route('box_slug')]) }}";
                loadOptions(preloadCityUrl, {
                    state_id: {{ $box->state_id }}
                }, '#city_select', {{ $box->city_id ?? 'null' }});
            @endif
        });
    </script>
@endpush
@endsection
