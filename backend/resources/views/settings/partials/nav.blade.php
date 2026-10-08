<div class="card">
    <div class="card-body pb-0 pt-2">
        <ul class="nav nav-tabs nav-tabs-bottom">
            <li class="nav-item me-3">
                <a href="{{ box_route('settings.general.companies') }}" 
                   class="nav-link px-0 {{ request()->routeIs('settings.general.*') ? 'active' : '' }}">
                    <i class="ti ti-settings-cog me-2"></i>General Settings
                </a>
            </li>
            <li class="nav-item me-3">
                <a href="{{ box_route('website.settings.company') }}"
                   class="nav-link px-0 {{ request()->routeIs('website.settings.*') ? 'active' : '' }}">
                    <i class="ti ti-world-cog me-2"></i>Website Settings
                </a>
            </li>
            <li class="nav-item me-3">
                <a href="{{ box_route('app.settings.invoice') }}" 
                   class="nav-link px-0 {{ request()->routeIs('app.settings.*') ? 'active' : '' }}">
                    <i class="ti ti-apps me-2"></i>App Settings
                </a>
            </li>
            <li class="nav-item me-3">
                <a href="{{ box_route('system.settings.email') }}" 
                   class="nav-link px-0 {{ request()->routeIs('system.settings.*') ? 'active' : '' }}">
                    <i class="ti ti-device-laptop me-2"></i>System Settings
                </a>
            </li>
            <li class="nav-item me-3">
                <a href="{{ box_route('financial.settings.payment_gateways') }}" 
                   class="nav-link px-0 {{ request()->routeIs('financial.settings.*') ? 'active' : '' }}">
                    <i class="ti ti-moneybag me-2"></i>Financial Settings
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ box_route('other.settings.storage') }}" 
                   class="nav-link px-0 {{ request()->routeIs('other.settings.*') ? 'active' : '' }}">
                    <i class="ti ti-flag-cog me-2"></i>Other Settings
                </a>
            </li>
        </ul>
    </div>
</div>
