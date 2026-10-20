@extends('layouts.vendor')

@section('content')
<style>

        
        
        
        /* Layout */
        
        
        
        /* Card Styling */
        .stat-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Progress Bar */
        .progress { height: 8px; border-radius: 10px; }
        .target-percentage { font-size: 2rem; font-weight: 800; color: var(--indigo-primary); }

        @media (max-width: 768px) {
            
            
        }
    
.stat-card { border: none; border-radius: 12px; transition: transform 0.2s; }
.stat-card:hover { transform: translateY(-5px); }
.code-badge {
    background: #eef2ff;
    color: #4338ca;
    font-family: 'Courier New', Courier, monospace;
    font-weight: 700;
    font-size: 1.25rem;
    padding: 0.4rem 1.1rem;
    border-radius: 8px;
    letter-spacing: 1px;
    border: 1px dashed #6366f1;
    display: inline-block;
}
</style>

<div class="page-wrapper">
    <div class="content">
        <!-- Vendor Alert Notification Container (Replacing Toasts) -->
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

            @if($vendor->status === 'pending')
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center p-3" role="alert">
                    <i class="ti ti-clock-hour-4 fs-24 me-3 text-warning"></i>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark">Vendor Account Pending Admin Review</h6>
                        <p class="mb-0 text-muted small">Your vendor profile is currently under review by the superadmin. You can explore your dashboard and copy your referral link while approval is processed.</p>
                    </div>
                </div>
            @endif
        </div>
        
        <header class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold m-0">Performance Overview</h3>
                <p class="text-muted small m-0">Welcome back, {{ $vendor->company_name ?: (Auth::user()->name ?? 'Vendor') }}. Here's your performance summary.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-white border shadow-sm rounded-3">
                    <i class="fa-regular fa-calendar me-2"></i> {{ date('F Y') }}
                </button>
            </div>
        </header>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card stat-card shadow-sm h-100 p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3 gap-2">
                        <div>
                            <h6 class="text-uppercase text-muted fw-bold small mb-2" id="targetLabel1">Monthly Target Achievement</h6>
                            <div class="btn-group btn-group-sm mb-2 shadow-sm" role="group" id="vendorDashPeriodGroup">
                                <button type="button" class="btn btn-outline-primary active py-1 px-3 fw-semibold" data-dash-period="monthly">Monthly</button>
                                <button type="button" class="btn btn-outline-primary py-1 px-3 fw-semibold" data-dash-period="quarterly">Quarterly</button>
                                <button type="button" class="btn btn-outline-primary py-1 px-3 fw-semibold" data-dash-period="half_yearly">Half-Yearly</button>
                                <button type="button" class="btn btn-outline-primary py-1 px-3 fw-semibold" data-dash-period="yearly">Yearly</button>
                            </div>
                            <p class="target-percentage m-0" id="targetPercentage">0.0%</p>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle" id="targetDifference">0.0% vs last month</span>
                    </div>
                    
                    <div class="mb-2 d-flex justify-content-between small">
                        <span>Current Revenue: <strong id="targetCurrentVal">₹0.00</strong></span>
                        <span class="text-muted" id="targetMaxVal">Target: ₹0.00</span>
                    </div>
                    <div class="progress mb-3" style="height: 10px; border-radius: 6px;">
                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" id="targetProgressBar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="text-muted small mb-0">Distance to Target: <strong id="targetRemaining">₹0.00</strong></p>
                        <span class="badge bg-light text-dark border small" id="targetPeriodTag">Current Month</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card stat-card shadow-sm h-100 p-4 bg-primary text-white">
                    <h6 class="text-uppercase fw-bold small opacity-75" id="targetLabel2">Monthly Target</h6>
                    <h2 class="fw-bold mb-3" id="targetDisplayVal">₹0.00</h2>
                    <p class="small opacity-75 mb-0" id="targetSetDate">Dynamic Target</p>
                    <hr class="opacity-25">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-chart-line"></i>
                        <span class="small" id="targetPeriodInfo">Calculated based on active target schedule</span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card stat-card shadow-sm p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-info-subtle text-info">
                            <i class="fa-solid fa-clock-rotate-left fs-5"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small fw-bold mb-1">Revenue: Last Month</h6>
                            <h4 class="fw-bold m-0"><span id="lastMonthCommission">Loading...</span></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card stat-card shadow-sm p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-success-subtle text-success">
                            <i class="fa-solid fa-hand-holding-dollar fs-5"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small fw-bold mb-1" id="revenueCurrentLabel">Revenue: Current Month</h6>
                            <h4 class="fw-bold m-0"><span id="currentMonthCommission">Loading...</span></h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vendor Partner Referral Section -->
            <div class="col-12 mt-4">
                <div class="card stat-card shadow-sm p-4 bg-white border-0">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                        <div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small fw-semibold text-uppercase mb-2">Partner Referral Program</span>
                            <h5 class="fw-bold m-0 text-dark">Grow Your Vendor Network</h5>
                            <p class="text-muted small m-0">Invite vendors to join {{ config('app.name') }}. Share your unique referral code or link and track registrations.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('vendor.referrals') }}" class="btn btn-outline-primary d-flex align-items-center">
                                <i class="ti ti-layout-grid me-1 fs-18"></i> View All Referrals & Invitations
                            </a>
                            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#dashboardShareEmailModal">
                                <i class="ti ti-mail-forward me-1 fs-18"></i> Invite via Email
                            </button>
                        </div>
                    </div>

                    <div class="row g-4 pt-2">
                        <!-- Referral Code & Link Box -->
                        <div class="col-lg-7">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <label class="form-label small text-muted fw-semibold mb-0">Your Unique Referral Identifier</label>
                                    @if($vendor->referral_code)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small"><i class="ti ti-circle-check me-1"></i>Active & Approved</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 small"><i class="ti ti-clock me-1"></i>Pending Superadmin Approval</span>
                                    @endif
                                </div>

                                @if($vendor->referral_code)
                                    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                                        <span class="code-badge" id="dashReferralCodeText">{{ $vendor->referral_code }}</span>
                                        <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" id="btnDashCopyCode">
                                            <i class="ti ti-copy me-1"></i> Copy Code
                                        </button>
                                    </div>

                                    <label class="form-label small text-muted fw-semibold mb-1">Shareable Vendor Registration Link</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-white" id="dashReferralUrlInput" value="{{ $referralUrl }}" readonly>
                                        <button class="btn btn-primary fw-semibold" type="button" id="btnDashCopyLink">
                                            <i class="ti ti-link me-1"></i> Copy Link
                                        </button>
                                        <button class="btn btn-outline-secondary fw-semibold" type="button" data-bs-toggle="modal" data-bs-target="#dashboardShareEmailModal">
                                            <i class="ti ti-send me-1"></i> Share
                                        </button>
                                    </div>
                                    <p class="text-muted small mt-2 mb-0">
                                        <i class="ti ti-info-circle text-primary me-1"></i> Anyone signing up through this link or entering this code will automatically register as a <strong>Vendor Partner</strong> under your referral account.
                                    </p>
                                @else
                                    <div class="alert alert-warning border-0 p-3 mb-0 d-flex align-items-center">
                                        <i class="ti ti-clock-hour-4 fs-24 text-warning me-3"></i>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">Referral Code Pending Approval</h6>
                                            <p class="mb-0 text-muted small">Your unique referral code and sharing link will be generated after your account is approved by the Super Admin.</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Referral Stats Quick Cards -->
                        <div class="col-lg-5">
                            <div class="row g-3 h-100">
                                <div class="col-sm-6">
                                    <div class="p-3 bg-success-subtle rounded-3 border border-success-subtle h-100 d-flex flex-column justify-content-between">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <span class="small fw-bold text-success text-uppercase">Joined Vendors</span>
                                            <i class="ti ti-users-group text-success fs-20"></i>
                                        </div>
                                        <div class="my-2">
                                            <h3 class="fw-bold m-0 text-success" id="dashJoinedVendorsCount">{{ $referredVendorsCount ?? 0 }}</h3>
                                            <span class="small text-muted">Registered Partners</span>
                                        </div>
                                        <a href="{{ route('vendor.referrals') }}" class="small fw-semibold text-success text-decoration-none">
                                            View Partners <i class="ti ti-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-3 bg-primary-subtle rounded-3 border border-primary-subtle h-100 d-flex flex-column justify-content-between">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <span class="small fw-bold text-primary text-uppercase">Sent Invitations</span>
                                            <i class="ti ti-mail-share text-primary fs-20"></i>
                                        </div>
                                        <div class="my-2">
                                            <h3 class="fw-bold m-0 text-primary" id="dashSentInvitationsCount">{{ $invitationsCount ?? 0 }}</h3>
                                            <span class="small text-muted">Email Invites Sent</span>
                                        </div>
                                        <a href="{{ route('vendor.referrals') }}" class="small fw-semibold text-primary text-decoration-none">
                                            Track Status <i class="ti ti-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Latest Referral Joins & Shared Invitations Activity Section -->
                    <hr class="my-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <div>
                            <h6 class="fw-bold m-0 text-dark">Recent Referral Activity</h6>
                            <p class="text-muted small m-0">Recent vendor partner registrations and shared referral email invitations.</p>
                        </div>
                        <ul class="nav nav-pills" id="dashReferralActivityTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active btn-sm py-1 px-3 fw-semibold" id="dash-tab-joins-btn" data-bs-toggle="pill" data-bs-target="#dash-tab-joins" type="button" role="tab" aria-controls="dash-tab-joins" aria-selected="true">
                                    <i class="ti ti-users-group me-1"></i> Latest Joined Vendors (<span id="dashJoinedVendorsBadgeCount">{{ count($latestReferredVendors ?? []) }}</span>)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link btn-sm py-1 px-3 fw-semibold ms-2" id="dash-tab-shares-btn" data-bs-toggle="pill" data-bs-target="#dash-tab-shares" type="button" role="tab" aria-controls="dash-tab-shares" aria-selected="false">
                                    <i class="ti ti-mail-share me-1"></i> Latest Shared Invitations (<span id="dashSharedInvitesBadgeCount">{{ count($latestInvitations ?? []) }}</span>)
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content" id="dashReferralActivityTabsContent">
                        <!-- Tab 1: Latest Joined Vendors -->
                        <div class="tab-pane fade show active" id="dash-tab-joins" role="tabpanel" aria-labelledby="dash-tab-joins-btn">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-3 py-2">Vendor / Company</th>
                                            <th class="py-2">Contact Person</th>
                                            <th class="py-2">Email</th>
                                            <th class="py-2">Phone</th>
                                            <th class="py-2">Email Verification</th>
                                            <th class="py-2">Vendor Status</th>
                                            <th class="py-2 text-end rounded-end pe-3">Joined Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dashLatestJoinedTableBody">
                                        @forelse($latestReferredVendors as $refVendor)
                                            <tr>
                                                <td class="ps-3 py-2 fw-semibold text-dark">{{ $refVendor->company_name ?: 'N/A' }}</td>
                                                <td class="py-2 text-secondary">{{ $refVendor->user->name ?? 'N/A' }}</td>
                                                <td class="py-2 text-muted">{{ $refVendor->user->email ?? 'N/A' }}</td>
                                                <td class="py-2 text-muted">{{ $refVendor->user->mobile ?? ($refVendor->pincode ?? 'N/A') }}</td>
                                                <td class="py-2">
                                                    @if(!is_null($refVendor->user?->email_verified_at))
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="ti ti-check me-1"></i>Verified</span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="ti ti-clock me-1"></i>Unverified</span>
                                                    @endif
                                                </td>
                                                <td class="py-2">
                                                    @if(strtolower($refVendor->status) === 'approved')
                                                        <span class="badge bg-success text-white px-2 py-1">Approved</span>
                                                    @elseif(strtolower($refVendor->status) === 'rejected')
                                                        <span class="badge bg-danger text-white px-2 py-1">Rejected</span>
                                                    @else
                                                        <span class="badge bg-secondary text-white px-2 py-1">Pending</span>
                                                    @endif
                                                </td>
                                                <td class="py-2 text-end pe-3 text-muted small">{{ $refVendor->created_at->format('M d, Y') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted small">
                                                    <i class="ti ti-users-group text-muted opacity-50 fs-24 d-block mb-1"></i>
                                                    No vendor partners have registered yet. Share your referral link or invite partners via email.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                <span class="small text-muted">Showing latest {{ count($latestReferredVendors ?? []) }} joined vendor accounts</span>
                                <a href="{{ route('vendor.referrals') }}" class="small fw-semibold text-primary text-decoration-none">
                                    View Full Referral Directory <i class="ti ti-arrow-right"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Tab 2: Latest Shared Invitations -->
                        <div class="tab-pane fade" id="dash-tab-shares" role="tabpanel" aria-labelledby="dash-tab-shares-btn">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-3 py-2">Recipient Email</th>
                                            <th class="py-2">Invitation Status</th>
                                            <th class="py-2">Registered User / Company</th>
                                            <th class="py-2">Referral Code</th>
                                            <th class="py-2">Last Sent Date</th>
                                            <th class="py-2 text-end rounded-end pe-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dashLatestSharedTableBody">
                                        @forelse($latestInvitations as $invitation)
                                            <tr>
                                                <td class="ps-3 py-2 fw-semibold text-dark">{{ $invitation->recipient_email }}</td>
                                                <td class="py-2">
                                                    @if($invitation->status === 'joined')
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="ti ti-circle-check me-1"></i>Joined</span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="ti ti-clock me-1"></i>Pending Registration</span>
                                                    @endif
                                                </td>
                                                <td class="py-2">
                                                    @if($invitation->status === 'joined' && $invitation->registeredBox)
                                                        <span class="fw-semibold text-dark">{{ $invitation->registeredBox->name }}</span>
                                                        <div class="small text-muted">{{ $invitation->registeredBox->superadmin->name ?? '' }} (CRM User)</div>
                                                    @elseif($invitation->status === 'joined' && $invitation->registeredVendor)
                                                        <span class="fw-semibold text-dark">{{ $invitation->registeredVendor->company_name }}</span>
                                                        <div class="small text-muted">{{ $invitation->registeredVendor->user->name ?? '' }} (Vendor)</div>
                                                    @elseif($invitation->status === 'joined')
                                                        <span class="text-success small fw-semibold"><i class="ti ti-circle-check me-1"></i>Registered</span>
                                                    @else
                                                        <span class="text-muted small fst-italic"><i class="ti ti-hourglass me-1"></i>Awaiting Registration</span>
                                                    @endif
                                                </td>
                                                <td class="py-2"><span class="badge bg-light text-dark font-monospace border">{{ $invitation->referral_code ?: 'N/A' }}</span></td>
                                                <td class="py-2 text-muted small">{{ $invitation->last_sent_at ? $invitation->last_sent_at->format('M d, Y h:i A') : $invitation->created_at->format('M d, Y h:i A') }}</td>
                                                <td class="py-2 text-end pe-3">
                                                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 btn-dash-resend" data-email="{{ $invitation->recipient_email }}">
                                                        <i class="ti ti-mail-forward me-1"></i> Resend
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted small">
                                                    <i class="ti ti-mail-off text-muted opacity-50 fs-24 d-block mb-1"></i>
                                                    No referral invitations sent yet. Click "Invite via Email" to send an invitation.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                <span class="small text-muted">Showing latest {{ count($latestInvitations ?? []) }} sent email invitations</span>
                                <a href="{{ route('vendor.referrals') }}" class="small fw-semibold text-primary text-decoration-none">
                                    Manage All Invitations & Status <i class="ti ti-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4">
                <div class="card stat-card shadow-sm p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold m-0">Revenue Analytics</h5>
                            <p class="text-muted small m-0">Comparing performance timeline progression trends</p>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1.5 small">Current Month</span>
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1.5 small">Last Month</span>
                        </div>
                    </div>
                    <div style="position: relative; height:320px; width:100%;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Share via Email Modal on Dashboard -->
<div class="modal fade" id="dashboardShareEmailModal" tabindex="-1" aria-labelledby="dashboardShareEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="dashboardShareEmailModalLabel">
                    <i class="ti ti-mail-forward text-primary me-2"></i> Share Referral via Email
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="dashShareReferralForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 small mb-3 border-0 bg-info-subtle text-info-emphasis">
                        <i class="ti ti-info-circle me-1"></i> Invitations are dispatched in the background using queued jobs. You can track whether the recipient has joined in the <a href="{{ route('vendor.referrals') }}" class="fw-bold text-decoration-underline">Referrals Panel</a>.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Recipient Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="recipient_email" id="dashRecipientEmailInput" class="form-control" placeholder="partner@example.com" required>
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
                    <button type="submit" class="btn btn-primary d-flex align-items-center" id="btnDashSendInvite">
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
    var dashGlobalData = null;

    function renderPeriodPerformance(period) {
        if (!dashGlobalData) return;

        let perf = null;
        let periodName = 'Monthly';
        let periodTag = 'Current Month';

        if (period === 'quarterly' && dashGlobalData.current_quarter_perf) {
            perf = dashGlobalData.current_quarter_perf;
            periodName = perf.label || 'Quarterly';
            periodTag = 'Current Quarter (' + periodName + ')';
        } else if (period === 'half_yearly' && dashGlobalData.current_half_perf) {
            perf = dashGlobalData.current_half_perf;
            periodName = perf.label || 'Half-Yearly';
            periodTag = 'Current Half-Year (' + periodName + ')';
        } else if (period === 'yearly' && dashGlobalData.current_year_perf) {
            perf = dashGlobalData.current_year_perf;
            periodName = perf.label || 'Yearly';
            periodTag = 'Full Year (' + (perf.year || new Date().getFullYear()) + ')';
        } else {
            perf = dashGlobalData.current_month_perf || null;
            periodName = perf ? perf.label : 'Monthly';
            periodTag = 'Current Month';
        }

        let target = perf ? parseFloat(perf.target_amount || 0) : parseFloat(dashGlobalData.monthlyTarget || 0);
        let current = perf ? parseFloat(perf.actual_sales || dashGlobalData.currentCommission || 0) : parseFloat(dashGlobalData.currentCommission || 0);
        let pct = perf ? parseFloat(perf.achievement_percent || 0) : (target > 0 ? (current / target) * 100 : 0);
        let remaining = perf ? parseFloat(perf.shortfall || 0) : Math.max(0, target - current);

        $('#targetLabel1').text(periodName + ' Target Achievement');
        $('#targetLabel2').text(periodName + ' Target');
        $('#targetPeriodTag').text(periodTag);
        $('#targetPercentage').text(pct.toFixed(1) + '%');
        $('#targetProgressBar').css('width', Math.min(100, pct) + '%');
        $('#targetCurrentVal').text('₹' + current.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#targetMaxVal').text('Target: ₹' + target.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#targetDisplayVal').text('₹' + target.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#targetRemaining').text('₹' + remaining.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        if (perf && perf.start_date && perf.end_date) {
            $('#targetSetDate').text(perf.start_date + ' to ' + perf.end_date);
            $('#targetPeriodInfo').text(perf.deals_count + ' sales closed | ₹' + (perf.actual_commission || 0).toLocaleString('en-IN') + ' commission earned');
        } else {
            $('#targetSetDate').text('Standard Schedule');
            $('#targetPeriodInfo').text('Calculated based on active target schedule');
        }
    }

    $(document).ready(function() {
        // Handle period toggle button clicks
        $('#vendorDashPeriodGroup').on('click', 'button[data-dash-period]', function(e) {
            e.preventDefault();
            $('#vendorDashPeriodGroup button').removeClass('active');
            $(this).addClass('active');
            let period = $(this).data('dash-period');
            renderPeriodPerformance(period);
        });

        $.ajax({
            url: '{{ route('vendor.api.dashboard') }}',
            method: 'GET',
            success: function(data) {
                dashGlobalData = data;
                let current = parseFloat(data.currentCommission || 0);
                let last = parseFloat(data.lastMonthCommission || 0);
                
                $('#currentMonthCommission').text('₹' + current.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#lastMonthCommission').text('₹' + last.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                
                // Render initial (monthly) view
                renderPeriodPerformance('monthly');
                
                let diff = 0;
                if(last > 0) {
                    diff = ((current - last) / last) * 100;
                } else if(current > 0) {
                    diff = 100;
                }
                
                if(diff >= 0) {
                    $('#targetDifference').removeClass('bg-danger-subtle text-danger border-danger-subtle').addClass('bg-success-subtle text-success border-success-subtle').text('+' + diff.toFixed(1) + '% vs last month');
                } else {
                    $('#targetDifference').removeClass('bg-success-subtle text-success border-success-subtle').addClass('bg-danger-subtle text-danger border-danger-subtle').text(diff.toFixed(1) + '% vs last month');
                }
                if (data.referred_vendors_count !== undefined) {
                    $('#dashJoinedVendorsCount').text(data.referred_vendors_count);
                }
                if (data.invitations_count !== undefined) {
                    $('#dashSentInvitationsCount').text(data.invitations_count);
                }

                if (data.latest_referred_vendors) {
                    renderDashJoinedVendors(data.latest_referred_vendors);
                }
                if (data.latest_invitations) {
                    renderDashSharedInvitations(data.latest_invitations);
                }
            },
            error: function() {
                $('#currentMonthCommission').text('Error');
                $('#lastMonthCommission').text('Error');
                showVendorAlert('Unable to load real-time analytics data. Please refresh the page.', 'error');
            }
        });

        function renderDashJoinedVendors(vendors) {
            if (!vendors || vendors.length === 0) {
                $('#dashLatestJoinedTableBody').html(`
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted small">
                            <i class="ti ti-users-group text-muted opacity-50 fs-24 d-block mb-1"></i>
                            No vendor partners have registered yet. Share your referral link or invite partners via email.
                        </td>
                    </tr>
                `);
                $('#dashJoinedVendorsBadgeCount').text('0');
                return;
            }
            $('#dashJoinedVendorsBadgeCount').text(vendors.length);
            let rows = '';
            $.each(vendors, function(idx, item) {
                const verifiedBadge = item.is_verified 
                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="ti ti-check me-1"></i>Verified</span>'
                    : '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="ti ti-clock me-1"></i>Unverified</span>';

                const statusBadge = item.status.toLowerCase() === 'approved'
                    ? '<span class="badge bg-success text-white px-2 py-1">Approved</span>'
                    : (item.status.toLowerCase() === 'rejected'
                        ? '<span class="badge bg-danger text-white px-2 py-1">Rejected</span>'
                        : '<span class="badge bg-secondary text-white px-2 py-1">Pending</span>');

                rows += `<tr>
                    <td class="ps-3 py-2 fw-semibold text-dark">${item.company_name}</td>
                    <td class="py-2 text-secondary">${item.contact_name}</td>
                    <td class="py-2 text-muted">${item.email}</td>
                    <td class="py-2 text-muted">${item.phone}</td>
                    <td class="py-2">${verifiedBadge}</td>
                    <td class="py-2">${statusBadge}</td>
                    <td class="py-2 text-end pe-3 text-muted small">${item.created_at}</td>
                </tr>`;
            });
            $('#dashLatestJoinedTableBody').html(rows);
        }

        function renderDashSharedInvitations(invitations) {
            if (!invitations || invitations.length === 0) {
                $('#dashLatestSharedTableBody').html(`
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted small">
                            <i class="ti ti-mail-off text-muted opacity-50 fs-24 d-block mb-1"></i>
                            No referral invitations sent yet. Click "Invite via Email" to send an invitation.
                        </td>
                    </tr>
                `);
                $('#dashSharedInvitesBadgeCount').text('0');
                return;
            }
            $('#dashSharedInvitesBadgeCount').text(invitations.length);
            let rows = '';
            $.each(invitations, function(idx, item) {
                const isJoined = item.status === 'joined';
                const statusBadge = isJoined
                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="ti ti-circle-check me-1"></i>Joined</span>'
                    : '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="ti ti-clock me-1"></i>Pending Registration</span>';

                let vendorDetails = '';
                if (isJoined && item.registered_vendor) {
                    vendorDetails = `<span class="fw-semibold text-dark">${item.registered_vendor.company_name}</span>
                        <div class="small text-muted">${item.registered_vendor.name} (${item.registered_vendor.type || 'Registered'})</div>`;
                } else if (isJoined) {
                    vendorDetails = '<span class="text-success small fw-semibold"><i class="ti ti-circle-check me-1"></i>Registered</span>';
                } else {
                    vendorDetails = '<span class="text-muted small fst-italic"><i class="ti ti-hourglass me-1"></i>Awaiting Registration</span>';
                }

                rows += `<tr>
                    <td class="ps-3 py-2 fw-semibold text-dark">${item.recipient_email}</td>
                    <td class="py-2">${statusBadge}</td>
                    <td class="py-2">${vendorDetails}</td>
                    <td class="py-2"><span class="badge bg-light text-dark font-monospace border">${item.referral_code || 'N/A'}</span></td>
                    <td class="py-2 text-muted small">${item.last_sent_at || item.created_at}</td>
                    <td class="py-2 text-end pe-3">
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 btn-dash-resend" data-email="${item.recipient_email}">
                            <i class="ti ti-mail-forward me-1"></i> Resend
                        </button>
                    </td>
                </tr>`;
            });
            $('#dashLatestSharedTableBody').html(rows);
        }

        // Resend button click from dashboard
        $(document).on('click', '.btn-dash-resend', function() {
            const email = $(this).data('email');
            $('#dashRecipientEmailInput').val(email);
            $('#dashboardShareEmailModal').modal('show');
            setTimeout(function() {
                $('#dashRecipientEmailInput').focus();
            }, 500);
        });

        // Copy Referral Code from Dashboard
        $('#btnDashCopyCode').on('click', function() {
            const code = $('#dashReferralCodeText').text().trim();
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

        // Copy Referral Link from Dashboard
        $('#btnDashCopyLink').on('click', function() {
            const url = $('#dashReferralUrlInput').val().trim();
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

        // Share Referral Email Form from Dashboard
        $('#dashShareReferralForm').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#btnDashSendInvite');
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Queuing Email...');

            $.ajax({
                url: "{{ route('vendor.share_referral_email') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    $('#dashboardShareEmailModal').modal('hide');
                    $('#dashShareReferralForm')[0].reset();
                    showVendorAlert(res.message || 'Invitation email has been successfully queued!', 'success');
                    
                    // Increment Sent count on dashboard
                    let curCount = parseInt($('#dashSentInvitationsCount').text()) || 0;
                    $('#dashSentInvitationsCount').text(curCount + 1);

                    // If invitation details returned, re-fetch dashboard data to refresh activity tables
                    if (typeof fetchDashboardData === 'function') {
                        fetchDashboardData();
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
        };

        // Enforce replacement of all toasts on this page
        window.showToast = function(message, type = 'success') {
            window.showVendorAlert(message, type);
        };
        if (typeof toastr !== 'undefined') {
            window.toastr.success = (m) => window.showVendorAlert(m, 'success');
            window.toastr.error = (m) => window.showVendorAlert(m, 'error');
            window.toastr.warning = (m) => window.showVendorAlert(m, 'warning');
            window.toastr.info = (m) => window.showVendorAlert(m, 'info');
        }
    });
</script>
@endsection

