<div class="card">
    <div class="card-body">
        <div class="settings-sidebar">
            @if(request()->routeIs('website.settings.*') || request()->routeIs('website.settings'))
                <h4 class="fw-semibold mb-3">Website Settings</h4>
                <div class="list-group list-group-flush settings-sidebar">
                    <a href="{{ box_route('website.settings.company') }}"
                        class="fw-medium {{ request()->routeIs('website.settings.company') ? 'active' : '' }}">
                        Company Settings
                    </a>
                    <a href="{{ box_route('website.settings.branch') }}"
                        class="fw-medium {{ request()->routeIs('website.settings.branch') ? 'active' : '' }}">
                        Company Branches
                    </a>

                    <a href="{{ box_route('website.settings.companys_currency') }}"
                        class="fw-medium {{ request()->routeIs('website.settings.companys_currency') ? 'active' : '' }}">
                        Company Currency
                    </a>
                    <a href="{{ box_route('website.settings.attendance.module') }}"
                        class="fw-medium {{ request()->routeIs('website.settings.attendance.module') ? 'active' : '' }}">
                        Attendance Module
                    </a>

                    {{-- <a href="{{ box_route('website.settings.localization') }}"
                        class="fw-medium {{ request()->routeIs('website.settings.localization') ? 'active' : '' }}">
                        Localization
                    </a>
                    <a href="{{ box_route('website.settings.prefixes') }}"
                        class="fw-medium {{ request()->routeIs('website.settings.prefixes') ? 'active' : '' }}">
                        Prefixes
                    </a>
                    <a href="{{ box_route('website.settings.preference') }}"
                        class="fw-medium {{ request()->routeIs('website.settings.preference') ? 'active' : '' }}">
                        Preference
                    </a> --}}
                    {{-- <a href="{{ box_route('website.settings.appearance') }}"
                        class="fw-medium {{ request()->routeIs('website.settings.appearance') ? 'active' : '' }}">
                        Appearance
                    </a>
                    <a href="{{ box_route('website.settings.language') }}"
                        class="fw-medium {{ request()->routeIs('website.settings.language') ? 'active' : '' }}">
                        Language
                    </a> --}}
                    {{-- <a href="{{ box_route('settings.general.custom_fields') }}"
                        class="fw-medium {{ request()->is('*/custom-fields*') ? 'active' : '' }}">
                        Custom Fields
                    </a> --}}
                </div>
            @elseif(request()->routeIs('app.settings.*') || request()->routeIs('settings.general.custom_fields'))
                <h4 class="fw-semibold mb-3">App Settings</h4>
                <div class="list-group list-group-flush settings-sidebar">
                    {{-- <a href="{{ box_route('app.settings.invoice') }}"
                        class="fw-medium {{ request()->routeIs('app.settings.invoice') ? 'active' : '' }}">
                        Invoice Settings
                    </a>
                    <a href="{{ box_route('app.settings.printers') }}"
                        class="fw-medium {{ request()->routeIs('app.settings.printers') ? 'active' : '' }}">
                        Printer
                    </a> --}}
                    {{-- <a href="{{ box_route('settings.general.custom_fields') }}"
                        class="fw-medium {{ request()->is('*/custom-fields*') ? 'active' : '' }}">
                        Custom Fields
                    </a> --}}
                    <a href="{{ box_route('app.settings.manage.form') }}"
                        class="fw-medium {{ request()->routeIs('app.settings.manage.form') ? 'active' : '' }}">
                        Manage Form
                    </a>
                    <a href="{{ box_route('app.settings.source') }}"
                        class="fw-medium {{ request()->routeIs('app.settings.source') ? 'active' : '' }}">
                        Lead Sources
                    </a>
                    <a href="{{ box_route('app.settings.lost.reason') }}"
                        class="fw-medium {{ request()->routeIs('app.settings.lost.reason') ? 'active' : '' }}">
                        Lost Reason
                    </a>
                    <a href="{{ box_route('app.settings.contact.stage') }}"
                        class="fw-medium {{ request()->routeIs('app.settings.contact.stage') ? 'active' : '' }}">
                        Lead Status
                    </a>
                    <a href="{{ box_route('app.settings.leads.leads_sub_status') }}"
                        class="fw-medium {{ request()->routeIs('app.settings.leads.leads_sub_status') ? 'active' : '' }}">
                        Leads Sub Status
                    </a>
                    <a href="{{ box_route('app.settings.industry') }}"
                        class="fw-medium {{ request()->routeIs('app.settings.industry') ? 'active' : '' }}">
                        Industry
                    </a>
                    <a href="{{ box_route('app.settings.calls') }}"
                        class="fw-medium {{ request()->routeIs('app.settings.calls') ? 'active' : '' }}">
                        Activity Setting
                    </a>
                </div>
            @elseif(request()->routeIs('system.settings.*'))
                <h4 class="fw-semibold mb-3">System Settings</h4>
                <div class="list-group list-group-flush settings-sidebar">
                    {{-- <a href="{{ box_route('system.settings.email') }}"
                        class="fw-medium {{ request()->routeIs('system.settings.email') ? 'active' : '' }}">
                        Email Settings
                    </a>
                    <a href="{{ box_route('system.settings.sms_gateways') }}"
                        class="fw-medium {{ request()->routeIs('system.settings.sms_gateways') ? 'active' : '' }}">
                        SMS Gateways
                    </a>
                    <a href="{{ box_route('system.settings.gdpr') }}"
                        class="fw-medium {{ request()->routeIs('system.settings.gdpr') ? 'active' : '' }}">
                        GDPR Cookies
                    </a> --}}
                </div>
            @elseif(request()->routeIs('financial.settings.*'))
                <h4 class="fw-semibold mb-3">Financial Settings</h4>
                <div class="list-group list-group-flush settings-sidebar">
                    {{-- <a href="{{ box_route('financial.settings.payment_gateways') }}"
                        class="fw-medium {{ request()->routeIs('financial.settings.payment_gateways') ? 'active' : '' }}">
                        Payment Gateways
                    </a> --}}
                    <a href="{{ box_route('financial.settings.bank_accounts') }}"
                        class="fw-medium {{ request()->routeIs('financial.settings.bank_accounts') ? 'active' : '' }}">
                        Bank Accounts
                    </a>
                    <a href="{{ box_route('financial.settings.payment_modes') }}"
                        class="fw-medium {{ request()->routeIs('financial.settings.payment_modes') ? 'active' : '' }}">
                        Payment Modes
                    </a>
                    <a href="{{ box_route('financial.settings.divisions') }}"
                        class="fw-medium {{ request()->routeIs('financial.settings.divisions') ? 'active' : '' }}">
                        Divisions
                    </a>

                    {{-- <a href="{{ box_route('financial.settings.tax_rates') }}"
                        class="fw-medium {{ request()->routeIs('financial.settings.tax_rates') ? 'active' : '' }}">
                        Tax Rates
                    </a>
                    <a href="{{ box_route('financial.settings.currencies') }}"
                        class="fw-medium {{ request()->routeIs('financial.settings.currencies') ? 'active' : '' }}">
                        Currencies
                    </a> --}}
                </div>
            @elseif(request()->routeIs('other.settings.*'))
                <h4 class="fw-semibold mb-3">Other Settings</h4>
                <div class="list-group list-group-flush settings-sidebar">
                    <a href="{{ box_route('other.settings.storage') }}"
                        class="fw-medium {{ request()->routeIs('other.settings.storage') ? 'active' : '' }}">
                        Storage
                    </a>
                    {{-- <a href="{{ box_route('other.settings.ban_ip') }}"
                        class="fw-medium {{ request()->routeIs('other.settings.ban_ip') ? 'active' : '' }}">
                        Ban IP Address
                    </a> --}}
                    <a href="{{ box_route('other.settings.box_settings') }}"
                        class="fw-medium {{ request()->routeIs('other.settings.box_settings') ? 'active' : '' }}">
                        Box Settings
                    </a>
                </div>
            @elseif(request()->routeIs('settings.general.*'))
                <h4 class="fw-semibold mb-3">General Settings</h4>
                <div class="list-group list-group-flush settings-sidebar">
                    <a href="{{ box_route('settings.general.companies') }}"
                        class="fw-medium {{ request()->routeIs('settings.general.companies') ? 'active' : '' }}">
                        Companies
                    </a>
                    <a href="{{ box_route('settings.general.companies_grid') }}"
                        class="fw-medium {{ request()->routeIs('settings.general.companies_grid') ? 'active' : '' }}">
                        Companies Grid
                    </a>
                    {{-- <a href="{{ box_route('settings.general.companys_details') }}"
                        class="fw-medium {{ request()->routeIs('settings.general.companys_details') ? 'active' : '' }}">
                        Companies Details
                    </a> --}}
                    {{-- <a href="{{ box_route('settings.general.hsns') }}"
                        class="fw-medium {{ request()->routeIs('settings.general.hsns') ? 'active' : '' }}">
                        HSNs
                    </a>
                    <a href="#"
                        class="fw-medium">
                        Items
                    </a>
                    <a href="{{ box_route('settings.general.packages') }}"
                        class="fw-medium {{ request()->routeIs('settings.general.packages') ? 'active' : '' }}">
                        Packages
                    </a>
                    <a href="{{ box_route('settings.general.companys') }}"
                        class="fw-medium {{ request()->routeIs('settings.general.companys') ? 'active' : '' }}">
                        Setting
                    </a> --}}
                    {{-- <a href="{{ box_route('settings.general.activities') }}"
                        class="fw-medium {{ request()->routeIs('settings.general.activities') ? 'active' : '' }}">
                        Activity Type
                    </a>
                    <a href="{{ box_route('settings.general.sources') }}"
                        class="fw-medium {{ request()->routeIs('settings.general.sources') ? 'active' : '' }}">
                        Lead Source
                    </a>
                    <a href="{{ box_route('settings.general.lead_status') }}"
                        class="fw-medium {{ request()->routeIs('settings.general.lead_status') ? 'active' : '' }}">
                        Lead Status
                    </a> --}}
                    <a href="{{ box_route('settings.general.custom_fields') }}"
                        class="fw-medium {{ request()->routeIs('settings.general.custom_fields') ? 'active' : '' }}">
                        Custom Fields
                    </a>
                    <a href="{{ box_route('settings.general.email_inboxes') }}"
                        class="fw-medium {{ request()->routeIs('settings.general.email_inboxes') ? 'active' : '' }}">
                        Email Inboxes
                    </a>
                    <a href="{{ box_route('settings.general.email_messages') }}"
                        class="fw-medium {{ request()->routeIs('settings.general.email_messages') ? 'active' : '' }}">
                        Email Messages
                    </a>
                    <a href="{{ box_route('settings.general.api_keys') }}"
                        class="fw-medium {{ request()->routeIs('settings.general.api_keys') ? 'active' : '' }}">
                        Lead APIs
                    </a>
                    {{-- <a href="{{ box_route('manage_users.roles_permissions') }}"
                        class="fw-medium {{ request()->routeIs('manage_users.roles_permissions') ? 'active' : '' }}">
                        User Roles
                    </a> --}}
                </div>
            @endif
        </div>
    </div>
</div>
