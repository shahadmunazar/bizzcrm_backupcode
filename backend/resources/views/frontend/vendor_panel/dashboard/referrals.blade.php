@extends('layouts.vendor')

@section('title', 'Vendor Referrals & Invitations')

@section('content')
<style>
.stat-card {
    border: none;
    border-radius: 14px;
    transition: transform 0.2s, box-shadow 0.2s;
    background: #ffffff;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08) !important;
}
.code-badge {
    background: #eef2ff;
    color: #4338ca;
    font-family: 'Courier New', Courier, monospace;
    font-weight: 700;
    font-size: 1.35rem;
    padding: 0.5rem 1.25rem;
    border-radius: 8px;
    letter-spacing: 1px;
    border: 1px dashed #6366f1;
    display: inline-block;
}
.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #64748b;
    font-size: 0.95rem;
    font-weight: 600;
    padding: 0.85rem 1.25rem;
    transition: all 0.2s ease-in-out;
}
.nav-tabs .nav-link:hover {
    color: #3b82f6;
    border-bottom-color: #cbd5e1;
}
.nav-tabs .nav-link.active {
    color: #2563eb;
    border-bottom-color: #2563eb;
    background: transparent;
}
.filter-btn.active {
    background-color: #2563eb !important;
    color: #ffffff !important;
    border-color: #2563eb !important;
}
</style>

<div class="page-wrapper">
    <div class="content">
        <!-- Notification Alert Container (Replacing Toasts) -->
        <div id="vendorAlertContainer" class="mb-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="ti ti-check me-2 fs-18"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="ti ti-alert-circle me-2 fs-18"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                    <i class="ti ti-alert-triangle me-2 fs-18"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        <!-- Page Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold m-0 text-dark">Vendor Referral Program</h3>
                <p class="text-muted small m-0">Invite vendors to join {{ config('app.name') }}. All invitations and registrations are tracked in real time.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary d-flex align-items-center" id="btnRefreshAll">
                    <i class="ti ti-refresh me-1 fs-18"></i> Refresh Data
                </button>
                <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#shareEmailModal">
                    <i class="ti ti-mail-forward me-1 fs-18"></i> Invite Vendor via Email
                </button>
            </div>
        </div>

        <!-- Top Stat Cards -->
        <div class="row g-4 mb-4">
            <!-- Referral Code & Share Link -->
            <div class="col-xl-6 col-lg-12">
                <div class="card stat-card shadow-sm h-100 p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small fw-semibold text-uppercase mb-2">Unique Referral Identifier</span>
                            <h5 class="fw-bold mb-1">Referral Code & Direct Link</h5>
                        </div>
                        <i class="ti ti-gift fs-28 text-primary"></i>
                    </div>

                    @if($vendor->referral_code)
                        <div class="mb-3 d-flex flex-wrap align-items-center gap-3">
                            <span class="code-badge" id="referralCodeText">{{ $vendor->referral_code }}</span>
                            <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" id="btnCopyCode">
                                <i class="ti ti-copy me-1"></i> Copy Code
                            </button>
                        </div>

                        <label class="form-label small text-muted fw-semibold mb-1">Shareable Vendor Registration Link</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control bg-light" id="referralUrlInput" value="{{ $referralUrl }}" readonly>
                            <button class="btn btn-primary fw-semibold" type="button" id="btnCopyLink">
                                <i class="ti ti-link me-1"></i> Copy Link
                            </button>
                            <button class="btn btn-outline-secondary fw-semibold" type="button" data-bs-toggle="modal" data-bs-target="#shareEmailModal">
                                <i class="ti ti-send me-1"></i> Share via Email
                            </button>
                        </div>

                        <p class="text-muted small mb-0">
                            <i class="ti ti-info-circle text-primary me-1"></i> Anyone signing up through this link or entering this code will automatically register as a <strong>Vendor Partner</strong> under your referral account.
                        </p>
                    @else
                        <div class="alert alert-warning border-0 p-3 mb-3 d-flex align-items-center">
                            <i class="ti ti-clock-hour-4 fs-28 text-warning me-3"></i>
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">Referral Code Pending Approval</h6>
                                <p class="mb-0 text-muted small">Your unique vendor referral code and sharing link will be generated after your account is approved by the Super Admin.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Joined Vendor Partners Count -->
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card shadow-sm h-100 p-4 border-start border-success border-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-uppercase small fw-bold text-muted">Registered Vendors</span>
                            <h2 class="fw-bold my-2 display-6 text-success" id="totalReferredCount">{{ $referredVendorsCount }}</h2>
                        </div>
                        <div class="p-2 bg-success-subtle rounded-3 text-success">
                            <i class="ti ti-users-group fs-24"></i>
                        </div>
                    </div>
                    <p class="small text-muted mb-3">Vendor partners who completed signup via your referral.</p>
                    <div class="mt-auto pt-2 border-top">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small fw-semibold">
                            <i class="ti ti-circle-check me-1"></i> Joined Network
                        </span>
                    </div>
                </div>
            </div>

            <!-- Sent Email Invitations Count -->
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card shadow-sm h-100 p-4 border-start border-primary border-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-uppercase small fw-bold text-muted">Sent Invitations</span>
                            <h2 class="fw-bold my-2 display-6 text-primary" id="totalInvitationsCount">{{ $invitationsCount ?? 0 }}</h2>
                        </div>
                        <div class="p-2 bg-primary-subtle rounded-3 text-primary">
                            <i class="ti ti-mail-share fs-24"></i>
                        </div>
                    </div>
                    <p class="small text-muted mb-2">Total referral email invitations dispatched.</p>
                    <div class="mt-auto pt-2 border-top d-flex gap-2">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small fw-semibold" id="joinedInvitationsBadge">
                            <span id="joinedInvitationsCountText">{{ $joinedInvitationsCount ?? 0 }}</span> Joined
                        </span>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 small fw-semibold" id="pendingInvitationsBadge">
                            <span id="pendingInvitationsCountText">{{ $pendingInvitationsCount ?? 0 }}</span> Pending
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Referral Management Tabs Card -->
        <div class="card stat-card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom pt-3 pb-0 px-4">
                <ul class="nav nav-tabs card-header-tabs border-bottom-0" id="referralTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-joined-btn" data-bs-toggle="tab" data-bs-target="#pane-joined" type="button" role="tab" aria-controls="pane-joined" aria-selected="true">
                            <i class="ti ti-users-group me-1 fs-18 text-success"></i> Joined Vendor Partners
                            <span class="badge bg-success rounded-pill ms-2" id="tabJoinedCount">{{ $referredVendorsCount }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-invitations-btn" data-bs-toggle="tab" data-bs-target="#pane-invitations" type="button" role="tab" aria-controls="pane-invitations" aria-selected="false">
                            <i class="ti ti-mail-share me-1 fs-18 text-primary"></i> Sent Email Invitations & Status
                            <span class="badge bg-primary rounded-pill ms-2" id="tabInvitationsCount">{{ $invitationsCount ?? 0 }}</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="referralTabsContent">
                    <!-- Tab 1: Joined Vendor Partners -->
                    <div class="tab-pane fade show active" id="pane-joined" role="tabpanel" aria-labelledby="tab-joined-btn">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                            <div>
                                <h5 class="fw-bold m-0 text-dark">Joined Vendor Accounts</h5>
                                <p class="text-muted small m-0">Live directory of vendor partner accounts that registered using your referral code.</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="text" id="registeredSearchInput" class="form-control form-control-sm" placeholder="Search vendors..." style="max-width: 220px;">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnRefreshReferrals">
                                    <i class="ti ti-refresh me-1"></i> Refresh
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="referralsTable">
                                <thead class="table-light text-muted small text-uppercase">
                                    <tr>
                                        <th class="ps-3 py-3">Vendor / Company</th>
                                        <th class="py-3">Contact Person</th>
                                        <th class="py-3">Email</th>
                                        <th class="py-3">Phone</th>
                                        <th class="py-3">Verification</th>
                                        <th class="py-3">Vendor Status</th>
                                        <th class="py-3 text-end rounded-end pe-3">Joined Date</th>
                                    </tr>
                                </thead>
                                <tbody id="referralsTableBody">
                                    <tr><td colspan="7" class="text-center py-4 text-muted">Loading registered vendors...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 2: Sent Email Invitations -->
                    <div class="tab-pane fade" id="pane-invitations" role="tabpanel" aria-labelledby="tab-invitations-btn">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3">
                            <div>
                                <h5 class="fw-bold m-0 text-dark">Sent Referral Invitations & Activity</h5>
                                <p class="text-muted small m-0">All shared referral links sent via email. Real-time status tracks who has joined or is pending signup.</p>
                            </div>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <div class="btn-group btn-group-sm" role="group" aria-label="Status Filter">
                                    <button type="button" class="btn btn-outline-secondary filter-btn active" data-filter="all">
                                        All (<span id="filterAllCount">0</span>)
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="joined">
                                        Joined (<span id="filterJoinedCount">0</span>)
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="pending">
                                        Pending (<span id="filterPendingCount">0</span>)
                                    </button>
                                </div>
                                <input type="text" id="invitationSearchInput" class="form-control form-control-sm" placeholder="Search email..." style="max-width: 220px;">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnRefreshInvitations">
                                    <i class="ti ti-refresh me-1"></i> Refresh
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#shareEmailModal">
                                    <i class="ti ti-plus me-1"></i> New Invitation
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="invitationsTable">
                                <thead class="table-light text-muted small text-uppercase">
                                    <tr>
                                        <th class="ps-3 py-3">Recipient Email</th>
                                        <th class="py-3">Invitation Status</th>
                                        <th class="py-3">Registered User / Company</th>
                                        <th class="py-3">Referral Code</th>
                                        <th class="py-3">Last Sent Date</th>
                                        <th class="py-3 text-end rounded-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="invitationsTableBody">
                                    <tr><td colspan="6" class="text-center py-4 text-muted">Loading invitations...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Share via Email Modal -->
<div class="modal fade" id="shareEmailModal" tabindex="-1" aria-labelledby="shareEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="shareEmailModalLabel">
                    <i class="ti ti-mail-forward text-primary me-2"></i> Share Referral via Email
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="shareReferralForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 small mb-3 border-0 bg-info-subtle text-info-emphasis">
                        <i class="ti ti-info-circle me-1"></i> Invitations are dispatched in the background using queued jobs. You can track whether the recipient has joined in the <strong>Sent Email Invitations</strong> tab.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Recipient Email Address <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="email" name="recipient_email" id="recipientEmailInput" class="form-control" placeholder="partner@example.com" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Subject</label>
                        <input type="text" name="subject" class="form-control" value="Invitation from {{ $vendor->company_name ?: 'Partner' }} to join {{ config('app.name') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Personal Invitation Message</label>
                        <textarea name="message" class="form-control" rows="4">Hello,

You have been invited by {{ $vendor->company_name ?: (Auth::user()->name ?? 'a Partner') }} to join {{ config('app.name') }}.

Use Referral Code: {{ $vendor->referral_code }}
Or register directly using this link:
{{ $referralUrl }}

Join today to get started with powerful CRM and business management tools!</textarea>
                        <small class="text-muted">The referral link and unique code are automatically included in the invitation email.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center" id="btnSendInvite">
                        <i class="ti ti-send me-1"></i> Send Queued Invitation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Global alert helper replacing all toast notifications
        window.showVendorAlert = function(message, type = 'success', dismissible = true) {
            const alertClass = type === 'success' ? 'alert-success' : (type === 'error' ? 'alert-danger' : (type === 'warning' ? 'alert-warning' : 'alert-info'));
            const iconClass = type === 'success' ? 'ti ti-check' : (type === 'error' ? 'ti ti-alert-circle' : (type === 'warning' ? 'ti ti-alert-triangle' : 'ti ti-info-circle'));

            if ($('#vendorAlertContainer').find(`.alert:contains("${message}")`).length > 0) {
                return;
            }

            const dismissBtn = dismissible ? `<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>` : '';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show shadow-sm mb-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="${iconClass} fs-18 me-2"></i>
                        <div class="flex-grow-1">${message}</div>
                        ${dismissBtn}
                    </div>
                </div>
            `;
            $('#vendorAlertContainer').prepend(alertHtml);
            $('html, body').animate({ scrollTop: $('#vendorAlertContainer').offset().top - 80 }, 200);
        };

        window.showToast = function(message, type = 'success') {
            window.showVendorAlert(message, type);
        };
        if (typeof toastr !== 'undefined') {
            window.toastr.success = (m) => window.showVendorAlert(m, 'success');
            window.toastr.error = (m) => window.showVendorAlert(m, 'error');
            window.toastr.warning = (m) => window.showVendorAlert(m, 'warning');
            window.toastr.info = (m) => window.showVendorAlert(m, 'info');
        }

        // Copy Referral Code button
        $('#btnCopyCode').on('click', function() {
            const code = $('#referralCodeText').text().trim();
            if (navigator.clipboard) {
                navigator.clipboard.writeText(code).then(function() {
                    showVendorAlert(`Referral code <strong>${code}</strong> copied to clipboard!`, 'success');
                }).catch(function() {
                    fallbackCopy(code, 'Referral code');
                });
            } else {
                fallbackCopy(code, 'Referral code');
            }
        });

        // Copy Referral Link button
        $('#btnCopyLink').on('click', function() {
            const url = $('#referralUrlInput').val().trim();
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function() {
                    showVendorAlert('Referral link copied to clipboard successfully!', 'success');
                }).catch(function() {
                    fallbackCopy(url, 'Referral link');
                });
            } else {
                fallbackCopy(url, 'Referral link');
            }
        });

        function fallbackCopy(text, label) {
            const temp = $('<input>');
            $('body').append(temp);
            temp.val(text).select();
            document.execCommand('copy');
            temp.remove();
            showVendorAlert(`${label} copied to clipboard!`, 'success');
        }

        let allVendorsData = [];
        let allInvitationsData = [];
        let currentFilter = 'all';

        // Load referral data via API
        function loadReferrals() {
            $('#referralsTableBody').html('<tr><td colspan="7" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Loading registered vendors...</td></tr>');
            $('#invitationsTableBody').html('<tr><td colspan="6" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Loading invitations...</td></tr>');

            $.ajax({
                url: "{{ route('vendor.api.referrals') }}",
                method: 'GET',
                success: function(data) {
                    allVendorsData = data.referred_vendors || [];
                    allInvitationsData = data.invitations || [];

                    // Update Top Stats
                    const totalVendors = data.total_referred_vendors || allVendorsData.length;
                    const totalInvitations = data.total_invitations || allInvitationsData.length;
                    const joinedCount = data.joined_invitations_count || allInvitationsData.filter(i => i.status === 'joined').length;
                    const pendingCount = data.pending_invitations_count || allInvitationsData.filter(i => i.status === 'pending').length;

                    $('#totalReferredCount').text(totalVendors);
                    $('#tabJoinedCount').text(totalVendors);

                    $('#totalInvitationsCount').text(totalInvitations);
                    $('#tabInvitationsCount').text(totalInvitations);
                    $('#joinedInvitationsCountText').text(joinedCount);
                    $('#pendingInvitationsCountText').text(pendingCount);

                    $('#filterAllCount').text(totalInvitations);
                    $('#filterJoinedCount').text(joinedCount);
                    $('#filterPendingCount').text(pendingCount);

                    renderRegisteredVendors();
                    renderInvitations();
                },
                error: function() {
                    $('#referralsTableBody').html('<tr><td colspan="7" class="text-center py-4 text-danger"><i class="ti ti-alert-triangle me-1"></i> Error loading vendor records.</td></tr>');
                    $('#invitationsTableBody').html('<tr><td colspan="6" class="text-center py-4 text-danger"><i class="ti ti-alert-triangle me-1"></i> Error loading invitation records.</td></tr>');
                    showVendorAlert('Failed to load referral records. Please try again.', 'error');
                }
            });
        }

        // Render Tab 1: Joined Vendor Partners
        function renderRegisteredVendors() {
            const query = $('#registeredSearchInput').val().toLowerCase().trim();
            const filtered = allVendorsData.filter(v => {
                if (!query) return true;
                return (v.company_name && v.company_name.toLowerCase().includes(query)) ||
                       (v.contact_name && v.contact_name.toLowerCase().includes(query)) ||
                       (v.email && v.email.toLowerCase().includes(query)) ||
                       (v.phone && v.phone.toLowerCase().includes(query));
            });

            if (filtered.length === 0) {
                $('#referralsTableBody').html(`
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="my-3">
                                <i class="ti ti-users-group text-muted opacity-50" style="font-size: 48px;"></i>
                                <h6 class="fw-bold mt-2 text-dark">No Registered Vendors Found</h6>
                                <p class="text-muted small mb-3">Share your referral link or invite partners via email to grow your network.</p>
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#shareEmailModal">
                                    <i class="ti ti-mail-forward me-1"></i> Invite Your First Partner
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
                return;
            }

            let rows = '';
            $.each(filtered, function(index, item) {
                const verifiedBadge = item.is_verified 
                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="ti ti-check me-1"></i>Verified</span>'
                    : '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="ti ti-clock me-1"></i>Unverified</span>';
                
                const statusBadge = item.status.toLowerCase() === 'approved'
                    ? '<span class="badge bg-success text-white px-2 py-1">Approved</span>'
                    : (item.status.toLowerCase() === 'rejected'
                        ? '<span class="badge bg-danger text-white px-2 py-1">Rejected</span>'
                        : '<span class="badge bg-secondary text-white px-2 py-1">Pending</span>');

                rows += '<tr>';
                rows += `<td class="py-3 ps-3"><h6 class="mb-0 fw-semibold text-dark">${item.company_name}</h6></td>`;
                rows += `<td class="py-3 text-secondary">${item.contact_name}</td>`;
                rows += `<td class="py-3 text-muted">${item.email}</td>`;
                rows += `<td class="py-3 text-muted">${item.phone}</td>`;
                rows += `<td class="py-3">${verifiedBadge}</td>`;
                rows += `<td class="py-3">${statusBadge}</td>`;
                rows += `<td class="py-3 text-end pe-3 text-muted small">${item.created_at}</td>`;
                rows += '</tr>';
            });
            $('#referralsTableBody').html(rows);
        }

        // Render Tab 2: Sent Email Invitations
        function renderInvitations() {
            const query = $('#invitationSearchInput').val().toLowerCase().trim();
            const filtered = allInvitationsData.filter(inv => {
                if (currentFilter !== 'all' && inv.status !== currentFilter) {
                    return false;
                }
                if (!query) return true;
                return (inv.recipient_email && inv.recipient_email.toLowerCase().includes(query)) ||
                       (inv.status && inv.status.toLowerCase().includes(query)) ||
                       (inv.referral_code && inv.referral_code.toLowerCase().includes(query));
            });

            if (filtered.length === 0) {
                $('#invitationsTableBody').html(`
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="my-3">
                                <i class="ti ti-mail-off text-muted opacity-50" style="font-size: 48px;"></i>
                                <h6 class="fw-bold mt-2 text-dark">No Invitations Found</h6>
                                <p class="text-muted small mb-3">Send referral invitations to vendors and track their registration status here.</p>
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#shareEmailModal">
                                    <i class="ti ti-mail-forward me-1"></i> Send an Invitation
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
                return;
            }

            let rows = '';
            $.each(filtered, function(index, item) {
                const isJoined = item.status === 'joined';
                const statusBadge = isJoined
                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="ti ti-circle-check me-1"></i>Joined</span>'
                    : '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="ti ti-clock me-1"></i>Pending Registration</span>';

                let vendorDetails = '';
                if (isJoined && item.registered_vendor) {
                    vendorDetails = `
                        <div>
                            <span class="fw-semibold text-dark">${item.registered_vendor.company_name}</span>
                            <div class="small text-muted">${item.registered_vendor.name} (${item.registered_vendor.type || 'Registered'})</div>
                        </div>
                    `;
                } else if (isJoined) {
                    vendorDetails = '<span class="text-success small fw-semibold"><i class="ti ti-circle-check me-1"></i>Registered</span>';
                } else {
                    vendorDetails = '<span class="text-muted small fst-italic"><i class="ti ti-hourglass me-1"></i>Awaiting Registration</span>';
                }

                const actionBtn = `
                    <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 btn-resend-invite" data-email="${item.recipient_email}" title="Resend Invitation Email">
                        <i class="ti ti-mail-forward me-1"></i> Resend
                    </button>
                `;

                rows += '<tr>';
                rows += `<td class="py-3 ps-3"><span class="fw-semibold text-dark">${item.recipient_email}</span></td>`;
                rows += `<td class="py-3">${statusBadge}</td>`;
                rows += `<td class="py-3">${vendorDetails}</td>`;
                rows += `<td class="py-3"><span class="badge bg-light text-dark font-monospace border">${item.referral_code || 'N/A'}</span></td>`;
                rows += `<td class="py-3 text-muted small">${item.last_sent_at || item.created_at}</td>`;
                rows += `<td class="py-3 text-end pe-3">${actionBtn}</td>`;
                rows += '</tr>';
            });
            $('#invitationsTableBody').html(rows);
        }

        // Search inputs listeners
        $('#registeredSearchInput').on('keyup', renderRegisteredVendors);
        $('#invitationSearchInput').on('keyup', renderInvitations);

        // Filter button listeners
        $('.filter-btn').on('click', function() {
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            currentFilter = $(this).data('filter');
            renderInvitations();
        });

        // Resend button click handler
        $(document).on('click', '.btn-resend-invite', function() {
            const email = $(this).data('email');
            $('#recipientEmailInput').val(email);
            $('#shareEmailModal').modal('show');
            setTimeout(function() {
                $('#recipientEmailInput').focus();
            }, 500);
        });

        // Refresh triggers
        $('#btnRefreshReferrals, #btnRefreshInvitations, #btnRefreshAll').on('click', function() {
            loadReferrals();
            showVendorAlert('Referral records and invitations refreshed.', 'info');
        });

        // Initial Load
        loadReferrals();

        // Share Referral Form via Email submission (Queued background job)
        $('#shareReferralForm').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#btnSendInvite');
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Queuing Email...');

            $.ajax({
                url: "{{ route('vendor.share_referral_email') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    $('#shareEmailModal').modal('hide');
                    $('#shareReferralForm')[0].reset();
                    showVendorAlert(res.message || 'Invitation email has been successfully queued!', 'success');
                    
                    // Reload data to reflect new/updated invitation
                    loadReferrals();

                    // Switch to the Invitations tab so the user sees the newly added invitation immediately
                    const triggerEl = document.querySelector('#tab-invitations-btn');
                    if (triggerEl) {
                        const tab = bootstrap.Tab.getOrCreateInstance(triggerEl);
                        tab.show();
                    }
                },
                error: function(xhr) {
                    let errMsg = 'Failed to send referral email. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    showVendorAlert(errMsg, 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="ti ti-send me-1"></i> Send Queued Invitation');
                }
            });
        });
    });
</script>
@endsection
