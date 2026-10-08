<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title"><span>Vendor Panel</span></li>
                <li class="{{ Request::routeIs('vendor.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('vendor.dashboard') }}">
                        <i class="ti ti-layout-dashboard"></i><span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ Request::routeIs('vendor.transaction') ? 'active' : '' }}">
                    <a href="{{ route('vendor.transaction') }}">
                        <i class="ti ti-receipt"></i><span>My Transactions</span>
                    </a>
                </li>
                <li class="{{ Request::routeIs('vendor.commission') ? 'active' : '' }}">
                    <a href="{{ route('vendor.commission') }}">
                        <i class="ti ti-percentage"></i><span>My Commission</span>
                    </a>
                </li>
                <li class="{{ Request::routeIs('vendor.referrals') ? 'active' : '' }}">
                    <a href="{{ route('vendor.referrals') }}">
                        <i class="ti ti-users"></i><span>My Referrals</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
