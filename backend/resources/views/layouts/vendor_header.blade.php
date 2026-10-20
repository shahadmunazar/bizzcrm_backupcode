<div class="header">
    <!-- Logo -->
    <div class="header-left active">
        <a href="{{ route('vendor.dashboard') }}" class="logo logo-normal">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo">
            <img src="{{ asset('assets/img/logo-white.png') }}" class="white-logo" alt="Logo">
        </a>
        <a href="{{ route('vendor.dashboard') }}" class="logo-small">
            <img src="{{ asset('assets/img/logo-small.png') }}" alt="Logo">
        </a>
        <a id="toggle_btn" href="javascript:void(0);">
            <i class="ti ti-arrow-bar-to-left"></i>
        </a>
    </div>
    <!-- /Logo -->

    <a id="mobile_btn" class="mobile_btn" href="#sidebar">
        <span class="bar-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </a>

    <div class="header-user">
        <ul class="nav user-menu align-items-center">
            @if(session()->has('impersonated_by'))
                <li class="nav-item me-2 d-none d-sm-block">
                    <div class="d-flex align-items-center bg-danger-subtle border border-danger-subtle text-danger px-3 py-1 rounded-pill small fw-semibold shadow-sm">
                        <i class="ti ti-shield-lock me-1 fs-5"></i>
                        <span>Impersonating Vendor</span>
                        <a href="{{ route('vendor.leave_impersonation') }}" class="btn btn-xs btn-danger text-white rounded-pill ms-2 px-2 py-0 fw-bold">
                            <i class="ti ti-arrow-back-up me-1"></i> Exit
                        </a>
                    </div>
                </li>
            @endif
            <li class="nav-item dropdown has-arrow main-drop">
                <a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
                    <span class="user-info">
                        <span class="user-letter">
                            <img src="{{ asset('assets/img/profiles/avatar-20.jpg') }}" alt="Profile">
                        </span>
                        <span class="user-detail">
                            <span class="user-name">{{ Auth::user()->name ?? 'Vendor' }}</span>
                            <span class="user-role">Vendor</span>
                        </span>
                    </span>
                </a>
                <div class="dropdown-menu menu-drop-user">
                    <div class="profilename">
                        <div class="profileset">
                            <span class="user-img">
                                <img src="{{ asset('assets/img/profiles/avatar-20.jpg') }}" alt="Profile">
                                <span class="status online"></span>
                            </span>
                            <div class="profilesets">
                                <h6>{{ Auth::user()->name ?? 'Vendor' }}</h6>
                                <h5>Vendor</h5>
                            </div>
                        </div>
                        <hr class="m-0">
                        @if(session()->has('impersonated_by'))
                            <a class="dropdown-item text-danger fw-bold bg-danger-subtle" href="{{ route('vendor.leave_impersonation') }}">
                                <i class="ti ti-arrow-back-up me-2"></i> Exit Impersonation
                            </a>
                            <hr class="m-0">
                        @endif
                        <a class="dropdown-item" href="{{ route('vendor.dashboard') }}">
                            <i class="ti ti-layout-dashboard me-2"></i> Dashboard
                        </a>
                        <hr class="m-0">
                        <!-- Logout Form -->
                        <form id="vendor-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('vendor-logout-form').submit();">
                            <i class="ti ti-login me-2"></i>Logout
                        </a>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>