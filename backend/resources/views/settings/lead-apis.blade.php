@extends('layouts.main')

@section('content')
@section('title', 'Lead APIs')

<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">Lead APIs</h4>
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
                    <div class="row">
                        <div class="col-12">
                             <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ box_route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Lead APIs</li>
                            </ul>
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
                        <!-- Lead APIs -->
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h4 class="fw-semibold mb-0">Lead APIs</h4>
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0);" onclick="launchFacebookLogin()" class="btn btn-primary btn-sm me-2" id="configure-facebook-btn">
                                        {{ isset($facebookConnected) && $facebookConnected ? 'Reconnect Facebook' : 'Configure Facebook' }}
                                    </a>
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modal_add_api">
                                        <i class="ti ti-plus me-1"></i> Add New
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Dynamic Active/Configured APIs Section --}}
                                    @foreach($integrations as $integration)
                                    <div class="col-md-6 mb-4">
                                        <div class="card border h-100">
                                            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0">{{ $integration['name'] }}</h5>
                                                @if($integration['status'] == 'Enabled')
                                                    <span class="badge bg-success">Connected</span>
                                                @else
                                                   <div><button class="btn btn-success btn-sm">Enable</button></div>
                                                @endif
                                            </div>
                                            <div class="card-body">
                                                    @foreach($integration['params'] as $key => $value)
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">{{ ucfirst($key) }}</label>
                                                        <input type="text" class="form-control mb-2" value="{{ $value }}" readonly disabled>
                                                    </div>
                                                    @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach


                                </div>
                            </div>
                        </div>
                        <!-- /Lead APIs -->
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Offcanvas for Adding New API -->
<!-- Modal for Adding New API -->
<div class="modal fade" id="modal_add_api" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-bottom theme-bg text-white">
                <h5 class="modal-title text-white">API List</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
        <div class="row gy-4">
            
            {{-- APIs Grid --}}
             @php
                $api_list = [
                    ['name' => '99 Acers', 'icon' => 'assets/img/iconsapi/99acres-icon.png', 'type' => 'img'],
                    ['name' => 'Facebook', 'icon' => 'fa-brands fa-facebook-f', 'type' => 'icon', 'color' => 'text-primary'],
                    ['name' => 'India Mart', 'icon' => 'assets/img/iconsapi/indiamart-icon.png', 'type' => 'img'],
                    ['name' => 'Pharma Hopers', 'icon' => 'assets/img/iconsapi/p-hooper-icon.png', 'type' => 'img'],
                    ['name' => 'Pharma Khabar', 'icon' => 'assets/img/iconsapi/pk-icon.png', 'type' => 'img'],
                    ['name' => 'Pharma Vends', 'icon' => 'assets/img/iconsapi/p-vends-icon.png', 'type' => 'img'],
                    ['name' => 'Trade India', 'icon' => 'assets/img/iconsapi/trade-india-icon.png', 'type' => 'img'],
                    ['name' => 'Trade XL', 'icon' => 'assets/img/iconsapi/t-xl-icon.png', 'type' => 'img'],
                    ['name' => 'Web Hook', 'icon' => 'fa-solid fa-circle-nodes', 'type' => 'icon', 'color' => 'text-danger'],
                    ['name' => 'ZipZapGo Default', 'icon' => 'assets/img/iconsapi/bizz-crm-icon.png', 'type' => 'img'],
                    ['name' => 'Bizz Digital', 'icon' => 'assets/img/iconsapi/bizz-icon.png', 'type' => 'img'],
                    ['name' => 'Adflair Media', 'icon' => 'assets/img/iconsapi/af-icon.png', 'type' => 'img'],
                    ['name' => 'Dawa Charcha', 'icon' => 'assets/img/iconsapi/dc-icon.png', 'type' => 'img'],
                    ['name' => 'Franchis Express', 'icon' => 'assets/img/iconsapi/franch-express-icon.png', 'type' => 'img'],
                    ['name' => 'Just Dial', 'icon' => 'assets/img/iconsapi/jd-icon.png', 'type' => 'img'],
                    ['name' => 'Next Tech Mart', 'icon' => 'assets/img/iconsapi/ntm-icon.png', 'type' => 'img'],
                    ['name' => 'Pharma Biz Connect', 'icon' => 'assets/img/iconsapi/pbc-icon.png', 'type' => 'img'],
                    ['name' => 'Pharma Buffet', 'icon' => 'assets/img/iconsapi/pharma-buffet-icon.png', 'type' => 'img'],
                    ['name' => 'Pharma Business', 'icon' => 'fa-solid fa-building', 'type' => 'icon', 'color' => 'text-info'],
                ];
            @endphp

            @foreach($api_list as $api)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border">
                    <div class="card-header bg-transparent border-bottom p-3">
                        <h6 class="card-title mb-0 text-dark">{{ $api['name'] }}</h6>
                    </div>
                    <div class="card-body text-center p-4">
                        <div class="mb-4 d-flex align-items-center justify-content-center" style="height: 80px;">
                            @if($api['type'] == 'img')
                                <img src="{{ asset($api['icon']) }}" alt="{{ $api['name'] }}" class="img-fluid" style="max-height: 60px;">
                            @else
                                <i class="{{ $api['icon'] }} fa-4x {{ $api['color'] ?? '' }}"></i>
                            @endif
                        </div>
                        <button class="btn btn-success px-4" style="min-width: 100px;">Enable</button>
                    </div>
                </div>
            </div>
            @endforeach
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@push('scripts')
<script>
    // Facebook Integration Scripts (Should be moved to a separate JS file usually)
    function launchFacebookLogin() {
        const boxSlug = '{{ $box->slug ?? "" }}' || "{{ Request::segment(1) }}";
        const currentUrl = window.location.href;
        
        // Use local callback route as redirect URI
        const redirectUriLocal = '{{ route("facebook.callback.auth") }}';
        
        const connectUrl = '{{ route("facebook.connect") }}' + 
            '?box_slug=' + encodeURIComponent(boxSlug) +
            '&current_url=' + encodeURIComponent(currentUrl) +
            '&redirect_uri=' + encodeURIComponent(redirectUriLocal);

        console.log('Redirecting to Facebook OAuth:', connectUrl);
        window.location.href = connectUrl;
    }

</script>
@endpush
