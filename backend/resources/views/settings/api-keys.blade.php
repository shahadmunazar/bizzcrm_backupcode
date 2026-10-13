@extends('layouts.main')
@section('content')
@section('title', 'API Keys')

    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    @endpush

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @endpush

    <div class="page-wrapper">
        <div class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="page-title">Lead APIs</h4>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ box_route('admin.dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Lead APIs</li>
                                </ul>
                            </div>
                            <div class="col-4 text-end">
                                <div class="head-icons">
                                    <a href="{{ url()->current() }}" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Refresh">
                                        <i class="ti ti-refresh-dot"></i>
                                    </a>
                                    <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Collapse" id="collapse-header">
                                        <i class="ti ti-chevrons-up"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Page Header -->

                    @include('settings.partials.nav')

                    <div class="row">
                        <div class="col-xl-3 col-lg-12 theiaStickySidebar">
                            @include('settings.partials.sidebar')
                        </div>
                        <div class="col-xl-9 col-lg-12">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex">
                                    <button class="btn btn-warning me-2" id="sync-legacy-apis-btn">
                                        <i class="ti ti-refresh me-1"></i> Sync Legacy APIs
                                    </button>
                                    <a href="javascript:void(0);" onclick="launchFacebookLogin()" class="btn btn-primary me-2">
                                        <i class="fa-brands fa-facebook me-1"></i>
                                        {{ $facebookConnected ? 'Add Facebook Account' : 'Configure Facebook' }}
                                    </a>
                                </div>
                                <div class="d-flex me-2">
                                    <a href="{{ box_route('google.connect.box', ['box_slug' => $box->slug, 'current_url' => url()->current()]) }}"
                                        class="btn btn-danger d-flex align-items-center {{ $googleConnected ? 'rounded-end-0' : '' }}">
                                        <i class="fa-brands fa-google me-2"></i>
                                        @if($googleConnected)
                                            <span>Reconnect Gmail</span>
                                            <span class="badge bg-white text-danger ms-2">{{ $connectedGmailCount }}</span>
                                        @else
                                            <span>Connect Gmail</span>
                                        @endif
                                    </a>
                                    @if($googleConnected)
                                        <a href="{{ box_route('settings.general.email_inboxes') }}"
                                            class="btn btn-outline-danger d-flex align-items-center rounded-start-0 border-start-0"
                                            data-bs-toggle="tooltip" title="Inbox Settings & Mail Filters">
                                            <i class="ti ti-mail-cog me-1"></i> Inbox Settings
                                        </a>
                                    @endif
                                </div>
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add_api_modal">
                                    <i class="ti ti-plus me-1"></i> Add New
                                </button>
                            </div>

                            <div class="row" id="connected_integrations_grid">
                                <div class="col-12 text-center py-5" id="grid_loader">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <p class="mt-2 text-muted">Loading your connected APIs...</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add API Modal -->
    <div class="modal fade" id="add_api_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">Available API Integrations</h5>
                    <button type="button" class="btn-close-custom bg-transparent border-0" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x fs-3"></i>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="row gy-4" id="available_api_grid">
                        {{-- Populated via JS --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Webhook Integration Guide Modal -->
    <div class="modal fade" id="webhook_guide_modal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">Webhook Integration Guide</h5>
                    <button type="button" class="btn-close-custom bg-transparent border-0" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x fs-3"></i>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <ul class="nav nav-tabs nav-tabs-solid mb-3">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#guide_curl">cURL</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#guide_postman">Postman</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#guide_nodejs">Node.js</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#guide_python">Python</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#guide_fields">Fields
                                Mapping</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#guide_json_all">Meta JSON</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane show active" id="guide_curl">
                            <div class="alert alert-info py-2 small mb-3">
                                <i class="ti ti-info-circle me-1"></i> These examples show basic fields. For a complete list
                                of all supported fields and their format, please check the <strong>Fields</strong> tab.
                            </div>
                            <h6 class="fw-bold mb-2">JSON Request (Recommended)</h6>
                            <div class="position-relative mb-3">
                                <pre
                                    class="bg-dark text-white p-3 rounded small mb-0"><code id="curl_json_code"></code></pre>
                                <button class="btn btn-sm btn-outline-light position-absolute top-0 end-0 m-2 copy-btn"
                                    data-target="curl_json_code"><i class="ti ti-copy"></i></button>
                            </div>

                            <h6 class="fw-bold mb-2">Form Data POST</h6>
                            <div class="position-relative">
                                <pre
                                    class="bg-dark text-white p-3 rounded small mb-0"><code id="curl_form_code"></code></pre>
                                <button class="btn btn-sm btn-outline-light position-absolute top-0 end-0 m-2 copy-btn"
                                    data-target="curl_form_code"><i class="ti ti-copy"></i></button>
                            </div>
                        </div>
                        <div class="tab-pane" id="guide_postman">
                            <p>Import this JSON directly into Postman to get started:</p>
                            <div class="position-relative">
                                <pre class="bg-dark text-white p-3 rounded small mb-0"
                                    style="max-height: 300px; overflow-y: auto;"><code id="postman_json_code"></code></pre>
                                <button class="btn btn-sm btn-outline-light position-absolute top-0 end-0 m-2 copy-btn"
                                    data-target="postman_json_code"><i class="ti ti-copy"></i></button>
                            </div>
                        </div>
                        <div class="tab-pane" id="guide_nodejs">
                            <h6 class="fw-bold mb-2">Axios Example</h6>
                            <div class="position-relative">
                                <pre
                                    class="bg-dark text-white p-3 rounded small mb-0"><code id="nodejs_code" class="language-javascript"></code></pre>
                                <button class="btn btn-sm btn-outline-light position-absolute top-0 end-0 m-2 copy-btn"
                                    data-target="nodejs_code"><i class="ti ti-copy"></i></button>
                            </div>
                        </div>
                        <div class="tab-pane" id="guide_python">
                            <h6 class="fw-bold mb-2">Requests Example</h6>
                            <div class="position-relative">
                                <pre
                                    class="bg-dark text-white p-3 rounded small mb-0"><code id="python_code" class="language-python"></code></pre>
                                <button class="btn btn-sm btn-outline-light position-absolute top-0 end-0 m-2 copy-btn"
                                    data-target="python_code"><i class="ti ti-copy"></i></button>
                            </div>
                        </div>
                        <div class="tab-pane" id="guide_fields">
                            <div class="alert alert-info py-2 small mb-3">
                                <i class="ti ti-info-circle me-1"></i> <strong>System Fields</strong> are automatically
                                mapped to Lead properties. <strong>Custom Fields</strong> are saved if the key exactly
                                matches your box custom field column name.
                            </div>

                            <!-- Location ID Finder -->
                            <div class="card border mb-3 bg-light">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold mb-2 small"><i class="ti ti-map-pin me-1"></i> Location ID Finder
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label class="small fw-bold">Country</label>
                                            <select id="guide_country_select" class="form-select form-select-sm">
                                                <option value="">Select Country</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small fw-bold">State</label>
                                            <select id="guide_state_select" class="form-select form-select-sm" disabled>
                                                <option value="">Select State</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small fw-bold">City</label>
                                            <select id="guide_city_select" class="form-select form-select-sm" disabled>
                                                <option value="">Select City</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="location_id_display" class="mt-2 small text-primary fw-bold"
                                        style="display:none;">
                                        Selected ID: <span id="current_location_id_val"></span>
                                    </div>
                                </div>
                            </div>
                            <div style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-sm border table-hover">
                                    <thead class="bg-light sticky-top">
                                        <tr>
                                            <th>Field Key</th>
                                            <th>Label</th>
                                            <th>Type / Options</th>
                                        </tr>
                                    </thead>
                                    <tbody id="guide_fields_body">
                                        {{-- Populated via JS --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="guide_json_all">
                            <div class="alert alert-info py-2 small mb-3">
                                <i class="ti ti-info-circle me-1"></i> Copy this JSON to get a full list of all
                                <strong>IDs</strong> and <strong>Field Keys</strong> available for your box.
                            </div>
                            <div class="position-relative">
                                <pre class="bg-dark text-white p-3 rounded small mb-0"
                                    style="max-height: 500px; overflow-y: auto;"><code id="meta_json_code"></code></pre>
                                <button class="btn btn-sm btn-outline-light position-absolute top-0 end-0 m-2 copy-btn"
                                    data-target="meta_json_code"><i class="ti ti-copy"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirm_action_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="confirm_modal_title">Confirm Action</h5>
                    <button type="button" class="btn-close-custom bg-transparent border-0" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x fs-3"></i>
                    </button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="mb-3 text-warning">
                        <i class="ti ti-alert-triangle fs-1"></i>
                    </div>
                    <h5 id="confirm_modal_heading">Are you sure?</h5>
                    <p class="text-muted" id="confirm_modal_text"></p>
                </div>
                <div class="modal-footer border-top justify-content-center">
                    <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btn_confirm_action">Yes, Proceed</button>
                </div>
            </div>
        </div>
    </div>

    <!-- /Add API Modal -->
    <div class="modal fade" id="add_source_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Source</h5>
                    <button type="button" class="btn-close-custom bg-transparent border-0" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x fs-3"></i>
                    </button>
                </div>
                <form id="add_source_form">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Source Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                placeholder="e.g. Instagram, Website, Referral" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <input type="radio" class="form-check-input" id="source_active" name="status" value="1"
                                        checked>
                                    <label class="form-check-label ms-1" for="source_active">Active</label>
                                </div>
                                <div>
                                    <input type="radio" class="form-check-input" id="source_inactive" name="status"
                                        value="0">
                                    <label class="form-check-label ms-1" for="source_inactive">Inactive</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btn_save_source">Create Source</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        // Check and redirect from 127.0.0.1 to localhost BEFORE SDK loads
        // This prevents Facebook SDK from initializing with invalid domain
        (function () {
            if (window.location.hostname === '127.0.0.1') {
                const localhostUrl = window.location.href.replace('127.0.0.1', 'localhost');
                console.warn('⚠️ Facebook doesn\'t accept IP addresses (127.0.0.1) in App Domains.');
                console.warn('🔄 Redirecting to localhost for Facebook compatibility...');
                window.location.replace(localhostUrl); // Use replace() to avoid adding to history
                return;
            }
        })();

        window.fbAsyncInit = function () {
            // Only initialize SDK if on HTTPS (production or local HTTPS)
            // On HTTP (localhost), we'll use direct OAuth redirect instead
            const isHttps = window.location.protocol === 'https:';

            if (isHttps) {
                FB.init({
                    appId: '{{ config('services.facebook.client_id') }}',
                    cookie: true,
                    xfbml: true,
                    version: 'v19.0'
                });

                console.log('Facebook SDK initialized (HTTPS)');
            } else {
                console.log('Running on HTTP - Facebook SDK not initialized (will use OAuth redirect)');
            }

            // Debug: Log current domain
            console.log('Current domain:', window.location.hostname);
            console.log('Current URL:', window.location.href);
            console.log('Facebook App ID:', '{{ config('services.facebook.client_id') }}');
        };

        function launchFacebookLogin() {
            const appId = '{{ config('services.facebook.client_id') }}';
            const boxSlug = '{{ $box->slug ?? "" }}' || "{{ Request::segment(1) }}";
            localStorage.setItem('last_tenant_slug', boxSlug);
            localStorage.setItem('last_auth_url', window.location.href);
            
            // Use the STATIC callback URL whitelisted in Meta Dashboard
            const redirectUri = window.location.origin + '/auth/facebook/callback';
            
            // Pass boxSlug in state to maintain context
            const state = encodeURIComponent(`box_slug=${boxSlug}&display=popup`);
            
            const oauthUrl = `https://www.facebook.com/v22.0/dialog/oauth?client_id=${appId}&redirect_uri=${encodeURIComponent(redirectUri)}&state=${state}&scope=public_profile,email,pages_show_list,pages_read_engagement,pages_manage_metadata,pages_manage_ads,ads_management,leads_retrieval&auth_type=rerequest&response_type=code&display=popup`;

            const popup = window.open(oauthUrl, 'FacebookLogin', 'width=600,height=700');

            // Listen for message from the callback popup
            const messageHandler = function(event) {
                if (event.data.type === 'facebook-auth-success') {
                    window.removeEventListener('message', messageHandler);
                    alert('✓ Facebook connected successfully!');
                    window.location.reload();
                } else if (event.data.type === 'facebook-auth-error') {
                    window.removeEventListener('message', messageHandler);
                    alert('Facebook Error: ' + event.data.error);
                }
            };

            window.addEventListener('message', messageHandler);
        }

        // WhatsApp Business API Login Function
        function launchWhatsAppLogin() {
            const appId = '{{ config('services.facebook.client_id') }}';
            const configId = '{{ config('services.whatsapp.config_id') }}';
            const boxSlug = '{{ $box->slug ?? "" }}' || "{{ Request::segment(1) }}";
            localStorage.setItem('last_tenant_slug', boxSlug);
            localStorage.setItem('last_auth_url', window.location.href);
            
            // Use the STATIC callback URL whitelisted in Meta Dashboard
            const redirectUri = window.location.origin + '/auth/facebook/callback';
            
            // Pass boxSlug in state to maintain context
            const state = encodeURIComponent(`box_slug=${boxSlug}&display=popup&type=whatsapp`);
            
            const oauthUrl = `https://www.facebook.com/v22.0/dialog/oauth?client_id=${appId}&config_id=${configId}&redirect_uri=${encodeURIComponent(redirectUri)}&state=${state}&response_type=code&display=popup`;

            const popup = window.open(oauthUrl, 'WhatsAppLogin', 'width=600,height=700');

            // Listen for message from the callback popup
            const messageHandler = function(event) {
                if (event.data.type === 'facebook-auth-success') {
                    window.removeEventListener('message', messageHandler);
                    alert('✓ WhatsApp connected successfully!');
                    window.location.reload();
                } else if (event.data.type === 'facebook-auth-error') {
                    window.removeEventListener('message', messageHandler);
                    alert('WhatsApp Error: ' + event.data.error);
                }
            };

            window.addEventListener('message', messageHandler);
        }

        // Check Facebook Connection Status
        function checkFacebookConnection() {
            const url = '{{ route('facebook.checkConnection', ['box_slug' => request()->route('box_slug')]) }}';

            const btn = document.getElementById('check-connection-btn');
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking...';

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.connected) {
                            alert('✓ Facebook is Connected!\n\nToken is saved and ready to use.');
                        } else {
                            alert('⚠ Facebook is Not Connected\n\nPlease click "Configure" to connect your Facebook account.');
                        }
                    } else {
                        alert('Failed to check connection: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error checking connection:', error);
                    alert('An error occurred while checking connection status.');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
        }

        // Get Business Integration Details Function
        function getBusinessIntegration() {
            const url = '{{ route('facebook.businessIntegration', ['box_slug' => request()->route('box_slug')]) }}';

            const btn = document.getElementById('get-integration-btn');
            const originalHtml = btn.innerHTML;

            // Disable button and show loading
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Fetching...';

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const integration = data.data?.integration || data.data?.saved_in;
                        let message = 'Business Integration Details Saved Successfully!\n\n';
                        if (integration) {
                            message += 'Integration ID: ' + (integration.id || 'N/A') + '\n';
                            message += 'Name: ' + (integration.name || 'N/A') + '\n';
                            message += 'App ID: ' + (integration.app_id || 'N/A') + '\n';
                            if (integration.created_time) {
                                message += 'Created: ' + new Date(integration.created_time).toLocaleString() + '\n';
                            }
                        }
                        alert(message);
                    } else {
                        alert('Failed to get integration details: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error getting integration details:', error);
                    alert('An error occurred while getting integration details.');
                })
                .finally(() => {
                    // Re-enable button
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
        }

        // Sync Facebook Leads Function
        function syncFacebookLeads() {
            const syncUrl = '{{ route('facebook.sync-leads', ['box_slug' => request()->route('box_slug')]) }}';

            const btn = document.getElementById('sync-leads-btn');
            const originalHtml = btn.innerHTML;

            // Disable button and show loading
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Syncing...';

            fetch(syncUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Lead synchronization started in background!\n\nLeads will be imported to your CRM leads table shortly. You can check the progress on the Facebook Leads page.');
                        if (confirm('Would you like to view the Facebook Leads history page?')) {
                            window.location.href = '{{ route('facebook.leads', ['box_slug' => request()->route('box_slug')]) }}';
                        } else {
                            // Refresh integrations to update stats
                            fetchIntegrations();
                        }
                    } else {
                        alert('Failed to start sync: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error syncing leads:', error);
                    alert('An error occurred while starting lead synchronization.');
                })
                .finally(() => {
                    // Re-enable button
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
        }

        // Sync Facebook Forms Function
        function syncFacebookForms() {
            const syncUrl = '{{ route('facebook.sync-forms', ['box_slug' => request()->route('box_slug')]) }}';

            const btn = document.getElementById('sync-forms-btn');
            const originalHtml = btn.innerHTML;

            // Disable button and show loading
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Syncing...';

            fetch(syncUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Forms synced successfully!\n\nPages: ' + (data.data?.pages_count || 0) + '\nForms: ' + (data.data?.forms_count || 0) + '\n\nView all forms on the Facebook Forms page.');
                        // Optionally redirect to Facebook Forms page
                        const facebookFormsUrl = '{{ route('settings.integrations.facebook.form', ['box_slug' => request()->route('box_slug')]) }}';
                        if (confirm('Would you like to view all synced forms?')) {
                            window.location.href = facebookFormsUrl;
                        }
                    } else {
                        alert('Failed to sync forms: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error syncing forms:', error);
                    alert('An error occurred while syncing forms.');
                })
                .finally(() => {
                    // Re-enable button
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
        }

        // Check WhatsApp Connection Status
        function checkWhatsAppConnection() {
            const url = '{{ route('whatsapp.business.checkConnection', ['box_slug' => request()->route('box_slug')]) }}';

            const btn = document.getElementById('check-whatsapp-connection-btn');
            if (!btn) {
                console.error('Check WhatsApp connection button not found');
                return;
            }

            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking...';

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.connected) {
                            let message = '✓ WhatsApp Business is Connected!\n\n';
                            message += 'Total Accounts: ' + (data.data?.total_accounts || 0) + '\n';
                            message += 'Active Accounts: ' + (data.data?.active_accounts || 0) + '\n';
                            message += 'Connected Phones: ' + (data.data?.connected_phone_numbers || 0);
                            alert(message);
                        } else {
                            alert('⚠ WhatsApp Business is Not Connected\n\nPlease click "Configure" to connect your WhatsApp Business account.');
                        }
                    } else {
                        alert('Failed to check connection: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error checking WhatsApp connection:', error);
                    alert('An error occurred while checking connection status.');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
        }

        // Register WhatsApp Webhook
        function registerWhatsAppWebhook() {
            @if(isset($whatsappAccounts) && !empty($whatsappAccounts))
                // If multiple accounts, let user choose
                const accounts = @json($whatsappAccounts);
                if (accounts.length === 0) {
                    alert('No active WhatsApp Business accounts found. Please connect an account first.');
                    return;
                }

                let wabaId = accounts[0].waba_id; // Default to first account
                if (accounts.length > 1) {
                    const accountList = accounts.map((acc, idx) => `${idx + 1}. ${acc.name || acc.waba_id}`).join('\n');
                    const choice = prompt(`Multiple accounts found. Enter account number (1-${accounts.length}):\n\n${accountList}`);
                    if (choice && choice >= 1 && choice <= accounts.length) {
                        wabaId = accounts[choice - 1].waba_id;
                    } else {
                        return; // User cancelled
                    }
                }

                const webhookUrl = '{{ config('app.url') }}/webhook/whatsapp';
                const verifyToken = '{{ config('services.whatsapp.webhook_verify_token') ?: 'BizzWhatsAppWebhook2026' }}';

                if (!confirm(`Register webhook for account: ${accounts.find(a => a.waba_id === wabaId)?.name || wabaId}?\n\nWebhook URL: ${webhookUrl}\nVerify Token: ${verifyToken}`)) {
                    return;
                }

                const url = '{{ route('whatsapp.business.registerWebhook', ['box_slug' => request()->route('box_slug'), 'wabaId' => ':wabaId']) }}'.replace(':wabaId', wabaId);

                const btn = document.getElementById('register-webhook-btn');
                const originalHtml = btn.innerHTML;

                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Registering...';

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        webhook_url: webhookUrl,
                        verify_token: verifyToken
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('✓ Webhook registered successfully!\n\nYour webhook is now configured to receive WhatsApp Business events from Meta.\n\nMake sure to also configure the webhook in Meta Dashboard:\n1. Go to Meta for Developers → Your App → WhatsApp → Configuration\n2. Set Webhook URL: ' + webhookUrl + '\n3. Set Verify Token: ' + verifyToken + '\n4. Click "Verify and Save"');
                        } else {
                            alert('Failed to register webhook: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error registering webhook:', error);
                        alert('An error occurred while registering webhook.');
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    });
            @else
                alert('No active WhatsApp Business accounts found. Please connect an account first.');
            @endif
        }

        // Check WhatsApp Webhook Status
        function checkWhatsAppWebhookStatus() {
            @if(isset($whatsappAccounts) && !empty($whatsappAccounts))
                const accounts = @json($whatsappAccounts);
                if (accounts.length === 0) {
                    alert('No active WhatsApp Business accounts found.');
                    return;
                }

                let wabaId = accounts[0].waba_id; // Default to first account
                if (accounts.length > 1) {
                    const accountList = accounts.map((acc, idx) => `${idx + 1}. ${acc.name || acc.waba_id}`).join('\n');
                    const choice = prompt(`Multiple accounts found. Enter account number (1-${accounts.length}):\n\n${accountList}`);
                    if (choice && choice >= 1 && choice <= accounts.length) {
                        wabaId = accounts[choice - 1].waba_id;
                    } else {
                        return; // User cancelled
                    }
                }

                const url = '{{ route('whatsapp.business.checkWebhook', ['box_slug' => request()->route('box_slug'), 'wabaId' => ':wabaId']) }}'.replace(':wabaId', wabaId);

                const btn = document.getElementById('check-webhook-btn');
                const originalHtml = btn.innerHTML;

                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking...';

                fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const status = data.subscribed ? '✓ Connected' : '⚠ Not Connected';
                            let message = `Webhook Status: ${status}\n\n`;
                            message += `Account: ${accounts.find(a => a.waba_id === wabaId)?.name || wabaId}\n`;
                            message += `Message: ${data.message || 'N/A'}`;

                            if (data.data && data.data.subscribed_fields) {
                                message += `\n\nSubscribed Fields: ${data.data.subscribed_fields.join(', ')}`;
                            }

                            alert(message);
                        } else {
                            alert('Failed to check webhook status: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error checking webhook status:', error);
                        alert('An error occurred while checking webhook status.');
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    });
            @else
                alert('No active WhatsApp Business accounts found.');
            @endif
        }

        function syncGmailEmails() {
            let btn = $(event.target).closest('button');
            let originalHtml = btn.innerHTML;

            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Syncing...');

            const url = '{{ box_route('google.sync_emails') }}';

            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message || 'Sync failed');
                    }
                },
                error: function (xhr) {
                    let msg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred';
                    toastr.error(msg);
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="ti ti-refresh me-1"></i> Sync Emails Now');
                }
            });
        }

        // Manual Token Entry (Workaround for Facebook Login issues)
        function showManualTokenEntry() {
            const token = prompt('Enter WhatsApp Business Access Token:\n\n' +
                'To get your access token:\n' +
                '1. Go to Meta for Developers → Your App\n' +
                '2. Go to Tools → Graph API Explorer\n' +
                '3. Select your app and generate a User Access Token\n' +
                '4. Add permissions: whatsapp_business_management, whatsapp_business_messaging\n' +
                '5. Copy the generated token and paste it here\n\n' +
                'Note: This is a temporary workaround. For production, use OAuth flow.');

            if (!token || token.trim() === '') {
                return;
            }

            // Save token manually
            const saveUrl = '{{ route('settings.integrations.facebook.save', ['box_slug' => request()->route('box_slug')]) }}';

            // Create a fake authorization flow by directly saving the token
            fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    access_token: token.trim(),
                    type: 'whatsapp',
                    manual_entry: true
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✓ Token saved successfully!\n\nWhatsApp Business API is now connected.');
                        window.location.reload();
                    } else {
                        alert('Failed to save token: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error saving token:', error);
                    alert('An error occurred while saving token.');
                });
        }

        $(document).ready(function () {
            const boxSlug = "{{ Request::segment(1) }}";
            let globalData = null;
            let lastActiveIntegrationForm = null;

            fetchIntegrations();

            function fetchIntegrations() {
                $('#grid_loader').show();
                // Don't empty yet to avoid flickering if we are refreshing

                $.ajax({
                    url: "{{ box_route('settings.get.crm_integrations') }}",
                    type: "GET",
                    success: function (response) {
                        $('#grid_loader').hide();
                        if (response.data) {
                            globalData = response.data;
                            renderIntegrations(response.data);
                        }
                    },
                    error: function () {
                        $('#grid_loader').hide();
                        toastr.error('Failed to load integrations');
                    }
                });
            }

            function renderIntegrations(data) {
                let connectedContainer = $('#connected_integrations_grid');
                let availableContainer = $('#available_api_grid');

                connectedContainer.find('.col-md-6, .col-12:not(#grid_loader)').remove();
                availableContainer.empty();

                let hasConnected = false;

                // 1. Render Connected APIs on Main Page
                data.connected_apis.forEach(function (box_api) {
                    hasConnected = true;

                    // If it's gmail and has multiple accounts, split them into separate cards
                    if (box_api.api.system_name === 'gmail' && box_api.gmail_accounts && box_api.gmail_accounts.length > 0) {
                        box_api.gmail_accounts.forEach(function (acc) {
                            // Create a clone to avoid polluting other cards
                            let virtual_api = JSON.parse(JSON.stringify(box_api));
                            virtual_api.gmail_accounts = [acc]; // Only this account for this card
                            virtual_api.display_name_override = `Gmail (${acc.email})`;
                            connectedContainer.append(renderIntegrationCardHtml(virtual_api, data));
                        });
                    } else {
                        connectedContainer.append(renderIntegrationCardHtml(box_api, data));
                    }
                });

                // 2. Render Available APIs in Modal
                data.available_apis.forEach(function (api) {
                    let iconHtml = '';
                    if (api.icon_type === 'img') {
                        iconHtml = `<img src="{{ asset('') }}${api.icon}" alt="${api.name}" class="img-fluid mb-3" style="max-height: 70px;">`;
                    } else if (api.icon) {
                        let colorClass = api.system_name === 'facebook' ? 'text-primary' : (api.system_name === 'webhook' ? 'text-danger' : 'text-info');
                        iconHtml = `<i class="${api.icon} fa-4x ${colorClass} mb-3"></i>`;
                    }

                    let modalItemHtml = `
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm border text-center p-4">
                                ${iconHtml}
                                <h6 class="fw-bold mb-2">${api.name}</h6>
                                <div class="mt-auto">
                                    <button class="btn btn-success px-4 add-api-btn" data-id="${api.id}">Enable</button>
                                </div>
                            </div>
                        </div>
                    `;
                    availableContainer.append(modalItemHtml);
                });

                if (!hasConnected) {
                    connectedContainer.append('<div class="col-12 text-center py-5"><p class="text-muted">No APIs connected yet. Click "Add New" to get started.</p></div>');
                }

                // Re-initialize Select2 for new elements
                if ($('.select2').length > 0) {
                    $('.select2').select2({ width: '100%' });
                }
                if ($('.select2-tags').length > 0) {
                    $('.select2-tags').select2({
                        width: '100%',
                        tags: true,
                        tokenSeparators: [',', ' '],
                        placeholder: 'Type and press enter...'
                    });
                }
            }

            function renderIntegrationCardHtml(box_api, data) {
                let api = box_api.api;
                let is_enabled = (box_api.status === 'Enabled');

                let iconHtml = '';
                if (api.icon_type === 'img') {
                    iconHtml = `<img src="{{ asset('') }}${api.icon}" alt="${api.name}" class="img-fluid" style="max-height: 30px;">`;
                } else if (api.icon) {
                    let colorClass = api.system_name === 'facebook' ? 'text-primary' : (api.system_name === 'webhook' ? 'text-danger' : 'text-info');
                    iconHtml = `<i class="${api.icon} ${colorClass}"></i>`;
                }

                let robinBadge = '';
                if (box_api.params && box_api.params.round_robin == '1') {
                    let userCount = (box_api.users || []).length;
                    robinBadge = `<span class="badge bg-soft-info text-info border-info ms-2" title="Round Robin Enabled">
                                    <i class="ti ti-users me-1"></i> ${userCount}
                                  </span>`;
                }

                let gmailBadge = '';
                // Only show gmail badge (account count) if it's NOT a split-account card
                if (!box_api.display_name_override && api.system_name === 'gmail' && box_api.gmail_accounts && box_api.gmail_accounts.length > 0) {
                    gmailBadge = `<span class="badge bg-soft-danger text-danger border-danger ms-2">
                                    <i class="fa-brands fa-google me-1"></i> ${box_api.gmail_accounts.length}
                                  </span>`;
                }

                let cardContent = '';
                let headerAction = '';

                if (is_enabled) {
                    let statusColor = (box_api.connection_status === 'Connected') ? 'text-success' : 'text-danger';
                    let statusText = box_api.connection_status || 'Disconnected';
                    headerAction = `<span class="${statusColor} fw-bold">${statusText}</span>`;
                    cardContent = renderDetailMode(api, box_api, data);
                } else {
                    let lastChecked = box_api.last_checked_date || '-';
                    let lastLead = box_api.last_lead_date || '-';
                    let errorLog = box_api.error_log ? `<div class="alert alert-danger py-1 px-2 small mt-2 mb-0"><i class="ti ti-alert-triangle me-1"></i> ${box_api.error_log}</div>` : '';

                    let bigIconHtml = '';
                    if (api.icon_type === 'img') {
                        bigIconHtml = `<img src="{{ asset('') }}${api.icon}" alt="${api.name}" class="img-fluid mb-3" style="max-height: 80px;">`;
                    } else if (api.icon) {
                        let colorClass = api.system_name === 'facebook' ? 'text-primary' : (api.system_name === 'webhook' ? 'text-danger' : 'text-info');
                        bigIconHtml = `<i class="${api.icon} fa-5x ${colorClass} mb-4"></i>`;
                    }

                    cardContent = `
                        <div class="text-center py-4">
                            ${bigIconHtml}
                            <div class="mb-4">
                                <button class="btn btn-success px-4 toggle-status-btn" data-id="${box_api.id}" data-status="Enabled">Enable</button>
                            </div>
                            <div class="text-muted small">
                                <div class="mb-1"><i class="ti ti-history me-1"></i>Last checked on : ${lastChecked}</div>
                                <div><i class="ti ti-calendar-event me-1"></i>Last Lead on : ${lastLead}</div>
                            </div>
                            ${errorLog}
                        </div>
                    `;
                }

                let displayName = box_api.display_name_override || api.name;

                return `
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 border-top-primary shadow-sm">
                            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fs-4">${iconHtml}</span>
                                    <h5 class="card-title mb-0 text-dark">${displayName} ${robinBadge} ${gmailBadge}</h5>
                                </div>
                                ${headerAction}
                            </div>
                            <div class="card-body p-4">
                                ${cardContent}
                            </div>
                        </div>
                    </div>
                `;
            }

            function renderDetailMode(api, box_api, data) {
                let baseUrl = "{{ url('/') }}";
                let params = box_api.params || {};
                // Set defaults if not set
                if (params.round_robin === undefined) params.round_robin = '0';
                if (params.enable_shifts === undefined) params.enable_shifts = 'No';

                let webhookFields = '';
                if (api.system_name === 'facebook') {
                    let leadStats = box_api.lead_stats || { total: 0, processed: 0, pending: 0 };
                    let crmStats = box_api.crm_stats || { assigned: [], statuses: [] };
                    
                    let assignedHtml = '';
                    if (crmStats.assigned && crmStats.assigned.length > 0) {
                        assignedHtml = crmStats.assigned.map(a => `
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">${a.assigned_to ? a.assigned_to.name : 'Unknown'}</span>
                                <span class="badge bg-soft-info text-info">${a.count}</span>
                            </div>
                        `).join('');
                    } else {
                        assignedHtml = '<div class="text-muted small">No leads assigned yet.</div>';
                    }

                    let statusHtml = '';
                    if (crmStats.statuses && crmStats.statuses.length > 0) {
                        statusHtml = crmStats.statuses.map(s => `
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">${s.lead_status ? s.lead_status.name : 'No Status'}</span>
                                <span class="badge bg-soft-secondary text-secondary">${s.count}</span>
                            </div>
                        `).join('');
                    }

                    webhookFields = `
                        <div class="alert alert-soft-primary py-2 small mb-3 border-primary">
                            <i class="fa-brands fa-facebook me-1"></i> Connected to Facebook for Leadgen Sync.
                        </div>

                        <!-- Lead Stats Summary -->
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body p-2">
                                <div class="row g-0 text-center">
                                    <div class="col-4 border-end">
                                        <div class="fw-bold text-dark">${leadStats.total}</div>
                                        <div class="text-muted" style="font-size: 10px;">TOTAL</div>
                                    </div>
                                    <div class="col-4 border-end">
                                        <div class="fw-bold text-success">${leadStats.processed}</div>
                                        <div class="text-muted" style="font-size: 10px;">SYNCED</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="fw-bold text-warning">${leadStats.pending}</div>
                                        <div class="text-muted" style="font-size: 10px;">PENDING</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        ${box_api.facebook_accounts && box_api.facebook_accounts.length > 0 ? `
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Connected Accounts (${box_api.facebook_accounts.length})</label>
                                <div class="list-group list-group-flush border rounded">
                                    ${box_api.facebook_accounts.map(acc => `
                                        <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-soft-primary rounded-circle me-3" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fa-brands fa-facebook-f text-primary fs-5"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark mb-0 small">${acc.name}</div>
                                                    <div class="text-muted" style="font-size: 10px;">
                                                        ID: ${acc.facebook_id}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-soft-success text-success border border-success-300 px-2 py-1" style="font-size: 9px;">Connected</span>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        ` : ''}

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Quick Actions</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="checkFacebookConnection()">
                                        <i class="ti ti-refresh me-1"></i> Check Status
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-primary btn-sm w-100" id="sync-leads-btn" onclick="syncFacebookLeads()">
                                        <i class="ti ti-cloud-download me-1"></i> Sync Leads
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-info btn-sm w-100" id="sync-forms-btn" onclick="syncFacebookForms()">
                                        <i class="ti ti-forms me-1"></i> Sync Forms
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="getBusinessIntegration()">
                                        <i class="ti ti-briefcase me-1"></i> Business Data
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Assignment & Status Summary -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark small mb-1">Assigned To</label>
                                <div class="border rounded p-2 bg-white" style="max-height: 100px; overflow-y: auto;">
                                    ${assignedHtml}
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark small mb-1">Lead Status</label>
                                <div class="border rounded p-2 bg-white" style="max-height: 100px; overflow-y: auto;">
                                    ${statusHtml || '<div class="text-muted small">No status info.</div>'}
                                </div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold text-dark small">Account Management</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-soft-primary btn-sm w-50" onclick="launchFacebookLogin()">
                                    <i class="fa-brands fa-facebook me-1"></i> Reconnect
                                </button>
                                <button type="button" class="btn btn-soft-danger btn-sm w-50" onclick="disconnectFacebook()">
                                    <i class="ti ti-plug-off me-1"></i> Disconnect
                                </button>
                            </div>
                        </div>
                    `;
                } else if (api.system_name === 'gmail') {
                    webhookFields = `
                        <div class="alert alert-soft-danger py-2 small mb-3 border-danger">
                            <i class="fa-brands fa-google me-1"></i> Gmail Integration Active.
                        </div>

                        ${box_api.gmail_accounts && box_api.gmail_accounts.length > 0 ? `
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">${box_api.display_name_override ? 'Connected Account' : 'Connected Accounts (' + box_api.gmail_accounts.length + ')'}</label>
                                <div class="list-group list-group-flush border rounded">
                                    ${box_api.gmail_accounts.map(acc => `
                                        <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-soft-danger rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fa-solid fa-envelope text-danger fs-4"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark mb-0">${acc.email}</div>
                                                    <div class="text-muted" style="font-size: 11px;">
                                                        <i class="ti ti-history me-1"></i>Last Synced: ${acc.last_sync_at || 'Never'}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="badge bg-soft-success text-success border border-success-300 px-2 py-1">Active</span>
                                                <button type="button" class="btn btn-icon btn-lg btn-soft-danger rounded-circle" onclick="disconnectGmail(${acc.id}, '${acc.email}')" title="Disconnect Account" style="width: 42px; height: 42px;">
                                                    <i class="ti ti-x fs-3"></i>
                                                </button>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        ` : ''}

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Quick Actions</label>
                            <div class="row g-2">
                                <div class="col-12">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="syncGmailEmails()">
                                        <i class="ti ti-refresh me-1"></i> Sync Emails Now
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Account Management</label>
                            <a href="{{ box_route('google.connect.box') }}" class="btn btn-soft-danger btn-sm w-100">
                                <i class="fa-brands fa-google me-1"></i> Reconnect / Change Account
                            </a>
                        </div>
                    `;
                } else if (api.system_name === 'webhook') {
                    webhookFields = `
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Source Name</label>
                            <input type="text" name="params[source_name]" class="form-control" value="${params.source_name || 'Website Query'}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Endpoint</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light" value="${baseUrl}/api/webhook/${params.webhook_slug || box_api.id}" readonly id="endpoint_${box_api.id}">
                                <button class="btn btn-outline-secondary copy-btn" type="button" data-target="endpoint_${box_api.id}"><i class="ti ti-copy"></i></button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Authorization Bearer Token</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light token-input" value="${box_api.token || ''}" readonly id="token_${box_api.id}">
                                <button class="btn btn-outline-secondary copy-btn" type="button" data-target="token_${box_api.id}"><i class="ti ti-copy"></i></button>
                                <button class="btn btn-info btn-sm text-white regenerate-token-btn" type="button" data-id="${box_api.id}">Regenerate</button>
                            </div>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                            <button type="button" class="btn btn-outline-success btn-sm flex-fill test-webhook-btn" 
                                data-id="${box_api.id}"
                                data-endpoint="${baseUrl}/api/webhook/${box_api.id}" 
                                data-token="${box_api.token}">
                                <i class="ti ti-activity me-1"></i> Test Connection
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm flex-fill show-guide-btn" 
                                data-endpoint="${baseUrl}/api/webhook/${box_api.id}" 
                                data-token="${box_api.token}">
                                <i class="ti ti-book me-1"></i> Integration Guide
                            </button>
                        </div>
                    `;

                } else {
                    let apiParams = api.params || {};
                    if (typeof apiParams === 'string') {
                        try {
                            apiParams = JSON.parse(apiParams);
                        } catch (e) {
                            console.error('Error parsing JSON for API params:', e);
                            apiParams = {};
                        }
                    }
                    Object.keys(apiParams).forEach(key => {
                        webhookFields += `
                            <div class="mb-3">
                                <label class="form-label fw-semibold">${apiParams[key]}</label>
                                <input type="text" name="params[${key}]" class="form-control" value="${params[key] || ''}" placeholder="${apiParams[key]}">
                            </div>
                        `;
                    });
                }

                let sourceOptions = data.sources.map(s => `<option value="${s.id}" ${params.lead_source_id == s.id ? 'selected' : ''}>${s.name}</option>`).join('');
                let userOptions = data.users.map(u => `<option value="${u.id}" ${params.assigned_to == u.id ? 'selected' : ''}>${u.name} (${u.email})</option>`).join('');
                let stageOptions = data.contactStages.map(s => `<option value="${s.id}">${s.name}</option>`).join('');

                // For multi-selects, we need to handle selected state differently
                let autoTrashOptions = data.contactStages.map(s => {
                    let selected = (params.auto_trash || []).map(String).includes(String(s.id)) ? 'selected' : '';
                    return `<option value="${s.id}" ${selected}>${s.name}</option>`;
                }).join('');

                let autoUnassignedOptions = data.contactStages.map(s => {
                    let selected = (params.auto_unassigned || []).map(String).includes(String(s.id)) ? 'selected' : '';
                    return `<option value="${s.id}" ${selected}>${s.name}</option>`;
                }).join('');

                return `
                    <form class="integration-form" data-id="${box_api.id}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        ${webhookFields}


                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fw-semibold mb-0">Lead Source</label>
                                <a href="javascript:void(0);" class="text-danger small fw-bold" data-bs-toggle="modal" data-bs-target="#add_source_modal">
                                    <i class="ti ti-square-rounded-plus me-1"></i>Add Source
                                </a>
                            </div>
                            <select name="params[lead_source_id]" class="form-select select2 lead-source-select">
                                <option value="">Select Source</option>
                                ${sourceOptions}
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Round Robin Lead Allocation</label>
                            <select name="params[round_robin]" class="form-select round-robin-toggle">
                                <option value="1" ${params.round_robin == '1' ? 'selected' : ''}>Yes</option>
                                <option value="0" ${params.round_robin == '0' ? 'selected' : ''}>No</option>
                            </select>
                        </div>

                        <div class="mb-3 direct-assign-container" style="${params.round_robin == '1' ? 'display:none;' : ''}">
                            <label class="form-label fw-semibold">Assigned To (Direct)</label>
                            <select name="params[assigned_to]" class="form-select select2">
                                <option value="">Select User</option>
                                ${userOptions}
                            </select>
                            <div class="small text-muted">Used when Round Robin is disabled</div>
                        </div>

                        <div class="round-robin-fields-container" style="${params.round_robin == '1' ? '' : 'display:none;'}">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Round Robin Users <span class="badge bg-info ms-1 robin-count-badge">${(params.round_robin_users || []).length} Selected</span></label>
                                <select name="params[round_robin_users][]" class="form-select select2 robin-users-select" multiple="multiple">
                                    ${data.users.map(u => {
                    let selected = (params.round_robin_users || []).map(String).includes(String(u.id)) ? 'selected' : '';
                    return `<option value="${u.id}" ${selected}>${u.name}</option>`;
                }).join('')}
                                </select>
                            </div>

                            <!-- Round Robin Shifts Section -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Enable Round Robin Shifts</label>
                                <select name="params[enable_shifts]" class="form-select enable-shifts-toggle">
                                    <option value="No" ${params.enable_shifts == 'No' ? 'selected' : ''}>No</option>
                                    <option value="Yes" ${params.enable_shifts == 'Yes' ? 'selected' : ''}>Yes</option>
                                </select>
                            </div>

                            <div class="shifts-container mb-4" style="${params.enable_shifts == 'Yes' ? '' : 'display:none;'}">
                                <h6 class="fw-bold mb-3">User Shifts Configuration</h6>
                                <div class="shifts-list">
                                    ${(params.round_robin_users || []).map(userId => {
                    let user = data.users.find(u => u.id == userId);
                    if (!user) return '';
                    let shift = (params.shifts || {})[userId] || { start: '09:00', end: '18:00', is_default: false };
                    return `
                                            <div class="card border mb-2 user-shift-row" data-user-id="${userId}">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="fw-bold text-primary">${user.name} (${user.email})</span>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="params[shifts][${userId}][is_default]" value="1" ${shift.is_default ? 'checked' : ''} id="default_${userId}">
                                                            <label class="form-check-label small" for="default_${userId}">Default</label>
                                                        </div>
                                                    </div>
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <label class="form-label small mb-1">Start Time</label>
                                                            <input type="time" name="params[shifts][${userId}][start]" class="form-control form-control-sm" value="${shift.start}">
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label small mb-1">End Time</label>
                                                            <input type="time" name="params[shifts][${userId}][end]" class="form-control form-control-sm" value="${shift.end}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                }).join('')}
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lead Status</label>
                            <select name="params[lead_status_id]" class="form-select select2">
                                <option value="">Select Status</option>
                                ${data.contactStages.map(s => `<option value="${s.id}" ${params.lead_status_id == s.id ? 'selected' : ''}>${s.name}</option>`).join('')}
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Auto Trash</label>
                            <select name="params[auto_trash][]" class="form-select select2-tags" multiple="multiple">
                                <option value="Buy Lead" ${(params.auto_trash || []).includes('Buy Lead') ? 'selected' : ''}>Buy Lead</option>
                                <option value="Call Lead" ${(params.auto_trash || []).includes('Call Lead') ? 'selected' : ''}>Call Lead</option>
                                <option value="Direct Lead" ${(params.auto_trash || []).includes('Direct Lead') ? 'selected' : ''}>Direct Lead</option>
                                <option value="Catalog-views" ${(params.auto_trash || []).includes('Catalog-views') ? 'selected' : ''}>Catalog-views</option>
                                <option value="WhatsApp Enquiries" ${(params.auto_trash || []).includes('WhatsApp Enquiries') ? 'selected' : ''}>WhatsApp Enquiries</option>
                                ${(params.auto_trash || []).filter(word => !['Buy Lead', 'Call Lead', 'Direct Lead', 'Catalog-views', 'WhatsApp Enquiries'].includes(word) && isNaN(word)).map(word => `<option value="${word}" selected>${word}</option>`).join('')}
                            </select>
                            <div class="small text-muted">Select lead types or type custom words to automatically trash.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Has Words</label>
                            <select name="params[has_words][]" class="form-select select2-tags" multiple="multiple">
                                ${(params.has_words || []).map(word => `<option value="${word}" selected>${word}</option>`).join('')}
                            </select>
                            <div class="small text-muted">Type custom words and press Enter to add. Only leads matching these will be imported.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Auto Unassigned</label>
                            <select name="params[auto_unassigned][]" class="form-select select2-tags" multiple="multiple">
                                <option value="Buy Lead" ${(params.auto_unassigned || []).includes('Buy Lead') ? 'selected' : ''}>Buy Lead</option>
                                <option value="Call Lead" ${(params.auto_unassigned || []).includes('Call Lead') ? 'selected' : ''}>Call Lead</option>
                                <option value="Direct Lead" ${(params.auto_unassigned || []).includes('Direct Lead') ? 'selected' : ''}>Direct Lead</option>
                                <option value="Catalog-views" ${(params.auto_unassigned || []).includes('Catalog-views') ? 'selected' : ''}>Catalog-views</option>
                                <option value="WhatsApp Enquiries" ${(params.auto_unassigned || []).includes('WhatsApp Enquiries') ? 'selected' : ''}>WhatsApp Enquiries</option>
                                ${(params.auto_unassigned || []).filter(word => !['Buy Lead', 'Call Lead', 'Direct Lead', 'Catalog-views', 'WhatsApp Enquiries'].includes(word) && isNaN(word)).map(word => `<option value="${word}" selected>${word}</option>`).join('')}
                            </select>
                            <div class="small text-muted">Select lead types or type custom words to remain unassigned.</div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success px-4 save-integration-btn">Save</button>
                            <button type="button" class="btn btn-danger px-4 disconnect-btn" data-api-id="${box_api.id}">Disable</button>
                        </div>

                        <div class="mt-4 pt-3 border-top text-center text-muted small fst-italic">
                            <div class="mb-1"><i class="ti ti-history me-1"></i>Last checked on : ${box_api.last_checked_date || '-'}</div>
                            <div><i class="ti ti-calendar-event me-1"></i>Last Lead on : ${box_api.last_lead_date || '-'}</div>
                        </div>

                        ${box_api.error_log ? `
                            <div class="alert alert-soft-danger py-2 px-3 small mt-3 mb-0 border-danger d-flex align-items-center">
                                <i class="ti ti-alert-circle fs-5 me-2"></i>
                                <div>
                                    <strong class="d-block mb-1">API Issue Detected:</strong>
                                    ${box_api.error_log}
                                </div>
                            </div>
                        ` : ''}
                    </form>
                `;
            }

            // Add API (from Modal)
            $(document).on('click', '.add-api-btn', function () {
                let btn = $(this);
                let apiId = btn.data('id');
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Adding...');

                $.ajax({
                    url: "{{ box_route('settings.add.api') }}",
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}", api_id: apiId },
                    success: function (response) {
                        toastr.success('API added. Please configure and enable it.');
                        $('#add_api_modal').modal('hide');
                        fetchIntegrations();
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).text('Enable');
                        let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Operation failed';
                        toastr.error(msg);
                    }
                });
            });

            // Toggle Status (on Main Page)
            $(document).on('click', '.toggle-status-btn', function () {
                let btn = $(this);
                let apiId = btn.data('id');
                let status = btn.data('status');

                if (status === 'Disabled') {
                    showConfirm(
                        'Disable Integration',
                        'Disable Integration?',
                        'Are you sure you want to disable this integration? It will stop receiving leads immediately.',
                        'Yes, Disable',
                        'disable',
                        { btn: btn, apiId: apiId }
                    );
                    return;
                }

                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

                $.ajax({
                    url: "{{ box_route('settings.toggle.crm_integration') }}",
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}", api_id: apiId, status: status },
                    success: function (response) {
                        toastr.success('Status updated to ' + status);
                        fetchIntegrations();
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).text(status);
                        let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Operation failed';
                        toastr.error(msg);
                    }
                });
            });




            // Save Integration Settings
            $(document).on('submit', '.integration-form', function (e) {
                e.preventDefault();
                let form = $(this);
                let btn = form.find('.save-integration-btn');
                let id = form.data('id');

                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

                $.ajax({
                    url: "{{ box_route('settings.update.crm_integration') }}",
                    type: "POST",
                    data: form.serialize() + '&id=' + id,
                    success: function (response) {
                        btn.prop('disabled', false).text('Save');
                        toastr.success(response.message);
                    },
                    error: function () {
                        btn.prop('disabled', false).text('Save');
                        toastr.error('Failed to save settings');
                    }
                });
            });

            // Confirm Modal Logic
            let confirmAction = null;
            let pendingData = {};

            function showConfirm(title, heading, text, btnText, action, data = {}) {
                confirmAction = action;
                pendingData = data;
                $('#confirm_modal_title').text(title);
                $('#confirm_modal_heading').text(heading);
                $('#confirm_modal_text').text(text);
                $('#btn_confirm_action').text(btnText).prop('disabled', false).removeClass('btn-danger').addClass('btn-primary');

                if (action === 'disable') {
                    $('#btn_confirm_action').removeClass('btn-primary').addClass('btn-danger');
                }

                $('#confirm_action_modal').modal('show');
            }

            $('#btn_confirm_action').on('click', function () {
                let btn = $(this);
                if (confirmAction === 'disable') {
                    executeDisable(btn);
                } else if (confirmAction === 'regenerate') {
                    executeRegenerate(btn);
                } else if (confirmAction === 'disconnect_gmail') {
                    executeDisconnectGmail(btn);
                } else if (confirmAction === 'disconnect_facebook') {
                    executeDisconnectFacebook(btn);
                }
            });

            // Disconnect API Handler
            $(document).on('click', '.disconnect-btn', function () {
                let btn = $(this);
                showConfirm(
                    'Disable Integration',
                    'Disable Integration?',
                    'Are you sure you want to disable this integration? It will stop receiving leads immediately.',
                    'Yes, Disable',
                    'disable',
                    { btn: btn, apiId: btn.data('api-id') }
                );
            });

            function executeDisable(modalBtn) {
                modalBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Disabling...');

                $.ajax({
                    url: "{{ box_route('settings.toggle.crm_integration') }}",
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}", api_id: pendingData.apiId, status: 'Disabled' },
                    success: function (response) {
                        toastr.success('Disabled successfully');
                        $('#confirm_action_modal').modal('hide');
                        fetchIntegrations();
                    },
                    error: function () {
                        modalBtn.prop('disabled', false).text('Yes, Disable');
                        toastr.error('Operation failed');
                    }
                });
            }

            window.disconnectGmail = function (id, email) {
                showConfirm(
                    'Disconnect Gmail',
                    'Disconnect Account?',
                    'Are you sure you want to disconnect ' + email + '? You will no longer receive leads from this account.',
                    'Yes, Disconnect',
                    'disconnect_gmail',
                    { id: id }
                );
            };

            function executeDisconnectGmail(modalBtn) {
                modalBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Disconnecting...');

                $.ajax({
                    url: "{{ url('/') }}/" + boxSlug + "/google/disconnect/" + pendingData.id,
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        toastr.success(response.message || 'Disconnected successfully');
                        $('#confirm_action_modal').modal('hide');
                        // Fetch integrations to refresh the view
                        fetchIntegrations();
                    },
                    error: function (xhr) {
                        modalBtn.prop('disabled', false).text('Yes, Disconnect');
                        let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Operation failed';
                        toastr.error(msg);
                    }
                });
            }

            window.disconnectFacebook = function () {
                showConfirm(
                    'Disconnect Facebook',
                    'Disconnect Account?',
                    'Are you sure you want to disconnect Facebook? You will no longer receive leads from Facebook Leadgen.',
                    'Yes, Disconnect',
                    'disconnect_facebook',
                    {}
                );
            };

            function executeDisconnectFacebook(modalBtn) {
                modalBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Disconnecting...');

                $.ajax({
                    url: '{{ route('facebook.disconnect', ['box_slug' => request()->route('box_slug')]) }}',
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        toastr.success(response.message || 'Disconnected successfully');
                        $('#confirm_action_modal').modal('hide');
                        // Reload to reflect changes
                        window.location.reload();
                    },
                    error: function (xhr) {
                        modalBtn.prop('disabled', false).text('Yes, Disconnect');
                        let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Operation failed';
                        toastr.error(msg);
                    }
                });
            }

            // Regenerate Token Handler
            $(document).on('click', '.regenerate-token-btn', function () {
                let btn = $(this);
                showConfirm(
                    'Regenerate Token',
                    'Regenerate Token?',
                    'This will invalidate the existing token immediately. External services using the current token will stop working.',
                    'Yes, Regenerate',
                    'regenerate',
                    {
                        btn: btn,
                        id: btn.data('id'),
                        input: btn.closest('.mb-3').find('.token-input')
                    }
                );
            });

            // Track which integration form is opening the Add Source modal
            $(document).on('click', '[data-bs-target="#add_source_modal"]', function () {
                lastActiveIntegrationForm = $(this).closest('form');
            });

            // Add Source Handle
            $('#add_source_form').on('submit', function (e) {
                e.preventDefault();
                let btn = $('#btn_save_source');
                let form = $(this);
                let name = form.find('input[name="name"]').val().trim();
                let status = form.find('input[name="status"]:checked').val();

                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Creating...');

                $.ajax({
                    url: "{{ route('store.source', ['box_slug' => request()->route('box_slug')]) }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        name: name,
                        type: 'source',
                        status: status
                    },
                    success: function (response) {
                        btn.prop('disabled', false).text('Create Source');
                        if (response.success) {
                            toastr.success(response.message || 'Source created successfully');
                            $('#add_source_modal').modal('hide');
                            form[0].reset();

                            // Update globalData and all lead-source-select dropdowns
                            if (response.data && response.data.id) {
                                const newSource = { id: response.data.id, name: response.data.name };
                                if (globalData && globalData.sources) {
                                    globalData.sources.push(newSource);
                                }

                                $('.lead-source-select').each(function () {
                                    let select = $(this);
                                    let isSourceForThisForm = lastActiveIntegrationForm && lastActiveIntegrationForm.length > 0 && select.closest('form')[0] === lastActiveIntegrationForm[0];

                                    if (isSourceForThisForm) {
                                        // Add and select the new source for the active form
                                        let newOption = new Option(newSource.name, newSource.id, true, true);
                                        select.append(newOption).trigger('change');

                                        // If not using Select2, we still need to set the value explicitly
                                        if (!select.hasClass('select2-hidden-accessible')) {
                                            select.val(newSource.id);
                                        }
                                    } else {
                                        // Just add the new source as an option for other forms
                                        let newOption = new Option(newSource.name, newSource.id, false, false);
                                        select.append(newOption);
                                        // If Select2 is active, we don't necessarily need to trigger change unless we want to refresh UI
                                        if (select.hasClass('select2-hidden-accessible')) {
                                            select.trigger('change');
                                        }
                                    }
                                });

                                // Automatically save the integration if we know which one was being edited
                                if (lastActiveIntegrationForm && lastActiveIntegrationForm.length > 0) {
                                    setTimeout(() => {
                                        lastActiveIntegrationForm.trigger('submit');
                                    }, 100);
                                }
                            } else {
                                // If response doesn't have data, just refresh all integrations to be sure
                                fetchIntegrations();
                            }
                        }
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).text('Create Source');
                        let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to create source';
                        toastr.error(msg);
                    }
                });
            });

            function executeRegenerate(modalBtn) {
                modalBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Regenerating...');
                pendingData.btn.prop('disabled', true);

                $.ajax({
                    url: "{{ box_route('settings.regenerate.crm_token') }}",
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}", id: pendingData.id },
                    success: function (response) {
                        modalBtn.prop('disabled', false).text('Yes, Regenerate');
                        pendingData.btn.prop('disabled', false);
                        pendingData.input.val(response.token);
                        $('#confirm_action_modal').modal('hide');
                        toastr.success(response.message);
                    },
                    error: function () {
                        modalBtn.prop('disabled', false).text('Yes, Regenerate');
                        pendingData.btn.prop('disabled', false);
                        toastr.error('Regeneration failed');
                    }
                });
            }
            // Toggle Round Robin Fields
            $(document).on('change', '.round-robin-toggle', function () {
                let val = $(this).val();
                let form = $(this).closest('form');
                if (val === '1') {
                    form.find('.direct-assign-container').hide();
                    form.find('.round-robin-fields-container').show();
                } else {
                    form.find('.direct-assign-container').show();
                    form.find('.round-robin-fields-container').hide();
                }
            });

            // Toggle Shifts Container
            $(document).on('change', '.enable-shifts-toggle', function () {
                let val = $(this).val();
                let container = $(this).closest('form').find('.shifts-container');
                if (val === 'Yes') {
                    container.slideDown();
                } else {
                    container.slideUp();
                }
            });

            // Dynamic Shifts List when users are selected
            $(document).on('change', '.robin-users-select', function () {
                let select = $(this);
                let selectedIds = select.val() || [];
                let form = select.closest('form');
                let listContainer = form.find('.shifts-list');

                // Update badge count
                form.find('.robin-count-badge').text(selectedIds.length + ' Selected');

                // Remove rows that are no longer selected
                listContainer.find('.user-shift-row').each(function () {
                    let userId = $(this).data('user-id').toString();
                    if (!selectedIds.includes(userId)) {
                        $(this).remove();
                    }
                });

                // Add rows for new selections
                selectedIds.forEach(userId => {
                    if (listContainer.find(`.user-shift-row[data-user-id="${userId}"]`).length === 0) {
                        let user = globalData.users.find(u => u.id == userId);
                        if (user) {
                            let newRow = `
                                <div class="card border mb-2 user-shift-row" data-user-id="${userId}">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold text-primary">${user.name} (${user.email})</span>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="params[shifts][${userId}][is_default]" value="1" id="default_${userId}">
                                                <label class="form-check-label small" for="default_${userId}">Default</label>
                                            </div>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label small mb-1">Start Time</label>
                                                <input type="time" name="params[shifts][${userId}][start]" class="form-control form-control-sm" value="09:00">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small mb-1">End Time</label>
                                                <input type="time" name="params[shifts][${userId}][end]" class="form-control form-control-sm" value="18:00">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            listContainer.append(newRow);
                        }
                    }
                });
            });

            // Test Webhook Connection Logic
            $(document).on('click', '.test-webhook-btn', function () {
                let btn = $(this);
                let endpoint = btn.data('endpoint');
                let token = btn.data('token');
                let origHtml = btn.html();
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Pinging...');

                $.ajax({
                    url: endpoint,
                    type: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({ ping: true }),
                    success: function (res) {
                        btn.prop('disabled', false).html(origHtml);
                        if (res.success) {
                            toastr.success(res.message || 'Webhook is verified and ready!');
                            fetchIntegrations();
                        } else {
                            toastr.warning(res.message || 'Webhook check returned an issue.');
                        }
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).html(origHtml);
                        let msg = xhr.responseJSON ? (xhr.responseJSON.message || xhr.responseJSON.error || JSON.stringify(xhr.responseJSON.errors)) : 'Webhook test failed';
                        toastr.error(msg);
                    }
                });
            });

            // Show Guide Logic
            $(document).on('click', '.show-guide-btn', function () {
                let endpoint = $(this).data('endpoint');
                let token = $(this).data('token');

                // Set cURL JSON
                $('#curl_json_code').text(`curl --location '${endpoint}' \\
    --header 'Content-Type: application/json' \\
    --header 'Authorization: Bearer ${token}' \\
    --data '{
        "name": "Test Lead",
        "phone": "9876543210",
        "email": "lead@example.com",
        "query": "Test inquiry from website form"
    }'`);

                // Set cURL Form
                $('#curl_form_code').text(`curl --location '${endpoint}' \\
    --header 'Authorization: Bearer ${token}' \\
    --form 'name="Test Lead"' \\
    --form 'phone="9876543210"' \\
    --form 'email="lead@example.com"' \\
    --form 'query="Test inquiry from website form"'`);

                // Set Postman JSON
                let postmanObj = {
                    "info": {
                        "name": "Lead Webhook Integration",
                        "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
                    },
                    "item": [{
                        "name": "Push Lead (Full Payload)",
                        "request": {
                            "method": "POST",
                            "header": [
                                { "key": "Authorization", "value": "Bearer " + token, "type": "text" },
                                { "key": "Content-Type", "value": "application/json", "type": "text" }
                            ],
                            "body": {
                                "mode": "raw",
                                "raw": JSON.stringify({
                                    "name": "John",
                                    "email": "shahad1932@gmal.com"
                                }, null, 4)
                            },
                            "url": { "raw": endpoint }
                        }
                    }]
                };

                $('#postman_json_code').text(JSON.stringify(postmanObj, null, 4));

                // Set Node.js Code
                $('#nodejs_code').text(`const axios = require('axios');
    let data = JSON.stringify({
      "name": "John",
      "email": "shahad1932@gmal.com"
    });

    let config = {
      method: 'post',
      maxBodyLength: Infinity,
      url: '${endpoint}',
      headers: { 
        'Content-Type': 'application/json', 
        'Authorization': 'Bearer ${token}'
      },
      data : data
    };

    axios.request(config)
    .then((response) => {
      console.log(JSON.stringify(response.data));
    })
    .catch((error) => {
      console.log(error);
    });`);

                // Set Python Code
                $('#python_code').text(`import requests
    import json

    url = "${endpoint}"

    payload = json.dumps({
      "name": "John",
      "email": "shahad1932@gmal.com"
    })
    headers = {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer ${token}'
    }

    response = requests.request("POST", url, headers=headers, data=payload)

    print(response.text)`);

                // Render Fields Table
                let fieldsBody = $('#guide_fields_body');
                fieldsBody.empty();
                if (globalData && globalData.system_fields) {
                    globalData.system_fields.forEach(f => {
                        let typeDisplay = `<span class="badge bg-light text-dark border">${f.field_type}</span>`;

                        // Show options for select/radio fields
                        if (['select', 'radio'].includes(f.field_type)) {
                            let options = [];
                            if (f.field === 'lead_status_id' && globalData.contactStages) {
                                options = globalData.contactStages.map(s => `ID: ${s.id} (${s.name})`);
                            } else if (f.field === 'lead_sub_status_id' && globalData.subStatuses) {
                                options = globalData.subStatuses.map(s => `ID: ${s.id} (${s.name})`);
                            } else if (f.field === 'lead_source_id' && globalData.sources) {
                                options = globalData.sources.map(s => `ID: ${s.id} (${s.name})`);
                            } else if (f.field === 'industry' && globalData.industries) {
                                options = globalData.industries.map(s => `ID: ${s.id} (${s.name})`);
                            } else if (f.field === 'country_id' && globalData.countries) {
                                options = globalData.countries.map(c => `ID: ${c.id} (${c.name})`);
                            } else if (f.field === 'state_id' && globalData.states) {
                                options = globalData.states.map(s => `ID: ${s.id} (${s.name})`);
                            } else if (f.field === 'city_id' && globalData.cities) {
                                options = globalData.cities.map(c => `ID: ${c.id} (${c.name})`);
                            } else if (f.options && Array.isArray(f.options)) {
                                options = f.options.map(o => `Value: ${o.value} (${o.label})`);
                            }

                            if (options.length > 0) {
                                typeDisplay += `<div class="mt-1 small text-muted font-monospace" style="font-size: 11px; line-height: 1.2;">
                                                 ${options.join('<br>')}
                                               </div>`;
                            }
                        }

                        fieldsBody.append(`
                            <tr>
                                <td><code>${f.field}</code></td>
                                <td>${f.name}</td>
                                <td>${typeDisplay}</td>
                            </tr>
                        `);
                    });
                }
                fieldsBody.append(`
                    <tr class="table-secondary py-1 text-center">
                        <td colspan="3"><small class="fw-bold">CUSTOM FIELDS</small></td>
                    </tr>
                `);

                if (globalData.custom_fields && globalData.custom_fields.length > 0) {
                    globalData.custom_fields.forEach(cf => {
                        let typeDisplay = `<span class="badge bg-light text-dark border">${cf.type}</span>`;
                        if (cf.params && cf.params.options) {
                            let cfOptions = cf.params.options.map(o => `Value: ${o.value} (${o.label})`);
                            if (cfOptions.length > 0) {
                                typeDisplay += `<div class="mt-1 small text-muted font-monospace" style="font-size: 11px; line-height: 1.2;">
                                                 ${cfOptions.join('<br>')}
                                               </div>`;
                            }
                        }

                        fieldsBody.append(`
                            <tr>
                                <td><code>${cf.col_name}</code></td>
                                <td>${cf.name}</td>
                                <td>${typeDisplay}</td>
                            </tr>
                        `);
                    });
                } else {
                    fieldsBody.append(`
                        <tr>
                             <td colspan="3" class="text-center text-muted small">No custom fields found.</td>
                        </tr>
                    `);
                }

                // Population of Location Selects
                let countrySelect = $('#guide_country_select');
                countrySelect.empty().append('<option value="">Select Country</option>');
                if (globalData.countries) {
                    globalData.countries.forEach(c => {
                        countrySelect.append(`<option value="${c.id}">${c.name}</option>`);
                    });
                }
                $('#guide_state_select').empty().append('<option value="">Select State</option>').prop('disabled', true);
                $('#guide_city_select').empty().append('<option value="">Select City</option>').prop('disabled', true);
                $('#location_id_display').hide();

                // Generate Metadata JSON
                let metaJson = {
                    box_slug: boxSlug,
                    api_endpoint: endpoint,
                    api_token: token,
                    fields: globalData.system_fields.map(f => ({
                        key: f.field,
                        label: f.name,
                        type: f.field_type
                    })),
                    custom_fields: globalData.custom_fields ? globalData.custom_fields.map(cf => ({
                        key: cf.col_name,
                        label: cf.name,
                        type: cf.type
                    })) : [],
                    status_ids: globalData.contactStages ? globalData.contactStages.map(s => ({ id: s.id, name: s.name })) : [],
                    sub_status_ids: globalData.subStatuses ? globalData.subStatuses.map(s => ({ id: s.id, name: s.name })) : [],
                    source_ids: globalData.sources ? globalData.sources.map(s => ({ id: s.id, name: s.name })) : [],
                    industry_ids: globalData.industries ? globalData.industries.map(s => ({ id: s.id, name: s.name })) : [],
                    countries: globalData.countries ? globalData.countries.map(c => ({ id: c.id, name: c.name })) : [],
                    states: globalData.states ? globalData.states.map(s => ({ id: s.id, name: s.name })) : [],
                    cities: globalData.cities ? globalData.cities.map(c => ({ id: c.id, name: c.name })) : []
                };
                $('#meta_json_code').text(JSON.stringify(metaJson, null, 4));

                $('#webhook_guide_modal').modal('show');
            });

            // Location Finder Handlers
            $(document).on('change', '#guide_country_select', function () {
                let countryId = $(this).val();
                let stateSelect = $('#guide_state_select');
                let citySelect = $('#guide_city_select');

                stateSelect.empty().append('<option value="">Loading...</option>').prop('disabled', true);
                citySelect.empty().append('<option value="">Select City</option>').prop('disabled', true);
                $('#location_id_display').show().find('#current_location_id_val').text('Country ID: ' + countryId);

                if (!countryId) {
                    stateSelect.empty().append('<option value="">Select State</option>');
                    $('#location_id_display').hide();
                    return;
                }

                $.get("{{ box_route('settings.get.states') }}", { country_id: countryId }, function (res) {
                    stateSelect.empty().append('<option value="">Select State</option>').prop('disabled', false);
                    if (res.success && res.data) {
                        res.data.forEach(s => {
                            stateSelect.append(`<option value="${s.id}">${s.name}</option>`);
                        });
                    }
                }).fail(function () {
                    stateSelect.empty().append('<option value="">Error loading</option>');
                });
            });

            $(document).on('change', '#guide_state_select', function () {
                let stateId = $(this).val();
                let citySelect = $('#guide_city_select');

                citySelect.empty().append('<option value="">Loading...</option>').prop('disabled', true);
                $('#location_id_display').show().find('#current_location_id_val').text('State ID: ' + stateId);

                if (!stateId) {
                    citySelect.empty().append('<option value="">Select City</option>');
                    return;
                }

                $.get("{{ box_route('settings.get.cities') }}", { state_id: stateId }, function (res) {
                    citySelect.empty().append('<option value="">Select City</option>').prop('disabled', false);
                    if (res.success && res.data) {
                        res.data.forEach(c => {
                            citySelect.append(`<option value="${c.id}">${c.name}</option>`);
                        });
                    }
                }).fail(function () {
                    citySelect.empty().append('<option value="">Error loading</option>');
                });
            });

            $(document).on('change', '#guide_city_select', function () {
                let cityId = $(this).val();
                if (cityId) {
                    $('#location_id_display').show().find('#current_location_id_val').text('City ID: ' + cityId);
                }
            });

            // Copy to clipboard
            $(document).on('click', '.copy-btn', function () {
                let targetId = $(this).data('target');
                let element = document.getElementById(targetId);
                let textToCopy = '';

                if (element) {
                    if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
                        element.select();
                        element.setSelectionRange(0, 99999); // For mobile devices
                        textToCopy = element.value;
                    } else {
                        textToCopy = element.textContent || element.innerText;
                    }

                    navigator.clipboard.writeText(textToCopy).then(function () {
                        toastr.info('Copied to clipboard');
                    }).catch(function (err) {
                        // Fallback for older browsers or if permission denied
                        console.error('Copy failed', err);
                        toastr.error('Failed to copy');
                    });
                }
            });

            // Sync Legacy APIs
            $(document).on('click', '#sync-legacy-apis-btn', function() {
                const btn = $(this);
                const originalHtml = btn.html();
                btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Syncing...');
                btn.prop('disabled', true);

                $.ajax({
                    url: '{{ route("website.settings.sync_legacy_apis", ["box_slug" => $box->slug ?? request()->segment(1)]) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if(data.success) {
                            toastr.success(data.message);
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            toastr.error(data.message || 'An error occurred during sync.');
                            btn.html(originalHtml);
                            btn.prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        toastr.error('A network error occurred.');
                        btn.html(originalHtml);
                        btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>

@endpush