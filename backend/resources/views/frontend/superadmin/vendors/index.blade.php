@extends('layouts.main')

@section('content')
<style>
.stat-card { border: none; border-radius: 12px; transition: transform 0.2s; }
.stat-card:hover { transform: translateY(-3px); }
.nav-pills .nav-link.active {
    background-color: #4f46e5 !important;
    color: #fff !important;
}
.nav-pills .nav-link {
    color: #475569;
    background-color: #f1f5f9;
}
.referral-stat-pill {
    transition: all 0.2s ease;
    cursor: pointer;
}
.referral-stat-pill:hover {
    transform: scale(1.04);
}
</style>

<div class="page-wrapper">
    <div class="content">
        
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Registered Vendors Table Section -->
        <div class="col-12 mt-4">
            <div class="card stat-card shadow-sm p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold m-0 text-dark d-flex align-items-center gap-2">
                            <i class="fa-solid fa-users-gear text-primary"></i> Vendors Management
                        </h5>
                        <p class="text-muted small m-0">Manage onboarding suppliers, direct vendor login impersonation, and track joined users/referrals</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-light border fw-medium text-secondary px-3 py-1.5" onclick="loadVendorsTable()">
                            <i class="fa-solid fa-rotate me-1"></i> Refresh
                        </button>
                    </div>
                </div>

                <!-- Responsive Table Container Wrapper -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary small text-uppercase">
                            <tr>
                                <th class="py-3 border-0 ps-3" style="font-size: 0.75rem; letter-spacing: 0.05em;">Company / Contact</th>
                                <th class="py-3 border-0" style="font-size: 0.75rem; letter-spacing: 0.05em;">User Account</th>
                                <th class="py-3 border-0" style="font-size: 0.75rem; letter-spacing: 0.05em;">Phone</th>
                                <th class="py-3 border-0" style="font-size: 0.75rem; letter-spacing: 0.05em;">Status</th>
                                <th class="py-3 border-0 text-center" style="font-size: 0.75rem; letter-spacing: 0.05em;">Referrals & Joined Users</th>
                                <th class="py-3 border-0" style="font-size: 0.75rem; letter-spacing: 0.05em;">Registration Date</th>
                                <th class="py-3 border-0 text-end pe-3" style="font-size: 0.75rem; letter-spacing: 0.05em;">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-dark small" id="vendorTableBody">
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-spinner fa-spin me-2"></i> Loading vendors...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Vendor Action (Review/Approve/Target/Reject) Modal -->
        <div class="modal fade" id="vendorActionModal" tabindex="-1" aria-labelledby="vendorActionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold" id="vendorActionModalLabel">Review Application</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-4">You are reviewing the application for <strong id="modalCompanyName" class="text-dark"></strong>.</p>
                        
                        <form id="vendorActionForm">
                            <input type="hidden" id="modalVendorId" value="">
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold small">Target Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="modalMonthlyTarget" value="0" required>
                                <div class="form-text text-muted">Set the sales target limit for this vendor.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Target Duration</label>
                                <select class="form-select" id="modalTargetType" onchange="toggleCustomDates()">
                                    <option value="monthly">Monthly Target</option>
                                    <option value="quarterly">Quarterly Target (3 Months)</option>
                                    <option value="half_yearly">Half-Yearly Target (6 Months)</option>
                                    <option value="yearly">Yearly Target (12 Months)</option>
                                    <option value="custom">Custom Date Range</option>
                                </select>
                            </div>

                            <!-- Stepped Target Increase Option -->
                            <div class="mb-3 border rounded-3 p-3 bg-light">
                                <div class="form-check form-switch mb-1">
                                    <input class="form-check-input" type="checkbox" id="modalIsStepped" onchange="toggleSteppedOptions()">
                                    <label class="form-check-label fw-bold small text-dark" for="modalIsStepped">
                                        Enable Stepped Target Increase
                                    </label>
                                </div>
                                <div class="text-muted small mb-2">
                                    Set initial target for e.g. 3 months, then increase thereafter.
                                </div>
                                <div id="modalSteppedOptions" style="display: none;">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-muted">Initial Duration (Months)</label>
                                            <input type="number" class="form-control form-control-sm" id="modalStepMonths" value="3" min="1" max="12">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-muted">Increased Target (₹)</label>
                                            <input type="number" step="0.01" class="form-control form-control-sm" id="modalNextTargetAmount" placeholder="e.g. 50000">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 row" id="customDatesContainer" style="display: none;">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Start Date</label>
                                    <input type="date" class="form-control" id="modalTargetStartDate">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">End Date</label>
                                    <input type="date" class="form-control" id="modalTargetEndDate">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold small text-uppercase text-muted">Select Action</label>
                                <div class="d-flex gap-2">
                                    <div class="form-check form-check-inline custom-radio flex-fill border rounded-3 p-3 m-0" style="cursor: pointer;" onclick="$('#actionApprove').prop('checked', true); toggleRemarks();">
                                        <input class="form-check-input mt-1" type="radio" name="action_type" id="actionApprove" value="approve" checked onchange="toggleRemarks()">
                                        <label class="form-check-label ms-1 fw-semibold text-success small" for="actionApprove" style="cursor: pointer;">Approve Vendor</label>
                                    </div>
                                    <div class="form-check form-check-inline custom-radio flex-fill border rounded-3 p-3 m-0" style="cursor: pointer;" onclick="$('#actionTarget').prop('checked', true); toggleRemarks();">
                                        <input class="form-check-input mt-1" type="radio" name="action_type" id="actionTarget" value="target" onchange="toggleRemarks()">
                                        <label class="form-check-label ms-1 fw-semibold text-primary small" for="actionTarget" style="cursor: pointer;">Update Target Only</label>
                                    </div>
                                    <div class="form-check form-check-inline custom-radio flex-fill border rounded-3 p-3 m-0" style="cursor: pointer;" onclick="$('#actionReject').prop('checked', true); toggleRemarks();">
                                        <input class="form-check-input mt-1" type="radio" name="action_type" id="actionReject" value="reject" onchange="toggleRemarks()">
                                        <label class="form-check-label ms-1 fw-semibold text-danger small" for="actionReject" style="cursor: pointer;">Reject Vendor</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3" id="remarksContainer" style="display: none;">
                                <label class="form-label fw-semibold small">Rejection Remarks <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="modalRemarks" rows="3" placeholder="Please provide a reason for rejection..."></textarea>
                                <div class="form-text text-muted">These remarks will be included in the email sent to the vendor.</div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light shadow-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary shadow-sm px-4" onclick="submitVendorAction()">Confirm Action</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Referrals & Joined Users Breakdown Modal -->
        <div class="modal fade" id="vendorReferralsModal" tabindex="-1" aria-labelledby="vendorReferralsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    
                    <!-- Modal Header -->
                    <div class="modal-header bg-light border-bottom px-4 py-3" style="border-radius: 16px 16px 0 0;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-md bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 48px; height: 48px;">
                                <i class="fa-solid fa-network-wired"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark m-0 d-flex align-items-center gap-2">
                                    <span id="refModalVendorName">Vendor Referrals</span>
                                    <span class="badge bg-primary-subtle text-primary font-monospace px-2 py-1 fs-6" id="refModalVendorCode">CODE</span>
                                </h5>
                                <p class="text-muted small m-0">Track all CRM companies, users, sub-vendors, and invitations joined under this vendor's network</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="#" id="refModalImpersonateBtn" class="btn btn-sm btn-success text-white shadow-sm fw-medium d-inline-flex align-items-center gap-1">
                                <i class="fa-solid fa-right-to-bracket"></i> Login as Vendor
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-4 bg-light-subtle">
                        
                        <!-- Loading State -->
                        <div id="refModalLoading" class="text-center py-5">
                            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
                            <p class="text-muted fw-semibold">Loading joined users and referral details...</p>
                        </div>

                        <!-- Content Container -->
                        <div id="refModalContent" style="display: none;">
                            
                            <!-- Summary Stat Cards -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-3 col-sm-6">
                                    <div class="card border-0 shadow-sm p-3 h-100 bg-white" style="border-radius: 12px; border-left: 4px solid #4f46e5 !important;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="text-muted small fw-semibold text-uppercase">Joined Companies</span>
                                                <h3 class="fw-bold mb-0 text-dark mt-1" id="statJoinedBoxes">0</h3>
                                                <small class="text-muted">CRM Instances</small>
                                            </div>
                                            <div class="rounded-circle p-3 text-primary fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #e0e7ff;">
                                                <i class="fa-solid fa-building"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <div class="card border-0 shadow-sm p-3 h-100 bg-white" style="border-radius: 12px; border-left: 4px solid #059669 !important;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="text-muted small fw-semibold text-uppercase">Total Users Joined</span>
                                                <h3 class="fw-bold mb-0 text-dark mt-1" id="statJoinedUsers">0</h3>
                                                <small class="text-muted">Across All Companies</small>
                                            </div>
                                            <div class="rounded-circle p-3 text-success fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #d1fae5;">
                                                <i class="fa-solid fa-users"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <div class="card border-0 shadow-sm p-3 h-100 bg-white" style="border-radius: 12px; border-left: 4px solid #d97706 !important;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="text-muted small fw-semibold text-uppercase">Sub-Vendors</span>
                                                <h3 class="fw-bold mb-0 text-dark mt-1" id="statJoinedVendors">0</h3>
                                                <small class="text-muted">Referred Partner Vendors</small>
                                            </div>
                                            <div class="rounded-circle p-3 text-warning fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fef3c7;">
                                                <i class="fa-solid fa-handshake"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <div class="card border-0 shadow-sm p-3 h-100 bg-white" style="border-radius: 12px; border-left: 4px solid #2563eb !important;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="text-muted small fw-semibold text-uppercase">Email Invitations</span>
                                                <h3 class="fw-bold mb-0 text-dark mt-1" id="statInvitations">0</h3>
                                                <small class="text-muted"><span id="statJoinedInv" class="text-success fw-bold">0</span> Joined / <span id="statPendingInv" class="text-warning fw-bold">0</span> Pending</small>
                                            </div>
                                            <div class="rounded-circle p-3 text-info fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #dbeafe;">
                                                <i class="fa-solid fa-envelope-open-text"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Referral Link Copy Card -->
                            <div class="card border-0 shadow-sm mb-4 bg-white p-3" style="border-radius: 12px;">
                                <div class="row align-items-center g-2">
                                    <div class="col-lg-6">
                                        <span class="fw-semibold text-dark small"><i class="fa-solid fa-link me-1 text-primary"></i> Vendor's Registration Referral Link:</span>
                                        <div class="input-group input-group-sm mt-1">
                                            <input type="text" class="form-control font-monospace bg-light" id="refModalUrlInput" readonly>
                                            <button class="btn btn-outline-primary" type="button" onclick="copyRefUrl()">
                                                <i class="fa-regular fa-copy me-1"></i> Copy
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="d-flex align-items-center justify-content-lg-end flex-wrap gap-3 small text-muted">
                                            <span><strong>Admin Contact:</strong> <span id="refModalContactPerson" class="text-dark">-</span></span>
                                            <span><strong>Email:</strong> <span id="refModalContactEmail" class="text-dark">-</span></span>
                                            <span><strong>Phone:</strong> <span id="refModalContactPhone" class="text-dark">-</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabs Navigation -->
                            <ul class="nav nav-pills mb-3 gap-2" id="vendorReferralTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active px-4 py-2 rounded-pill fw-semibold" id="tab-boxes-btn" data-bs-toggle="pill" data-bs-target="#tab-boxes-pane" type="button" role="tab">
                                        <i class="fa-solid fa-building me-1"></i> Joined Companies & Users <span class="badge bg-white text-primary ms-1" id="badgeTabBoxes">0</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link px-4 py-2 rounded-pill fw-semibold" id="tab-invitations-btn" data-bs-toggle="pill" data-bs-target="#tab-invitations-pane" type="button" role="tab">
                                        <i class="fa-solid fa-envelope me-1"></i> Email Invitations <span class="badge bg-secondary ms-1" id="badgeTabInv">0</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link px-4 py-2 rounded-pill fw-semibold" id="tab-vendors-btn" data-bs-toggle="pill" data-bs-target="#tab-vendors-pane" type="button" role="tab">
                                        <i class="fa-solid fa-handshake me-1"></i> Referred Vendors <span class="badge bg-secondary ms-1" id="badgeTabVendors">0</span>
                                    </button>
                                </li>
                            </ul>

                            <!-- Tabs Content -->
                            <div class="tab-content" id="vendorReferralTabContent">
                                
                                <!-- Tab 1: Joined Companies & Users -->
                                <div class="tab-pane fade show active" id="tab-boxes-pane" role="tabpanel">
                                    <div class="card border-0 shadow-sm bg-white overflow-hidden" style="border-radius: 12px;">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light text-secondary small text-uppercase">
                                                    <tr>
                                                        <th class="ps-3 py-3 border-0">Company Name</th>
                                                        <th class="py-3 border-0">Admin / Primary Contact</th>
                                                        <th class="py-3 border-0">Phone</th>
                                                        <th class="py-3 border-0 text-center">Active Users</th>
                                                        <th class="py-3 border-0 text-center">Status</th>
                                                        <th class="py-3 border-0 text-end pe-3">Joined Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="refModalBoxesTableBody" class="small">
                                                    <!-- Rendered by JS -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 2: Email Invitations -->
                                <div class="tab-pane fade" id="tab-invitations-pane" role="tabpanel">
                                    <div class="card border-0 shadow-sm bg-white overflow-hidden" style="border-radius: 12px;">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light text-secondary small text-uppercase">
                                                    <tr>
                                                        <th class="ps-3 py-3 border-0">Invited Recipient Email</th>
                                                        <th class="py-3 border-0">Status</th>
                                                        <th class="py-3 border-0">Joined Account Details</th>
                                                        <th class="py-3 border-0">Sent Date</th>
                                                        <th class="py-3 border-0 text-end pe-3">Registered Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="refModalInvitationsTableBody" class="small">
                                                    <!-- Rendered by JS -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 3: Referred Vendors -->
                                <div class="tab-pane fade" id="tab-vendors-pane" role="tabpanel">
                                    <div class="card border-0 shadow-sm bg-white overflow-hidden" style="border-radius: 12px;">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light text-secondary small text-uppercase">
                                                    <tr>
                                                        <th class="ps-3 py-3 border-0">Vendor Company</th>
                                                        <th class="py-3 border-0">Contact Person</th>
                                                        <th class="py-3 border-0">Phone</th>
                                                        <th class="py-3 border-0">Category</th>
                                                        <th class="py-3 border-0 text-center">Status</th>
                                                        <th class="py-3 border-0 text-end pe-3">Joined Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="refModalVendorsTableBody" class="small">
                                                    <!-- Rendered by JS -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer border-top bg-light py-2 px-4" style="border-radius: 0 0 16px 16px;">
                        <button type="button" class="btn btn-secondary shadow-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    function showToast(message, type = 'success') {
        let alertClass = type === 'success' ? 'alert-success' : (type === 'warning' ? 'alert-warning' : 'alert-danger');
        let iconClass = type === 'success' ? 'fa-circle-check' : (type === 'warning' ? 'fa-triangle-exclamation' : 'fa-circle-xmark');
        let toastHtml = `
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 10999;">
                <div class="alert ${alertClass} alert-dismissible fade show shadow-lg d-flex align-items-center gap-2" role="alert" style="min-width: 280px; border-radius: 10px;">
                    <i class="fa-solid ${iconClass} fs-5"></i>
                    <div class="flex-grow-1">${message}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        `;
        let $toast = $(toastHtml).appendTo('body');
        setTimeout(function() {
            $toast.fadeOut(400, function() { $(this).remove(); });
        }, 4000);
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function loadVendorsTable() {
        $('#vendorTableBody').html('<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fa-solid fa-spinner fa-spin me-2"></i> Loading vendors...</td></tr>');
        
        $.ajax({
            url: '{{ route('superadmin.vendors.api.list', ['box_slug' => request()->route('box_slug')]) }}',
            method: 'GET',
            success: function(data) {
                let rows = '';
                if(data.vendors && data.vendors.length > 0) {
                    $.each(data.vendors, function(index, vendor) {
                        let dateObj = new Date(vendor.created_at);
                        let dateStr = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        let detailUrl = "{{ route('superadmin.vendors.detail', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}".replace(':id', vendor.id);
                        let impersonateUrl = "{{ route('superadmin.vendors.impersonate', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}".replace(':id', vendor.id);
                        
                        let statusBadge = '';
                        if(vendor.status === 'approved') {
                            statusBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">Approved</span>';
                        } else if(vendor.status === 'rejected') {
                            statusBadge = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 small">Rejected</span>';
                        } else {
                            statusBadge = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 small">Pending</span>';
                        }

                        let avatarUrl = vendor.profile_image 
                            ? '{{ asset('storage') }}/' + vendor.profile_image
                            : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(vendor.company_name || 'Vendor') + '&background=random';

                        let refBoxesCount = vendor.referred_boxes_count || 0;
                        let invitationsCount = vendor.invitations_count || 0;
                        let subVendorCount = vendor.referred_vendors_count || 0;
                        let totalJoined = refBoxesCount + subVendorCount;

                        let safeCompanyName = (vendor.company_name || 'Vendor').replace(/'/g, "\\'").replace(/"/g, '&quot;');
                        let safeRefCode = vendor.referral_code || '';

                        rows += '<tr>';
                        rows += '<td class="py-3 ps-3">';
                        rows += '  <div class="d-flex align-items-center gap-3">';
                        rows += '    <img src="' + avatarUrl + '" class="rounded-circle object-fit-cover shadow-xs" width="40" height="40" alt="">';
                        rows += '    <div>';
                        rows += '      <h6 class="mb-0 fw-semibold text-dark">' + escapeHtml(vendor.company_name || 'N/A') + '</h6>';
                        rows += '      <span class="badge bg-light text-muted border font-monospace mt-1 px-2 py-0.5" style="font-size: 0.72rem;">' + (vendor.referral_code || 'No Code') + '</span>';
                        rows += '    </div>';
                        rows += '  </div>';
                        rows += '</td>';
                        
                        rows += '<td class="py-3"><div class="d-flex flex-column"><span class="text-dark fw-medium">' + escapeHtml(vendor.user ? vendor.user.name : 'N/A') + '</span><span class="text-muted small">' + escapeHtml(vendor.user ? vendor.user.email : 'N/A') + '</span></div></td>';
                        rows += '<td class="py-3"><span class="text-dark">' + escapeHtml(vendor.mobile_number || 'N/A') + '</span></td>';
                        rows += '<td class="py-3">' + statusBadge + '</td>';
                        
                        // Referrals Column: Clickable button opening full modal breakdown
                        rows += '<td class="py-3 text-center">';
                        rows += '  <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 shadow-sm d-inline-flex align-items-center gap-2 referral-stat-pill" onclick="openReferralsModal(' + vendor.id + ', \'' + safeCompanyName + '\', \'' + safeRefCode + '\')" title="Click to view full referral & joined users breakdown">';
                        rows += '    <i class="fa-solid fa-users text-primary"></i>';
                        rows += '    <span class="fw-bold">' + totalJoined + '</span> <span class="small text-muted">Joined</span>';
                        rows += '    <span class="badge bg-primary text-white rounded-pill px-1.5" style="font-size: 0.7rem;">' + invitationsCount + ' Inv</span>';
                        rows += '  </button>';
                        rows += '</td>';

                        rows += '<td class="py-3"><span class="text-muted small">' + dateStr + '</span></td>';
                        
                        // Actions Column
                        rows += '<td class="py-3 text-end pe-3">';
                        rows += '  <div class="d-inline-flex align-items-center gap-1">';
                        
                        // Direct Login as Vendor Button
                        rows += '    <a href="' + impersonateUrl + '" class="btn btn-sm btn-success text-white shadow-sm fw-medium d-inline-flex align-items-center gap-1" title="Login directly as Vendor (' + escapeHtml(vendor.company_name) + ')">';
                        rows += '      <i class="fa-solid fa-right-to-bracket"></i> <span class="d-none d-lg-inline">Login as Vendor</span>';
                        rows += '    </a>';

                        // Referrals Modal Button
                        rows += '    <button type="button" class="btn btn-sm btn-info text-white shadow-sm d-inline-flex align-items-center gap-1" onclick="openReferralsModal(' + vendor.id + ', \'' + safeCompanyName + '\', \'' + safeRefCode + '\')" title="Inspect Joined Users & Companies">';
                        rows += '      <i class="fa-solid fa-network-wired"></i> <span class="d-none d-xl-inline">Referrals</span>';
                        rows += '    </button>';

                        // Profile Details Link
                        rows += '    <a href="' + detailUrl + '" class="btn btn-sm btn-light border shadow-sm text-secondary" title="View Profile"><i class="fa-solid fa-eye"></i></a>';

                        // Target / Approval Action Button
                        rows += '    <button class="btn btn-sm btn-primary shadow-sm" onclick="openActionModal(' + vendor.id + ', \'' + safeCompanyName + '\', ' + (vendor.monthly_target || 0) + ', \'' + (vendor.target_type || 'monthly') + '\', \'' + (vendor.target_start_date || '') + '\', \'' + (vendor.target_end_date || '') + '\')">Action</button>';
                        rows += '  </div>';
                        rows += '</td>';
                        rows += '</tr>';
                    });
                } else {
                    rows = '<tr><td colspan="7" class="text-center py-4">No vendors found.</td></tr>';
                }
                $('#vendorTableBody').html(rows);
            },
            error: function() {
                $('#vendorTableBody').html('<tr><td colspan="7" class="text-center py-4 text-danger">Error loading vendors.</td></tr>');
            }
        });
    }

    $(document).ready(function() {
        loadVendorsTable();
    });

    // Open Referrals Breakdown Modal
    function openReferralsModal(vendorId, companyName, referralCode) {
        $('#refModalVendorName').text(companyName || 'Vendor Referrals');
        $('#refModalVendorCode').text(referralCode || 'NO CODE');
        
        let impersonateUrl = "{{ route('superadmin.vendors.impersonate', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}".replace(':id', vendorId);
        $('#refModalImpersonateBtn').attr('href', impersonateUrl);

        $('#refModalLoading').show();
        $('#refModalContent').hide();

        var refModal = new bootstrap.Modal(document.getElementById('vendorReferralsModal'));
        refModal.show();

        let apiUrl = "{{ route('superadmin.vendors.api.referrals', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}".replace(':id', vendorId);

        $.ajax({
            url: apiUrl,
            method: 'GET',
            success: function(res) {
                // Summary Stat Cards
                $('#statJoinedBoxes').text(res.summary.total_boxes || 0);
                $('#statJoinedUsers').text(res.summary.total_box_users || 0);
                $('#statJoinedVendors').text(res.summary.total_vendors || 0);
                $('#statInvitations').text(res.summary.total_invitations || 0);
                $('#statJoinedInv').text(res.summary.joined_invitations || 0);
                $('#statPendingInv').text(res.summary.pending_invitations || 0);

                // Vendor Meta Info
                $('#refModalUrlInput').val(res.vendor.referral_url || '');
                $('#refModalContactPerson').text(res.vendor.name || '-');
                $('#refModalContactEmail').text(res.vendor.email || '-');
                $('#refModalContactPhone').text(res.vendor.phone || '-');

                // Tab Badges
                $('#badgeTabBoxes').text(res.boxes ? res.boxes.length : 0);
                $('#badgeTabInv').text(res.invitations ? res.invitations.length : 0);
                $('#badgeTabVendors').text(res.vendors ? res.vendors.length : 0);

                // Tab 1: Boxes / Companies Table
                let boxesHtml = '';
                if (res.boxes && res.boxes.length > 0) {
                    $.each(res.boxes, function(i, box) {
                        boxesHtml += '<tr>';
                        boxesHtml += '<td class="ps-3 py-3">';
                        boxesHtml += '  <div class="fw-semibold text-dark">' + escapeHtml(box.name) + '</div>';
                        boxesHtml += '  <div class="text-muted small font-monospace">' + escapeHtml(box.slug) + '</div>';
                        boxesHtml += '</td>';
                        boxesHtml += '<td class="py-3">';
                        boxesHtml += '  <div class="fw-medium text-dark">' + escapeHtml(box.contact_name) + '</div>';
                        boxesHtml += '  <div class="text-muted small">' + escapeHtml(box.contact_email) + '</div>';
                        boxesHtml += '</td>';
                        boxesHtml += '<td class="py-3"><span class="text-dark">' + escapeHtml(box.contact_phone) + '</span></td>';
                        boxesHtml += '<td class="py-3 text-center"><span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold">' + box.users_count + ' Users</span></td>';
                        boxesHtml += '<td class="py-3 text-center"><span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">' + escapeHtml(box.status) + '</span></td>';
                        boxesHtml += '<td class="py-3 text-end pe-3 text-muted">' + box.created_at + '</td>';
                        boxesHtml += '</tr>';
                    });
                } else {
                    boxesHtml = '<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fa-solid fa-folder-open me-2"></i> No CRM companies registered under this referral code yet.</td></tr>';
                }
                $('#refModalBoxesTableBody').html(boxesHtml);

                // Tab 2: Invitations Table
                let invHtml = '';
                if (res.invitations && res.invitations.length > 0) {
                    $.each(res.invitations, function(i, inv) {
                        let invBadge = inv.status === 'joined' 
                            ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Joined</span>'
                            : '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Pending</span>';

                        invHtml += '<tr>';
                        invHtml += '<td class="ps-3 py-3 font-monospace fw-medium text-dark">' + escapeHtml(inv.recipient_email) + '</td>';
                        invHtml += '<td class="py-3">' + invBadge + '</td>';
                        invHtml += '<td class="py-3 text-dark">' + escapeHtml(inv.joined_entity) + '</td>';
                        invHtml += '<td class="py-3 text-muted">' + inv.last_sent_at + '</td>';
                        invHtml += '<td class="py-3 text-end pe-3 text-muted">' + inv.registered_at + '</td>';
                        invHtml += '</tr>';
                    });
                } else {
                    invHtml = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fa-solid fa-envelope-open me-2"></i> No email invitations sent by this vendor.</td></tr>';
                }
                $('#refModalInvitationsTableBody').html(invHtml);

                // Tab 3: Referred Sub-Vendors Table
                let vendorsHtml = '';
                if (res.vendors && res.vendors.length > 0) {
                    $.each(res.vendors, function(i, v) {
                        let vBadge = v.status === 'approved' 
                            ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Approved</span>'
                            : '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">' + escapeHtml(v.status) + '</span>';

                        vendorsHtml += '<tr>';
                        vendorsHtml += '<td class="ps-3 py-3 fw-semibold text-dark">' + escapeHtml(v.company_name) + '</td>';
                        vendorsHtml += '<td class="py-3">';
                        vendorsHtml += '  <div class="fw-medium text-dark">' + escapeHtml(v.contact_name) + '</div>';
                        vendorsHtml += '  <div class="text-muted small">' + escapeHtml(v.contact_email) + '</div>';
                        vendorsHtml += '</td>';
                        vendorsHtml += '<td class="py-3 text-dark">' + escapeHtml(v.contact_phone) + '</td>';
                        vendorsHtml += '<td class="py-3 text-dark">' + escapeHtml(v.industry) + '</td>';
                        vendorsHtml += '<td class="py-3 text-center">' + vBadge + '</td>';
                        vendorsHtml += '<td class="py-3 text-end pe-3 text-muted">' + v.created_at + '</td>';
                        vendorsHtml += '</tr>';
                    });
                } else {
                    vendorsHtml = '<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fa-solid fa-user-plus me-2"></i> No partner vendors registered under this vendor.</td></tr>';
                }
                $('#refModalVendorsTableBody').html(vendorsHtml);

                $('#refModalLoading').hide();
                $('#refModalContent').fadeIn(200);
            },
            error: function() {
                $('#refModalLoading').html('<div class="text-danger py-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> Failed to load referral details. Please try again.</div>');
            }
        });
    }

    function copyRefUrl() {
        let input = document.getElementById('refModalUrlInput');
        if (!input || !input.value) return;
        
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(function() {
            showToast("Referral link copied to clipboard!", "success");
        }).catch(function() {
            document.execCommand('copy');
            showToast("Referral link copied to clipboard!", "success");
        });
    }

    // Action Modal Management
    function openActionModal(id, companyName, target, type, start, end) {
        $('#modalVendorId').val(id);
        $('#modalCompanyName').text(companyName);
        $('#modalMonthlyTarget').val(target);
        $('#modalTargetType').val(type);
        $('#modalTargetStartDate').val(start);
        $('#modalTargetEndDate').val(end);
        
        $('#actionApprove').prop('checked', true);
        $('#modalIsStepped').prop('checked', false);
        $('#modalSteppedOptions').hide();
        $('#modalStepMonths').val(3);
        $('#modalNextTargetAmount').val('');
        toggleRemarks();
        toggleCustomDates();
        
        var actionModal = new bootstrap.Modal(document.getElementById('vendorActionModal'));
        actionModal.show();
    }

    function toggleSteppedOptions() {
        if ($('#modalIsStepped').is(':checked')) {
            $('#modalSteppedOptions').slideDown(200);
        } else {
            $('#modalSteppedOptions').slideUp(200);
        }
    }

    function toggleCustomDates() {
        if ($('#modalTargetType').val() === 'custom') {
            $('#customDatesContainer').slideDown(200);
        } else {
            $('#customDatesContainer').slideUp(200);
        }
    }

    function toggleRemarks() {
        if ($('#actionReject').is(':checked')) {
            $('#remarksContainer').slideDown(200);
        } else {
            $('#remarksContainer').slideUp(200);
            $('#modalRemarks').val('');
        }
    }

    function submitVendorAction() {
        var id = $('#modalVendorId').val();
        var action = $('input[name="action_type"]:checked').val();
        var remarks = $('#modalRemarks').val();
        var target = $('#modalMonthlyTarget').val();
        var targetType = $('#modalTargetType').val();
        var targetStart = $('#modalTargetStartDate').val();
        var targetEnd = $('#modalTargetEndDate').val();
        var isStepped = $('#modalIsStepped').is(':checked') ? 1 : 0;
        var stepMonths = $('#modalStepMonths').val();
        var nextTarget = $('#modalNextTargetAmount').val();

        if (action === 'reject' && remarks.trim() === '') {
            showToast("Please provide rejection remarks.", "warning");
            return;
        }

        var url = "";
        if (action === 'approve') {
            url = "{{ route('superadmin.vendors.approve', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}".replace(':id', id);
        } else if (action === 'target') {
            url = "{{ route('superadmin.vendors.target', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}".replace(':id', id);
        } else {
            url = "{{ route('superadmin.vendors.reject', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}".replace(':id', id);
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                remarks: remarks,
                monthly_target: target,
                target_amount: target,
                target_type: targetType,
                target_start_date: targetStart,
                target_end_date: targetEnd,
                is_stepped: isStepped,
                step_months: stepMonths,
                next_target_amount: nextTarget
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message || "Vendor updated successfully.", "success");
                    var modalEl = document.getElementById('vendorActionModal');
                    if (modalEl) {
                        var modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) {
                            modalInstance.hide();
                        } else {
                            $('#vendorActionModal').modal('hide');
                        }
                    }
                    setTimeout(function() {
                        loadVendorsTable();
                    }, 800);
                } else {
                    showToast(response.message || "An error occurred.", "danger");
                }
            },
            error: function(xhr) {
                var errorMsg = "Server error occurred.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showToast(errorMsg, "danger");
            }
        });
    }
</script>
@endsection
