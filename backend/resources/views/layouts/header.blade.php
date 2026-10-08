<div class="header">

    @php
        use App\Models\Box;
        use App\Models\BoxUser;
        use Illuminate\Support\Facades\Auth;

        $dashboardRoute = url('/');
        if (Auth::check()) {
            $user = Auth::user();
            
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
        }
    @endphp

    <!-- Logo -->
    <div class="header-left active">
        <a href="{{ $dashboardRoute }}" class="logo logo-normal">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo">
            <img src="{{ asset('assets/img/logo-white.png') }}" class="white-logo" alt="Logo">
        </a>
        <a href="{{ $dashboardRoute }}" class="logo-small">
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
        <ul class="nav user-menu">

            <!-- Search -->
            <li class="nav-item nav-search-inputs me-auto">
                <div class="top-nav-search">
                    <a href="javascript:void(0);" class="responsive-search">
                        <i class="fa fa-search"></i>
                    </a>
                    <form action="#" class="dropdown">
                        <div class="searchinputs" id="dropdownMenuClickable">
                            <input type="text" placeholder="Search">
                            <div class="search-addon">
                                <button type="submit"><i class="ti ti-command"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </li>
            <!-- /Search -->

            <!-- Horizontal Single -->
            <li>
                <div class="sidebar sidebar-horizontal" id="horizontal-single">
                    <div class="sidebar-menu">
                        <div class="main-menu">
                            <ul class="nav-menu">
                                <li class="menu-title">
                                    <span>Main</span>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="active">
                                        <i class="ti ti-layout-2"></i><span>Dashboard</span><span
                                            class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="index.html" class="active">Deals Dashboard</a></li>
                                        <li><a href="leads-dashboard.html">Leads Dashboard</a></li>
                                        <li><a href="project-dashboard.html">Project Dashboard</a></li>
                                        <li class="submenu">
                                            <a href="javascript:void(0)">
                                                <i class="ti ti-user-star"></i><span>Super Admin</span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <ul>
                                                <li><a href="{{ box_route('superadmin.dashboard') }}">Dashboard</a></li>
                                                <li><a href="{{ box_route('superadmin.company') }}">Companies</a></li>
                                                <li><a href="{{ box_route('superadmin.subscription') }}">Subscriptions</a></li>
                                                <li><a href="{{ box_route('superadmin.packages') }}">Packages</a></li>
                                                <li><a href="{{ box_route('superadmin.package-features.page') }}">Package Features</a></li>
                                                <li><a href="{{ box_route('superadmin.faqs') }}">FAQs</a></li>
                                                <li><a href="{{ box_route('superadmin.master_industry') }}">Industry Register</a></li>
                                                <li><a href="{{ box_route('employee.leads_type') }}">Domain</a></li>
                                                <li><a href="{{ box_route('superadmin.purchase_transaction') }}">Purchase Transaction</a></li>
                                                <li><a href="{{ box_route('superadmin.realtime_logs') }}">Realtime Logs</a></li>
                                                <li><a href="{{ box_route('superadmin.vendors.index') }}">Vendors</a></li>
                                                <li><a href="{{ box_route('superadmin.vendors.dashboard') }}">Vendor Dashboard</a></li>
                                                <li><a href="{{ box_route('superadmin.vendors.transaction') }}">Vendor Transactions</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"><i
                                            class="ti ti-brand-airtable"></i><span>Application</span>
                                        <span class="menu-arrow"></span></a>
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
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="ti ti-layout-2"></i><span>layouts</span><span
                                            class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="layout-mini.html">Mini</a></li>
                                        <li><a href="layout-horizontal-single.html">Horizontal Single</a></li>
                                        <li><a href="layout-without-header.html">Without Header</a></li>
                                        <li><a href="layout-rtl.html">RTL</a></li>
                                        <li><a href="layout-detached.html">Detached</a></li>
                                        <li><a href="layout-dark.html">Dark</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="#">
                                        <i class="ti ti-ti ti-user-up"></i><span>Crm</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li>
                                            <a href="contacts.html"><span>Contacts</span></a>
                                        </li>
                                        <li>
                                            <a href="companies.html"><span>Companies</span></a>
                                        </li>
                                        <li>
                                            <a href="{{ box_route('deals.dashboard') }}"><span>Deals</span></a>
                                        </li>
                                        <li>
                                            <a href="{{ box_route('leads.details') }}"><span>Leads</span></a>
                                        </li>
                                        <li>
                                            <a href="pipeline.html"><span>Pipeline</span></a>
                                        </li>
                                        <li>
                                            <a href="{{ box_route('whatsapp.campaign') }}"><span>Campaign</span></a>
                                        </li>
                                        <li>
                                            <a href="{{ box_route('project.dashboard') }}"><span>Projects</span></a>
                                        </li>
                                        <li>
                                            <a href="{{ box_route('tasks.index') }}"><span>Tasks</span></a>
                                        </li>
                                        <li>
                                            <a href="proposals.html"><span>Proposals</span></a>
                                        </li>
                                        <li>
                                            <a href="contracts.html"><span>Contracts</span></a>
                                        </li>
                                        <li>
                                            <a href="estimations.html"><span>Estimations</span></a>
                                        </li>
                                        <li>
                                            <a href="invoices.html"><span>Invoices</span></a>
                                        </li>
                                        <li>
                                            <a href="payments.html"><span>Payments</span></a>
                                        </li>
                                        <li>
                                            <a href="analytics.html"><span>Analytics</span></a>
                                        </li>
                                        <li>
                                            <a href="{{ box_route('activities.get') }}"><span>Activities</span></a>
                                        </li>
                                        <li class="submenu">
                                            <a href="#">
                                                <span>Crm Settings</span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <ul>
                                                <li><a
                                                        href="{{ box_route('app.settings.source') }}"><span>Sources</span></a>
                                                </li>
                                                <li><a href="{{ box_route('app.settings.lost.reason') }}"><span>Lost
                                                            Reason</span></a></li>
                                                <li><a href="{{ box_route('app.settings.contact.stage') }}"><span>Contact
                                                            Stage</span></a></li>
                                                <li><a href="{{ box_route('app.settings.industry') }}"><span>Industry</span></a>
                                                </li>
                                                <li><a href="{{ box_route('app.settings.calls') }}"><span>Calls</span></a></li>
                                            </ul>

                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0)" id="reports-link">
    <i class="ti ti-file-invoice"></i><span>Reports</span>
    <span class="menu-arrow"></span>
</a>
                                    <ul>
                                        <li><a href="lead-reports.html">Lead Reports</a></li>
                                        <li><a href="deal-reports.html">Deal Reports</a></li>
                                        <li><a href="contact-reports.html">Contact Reports</a></li>
                                        <li><a href="company-reports.html">Company Reports</a></li>
                                        <li><a href="project-reports.html">Project Reports</a></li>
                                        <li><a href="task-reports.html">Task Reports</a></li>
                                    </ul>
                                </li>

                                <li class="submenu">
                                    <a href="#">
                                        <i class="ti ti-page-break"></i><span>Pages</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="manage-users.html"><span>Manage Users</span></a></li>
                                        <li><a href="roles-permissions.html"><span>Roles & Permissions</span></a></li>
                                        <li><a href="delete-request.html"><span>Delete Request</span></a></li>
                                        <li class="submenu">
                                            <a href="javascript:void(0);">
                                                <span>Membership</span><span class="menu-arrow"></span>
                                            </a>
                                            <ul>
                                                <li><a href="membership-plans.html">Membership Plans</a></li>
                                                <li><a href="membership-addons.html">Membership Addons</a></li>
                                                <li><a href="membership-transactions.html">Transactions</a></li>
                                            </ul>
                                        </li>
                                        <li class="submenu">
                                            <a href="javascript:void(0);">
                                                <span>Location</span><span class="menu-arrow"></span>
                                            </a>
                                            <ul>
                                                <li><a href="countries.html">Countries</a></li>
                                                <li><a href="states.html">States</a></li>
                                                <li><a href="cities.html">Cities</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="testimonials.html"><span>Testimonials</span></a></li>
                                        <li><a href="faq.html"><span>FAQ</span></a></li>
                                        <li><a href="contact-messages.html"><span>Contact Messages</span></a></li>
                                        <li><a href="tickets.html"><span>Tickets</span></a></li>
                                        <li class="submenu">
                                            <a href="javascript:void(0);">
                                                <span>Authentication</span><span class="menu-arrow"></span>
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
                                                <span>Error Pages</span><span class="menu-arrow"></span>
                                            </a>
                                            <ul>
                                                <li><a href="error-404.html">404 Error</a></li>
                                                <li><a href="error-500.html">500 Error</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="blank-page.html"><span>Blank Page</span></a></li>
                                        <li><a href="coming-soon.html"><span>Coming Soon</span></a></li>
                                        <li><a href="under-maintenance.html"><span>Under Maintenance</span></a></li>
                                        <li class="submenu">
                                            <a href="javascript:void(0);">
                                                <span>Base UI</span><span class="menu-arrow"></span>
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
                                                <span>Advanced UI</span><span class="menu-arrow"></span>
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
                                            <a href="javascript:void(0);">
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
                                            <a href="javascript:void(0);">
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
                                                <span>Forms</span><span class="menu-arrow"></span>
                                            </a>
                                            <ul>
                                                <li class="submenu submenu-two">
                                                    <a href="javascript:void(0);">Form Elements<span
                                                            class="menu-arrow inside-submenu"></span></a>
                                                    <ul>
                                                        <li><a href="form-basic-inputs.html">Basic Inputs</a></li>
                                                        <li><a href="form-checkbox-radios.html">Checkbox & Radios</a>
                                                        </li>
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
                                                        <li><a href="form-floating-labels.html">Floating Labels</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li><a href="form-validation.html">Form Validation</a></li>
                                                <li><a href="form-select2.html">Select2</a></li>
                                                <li><a href="form-wizard.html">Form Wizard</a></li>
                                                <li><a href="form-pickers.html">Form Picker</a></li>
                                            </ul>
                                        </li>
                                        <li class="submenu">
                                            <a href="javascript:void(0);"><span>Tables</span><span
                                                    class="menu-arrow"></span></a>
                                            <ul>
                                                <li><a href="tables-basic.html">Basic Tables </a></li>
                                                <li><a href="data-tables.html">Data Table </a></li>
                                            </ul>
                                        </li>
                                        <li class="submenu">
                                            <a href="javascript:void(0);"><span>Maps</span>
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
                                        <li><a href="javascript:void(0);"><span>Documentation</span></a></li>
                                        <li><a href="javascript:void(0);"><span>Changelog v2.2.2</span></a></li>
                                        <li class="submenu">
                                            <a href="javascript:void(0);"><span>Multi Level</span><span
                                                    class="menu-arrow"></span></a>
                                            <ul>
                                                <li><a href="javascript:void(0);">Level 1.1</a></li>
                                                <li class="submenu submenu-two"><a href="javascript:void(0);">Level
                                                        1.2<span class="menu-arrow inside-submenu"></span></a>
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
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0)">
                                        <i class="ti ti-settings-cog"></i><span>Settings</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li class="submenu">
                                            <a href="javascript:void(0)"><span>General Settings</span><span
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
                                                <span>Website Settings</span><span class="menu-arrow"></span>
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
                                                <span>App Settings</span><span class="menu-arrow"></span>
                                            </a>
                                            <ul>
                                                <li><a href="invoice-settings.html">Invoice Settings</a></li>
                                                <li><a href="printers.html">Printers</a></li>
                                                <li><a href="custom-fields.html">Custom Fields</a></li>
                                            </ul>
                                        </li>
                                        <li class="submenu">
                                            <a href="javascript:void(0);">
                                                <span>System Settings</span><span class="menu-arrow"></span>
                                            </a>
                                            <ul>
                                                <li><a href="email-settings.html">Email Settings</a></li>
                                                <li><a href="sms-gateways.html">SMS Gateways</a></li>
                                                <li><a href="gdpr-cookies.html">GDPR Cookies</a></li>
                                            </ul>
                                        </li>
                                        <li class="submenu">
                                            <a href="javascript:void(0);">
                                                <span>Financial Settings</span><span class="menu-arrow"></span>
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
                                                <span>Other Settings</span><span class="menu-arrow"></span>
                                            </a>
                                            <ul>
                                                <li><a href="storage.html">Storage</a></li>
                                                <li><a href="ban-ip-address.html">Ban IP Address</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </li>
            <!-- /Horizontal Single -->

            <!-- Nav List -->
            <li class="nav-item nav-list">
                <ul class="nav">
                    <li>
                        <div>
                            <a href="#" class="btn btn-icon border btn-menubar btnFullscreen">
                                <i class="ti ti-maximize"></i>
                            </a>
                        </div>
                    </li>
                    <li class="dark-mode-list">
                        <a href="javascript:void(0);" id="dark-mode-toggle" class="dark-mode-toggle">
                            <i class="ti ti-sun light-mode active"></i>
                            <i class="ti ti-moon dark-mode"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="javascript:void(0);" class="btn btn-header-list" data-bs-toggle="dropdown">
                            <i class="ti ti-layout-grid-add"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end menus-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="menu-list">
                                        <li>
                                            <a href="contacts.html">
                                                <div class="menu-details">
                                                    <span class="menu-list-icon bg-violet">
                                                        <i class="ti ti-user-up"></i>
                                                    </span>
                                                    <div class="menu-details-content">
                                                        <p>Contacts</p>
                                                        <span>Add New Contact</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="pipeline.html">
                                                <div class="menu-details">
                                                    <span class="menu-list-icon bg-green">
                                                        <i class="ti ti-timeline-event-exclamation"></i>
                                                    </span>
                                                    <div class="menu-details-content">
                                                        <p>Pipline</p>
                                                        <span>Add New Pipline</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ box_route('activities.get') }}">
                                                <div class="menu-details">
                                                    <span class="menu-list-icon bg-pink">
                                                        <i class="ti ti-bounce-right"></i>
                                                    </span>
                                                    <div class="menu-details-content">
                                                        <p>Activities</p>
                                                        <span>Add New Activity</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="analytics.html">
                                                <div class="menu-details">
                                                    <span class="menu-list-icon bg-info">
                                                        <i class="ti ti-analyze"></i>
                                                    </span>
                                                    <div class="menu-details-content">
                                                        <p>Analytics</p>
                                                        <span>Shows All Information</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ box_route('project.dashboard') }}">
                                                <div class="menu-details">
                                                    <span class="menu-list-icon bg-danger">
                                                        <i class="ti ti-atom-2"></i>
                                                    </span>
                                                    <div class="menu-details-content">
                                                        <p>Projects</p>
                                                        <span>Add New Project</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ box_route('deals.dashboard') }}">
                                                <div class="menu-details">
                                                    <span class="menu-list-icon bg-info">
                                                        <i class="ti ti-medal"></i>
                                                    </span>
                                                    <div class="menu-details-content">
                                                        <p>Deals</p>
                                                        <span>Add New Deals</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#globalAddLeadModal">
                                                <div class="menu-details">
                                                    <span class="menu-list-icon bg-secondary">
                                                        <i class="ti ti-chart-arcs"></i>
                                                    </span>
                                                    <div class="menu-details-content">
                                                        <p>Leads</p>
                                                        <span>Add New Leads</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="menu-list">
                                        <li>
                                            <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#global_add_company">
                                                <div class="menu-details">
                                                    <span class="menu-list-icon bg-tertiary">
                                                        <i class="ti ti-building-community"></i>
                                                    </span>
                                                    <div class="menu-details-content">
                                                        <p>Company</p>
                                                        <span>Add New Company</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        
                                        <li>
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#global_add_contact_stage">
                                                <div class="menu-details">
                                                    <span class="menu-list-icon bg-warning">
                                                        <i class="ti ti-star"></i>
                                                    </span>
                                                    <div class="menu-details-content">
                                                        <p>Lead Status</p>
                                                        <span>Add New Lead Status</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        
                                        <li>
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#global_add_leadsubstatus">
                                                <div class="menu-details">
                                                    <span class="menu-list-icon bg-info">
                                                        <i class="ti ti-activity"></i>
                                                    </span>
                                                    <div class="menu-details-content">
                                                        <p>Lead Sub Status</p>
                                                        <span>Add New Lead Sub Status</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>

                                        <li>
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#global_add_source">
                                                <div class="menu-details">
                                                    <span class="menu-list-icon bg-success">
                                                        <i class="ti ti-link"></i>
                                                    </span>
                                                    <div class="menu-details-content">
                                                        <p>Lead Source</p>
                                                        <span>Add New Lead Source</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#global_add_employee">
                                                <div class="menu-details">
                                                    <span class="menu-list-icon bg-info">
                                                        <i class="ti ti-users"></i>
                                                    </span>
                                                    <div class="menu-details-content">
                                                        <p>User</p>
                                                        <span>Add New User</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ box_route('tasks.index') }}">
                                                <div class="menu-details">
                                                    <span class="menu-list-icon bg-success">
                                                        <i class="ti ti-list-check"></i>
                                                    </span>
                                                    <div class="menu-details-content">
                                                        <p>Tasks</p>
                                                        <span>Add New Task</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#upload-campaign-modal">
                                                <div class="menu-details">
                                                    <span class="menu-list-icon bg-purple">
                                                        <i class="ti ti-brand-campaignmonitor"></i>
                                                    </span>
                                                    <div class="menu-details-content">
                                                        <p>Campaign</p>
                                                        <span>Add New Campaign</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="javascript:void(0);" class="btn btn-help" data-bs-toggle="modal" data-bs-target="#globalFaqModal" title="Frequently Asked Questions">
                            <i class="ti ti-help-hexagon"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="lead-reports.html" class="btn btn-chart-pie">
                            <i class="ti ti-chart-pie"></i>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- /Nav List -->

            <!-- WhatsApp Notifications -->
            <li class="nav-item dropdown nav-item-email nav-item-box">
                <a href="javascript:void(0);" class="nav-link" data-bs-toggle="dropdown" onclick="fetchWhatsAppNotifications()">
                    <i class="ti ti-brand-whatsapp"></i>
                    <span class="badge rounded-pill whatsapp-notification-count" style="display: none; background-color: #25D366 !important;">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                    <div class="topnav-dropdown-header d-flex justify-content-between align-items-center" style="background: #25D366 !important;">
                         <span class="notification-title text-white">WhatsApp Messages</span>
                         <div class="d-flex align-items-center">
                             <a href="{{ box_route('whatsapp.index') }}" class="clear-link text-white opacity-75 me-3">View All</a>
                             <a href="javascript:void(0);" class="clear-link text-white opacity-75" onclick="markAllNotificationsAsRead('whatsapp')">Clear All</a>
                         </div>
                    </div>
                    <div class="noti-content">
                        <ul class="notification-list" id="whatsapp-notification-list">
                            <li class="notification-message text-center p-3">
                                <span class="spinner-border spinner-border-sm text-success" role="status" aria-hidden="true"></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
            <!-- /WhatsApp Notifications -->
            <li class="nav-item nav-item-email nav-item-box">
                <a href="{{ box_route('whatsapp.index') }}" title="WhatsApp Chats">
                    <i class="ti ti-message-circle-exclamation"></i>
                </a>
            </li>
            <!-- /Chat -->
             
            <!-- Chat / Leads Notifications -->
            <li class="nav-item dropdown nav-item-box">
                <a href="javascript:void(0);" class="nav-link" data-bs-toggle="dropdown" onclick="fetchLeadNotifications()">
                    <i class="ti ti-message-dots"></i>
                    <span class="badge rounded-pill lead-notification-count" style="display: none;">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                    <div class="topnav-dropdown-header pt-1">
                         <span class="notification-title">Notifications</span>
                         <a href="javascript:void(0);" class="clear-link" onclick="markAllNotificationsAsRead('lead')">Clear All</a>
                    </div>
                    <div class="noti-content">
                        <ul class="notification-list" id="lead-notification-list">
                            <li class="notification-message text-center p-3">
                                <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span> Loading...
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
            <!-- /Chat -->

            <!-- Task Notifications -->
            <li class="nav-item dropdown nav-item-box">
                <a href="javascript:void(0);" class="nav-link" data-bs-toggle="dropdown" onclick="fetchTaskNotifications()">
                    <i class="ti ti-bell"></i>
                    <span class="badge rounded-pill task-notification-count" style="display: none;">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                    <div class="topnav-dropdown-header">
                        <h4 class="notification-title">Pending Tasks</h4>
                        <a href="javascript:void(0);" class="clear-link" onclick="markAllNotificationsAsRead('task')">Clear all</a>
                    </div>
                    <div class="noti-content">
                        <ul class="notification-list" id="task-notification-list">
                            <li class="notification-message text-center p-3">
                                <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span> Loading...
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
            <!-- /Notifications -->
            
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initial fetch on page load
        fetchNotificationCounts();
        
        // Listen for real-time WebSocket notifications
        if (typeof window.Echo !== 'undefined' && window.authUserId) {
            window.Echo.private('App.Models.User.' + window.authUserId)
                .notification((notification) => {
                    console.log('Real-time Notification Received:', notification);
                    
                    // Fetch updated counts instantly
                    fetchNotificationCounts();
                    
                    // Show a toast
                    if (typeof showToast === 'function') {
                        showToast(notification.message || 'You have a new notification!', 'info');
                    }
                });
        }
    });

    // Helper to get URL with params
    function getNotifUrl(type) {
        const baseUrl = "{{ route('notifications.get', ['box_slug' => request()->route('box_slug') ?? 'default']) }}";
        return baseUrl + "?type=" + type;
    }
    
    function fetchNotificationCounts() {
        $.ajax({
            url: getNotifUrl('lead,task,export_ready,whatsapp,missed_punch_out,general'),
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    console.log('Fetched Notification Counts:', response.data);
                    let taskCount = 0;
                    let leadCount = 0;
                    let whatsappCount = 0;

                    response.data.forEach(function(item) {
                        // Check type to categorize
                        if (item.type.includes('TaskAssignedNotification') || item.type.includes('ExportReadyNotification') || item.type.includes('MissedPunchOutNotification') || item.type.includes('GeneralNotification')) {
                            taskCount++;
                        } else if (item.type.includes('NewLeadNotification')) {
                            leadCount++;
                        } else if (item.type.includes('WhatsAppMessageNotification')) {
                            whatsappCount++;
                        }
                    });

                    // Update Task Badge
                    const taskBadge = $('.task-notification-count');
                    taskCount > 0 ? taskBadge.text(taskCount).show() : taskBadge.hide();

                    // Update Lead Badge
                    $('.lead-notification-count').text(leadCount); // Update all instances
                    const leadBadge = $('.nav-link .lead-notification-count'); // Specific one if needed
                    leadCount > 0 ? leadBadge.show() : leadBadge.hide();

                    // Update WhatsApp Badge
                    const waBadge = $('.whatsapp-notification-count');
                    whatsappCount > 0 ? waBadge.text(whatsappCount).show() : waBadge.hide();
                }
            }
        });
    }

    function fetchWhatsAppNotifications() {
        const list = $('#whatsapp-notification-list');
        // list.empty().append('<li class="notification-message text-center p-3"><span class="spinner-border spinner-border-sm text-success"></span></li>');
        
        $.ajax({
            url: "{{ box_route('whatsapp.notifications.unread') }}",
            method: 'GET',
            success: function(response) {
                list.empty();
                const boxSlug = "{{ request()->route('box_slug') ?? 'default' }}";

                if (response.success && response.data && response.data.length > 0) {
                    console.log('Fetched WhatsApp Notifications:', response.data);
                    response.data.forEach(msg => {
                        const date = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        const leadName = msg.lead ? msg.lead.name : (msg.sender_id || 'Unknown');
                        const isOutgoing = msg.type === 'OUTGOING';
                        const chatUrl = msg.lead 
                            ? `/${boxSlug}/whatsapp/chats/${msg.lead.id}`
                            : `/${boxSlug}/whatsapp/chats?chat_id=${msg.sender_id}`;
                        
                        const icon = isOutgoing 
                            ? '<i class="ti ti-arrow-up-right text-info fs-14"></i>' 
                            : '<i class="ti ti-arrow-down-left text-success fs-14"></i>';
                        
                        const label = isOutgoing ? 'To:' : 'From:';
                        
                        const item = `
                            <li class="notification-message p-0 border-bottom ${msg.is_read ? 'bg-white' : 'bg-light'}">
                                <div class="d-flex justify-content-between align-items-start p-3">
                                    <a href="${chatUrl}" target="_blank" onclick="markNotificationAsRead(event, '${msg.id}', 'whatsapp', this)" class="d-flex align-items-center text-decoration-none flex-grow-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <div class="rounded-circle ${isOutgoing ? 'bg-info' : 'bg-success'} text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="ti ti-brand-whatsapp fs-18"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="mb-0 text-dark fw-bold fs-14">${icon} <small class="text-muted fw-normal">${label}</small> ${leadName}</h6>
                                                <span class="text-muted fs-11">${date}</span>
                                            </div>
                                            <p class="text-muted fs-12 mb-0 text-truncate" style="max-width: 200px;">
                                                ${msg.text ? msg.text : (msg.caption || 'Attachment')}
                                            </p>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0);" onclick="markNotificationAsRead(event, '${msg.id}', 'whatsapp', this)" class="text-white bg-secondary rounded d-flex align-items-center justify-content-center flex-shrink-0" style="width:24px;height:24px;font-size:14px;line-height:1;margin-left:10px;margin-top:8px;">
                                        <i class="ti ti-x"></i>
                                    </a>
                                </div>
                            </li>
                        `;
                        list.append(item);
                    });
                } else {
                    list.append('<li class="notification-message text-center p-4"><p class="text-muted mb-0">No unread messages</p></li>');
                }
            },
            error: function() {
                list.html('<li class="notification-message text-center p-3 text-danger">Error loading chats.</li>');
            }
        });
    }

    // --- Task Notifications List (On Click) ---
    function fetchTaskNotifications() {
        $.ajax({
            url: getNotifUrl('task,export_ready,missed_punch_out,general'),
            method: 'GET',
            success: function(response) {
                const list = $('#task-notification-list');
                list.empty();

                if (response.success && response.data && response.data.length > 0) {
                    console.log('Fetched Task Notifications:', response.data);
                    response.data.forEach(function(item) {
                        let content = '';
                        if (item.type.includes('ExportReadyNotification')) {
                             content = `
                                <li class="notification-message p-2 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="${item.data.link}" target="_blank" onclick="markNotificationAsRead(event, '${item.id}', 'task', this)" class="flex-grow-1">
                                            <div class="media d-flex">
                                                <span class="avatar flex-shrink-0">
                                                    <i class="ti ti-download text-success"></i>
                                                </span>
                                                <div class="media-body flex-grow-1">
                                                    <p class="noti-details"><span class="noti-title">Export Ready</span></p>
                                                    <p class="noti-time"><span class="notification-time">${item.data.message}</span></p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="javascript:void(0);" onclick="markNotificationAsRead(event, '${item.id}', 'task', this)" class="text-white bg-secondary rounded d-flex align-items-center justify-content-center flex-shrink-0" style="width:24px;height:24px;font-size:14px;line-height:1;margin-left:10px;">
                                            <i class="ti ti-x"></i>
                                        </a>
                                    </div>
                                </li>
                            `;
                        } else if (item.type.includes('MissedPunchOutNotification')) {
                             content = `
                                <li class="notification-message p-2 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="javascript:void(0);" onclick="markNotificationAsRead(event, '${item.id}', 'task', this)" class="flex-grow-1">
                                            <div class="media d-flex">
                                                <span class="avatar flex-shrink-0 bg-danger-transparent">
                                                    <i class="ti ${item.data.icon} fs-18 text-danger"></i>
                                                </span>
                                                <div class="media-body flex-grow-1">
                                                    <p class="noti-details"><span class="noti-title">${item.data.title}</span></p>
                                                    <p class="noti-time"><span class="notification-time">${item.data.message}</span></p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="javascript:void(0);" onclick="markNotificationAsRead(event, '${item.id}', 'task', this)" class="text-white bg-secondary rounded d-flex align-items-center justify-content-center flex-shrink-0" style="width:24px;height:24px;font-size:14px;line-height:1;margin-left:10px;">
                                            <i class="ti ti-x"></i>
                                        </a>
                                    </div>
                                </li>
                            `;
                        } else if (item.type.includes('GeneralNotification')) {
                             content = `
                                <li class="notification-message p-2 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="${item.data.action_url || 'https://staging.bizzcrm.com/'}" target="_blank" onclick="markNotificationAsRead(event, '${item.id}', 'task', this)" class="flex-grow-1">
                                            <div class="media d-flex">
                                                <span class="avatar flex-shrink-0 bg-info-transparent">
                                                    <i class="ti ti-bell-ringing fs-18 text-info"></i>
                                                </span>
                                                <div class="media-body flex-grow-1">
                                                    <p class="noti-details"><span class="noti-title">${item.data.title}</span></p>
                                                    <p class="noti-time"><span class="notification-time">${item.data.message}</span></p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="javascript:void(0);" onclick="markNotificationAsRead(event, '${item.id}', 'task', this)" class="text-white bg-secondary rounded d-flex align-items-center justify-content-center flex-shrink-0" style="width:24px;height:24px;font-size:14px;line-height:1;margin-left:10px;">
                                            <i class="ti ti-x"></i>
                                        </a>
                                    </div>
                                </li>
                            `;
                        } else {
                            // Existing Task logic (assuming structure)
                            content = `
                                <li class="notification-message p-2 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="${item.data.link || '#'}" onclick="markNotificationAsRead(event, '${item.id}', 'task', this)" class="flex-grow-1">
                                            <div class="media d-flex">
                                                <span class="avatar flex-shrink-0 bg-primary-transparent">
                                                    <i class="ti ti-clipboard-check fs-18 text-primary"></i>
                                                </span>
                                                <div class="media-body flex-grow-1">
                                                    <p class="noti-details"><span class="noti-title">${item.data.message}</span></p>
                                                    <p class="noti-time"><span class="notification-time">${item.created_at_human || (item.created_at ? new Date(item.created_at).toLocaleString() : '')}</span></p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="javascript:void(0);" onclick="markNotificationAsRead(event, '${item.id}', 'task', this)" class="text-white bg-secondary rounded d-flex align-items-center justify-content-center flex-shrink-0" style="width:24px;height:24px;font-size:14px;line-height:1;margin-left:10px;">
                                            <i class="ti ti-x"></i>
                                        </a>
                                    </div>
                                </li>
                            `;
                        }
                        list.append(content);
                    });
                } else {
                    list.append('<li class="notification-message text-center p-3">No pending tasks found.</li>');
                }
            },
            error: function() {
                $('#task-notification-list').html('<li class="notification-message text-center p-3 text-danger">Failed to load notifications.</li>');
            }
        });
    }



    function fetchLeadNotifications() {
        $.ajax({
            url: getNotifUrl('lead'),
            method: 'GET',
            success: function(response) {
                const list = $('#lead-notification-list');
                list.empty();
                const boxSlug = "{{ request()->route('box_slug') ?? 'default' }}";

                console.log("Lead Notifications Response:", response); // Debugging Log

                if (response.success && response.data.length > 0) {
                    // Ensure the header count is also synced just in case
                    $('.lead-notification-count').text(response.data.length); 

                    response.data.forEach(notification => {
                        const d = notification.data;
                        const dateObj = new Date(notification.created_at);
                        const day = String(dateObj.getDate()).padStart(2, '0');
                        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                        const year = dateObj.getFullYear();
                        let hours = dateObj.getHours();
                        const minutes = String(dateObj.getMinutes()).padStart(2, '0');
                        const ampm = hours >= 12 ? 'PM' : 'AM';
                        hours = hours % 12;
                        hours = hours ? hours : 12; 
                        const created = `${day}-${month}-${year} ${hours}:${minutes} ${ampm}`;

                        const detailsUrl = `/${boxSlug}/leads-details?lead_id=${d.lead_id}`;
                        
                        const badgeClass = d.bg_color || 'bg-success';
                        let badgeText = d.title || 'Lead Assigned';
                        if (!d.title) {
                            if (d.type === 'duplicate_lead') {
                                badgeText = 'Duplicate Lead';
                            } else if (d.type === 'lead_restored') {
                                badgeText = 'Lead Restored';
                            } else if (d.type === 'lead_transfer') {
                                badgeText = 'Lead Transferred';
                            }
                        }
                        
                        const item = `
                            <li class="notification-message bg-white border-bottom p-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <a href="${detailsUrl}" class="fw-bold text-dark text-decoration-underline me-2 fs-15 ps-0 pt-1 pb-1">${d.lead_name}</a>
                                            <span class="badge ${badgeClass} rounded-pill px-2 py-1 fs-11">${badgeText}</span>
                                        </div>
                                        <p class="mb-1 text-dark fs-13">Lead Source : ${d.source || 'N/A'}</p>
                                        <p class="mb-0 text-muted fs-12">Created At : ${created}</p>
                                    </div>
                                    <a href="javascript:void(0);" onclick="markNotificationAsRead(event, '${notification.id}', 'lead', this)" class="text-white bg-secondary rounded d-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:14px;line-height:1;margin-left:10px;">
                                        <i class="ti ti-x"></i>
                                    </a>
                                </div>
                            </li>
                        `;
                        list.append(item);
                    });
                } else {
                    list.append('<li class="notification-message text-center p-3">No new leads</li>');
                }
            }
        });
    }

    // Shared Render Logic
    function renderNotificationList(data, selector, emptyMsg) {
        const list = $(selector);
        list.empty();
        const boxSlug = "{{ request()->route('box_slug') ?? 'default' }}";

        if (data && data.length > 0) {
            data.forEach(notification => {
                const d = notification.data;
                const created = new Date(notification.created_at).toLocaleString();
                // If it's a lead notification or task, both link to leads details usually
                // Or tasks link to activity? Assuming lead details for now
                const detailsUrl = `/${boxSlug}/leads-details?lead_id=${d.lead_id}`;
                
                const item = `
                    <li class="notification-message p-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="${detailsUrl}" onclick="markNotificationAsRead(event, '${notification.id}', null, this)" class="flex-grow-1">
                                <div class="media d-flex">
                                    <span class="avatar flex-shrink-0 bg-primary-transparent">
                                        <i class="ti ${d.icon || 'ti-bell'} fs-18"></i>
                                    </span>
                                    <div class="media-body flex-grow-1">
                                        <p class="noti-details">${d.title}</p>
                                        <p class="noti-sub-details">${d.message}</p>
                                        <p class="noti-time">${created}</p>
                                    </div>
                                </div>
                            </a>
                            <a href="javascript:void(0);" onclick="markNotificationAsRead(event, '${notification.id}', null, this)" class="text-white bg-secondary rounded d-flex align-items-center justify-content-center flex-shrink-0" style="width:24px;height:24px;font-size:14px;line-height:1;margin-left:10px;">
                                <i class="ti ti-x"></i>
                            </a>
                        </div>
                    </li>
                `;
                list.append(item);
            });
        } else {
            list.append(`<li class="notification-message text-center p-3">${emptyMsg}</li>`);
        }
    }

    function markNotificationAsRead(e, id, type = null, element = null) {
        if (e) {
            e.stopPropagation();
        }
        
        let url = '{{ route("notifications.mark_read", ["box_slug" => request()->route("box_slug") ?? "default", "id" => ":id"]) }}';
        url = url.replace(':id', id);
        
        if (type) {
            url += `?type=${type}`;
        }
        
        $.ajax({
            url: url,
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                // Remove element instantly
                if (element) {
                    const ul = $(element).closest('ul');
                    $(element).closest('li.notification-message').remove();
                    
                    // Show empty state if list is empty
                    if (ul.children('li.notification-message').length === 0) {
                        if (ul.attr('id') === 'task-notification-list') {
                            ul.html('<li class="notification-message text-center p-3">No new tasks</li>');
                        } else if (ul.attr('id') === 'lead-notification-list') {
                            ul.html('<li class="notification-message text-center p-3">No new leads</li>');
                        } else if (ul.attr('id') === 'whatsapp-notification-list') {
                            ul.html('<li class="notification-message text-center p-4"><p class="text-muted mb-0">No unread messages</p></li>');
                        }
                    }
                }
                // Refresh both to be safe
                fetchNotificationCounts();
            },
            error: function(xhr) {
                console.error("Mark as read failed:", xhr);
                alert("Failed to mark as read. Check console. URL: " + url + " Status: " + xhr.status);
            }
        });
    }

    function markAllNotificationsAsRead(type) {
        const boxSlug = "{{ request()->route('box_slug') ?? 'default' }}";

        $.ajax({
            url: '{{ route("notifications.mark_all_read", ["box_slug" => request()->route("box_slug") ?? "default"]) }}',
            method: 'POST',
            data: { 
                _token: '{{ csrf_token() }}',
                type: type 
            },
            success: function() {
                fetchNotificationCounts();
                if (type === 'task') {
                    $('#task-notification-list').html('<li class="notification-message text-center p-3">No new tasks</li>');
                } else if (type === 'lead') {
                    $('#lead-notification-list').html('<li class="notification-message text-center p-3">No new leads</li>');
                } else if (type === 'whatsapp') {
                    $('#whatsapp-notification-list').html('<li class="notification-message text-center p-4"><p class="text-muted mb-0">No unread messages</p></li>');
                }
            }
        });
    }
</script>
@php
    $user = Auth::user();
    $boxUser = $user->box_user_new;
    $profileImage = $boxUser?->profile_image_path;
    $name = $user->name ?? 'U';
    $initial = strtoupper(substr($name, 0, 1));
@endphp
            <!-- Profile Dropdown -->
            <li class="nav-item dropdown has-arrow main-drop">
                <a href="javascript:void(0);" class="nav-link userset" data-bs-toggle="dropdown">
                    <span class="user-info">
                        <span class="user-letter">
@if ($profileImage)
    <img src="{{ asset('storage/' . $profileImage) }}" alt="Profile" class="img-fluid rounded-circle" width="40" height="40" />
@else
    <div class="letter-avatar rounded-circle text-white text-uppercase d-flex justify-content-center align-items-center"
         title="{{ $name }}"
         style="width: 40px; height: 40px; background-color: {{ getColorFromNameBlades($name) }}; font-size: 18px; font-weight: bold;">
        {{ $initial }}
    </div>
@endif                        </span>
                        <span class="badge badge-success rounded-pill"></span>
                    </span>
                </a>
                @php
    function getColorFromNameBlades($name) {
        $colors = [
            "#f44336", "#e91e63", "#9c27b0", "#673ab7", "#3f51b5",
            "#2196f3", "#03a9f4", "#00bcd4", "#009688", "#4caf50",
            "#8bc34a", "#cddc39", "#ffc107", "#ff9800", "#ff5722"
        ];
        $char = strtoupper(trim($name)[0] ?? 'A');
        $index = ord($char) % count($colors);
        return $colors[$index];
    }
@endphp


                <div class="dropdown-menu menu-drop-user">
                    <div class="profilename">
                        <div class="p-3 border-bottom mb-2">
                            <p class="mb-0 fw-bold fs-14">{{ Auth::user()->name }}</p>
                            @php $activeSub = Auth::user()->activeSubscription(); @endphp
                            @if(Auth::user()->hasRole('admin'))
                                @if($activeSub)
                                    <span class="badge bg-soft-success text-success mt-1">
                                        <i class="ti ti-crown me-1"></i> {{ $activeSub->plan->name }} Plan
                                    </span>
                                @else
                                    <span class="badge bg-soft-secondary text-secondary mt-1">Free Account</span>
                                @endif
                            @endif
                        </div>
                        <a class="dropdown-item" href="{{ Auth::user()->hasRole('superadmin') ? box_route('superadmin.dashboard') : box_route('employee.dashboard') }}">
                            <i class="ti ti-layout-2"></i> Dashboard
                        </a>
                        <a class="dropdown-item"
                           href="{{ route('employee.details', ['box_slug' => request()->route('box_slug')]) }}?emp_id={{ Auth::user()->box_user_new?->id }}">
                            <i class="ti ti-user-pin"></i> My Profile
                        </a>
                        @if(Auth::user()->hasRole('admin'))
                        <a class="dropdown-item" href="{{ box_route('subscription.index') }}">
                            <i class="ti ti-credit-card"></i> Subscription
                        </a>
                        <a class="dropdown-item text-danger" href="{{ box_route('account.delete.index') }}">
                            <i class="ti ti-trash"></i> Request Delete
                        </a>
                        @endif
                        <!-- Logout link -->
                        <a class="dropdown-item" href="#"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="ti ti-lock"></i> Logout
                        </a>

                            <!-- Hidden logout form -->
                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                    </div>
                </div>

            </li>
            <!-- /Profile Dropdown -->

        </ul>
    </div>

    <!-- Mobile Menu -->
    <div class="dropdown mobile-user-menu">
        <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
            aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ Auth::user()->hasRole('superadmin') ? box_route('superadmin.dashboard') : box_route('employee.dashboard') }}">
                <i class="ti ti-layout-2"></i> Dashboard
            </a>
            <a class="dropdown-item" href="{{ route('employee.details', ['box_slug' => request()->route('box_slug')]) }}?emp_id={{ Auth::user()->box_user_new?->id }}">
                <i class="ti ti-user-pin"></i> My Profile
            </a>
            @if(Auth::user()->hasRole('admin'))
            <a class="dropdown-item" href="{{ box_route('subscription.index') }}">
                <i class="ti ti-credit-card"></i> Subscription
            </a>
            <a class="dropdown-item text-danger" href="{{ box_route('account.delete.index') }}">
                <i class="ti ti-trash"></i> Request Delete
            </a>
            @endif
            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="ti ti-lock"></i> Logout
            </a>
        </div>
    </div>
    <!-- /Mobile Menu -->

</div>

@include('frontend.leads.partials.global_add_lead_modal')
@include('frontend.employess.partials.global_add_employee_modal')
@include('frontend.employess.components.designation_tree_modal')
@include('frontend.employess.crm_setting.partials.global_add_contact_stage_modal')
@include('frontend.employess.crm_setting.partials.global_add_lead_sub_status_modal')
@include('frontend.employess.crm_setting.partials.global_add_source_modal')
@include('frontend.employess.crm_setting.partials.global_add_campaign_modal')
@include('frontend.employess.crm_setting.partials.global_add_company_modal')

<!-- Global FAQ Modal -->
<div class="modal fade custom-modal" id="globalFaqModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0 pt-2 pb-2">
                <div class="form-header modal-header-title text-start mb-0">
                    <h4 class="mb-0 fs-3">Frequently Asked Questions</h4>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-window-close"></i></button>
            </div>
            <div class="modal-body pt-2 px-4 pb-4" style="max-height: inherit;">
                <!-- Search Bar -->
                <div class="mb-4">
                    <div class="input-group input-group-lg shadow-sm rounded border" style="background: white; padding: 5px;">
                        <span class="input-group-text bg-white border-0"><i class="fa-solid fa-magnifying-glass fs-15"></i></span>
                        <input type="text" id="globalFaqSearchInput" class="form-control border-0 shadow-none bg-white" placeholder="Search FAQs by question or answer...">
                    </div>
                </div>

                <div class="accordion" id="globalFaqAccordion">
                    <!-- FAQs will be loaded here via AJAX -->
                </div>
                
                <div id="globalFaqLoader" class="text-center my-5" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                
                <div id="globalFaqNoResults" class="alert alert-warning text-center mt-3" style="display: none;">
                    No FAQs found matching your search.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let globalFaqSearchTimeout;
    let globalFaqLoaded = false;

    function loadGlobalFaqs(searchQuery = '') {
        $('#globalFaqAccordion').empty();
        $('#globalFaqLoader').show();
        $('#globalFaqNoResults').hide();

        $.ajax({
            url: "{{ route('frontend.faq.api') }}",
            type: "GET",
            data: { search: searchQuery },
            dataType: "json",
            success: function(response) {
                $('#globalFaqLoader').hide();
                if(response && response.status && response.data.length > 0) {
                    let html = '';
                    response.data.forEach(function(faq, index) {
                        let isFirst = (index === 0 && searchQuery === ''); 
                        let isSearchMatch = (searchQuery !== ''); 
                        let isOpen = isFirst || isSearchMatch;
                        
                        let collapsedClass = isOpen ? '' : 'collapsed';
                        let expandedAttr = isOpen ? 'true' : 'false';
                        let showClass = isOpen ? 'show' : '';

                        html += `
                            <div class="accordion-item mb-3 border shadow-sm rounded faq-item">
                                <h2 class="accordion-header" id="globalHeading${faq.id}">
                                    <button class="accordion-button ${collapsedClass} fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#globalCollapse${faq.id}" aria-expanded="${expandedAttr}" aria-controls="globalCollapse${faq.id}" style="font-size: 1.1rem; padding: 1.25rem;">
                                        ${faq.question}
                                    </button>
                                </h2>
                                <div id="globalCollapse${faq.id}" class="accordion-collapse collapse ${showClass}" aria-labelledby="globalHeading${faq.id}" data-bs-parent="#globalFaqAccordion">
                                    <div class="accordion-body" style="font-size: 1rem; line-height: 1.6; color: #495057; padding: 1.25rem;">
                                        ${faq.answer}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#globalFaqAccordion').html(html);
                } else {
                    $('#globalFaqNoResults').show();
                }
            },
            error: function(xhr, status, error) {
                $('#globalFaqLoader').hide();
                console.error("Error loading FAQs:", error);
            }
        });
    }

    // Use Bootstrap event for modal
    $('#globalFaqModal').on('show.bs.modal', function () {
        if (!globalFaqLoaded) {
            loadGlobalFaqs();
            globalFaqLoaded = true;
        }
    });

    $('#globalFaqSearchInput').on('input', function() {
        let query = $(this).val().trim();
        clearTimeout(globalFaqSearchTimeout);
        globalFaqSearchTimeout = setTimeout(function() {
            loadGlobalFaqs(query);
        }, 300);
    });
});
</script>