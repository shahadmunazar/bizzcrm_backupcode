@extends('layouts.main')
@section('title', 'WhatsApp Create Project')
@section('content')

    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content">

            <div class="row">
                <div class="col-md-12">
                    
                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col-4">
                                <h4 class="page-title">Create Project</h4>
                            </div>
                            <div class="col-8 text-end">
                                <div class="head-icons">
                                    <a href="javascript:void(0);" onclick="window.location.reload();"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-original-title="Refresh">
                                        <i class="ti ti-refresh-dot"></i>
                                    </a>
                                    <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-original-title="Collapse" id="collapse-header">
                                        <i class="ti ti-chevrons-up"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Page Header -->

                    <!-- Status Bar -->
                    <div class="card border-0 mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                                <div class="d-flex align-items-center mb-0">
                                    <span class="avatar avatar-md bg-success-light me-2">
                                        <i class="ti ti-brand-whatsapp fs-40 text-success"></i>
                                    </span>
                                    <div>
                                        <p class="mb-0">WhatsApp Business API</p>
                                        <h5 class="mb-0">WhatsApp Business Accounts and Phone Numbers</h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center flex-wrap row-gap-2">
                                    <div class="me-3">
                                        <span class="badge @if(count($accounts) > 0) bg-success-light text-success @else bg-warning-light text-warning @endif">
                                            <i class="ti ti-circle-filled fs-8 me-1"></i>
                                            @if(count($accounts) > 0) Connected @else Action Required @endif
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button onclick="syncFromMeta()" class="btn btn-outline-primary d-flex align-items-center" id="sync-btn">
                                            <i class="ti ti-refresh me-2"></i>Sync from Meta
                                        </button>
                                        <a href="javascript:void(0);" onclick="launchWhatsAppLogin()"
                                            class="btn btn-primary d-flex align-items-center">
                                            <i class="ti ti-plus me-2"></i>Connect More Accounts
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Create Project Form -->
                        <div class="col-md-6">
                            <div class="card flex-fill h-100">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Launch New Project</h4>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-4">One Business Project is associated with one WhatsApp Business API Number. This helps you segregate campaigns and audiences.</p>
                                    <form id="create-project-form">
                                        <div class="mb-3">
                                            <label class="form-label">Project Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg" id="project_name" placeholder="e.g. Pharma Marketing 2024" required>
                                        </div>

                                        <div class="d-grid shadow-sm">
                                            <button type="submit" class="btn btn-lg btn-success" id="btn-create-project">
                                                <i class="ti ti-rocket me-1"></i> Create Project
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Guide / Multi Account Info -->
                        <div class="col-md-6">
                            <div class="card flex-fill h-100">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Multi-Account Management</h4>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex mb-4">
                                        <div class="avatar avatar-sm bg-info-light text-info rounded-circle flex-shrink-0 me-3">
                                            <i class="ti ti-layers-intersect fs-20"></i>
                                        </div>
                                        <div>
                                            <h6>Aggregate Multiple Portfolios</h6>
                                            <p class="text-muted small">You can connect multiple Facebook Portfolios and WhatsApp Business Accounts to ZipZapGo.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-4">
                                        <div class="avatar avatar-sm bg-purple-light text-purple rounded-circle flex-shrink-0 me-3">
                                            <i class="ti ti-device-mobile fs-20"></i>
                                        </div>
                                        <div>
                                            <h6>Direct Phone Mapping</h6>
                                            <p class="text-muted small">Each project can be mapped to a specific phone number from your connected WABA accounts.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info border-0 shadow-none mb-0">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-info-circle fs-20 me-2 text-info"></i>
                                            <p class="mb-0 text-dark small">To connect a new portfolio, use the <strong>Connect More</strong> button above. All numbers within that portfolio will automatically become available for projects.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Connected Accounts Table -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">All Connected WhatsApp Accounts</h4>
                                    <span class="badge bg-soft-primary" id="total-accounts-badge">{{ count($accounts) }} Total</span>
                                </div>
                                <div class="card-body">
                                    <div class="" id="accounts-table-container">
                                        <div id="accounts-loader" class="text-center p-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                        <div class="row d-none" id="accounts-grid">
                                            <!-- Cards will be injected here -->
                                        </div>

                                        <div id="accounts-empty-state" class="text-center p-5 d-none">
                                            <img src="{{ asset('assets/img/icons/empty-box.svg') }}" alt="Empty" class="mb-3" style="width: 80px; opacity: 0.5;">
                                            <h5 class="text-muted">No accounts connected yet</h5>
                                            <p class="text-muted small mb-3">Connect your first WhatsApp Business Account to start creating projects.</p>
                                            <button onclick="launchWhatsAppLogin()" class="btn btn-primary">
                                                <i class="fa-brands fa-facebook me-1"></i> Connect Now
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Created Projects Table -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">Created Projects</h4>
                                    <span class="badge bg-soft-primary" id="total-projects-badge">0 Total</span>
                                </div>
                                <div class="card-body p-0">
                                        <div class="row d-none p-3" id="projects-grid">
                                            <!-- Project cards will be injected here -->
                                        </div>
                                        <div id="projects-empty-state" class="text-center p-5 d-none">
                                            <img src="{{ asset('assets/img/icons/empty-box.svg') }}" alt="Empty" class="mb-3" style="width: 80px; opacity: 0.5;">
                                            <h5 class="text-muted">No projects created yet</h5>
                                            <p class="text-muted small mb-0">Use the form above to create your first WhatsApp project.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <!-- /Page Wrapper -->

@endsection

@push('scripts')
<script>
    function launchWhatsAppLogin() {
        if (window.location.hostname === '127.0.0.1') {
            const localhostUrl = window.location.href.replace('127.0.0.1', 'localhost');
            console.warn('⚠️ Still on 127.0.0.1, redirecting to localhost...');
            window.location.replace(localhostUrl);
            return;
        }

        const appId = '{{ config('services.facebook.client_id') }}';
        const boxSlug = '{{ $box->slug ?? "" }}' || "{{ Request::segment(1) }}";
        
        // Save context for callback redirection
        localStorage.setItem('last_tenant_slug', boxSlug);
        localStorage.setItem('last_auth_url', window.location.href);
        
        const saveUrl = '{{ route('settings.integrations.facebook.save', ['box_slug' => $box->slug ?? ':box_slug']) }}'.replace(':box_slug', boxSlug);
        
        const currentUrl = window.location.href;
        const redirectUriLocal = '{{ route("facebook.callback.auth") }}';

        const connectUrl = '{{ route("facebook.connect") }}' + 
            '?box_slug=' + encodeURIComponent(boxSlug) +
            '&current_url=' + encodeURIComponent(currentUrl) +
            '&type=whatsapp' +
            '&redirect_uri=' + encodeURIComponent(redirectUriLocal);
        
        console.log('Opening WhatsApp Business OAuth popup:', connectUrl);

        const popup = window.open(
            connectUrl,
            'WhatsApp Business Login',
            'width=600,height=700,scrollbars=yes,resizable=yes,left=' + (screen.width/2 - 300) + ',top=' + (screen.height/2 - 350)
        );

        if (!popup || popup.closed || typeof popup.closed == 'undefined') {
            alert('Popup was blocked. Please allow popups and try again.');
            return;
        }
        
        const messageHandler = function(event) {
            if (event.data.type === 'facebook-auth-success') {
                window.removeEventListener('message', messageHandler);
                if (popup && !popup.closed) popup.close();
                
                const { code, redirect_uri } = event.data;
                
                fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        code: code,
                        type: 'whatsapp',
                        redirect_uri: redirect_uri || redirectUriLocal
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success('Connected successfully!');
                        setTimeout(function() { window.location.reload(); }, 1500);
                    } else {
                        toastr.error('Connection failed: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error saving integration:', error);
                    toastr.error('An error occurred.');
                });
            }
        };

        window.addEventListener('message', messageHandler);
    }

    // Avatar Fallback Logic
    document.addEventListener('error', function (event) {
        if (event.target.tagName.toLowerCase() !== 'img') return;
        
        const img = event.target;
        if (img.classList.contains('no-fallback')) return;

        const name = img.getAttribute('alt') || 'User';
        const initials = name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2) || 'WA';
        
        const colors = ['#007bff', '#6610f2', '#6f42c1', '#e83e8c', '#dc3545', '#fd7e14', '#ffc107', '#28a745', '#20c997', '#17a2b8'];
        const colorIndex = name.length % colors.length;
        const color = colors[colorIndex];

        const canvas = document.createElement('canvas');
        canvas.width = 100;
        canvas.height = 100;
        const ctx = canvas.getContext('2d');
        
        ctx.fillStyle = color;
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 50px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(initials, canvas.width / 2, canvas.height / 2);
        
        img.src = canvas.toDataURL();
        img.onerror = null; 
    }, true);

    function syncFromMeta() {
        const btn = $('#sync-btn');
        const originalHtml = btn.html();
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Syncing...');
        
        const url = "{{ box_route('whatsapp.sync_meta') }}";
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Re-fetch accounts to update the table
                    fetchAccounts();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                console.error('Sync error:', xhr);
                toastr.error('An error occurred during sync.');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    }

    $(document).ready(function() {
        // Initial Fetch
        fetchAccounts();

        // Handle Create Project Form Submission
        $('#create-project-form').on('submit', function(e) {
            e.preventDefault();
            
            const btn = $('#btn-create-project');
            const originalHtml = btn.html();
            const projectName = $('#project_name').val();
            
            if (!projectName.trim()) {
                toastr.error('Project Name is required.');
                return;
            }

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Creating...');
            
            $.ajax({
                url: "{{ box_route('whatsapp.store_project') }}",
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    name: projectName
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#project_name').val('');
                        // Re-fetch to update projects table
                        fetchAccounts();
                    } else {
                        toastr.error(response.message || 'Failed to create project.');
                    }
                },
                error: function(xhr) {
                    console.error('Error creating project:', xhr);
                    toastr.error('An error occurred while creating the project.');
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalHtml);
                }
            });
        });
    });

    function fetchAccounts() {
            $.ajax({
                url: "{{ box_route('whatsapp.create_project') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        let accounts = response.accounts || [];
                        let phoneNumbers = response.phoneNumbers || [];
                        let projects = response.projects || [];
                        window.allAccounts = accounts;

                        
                        $('#accounts-loader').addClass('d-none');
                        $('#total-accounts-badge').text(accounts.length + ' Total');

                        if (accounts.length === 0) {
                            $('#accounts-empty-state').removeClass('d-none');
                            $('#accounts-grid').addClass('d-none');
                        } else {
                            $('#accounts-empty-state').addClass('d-none');
                            $('#accounts-grid').removeClass('d-none');
                            
                            let cardsHtml = '';
                            accounts.forEach(function(account) {
                                let statusBadge = account.is_active 
                                    ? '<span class="badge bg-success-light text-success"><span class="dot-status bg-success me-1"></span> CONNECTED</span>'
                                    : '<span class="badge bg-warning-light text-warning"><span class="dot-status bg-warning me-1"></span> INACTIVE</span>';
                                
                                let accountPhones = phoneNumbers.filter(p => p.target_id === account.id);
                                let phonesHtml = '';
                                
                                if (accountPhones.length > 0) {
                                    accountPhones.forEach(function(phone) {
                                        let nameStatus = phone.name_status ? phone.name_status + ' : ' : '';
                                        phonesHtml += `
                                            <span class="badge bg-outline-dark mb-1 me-1" data-bs-toggle="tooltip" title="ID: ${phone.whatsapp_id || ''}">
                                                <i class="fa-brands fa-whatsapp me-1 text-success"></i><?php /*${nameStatus}*/ ?>${phone.display_phone_number}
                                            </span>
                                        `;
                                    });
                                } else {
                                    phonesHtml = '<span class="text-muted small">No numbers found</span>';
                                }
                                
                                let portfolioName = account.portfolio ? account.portfolio.name : 'N/A';

                                cardsHtml += `
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card shadow-sm h-100 border border-light">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="avatar avatar-md bg-success text-white rounded me-3 d-flex align-items-center justify-content-center">
                                                        <i class="fa-brands fa-whatsapp fs-25"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="fw-bold text-dark mb-0">${account.name || 'Unnamed Account'}</h5>
                                                        <small class="text-muted">WABA: ${account.waba_id}</small>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="fw-bold fs-15 mb-1">Portfolio</div>
                                                    <div class="fw-medium">${portfolioName}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="fw-bold fs-15 mb-1">Status</div>
                                                    <div>${statusBadge}</div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold fs-15 mb-1">Available Numbers</div>
                                                    <div class="d-flex flex-wrap">
                                                        ${phonesHtml}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            
                            $('#accounts-grid').html(cardsHtml);
                            
                            // Reinitialize tooltips dynamically generated
                            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                                return new bootstrap.Tooltip(tooltipTriggerEl);
                            });
                        }

                        // Render Projects
                        $('#total-projects-badge').text(projects.length + ' Total');
                        if (projects.length === 0) {
                            $('#projects-empty-state').removeClass('d-none');
                            $('#projects-grid').addClass('d-none');
                        } else {
                            $('#projects-empty-state').addClass('d-none');
                            $('#projects-grid').removeClass('d-none');
                            
                            let projectsHtml = '';
                            projects.forEach(function(project) {
                                let statusBadge = project.is_active 
                                    ? '<span class="badge bg-success-light text-success">ACTIVE</span>'
                                    : '<span class="badge bg-secondary-light text-secondary">INACTIVE</span>';
                                    
                                let createdAt = new Date(project.created_at).toLocaleDateString('en-US', {
                                    year: 'numeric', month: 'short', day: 'numeric'
                                });


                                projectsHtml += `
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card shadow-sm h-100 border border-light">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="avatar avatar-md bg-primary text-white rounded me-3 d-flex align-items-center justify-content-center">
                                                        <i class="ti ti-folder fs-3"></i>
                                                    </div>
                                                    <div class="overflow-hidden">
                                                        <h3 class="fw-bold text-dark mb-0 text-truncate">${project.name}</h3>
                                                        ${project.account 
                                                            ? `<small class="text-muted text-truncate d-block">Account: ${project.account.name}</small>`
                                                            : `<div class="mt-1">
                                                                <select class="form-select form-select-sm" onchange="linkProjectAccount(${project.id}, this.value)">
                                                                    <option value="">Link Account...</option>
                                                                    ${(window.allAccounts || []).map(acc => `<option value="${acc.id}">${acc.name}</option>`).join('')}
                                                                </select>
                                                               </div>`
                                                        }
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                                    <small class="text-muted"><i class="ti ti-calendar me-1"></i>${createdAt}</small>
                                                    ${statusBadge}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            
                            $('#projects-grid').html(projectsHtml);
                        }
                    }
                },
                error: function(xhr) {
                    $('#accounts-loader').addClass('d-none');
                    console.error("Failed to load accounts via AJAX", xhr);
                }
            });
    }

    function linkProjectAccount(projectId, accountId) {
        if (!accountId) return;
        
        $.ajax({
            url: "{{ box_route('whatsapp.link_account') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                project_id: projectId,
                whatsapp_business_account_id: accountId
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    fetchAccounts();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Failed to link account.');
            }
        });
    }
</script>
<style>
    .dot-status { height: 8px; width: 8px; border-radius: 50%; display: inline-block; }
    .bg-outline-info { border: 1px solid #0dcaf0; color: #0dcaf0; background: transparent; }
    .bg-soft-primary { background-color: rgba(78, 115, 223, 0.1); color: #4e73df; }
</style>
@endpush