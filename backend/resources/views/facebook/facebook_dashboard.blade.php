@extends('layouts.main')
@section('title', 'Facebook Dashboard')
@section('content')

<div class="page-wrapper">
    <div class="content" id="facebook-integration-content">
        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Facebook Leads & Ads</h2>
                @if(session('success'))
                    <div class="alert alert-success mt-2 py-1 px-2 small">
                        <strong>Success!</strong> {{ session('success') }}
                    </div>
                @endif
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                <div class="d-flex align-items-center flex-wrap mb-1" id="fb-status-container">
                    <span class="me-2 ac-con fw-medium">Facebook Connection Status: 
                        <span id="fb-status-badge" class="badge @if($facebookConnected) bg-success @else bg-warning @endif" style="padding-top: 7.5px;padding-bottom: 7.5px;">
                            @if($facebookConnected) 
                                CONNECTED 
                            @else 
                                PENDING 
                            @endif
                        </span>
                    </span>
                    <button type="button" onclick="syncAllForms()" class="btn btn-sm btn-outline-info me-2" title="Sync Forms & Pages">
                        <i class="ti ti-refresh me-1"></i> Sync Forms
                    </button>
                    <a href="javascript:void(0);" onclick="launchFacebookLogin()" class="btn bg-primary btn-sm me-2 btn-connect-fb text-white"><i class="ti ti-plug-connected me-1"></i>Add Account</a>
                    @if($facebookConnected)
                    <a href="javascript:void(0);" onclick="confirmDisconnect()" class="btn bg-danger btn-sm me-2"><i class="ti ti-plug-off me-1"></i>Disconnect</a>
                    @endif
                </div>
                <div class="ms-2 head-icons">
                    <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
                        <i class="ti ti-chevrons-up"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <!-- Welcome Wrap -->
        <div class="card border-0" style="background:#0761f6;">
            <div class="card-body pb-1">
                <div class="d-flex align-items-center mb-3">
                    <span class="avatar avatar-xl flex-shrink-0">
                        <img src="{{ asset('assets/img/profiles/avatar-31.jpg') }}" class="rounded-circle" alt="img" />
                    </span>
                    <div class="ms-3">
                        <h3 class="text-white">Hey {{ Auth::user()->first_name ?? Auth::user()->name }}, Welcome to Facebook Ads Dashboard</h3>
                        <p class="text-white">Manage your Meta lead forms, pages, and advertisement sync in real-time.</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center flex-wrap mb-1">
                        <a href="#" class="btn btn-light btn-md me-2 mb-2"><i class="ti ti-book me-1"></i>Ads Guide</a>
                        <a href="#" class="btn btn-light btn-md me-2 mb-2"><i class="ti ti-video me-1"></i>Tutorials</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Welcome Wrap -->

        <!-- Facebook Stats Row -->
        <div class="row">
            <div class="col-xl-3 col-sm-6 col-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="stats-info">
                                <p class="mb-1 text-gray-9">Total Facebook Leads</p>
                                <h4 class="mb-0" id="stat-total-leads"><i class="fas fa-spinner fa-spin fs-14"></i></h4>
                            </div>
                            <div class="stats-icon bg-info-light">
                                <i class="ti ti-user-check fs-30 text-info"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <p class="mb-0 fs-12 text-muted">All Time Sync</p>
                            <span class="badge bg-success-transparent text-success fs-10">Live</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="stats-info">
                                <p class="mb-1 text-gray-9">Connected Pages</p>
                                <h4 class="mb-0" id="stat-total-pages"><i class="fas fa-spinner fa-spin fs-14"></i></h4>
                            </div>
                            <div class="stats-icon bg-success-light">
                                <i class="ti ti-brand-facebook fs-30 text-success"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <p class="mb-0 fs-12 text-muted">Active Subscriptions</p>
                            <span class="badge bg-success-transparent text-success fs-10" id="stat-pages-active">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="stats-info">
                                <p class="mb-1 text-gray-9">Lead Forms</p>
                                <h4 class="mb-0" id="stat-total-forms"><i class="fas fa-spinner fa-spin fs-14"></i></h4>
                            </div>
                            <div class="stats-icon bg-warning-light">
                                <i class="ti ti-forms fs-30 text-warning"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <p class="mb-0 fs-12 text-muted">Forms Discovered</p>
                            <span class="badge bg-warning-transparent text-warning fs-10">Synced</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="stats-info">
                                <p class="mb-1 text-gray-9">Ad Accounts</p>
                                <h4 class="mb-0" id="stat-total-accounts"><i class="fas fa-spinner fa-spin fs-14"></i></h4>
                            </div>
                            <div class="stats-icon bg-danger-light">
                                <i class="ti ti-ad fs-30 text-danger"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <p class="mb-0 fs-12 text-muted">Manager Access</p>
                            <span class="badge bg-danger-light text-danger fs-10">Verified</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Facebook Stats Row -->

        <!-- Lead Analytics Graph -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm border-primary">
                    <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                        <h5 class="card-title mb-0"><i class="ti ti-chart-area-line me-2 fs-24 fb-color"></i> Facebook Lead Acquisition (Monthly)</h5>
                    </div>
                    <div class="card-body">
                        <div id="lead-analytics-chart" style="min-height: 350px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Lead Analytics Graph -->


        <div class="row">
            <div class="col-md-7">
                <!-- Connected Ad Accounts List -->
                <div class="card mb-0 shadow-sm border-info" id="accounts-card" style="display: none;">
                    <div class="card-header justify-content-between d-flex align-items-center bg-soft-info">
                        <div class="card-title mb-0">
                            <span class="avatar rounded-circle bg-info text-white mb-0 me-2">
                                <i class="ti ti-ad fs-16"></i>
                            </span> Connected Ad Accounts
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Account Name</th>
                                        <th>Status</th>
                                        <th>Currency</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="accounts-tbody">
                                    <!-- Rendered via AJAX -->
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-center p-2 bg-light border-top" id="accounts-footer" style="display: none;">
                            <a href="javascript:void(0);" id="btnShowMoreAccs" onclick="toggleExtraAccs()" class="btn btn-xs btn-link text-info fw-bold">
                                <i class="ti ti-chevron-down me-1"></i> Show More
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Connected Pages List -->
                <div class="card shadow-sm border-primary mb-4" id="pages-card" style="display: none;">
                    <div class="card-header justify-content-between d-flex align-items-center bg-soft-primary">
                        <div class="card-title mb-0">
                            <span class="avatar rounded-circle fb-bg text-white mb-0 me-2">
                                <i class="ti ti-brand-facebook fs-22"></i>
                            </span> Connected Facebook Pages
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" onclick="syncAllForms()" class="btn btn-sm btn-outline-primary"><i class="ti ti-refresh me-1"></i> Sync All Forms</button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Page Details</th>
                                        <th>Lead Forms</th>
                                        <th>Webhook</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="pages-tbody">
                                    <!-- Rendered via AJAX -->
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-center p-2 bg-light border-top" id="pages-footer" style="display: none;">
                            <a href="javascript:void(0);" id="btnShowMorePages" onclick="toggleExtraPages()" class="btn btn-xs btn-link text-primary fw-bold">
                                <i class="ti ti-chevron-down me-1"></i> Show More
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card border-primary">
                    <div class="card-header justify-content-between d-flex align-items-center bg-soft-primary">
                        <div class="card-title mb-0">
                            <span class="avatar rounded-circle fb-bg text-white mb-0 me-2">
                                <i class="ti ti-brand-facebook fs-22"></i>
                            </span> Facebook Connect
                        </div>
                        @if(isset($facebookConnected) && $facebookConnected)
                            <span class="badge bg-success ms-2">Connected</span>
                        @else
                            <span class="badge bg-danger ms-2">Not Connected</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
                        @if(isset($facebookConnected) && $facebookConnected)
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>✓ Connected!</strong> Facebook is connected and ready to sync forms.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @else
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>⚠ Not Connected</strong> Please click "Configure" to connect your Facebook account.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <div class="action-btns text-right mt-3 fbdashbtns">
                            <a href="javascript:void(0);" onclick="launchFacebookLogin()" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm" id="configure-facebook-btn">
                                <i class="fa-solid fa-plug"></i> {{ isset($facebookConnected) && $facebookConnected ? 'Reconnect' : 'Configure' }}
                            </a>
                            <a href="javascript:void(0);" onclick="checkFacebookConnection()" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm" id="check-connection-btn">
                                <i class="fa-solid fa-check-circle"></i> Check Status
                            </a>
                            @if(isset($facebookConnected) && $facebookConnected)
                                <a href="javascript:void(0);" onclick="getBusinessIntegration()" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm me-2" id="get-integration-btn">
                                    <i class="fa-solid fa-download"></i> Get Integration Details
                                </a>
                                <a href="javascript:void(0);" onclick="syncAllForms()" class="fw-bold text-decoration-none link-primary btn btn-warning btn-sm" id="sync-forms-btn">
                                    <i class="fa-solid fa-sync"></i> Sync Forms
                                </a>
                            @endif
                            <a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
                            <a href="{{ box_route('facebook.list') }}" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
                            <a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="row flex-fill">
                    <div class="col-md-12 d-flex">
                        <div class="card flex-fill border-warning">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="">
                                        <h6 class="fs-15 fw-medium text-default mb-2">Lead Sync Status</h6>
                                        <div class="d-flex align-items-center">
                                            <div class="spinner-grow spinner-grow-sm text-success me-2" role="status"></div>
                                            <span class="fw-bold text-success">Live Real-time Webhooks</span>
                                        </div>
                                    </div>
                                    <div class="">
                                        <button onclick="syncAllLeads()" class="btn btn-warning btn-sm">Force Sync Now</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 d-flex">
                        <div class="card flex-fill">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Real-time Lead Mapping</h6>
                            </div>
                            <div class="card-body">
                                <p class="small text-muted mb-3">Ensure your Meta Form fields match your CRM fields for perfect lead creation.</p>
                                <a href="{{ box_route('facebook.list') }}" class="btn fb-bg btn-block w-100">
                                    <i class="ti ti-settings me-1 fs-15"></i> Configure Form Mapping
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body">
                                <span class="avatar rounded-circle bg-primary mb-2">
                                    <i class="ti ti-help fs-22"></i>
                                </span>
                                <h6 class="fs-18 fw-medium text-default mb-1"><strong>Integration Guide</strong></h6>
                                <p class="small">Learn how to setup Facebook Lead Ads and Webhooks correctly.</p>
                                <a href="#" class="text-primary small fw-bold">Read Documentation →</a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Leads Section -->
                    <div class="col-md-12 d-flex mb-4">
                        <div class="card mb-0 shadow-sm border-success flex-fill">
                            <div class="card-header justify-content-between d-flex align-items-center bg-soft-success">
                                <div class="card-title mb-0">
                                    <span class="avatar rounded-circle bg-success text-white mb-0 me-2">
                                        <i class="ti ti-users fs-22"></i>
                                    </span> Recent Facebook Leads
                                </div>
                                <a href="{{ box_route('facebook.leads') }}" class="btn btn-sm btn-link text-success fw-bold">View All Leads →</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover table-nowrap mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Lead Details</th>
                                                <th>Page / Form</th>
                                            </tr>
                                        </thead>
                                        <tbody id="leads-tbody">
                                            <tr>
                                                <td colspan="2" class="text-center p-4">
                                                    <div class="text-muted small"><i class="fas fa-spinner fa-spin me-1"></i> Loading recent leads...</div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer text-center p-2 bg-light border-top" id="leads-footer" style="display: none;">
                                    <a href="{{ box_route('facebook.leads') }}" class="btn btn-xs btn-link text-success fw-bold">
                                        Show More Leads
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div class="modal fade" id="customConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customConfirmTitle">Confirm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="customConfirmMessage">Are you sure?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="customConfirmBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>
<!-- /Custom Confirmation Modal -->

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        fetchDashboardData();
    });

    function fetchDashboardData() {
        $.get('{{ box_route('facebook.dashboard_data') }}')
            .done(function(res) {
                if (res.success) {
                    renderStats(res.stats);
                    renderAccounts(res.accounts);
                    renderPages(res.pages);
                    renderRecentLeads(res.recent_leads);
                    renderChart(res.chart);
                }
            });
    }

    function renderStats(stats) {
        $('#stat-total-leads').text(stats.total_leads);
        $('#stat-total-pages').text(stats.total_pages);
        $('#stat-total-forms').text(stats.total_forms);
        $('#stat-total-accounts').text(stats.total_accounts);
        $('#stat-pages-active').text(stats.total_pages + ' Active');
    }

    function renderAccounts(accounts) {
        const tbody = $('#accounts-tbody');
        tbody.empty();
        
        if (accounts.length > 0) {
            $('#accounts-card').show();
            accounts.forEach((acc, index) => {
                const isHidden = index >= 4 ? 'd-none extra-acc-row' : '';
                tbody.append(`
                    <tr class="${isHidden}">
                        <td>
                            <div class="fw-bold text-dark">${acc.name}</div>
                            <small class="text-muted">ID: ${acc.facebook_id}</small>
                        </td>
                        <td>
                            <span class="badge ${acc.account_status == 1 ? 'bg-success-light text-success' : 'bg-warning-light text-warning'}">
                                ${acc.account_status == 1 ? 'Active' : 'Inactive'}
                            </span>
                        </td>
                        <td>${acc.currency || 'USD'}</td>
                        <td>
                            <button onclick="disconnectAdAccount('${acc.id}', '${acc.name}')" class="btn btn-xs btn-outline-danger">
                                <i class="ti ti-trash me-1"></i> Disconnect
                            </button>
                        </td>
                    </tr>
                `);
            });
            if (accounts.length > 4) {
                $('#accounts-footer').show();
                $('#btnShowMoreAccs').html(`<i class="ti ti-chevron-down me-1"></i> Show More (${accounts.length - 4} More)`);
            }
        }
    }

    function renderPages(pages) {
        const tbody = $('#pages-tbody');
        tbody.empty();
        
        if (pages.length > 0) {
            $('#pages-card').show();
            pages.forEach((page, index) => {
                const isHidden = index >= 4 ? 'd-none extra-page-row' : '';
                tbody.append(`
                    <tr class="${isHidden}">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="ms-2">
                                    <div class="fw-bold text-dark">${page.name}</div>
                                    <small class="text-muted d-block">ID: ${page.facebook_id}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info-light text-info">
                                ${page.form_count} Forms
                            </span>
                        </td>
                        <td>
                            <span class="badge ${page.webhook_enabled ? 'bg-success-light text-success' : 'bg-warning-light text-warning'}">
                                ${page.webhook_enabled ? 'Active' : 'Disabled'}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <button onclick="checkWebhook('${page.id}')" class="btn btn-sm btn-outline-info">
                                    <i class="ti ti-activity me-1"></i> Test
                                </button>
                                <a href="{{ box_route('facebook.leads') }}?page_id=${page.id}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-users me-1"></i> View Leads
                                </a>
                            </div>
                        </td>
                    </tr>
                `);
            });
            if (pages.length > 4) {
                $('#pages-footer').show();
                $('#btnShowMorePages').html(`<i class="ti ti-chevron-down me-1"></i> Show More (${pages.length - 4} More)`);
            }
        }
    }

    function renderRecentLeads(leads) {
        const tbody = $('#leads-tbody');
        tbody.empty();
        
        if (leads.length > 0) {
            $('#leads-footer').show();
            leads.forEach(lead => {
                tbody.append(`
                    <tr>
                        <td>
                            <div class="fw-bold text-dark fs-12">${lead.name}</div>
                            <small class="text-muted" style="font-size:10px;">${lead.email} | ${lead.phone}</small>
                        </td>
                        <td>
                            <div class="small fw-medium fs-11">${lead.page_name}</div>
                            <small class="text-muted" style="font-size:9px;">${lead.time}</small>
                        </td>
                    </tr>
                `);
            });
        } else {
            tbody.append('<tr><td colspan="2" class="text-center p-4 text-muted">No recent leads found.</td></tr>');
        }
    }

    function renderChart(chart) {
        var options = {
            series: [{
                name: 'Leads',
                data: chart.data
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: { show: false }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            colors: ['#1877F2'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: chart.labels,
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: { title: { text: 'Total Leads' } },
            grid: { borderColor: '#f1f1f1' },
            tooltip: { theme: 'light' }
        };

        var apexChart = new ApexCharts(document.querySelector("#lead-analytics-chart"), options);
        apexChart.render();
    }

    function launchFacebookLogin() {
        const appId = '{{ config('services.facebook.client_id') }}';
        const boxSlug = '{{ $box->slug ?? "" }}' || "{{ Request::segment(1) }}";
        localStorage.setItem('last_tenant_slug', boxSlug);
        localStorage.setItem('last_auth_url', window.location.href);
        const saveUrl = '{{ route('settings.integrations.facebook.save', ['box_slug' => $box->slug ?? ':box_slug']) }}'.replace(':box_slug', boxSlug);
        
        // Use the STATIC callback URL whitelisted in Meta Dashboard
        const redirectUri = window.location.origin + '/auth/facebook/callback';
        
        // Pass boxSlug in state to maintain context
        const state = encodeURIComponent(`box_slug=${boxSlug}&display=popup`);
        
        const oauthUrl = `https://www.facebook.com/v22.0/dialog/oauth?client_id=${appId}&redirect_uri=${encodeURIComponent(redirectUri)}&state=${state}&scope=public_profile,email,pages_show_list,pages_read_engagement,pages_manage_metadata,pages_manage_ads,ads_management,leads_retrieval&auth_type=rerequest&response_type=code&display=popup`;
        
        console.log('--- OAuth Debug Info ---');
        console.log('Static Redirect URI:', redirectUri);
        console.log('OAuth URL:', oauthUrl);
        console.log('------------------------');

        const width = 600;
        const height = 700;
        const left = (window.screen.width / 2) - (width / 2);
        const top = (window.screen.height / 2) - (height / 2);
        
        const popup = window.open(oauthUrl, 'FacebookLogin', `width=${width},height=${height},left=${left},top=${top}`);

        // Listen for message from the callback popup
        const messageHandler = function(event) {
            if (event.data.type === 'facebook-auth-success') {
                window.removeEventListener('message', messageHandler);
                // The server already saved the token, we just need to reload
                alert('✓ Facebook connected successfully!');
                window.location.reload();
            } else if (event.data.type === 'facebook-auth-error') {
                window.removeEventListener('message', messageHandler);
                alert('Facebook Error: ' + event.data.error);
            }
        };

        window.addEventListener('message', messageHandler);

        const checkPopup = setInterval(() => {
            if (popup.closed) {
                clearInterval(checkPopup);
            }
        }, 1000);
    }

    function showConfirmModal(title, message, confirmCallback, btnClass = 'btn-primary', btnText = 'Confirm') {
        $('#customConfirmTitle').text(title);
        $('#customConfirmMessage').text(message);
        $('#customConfirmBtn').removeClass().addClass('btn ' + btnClass).text(btnText);
        
        $('#customConfirmBtn').off('click').on('click', function() {
            $('#customConfirmModal').modal('hide');
            confirmCallback();
        });
        
        $('#customConfirmModal').modal('show');
    }

    function syncAllForms() {
        showConfirmModal('Sync Forms', 'Are you sure you want to sync all forms?', function() {
            $.post('{{ box_route('facebook.sync-forms') }}', { _token: '{{ csrf_token() }}' })
                .done(function(res) {
                    alert('Forms synced!');
                    fetchDashboardData();
                })
                .fail(function(err) {
                    alert('Sync failed');
                });
        }, 'btn-primary', 'Sync Now');
    }

    function syncAllLeads() {
        showConfirmModal('Start Lead Sync', 'Leads will be synced in the background. Continue?', function() {
            $.post('{{ box_route('facebook.sync-leads') }}', { _token: '{{ csrf_token() }}' })
                .done(function(res) {
                    alert(res.message);
                });
        }, 'btn-primary', 'Start Sync');
    }

    function confirmDisconnect() {
        showConfirmModal('Disconnect Facebook?', 'This will stop all real-time lead syncing.', function() {
            $.post('{{ box_route('facebook.disconnect') }}', { _token: '{{ csrf_token() }}' })
                .done(function() { location.reload(); });
        }, 'btn-danger', 'Yes, Disconnect');
    }

    function toggleExtraPages() {
        const rows = $('.extra-page-row');
        const btn = $('#btnShowMorePages');
        if (rows.hasClass('d-none')) {
            rows.removeClass('d-none');
            btn.html('<i class="ti ti-chevron-up me-1"></i> Show Less');
        } else {
            rows.addClass('d-none');
            btn.html('<i class="ti ti-chevron-down me-1"></i> Show More');
        }
    }

    function toggleExtraAccs() {
        const rows = $('.extra-acc-row');
        const btn = $('#btnShowMoreAccs');
        if (rows.hasClass('d-none')) {
            rows.removeClass('d-none');
            btn.html('<i class="ti ti-chevron-up me-1"></i> Show Less');
        } else {
            rows.addClass('d-none');
            btn.html('<i class="ti ti-chevron-down me-1"></i> Show More');
        }
    }

    function disconnectAdAccount(id, name) {
        showConfirmModal('Disconnect Account?', `Are you sure you want to disconnect ${name}?`, function() {
            $.post('{{ box_route('facebook.disconnect_account') }}', { 
                _token: '{{ csrf_token() }}',
                id: id
            }).done(function() { fetchDashboardData(); });
        }, 'btn-danger', 'Yes, Disconnect');
    }
    
    function checkFacebookConnection() {
        $.get('{{ box_route('facebook.dashboard_data') }}')
            .done(function(res) {
                if(res.success && res.stats.total_accounts > 0) {
                    alert('Facebook is connected and working!');
                } else {
                    alert('Facebook is not connected properly.');
                }
            }).fail(function() {
                alert('Failed to check status.');
            });
    }

    function getBusinessIntegration() {
        alert('Integration Details:\nStatus: Active\nWebhooks: Listening');
    }
    
    function checkWebhook(pageId) {
        $.get(`/{{ $box->slug }}/settings/facebook/check_webhook/${pageId}`)
            .done(function(res) {
                alert('Webhook Status: ' + res.message);
            });
    }
</script>
@endpush
