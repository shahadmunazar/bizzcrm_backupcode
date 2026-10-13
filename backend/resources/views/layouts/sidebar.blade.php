<div class="sidebar" id="sidebar">
    <div class="modern-profile p-3 pb-0">

        <div class="sidebar-nav mb-3">
            <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded nav-justified bg-transparent" role="tablist">
                <li class="nav-item"><a class="nav-link active border-0" href="#">Menu</a></li>
                <li class="nav-item"><a class="nav-link border-0" href="chat.html">Chats</a></li>
                <li class="nav-item"><a class="nav-link border-0" href="email.html">Inbox</a></li>
            </ul>
        </div>
    </div>
    <div class="sidebar-header p-3 pb-0 pt-2">

        <div class="d-flex align-items-center justify-content-between menu-item mb-3">
            <div class="me-3">
                <a href="calendar.html" class="btn btn-icon border btn-menubar">
                    <i class="ti ti-layout-grid-remove"></i>
                </a>
            </div>
            <div class="me-3">
                <a href="chat.html" class="btn btn-icon border btn-menubar position-relative">
                    <i class="ti ti-brand-hipchat"></i>
                </a>
            </div>
            <div class="me-3 notification-item">
                <a href="activities.html" class="btn btn-icon border btn-menubar position-relative me-1">
                    <i class="ti ti-bell"></i>
                    <span class="notification-status-dot"></span>
                </a>
            </div>
            <div class="me-0">
                <a href="email.html" class="btn btn-icon border btn-menubar">t
                    <i class="ti ti-message"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                @php
                    use App\Models\Box;
                    use App\Models\BoxUser;
                    use Illuminate\Support\Facades\Auth;
                    $user = Auth::user();
                    $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
                    $boxSlug = '';
                    if ($boxUserEntry) {
                        $box = Box::find($boxUserEntry->box_id);
                        $boxSlug = $box?->slug ?? '';
                        $currentBoxSlug = $boxSlug;
                    }

                    $dashboardRoute = '';

                    if ($user->hasRole('superadmin')) {
                        $dashboardRoute = box_route('superadmin.dashboard');
                    } elseif ($user->hasRole('admin') || $user->hasRole('manager')) {
                        $dashboardRoute = box_route('admin.dashboard');
                    } elseif ($user->hasRole('employee')) {
                        $dashboardRoute = box_route('employee.dashboard');
                    } elseif ($user->hasRole('deals')) {
                        $dashboardRoute = box_route('deals.dashboard');
                    } elseif ($user->hasRole('project') || $user->hasRole('project-manager')) {
                        $dashboardRoute = box_route('project.dashboard');
                    } elseif ($user->hasRole('tracking')) {
                        $dashboardRoute = box_route('tracking.dashboard');
                    } elseif ($user->hasRole('leads')) {
                        $dashboardRoute = box_route('leads.dashboard');
                    } else {
                        $dashboardRoute = box_route('home');
                    }

                    use App\Helpers\PermissionHelper;
                    $isPrivilegedUser = PermissionHelper::isPrivilegedUser();
                @endphp



                <li class="clinicdropdown">
                    <a href="{{ $dashboardRoute }}" class="d-flex align-items-center gap-2">
                        @php
                            $user = Auth::user();
                            $boxUser = $user->box_user_new;
                            $profileImage = $boxUser?->profile_image_path;
                            $userName = $user->name ?? 'U';
                            $initial = strtoupper(substr($userName, 0, 1));
                        @endphp

                        {{-- Avatar --}}
                        @if ($profileImage)
                            <img src="{{ asset('storage/' . $profileImage) }}" class="img-fluid rounded-circle"
                                alt="Profile" style="width: 40px; height: 40px;">
                        @else
                            <div class="letter-avatar rounded-circle text-white text-uppercase d-flex justify-content-center align-items-center"
                                title="{{ $userName }}"
                                style="width: 40px; height: 40px; background-color: {{ getColorFromNameBlade($userName) }}; font-size: 18px; font-weight: bold;">
                                {{ $initial }}
                            </div>
                        @endif

                        {{-- Name + Role --}}
                        <div class="user-names">
                            <h5 class="mb-0" style="font-size: 16px;">{{ $userName }}</h5>
                            <h6 class="mb-0" style="font-size: 13px; color: #888;">
                                {{ ucfirst($user->getRoleNames()->first() ?? 'User') }}
                            </h6>
                        </div>
                    </a>
                </li>

                @php
                    function getColorFromNameBlade($name)
                    {
                        $colors = [
                            '#f44336',
                            '#e91e63',
                            '#9c27b0',
                            '#673ab7',
                            '#3f51b5',
                            '#2196f3',
                            '#03a9f4',
                            '#00bcd4',
                            '#009688',
                            '#4caf50',
                            '#8bc34a',
                            '#cddc39',
                            '#ffc107',
                            '#ff9800',
                            '#ff5722',
                        ];
                        $char = strtoupper(trim($name)[0] ?? 'A');
                        $index = ord($char) % count($colors);
                        return $colors[$index];
                    }
                @endphp



            </ul>
            <ul>
                <li>
                    <h6 class="submenu-hdr">Main Menu</h6>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-layout-2"></i><span>Dashboard</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if($user->hasRole(['admin', 'superadmin', 'manager']) || $user->id == 1)
                                <li><a href="{{ box_route('admin.dashboard') }}"
                                        class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Admin
                                        Dashboard</a></li>
                                @endif
                                {{-- <li><a href="{{ box_route('tracking.dashboard') }}"
                                        class="{{ request()->routeIs('tracking.dashboard') ? 'active' : '' }}">Tracking
                                        Dashboard</a></li> --}}
                                {{-- <li><a href="{{ box_route('deals.dashboard') }}"
                                        class="{{ request()->routeIs('deals.dashboard') ? 'active' : '' }}">Deals
                                        Dashboard</a></li> --}}
                                <li><a href="{{ box_route('leads.dashboard') }}"
                                        class="{{ request()->routeIs('leads.dashboard') ? 'active' : '' }}">Leads
                                        Dashboard</a></li>
                                {{-- <li><a href="{{ box_route('project.dashboard') }}"
                                        class="{{ request()->routeIs('project.dashboard') ? 'active' : '' }}">Project
                                        Dashboard</a></li> --}}
                                <li><a href="{{ box_route('call.dashboard') }}"
                                        class="{{ request()->routeIs('call.dashboard') ? 'active' : '' }}">Call
                                        Dashboard</a></li>
                                {{-- <li><a href="{{ box_route('call_logs.index') }}"
                                        class="{{ request()->routeIs('call_logs.index') ? 'active' : '' }}">Call
                                        Logs</a></li> --}}
                                {{-- @if ($isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute('whatsapp.dashboard'))
                                <li><a href="{{ box_route('whatsapp.dashboard') }}"
                                        class="{{ request()->routeIs('whatsapp.dashboard') ? 'active' : '' }}">WhatsApp
                                        Dashboard</a></li>
                                @endif --}}
                                @if ($isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute('facebook.dashboard'))
                                <li><a href="{{ box_route('facebook.dashboard') }}"
                                        class="{{ request()->routeIs('facebook.dashboard') ? 'active' : '' }}">Facebook
                                        Dashboard</a></li>
                                @endif
                                {{-- <li><a href="{{ box_route('whatsapp.index') }}"
                                        class="{{ request()->routeIs('whatsapp.index') ? 'active' : '' }}">WhatsApp
                                        Chat</a></li> --}}
                            </ul>
                        </li>

                        @if ($user->hasRole('superadmin') || $user->id == '1')
                            <li class="submenu">
                                <a href="javascript:void(0)"
                                    class="{{ request()->routeIs('superadmin.*') ? 'subdrop active' : '' }}">
                                    <i class="ti ti-user-star"></i><span>Super Admin</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ box_route('superadmin.dashboard') }}"
                                            class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">Dashboard</a>
                                    </li>
                                    <li><a href="{{ box_route('superadmin.company') }}"
                                            class="{{ request()->routeIs('superadmin.company') ? 'active' : '' }}">Companies</a>
                                    </li>
                                    <li><a href="{{ box_route('superadmin.subscription') }}"
                                            class="{{ request()->routeIs('superadmin.subscription') ? 'active' : '' }}">Subscriptions</a>
                                    </li>
                                    <li><a href="{{ box_route('superadmin.packages') }}"
                                            class="{{ request()->routeIs('superadmin.packages') ? 'active' : '' }}">Packages</a>
                                    </li>
                                    <li><a href="{{ box_route('superadmin.coupons') }}"
                                            class="{{ request()->routeIs('superadmin.coupons') ? 'active' : '' }}">Coupons</a>
                                    </li>

                                    <li><a href="{{ box_route('superadmin.package-features.page') }}"
                                            class="{{ request()->routeIs('superadmin.package-features.page') ? 'active' : '' }}">Package
                                            Features</a>
                                    </li>
                                    <li><a href="{{ box_route('superadmin.faqs') }}"
                                            class="{{ request()->routeIs('superadmin.faqs') ? 'active' : '' }}">FAQs</a>
                                    </li>
                                    <li><a href="{{ box_route('superadmin.master_industry') }}"
                                            class="{{ request()->routeIs('superadmin.master_industry') ? 'active' : '' }}">Industry Register</a>
                                    </li>
                                    <li><a href="{{ box_route('employee.leads_type') }}"
                                            class="{{ request()->routeIs('employee.leads_type') ? 'active' : '' }}">Domain</a>
                                    </li>
                                    <li><a href="{{ box_route('superadmin.purchase_transaction') }}"
                                            class="{{ request()->routeIs('superadmin.purchase_transaction') ? 'active' : '' }}">Purchase
                                            Transaction</a></li>
                                    <li><a href="{{ box_route('superadmin.realtime_logs') }}"
                                            class="{{ request()->routeIs('superadmin.realtime_logs') ? 'active' : '' }}">Realtime Logs</a></li>
                                    <li><a href="{{ box_route('manage_users.organizational_structure') }}"
                                            class="{{ request()->routeIs('manage_users.organizational_structure') ? 'active' : '' }}">Organizational
                                            Structure</a></li>
                                    <li><a href="{{ box_route('superadmin.account-deletion-requests.index') }}"
                                            class="{{ request()->routeIs('superadmin.account-deletion-requests.index') ? 'active' : '' }}">Delete Requests</a></li>
                                </ul>
                            </li>

                            <li class="submenu">
                                <a href="javascript:void(0)" class="{{ request()->routeIs('superadmin.vendors.*') ? 'subdrop active' : '' }}">
                                    <i class="ti ti-building-store"></i><span>Vendors</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ box_route('superadmin.vendors.index') }}"
                                            class="{{ request()->routeIs('superadmin.vendors.index') ? 'active' : '' }}">Vendors</a></li>
                                    <li><a href="{{ box_route('superadmin.vendors.percentages') }}"
                                            class="{{ request()->routeIs('superadmin.vendors.percentages') ? 'active' : '' }}">Vendor Percentages</a></li>
                                    <li><a href="{{ box_route('superadmin.vendors.dashboard') }}"
                                            class="{{ request()->routeIs('superadmin.vendors.dashboard') ? 'active' : '' }}">Vendor Dashboard</a></li>
                                    <li><a href="{{ box_route('superadmin.vendors.transaction') }}"
                                            class="{{ request()->routeIs('superadmin.vendors.transaction') ? 'active' : '' }}">Vendor Transactions</a></li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </li>
                @php
                    use App\Helpers\SubscriptionHelper;
                    $user = Auth::user();
                    // Feature flags from Subscription
                    $hasAttendanceFeature = SubscriptionHelper::hasFeature('Attendance');
                    $hasHrToolsFeature = SubscriptionHelper::hasFeature('HR Documents');
                    $hasPayrollFeature = SubscriptionHelper::hasFeature('Salary');
                    $hasLeadsFeature = SubscriptionHelper::hasFeature('Lead Integration');
                    $hasActivitiesFeature = SubscriptionHelper::hasFeature('Reminders');
                    $hasCustomFieldsFeature = SubscriptionHelper::hasFeature('Custom Fields');
                    $hasWhatsAppFeature = SubscriptionHelper::hasFeature('WhatsApp API Integration');
                    $hasSmsFeature = SubscriptionHelper::hasFeature('SMS Integration');
                    $hasLeaveManagementFeature = SubscriptionHelper::hasFeature('Leave Management');
                    $hasHolidaysFeature = SubscriptionHelper::hasFeature('Holidays');
                    $hasCallRecordsFeature = SubscriptionHelper::hasFeature('Call Records');
                    // Additional features can be added here

                    if ($isPrivilegedUser) {
                        $hasAttendanceRead = $hasAttendanceFeature;
                        $hasHrToolsRead = $hasHrToolsFeature;
                        $hasPayrollRead = $hasPayrollFeature;
                        $hasLeadsRead = $hasLeadsFeature;
                        $hasIntegrationsRead = true;
                        $hasGeneralSettingsRead = true;
                        $hasWebsiteSettingsRead = true;
                        $hasAppSettingsRead = true;
                        $hasSystemSettingsRead = true;
                        $hasFinancialSettingsRead = true;
                        $hasOtherSettingsRead = true;
                        $hasUserManagementRead = true;
                        $hasAccountsRead = true;
                        $hasActivitiesRecordsRead = $hasActivitiesFeature;
                        $hasReportsRead = true;
                        
                        $hasFinanceDashboardRead = true;
                        $hasFinanceSettingsRead = true;
                        $hasFinanceExpensesRead = true;
                        $hasFinanceApprovalsRead = true;
                        $hasFinanceReimbursementsRead = true;
                        $hasFinanceRead = true;
                    } else {
                        // For non-admin users, check actual permissions from database AND subscription feature
                        $hasAttendanceRead = PermissionHelper::hasModulePermission('attendance', 'read') && $hasAttendanceFeature;
                        $hasHrToolsRead = PermissionHelper::hasModulePermission('hr_tools', 'read') && $hasHrToolsFeature;
                        $hasPayrollRead = PermissionHelper::hasModulePermission('payroll', 'read') && $hasPayrollFeature;
                        $hasLeadsRead = PermissionHelper::hasModulePermission('leads', 'read') && $hasLeadsFeature;
                        $hasIntegrationsRead = PermissionHelper::hasModulePermission('integrations', 'read');
                        $hasGeneralSettingsRead = PermissionHelper::hasModulePermission('general_settings', 'read');
                        $hasWebsiteSettingsRead = PermissionHelper::hasModulePermission('website_settings', 'read');
                        $hasAppSettingsRead = PermissionHelper::hasModulePermission('app_settings', 'read');
                        $hasSystemSettingsRead = PermissionHelper::hasModulePermission('system_settings', 'read');
                        $hasFinancialSettingsRead = PermissionHelper::hasModulePermission('financial_settings', 'read');
                        $hasOtherSettingsRead = PermissionHelper::hasModulePermission('other_settings', 'read');
                        $hasUserManagementRead = PermissionHelper::hasModulePermission('user_management', 'read');
                        $hasAccountsRead = PermissionHelper::hasModulePermission('accounts', 'read');
                        $hasActivitiesRecordsRead = PermissionHelper::hasModulePermission('activities_records', 'read') && $hasActivitiesFeature;
                        $hasReportsRead = PermissionHelper::hasModulePermission('reports', 'read');
                        
                        $hasFinanceDashboardRead = PermissionHelper::hasModulePermission('finance_dashboard', 'read');
                        
                        $hasAllowanceTypes = PermissionHelper::hasModulePermission('finance_settings_allowance_types', 'read');
                        $hasExpenseCategories = PermissionHelper::hasModulePermission('finance_settings_expense_categories', 'read');
                        $hasApprovalRules = PermissionHelper::hasModulePermission('finance_settings_approval_rules', 'read');
                        $hasFinanceSettingsRead = $hasAllowanceTypes || $hasExpenseCategories || $hasApprovalRules;

                        $hasFinanceExpensesRead = PermissionHelper::hasModulePermission('finance_expenses', 'read');
                        $hasFinanceApprovalsRead = PermissionHelper::hasModulePermission('finance_approvals', 'read');
                        $hasFinanceReimbursementsRead = PermissionHelper::hasModulePermission('finance_reimbursements', 'read');
                        $hasFinanceRead = $hasFinanceDashboardRead || $hasFinanceSettingsRead || $hasFinanceExpensesRead || $hasFinanceApprovalsRead || $hasFinanceReimbursementsRead;
                    }
                @endphp
                @php
                    $attendanceRoutes = [
                        'employee.attendace.admin',
                        'employee.biometric.machine',
                        'employee.attendace',
                        'employee.dashboard',
                        'employee.attendance.calendar',
                        'employee.leaves',
                        'employee.leave.admin',
                        'employee.leave_settings',
                        'employee.leads_type',
                        'employee.leave_transaction',
                        'employee.shift',
                        'employee.holiday_type',
                        'employee.holiday.page',
                    ];
                @endphp


                @if (($isPrivilegedUser || PermissionHelper::canAccessAny($attendanceRoutes)) && $hasAttendanceFeature)
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0)"
                                    class="{{ (request()->routeIs('employee.*') || request()->routeIs('device-register.*') || request()->routeIs('mobile-attendance.*')) && !request()->routeIs('manage_users.employee_list') && !request()->routeIs('employee.grid') && !request()->routeIs('employee.details') ? 'subdrop active' : '' }}">
                                    <i class="fa-solid fa-users-viewfinder"></i>
                                    <span>Attendance</span>
                                    <span class="menu-arrow"></span>
                                </a>

                                <ul>
                                    {{-- Attendance (Admin) --}}
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('employee.attendace.admin'))
                                        <li>
                                            <a href="{{ box_route('employee.attendace.admin') }}"
                                                data-route="employee.attendace.admin"
                                                class="{{ request()->routeIs('employee.attendace.admin') ? 'active' : '' }}">
                                                Attendance All
                                            </a>
                                        </li>
                                    @endif

                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('employee.attendace.team'))
                                        <li>
                                            <a href="{{ box_route('employee.attendace.team') }}"
                                                data-route="employee.attendace.team"
                                                class="{{ request()->routeIs('employee.attendace.team') ? 'active' : '' }}">
                                                Team Attendance
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Biometric Machine --}}
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('employee.biometric.machine'))
                                        <li>
                                            <a href="{{ box_route('employee.biometric.machine') }}"
                                                data-route="employee.biometric.machine"
                                                class="{{ request()->routeIs('employee.biometric.machine') ? 'active' : '' }}">
                                                Biometric Machine (Admin)
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Mobile Device Register --}}
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('device-register.index'))
                                        <li>
                                            <a href="{{ box_route('device-register.index') }}"
                                                data-route="device-register.index"
                                                class="{{ request()->routeIs('device-register.*') ? 'active' : '' }}">
                                                Mobile Device Register
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Enrolled Fingerprints --}}
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('mobile-attendance.fingerprints'))
                                        <li>
                                            <a href="{{ box_route('mobile-attendance.fingerprints') }}"
                                                data-route="mobile-attendance.fingerprints"
                                                class="{{ request()->routeIs('mobile-attendance.fingerprints') ? 'active' : '' }}">
                                                Enrolled Fingerprints
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Mobile Punches --}}
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('mobile-attendance.punches'))
                                        <li>
                                            <a href="{{ box_route('mobile-attendance.punches') }}"
                                                data-route="mobile-attendance.punches"
                                                class="{{ request()->routeIs('mobile-attendance.punches') ? 'active' : '' }}">
                                                Mobile Punches
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Attendance (Employee) --}}
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('employee.attendace'))
                                        <li>
                                            <a href="{{ box_route('employee.attendace') }}" data-route="employee.attendace"
                                                class="{{ request()->routeIs('employee.attendace') ? 'active' : '' }}">
                                                Attendance
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Employee Dashboard --}}
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('employee.dashboard'))
                                        <li>
                                            <a href="{{ box_route('employee.dashboard') }}" data-route="employee.dashboard"
                                                class="{{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                                                Employee Dashboard
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Attendance Calendar --}}
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('employee.attendance.calendar'))
                                        <li>
                                            <a href="{{ box_route('employee.attendance.calendar') }}"
                                                data-route="employee.attendance.calendar"
                                                class="{{ request()->routeIs('employee.attendance.calendar') ? 'active' : '' }}">
                                                Attendance Calendar
                                            </a>
                                        </li>
                                    @endif
                                        

                                        <!--<li class="submenu submenu-two"><a href="javascript:void(0);">Leaves<span class="menu-arrow inside-submenu"></span></a>
											<ul>
												<li><a href="javascript:void(0);">Leaves </a></li>
                                                <li><a href="javascript:void(0);">Admin Leaves </a></li>
                                                <li><a href="javascript:void(0);">Leave Settings </a></li>
                                                <li><a href="javascript:void(0);">Leave Type </a></li>
                                                <li><a href="javascript:void(0);">Leave Transaction </a></li>
                                                <li><a href="javascript:void(0);">Leave Transaction All </a></li>
                                                <li><a href="javascript:void(0);">Team Leave Transaction  </a></li>
											</ul>
										</li>
                                        <li class="submenu submenu-two"><a href="javascript:void(0);">Holiday <span class="menu-arrow inside-submenu"></span></a>
											<ul>
												<li><a href="javascript:void(0);">Holiday  </a></li>
                                                <li><a href="javascript:void(0);">Holiday Type  </a></li>
											</ul>
										</li> -->
                                    
                                        <li class="submenu submenu-two"><a href="javascript:void(0);">Leaves<span class="menu-arrow inside-submenu"></span></a>
											<ul>
												{{-- Leaves --}}
                                                @if (($isPrivilegedUser || PermissionHelper::canAccessRoute('employee.leaves')) && $hasAttendanceFeature)
                                                    <li>
                                                        <a href="{{ box_route('employee.leaves') }}" data-route="employee.leaves"
                                                            class="{{ request()->routeIs('employee.leaves') ? 'active' : '' }}">
                                                            Leaves
                                                        </a>
                                                    </li>
                                                @endif

                                                {{-- Admin Leaves --}}
                                                @if (($isPrivilegedUser || PermissionHelper::canAccessRoute('employee.leave.admin')) && $hasAttendanceFeature)
                                                    <li>
                                                        <a href="{{ box_route('employee.leave.admin') }}" data-route="employee.leave.admin"
                                                            class="{{ request()->routeIs('employee.leave.admin') ? 'active' : '' }}">
                                                            Admin Leaves
                                                        </a>
                                                    </li>
                                                @endif
                                                {{-- Leave Settings --}}
                                                {{-- @if (($isPrivilegedUser || PermissionHelper::canAccessRoute('employee.leave_settings')) && $hasAttendanceFeature)
                                                    <li>
                                                        <a href="{{ box_route('employee.leave_settings') }}"
                                                            data-route="employee.leave_settings"
                                                            class="{{ request()->routeIs('employee.leave_settings') ? 'active' : '' }}">
                                                            Leave Settings
                                                        </a>
                                                    </li>
                                                @endif --}}
                                                {{-- Leave Type --}}
                                                @if (($isPrivilegedUser || PermissionHelper::canAccessRoute('employee.leads_type')) && $hasAttendanceFeature)
                                                    <li>
                                                        <a href="{{ box_route('employee.leads_type') }}" data-route="employee.leads_type"
                                                            class="{{ request()->routeIs('employee.leads_type') ? 'active' : '' }}">
                                                            Leave Type
                                                        </a>
                                                    </li>
                                                @endif
                                                {{-- Leave Transaction --}}
                                                @if (($isPrivilegedUser || PermissionHelper::canAccessRoute('employee.leave_transaction')) && $hasAttendanceFeature)
                                                    <li>
                                                        <a href="{{ box_route('employee.leave_transaction') }}"
                                                            data-route="employee.leave_transaction"
                                                            class="{{ request()->routeIs('employee.leave_transaction') ? 'active' : '' }}">
                                                            Leave Transaction
                                                        </a>
                                                    </li>
                                                @endif
                                                @if (($isPrivilegedUser || PermissionHelper::canAccessRoute('employee.leave_transaction')) && $hasAttendanceFeature)
                                                    <li>
                                                        <a href="{{ box_route('employee.leave_transaction.all') }}"
                                                            data-route="employee.leave_transaction.all"
                                                            class="{{ request()->routeIs('employee.leave_transaction.all') ? 'active' : '' }}">
                                                            Leave Transaction All
                                                        </a>
                                                    </li>
                                                @endif
                                                @if (($isPrivilegedUser || PermissionHelper::canAccessRoute('employee.leave_transaction.team')) && $hasAttendanceFeature)
                                                    <li>
                                                        <a href="{{ box_route('employee.leave_transaction.team') }}"
                                                            data-route="employee.leave_transaction.team"
                                                            class="{{ request()->routeIs('employee.leave_transaction.team') ? 'active' : '' }}">
                                                            Team Leave Transaction
                                                        </a>
                                                    </li>
                                                @endif
											</ul>
										</li>
                                        <li class="submenu submenu-two"><a href="javascript:void(0);">Holiday <span class="menu-arrow inside-submenu"></span></a>
											<ul>
												{{-- Holiday --}}
                                                @if (($isPrivilegedUser || PermissionHelper::canAccessRoute('employee.holiday.page')) && $hasAttendanceFeature)
                                                    <li>
                                                        <a href="{{ box_route('employee.holiday.page') }}"
                                                            data-route="employee.holiday.page"
                                                            class="{{ request()->routeIs('employee.holiday.page') ? 'active' : '' }}">
                                                            Holiday
                                                        </a>
                                                    </li>
                                                @endif
                                                {{-- Holiday Type --}}
                                                @if (($isPrivilegedUser || PermissionHelper::canAccessRoute('employee.holiday_type')) && $hasAttendanceFeature)
                                                    <li>
                                                        <a href="{{ box_route('employee.holiday_type') }}"
                                                            data-route="employee.holiday_type"
                                                            class="{{ request()->routeIs('employee.holiday_type') ? 'active' : '' }}">
                                                            Holiday Type
                                                        </a>
                                                    </li>
                                                @endif
											</ul>
										</li>




                                    

                                    

                                    

                                    

                                    

                                    

                                    
                                    {{-- Shift --}}
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('employee.shift'))
                                        <li>
                                            <a href="{{ box_route('employee.shift') }}" data-route="employee.shift"
                                                class="{{ request()->routeIs('employee.shift') ? 'active' : '' }}">
                                                Shift
                                            </a>
                                        </li>
                                    @endif

                                    

                                    

                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif


                @if ($hasHrToolsRead)
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0)"
                                    class="{{ request()->routeIs('hr.*') ? 'subdrop active' : '' }}">
                                    <i class="ti ti-cash"></i><span>HR Tools</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('hr.employess.assets'))
                                        <li>
                                            <a href="{{ box_route('hr.employess.assets') }}" data-route="hr.employess.assets"
                                                class="{{ request()->routeIs('hr.employess.assets') ? 'active' : '' }}">
                                                <span>Assets</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('hr.employess.hr.documents'))
                                        <li>
                                            <a href="{{ box_route('hr.employess.hr.documents') }}"
                                                data-route="hr.employess.hr.documents"
                                                class="{{ request()->routeIs('hr.employess.hr.documents') ? 'active' : '' }}">
                                                <span>Hr Documents</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('hr.employeess.letter.generation'))
                                        <li>
                                            <a href="{{ box_route('hr.employeess.letter.generation') }}"
                                                data-route="hr.employeess.letter.generation"
                                                class="{{ request()->routeIs('hr.employeess.letter.generation') ? 'active' : '' }}">
                                                <span>Letter Generation</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('hr.job.applicants.index'))
                                        <li>
                                            <a href="{{ box_route('hr.job.applicants.index') }}"
                                                data-route="hr.job.applicants.index"
                                                class="{{ request()->routeIs('hr.job.applicants.*') ? 'active' : '' }}">
                                                <span>Job Applicants</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif

                @if ($hasPayrollRead)
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0)"
                                    class="{{ request()->routeIs('payroll.*') ? 'subdrop active' : '' }}">
                                    <i class="ti ti-cash"></i><span>Payroll</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('payroll.employee.salary'))
                                        <li>
                                            <a href="{{ box_route('payroll.employee.salary') }}"
                                                data-route="payroll.employee.salary"
                                                class="{{ request()->routeIs('payroll.employee.salary') ? 'active' : '' }}">
                                                <span>Employee Salary</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('payroll.employee.report'))
                                        <li>
                                            <a href="{{ box_route('payroll.employee.report') }}"
                                                data-route="payroll.employee.report"
                                                class="{{ request()->routeIs('payroll.employee.report') ? 'active' : '' }}">
                                                <span>Salary Report</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('payroll.salary.slip'))
                                        <li>
                                            <a href="{{ box_route('payroll.salary.slip') }}" data-route="payroll.salary.slip"
                                                class="{{ request()->routeIs('payroll.salary.slip') ? 'active' : '' }}">
                                                <span>Pay Slip</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('payroll.payroll.items'))
                                        <li>
                                            <a href="{{ box_route('payroll.payroll.items') }}"
                                                data-route="payroll.payroll.items"
                                                class="{{ request()->routeIs('payroll.payroll.items') ? 'active' : '' }}">
                                                <span>Payroll Items</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('payroll.setting'))
                                        <li>
                                            <a href="{{ box_route('payroll.setting') }}" data-route="payroll.setting"
                                                class="{{ request()->routeIs('payroll.setting') ? 'active' : '' }}"><span>Payroll
                                                    Setting</span></a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('payroll.loan.settings'))
                                        <li>
                                            <a href="{{ box_route('payroll.loan.settings') }}"
                                                data-route="payroll.loan.settings"
                                                class="{{ request()->routeIs('payroll.loan.settings') ? 'active' : '' }}">
                                                <span>Loan Settings</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('payroll.loan.tracker'))
                                        <li>
                                            <a href="{{ box_route('payroll.loan.tracker') }}" data-route="payroll.loan.tracker"
                                                class="{{ request()->routeIs('payroll.loan.tracker') ? 'active' : '' }}"><span>Loan
                                                    Tracker</span></a>
                                        </li>
                                    @endif

                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif

                @if ($hasLeadsRead)
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0)"
                                    class="{{ request()->routeIs('leads.*') || request()->routeIs('lead.approvals.*') || request()->routeIs('app.settings.manage.form') || request()->routeIs('facebook.leads') ? 'subdrop active' : '' }}">
                                    <i class="ti ti-phone"></i><span>Leads</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <!-- @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('leads.dashboard'))
                                                <li>
                                                    <a href="{{ box_route('leads.dashboard') }}" data-route="leads.dashboard"
                                                        class="{{ request()->routeIs('leads.dashboard') ? 'active' : '' }}">
                                                        <span>Leads Dashboard</span>
                                                    </a>
                                                </li>
                                            @endif -->
                                    <li>
                                        <a href="{{ box_route('leads.user.dashboard') }}" data-route="leads.user.dashboard"
                                            class="{{ request()->routeIs('leads.user.dashboard') ? 'active' : '' }}">
                                            <span>User Leads Dashboard</span>
                                        </a>
                                    </li>
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('leads.details'))
                                        <li>
                                            <a href="{{ box_route('leads.details') }}" data-route="leads.details"
                                                class="{{ request()->routeIs('leads.details') ? 'active' : '' }}">
                                                <span>Leads</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('leads.details.all'))
                                        <li>
                                            <a href="{{ box_route('leads.details.all') }}" data-route="leads.details.all"
                                                class="{{ request()->routeIs('leads.details.all') ? 'active' : '' }}">
                                                <span>Leads All</span>
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a href="{{ box_route('leads.details.starred') }}" data-route="leads.details.starred"
                                            class="{{ request()->routeIs('leads.details.starred') ? 'active' : '' }}">
                                            <span>Leads Starred</span>
                                        </a>
                                    </li>
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('leads.details.trash'))
                                        <li>
                                            <a href="{{ box_route('leads.details.trash') }}" data-route="leads.details.trash"
                                                class="{{ request()->routeIs('leads.details.trash') ? 'active' : '' }}">
                                                <span>Leads Trash</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('leads.details.duplicate'))
                                        <li>
                                            <a href="{{ box_route('leads.details.duplicate') }}"
                                                data-route="leads.details.duplicate"
                                                class="{{ request()->routeIs('leads.details.duplicate') ? 'active' : '' }}">
                                                <span>Leads Duplicate</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser)
                                        <li>
                                            <a href="{{ box_route('lead.approvals.index') }}"
                                                data-route="lead.approvals.index"
                                                class="{{ request()->routeIs('lead.approvals.index') ? 'active' : '' }}">
                                                <span>Lead Approvals</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('leads.details.working-location'))
                                        <li>
                                            <a href="{{ box_route('leads.details.working-location') }}"
                                                data-route="leads.details.working-location"
                                                class="{{ request()->routeIs('leads.details.working-location') ? 'active' : '' }}">
                                                <span>Working Location</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if (PermissionHelper::canAccessRoute('app.settings.manage.form'))
                                        <li>
                                            <a href="{{ box_route('app.settings.manage.form') }}"
                                                data-route="app.settings.manage.form"
                                                class="{{ request()->routeIs('app.settings.manage.form') ? 'active' : '' }}">
                                                <span>Manage Form</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('facebook.leads'))
                                        <li>
                                            <a href="{{ route('facebook.leads', ['box_slug' => $boxSlug]) }}"
                                                class="{{ request()->routeIs('facebook.leads') ? 'active' : '' }}">
                                                <span>Facebook Leads</span>
                                            </a>
                                        </li>
                                    @endif

                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (\App\Helpers\PermissionHelper::hasModulePermission('activities_records', 'read'))
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0)"
                                    class="{{ (request()->routeIs('reports.ReminderList') || request()->routeIs('reports.TodaysFollowUp') || request()->routeIs('reports.PendingFollowUps') || request()->routeIs('reports.UpcomingFollowUps') || request()->routeIs('reports.TodayDSR') || request()->routeIs('reports.ThisMonthDSR') || request()->routeIs('reports.AllDSR') || request()->routeIs('reports.AllActivityLeads')) ? 'subdrop active' : '' }}">
                                    <i class="ti ti-file-analytics"></i><span>Activities Records</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    @if(\App\Helpers\PermissionHelper::hasModulePermission('activities_records', 'read', 'reminder_list'))
                                        <li>
                                            <a href="{{ box_route('reports.TodaysFollowUp') }}"
                                                class="{{ request()->routeIs('reports.TodaysFollowUp') ? 'active' : '' }}">
                                                <span>Todays Follow Up</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ box_route('reports.PendingFollowUps') }}"
                                                class="{{ request()->routeIs('reports.PendingFollowUps') ? 'active' : '' }}">
                                                <span>Pending Follow Ups</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ box_route('reports.UpcomingFollowUps') }}"
                                                class="{{ request()->routeIs('reports.UpcomingFollowUps') ? 'active' : '' }}">
                                                <span>Upcoming Follow Ups</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if(\App\Helpers\PermissionHelper::hasModulePermission('activities_records', 'read', 'today_dsr'))
                                        <li>
                                            <a href="{{ box_route('reports.TodayDSR') }}"
                                                class="{{ request()->routeIs('reports.TodayDSR') ? 'active' : '' }}">
                                                <span>Today DSR</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if(\App\Helpers\PermissionHelper::hasModulePermission('activities_records', 'read', 'this_month_dsr'))
                                        <li>
                                            <a href="{{ box_route('reports.ThisMonthDSR') }}"
                                                class="{{ request()->routeIs('reports.ThisMonthDSR') ? 'active' : '' }}">
                                                <span>This Month DSR</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if(\App\Helpers\PermissionHelper::hasModulePermission('activities_records', 'read', 'all_dsr'))
                                        <li>
                                            <a href="{{ box_route('reports.AllDSR') }}"
                                                class="{{ request()->routeIs('reports.AllDSR') ? 'active' : '' }}">
                                                <span>All DSR</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if(\App\Helpers\PermissionHelper::hasModulePermission('activities_records', 'read', 'all_activity_leads'))
                                        <li>
                                            <a href="{{ box_route('reports.AllActivityLeads') }}"
                                                class="{{ request()->routeIs('reports.AllActivityLeads') ? 'active' : '' }}">
                                                <span>All Activity</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif

                @php
                    $myReports = collect();
                    $todayTasksCount = 0;
                    if (isset($boxUserEntry) && $boxUserEntry) {
                        $myReports = \App\Models\MyReport::where('box_id', $boxUserEntry->box_id)
                            ->where(function ($q) use ($user) {
                                $q->where('user_id', $user->id)
                                    ->orWhere(function ($sub) {
                                        $sub->where('is_public', true);
                                    });
                            })
                            ->orderBy('name', 'asc')
                            ->get();

                        $todayTasksCount = \App\Models\Activity::where('box_id', $boxUserEntry->box_id)
                            ->where('type', '!=', 'history')
                            ->whereBetween('activity_date', [\Carbon\Carbon::today()->startOfDay(), \Carbon\Carbon::today()->endOfDay()])
                            ->where(function($q) {
                                $q->where('status', '!=', 'Completed')
                                  ->orWhereNull('status');
                            })
                            ->count();
                    }
                @endphp


                {{-- My Reports Section (Always Visible) --}}
                @if (isset($boxSlug))
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0)"
                                    class="{{ (request()->has('report_id') || request()->routeIs('tasks.index')) ? 'subdrop active' : '' }}">
                                    <i class="ti ti-file-analytics"></i><span>My Reports</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>

                                    @foreach($myReports as $report)
                                        <li>
                                            <a href="{{ box_route('leads.details', ['report_id' => $report->id]) }}"
                                                class="{{ (request()->query('report_id') == $report->id) ? 'active' : '' }}">
                                                <span>{{ $report->name }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif

                @if ($hasIntegrationsRead)
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0)"
                                    class="{{ request()->routeIs('settings.integrations.*') ? 'subdrop active' : '' }}">
                                    <i class="ti ti-phone"></i><span>Integration</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.integrations.integrations'))
                                        <li><a href="{{ box_route('settings.integrations.integrations') }}"
                                                data-route="settings.integrations.integrations"
                                                class="{{ request()->routeIs('settings.integrations.integrations') ? 'active' : '' }}">Integrations</a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('facebook.dashboard'))
                                        <li><a href="{{ box_route('facebook.dashboard') }}"
                                                data-route="facebook.dashboard"
                                                class="{{ request()->routeIs('facebook.dashboard') ? 'active' : '' }}">Facebook
                                                Dashboard</a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.integrations.facebook.form'))
                                        <li><a href="{{ box_route('settings.integrations.facebook.form') }}"
                                                data-route="settings.integrations.facebook.form"
                                                class="{{ request()->routeIs('settings.integrations.facebook.form') ? 'active' : '' }}">Facebook
                                                Forms</a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.integrations.compaign.settings'))
                                        <li>
                                            <a href="{{ box_route('settings.integrations.compaign.settings') }}"
                                                data-route="settings.integrations.compaign.settings"
                                                class="{{ request()->routeIs('settings.integrations.compaign.settings') ? 'active' : '' }}">
                                                Compaign Settings
                                            </a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.integrations.compaign.details'))
                                        <li>
                                            <a href="{{ box_route('settings.integrations.compaign.details') }}"
                                                data-route="settings.integrations.compaign.details"
                                                class="{{ request()->routeIs('settings.integrations.compaign.details') ? 'active' : '' }}">
                                                Compaign Details
                                            </a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.integrations.create.project'))
                                        <li>
                                            <a href="{{ box_route('settings.integrations.create.project') }}"
                                                data-route="settings.integrations.create.project"
                                                class="{{ request()->routeIs('settings.integrations.create.project') ? 'active' : '' }}">
                                                Create Project
                                            </a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.integrations.bulk.uploads'))
                                        <li><a href="{{ box_route('settings.integrations.bulk.uploads') }}"
                                                data-route="settings.integrations.bulk.uploads"
                                                class="{{ request()->routeIs('settings.integrations.bulk.uploads') ? 'active' : '' }}">Bulk
                                                Upload</a>
                                        </li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.integrations.labels'))
                                        <li><a href="{{ box_route('settings.integrations.labels') }}"
                                                data-route="settings.integrations.labels"
                                                class="{{ request()->routeIs('settings.integrations.labels') ? 'active' : '' }}">Labels
                                            </a>
                                        </li>
                                    @endif

                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif

                @if ($hasWhatsAppFeature || $isPrivilegedUser)
                    @php
                        $canAccessWhatsAppDashboard = $isPrivilegedUser || PermissionHelper::canAccessRoute('whatsapp.dashboard');
                        $canAccessWhatsAppChat = $isPrivilegedUser || PermissionHelper::canAccessRoute('whatsapp.index');
                        $canAccessWhatsAppCreate = $isPrivilegedUser || PermissionHelper::canAccessRoute('whatsapp.create_project');
                        $canAccessWhatsAppCampaign = $isPrivilegedUser || PermissionHelper::canAccessRoute('whatsapp.campaign');
                        $canAccessWhatsAppComplete = $isPrivilegedUser || PermissionHelper::canAccessRoute('whatsapp.campaign_complete');
                        $canAccessWhatsAppArchive = $isPrivilegedUser || PermissionHelper::canAccessRoute('whatsapp.campaign_archieve');
                    @endphp
                    @if ($canAccessWhatsAppDashboard || $canAccessWhatsAppChat || $canAccessWhatsAppCreate || $canAccessWhatsAppCampaign || $canAccessWhatsAppComplete || $canAccessWhatsAppArchive)
                        <li>
                            <ul>
                                <li class="submenu">
                                    <a href="javascript:void(0)"
                                        class="{{ request()->is('*/whatsapp*') ? 'subdrop active' : '' }}">
                                        <i class="fa-brands fa-whatsapp"></i><span>WhatsApp Integration</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($canAccessWhatsAppDashboard)
                                            <li>
                                                <a href="{{ box_route('whatsapp.dashboard') }}"
                                                    class="{{ request()->routeIs('whatsapp.dashboard') ? 'active' : '' }}">
                                                    <span>WhatsApp Dashboard</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if ($canAccessWhatsAppChat)
                                            <li>
                                                <a href="{{ box_route('whatsapp.index') }}"
                                                    class="{{ request()->routeIs('whatsapp.index') ? 'active' : '' }}">
                                                    <span>WhatsApp Chat</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if ($canAccessWhatsAppCreate)
                                            <li>
                                                <a href="{{ box_route('whatsapp.create_project') }}"
                                                    class="{{ request()->routeIs('whatsapp.create_project') ? 'active' : '' }}">
                                                    <span>Projects</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ box_route('whatsapp.create_template') }}"
                                                    class="{{ request()->routeIs('whatsapp.create_template') ? 'active' : '' }}">
                                                    <span>Templates</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ box_route('whatsapp.chatbots.index') }}"
                                                    class="{{ request()->routeIs('whatsapp.chatbots.*') ? 'active' : '' }}">
                                                    <span>Chatbots</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if ($canAccessWhatsAppCreate)
                                            <li>
                                                <a href="{{ box_route('whatsapp.quick_replies.index') }}"
                                                    class="{{ request()->routeIs('whatsapp.quick_replies.*') ? 'active' : '' }}">
                                                    <span>Quick Replies</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if ($canAccessWhatsAppCampaign)
                                            <li>
                                                <a href="{{ box_route('whatsapp.campaign') }}"
                                                    class="{{ request()->routeIs('whatsapp.campaign') ? 'active' : '' }}">
                                                    <span>Campaign</span>
                                                </a>
                                            </li>
                                        @endif

                                    </ul>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif


                @if ($hasAccountsRead)
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0)"
                                    class="{{ request()->routeIs('accounts.*') ? 'subdrop active' : '' }}">
                                    <i class="fa-solid fa-file-invoice-dollar"></i><span>Accounts</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    @php
                                        $accountsCompanies = \App\Models\Company::where('box_id', $boxUserEntry->box_id)->orderBy('id', 'asc')->get();
                                    @endphp
                                    @foreach($accountsCompanies as $acCompany)
                                        <li class="submenu shortname">
                                            <a href="javascript:void(0)"
                                                class="{{ (request()->routeIs('accounts.*') && request('company_id') == $acCompany->id) ? 'subdrop active' : '' }}">
                                                <span>{{ $acCompany->bill_name ?? $acCompany->name }}</span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <ul>
                                                @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('accounts.ledger.index'))
                                                    <li><a href="{{ box_route('accounts.ledger.index', ['company_id' => $acCompany->id]) }}"
                                                            class="{{ request()->routeIs('accounts.ledger.*') && request('company_id') == $acCompany->id ? 'active' : '' }}">Ledger</a>
                                                    </li>
                                                @endif
                                                @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('accounts.receipts'))
                                                    <li><a href="{{ box_route('accounts.receipts', ['company_id' => $acCompany->id]) }}"
                                                            class="{{ request()->routeIs('accounts.receipts') && request('company_id') == $acCompany->id ? 'active' : '' }}">Receipts</a>
                                                    </li>
                                                @endif
                                                @if ($isPrivilegedUser || App\Helpers\PermissionHelper::canAccessRoute('accounts.invoices'))
                                                    <li><a href="{{ box_route('accounts.invoices', ['company_id' => $acCompany->id]) }}"
                                                            class="{{ request()->routeIs('accounts.invoices') && request('company_id') == $acCompany->id ? 'active' : '' }}">Invoices</a>
                                                    </li>
                                                @endif
                                                @if ($isPrivilegedUser || App\Helpers\PermissionHelper::canAccessRoute('accounts.proforma_invoices'))
                                                    <li><a href="{{ box_route('accounts.proforma_invoices', ['company_id' => $acCompany->id]) }}"
                                                            class="{{ request()->routeIs('accounts.proforma_invoices') && request('company_id') == $acCompany->id ? 'active' : '' }}">Proforma Invoices</a>
                                                    </li>
                                                @endif
                                                @if ($isPrivilegedUser || App\Helpers\PermissionHelper::canAccessRoute('accounts.invoice_grid'))
                                                    <li><a href="{{ box_route('accounts.invoice_grid', ['company_id' => $acCompany->id]) }}"
                                                            class="{{ request()->routeIs('accounts.invoice_grid') && request('company_id') == $acCompany->id ? 'active' : '' }}">Invoice
                                                            Grid</a></li>
                                                @endif
                                                <li><a href="{{ box_route('accounts.products.index', ['company_id' => $acCompany->id]) }}" class="{{ request()->routeIs('accounts.products.index') && request('company_id') == $acCompany->id ? 'active' : '' }}">All Products</a></li>
                                                <li><a href="{{ box_route('accounts.product_hsns.index', ['company_id' => $acCompany->id]) }}" class="{{ request()->routeIs('accounts.product_hsns.index') && request('company_id') == $acCompany->id ? 'active' : '' }}">Product HSNs</a></li>
                                                <li><a href="{{ box_route('accounts.product_salts.index', ['company_id' => $acCompany->id]) }}" class="{{ request()->routeIs('accounts.product_salts.index') && request('company_id') == $acCompany->id ? 'active' : '' }}">Product Salts</a></li>
                                                @if ($isPrivilegedUser || App\Helpers\PermissionHelper::canAccessRoute('accounts.invoice_settings'))
                                                    <li><a href="{{ box_route('accounts.invoice_settings', ['company_id' => $acCompany->id]) }}"
                                                            class="{{ request()->routeIs('accounts.invoice_settings') && request('company_id') == $acCompany->id ? 'active' : '' }}">Invoice
                                                            Settings</a></li>
                                                @endif
                                                @if ($isPrivilegedUser || App\Helpers\PermissionHelper::canAccessRoute('accounts.invoice_templates'))
                                                    <li><a href="{{ box_route('accounts.invoice_templates', ['company_id' => $acCompany->id]) }}"
                                                            class="{{ request()->routeIs('accounts.invoice_templates') && request('company_id') == $acCompany->id ? 'active' : '' }}">Invoice
                                                            Templates</a></li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif

                @if ($hasReportsRead)
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0)"
                                    class="{{ (request()->routeIs('reports.source_stats') || request()->routeIs('reports.status_stats') || request()->routeIs('reports.task_stats') || request()->routeIs('reports.date_task_stats') || request()->routeIs('reports.user_stats')) ? 'subdrop active' : '' }}">
                                    <i class="fa-solid fa-chart-pie"></i><span>Stats</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    @if ($isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute('reports.source_stats'))
                                        <li><a href="{{ box_route('reports.source_stats') }}"
                                                class="{{ request()->routeIs('reports.source_stats') ? 'active' : '' }}">Source
                                                Stats</a></li>
                                    @endif
                                    @if ($isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute('reports.status_stats'))
                                        <li><a href="{{ box_route('reports.status_stats') }}"
                                                class="{{ request()->routeIs('reports.status_stats') ? 'active' : '' }}">Status
                                                Stats</a></li>
                                    @endif
                                    @if ($isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute('reports.task_stats'))
                                        <li><a href="{{ box_route('reports.task_stats') }}"
                                                class="{{ request()->routeIs('reports.task_stats') ? 'active' : '' }}">Task
                                                Stats</a></li>
                                    @endif
                                    @if ($isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute('reports.date_task_stats'))
                                        <li><a href="{{ box_route('reports.date_task_stats') }}"
                                                class="{{ request()->routeIs('reports.date_task_stats') ? 'active' : '' }}">Date
                                                Task Stats</a></li>
                                    @endif
                                    @if ($isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute('reports.user_stats'))
                                        <li><a href="{{ box_route('reports.user_stats') }}"
                                                class="{{ request()->routeIs('reports.user_stats') ? 'active' : '' }}">User
                                                Stats</a></li>
                                    @endif
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (
                        $hasGeneralSettingsRead ||
                        $hasWebsiteSettingsRead ||
                        $hasAppSettingsRead ||
                        $hasSystemSettingsRead ||
                        $hasFinancialSettingsRead ||
                        $hasOtherSettingsRead
                    )
                    <li>
                        <h6 class="submenu-hdr">Settings</h6>
                        <ul>
                            @if ($hasGeneralSettingsRead)
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        class="{{ (request()->routeIs('settings.general.*') && !request()->routeIs('manage_users.roles_permissions')) ? 'subdrop active' : '' }}">
                                        <i class="ti ti-settings-cog"></i><span>General Settings</span><span
                                            class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.general.companies'))
                                            <li><a href="{{ box_route('settings.general.companies') }}"
                                                    data-route="settings.general.companies"
                                                    class="{{ request()->routeIs('settings.general.companies') ? 'active' : '' }}">Companies</a>
                                            </li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.general.companies_grid'))
                                            <li><a href="{{ box_route('settings.general.companies_grid') }}"
                                                    data-route="settings.general.companies_grid"
                                                    class="{{ request()->routeIs('settings.general.companies_grid') ? 'active' : '' }}">Companies
                                                    Grid</a></li>
                                        @endif
                                        {{-- @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.general.companys_details'))
                                            <li><a href="{{ box_route('settings.general.companys_details') }}"
                                                    data-route="settings.general.companys_details"
                                                    class="{{ request()->routeIs('settings.general.companys_details') ? 'active' : '' }}">Companies
                                                    Details</a></li>
                                        @endif --}}

                                        {{-- @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.general.hsns'))
                                            <li><a href="{{ box_route('settings.general.hsns') }}"
                                                    data-route="settings.general.hsns"
                                                    class="{{ request()->routeIs('settings.general.hsns') ? 'active' : '' }}">HSNs</a>
                                            </li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.general.packages'))
                                            <li><a href="{{ box_route('settings.general.packages') }}"
                                                    data-route="settings.general.packages"
                                                    class="{{ request()->routeIs('settings.general.packages') ? 'active' : '' }}">Items</a>
                                            </li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.general.companys'))
                                            <li><a href="{{ box_route('settings.general.companys') }}"
                                                    data-route="settings.general.companys"
                                                    class="{{ request()->routeIs('settings.general.companys') ? 'active' : '' }}">Setting</a>
                                            </li>
                                        @endif --}}
                                        {{-- @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.general.activities'))
                                            <li><a href="{{ box_route('settings.general.activities') }}"
                                                    data-route="settings.general.activities"
                                                    class="{{ request()->routeIs('settings.general.activities') ? 'active' : '' }}">Activity
                                                    Type</a></li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.general.sources'))
                                            <li><a href="{{ box_route('settings.general.sources') }}"
                                                    data-route="settings.general.sources"
                                                    class="{{ request()->routeIs('settings.general.sources') ? 'active' : '' }}">Lead
                                                    Source</a></li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.general.lead_status'))
                                            <li><a href="{{ box_route('settings.general.lead_status') }}"
                                                    data-route="settings.general.lead_status"
                                                    class="{{ request()->routeIs('settings.general.lead_status') ? 'active' : '' }}">Lead
                                                    Status</a></li>
                                        @endif --}}
                                        @if (($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.general.custom_fields')) && $hasCustomFieldsFeature)
                                            <li><a href="{{ box_route('settings.general.custom_fields') }}"
                                                    data-route="settings.general.custom_fields"
                                                    class="{{ request()->routeIs('settings.general.custom_fields') ? 'active' : '' }}">Custom
                                                    Fields</a></li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.general.email_inboxes'))
                                            <li><a href="{{ box_route('settings.general.email_inboxes') }}"
                                                    data-route="settings.general.email_inboxes"
                                                    class="{{ request()->routeIs('settings.general.email_inboxes') ? 'active' : '' }}">Email
                                                    Inboxes</a></li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.general.email_messages'))
                                            <li><a href="{{ box_route('settings.general.email_messages') }}"
                                                    data-route="settings.general.email_messages"
                                                    class="{{ request()->routeIs('settings.general.email_messages') ? 'active' : '' }}">Email
                                                    Messages</a></li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.general.api_keys'))
                                            <li><a href="{{ box_route('settings.general.api_keys') }}"
                                                    data-route="settings.general.api_keys"
                                                    class="{{ request()->routeIs('settings.general.api_keys') ? 'active' : '' }}">Lead
                                                    APIs</a></li>
                                        @endif
                                        {{-- @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('manage_users.roles_permissions'))
                                            <li><a href="{{ box_route('manage_users.roles_permissions') }}"
                                                    data-route="manage_users.roles_permissions"
                                                    class="{{ request()->routeIs('manage_users.roles_permissions') ? 'active' : '' }}">User
                                                    Roles</a></li>
                                        @endif --}}
                                    </ul>
                                </li>
                            @endif

                            @if ($hasWebsiteSettingsRead)
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        class="{{ request()->routeIs('website.settings.*') && !request()->routeIs('manage_users.employee_type') && !request()->routeIs('manage_users.work_mode') ? 'subdrop active' : '' }}">
                                        <i class="ti ti-world-cog"></i><span>Website Settings</span><span
                                            class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('website.settings.company'))
                                            <li><a href="{{ box_route('website.settings.company') }}"
                                                    data-route="website.settings.company"
                                                    class="{{ request()->routeIs('website.settings.company') ? 'active' : '' }}">Company
                                                    Settings</a></li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('website.settings.branch'))
                                            <li><a href="{{ box_route('website.settings.branch') }}"
                                                    data-route="website.settings.branch"
                                                    class="{{ request()->routeIs('website.settings.branch') ? 'active' : '' }}">Company 
                                                    Branches</a></li>
                                        @endif


                                        @if (($isPrivilegedUser || PermissionHelper::canAccessRoute('website.settings.attendance.module')) && $hasAttendanceFeature)
                                            <li><a href="{{ box_route('website.settings.attendance.module') }}"
                                                    data-route="website.settings.attendance.module"
                                                    class="{{ request()->routeIs('website.settings.attendance.module') ? 'active' : '' }}">Attendace
                                                    Module</a></li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('website.settings.companys_currency'))
                                            <li><a href="{{ box_route('website.settings.companys_currency') }}"
                                                    data-route="website.settings.companys_currency"
                                                    class="{{ request()->routeIs('website.settings.companys_currency') ? 'active' : '' }}">Company
                                                    Currency
                                                </a></li>
                                        @endif


                                        {{-- @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('website.settings.localization'))
                                            <li><a href="{{ box_route('website.settings.localization') }}"
                                                    data-route="website.settings.localization"
                                                    class="{{ request()->routeIs('website.settings.localization') ? 'active' : '' }}">Localization</a>
                                            </li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('website.settings.prefixes'))
                                            <li><a href="{{ box_route('website.settings.prefixes') }}"
                                                    data-route="website.settings.prefixes"
                                                    class="{{ request()->routeIs('website.settings.prefixes') ? 'active' : '' }}">Prefixes</a>
                                            </li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('website.settings.preference'))
                                            <li><a href="{{ box_route('website.settings.preference') }}"
                                                    data-route="website.settings.preference"
                                                    class="{{ request()->routeIs('website.settings.preference') ? 'active' : '' }}">Preference</a>
                                            </li>
                                        @endif --}}
                                        {{-- @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('website.settings.appearance'))
                                            <li><a href="{{ box_route('website.settings.appearance') }}"
                                                    data-route="website.settings.appearance"
                                                    class="{{ request()->routeIs('website.settings.appearance') ? 'active' : '' }}">Appearance</a>
                                            </li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('website.settings.language'))
                                            <li><a href="{{ box_route('website.settings.language') }}"
                                                    data-route="website.settings.language"
                                                    class="{{ request()->routeIs('website.settings.language') ? 'active' : '' }}">Language</a>
                                            </li>
                                        @endif --}}
                                    </ul>
                                </li>
                            @endif

                            @if ($hasAppSettingsRead)
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        class="{{ request()->routeIs('app.settings.*') ? 'subdrop active' : '' }}">
                                        <i class="ti ti-apps"></i><span>App Settings</span><span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        {{-- @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('app.settings.invoice'))
                                            <li>
                                                <a href="{{ box_route('app.settings.invoice') }}" data-route="app.settings.invoice"
                                                    class="{{ request()->routeIs('app.settings.invoice') ? 'active' : '' }}">
                                                    Invoice Settings
                                                </a>
                                            </li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('app.settings.printers'))
                                            <li>
                                                <a href="{{ box_route('app.settings.printers') }}"
                                                    data-route="app.settings.printers"
                                                    class="{{ request()->routeIs('app.settings.printers') ? 'active' : '' }}">
                                                    Printers
                                                </a>
                                            </li>
                                        @endif --}}
                                        {{-- @if (($isPrivilegedUser || PermissionHelper::canAccessRoute('app.settings.custom_fields.index')) && $hasCustomFieldsFeature)
                                            <li>
                                                <a href="{{ box_route('app.settings.custom_fields.index') }}"
                                                    data-route="app.settings.custom_fields.index"
                                                    class="{{ request()->routeIs('app.settings.custom_fields.index') ? 'active' : '' }}">
                                                    Custom Fields
                                                </a>
                                            </li>
                                        @endif --}}
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('app.settings.manage.form'))
                                            <li>
                                                <a href="{{ box_route('app.settings.manage.form') }}"
                                                    data-route="app.settings.manage.form"
                                                    class="{{ request()->routeIs('app.settings.manage.form') ? 'active' : '' }}"><span>Manage
                                                        Form</span></a>
                                            </li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('app.settings.source'))
                                            <li><a href="{{ box_route('app.settings.source') }}" data-route="app.settings.source"
                                                    class="{{ request()->routeIs('app.settings.source') ? 'active' : '' }}"><span>Lead Source</span></a>
                                            </li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('app.settings.lost.reason'))
                                            <li><a href="{{ box_route('app.settings.lost.reason') }}"
                                                    data-route="app.settings.lost.reason"
                                                    class="{{ request()->routeIs('app.settings.lost.reason') ? 'active' : '' }}"><span>Lost
                                                        Reason</span></a></li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('app.settings.contact.stage'))
                                            <li><a href="{{ box_route('app.settings.contact.stage') }}"
                                                    data-route="app.settings.contact.stage"
                                                    class="{{ request()->routeIs('app.settings.contact.stage') ? 'active' : '' }}"><span>Lead
                                                        Status</span></a></li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('app.settings.leads.leads_sub_status'))
                                            <li><a href="{{ box_route('app.settings.leads.leads_sub_status') }}"
                                                    data-route="app.settings.leads.leads_sub_status"
                                                    class="{{ request()->routeIs('app.settings.leads.leads_sub_status') ? 'active' : '' }}"><span>Lead
                                                        Sub Status</span></a></li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('app.settings.industry'))
                                            <li><a href="{{ box_route('app.settings.industry') }}"
                                                    data-route="app.settings.industry"
                                                    class="{{ request()->routeIs('app.settings.industry') ? 'active' : '' }}"><span>Industry</span></a>
                                            </li>
                                        @endif
                                        @if (($isPrivilegedUser || PermissionHelper::canAccessRoute('app.settings.calls')) && $hasActivitiesFeature)
                                            <li><a href="{{ box_route('app.settings.calls') }}" data-route="app.settings.calls"
                                                    class="{{ request()->routeIs('app.settings.calls') ? 'active' : '' }}"><span>Activity
                                                        Setting</span></a></li>
                                        @endif

                                    </ul>
                                </li>
                            @endif

                            {{-- @if ($hasSystemSettingsRead)
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        class="{{ request()->routeIs('system.settings.*') ? 'subdrop active' : '' }}">
                                        <i class="ti ti-device-laptop"></i><span>System Settings</span><span
                                            class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('system.settings.email'))
                                            <li>
                                                <a href="{{ box_route('system.settings.email') }}"
                                                    data-route="system.settings.email"
                                                    class="{{ request()->routeIs('system.settings.email') ? 'active' : '' }}">
                                                    Email Settings
                                                </a>
                                            </li>
                                        @endif
                                        @if (($isPrivilegedUser || PermissionHelper::canAccessRoute('system.settings.sms_gateways')) && $hasSmsFeature)
                                            <li>
                                                <a href="{{ box_route('system.settings.sms_gateways') }}"
                                                    data-route="system.settings.sms_gateways"
                                                    class="{{ request()->routeIs('system.settings.sms_gateways') ? 'active' : '' }}">
                                                    SMS Gateways
                                                </a>
                                            </li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('system.settings.gdpr'))
                                            <li>
                                                <a href="{{ box_route('system.settings.gdpr') }}" data-route="system.settings.gdpr"
                                                    class="{{ request()->routeIs('system.settings.gdpr') ? 'active' : '' }}">
                                                    GDPR Cookies
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif --}}

                            {{-- Financial Settings --}}
                            @if ($hasFinancialSettingsRead)
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        class="{{ request()->routeIs('financial.settings.*') ? 'subdrop active' : '' }}">
                                        <i class="ti ti-moneybag"></i><span>Financial Settings</span><span
                                            class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        {{-- @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('financial.settings.payment_gateways'))
                                            <li>
                                                <a href="{{ box_route('financial.settings.payment_gateways') }}"
                                                    data-route="financial.settings.payment_gateways"
                                                    class="{{ request()->routeIs('financial.settings.payment_gateways') ? 'active' : '' }}">
                                                    Payment Gateways
                                                </a>
                                            </li>
                                        @endif --}}
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('financial.settings.bank_accounts'))
                                            <li>
                                                <a href="{{ box_route('financial.settings.bank_accounts') }}"
                                                    data-route="financial.settings.bank_accounts"
                                                    class="{{ request()->routeIs('financial.settings.bank_accounts') ? 'active' : '' }}">
                                                    Bank Accounts
                                                </a>
                                            </li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('financial.settings.payment_modes'))
                                            <li>
                                                <a href="{{ box_route('financial.settings.payment_modes') }}"
                                                    data-route="financial.settings.payment_modes"
                                                    class="{{ request()->routeIs('financial.settings.payment_modes') ? 'active' : '' }}">
                                                    Payment Modes
                                                </a>
                                            </li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('financial.settings.divisions'))
                                            <li>
                                                <a href="{{ box_route('financial.settings.divisions') }}"
                                                    data-route="financial.settings.divisions"
                                                    class="{{ request()->routeIs('financial.settings.divisions') ? 'active' : '' }}">
                                                    Divisions
                                                </a>
                                            </li>
                                        @endif
                                        {{-- @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('financial.settings.tax_rates'))
                                            <li>
                                                <a href="{{ box_route('financial.settings.tax_rates') }}"
                                                    data-route="financial.settings.tax_rates"
                                                    class="{{ request()->routeIs('financial.settings.tax_rates') ? 'active' : '' }}">
                                                    Tax Rates
                                                </a>
                                            </li>
                                        @endif
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('financial.settings.currencies'))
                                            <li>
                                                <a href="{{ box_route('financial.settings.currencies') }}"
                                                    data-route="financial.settings.currencies"
                                                    class="{{ request()->routeIs('financial.settings.currencies') ? 'active' : '' }}">
                                                    Currencies
                                                </a>
                                            </li>
                                        @endif --}}
                                    </ul>
                                </li>
                            @endif
                            {{-- Other Settings --}}
                            @if ($hasOtherSettingsRead)
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        class="{{ request()->routeIs('other.settings.*') ? 'subdrop active' : '' }}">
                                        <i class="ti ti-flag-cog"></i><span>Other Settings</span><span
                                            class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('other.settings.storage'))
                                            <li>
                                                <a href="{{ box_route('other.settings.storage') }}"
                                                    data-route="other.settings.storage"
                                                    class="{{ request()->routeIs('other.settings.storage') ? 'active' : '' }}">
                                                    Storage
                                                </a>
                                            </li>
                                        @endif
                                        {{-- @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('other.settings.ban_ip'))
                                            <li>
                                                <a href="{{ box_route('other.settings.ban_ip') }}"
                                                    data-route="other.settings.ban_ip"
                                                    class="{{ request()->routeIs('other.settings.ban_ip') ? 'active' : '' }}">
                                                    Ban IP Address
                                                </a>
                                            </li>
                                        @endif --}}
                                        @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('other.settings.box_settings'))
                                            <li>
                                                <a href="{{ box_route('other.settings.box_settings') }}"
                                                    data-route="other.settings.box_settings"
                                                    class="{{ request()->routeIs('other.settings.box_settings') ? 'active' : '' }}">
                                                    Box Settings
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                        </ul>
                    </li>
                @endif
                {{-- <h6 class="submenu-hdr">CRM Settings</h6>
                <ul>
                    <li>
                        <a href="contacts.html"><i class="ti ti-user-up"></i><span>Contacts</span></a>
                    </li>
                    <li>
                        <a href="companies.html"><i class="ti ti-building-community"></i><span>Companies</span></a>
                    </li>
                    <li>
                        <a href="deals.html"><i class="ti ti-medal"></i><span>Deals</span></a>
                    </li>

                    <li>
                        <a href="pipeline.html"><i
                                class="ti ti-timeline-event-exclamation"></i><span>Pipeline</span></a>
                    </li>
                    <li>
                        <a href="campaign.html"><i class="ti ti-brand-campaignmonitor"></i><span>Campaign</span></a>
                    </li>
                    <li>
                        <a href="projects.html"><i class="ti ti-atom-2"></i><span>Projects</span></a>
                    </li>
                    <li>
                        <a href="tasks.html"><i class="ti ti-list-check"></i><span>Tasks</span></a>
                    </li>
                    <li>
                        <a href="proposals.html"><i class="ti ti-file-star"></i><span>Proposals</span></a>
                    </li>
                    <li>
                        <a href="contracts.html"><i class="ti ti-file-check"></i><span>Contracts</span></a>
                    </li>
                    <li>
                        <a href="estimations.html"><i class="ti ti-file-report"></i><span>Estimations</span></a>
                    </li>
                    <li>
                        <a href="invoices.html"><i class="ti ti-file-invoice"></i><span>Invoices</span></a>
                    </li>
                    <li>
                        <a href="payments.html"><i class="ti ti-report-money"></i><span>Payments</span></a>
                    </li>
                    <li>
                        <a href="analytics.html"><i class="ti ti-chart-bar"></i><span>Analytics</span></a>
                    </li>
                    <li>
                        <a href="activities.html"><i class="ti ti-bounce-right"></i><span>Activities</span></a>
                    </li>
                </ul> --}}
                {{-- <h6 class="submenu-hdr">Layout Setting</h6>
                <ul>
                    <li>
                        <a href="layout-mini.html">
                            <i class="ti ti-layout-navbar"></i><span>Mini</span>
                        </a>
                    </li>
                    <li>
                        <a href="layout-horizontal-single.html">
                            <i class="ti ti-layout-navbar-inactive"></i><span>Horizontal Single</span>
                        </a>
                    </li>
                    <li>
                        <a href="layout-without-header.html">
                            <i class="ti ti-layout-sidebar"></i><span>Without Header</span>
                        </a>
                    </li>
                    <li>
                        <a href="layout-rtl.html">
                            <i class="ti ti-text-direction-rtl"></i><span>RTL</span>
                        </a>
                    </li>
                    <li>
                        <a href="layout-detached.html">
                            <i class="ti ti-details"></i><span>Detached</span>
                        </a>
                    </li>
                    <li>
                        <a href="layout-dark.html">
                            <i class="ti ti-details"></i><span>Dark</span>
                        </a>
                    </li>
                </ul> --}}
                </li>
                {{-- <li>
                    <h6 class="submenu-hdr">CRM Settings</h6>
                    <ul>
                        <li><a href="{{ box_route('setting.source') }}"
                                class="{{ request()->routeIs('setting.source') ? 'active' : '' }}"><i
                                    class="ti ti-artboard"></i><span>Sources</span></a></li>
                        <li><a href="{{ box_route('lost.reason') }}"
                                class="{{ request()->routeIs('lost.reason') ? 'active' : '' }}"><i
                                    class="ti ti-message-exclamation"></i><span>Lost Reason</span></a></li>
                        <li><a href="{{ box_route('contact.stage') }}"
                                class="{{ request()->routeIs('contact.stage') ? 'active' : '' }}"><i
                                    class="ti ti-steam"></i><span>Lead
                                    Status</span></a></li>
                        <li><a href="{{ box_route('industry') }}"
                                class="{{ request()->routeIs('industry') ? 'active' : '' }}"><i
                                    class="ti ti-building-factory"></i><span>Industry</span></a></li>
                        <li><a href="{{ box_route('calls') }}"
                                class="{{ request()->routeIs('calls') ? 'active' : '' }}"><i
                                    class="ti ti-phone-check"></i><span>Activity
                                    Setting</span></a></li>
                    </ul>
                </li> --}}
                {{-- <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0)">
                                <i class="fa-brands fa-whatsapp"></i><span>WhatsApp</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="whatsappchat.html">WhatsApp Chat</a></li>
                                <li><a href="campaign.html">Campaign</a></li>
                                <li><a href="whatsapp-template.html">WhatsApp Templates</a></li>

                            </ul>
                        </li>
                    </ul>
                </li> --}}
                {{-- <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0)">
                                <i class="ti ti-phone"></i><span>Auto Dialer</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ route('call.dashboard', ['box_slug' => request()->route('box_slug')]) }}">Call Dashboard</a></li>
                                <li><a href="calls.html">calls</a></li>
                                <li><a href="call-history.html">call-history</a></li>

                            </ul>
                        </li>
                    </ul>
                </li> --}}
                {{-- <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0)">
                                <i class="fa-solid fa-chart-simple"></i><span>Expense</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="designation-wise-expense.html">Designation Wise Expense</a></li>
                                <li><a href="expense.html">Expenses</a></li>
                                <!-- <li><a href="expenses.html">Expenses</a></li> -->
                                <li><a href="day-wise-expenses.html">Day wise Expenses</a></li>
                                <!-- <li><a href="expense-head.html">Expense Head</a></li> -->
                                <li><a href="standard-fare-chart.html">Standard Fare Chart</a></li>
                            </ul>
                        </li>
                    </ul>
                </li> --}}

                {{-- <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0)">
                                <i class="ti ti-user-star"></i><span>Sales & Purchase</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="sales-purchase.html">Sales Transactions</a></li>
                                <li><a href="purchase-transaction">Purchase Transaction</a></li>
                                <li><a href="quotations.html">Quotations</a></li>
                                <li><a href="all-documents">All Documents</a></li>
                                <li><a href="reminders">Reminders</a></li>
                            </ul>
                        </li>
                    </ul>
                </li> --}}
                {{-- <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0)">
                                <i class="fa-solid fa-chart-simple"></i><span>Inventory</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="inventory.html">Item Master</a></li>
                                <li><a href="inventory-approvals.html">Inventory Approvals</a></li>
                                <li><a href="stock-movement.html">Stock Movement</a></li>
                                <li><a href="barcode.html">Barcode</a></li>
                            </ul>
                        </li>
                    </ul>
                </li> --}}
                {{-- <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0)">
                                <i class="fa-solid fa-chart-simple"></i><span>Buyers & Suppliers</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="buyers.html">Buyers</a></li>
                                <li><a href="suplier.html">Suplier</a></li>
                                <li><a href="account-details.html">Account Details</a></li>
                            </ul>
                        </li>
                    </ul>
                </li> --}}
                {{-- <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0)">
                                <i class="fa-solid fa-chart-simple"></i><span>Payments</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="payments.html">Receivables</a></li>
                                <li><a href="payments.html">Payables</a></li>
                            </ul>
                        </li>
                    </ul>
                </li> --}}
                {{-- <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0)">
                                <i class="fa-solid fa-chart-simple"></i><span>Task Dashboard</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="tasks.html">Tasks</a></li>
                                <li><a href="tasks-important.html">Inportant Tasks</a></li>
                                <li><a href="tasks-completed.html">Completed Tasks</a></li>
                                <li><a href="task-reports.html">Task Reports</a></li>
                            </ul>
                        </li>
                    </ul>
                </li> --}}
                {{-- <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0)">
                                <i class="fa-solid fa-chart-simple"></i><span>Production</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="bill-materials.html">Bill of Materials</a></li>
                                <li><a href="work-orders.html">Work Orders</a></li>
                                <li><a href="production-process.html">Production Process</a></li>
                            </ul>
                        </li>
                    </ul>
                </li> --}}
                {{-- <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0)">
                                <i class="fa-solid fa-chart-simple"></i><span>Reports & Intelligence</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="reports.html">Reports</a></li>
                                <li><a href="sales-dashboard.html">Sales Dashboard</a></li>
                            </ul>
                        </li>
                    </ul>
                </li> --}}
                {{-- <li>
                    <h6 class="submenu-hdr">Field Tracking</h6>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0)">
                                <i class="fa-solid fa-users"></i><span>Employees</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="employees.html"><i class="ti ti-users"></i><span>Employees</span></a>
                                </li>
                                <li><a href="employee-details.html"><i class="ti ti-users"></i><span>Employee
                                            Detail</span></a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0)">
                                <i class="fa fa-hourglass-end" aria-hidden="true"></i><span>Tour</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="tour-list.html"><i class="ti ti-users"></i><span>Tour List</span></a>
                                </li>
                                <li><a href="tour-plan.html"><i class="ti ti-navigation-cog"></i><span>Tour
                                            Plan</span></a></li>
                                <li><a href="tour-bulk-uploading.html"><i class="ti ti-flag-question"></i><span>Tour
                                            bulk upload</span></a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0)">
                                <i class="fa fa-bullseye" aria-hidden="true"></i><span>Target</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="my-target.html"><i class="ti ti-users"></i><span>My Target</span></a>
                                </li>
                                <li><a href="target-template.html"><i class="ti ti-navigation-cog"></i><span>Target
                                            Template</span></a></li>
                                <li><a href="javascript:void(0)"><i class="ti ti-flag-question"></i><span>Team
                                            Targets</span></a>
                                </li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0)">
                                <i class="fa fa-line-chart" aria-hidden="true"></i><span>Activities</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="team-activity.html"><i class="ti ti-users"></i><span>Team
                                            Activity</span></a></li>
                                <li><a href="team-activity-detail.html"><i class="ti ti-users"></i><span>Team Activity
                                            Details</span></a></li>
                                <li><a href="tracking-history.html"><i class="ti ti-users"></i><span>Tracking
                                            History</span></a></li>
                            </ul>
                        </li>



                        <li class="submenu">
                            <a href="javascript:void(0)">
                                <i class="fa-solid fa-users"></i><span>Customers</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="customer-list.html"><i class="ti ti-users"></i><span>Customer
                                            List</span></a></li>
                            </ul>
                        </li>

                    </ul>
                </li> --}}
                <!-- <li>
      <ul>
      <li class="submenu">
         <a href="javascript:void(0);">
          <i class="ti ti-settings-cog"></i><span>General Settings</span><span class="menu-arrow"></span>
         </a>
         <ul>
          <li><a href="profile.html">Profile</a></li>
          <li><a href="security.html">Security</a></li>
          <li><a href="notifications.html">Notifications</a></li>
          <li><a href="connected-apps.html">Connected Apps</a></li>
         </ul>
        </li>
        </ul>
      </li> -->
                
<li>
                    <h6 class="submenu-hdr">Field Tracking</h6>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0)" class="{{ request()->routeIs('fieldstracking.customer-list') ? 'subdrop active' : '' }}">
                                <i class="fa-solid fa-users"></i><span>Customers</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ box_route('fieldstracking.customer-list') }}" class="{{ request()->routeIs('fieldstracking.customer-list') ? 'active' : '' }}"><i class="ti ti-users"></i><span>Customer List</span></a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0)" class="{{ request()->routeIs('fieldstracking.tracking-dashboard') ? 'subdrop active' : '' }}">
                                <i class="fa-solid fa-location-dot"></i><span>Field Tracking</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ box_route('fieldstracking.tracking-dashboard') }}" class="{{ request()->routeIs('fieldstracking.tracking-dashboard') ? 'active' : '' }}"><i class="ti ti-layout-dashboard"></i><span>Tracking Dashboard</span></a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0)" class="{{ request()->routeIs('fieldstracking.team-activity') || request()->routeIs('fieldstracking.team-activity-detail') || request()->routeIs('fieldstracking.tracking-history') ? 'subdrop active' : '' }}">
                                <i class="ti ti-chart-line"></i><span>Activities</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ box_route('fieldstracking.team-activity') }}" class="{{ request()->routeIs('fieldstracking.team-activity') ? 'active' : '' }}"><i class="ti ti-users"></i><span>Team Activity</span></a></li>
                                <li><a href="{{ box_route('fieldstracking.team-activity-detail') }}" class="{{ request()->routeIs('fieldstracking.team-activity-detail') ? 'active' : '' }}"><i class="ti ti-users"></i><span>Team Activity Details</span></a></li>
                                <li><a href="{{ box_route('fieldstracking.tracking-history') }}" class="{{ request()->routeIs('fieldstracking.tracking-history') ? 'active' : '' }}"><i class="ti ti-users"></i><span>Tracking History</span></a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0)" class="{{ request()->routeIs('fieldstracking.payment') || request()->routeIs('fieldstracking.payments') || request()->routeIs('fieldstracking.payment-admin') || request()->routeIs('fieldstracking.payment-gateways') ? 'subdrop active' : '' }}">
                                <i class="fa-solid fa-users"></i><span>Payments</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ box_route('fieldstracking.payments') }}" class="{{ request()->routeIs('fieldstracking.payments') ? 'active' : '' }}"><i class="ti ti-users"></i><span>Payments List</span></a></li>
                                <li><a href="{{ box_route('fieldstracking.payment') }}" class="{{ request()->routeIs('fieldstracking.payment') ? 'active' : '' }}"><i class="ti ti-credit-card"></i><span>Payment</span></a></li>
                                <li><a href="{{ box_route('fieldstracking.payment-admin') }}" class="{{ request()->routeIs('fieldstracking.payment-admin') ? 'active' : '' }}"><i class="ti ti-shield-lock"></i><span>Payment Admin</span></a></li>
                                <li><a href="{{ box_route('fieldstracking.payment-gateways') }}" class="{{ request()->routeIs('fieldstracking.payment-gateways') ? 'active' : '' }}"><i class="ti ti-building-bank"></i><span>Payment Gateways</span></a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0)" class="{{ request()->routeIs('fieldstracking.tour-list') || request()->routeIs('fieldstracking.tour-plan') || request()->routeIs('fieldstracking.tour-bulk-uploading') ? 'subdrop active' : '' }}">
                                <i class="fa-solid fa-hourglass"></i><span>Tour</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ box_route('fieldstracking.tour-list') }}" class="{{ request()->routeIs('fieldstracking.tour-list') ? 'active' : '' }}"><i class="ti ti-users"></i><span>Tour List</span></a></li>
                                <li><a href="{{ box_route('fieldstracking.tour-plan') }}" class="{{ request()->routeIs('fieldstracking.tour-plan') ? 'active' : '' }}"><i class="ti ti-triangle"></i><span>Tour Plan</span></a></li>
                                <li><a href="{{ box_route('fieldstracking.tour-bulk-uploading') }}" class="{{ request()->routeIs('fieldstracking.tour-bulk-uploading') ? 'active' : '' }}"><i class="ti ti-flag"></i><span>Tour bulk upload</span></a></li>
                            </ul>
                        </li>
                        @if ($hasFinanceRead)
                        <li class="submenu">
                            <a href="javascript:void(0)" class="{{ request()->routeIs('finance.*') ? 'subdrop active' : '' }}">
                                <i class="ti ti-wallet"></i><span>Finance & Expenses</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if ($hasFinanceDashboardRead)
                                <li>
                                    <a href="{{ route('finance.dashboard', ['box_slug' => session('box_slug', request()->route('box_slug'))]) }}" class="{{ request()->routeIs('finance.dashboard') ? 'active' : '' }}">
                                        <i class="ti ti-layout-dashboard"></i><span>Dashboard</span>
                                    </a>
                                </li>
                                @endif
                                @if ($hasFinanceSettingsRead)
                                <li>
                                    <a href="{{ route('finance.settings.index', ['box_slug' => session('box_slug', request()->route('box_slug'))]) }}" class="{{ request()->routeIs('finance.settings.*') ? 'active' : '' }}">
                                        <i class="ti ti-settings"></i><span>Settings</span>
                                    </a>
                                </li>
                                @endif
                                @if ($hasFinanceExpensesRead)
                                <li>
                                    <a href="{{ route('finance.expenses.index', ['box_slug' => session('box_slug', request()->route('box_slug'))]) }}" class="{{ request()->routeIs('finance.expenses.*') ? 'active' : '' }}">
                                        <i class="ti ti-receipt"></i><span>My Expenses</span>
                                    </a>
                                </li>
                                @endif
                                @if ($hasFinanceApprovalsRead)
                                <li>
                                    <a href="{{ route('finance.approvals.index', ['box_slug' => session('box_slug', request()->route('box_slug'))]) }}" class="{{ request()->routeIs('finance.approvals.*') ? 'active' : '' }}">
                                        <i class="ti ti-check"></i><span>Approvals</span>
                                    </a>
                                </li>
                                @endif
                                @if ($hasFinanceReimbursementsRead)
                                <li>
                                    <a href="{{ route('finance.reimbursements.index', ['box_slug' => session('box_slug', request()->route('box_slug'))]) }}" class="{{ request()->routeIs('finance.reimbursements.*') ? 'active' : '' }}">
                                        <i class="ti ti-cash"></i><span>Reimbursements</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                    </ul>
                </li>

                @if ($hasUserManagementRead)
                    <li>
                        <h6 class="submenu-hdr">User Management</h6>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);"
                                    class="{{ (request()->routeIs('manage_users.organizational_structure') || request()->routeIs('manage.users') || request()->routeIs('manage_users.roles_permissions') || request()->routeIs('manage_users.departments') || request()->routeIs('manage_users.designations') || request()->routeIs('manage_users.todo') || request()->routeIs('manage_users.projects') || request()->routeIs('manage_users.projects.contacts') || request()->routeIs('settings.project_types') || request()->routeIs('settings.project_categories') || request()->routeIs('manage_users.deals') || request()->routeIs('manage_users.deal_settings.*') || request()->routeIs('manage_users.permissions') || request()->routeIs('manage_users.employee_list') || request()->routeIs('employee.grid') || request()->routeIs('manage_users.employee_type') || request()->routeIs('manage_users.work_mode')) ? 'subdrop active' : '' }}">
                                    <i class="ti ti-users"></i><span>Manage Users</span><span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('manage_users.organizational_structure'))
                                        <li><a href="{{ box_route('manage_users.organizational_structure') }}"
                                                class="{{ request()->routeIs('manage_users.organizational_structure') ? 'active' : '' }}">Organization
                                                Structure</a></li>
                                    @endif

                                    {{-- @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('manage.users'))
                                        <li><a href="{{ box_route('manage.users') }}"
                                                class="{{ request()->routeIs('manage.users') ? 'active' : '' }}">Manage
                                                Users</a>
                                        </li>
                                    @endif --}}

                                    {{-- Employee List --}}
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('manage_users.employee_list'))
                                        <li>
                                            <a href="{{ box_route('manage_users.employee_list') }}" data-route="manage_users.employee_list"
                                                class="{{ request()->routeIs('manage_users.employee_list') ? 'active' : '' }}">
                                                Employee List
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Employee Grid --}}
                                    {{-- @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('employee.grid'))
                                        <li>
                                            <a href="{{ box_route('employee.grid') }}" data-route="employee.grid"
                                                class="{{ request()->routeIs('employee.grid') ? 'active' : '' }}">
                                                Employee Grid
                                            </a>
                                        </li>
                                    @endif --}}

                                    {{-- Employee Type Settings --}}
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('manage_users.employee_type'))
                                        <li><a href="{{ box_route('manage_users.employee_type') }}"
                                                data-route="manage_users.employee_type"
                                                class="{{ request()->routeIs('manage_users.employee_type') ? 'active' : '' }}">Employee
                                                Type Settings</a></li>
                                    @endif

                                    {{-- Work Mode Add --}}
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('manage_users.work_mode'))
                                        <li><a href="{{ box_route('manage_users.work_mode') }}"
                                                data-route="manage_users.work_mode"
                                                class="{{ request()->routeIs('manage_users.work_mode') ? 'active' : '' }}">Work
                                                Mode Add</a></li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('manage_users.roles_permissions'))
                                        <li>
                                            <a href="{{ box_route('manage_users.roles_permissions') }}"
                                                class="{{ request()->routeIs('manage_users.roles_permissions') ? 'active' : '' }}">
                                                Roles & Permissions
                                            </a>
                                        </li>
                                    @endif

                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('manage_users.permissions'))
                                        <li>
                                            <a href="{{ box_route('manage_users.permissions') }}"
                                                class="{{ request()->routeIs('manage_users.permissions') ? 'active' : '' }}">
                                                Permissions
                                            </a>
                                        </li>
                                    @endif

                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('manage_users.departments'))
                                        <li><a href="{{ box_route('manage_users.departments') }}" class="{{ request()->routeIs('manage_users.departments') ? 'active' : '' }}">Departments</a></li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('manage_users.designations'))
                                        <li><a href="{{ box_route('manage_users.designations') }}" class="{{ request()->routeIs('manage_users.designations') ? 'active' : '' }}">Designations</a></li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('manage_users.todo'))
                                        <li><a href="{{ box_route('manage_users.todo') }}" class="{{ request()->routeIs('manage_users.todo') ? 'active' : '' }}">To-Do</a></li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('manage_users.projects'))
                                        <li><a href="{{ box_route('manage_users.projects') }}" class="{{ request()->routeIs('manage_users.projects') ? 'active' : '' }}">Projects</a></li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('manage_users.projects.contacts'))
                                        <li><a href="{{ box_route('manage_users.projects.contacts') }}" class="{{ request()->routeIs('manage_users.projects.contacts') ? 'active' : '' }}">Project Contacts</a></li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.project_types'))
                                        <li><a href="{{ box_route('settings.project_types') }}" class="{{ request()->routeIs('settings.project_types') ? 'active' : '' }}">Project Types</a></li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('settings.project_categories'))
                                        <li><a href="{{ box_route('settings.project_categories') }}" class="{{ request()->routeIs('settings.project_categories') ? 'active' : '' }}">Project Categories</a></li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('manage_users.deals'))
                                        <li><a href="{{ box_route('manage_users.deals') }}" class="{{ request()->routeIs('manage_users.deals') ? 'active' : '' }}">Deals</a></li>
                                    @endif
                                    @if ($isPrivilegedUser || PermissionHelper::canAccessRoute('manage_users.deal_settings.index'))
                                        <li><a href="{{ box_route('manage_users.deal_settings.index') }}" class="{{ request()->routeIs('manage_users.deal_settings.*') ? 'active' : '' }}">Deal Settings</a></li>
                                    @endif
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="javascript:void(0)" class="{{ request()->routeIs('fieldstracking.my-target') || request()->routeIs('fieldstracking.target-template') || request()->routeIs('fieldstracking.team-targets') || request()->routeIs('fieldstracking.employee-dashboard') || request()->routeIs('fieldstracking.performance-dashboard') ? 'subdrop active' : '' }}">
                                    <i class="ti ti-target"></i><span>Target</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ box_route('fieldstracking.my-target') }}" class="{{ request()->routeIs('fieldstracking.my-target') ? 'active' : '' }}"><i class="ti ti-users"></i><span>My Target</span></a></li>
                                    <li><a href="{{ box_route('fieldstracking.target-template') }}" class="{{ request()->routeIs('fieldstracking.target-template') ? 'active' : '' }}"><i class="ti ti-triangle"></i><span>Target Template</span></a></li>
                                    <li><a href="{{ box_route('fieldstracking.team-targets') }}" class="{{ request()->routeIs('fieldstracking.team-targets') ? 'active' : '' }}"><i class="ti ti-flag"></i><span>Team Targets</span></a></li>
                                    <li><a href="{{ box_route('fieldstracking.employee-dashboard') }}" class="{{ request()->routeIs('fieldstracking.employee-dashboard') ? 'active' : '' }}"><i class="ti ti-users"></i><span>Employee Dashboard</span></a></li>
                                    <li><a href="{{ box_route('fieldstracking.performance-dashboard') }}" class="{{ request()->routeIs('fieldstracking.performance-dashboard') ? 'active' : '' }}"><i class="ti ti-chart-bar"></i><span>Performance Dashboard</span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif
                <hr style="border-width:5px; border-color:red; opacity:1;">


                <!-- <li>
       <h6 class="submenu-hdr">CRM</h6>
       <ul>
        <li>
         <a href="contacts.html"><i class="ti ti-user-up"></i><span>Contacts</span></a>
        </li>
        <li>
         <a href="companies.html"><i class="ti ti-building-community"></i><span>Companies</span></a>
        </li>
        <li>
         <a href="deals.html"><i class="ti ti-medal"></i><span>Deals</span></a>
        </li>
        <li>
         <a href="leads.html"><i class="ti ti-chart-arcs"></i><span>Leads</span></a>
        </li>
        <li>
         <a href="pipeline.html"><i class="ti ti-timeline-event-exclamation"></i><span>Pipeline</span></a>
        </li>
        <li>
         <a href="campaign.html"><i class="ti ti-brand-campaignmonitor"></i><span>Campaign</span></a>
        </li>
        <li>
         <a href="projects.html"><i class="ti ti-atom-2"></i><span>Projects</span></a>
        </li>
        <li>
         <a href="tasks.html"><i class="ti ti-list-check"></i><span>Tasks</span></a>
        </li>
        <li>
         <a href="proposals.html"><i class="ti ti-file-star"></i><span>Proposals</span></a>
        </li>
        <li>
         <a href="contracts.html"><i class="ti ti-file-check"></i><span>Contracts</span></a>
        </li>
        <li>
         <a href="estimations.html"><i class="ti ti-file-report"></i><span>Estimations</span></a>
        </li>
        <li>
         <a href="invoices.html"><i class="ti ti-file-invoice"></i><span>Invoices</span></a>
        </li>
        <li>
         <a href="payments.html"><i class="ti ti-report-money"></i><span>Payments</span></a>
        </li>
        <li>
         <a href="analytics.html"><i class="ti ti-chart-bar"></i><span>Analytics</span></a>
        </li>
        <li>
         <a href="activities.html"><i class="ti ti-bounce-right"></i><span>Activities</span></a>
        </li>
       </ul>
      </li> -->


                {{-- <li>
                    <h6 class="submenu-hdr">User Management</h6>
                    <ul>

                        <li><a href="manage-users.html"><i class="ti ti-users"></i><span>Manage Users</span></a></li>
                        <li><a href="roles-permissions.html"><i class="ti ti-navigation-cog"></i><span>Roles &
                                    Permissions</span></a></li>
                        <li><a href="delete-request.html"><i class="ti ti-flag-question"></i><span>Delete
                                    Request</span></a></li>
                        <li><a href="{{ box_route('manage_users.departments') }}"><i class="ti ti-users"></i><span>Departments</span></a></li>
                        <li><a href="{{ box_route('manage_users.designations') }}"><i class="ti ti-users"></i><span>Designations</span></a></li>
                    </ul>
                </li>
                <li>
                    <h6 class="submenu-hdr">Membership</h6>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-file-invoice"></i><span>Membership</span><span
                                    class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="membership-plans.html">Membership Plans</a></li>
                                <li><a href="membership-addons.html">Membership Addons</a></li>
                                <li><a href="membership-transactions.html">Transactions</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li>
                    <h6 class="submenu-hdr">Content</h6>
                    <ul>
                        <li><a href="pages.html"><i class="ti ti-page-break"></i><span>Pages</span></a></li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-map-pin-pin"></i><span>Location</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="countries.html">Countries</a></li>
                                <li><a href="states.html">States</a></li>
                                <li><a href="cities.html">Cities</a></li>
                            </ul>
                        </li>
                        <li><a href="testimonials.html"><i class="ti ti-quote"></i><span>Testimonials</span></a></li>
                        <li><a href="faq.html"><i class="ti ti-question-mark"></i><span>FAQ</span></a></li>
                    </ul>
                </li>
                <li>
                    <h6 class="submenu-hdr">Support</h6>
                    <ul>
                        <li><a href="contact-messages.html"><i class="ti ti-page-break"></i><span>Contact
                                    Messages</span></a></li>
                        <li><a href="tickets.html"><i class="ti ti-ticket"></i><span>Tickets</span></a></li>
                    </ul>
                </li>
                <li>
                    <h6 class="submenu-hdr">Settings</h6>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-settings-cog"></i><span>General Settings</span><span
                                    class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="profile.html">Profile</a></li>
                                <li><a href="security.html">Security</a></li>
                                <li><a href="notifications.html">Notifications</a></li>
                                <li><a href="connected-apps.html">Connected Apps</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-world-cog"></i><span>Website Settings</span><span
                                    class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="company-settings.html">Company Settings</a></li>
                                <li><a href="localization.html">Localization</a></li>
                                <li><a href="prefixes.html">Prefixes</a></li>
                                <li><a href="preference.html">Preference</a></li>
                                <li><a href="appearance.html">Appearance</a></li>
                                <li><a href="language.html">Language</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-apps"></i><span>App Settings</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="invoice-settings.html">Invoice Settings</a></li>
                                <li><a href="printers.html">Printers</a></li>
                                <li><a href="custom-fields.html">Custom Fields</a></li>
                                <li><a href="{{ box_route('app.settings.leads.leads_sub_status') }}">Leads Sub
                                        Status</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-device-laptop"></i><span>System Settings</span><span
                                    class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="email-settings.html">Email Settings</a></li>
                                <li><a href="sms-gateways.html">SMS Gateways</a></li>
                                <li><a href="gdpr-cookies.html">GDPR Cookies</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-moneybag"></i><span>Financial Settings</span><span
                                    class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="payment-gateways.html">Payment Gateways</a></li>
                                <li><a href="bank-accounts.html">Bank Accounts</a></li>
                                <li><a href="tax-rates.html">Tax Rates</a></li>
                                <li><a href="currencies.html">Currencies</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-flag-cog"></i><span>Other Settings</span><span
                                    class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="storage.html">Storage</a></li>
                                <li><a href="ban-ip-address.html">Ban IP Address</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li>
                    <h6 class="submenu-hdr">Pages</h6>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-lock-square-rounded"></i><span>Authentication</span><span
                                    class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="login.html">Login</a></li>
                                <li><a href="register.html">Register</a></li>
                                <li><a href="forgot-password.html">Forgot Password</a></li>
                                <li><a href="reset-password.html">Reset Password</a></li>
                                <li><a href="email-verification.html">Email Verification</a></li>
                                <li><a href="two-step-verification.html">2 Step Verification</a></li>
                                <li><a href="lock-screen.html">Lock Screen</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-error-404"></i><span>Error Pages</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="error-404.html">404 Error</a></li>
                                <li><a href="error-500.html">500 Error</a></li>
                            </ul>
                        </li>
                        <li><a href="blank-page.html"><i class="ti ti-apps"></i><span>Blank Page</span></a></li>
                        <li><a href="coming-soon.html"><i class="ti ti-device-laptop"></i><span>Coming Soon</span></a>
                        </li>
                        <li><a href="under-maintenance.html"><i class="ti ti-moneybag"></i><span>Under
                                    Maintenance</span></a></li>
                    </ul>
                </li>
                <li>
                    <h6 class="submenu-hdr">UI Interface</h6>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-adjustments-check"></i><span>Base UI</span><span
                                    class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="ui-alerts.html">Alerts</a></li>
                                <li><a href="ui-accordion.html">Accordion</a></li>
                                <li><a href="ui-avatar.html">Avatar</a></li>
                                <li><a href="ui-badges.html">Badges</a></li>
                                <li><a href="ui-borders.html">Border</a></li>
                                <li><a href="ui-buttons.html">Buttons</a></li>
                                <li><a href="ui-buttons-group.html">Button Group</a></li>
                                <li><a href="ui-breadcrumb.html">Breadcrumb</a></li>
                                <li><a href="ui-cards.html">Card</a></li>
                                <li><a href="ui-carousel.html">Carousel</a></li>
                                <li><a href="ui-colors.html">Colors</a></li>
                                <li><a href="ui-dropdowns.html">Dropdowns</a></li>
                                <li><a href="ui-grid.html">Grid</a></li>
                                <li><a href="ui-images.html">Images</a></li>
                                <li><a href="ui-lightbox.html">Lightbox</a></li>
                                <li><a href="ui-media.html">Media</a></li>
                                <li><a href="ui-modals.html">Modals</a></li>
                                <li><a href="ui-offcanvas.html">Offcanvas</a></li>
                                <li><a href="ui-pagination.html">Pagination</a></li>
                                <li><a href="ui-popovers.html">Popovers</a></li>
                                <li><a href="ui-progress.html">Progress</a></li>
                                <li><a href="ui-placeholders.html">Placeholders</a></li>
                                <li><a href="ui-rangeslider.html">Range Slider</a></li>
                                <li><a href="ui-spinner.html">Spinner</a></li>
                                <li><a href="ui-sweetalerts.html">Sweet Alerts</a></li>
                                <li><a href="ui-nav-tabs.html">Tabs</a></li>
                                <li><a href="ui-toasts.html">Toasts</a></li>
                                <li><a href="ui-tooltips.html">Tooltips</a></li>
                                <li><a href="ui-typography.html">Typography</a></li>
                                <li><a href="ui-video.html">Video</a></li>
                                <li><a href="ui-sortable.html">Sortable</a></li>
                                <li><a href="ui-swiperjs.html">Swiperjs</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-box-align-bottom"></i><span>Advanced UI</span><span
                                    class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="ui-ribbon.html">Ribbon</a></li>
                                <li><a href="ui-clipboard.html">Clipboard</a></li>
                                <li><a href="ui-drag-drop.html">Drag & Drop</a></li>
                                <li><a href="ui-rangeslider.html">Range Slider</a></li>
                                <li><a href="ui-rating.html">Rating</a></li>
                                <li><a href="ui-text-editor.html">Text Editor</a></li>
                                <li><a href="ui-counter.html">Counter</a></li>
                                <li><a href="ui-scrollbar.html">Scrollbar</a></li>
                                <li><a href="ui-stickynote.html">Sticky Note</a></li>
                                <li><a href="ui-timeline.html">Timeline</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><i class="ti ti-chart-donut-2"></i>
                                <span>Charts</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="chart-apex.html">Apex Charts</a></li>
                                <li><a href="chart-c3.html">Chart C3</a></li>
                                <li><a href="chart-js.html">Chart Js</a></li>
                                <li><a href="chart-morris.html">Morris Charts</a></li>
                                <li><a href="chart-flot.html">Flot Charts</a></li>
                                <li><a href="chart-peity.html">Peity Charts</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><i class="ti ti-icons"></i>
                                <span>Icons</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="icon-fontawesome.html">Fontawesome Icons</a></li>
                                <li><a href="icon-feather.html">Feather Icons</a></li>
                                <li><a href="icon-ionic.html">Ionic Icons</a></li>
                                <li><a href="icon-material.html">Material Icons</a></li>
                                <li><a href="icon-pe7.html">Pe7 Icons</a></li>
                                <li><a href="icon-simpleline.html">Simpleline Icons</a></li>
                                <li><a href="icon-themify.html">Themify Icons</a></li>
                                <li><a href="icon-weather.html">Weather Icons</a></li>
                                <li><a href="icon-typicon.html">Typicon Icons</a></li>
                                <li><a href="icon-flag.html">Flag Icons</a></li>
                                <li><a href="icon-tabler.html">Tabler Icons</a></li>
                                <li><a href="icon-bootstrap.html">Bootstrap Icons</a></li>
                                <li><a href="icon-remix.html">Remix Icons</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="ti ti-forms"></i><span>Forms</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);">Form Elements<span
                                            class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="form-basic-inputs.html">Basic Inputs</a></li>
                                        <li><a href="form-checkbox-radios.html">Checkbox & Radios</a></li>
                                        <li><a href="form-input-groups.html">Input Groups</a></li>
                                        <li><a href="form-grid-gutters.html">Grid & Gutters</a></li>
                                        <li><a href="form-select.html">Form Select</a></li>
                                        <li><a href="form-mask.html">Input Masks</a></li>
                                        <li><a href="form-fileupload.html">File Uploads</a></li>
                                    </ul>
                                </li>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);">Layouts<span
                                            class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="form-horizontal.html">Horizontal Form</a></li>
                                        <li><a href="form-vertical.html">Vertical Form</a></li>
                                        <li><a href="form-floating-labels.html">Floating Labels</a></li>
                                    </ul>
                                </li>
                                <li><a href="form-validation.html">Form Validation</a></li>
                                <li><a href="form-select2.html">Select2</a></li>
                                <li><a href="form-wizard.html">Form Wizard</a></li>
                                <li><a href="form-pickers.html">Form Picker</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><i class="ti ti-table"></i><span>Tables</span><span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="tables-basic.html">Basic Tables </a></li>
                                <li><a href="data-tables.html">Data Table </a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><i class="ti ti-map"></i><span>Maps</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li>
                                    <a href="maps-vector.html">Vector</a>
                                </li>
                                <li>
                                    <a href="maps-leaflet.html">Leaflet</a>
                                </li>
                            </ul>
                        </li>


                        <li class="submenu">
                            <a href="javascript:void(0);"><i
                                    class="ti ti-brand-airtable"></i><span>Application</span><span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="chat.html">Chat</a></li>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);">Call<span
                                            class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="video-call.html">Video Call</a></li>
                                        <li><a href="audio-call.html">Audio Call</a></li>
                                        <li><a href="call-history.html">Call History</a></li>
                                    </ul>
                                </li>
                                <li><a href="calendar.html">Calendar</a></li>
                                <li><a href="email.html">Email</a></li>
                                <li><a href="todo.html">To Do</a></li>
                                <li><a href="notes.html">Notes</a></li>
                                <li><a href="file-manager.html">File Manager</a></li>
                                <li><a href="social-feed.html">Social Feed</a></li>
                                <li><a href="kanban-view.html">Kanban</a></li>
                                <li><a href="invoice.html">Invoices</a></li>
                            </ul>
                        </li>



                    </ul>
                </li>
                <li>
                    <h6 class="submenu-hdr">Help</h6>
                    <ul>
                        <li><a href="javascript:void(0);"><i
                                    class="ti ti-file-type-doc"></i><span>Documentation</span></a></li>
                        <li><a href="javascript:void(0);"><i class="ti ti-arrow-capsule"></i><span>Changelog
                                    v2.2.2</span></a></li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><i class="ti ti-brand-databricks"></i><span>Multi
                                    Level</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="javascript:void(0);">Level 1.1</a></li>
                                <li class="submenu submenu-two"><a href="javascript:void(0);">Level 1.2<span
                                            class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="javascript:void(0);">Level 2.1</a></li>
                                        <li class="submenu submenu-two submenu-three"><a
                                                href="javascript:void(0);">Level 2.2<span
                                                    class="menu-arrow inside-submenu inside-submenu-two"></span></a>
                                            <ul>
                                                <li><a href="javascript:void(0);">Level 3.1</a></li>
                                                <li><a href="javascript:void(0);">Level 3.2</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li> --}}
            </ul>
        </div>
    </div>
</div>


@push('scripts')
   <?php /* <script>
        $(document).ready(function () {
            // Hide all submenus initially - keep closed by default
            $('#sidebar-menu .submenu > ul').hide();
            $('#sidebar-menu .submenu').removeClass('subdrop');
            $('#sidebar-menu .submenu > a').removeClass('active');

            // Toggle submenu on click
            $(document).on('click', '#sidebar-menu .submenu > a', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const $link = $(this);
                const $submenu = $link.next('ul');
                const $parent = $link.parent('.submenu');
                const $allSubmenus = $('#sidebar-menu .submenu');

                // Check if this submenu is currently open
                const isOpen = $parent.hasClass('subdrop') && $submenu.is(':visible');

                // Close all other submenus first
                $allSubmenus.not($parent).removeClass('subdrop').find('> ul').slideUp(300);
                $allSubmenus.not($parent).find('> a').removeClass('active');

                // Toggle the clicked submenu (open if closed, close if open)
                if (isOpen) {
                    // Close it
                    $submenu.slideUp(300);
                    $parent.removeClass('subdrop');
                    $link.removeClass('active');
                } else {
                    // Open it
                    $submenu.slideDown(300);
                    $parent.addClass('subdrop');
                    $link.addClass('active');
                }
            });

            // Only auto-open if current page link is inside (keep structure but don't auto-open all)
            $('#sidebar-menu .submenu > ul').each(function () {
                if ($(this).find('a.active').length) {
                    $(this).show();
                    $(this).parent('.submenu').addClass('subdrop');
                    $(this).prev('a').addClass('active');
                }
            });
        });
    </script> */ ?>



<script>
document.addEventListener("DOMContentLoaded", function () {
    const currentPage = window.location.pathname.split("/").pop();

    document.querySelectorAll("li.submenu-two ul li a").forEach(function (link) {
        const linkPage = link.getAttribute("href").split("/").pop();

        if (currentPage === linkPage) {
            link.closest("li.submenu-two").classList.add("startgo");
        }
    });
});
</script>

@endpush