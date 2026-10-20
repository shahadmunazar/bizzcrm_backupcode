@extends('layouts.main')

@section('content')
@php
    $currentBoxSlug = $boxSlug ?? request()->route('box_slug') ?? request()->segment(1) ?? 'demobizzcom';
@endphp
<style>
/* Profile Custom Styles */
.profile-cover {
    height: 140px;
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    border-radius: 14px 14px 0 0;
}
.profile-avatar-wrapper {
    margin-top: -55px;
    padding-left: 25px;
}
.profile-avatar {
    width: 100px;
    height: 100px;
    border: 4px solid #fff;
    border-radius: 16px;
    background: #fff;
    object-fit: contain;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
}
.referral-box {
    background-color: #f8fafc;
    border: 2px dashed #cbd5e1;
    border-radius: 14px;
    padding: 1.5rem 1rem;
    margin-top: 1rem;
}
.ref-title {
    color: #8b94a6;
    font-weight: 700;
    letter-spacing: 0.05em;
    font-size: 0.85rem;
    margin-bottom: 1rem;
}
.ref-code-badge {
    background-color: #4f46e5;
    color: white;
    font-family: 'Monaco', 'Consolas', monospace;
    font-weight: 700;
    font-size: 1.35rem;
    letter-spacing: 2px;
    padding: 0.4rem 1.1rem;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
}
.action-btn {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    color: #475569;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.action-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #cbd5e1;
    transform: translateY(-2px);
}
.stat-card { 
    border: none; 
    border-radius: 12px; 
    transition: transform 0.2s; 
}
.stat-card:hover { 
    transform: translateY(-3px); 
}
.progress-thin {
    height: 8px;
    border-radius: 10px;
}
.btn-xs {
    padding: 0.22rem 0.55rem;
    font-size: 0.75rem;
    border-radius: 6px;
    font-weight: 600;
}
.nav-target-pills .nav-link {
    border-radius: 20px;
    padding: 0.45rem 1.1rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    transition: all 0.2s;
}
.nav-target-pills .nav-link.active {
    background: #4f46e5;
    color: #ffffff !important;
}
.matrix-card {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}
.matrix-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
</style>

<div class="page-wrapper">
    <div class="content">
        <div class="container-fluid p-0">
            
            <!-- Top Navigation & Back -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('superadmin.vendors.index', ['box_slug' => $currentBoxSlug]) }}" class="btn btn-sm btn-light border shadow-sm text-secondary fw-medium">
                    <i class="fa-solid fa-arrow-left me-2"></i>Back to Vendors
                </a>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-light border shadow-sm text-secondary" onclick="loadVendorDetail()">
                        <i class="fa-solid fa-rotate me-1"></i> Refresh
                    </button>
                </div>
            </div>

            <!-- Profile Header Card -->
            <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 14px;">
                <div class="profile-cover"></div>
                <div class="profile-avatar-wrapper d-flex align-items-end justify-content-between pe-4 pb-3 flex-wrap gap-3">
                    <div class="d-flex align-items-end gap-3">
                        <div class="bg-white rounded-3 p-1">
                            <div id="vd-avatar" class="profile-avatar bg-primary-subtle d-flex align-items-center justify-content-center text-primary fs-2 fw-bold">
                                {{ strtoupper(substr($vendor->company_name ?? 'VE', 0, 2)) }}
                            </div>
                        </div>
                        <div class="mb-1">
                            <div class="d-flex align-items-center gap-2">
                                <h3 id="vd-company-name-heading" class="fw-bold m-0 text-dark">{{ $vendor->company_name ?? 'Vendor Details' }}</h3>
                                <span id="vd-status-badge-container">
                                    @if(($vendor->status ?? '') === 'approved')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 small fw-semibold">Active Vendor</span>
                                    @elseif(($vendor->status ?? '') === 'rejected')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 small fw-semibold">Rejected</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 small fw-semibold">Pending Approval</span>
                                    @endif
                                </span>
                            </div>
                            <p class="text-muted small m-0 mt-1">
                                <i class="fa-solid fa-location-dot me-1 text-danger"></i> 
                                <span id="vd-location">{{ $vendor->city?->name ?? 'Location N/A' }}</span>
                                <span class="mx-2">•</span>
                                <i class="fa-solid fa-tag me-1 text-primary"></i>
                                <span id="vd-category">{{ $vendor->industry?->name ?? 'General' }}</span>
                            </p>
                        </div>
                    </div>
                    <div id="vd-actions" class="d-flex align-items-center gap-2 mb-1">
                        <button class="btn btn-outline-primary shadow-sm fw-medium px-3 py-1.5 d-inline-flex align-items-center gap-1" onclick="openTargetModal()">
                            <i class="fa-solid fa-bullseye"></i> Set / Update Target
                        </button>
                        <a href="{{ route('superadmin.vendors.impersonate', ['box_slug' => $currentBoxSlug, 'id' => $vendor->id ?? $id ?? request()->route('id')]) }}" class="btn btn-success text-white shadow-sm fw-medium px-3 py-1.5 d-inline-flex align-items-center gap-1">
                            <i class="fa-solid fa-right-to-bracket"></i> Login as Vendor
                        </a>
                    </div>
                </div>
            </div>

            <!-- KPI Summary Row -->
            <div class="row g-3 mb-4">
                <!-- Current Month Target -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card shadow-sm p-3 h-100 bg-white border-0" style="border-left: 4px solid #4f46e5 !important;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="text-muted small fw-semibold text-uppercase">Current Month Target</span>
                                <h4 class="fw-bold mb-0 text-dark mt-1" id="kpi-target-amount">₹0.00</h4>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1" id="kpi-target-type">Monthly Target</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-circle p-2" onclick="openTargetModal()" title="Set / Edit Target">
                                <i class="fa-solid fa-bullseye"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>Sales: <strong class="text-success" id="kpi-target-achieved">₹0.00</strong></span>
                                <span class="fw-bold" id="kpi-target-percent">0%</span>
                            </div>
                            <div class="progress progress-thin bg-light">
                                <div id="kpi-target-progress-bar" class="progress-bar bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gross Sales Generated -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card shadow-sm p-3 h-100 bg-white border-0" style="border-left: 4px solid #059669 !important;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-semibold text-uppercase">Gross Sales Generated</span>
                                <h4 class="fw-bold mb-0 text-dark mt-1" id="kpi-gross-sales">₹0.00</h4>
                                <small class="text-muted">Total referral plan volume</small>
                            </div>
                            <div class="rounded-circle p-3 text-success fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #d1fae5;">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Commission Earned -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card shadow-sm p-3 h-100 bg-white border-0" style="border-left: 4px solid #2563eb !important;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-semibold text-uppercase">Commission Cleared (Paid)</span>
                                <h4 class="fw-bold mb-0 text-success mt-1" id="kpi-commission-earned">₹0.00</h4>
                                <small class="text-muted">Unpaid/Pending: <strong class="text-warning" id="kpi-commission-pending">₹0.00</strong></small>
                            </div>
                            <div class="rounded-circle p-3 text-primary fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #dbeafe;">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Referrals Network -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card shadow-sm p-3 h-100 bg-white border-0" style="border-left: 4px solid #d97706 !important;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-semibold text-uppercase">Joined Referrals</span>
                                <h4 class="fw-bold mb-0 text-dark mt-1" id="kpi-referrals-count">0</h4>
                                <small class="text-muted"><span id="kpi-boxes-count">0</span> Companies • <span id="kpi-vendors-count">0</span> Vendors</small>
                            </div>
                            <div class="rounded-circle p-3 text-warning fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fef3c7;">
                                <i class="fa-solid fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Target Performance Matrix: Monthly, Quarterly, Half-Yearly, Yearly -->
            <div class="card border-0 shadow-sm p-4 mb-4 bg-white" style="border-radius: 14px;">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-chart-line text-primary"></i> Target & Performance Matrix
                        </h5>
                        <p class="text-muted small m-0">Track achievement across Monthly, Quarterly, Half-Yearly, and Yearly schedules (including stepped target increases)</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <ul class="nav nav-target-pills gap-1" id="targetPeriodTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pill-monthly-tab" data-bs-toggle="pill" data-bs-target="#tab-monthly" type="button" role="tab">
                                    <i class="fa-regular fa-calendar-days me-1"></i> Monthly
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pill-quarterly-tab" data-bs-toggle="pill" data-bs-target="#tab-quarterly" type="button" role="tab">
                                    <i class="fa-solid fa-chart-pie me-1"></i> Quarterly
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pill-half-tab" data-bs-toggle="pill" data-bs-target="#tab-half" type="button" role="tab">
                                    <i class="fa-solid fa-scale-balanced me-1"></i> Half-Yearly
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pill-yearly-tab" data-bs-toggle="pill" data-bs-target="#tab-yearly" type="button" role="tab">
                                    <i class="fa-solid fa-award me-1"></i> Yearly
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pill-history-tab" data-bs-toggle="pill" data-bs-target="#tab-history" type="button" role="tab">
                                    <i class="fa-solid fa-clock-rotate-left me-1"></i> Target Schedules (<span id="schedulesCountBadge">0</span>)
                                </button>
                            </li>
                        </ul>
                        <button class="btn btn-sm btn-primary shadow-sm fw-medium ms-2 d-inline-flex align-items-center gap-1" onclick="openTargetModal()">
                            <i class="fa-solid fa-plus"></i> Set New Target
                        </button>
                    </div>
                </div>

                <div class="tab-content" id="targetPeriodTabContent">
                    
                    <!-- 1. Monthly Breakdown Tab -->
                    <div class="tab-pane fade show active" id="tab-monthly" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-secondary small text-uppercase">
                                    <tr>
                                        <th class="ps-3 py-3 border-0">Month</th>
                                        <th class="py-3 border-0">Target Quota</th>
                                        <th class="py-3 border-0">Actual Sales</th>
                                        <th class="py-3 border-0">Commission Earned</th>
                                        <th class="py-3 border-0" style="min-width: 180px;">Achievement Progress</th>
                                        <th class="py-3 border-0 text-center">Status</th>
                                        <th class="py-3 border-0 text-end pe-3">Shortfall / Surplus</th>
                                    </tr>
                                </thead>
                                <tbody id="targetMonthlyTableBody" class="small">
                                    <tr><td colspan="7" class="text-center py-4 text-muted"><i class="fa-solid fa-spinner fa-spin me-2"></i> Loading monthly targets...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 2. Quarterly Breakdown Tab -->
                    <div class="tab-pane fade" id="tab-quarterly" role="tabpanel">
                        <div class="row g-3" id="quarterlyCardsContainer">
                            <!-- Populated via JS -->
                        </div>
                    </div>

                    <!-- 3. Half-Yearly Breakdown Tab -->
                    <div class="tab-pane fade" id="tab-half" role="tabpanel">
                        <div class="row g-3" id="halfYearCardsContainer">
                            <!-- Populated via JS -->
                        </div>
                    </div>

                    <!-- 4. Yearly Summary Tab -->
                    <div class="tab-pane fade" id="tab-yearly" role="tabpanel">
                        <div id="yearlySummaryContainer">
                            <!-- Populated via JS -->
                        </div>
                    </div>

                    <!-- 5. Target Schedules & History Tab -->
                    <div class="tab-pane fade" id="tab-history" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-secondary small text-uppercase">
                                    <tr>
                                        <th class="ps-3 py-3 border-0">Period Type</th>
                                        <th class="py-3 border-0">Target Amount</th>
                                        <th class="py-3 border-0">Monthly Equivalent</th>
                                        <th class="py-3 border-0">Effective Duration</th>
                                        <th class="py-3 border-0">Notes / Step Description</th>
                                        <th class="py-3 border-0 text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="targetHistoryTableBody" class="small">
                                    <tr><td colspan="6" class="text-center py-4 text-muted"><i class="fa-solid fa-spinner fa-spin me-2"></i> Loading target history...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Main Details Row -->
            <div class="row g-4">
                
                <!-- Left Column: Business Profile & Commission Transactions -->
                <div class="col-lg-8">
                    
                    <!-- Business Information Card -->
                    <div class="card border-0 shadow-sm p-4 mb-4 bg-white" style="border-radius: 12px;">
                        <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-briefcase text-primary"></i> Business & Contact Profile
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.72rem;">Company Name</label>
                                <p id="vd-company-name" class="fw-semibold text-dark mb-0">{{ $vendor->company_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.72rem;">Contact Email</label>
                                <p id="vd-email" class="fw-semibold text-dark mb-0">{{ $vendor->user?->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.72rem;">Phone Number</label>
                                <p id="vd-phone" class="fw-semibold text-dark mb-0">{{ $vendor->mobile_number ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.72rem;">Business Category</label>
                                <p id="vd-industry" class="fw-semibold text-dark mb-0">{{ $vendor->industry?->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-12 border-top pt-3">
                                <label class="small text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.72rem;">Office Address</label>
                                <p id="vd-address" class="fw-semibold text-dark mb-0">{{ $vendor->address ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Commission Transactions History Table -->
                    <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 12px;">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-receipt text-primary"></i> Referral Commission Transactions
                                </h5>
                                <p class="text-muted small m-0">Commissions credited from referred user offline/online subscriptions</p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-secondary small text-uppercase">
                                    <tr>
                                        <th class="ps-3 py-3 border-0">Txn ID</th>
                                        <th class="py-3 border-0">Referred Company</th>
                                        <th class="py-3 border-0">Plan Amount</th>
                                        <th class="py-3 border-0">Commission</th>
                                        <th class="py-3 border-0 text-center">Status</th>
                                        <th class="py-3 border-0">Timeline / Details</th>
                                        <th class="py-3 border-0 text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="transactionTableBody" class="small">
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fa-solid fa-spinner fa-spin me-2"></i> Loading transactions...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Referral Network & Link Card -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm p-4 text-center bg-white" style="border-radius: 12px;">
                        <div class="icon-box bg-primary-subtle text-primary mx-auto mb-3" style="width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-gift fs-4"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Vendor Partner Referral</h5>
                        <p class="text-muted small mb-3">Referred CRM signups earn commission based on dynamic configured percentages.</p>
                        
                        <div class="referral-box">
                            <span class="ref-title d-block text-uppercase text-center">Referral Code</span>
                            <div class="d-flex flex-column align-items-center justify-content-center gap-3">
                                <span id="refCode" class="ref-code-badge text-center">{{ $vendor->referral_code ?? 'NO CODE' }}</span>
                                <div class="d-flex gap-2">
                                    <button onclick="copyCode()" class="btn action-btn" title="Copy Referral Code">
                                        <i id="copyIcon" class="fa-regular fa-copy"></i>
                                    </button>
                                    <button onclick="copyReferralLink()" class="btn action-btn" title="Copy Registration Link">
                                        <i class="fa-solid fa-link"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Registration URL Input -->
                        <div class="mt-3 text-start">
                            <label class="small text-muted fw-semibold">Registration URL</label>
                            <div class="input-group input-group-sm mt-1">
                                <input type="text" class="form-control font-monospace bg-light" id="vd-referral-url" readonly value="{{ $vendor->referral_code ? url('/register') . '?ref=' . $vendor->referral_code : '' }}">
                                <button class="btn btn-outline-primary" type="button" onclick="copyReferralLink()">
                                    Copy
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-3 border-top text-start">
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Commission Rate:</span>
                                <span class="fw-bold text-primary" id="vd-rate-percent">0%</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Successful Referrals:</span>
                                <span class="fw-bold text-dark" id="vd-referral-count">0</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Total Gross Volume:</span>
                                <span class="fw-bold text-dark" id="vd-gross-sales">₹0.00</span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Total Rewards Earned:</span>
                                <span class="fw-bold text-success" id="vd-total-rewards">₹0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- Vendor Target Setup Modal (Monthly, Quarterly, Half-Yearly, Yearly & Stepped Target) -->
<div class="modal fade" id="vendorTargetModal" tabindex="-1" aria-labelledby="vendorTargetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <div class="modal-header bg-light border-bottom px-4 py-3" style="border-radius: 14px 14px 0 0;">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="vendorTargetModalLabel">
                    <i class="fa-solid fa-bullseye text-primary"></i> Set Vendor Sales Target Quota
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="vendorTargetForm">
                    <input type="hidden" id="targetVendorId" value="{{ request()->route('id') }}">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Target Period <span class="text-danger">*</span></label>
                            <select class="form-select" id="modalTargetType" onchange="handleTargetTypeChange()">
                                <option value="monthly">Monthly (per month)</option>
                                <option value="quarterly">Quarterly (3 months)</option>
                                <option value="half_yearly">Half-Yearly (6 months)</option>
                                <option value="yearly">Yearly (12 months)</option>
                                <option value="custom">Custom Date Range</option>
                            </select>
                            <div class="form-text text-muted" id="periodHelpText">Target amount will apply monthly.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Target Amount (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="modalTargetAmount" placeholder="e.g. 25000" required>
                            </div>
                            <div class="form-text text-muted">Base sales quota for this period.</div>
                        </div>
                    </div>

                    <!-- Stepped / Increased Target Checkbox -->
                    <div class="card border border-primary-subtle bg-primary-subtle bg-opacity-10 p-3 mb-3" style="border-radius: 10px;">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="checkSteppedTarget" onchange="toggleSteppedSection()">
                            <label class="form-check-label fw-bold text-dark ms-1" for="checkSteppedTarget">
                                <i class="fa-solid fa-stairs text-primary me-1"></i> Enable Stepped Target Increase
                            </label>
                        </div>
                        <div class="small text-muted mt-1 ps-4">
                            Keep target same for initial months (e.g. first 3 months), and then automatically increase target thereafter.
                        </div>

                        <!-- Stepped Options (Hidden by default) -->
                        <div id="steppedFieldsContainer" class="mt-3 ps-4 pt-2 border-top" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Initial Duration (Months)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" min="1" max="12" class="form-control" id="modalStepMonths" value="3">
                                        <span class="input-group-text">Months</span>
                                    </div>
                                    <div class="form-text text-muted">Initial target applies for these months.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Increased Target Amount (₹) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" min="0" class="form-control" id="modalNextTargetAmount" placeholder="e.g. 50000">
                                    </div>
                                    <div class="form-text text-muted">New monthly quota effective after initial duration.</div>
                                </div>
                            </div>
                            <div class="alert alert-light border mt-2 mb-0 py-2 px-3 small text-muted">
                                <i class="fa-solid fa-circle-info text-primary me-1"></i> 
                                <span id="steppedPreviewText">Target will be ₹25,000/mo for first 3 months, then increased to ₹50,000/mo onwards.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Effective Start Date</label>
                            <input type="date" class="form-control" id="modalTargetStart" value="{{ date('Y-m-01') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">End Date <span class="text-muted">(Optional / Ongoing)</span></label>
                            <input type="date" class="form-control" id="modalTargetEnd">
                            <div class="form-text text-muted">Leave empty to keep target active indefinitely.</div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Remarks / Label</label>
                        <input type="text" class="form-control" id="modalTargetNotes" placeholder="e.g. Q4 Target Schedule">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top bg-light py-2 px-4" style="border-radius: 0 0 14px 14px;">
                <button type="button" class="btn btn-light shadow-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary shadow-sm px-4" id="saveTargetBtn" onclick="saveVendorTarget()">
                    <i class="fa-solid fa-check me-1"></i> Save Target
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Commission Payout Modal -->
<div class="modal fade" id="commissionPaymentModal" tabindex="-1" aria-labelledby="commissionPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <div class="modal-header border-bottom py-3 px-4 bg-light" style="border-radius: 14px 14px 0 0;">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="commissionPaymentModalLabel">
                    <i class="fa-solid fa-money-bill-transfer text-success"></i> Pay Vendor Commission
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="payModalTxnId" value="">
                
                <div class="p-3 bg-success-subtle bg-opacity-25 border border-success-subtle rounded-3 mb-3 text-center">
                    <span class="small text-muted text-uppercase fw-semibold">Commission Amount to Settle</span>
                    <h3 class="fw-bold text-success mb-0 mt-1" id="payModalAmount">₹0.00</h3>
                    <small class="text-muted" id="payModalTxnRef">#TXN-00000</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Payment / Transfer Reference (UTR, Cheque, Txn ID)</label>
                    <input type="text" class="form-control" id="payModalReference" placeholder="e.g. UTR-983274923 or NEFT-8392">
                    <div class="form-text text-muted">Optional reference number for payout tracking.</div>
                </div>

                <div class="mb-2">
                    <label class="form-label fw-semibold small">Payout Remarks / Notes</label>
                    <textarea class="form-control" id="payModalNotes" rows="2" placeholder="e.g. Commission transferred via direct IMPS"></textarea>
                </div>
            </div>
            <div class="modal-footer border-top bg-light py-2 px-4" style="border-radius: 0 0 14px 14px;">
                <button type="button" class="btn btn-light shadow-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success shadow-sm px-4" id="confirmPayBtn" onclick="submitCommissionPayment()">
                    <i class="fa-solid fa-check me-1"></i> Confirm & Mark Paid
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const vendorId = "{{ $vendor->id ?? $id ?? request()->route('id') }}";
    let currentVendorData = null;

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

    function formatCurrency(num) {
        return '₹' + parseFloat(num || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function loadVendorDetail() {
        let detailApiUrl = "{{ route('superadmin.vendors.api.detail', ['box_slug' => $currentBoxSlug, 'id' => ':id']) }}".replace(':id', vendorId);
        
        $.ajax({
            url: detailApiUrl,
            method: 'GET',
            success: function(data) {
                currentVendorData = data;
                let v = data.vendor;
                let s = data.stats || {};
                let overview = data.target_overview || {};

                // Header Profile Info
                $('#vd-company-name-heading').text(v.company_name || 'N/A');
                $('#vd-company-name').text(v.company_name || 'N/A');
                $('#vd-email').text(v.user ? v.user.email : 'N/A');
                $('#vd-phone').text(v.mobile_number || 'N/A');
                $('#vd-industry').text(v.industry ? v.industry.name : 'N/A');
                $('#vd-category').text(v.industry ? v.industry.name : 'General');
                $('#vd-address').text(v.address || 'N/A');
                $('#vd-location').text(v.city ? v.city.name : 'Location N/A');
                $('#refCode').text(v.referral_code || 'N/A');

                if(v.company_name) {
                    $('#vd-avatar').text(v.company_name.substring(0,2).toUpperCase());
                }

                // Status Badge
                let statusBadge = '';
                if(v.status === 'approved') {
                    statusBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 small fw-semibold">Active Vendor</span>';
                } else if(v.status === 'rejected') {
                    statusBadge = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 small fw-semibold">Rejected</span>';
                } else {
                    statusBadge = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 small fw-semibold">Pending Approval</span>';
                }
                $('#vd-status-badge-container').html(statusBadge);

                // Top Header Action Buttons
                let impersonateUrl = "{{ route('superadmin.vendors.impersonate', ['box_slug' => $currentBoxSlug, 'id' => ':id']) }}".replace(':id', v.id);
                $('#vd-actions').html(`
                    <button class="btn btn-outline-primary shadow-sm fw-medium px-3 py-1.5 d-inline-flex align-items-center gap-1" onclick="openTargetModal()">
                        <i class="fa-solid fa-bullseye"></i> Set / Update Target
                    </button>
                    <a href="${impersonateUrl}" class="btn btn-success text-white shadow-sm fw-medium px-3 py-1.5 d-inline-flex align-items-center gap-1">
                        <i class="fa-solid fa-right-to-bracket"></i> Login as Vendor
                    </a>
                `);

                // Top KPI Summary Cards
                let mPerf = data.current_month_perf || {};
                let currentMonthTarget = parseFloat(mPerf.target_amount || s.monthly_target || 0);
                $('#kpi-target-amount').text(formatCurrency(currentMonthTarget));
                
                let targetTypeLabel = v.target_type ? v.target_type.replace('_', ' ').toUpperCase() : 'MONTHLY';
                $('#kpi-target-type').text(targetTypeLabel);
                
                let currentMonthSales = parseFloat(mPerf.actual_sales || s.current_month_sales || 0);
                $('#kpi-target-achieved').text(formatCurrency(currentMonthSales) + ' sales');

                let targetPercent = mPerf.achievement_percent !== undefined ? mPerf.achievement_percent : (s.target_progress || 0);
                $('#kpi-target-percent').text(targetPercent + '%');
                $('#kpi-target-progress-bar').css('width', Math.min(100, targetPercent) + '%');

                let paidCommission = parseFloat(s.paid_commission || 0);
                let unpaidCommission = parseFloat(s.unpaid_commission || s.pending_commission || 0);

                $('#kpi-gross-sales').text(formatCurrency(s.gross_sales || 0));
                $('#kpi-commission-earned').text(formatCurrency(paidCommission));
                $('#kpi-commission-pending').text(formatCurrency(unpaidCommission));
                $('#kpi-commission-rate').text((s.commission_percentage || 10) + '% Rate');
                $('#kpi-referrals-count').text(s.total_referrals || 0);
                $('#kpi-boxes-count').text(s.referred_boxes_count || 0);
                $('#kpi-vendors-count').text(s.referred_vendors_count || 0);

                // Right Sidebar Stats
                let regUrl = v.referral_code ? `{{ url('/register') }}?ref=${v.referral_code}` : '';
                $('#vd-referral-url').val(regUrl);
                $('#vd-rate-percent').text((s.commission_percentage || 10) + '%');
                $('#vd-referral-count').text(s.total_referrals || 0);
                $('#vd-gross-sales').text(formatCurrency(s.gross_sales || 0));
                $('#vd-total-rewards').text(formatCurrency(s.total_commission || 0));

                // Render Target Performance Matrix
                renderTargetMatrix(overview);

                // Transactions Table
                let rows = '';
                if(data.transactions && data.transactions.length > 0) {
                    $.each(data.transactions, function(index, txn) {
                        let statusBadge = '';
                        let actionButtons = '';

                        if (txn.status === 'paid') {
                            statusBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small"><i class="fa-solid fa-check-double me-1"></i> Paid</span>';
                            actionButtons = `
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <span class="badge bg-light text-muted border small"><i class="fa-solid fa-circle-check text-success me-1"></i> Settled</span>
                                    <button class="btn btn-xs btn-outline-secondary" onclick="markCommissionUnpaid(${txn.id})" title="Revert to Unpaid">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                </div>
                            `;
                        } else if (txn.status === 'verified') {
                            statusBadge = '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 small"><i class="fa-solid fa-check-circle me-1"></i> Verified</span>';
                            actionButtons = `
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <button class="btn btn-xs btn-success text-white shadow-sm" onclick="openPayCommissionModal(${txn.id}, ${txn.commission_amount})" title="Pay Vendor">
                                        <i class="fa-solid fa-wallet me-1"></i> Pay
                                    </button>
                                    <button class="btn btn-xs btn-outline-secondary" onclick="markCommissionUnpaid(${txn.id})" title="Revert to Unpaid">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                </div>
                            `;
                        } else {
                            // Unpaid or pending
                            statusBadge = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 small"><i class="fa-solid fa-clock me-1"></i> Unpaid</span>';
                            actionButtons = `
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <button class="btn btn-xs btn-outline-info" onclick="verifyCommission(${txn.id})" title="Verify Commission">
                                        <i class="fa-solid fa-check-circle me-1"></i> Verify
                                    </button>
                                    <button class="btn btn-xs btn-success text-white shadow-sm" onclick="openPayCommissionModal(${txn.id}, ${txn.commission_amount})" title="Pay Vendor">
                                        <i class="fa-solid fa-wallet me-1"></i> Pay
                                    </button>
                                </div>
                            `;
                        }

                        let detailInfo = '';
                        if (txn.status === 'paid' && txn.paid_at) {
                            detailInfo = `<div class="small text-success fw-medium">Paid: ${txn.paid_at}</div>`;
                            if (txn.payment_reference) {
                                detailInfo += `<div class="small text-muted font-monospace">Ref: ${escapeHtml(txn.payment_reference)}</div>`;
                            }
                        } else if (txn.status === 'verified' && txn.verified_at) {
                            detailInfo = `<div class="small text-info fw-medium">Verified: ${txn.verified_at}</div>`;
                        } else {
                            detailInfo = `<div class="small text-muted">Awaiting Admin Action</div>`;
                        }
                            
                        rows += '<tr>';
                        rows += '<td class="py-3 ps-3"><span class="fw-semibold text-dark font-monospace">#TXN-' + txn.id.toString().padStart(5, '0') + '</span></td>';
                        rows += '<td class="py-3"><div class="fw-semibold text-dark">' + escapeHtml(txn.box_name) + '</div><div class="small text-muted font-monospace">' + escapeHtml(txn.box_slug) + '</div></td>';
                        rows += '<td class="py-3"><span class="text-dark fw-medium">' + formatCurrency(txn.transaction_amount) + '</span></td>';
                        rows += '<td class="py-3"><span class="text-primary fw-bold">' + formatCurrency(txn.commission_amount) + '</span> <span class="small text-muted">(' + txn.commission_percentage + '%)</span></td>';
                        rows += '<td class="py-3 text-center">' + statusBadge + '</td>';
                        rows += '<td class="py-3"><div class="small text-dark">' + txn.created_at + '</div>' + detailInfo + '</td>';
                        rows += '<td class="py-3 text-end pe-3">' + actionButtons + '</td>';
                        rows += '</tr>';
                    });
                } else {
                    rows = '<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fa-solid fa-receipt me-2"></i> No commission transactions recorded yet for this vendor.</td></tr>';
                }
                $('#transactionTableBody').html(rows);
            },
            error: function() {
                $('#transactionTableBody').html('<tr><td colspan="7" class="text-center py-4 text-danger">Error loading vendor details.</td></tr>');
            }
        });
    }

    function renderTargetMatrix(overview) {
        if(!overview) return;

        // 1. Monthly Table (12 Months)
        let monthlyRows = '';
        if(overview.months && overview.months.length > 0) {
            let currentM = overview.current_month || (new Date().getMonth() + 1);
            $.each(overview.months, function(i, m) {
                let isCurrent = (m.month_number === currentM);
                let rowClass = isCurrent ? 'table-primary bg-primary-subtle bg-opacity-25' : '';
                
                let badgeClass = 'bg-secondary-subtle text-secondary';
                let statusLabel = 'Not Reached';
                if(m.is_achieved) {
                    badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                    statusLabel = 'Achieved';
                } else if(m.actual_sales > 0) {
                    badgeClass = 'bg-info-subtle text-info border border-info-subtle';
                    statusLabel = 'In Progress';
                }

                let shortfallHtml = m.shortfall > 0 
                    ? `<span class="text-danger">-${formatCurrency(m.shortfall)}</span>`
                    : (m.actual_sales > m.target_amount ? `<span class="text-success">+${formatCurrency(m.actual_sales - m.target_amount)}</span>` : '<span class="text-muted">₹0.00</span>');

                monthlyRows += `
                    <tr class="${rowClass}">
                        <td class="ps-3 py-3 fw-semibold text-dark">
                            ${m.month_name} ${m.year}
                            ${isCurrent ? '<span class="badge bg-primary ms-1" style="font-size:0.65rem;">Current Month</span>' : ''}
                        </td>
                        <td class="py-3 fw-bold text-dark">${formatCurrency(m.target_amount)}</td>
                        <td class="py-3 text-dark fw-medium">${formatCurrency(m.actual_sales)}</td>
                        <td class="py-3 text-success fw-semibold">${formatCurrency(m.actual_commission)}</td>
                        <td class="py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1 progress-thin bg-light">
                                    <div class="progress-bar ${m.is_achieved ? 'bg-success' : 'bg-primary'}" style="width: ${Math.min(100, m.achievement_percent)}%;"></div>
                                </div>
                                <span class="fw-bold small" style="min-width: 45px;">${m.achievement_percent}%</span>
                            </div>
                        </td>
                        <td class="py-3 text-center"><span class="badge ${badgeClass} px-2 py-1">${statusLabel}</span></td>
                        <td class="py-3 text-end pe-3 small">${shortfallHtml}</td>
                    </tr>
                `;
            });
        }
        $('#targetMonthlyTableBody').html(monthlyRows);

        // 2. Quarterly Cards (Q1..Q4)
        let qHtml = '';
        if(overview.quarters && overview.quarters.length > 0) {
            let currentQ = overview.current_quarter || 1;
            $.each(overview.quarters, function(i, q) {
                let isCurrent = (q.quarter_number === currentQ);
                let borderStyle = isCurrent ? 'border: 2px solid #4f46e5 !important;' : '';
                qHtml += `
                    <div class="col-md-3 col-sm-6">
                        <div class="card matrix-card p-3 h-100 bg-white" style="${borderStyle}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold m-0 text-dark">Q${q.quarter_number} (${q.year})</h6>
                                ${isCurrent ? '<span class="badge bg-primary" style="font-size:0.65rem;">Active</span>' : ''}
                            </div>
                            <div class="small text-muted mb-2">${q.start_date} to ${q.end_date}</div>
                            
                            <div class="mb-3">
                                <div class="small text-muted">Quarterly Target:</div>
                                <div class="h5 fw-bold text-dark m-0">${formatCurrency(q.target_amount)}</div>
                            </div>
                            
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Achieved: <strong>${formatCurrency(q.actual_sales)}</strong></span>
                                    <span class="fw-bold ${q.is_achieved ? 'text-success' : 'text-primary'}">${q.achievement_percent}%</span>
                                </div>
                                <div class="progress progress-thin bg-light">
                                    <div class="progress-bar ${q.is_achieved ? 'bg-success' : 'bg-primary'}" style="width: ${Math.min(100, q.achievement_percent)}%;"></div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-auto small">
                                <span class="text-muted">Commission:</span>
                                <span class="fw-bold text-success">${formatCurrency(q.actual_commission)}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
        }
        $('#quarterlyCardsContainer').html(qHtml);

        // 3. Half-Yearly Cards (H1, H2)
        let hHtml = '';
        if(overview.half_years && overview.half_years.length > 0) {
            let currentH = overview.current_half || 1;
            $.each(overview.half_years, function(i, h) {
                let isCurrent = (h.half_number === currentH);
                let borderStyle = isCurrent ? 'border: 2px solid #4f46e5 !important;' : '';
                hHtml += `
                    <div class="col-md-6">
                        <div class="card matrix-card p-4 h-100 bg-white" style="${borderStyle}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="fw-bold m-0 text-dark">Half-Year ${h.half_number} (H${h.half_number} ${h.year})</h5>
                                ${isCurrent ? '<span class="badge bg-primary px-2 py-1">Current Active Half</span>' : ''}
                            </div>
                            <div class="small text-muted mb-3">${h.start_date} to ${h.end_date}</div>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <span class="text-muted small">Combined 6-Month Target:</span>
                                    <h4 class="fw-bold text-dark m-0">${formatCurrency(h.target_amount)}</h4>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted small">Actual Sales Achieved:</span>
                                    <h4 class="fw-bold ${h.is_achieved ? 'text-success' : 'text-primary'} m-0">${formatCurrency(h.actual_sales)}</h4>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Target Completion:</span>
                                    <span class="fw-bold fs-6">${h.achievement_percent}%</span>
                                </div>
                                <div class="progress progress-thin bg-light" style="height: 10px;">
                                    <div class="progress-bar ${h.is_achieved ? 'bg-success' : 'bg-primary'}" style="width: ${Math.min(100, h.achievement_percent)}%;"></div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-top pt-2 small">
                                <span>Shortfall: <strong class="${h.shortfall > 0 ? 'text-danger' : 'text-muted'}">${formatCurrency(h.shortfall)}</strong></span>
                                <span>Commission: <strong class="text-success">${formatCurrency(h.actual_commission)}</strong></span>
                            </div>
                        </div>
                    </div>
                `;
            });
        }
        $('#halfYearCardsContainer').html(hHtml);

        // 4. Yearly Summary Container
        let y = overview.yearly || {};
        let yHtml = `
            <div class="card matrix-card p-4 bg-light border-0">
                <div class="row align-items-center g-4">
                    <div class="col-md-4 border-end">
                        <span class="text-muted small fw-semibold text-uppercase">Full Year Target (${y.year || new Date().getFullYear()})</span>
                        <h2 class="fw-bold text-dark mt-1 mb-2">${formatCurrency(y.target_amount)}</h2>
                        <span class="badge bg-primary-subtle text-primary border px-2 py-1">Jan 01 - Dec 31</span>
                    </div>
                    <div class="col-md-5 border-end">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Annual Sales Achieved:</span>
                            <span class="fw-bold text-dark">${formatCurrency(y.actual_sales)}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Annual Target Progress:</span>
                            <span class="fw-bold ${y.is_achieved ? 'text-success' : 'text-primary'}">${y.achievement_percent}%</span>
                        </div>
                        <div class="progress progress-thin bg-white" style="height: 12px;">
                            <div class="progress-bar ${y.is_achieved ? 'bg-success' : 'bg-primary'}" style="width: ${Math.min(100, y.achievement_percent)}%;"></div>
                        </div>
                        <div class="small text-muted mt-2">
                            ${y.is_achieved ? '<i class="fa-solid fa-circle-check text-success me-1"></i> Annual quota fulfilled!' : '<i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Shortfall: ' + formatCurrency(y.shortfall)}
                        </div>
                    </div>
                    <div class="col-md-3 text-md-center">
                        <span class="text-muted small">Total Commission Credited:</span>
                        <h3 class="fw-bold text-success mt-1 mb-1">${formatCurrency(y.actual_commission)}</h3>
                        <small class="text-muted">${y.deals_count || 0} Referral Subscription Deals</small>
                    </div>
                </div>
            </div>
        `;
        $('#yearlySummaryContainer').html(yHtml);

        // 5. Target History / Schedules Table
        let histRows = '';
        let histCount = 0;
        if(overview.targets_history && overview.targets_history.length > 0) {
            histCount = overview.targets_history.length;
            $.each(overview.targets_history, function(i, t) {
                let typeBadge = '<span class="badge bg-primary-subtle text-primary border px-2 py-1 text-uppercase">' + escapeHtml(t.period_type) + '</span>';
                histRows += `
                    <tr>
                        <td class="ps-3 py-3">${typeBadge}</td>
                        <td class="py-3 fw-bold text-dark">${formatCurrency(t.target_amount)}</td>
                        <td class="py-3 fw-semibold text-primary">${formatCurrency(t.monthly_equivalent)}/mo</td>
                        <td class="py-3 text-dark font-monospace">${t.start_date || 'N/A'} <i class="fa-solid fa-arrow-right mx-1 text-muted"></i> ${t.end_date || 'Ongoing'}</td>
                        <td class="py-3 text-muted">${escapeHtml(t.notes || 'Target rule')}</td>
                        <td class="py-3 text-end pe-3">
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteTargetSchedule(${t.id})" title="Delete Target Rule">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            histRows = '<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fa-solid fa-calendar-xmark me-2"></i> No target schedules configured yet. Click "Set New Target" to create one.</td></tr>';
        }
        $('#schedulesCountBadge').text(histCount);
        $('#targetHistoryTableBody').html(histRows);
    }

    // Copy Referral Code
    function copyCode() {
        let code = $('#refCode').text();
        if(!code || code === '...') return;
        navigator.clipboard.writeText(code).then(function() {
            $('#copyIcon').removeClass('fa-copy fa-regular').addClass('fa-check fa-solid text-success');
            showToast("Referral code copied!", "success");
            setTimeout(function() {
                $('#copyIcon').removeClass('fa-check fa-solid text-success').addClass('fa-copy fa-regular');
            }, 1800);
        });
    }

    // Copy Registration Link
    function copyReferralLink() {
        let url = $('#vd-referral-url').val();
        if(!url) return;
        navigator.clipboard.writeText(url).then(function() {
            showToast("Referral registration link copied to clipboard!", "success");
        });
    }

    // Target Setup Modal Handlers
    function openTargetModal() {
        if(currentVendorData && currentVendorData.vendor) {
            let v = currentVendorData.vendor;
            $('#modalTargetAmount').val(v.monthly_target || '');
            $('#modalTargetType').val(v.target_type || 'monthly');
        }
        handleTargetTypeChange();
        $('#checkSteppedTarget').prop('checked', false);
        toggleSteppedSection();
        
        var targetModal = new bootstrap.Modal(document.getElementById('vendorTargetModal'));
        targetModal.show();
    }

    function handleTargetTypeChange() {
        let type = $('#modalTargetType').val();
        let helpText = 'Target amount will apply monthly.';
        if(type === 'quarterly') helpText = 'Target amount will apply over 3 months (Quarterly).';
        else if(type === 'half_yearly') helpText = 'Target amount will apply over 6 months (Half-Yearly).';
        else if(type === 'yearly') helpText = 'Target amount will apply over 12 months (Yearly).';
        else if(type === 'custom') helpText = 'Custom date range quota.';
        $('#periodHelpText').text(helpText);
        updateSteppedPreview();
    }

    function toggleSteppedSection() {
        if($('#checkSteppedTarget').is(':checked')) {
            $('#steppedFieldsContainer').slideDown(200);
            updateSteppedPreview();
        } else {
            $('#steppedFieldsContainer').slideUp(200);
        }
    }

    function updateSteppedPreview() {
        let baseAmount = $('#modalTargetAmount').val() || 25000;
        let months = $('#modalStepMonths').val() || 3;
        let nextAmount = $('#modalNextTargetAmount').val() || 50000;
        $('#steppedPreviewText').text(`Target will be ${formatCurrency(baseAmount)}/mo for first ${months} months, then automatically increased to ${formatCurrency(nextAmount)}/mo thereafter.`);
    }

    $('#modalTargetAmount, #modalStepMonths, #modalNextTargetAmount').on('input', function() {
        updateSteppedPreview();
    });

    // Save Vendor Target
    function saveVendorTarget() {
        let targetAmount = $('#modalTargetAmount').val();
        let targetType = $('#modalTargetType').val();
        let startDate = $('#modalTargetStart').val();
        let endDate = $('#modalTargetEnd').val();
        let notes = $('#modalTargetNotes').val();

        let isStepped = $('#checkSteppedTarget').is(':checked');
        let stepMonths = $('#modalStepMonths').val();
        let nextAmount = $('#modalNextTargetAmount').val();

        if(!targetAmount || parseFloat(targetAmount) < 0) {
            showToast("Please enter a valid target amount.", "warning");
            return;
        }

        if(isStepped && (!nextAmount || parseFloat(nextAmount) <= 0)) {
            showToast("Please enter the increased target amount for post-step period.", "warning");
            return;
        }

        let $btn = $('#saveTargetBtn');
        let origText = $btn.html();
        $btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...').prop('disabled', true);

        let targetUrl = "{{ route('superadmin.vendors.target', ['box_slug' => $currentBoxSlug, 'id' => ':id']) }}".replace(':id', vendorId);

        $.ajax({
            url: targetUrl,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                target_amount: targetAmount,
                target_type: targetType,
                target_start_date: startDate,
                target_end_date: endDate,
                notes: notes,
                is_stepped: isStepped ? 1 : 0,
                step_months: stepMonths,
                next_target_amount: nextAmount
            },
            success: function(res) {
                $btn.html(origText).prop('disabled', false);
                if(res.success) {
                    showToast(res.message || "Vendor target updated successfully!", "success");
                    var modalEl = document.getElementById('vendorTargetModal');
                    if (modalEl) {
                        var modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) {
                            modalInstance.hide();
                        } else {
                            $('#vendorTargetModal').modal('hide');
                        }
                    }
                    loadVendorDetail();
                } else {
                    showToast(res.message || "Failed to update target.", "danger");
                }
            },
            error: function(xhr) {
                $btn.html(origText).prop('disabled', false);
                let errMsg = "Failed to update vendor target.";
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                showToast(errMsg, "danger");
            }
        });
    }

    // Delete Target Schedule
    function deleteTargetSchedule(targetId) {
        if(!confirm("Are you sure you want to delete this target schedule? Target calculations will re-adjust accordingly.")) {
            return;
        }

        let deleteUrl = "{{ route('superadmin.vendors.target.delete', ['box_slug' => $currentBoxSlug, 'id' => ':id', 'targetId' => ':targetId']) }}"
            .replace(':id', vendorId)
            .replace(':targetId', targetId);

        $.ajax({
            url: deleteUrl,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                if(res.success) {
                    showToast(res.message, "success");
                    loadVendorDetail();
                } else {
                    showToast(res.message || "Failed to remove target schedule.", "danger");
                }
            },
            error: function() {
                showToast("Failed to remove target schedule.", "danger");
            }
        });
    }

    // Commission Status Management Handlers
    let statusUpdateUrlTemplate = "{{ route('superadmin.vendors.commission.status', ['box_slug' => $currentBoxSlug, 'commissionId' => ':commissionId']) }}";

    function verifyCommission(commissionId) {
        let url = statusUpdateUrlTemplate.replace(':commissionId', commissionId);
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                action: 'verify'
            },
            success: function(res) {
                if(res.success) {
                    showToast(res.message || "Commission verified successfully!", "success");
                    loadVendorDetail();
                } else {
                    showToast(res.message || "Failed to verify commission.", "danger");
                }
            },
            error: function(xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Error verifying commission.";
                showToast(msg, "danger");
            }
        });
    }

    function openPayCommissionModal(commissionId, amount) {
        $('#payModalTxnId').val(commissionId);
        $('#payModalTxnRef').text('#TXN-' + commissionId.toString().padStart(5, '0'));
        $('#payModalAmount').text(formatCurrency(amount));
        $('#payModalReference').val('');
        $('#payModalNotes').val('');

        var modal = new bootstrap.Modal(document.getElementById('commissionPaymentModal'));
        modal.show();
    }

    function submitCommissionPayment() {
        let commissionId = $('#payModalTxnId').val();
        let reference = $('#payModalReference').val();
        let notes = $('#payModalNotes').val();

        if(!commissionId) return;

        let $btn = $('#confirmPayBtn');
        let orig = $btn.html();
        $btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...').prop('disabled', true);

        let url = statusUpdateUrlTemplate.replace(':commissionId', commissionId);
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                action: 'pay',
                reference: reference,
                notes: notes
            },
            success: function(res) {
                $btn.html(orig).prop('disabled', false);
                if(res.success) {
                    showToast(res.message || "Commission marked as paid!", "success");
                    var modalEl = document.getElementById('commissionPaymentModal');
                    if (modalEl) {
                        var modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                        else $('#commissionPaymentModal').modal('hide');
                    }
                    loadVendorDetail();
                } else {
                    showToast(res.message || "Failed to process commission payment.", "danger");
                }
            },
            error: function(xhr) {
                $btn.html(orig).prop('disabled', false);
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Error paying commission.";
                showToast(msg, "danger");
            }
        });
    }

    function markCommissionUnpaid(commissionId) {
        if(!confirm("Are you sure you want to revert this commission to Unpaid?")) return;

        let url = statusUpdateUrlTemplate.replace(':commissionId', commissionId);
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                action: 'unpaid'
            },
            success: function(res) {
                if(res.success) {
                    showToast(res.message || "Commission reverted to unpaid.", "warning");
                    loadVendorDetail();
                } else {
                    showToast(res.message || "Failed to revert commission.", "danger");
                }
            },
            error: function(xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Error updating commission.";
                showToast(msg, "danger");
            }
        });
    }

    $(document).ready(function() {
        loadVendorDetail();
    });

    // Fallback if ready fired before script executed
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(function() {
            if (!currentVendorData) {
                loadVendorDetail();
            }
        }, 50);
    }
</script>
@endsection
