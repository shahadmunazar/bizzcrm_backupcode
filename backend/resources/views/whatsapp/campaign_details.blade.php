@extends('layouts.main')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap');
    
    .page-wrapper {
        font-family: 'Outfit', sans-serif !important;
        background-color: #f8f9fa;
    }
    
    .tab-style-6 .nav-link {
        border: 1px solid #e3e6ec;
        background: #fff;
        color: #495057;
        padding: 12px 20px;
        border-radius: 12px;
        margin-right: 8px;
        transition: all 0.2s ease-in-out;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 130px;
    }
    
    .tab-style-6 .nav-link.active {
        border-color: #3563e9;
        background-color: #3563e9;
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(53, 99, 233, 0.2);
    }
    
    .tab-style-6 .nav-link:hover:not(.active) {
        background-color: #f1f3f9;
        border-color: #cbd5e1;
    }
    
    .tab-style-6 .nav-link svg {
        transition: transform 0.2s;
    }
    
    .tab-style-6 .nav-link.active svg {
        transform: scale(1.1);
        fill: currentColor;
    }
    
    .percent-badge {
        font-size: 0.75rem;
        font-weight: 600;
        opacity: 0.9;
    }
    
    .count-badge {
        font-size: 0.8rem;
    }
    
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        background: #fff;
        overflow: hidden;
    }
    
    .card-header {
        background: #fff;
        border-bottom: 1px solid #f1f3f9;
        padding: 20px 24px;
    }
    
    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0;
    }
    
    .bg-soft-primary { background: rgba(53, 99, 233, 0.08); }
    .bg-soft-success { background: rgba(16, 185, 129, 0.08); }
    .bg-soft-warning { background: rgba(245, 158, 11, 0.08); }
    .bg-soft-danger { background: rgba(239, 68, 68, 0.08); }
    .bg-soft-info { background: rgba(6, 182, 212, 0.08); }
    
    .text-primary { color: #3563e9 !important; }
    .text-success { color: #10b981 !important; }
    .text-warning { color: #f59e0b !important; }
    .text-danger { color: #ef6868 !important; }
    .text-info { color: #06b6d4 !important; }

    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.06);
    }
    
    .contact-row.selected {
        background-color: rgba(53, 99, 233, 0.04) !important;
    }
    
    .recipient-checkbox {
        cursor: pointer;
        width: 17px;
        height: 17px;
        border-radius: 4px;
        border: 1px solid #cbd5e1;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 0.9rem;
        outline: none;
    }
    
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #3563e9;
        box-shadow: 0 0 0 2px rgba(53, 99, 233, 0.15);
    }
</style>

<div class="page-wrapper cardhead">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('whatsapp.dashboard', $box->slug) }}" class="text-muted text-decoration-none">WhatsApp</a></li>
                            <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">Campaign Dashboard</li>
                        </ol>
                    </nav>
                    <h3 class="page-title d-flex align-items-center mb-0">
                        <span class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 d-inline-flex">
                            <i class="ti ti-brand-campaignmonitor text-primary"></i>
                        </span>
                        {{ $campaign->name }}
                    </h3>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <div class="dropdown me-2">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-white border-1 px-3 py-2 shadow-sm" data-bs-toggle="dropdown" style="border-radius: 10px;"><i class="ti ti-package-export me-2"></i>Export</a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);" data-url="{{ route('whatsapp.campaign.export_details', ['box_slug' => $box->slug, 'id' => $campaign->id, 'format' => 'pdf']) }}" class="dropdown-item btn-export-details"><i class="ti ti-file-type-pdf text-danger me-1"></i>Export as PDF</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" data-url="{{ route('whatsapp.campaign.export_details', ['box_slug' => $box->slug, 'id' => $campaign->id, 'format' => 'excel']) }}" class="dropdown-item btn-export-details"><i class="ti ti-file-type-xls text-green me-1"></i>Export as Excel</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        @if(in_array($campaign->status, ['PENDING', 'IMPORTED']))
                            <button id="btn-start-campaign" class="btn btn-primary px-4 py-2 shadow-sm d-flex align-items-center gap-2" style="border-radius: 10px;">
                                <i class="bx bx-play-circle"></i>
                                <span class="fw-bold">Execute Campaign</span>
                            </button>
                        @endif
                        <button class="btn btn-light border px-3 py-2 shadow-sm" onclick="window.location.reload()" style="border-radius: 10px;" title="Refresh Page">
                            <i class="ti ti-refresh-dot"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Progress Section -->
        <div id="progress-container" class="mb-4" style="{{ $campaign->status === 'SENDING' ? '' : 'display: none;' }}">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                            <i class="bx bx-loader-alt bx-spin me-2 text-primary"></i> 
                            Campaign Progress: <span id="progress-percent">0</span>%
                        </h6>
                        <div class="d-flex align-items-center gap-2">
                            <span id="eta-badge" class="badge bg-soft-info text-info px-3 py-1 rounded-pill" style="display: none;"></span>
                            <span class="badge bg-soft-primary text-primary px-3 py-1 rounded-pill" id="progress-status">SENDING...</span>
                        </div>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 5px; background-color: #f0f2f5;">
                        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Tabs -->
        <ul class="nav nav-tabs mb-4 tab-style-6" id="campaignTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link btn btn-outline-dark active flex-column" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview-pane" type="button" role="tab" aria-controls="overview-pane" aria-selected="true">
                    <span class="d-block"><span class="percent-badge" id="percent-overview">100%</span> <span>(<span id="tab-count-overview">--</span>)</span></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-bar-chart-fill me-1" viewBox="0 0 16 16">
                        <path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/>
                    </svg>Overview
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link btn btn-outline-dark flex-column" id="sent-tab" data-bs-toggle="tab" data-bs-target="#sent-pane" type="button" role="tab" aria-controls="sent-pane" aria-selected="false">
                    <span class="d-block"><span class="percent-badge" id="percent-sent">--%</span> <span>(<span id="tab-count-sent">--</span>)</span></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-check2 me-1" viewBox="0 0 16 16">
                        <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                    </svg> Sent
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link btn btn-outline-dark flex-column" id="delivered-tab" data-bs-toggle="tab" data-bs-target="#delivered-pane" type="button" role="tab" aria-controls="delivered-pane" aria-selected="false">
                    <span class="d-block"><span class="percent-badge" id="percent-delivered">--%</span> <span>(<span id="tab-count-delivered">--</span>)</span></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-check2-all me-1" viewBox="0 0 16 16">
                        <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0"/>
                        <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708"/>
                    </svg> Delivered
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link btn btn-outline-dark flex-column" id="read-tab" data-bs-toggle="tab" data-bs-target="#read-pane" type="button" role="tab" aria-controls="read-pane" aria-selected="false">
                    <span class="d-block"><span class="percent-badge" id="percent-read">--%</span> <span>(<span id="tab-count-read">--</span>)</span></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-check2-all text-success me-1" viewBox="0 0 16 16">
                        <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0"/>
                        <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708"/>
                    </svg> Read
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link btn btn-outline-dark flex-column" id="clicked-tab" data-bs-toggle="tab" data-bs-target="#clicked-pane" type="button" role="tab" aria-controls="clicked-pane" aria-selected="false">
                    <span class="d-block"><span class="percent-badge" id="percent-clicked">0%</span> <span>(0)</span></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ca76ff" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mouse-pointer"><path d="M3 3l7.07 16.97 2.51-7.39 7.39-2.51L3 3z"></path><path d="M13 13l6 6"></path></svg>
                    Clicked
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link btn btn-outline-dark flex-column" id="replied-tab" data-bs-toggle="tab" data-bs-target="#replied-pane" type="button" role="tab" aria-controls="replied-pane" aria-selected="false">
                    <span class="d-block"><span class="percent-badge" id="percent-replied">--%</span> <span>(<span id="tab-count-replied">--</span>)</span></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-reply-fill text-info me-1" viewBox="0 0 16 16">
                        <path d="M5.921 11.9 1.353 8.62a.72.72 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                    </svg> Replied
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link btn btn-outline-dark flex-column" id="failed-tab" data-bs-toggle="tab" data-bs-target="#failed-pane" type="button" role="tab" aria-controls="failed-pane" aria-selected="false">
                    <span class="d-block"><span class="percent-badge" id="percent-failed">--%</span> <span>(<span id="tab-count-failed">--</span>)</span></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-chat-left-dots-fill text-danger me-1" viewBox="0 0 16 16">
                        <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793zm5 4a1 1 0 1 0-2 0 1 1 0 0 0 2 0m4 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0m3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                    </svg>Failed
                </button>
            </li>
        </ul>

        <div class="tab-content" id="campaignTabContent">
            <!-- OVERVIEW PANE -->
            <div class="tab-pane fade show active p-0 border-0" id="overview-pane" role="tabpanel" aria-labelledby="overview-tab">
                <div class="row">
                    <!-- Sidebar info columns -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body p-4">
                                <h6 class="mb-4 fw-bold fs-16 text-dark d-flex align-items-center">
                                    <i class="ti ti-info-circle fs-25 me-2 text-primary"></i>Campaign Information
                                </h6>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-3">
                                        <small class="text-muted fs-15 d-block mb-1">Campaign Type</small>
                                        <span class="fs-16 text-dark">Broadcast Campaign</span>
                                    </li>
                                    <li class="mb-3">
                                        <small class="text-muted fs-15 d-block mb-1">Message Type</small>
                                        <span class="fs-16 text-dark">Template Message (Text & Variables)</span>
                                    </li>
                                    <li class="mb-3">
                                        <small class="text-muted fs-15 d-block mb-1">Template Name</small>
                                        <span class="fs-16 text-primary">{{ $campaign->template->name ?? 'N/A' }}</span>
                                    </li>
                                    <li class="mb-3">
                                        <small class="text-muted fs-15 d-block mb-1">Created At</small>
                                        <span class="fs-16 text-dark">{{ $campaign->created_at ? $campaign->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                                    </li>
                                    <li class="mb-3">
                                        <small class="text-muted fs-15 d-block mb-1">Completed At</small>
                                        <span class="fs-16 text-dark">{{ $campaign->completed_at ? $campaign->completed_at->format('d M Y, h:i A') : 'Running / Pending' }}</span>
                                    </li>
                                    @if($campaign->completed_at)
                                    <li class="mb-0">
                                        <small class="text-muted fs-15 d-block mb-1">Campaign Duration</small>
                                        <span class="fs-16 text-success">Completed in {{ $campaign->completed_at->diffInMinutes($campaign->created_at) }} min</span>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body p-4">
                                <h6 class="mb-4 fw-bold text-dark d-flex align-items-center">
                                    <i class="ti ti-file-text fs-25 me-2 text-primary"></i>Import Details
                                </h6>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-3">
                                        <small class="text-muted fs-15 d-block mb-1">Source File</small>
                                        <span class="fs-16 text-dark">{{ $campaign->metadata['fileName'] ?? 'Manual Upload' }}</span>
                                    </li>
                                    <li class="mb-3">
                                        <small class="text-muted fs-15 d-block mb-1">Processed Contacts</small>
                                        <span class="fs-16 text-dark" id="display-processed-rows">{{ $campaign->processed_rows ?? 0 }}</span>
                                    </li>
                                    <li class="mb-0">
                                        <small class="text-muted fs-15 d-block mb-1">Dynamic Fields mapped</small>
                                        <div class="d-flex flex-wrap gap-1 mt-2">
                                            <span class="badge bg-soft-primary fs-14 fw-normal text-primary">Name</span>
                                            <span class="badge bg-soft-primary fs-14 fw-normal text-primary">Phone</span>
                                            @foreach($vars as $v)
                                                <span class="badge bg-soft-info fs-14 fw-normal text-info">Var {{ $v }}</span>
                                            @endforeach
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Right hand metrics boxes -->
                    <div class="col-lg-8">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="card stat-card mb-0 bg-white">
                                    <div class="card-body p-4">
                                        <span class="avatar rounded-circle bg-soft-primary text-primary mb-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                            <i class="ti fs-24 ti-users"></i>
                                        </span>
                                        <h6 class="text-default fw-semibold mb-1" style="font-size: 0.85rem;">Audience</h6>
                                        <h3 class="mb-0 fw-bold text-dark" id="stat-total">--</h3>
                                        <small class="text-default mt-2 d-block"><i class="bx bx-group me-1"></i> Imported List</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card stat-card mb-0 bg-white">
                                    <div class="card-body p-4">
                                        <span class="avatar rounded-circle bg-soft-success text-success mb-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                            <i class="ti fs-24 ti-circle-check"></i>
                                        </span>
                                        <h6 class="text-default fw-semibold mb-1" style="font-size: 0.85rem;">Delivered</h6>
                                        <h3 class="mb-0 fw-bold text-dark" id="stat-delivered">--</h3>
                                        <small class="text-success mt-2 d-block"><i class="bx bx-check-circle me-1"></i> Sent successfully</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card stat-card mb-0 bg-white">
                                    <div class="card-body p-4">
                                        <span class="avatar rounded-circle bg-soft-info text-info mb-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                            <i class="ti fs-24 ti-wallet"></i>
                                        </span>
                                        <h6 class="text-default fw-semibold mb-1" style="font-size: 0.85rem;">Credits Used</h6>
                                        <h3 class="mb-0 fw-bold text-dark" id="stat-credits">₹ 0.00</h3>
                                        <small class="text-default mt-2 d-block"><i class="bx bx-shield me-1"></i> Est. usage rate</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card stat-card mb-0">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <small class="text-default fs-15 fw-semibold">Sent</small>
                                            <span class="badge bg-soft-primary text-primary px-2 rounded-pill" id="badge-sent-pct">0%</span>
                                        </div>
                                        <h4 class="mb-0 fw-bold text-dark" id="stat-sent">--</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card stat-card mb-0">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <small class="text-default fs-15 fw-semibold">Read</small>
                                            <span class="badge bg-soft-success text-success px-2 rounded-pill" id="badge-read-pct">0%</span>
                                        </div>
                                        <h4 class="mb-0 fw-bold text-dark" id="stat-read">--</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card stat-card mb-0">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <small class="text-default fs-15 fw-semibold">Replied</small>
                                            <span class="badge bg-soft-info text-info px-2 rounded-pill" id="badge-replied-pct">0%</span>
                                        </div>
                                        <h4 class="mb-0 fw-bold text-dark" id="stat-replied">--</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card stat-card mb-0">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <small class="text-muted fw-semibold">Failed</small>
                                            <span class="badge bg-soft-danger text-danger px-2 rounded-pill" id="badge-failed-pct">0%</span>
                                        </div>
                                        <h4 class="mb-0 fw-bold text-dark" id="stat-failed">--</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Template text preview widget -->
                        <div class="card">
                            <div class="card-body p-4">
                                <h6 class="mb-3 fw-bold fs-16 text-dark d-flex align-items-center">
                                    <i class="ti ti-brand-whatsapp fs-25 me-2 text-success"></i>WhatsApp Message Template Preview
                                </h6>
                                <div class="p-3 bg-light rounded-3 border border-dashed text-dark" style="font-family: sans-serif; white-space: pre-line;">
                                    <div id="template-text-box">Loading template text preview...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABS FOR SUB-STATUS TABLES -->
            @php 
                $tabs = [
                    'sent' => 'Sent Contacts',
                    'delivered' => 'Delivered Contacts',
                    'read' => 'Read Contacts',
                    'clicked' => 'Clicked Contacts',
                    'replied' => 'Replied Contacts',
                    'failed' => 'Failed Contacts'
                ];
            @endphp

            @foreach($tabs as $tabKey => $tabTitle)
            <div class="tab-pane fade p-0 border-0" id="{{ $tabKey }}-pane" role="tabpanel" aria-labelledby="{{ $tabKey }}-tab">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title"><i class="ti ti-list me-2 text-primary"></i>{{ $tabTitle }}</h5>
                            <p class="text-default mb-0 mt-1">List of contacts with status: <strong class="text-primary">{{ ucfirst($tabKey) }}</strong></p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div id="bulk-actions-{{ $tabKey }}" style="display: none;">
                                <button class="btn btn-primary btn-sm px-3 rounded-pill shadow-sm btn-bulk-convert">
                                    Convert <span class="selected-count-badge">0</span> Selected
                                </button>
                            </div>
                            <button class="btn btn-danger px-3 btn-convert-entire-campaign">
                                <i class="bx bx-group me-1"></i> Convert All to Leads
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive p-3">
                            <table class="table table-hover align-middle mb-0" id="table-{{ $tabKey }}" style="width: 100%;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 40px;"><input type="checkbox" class="select-all-recipients" style="width: 17px; height: 17px;"></th>
                                        <th>Recipient</th>
                                        @foreach($vars as $v)
                                            <th>Var {{ $v }}</th>
                                        @endforeach
                                        <th class="text-center">Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- AJAX Loaded Server-side Yajra DataTable rows -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="modal-confirm-execute" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-xl bg-soft-primary rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bx bx-play fs-1 text-primary"></i>
                </div>
                <h4 class="fw-bold mb-2">Execute Campaign?</h4>
                <p class="text-muted mb-0">This will start sending messages in background batches according to your daily limits.</p>
                
                <div class="d-flex align-items-center justify-content-center gap-3 mt-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="btn-confirm-execute-submit" class="btn btn-primary px-4 shadow-sm">
                        <span class="fw-bold">Yes, Execute Now</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-confirm-convert" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-xl bg-soft-primary rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bx bx-user-plus fs-1 text-primary"></i>
                </div>
                <h4 class="fw-bold mb-2">Convert to Leads?</h4>
                <p class="text-muted mb-0" id="convert-modal-text">Are you sure you want to convert the selected recipients to CRM leads?</p>
                
                <div class="d-flex align-items-center justify-content-center gap-3 mt-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="btn-confirm-convert-submit" class="btn btn-primary px-4 shadow-sm">
                        <span class="fw-bold">Yes, Convert Now</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let selectedIds = new Set();
    let pollInterval = null;
    let tableInstances = {};

    const showToast = (msg, type = 'success') => {
        const colors = { success: '#10b981', danger: '#ef6868', warning: '#f59e0b', info: '#3563e9' };
        Swal.fire({ 
            text: msg, 
            toast: true, 
            position: 'top-end', 
            showConfirmButton: false, 
            timer: 3000, 
            background: colors[type] || colors.success, 
            color: '#fff' 
        });
    };

    // Load template variables text preview helper
    const loadTemplatePreview = () => {
        $.get("{{ route('whatsapp.template.variables', ['box_slug' => $box->slug, 'id' => $campaign->whats_app_template_id]) }}", function(res) {
            if (res.success && res.preview_html) {
                $('#template-text-box').html(res.preview_html);
            } else {
                $('#template-text-box').text('Unable to load template preview.');
            }
        }).fail(function() {
            $('#template-text-box').text('Unable to fetch template details.');
        });
    };
    loadTemplatePreview();

    // Reusable DataTable Initialization function
    const initTabTable = (tabKey) => {
        if (tableInstances[tabKey]) return; // already initialized

        const tableId = `#table-${tabKey}`;
        const table = $(tableId).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('whatsapp.campaign.data', ['box_slug' => $box->slug, 'id' => $campaign->id]) }}",
                data: function(d) {
                    d.status = tabKey;
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                { data: 'recipient', name: 'name' },
                @foreach($vars as $v)
                { data: 'var_{{ $v }}', name: 'var_{{ $v }}', orderable: false, searchable: false },
                @endforeach
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
            ],
            pageLength: 10,
            language: { 
                search: "", 
                searchPlaceholder: "Search contacts...",
                processing: '<div class="spinner-border text-primary spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>'
            },
            drawCallback: function(settings) {
                const json = settings.json;
                if (json) {
                    updateMetrics(json);
                }
                
                // Re-apply checkboxes state
                $(`${tableId} .recipient-checkbox`).each(function() {
                    if (selectedIds.has($(this).val())) {
                        $(this).prop('checked', true);
                        $(this).closest('tr').addClass('selected');
                    }
                });
                
                updateBulkUI(tabKey);
            },
            createdRow: function(row, data) {
                $(row).addClass('contact-row');
                if (data.lead_id) $(row).css({'opacity': '0.75', 'cursor': 'default'});
            }
        });

        tableInstances[tabKey] = table;
    };

    // Helper to calculate percentages and update metrics globally on screen
    const updateMetrics = (json) => {
        const total = parseInt(json.total_count) || 0;
        const sent = parseInt(json.sent_count) || 0;
        const pending = parseInt(json.pending_count) || 0;
        const pendingReal = parseInt(json.pending_count_real) || pending;
        const failed = parseInt(json.failed_count) || 0;
        const delivered = parseInt(json.delivered_count) || 0;
        const read = parseInt(json.read_count) || 0;
        const replied = parseInt(json.replied_count) || 0;

        // Est credits
        const estCredits = (sent * 0.50).toFixed(2);

        // Update UI Badges for Limits and ETA
        if (json.is_outside_window) {
            $('#progress-status').removeClass('bg-soft-primary text-primary bg-soft-danger text-danger').addClass('bg-soft-warning text-warning').html('<i class="bx bx-time"></i> PAUSED: WAITING FOR 9 AM');
            $('.bx-loader-alt').removeClass('bx-spin text-primary text-danger').addClass('text-warning');
        } else if (json.is_limit_reached) {
            $('#progress-status').removeClass('bg-soft-primary text-primary bg-soft-warning text-warning').addClass('bg-soft-danger text-danger').html('<i class="bx bx-stop-circle"></i> PAUSED: LIMIT REACHED (Resumes Tomorrow)');
            $('.bx-loader-alt').removeClass('bx-spin text-primary text-warning').addClass('text-danger');
        } else {
            $('#progress-status').removeClass('bg-soft-warning text-warning bg-soft-danger text-danger').addClass('bg-soft-primary text-primary').text('SENDING...');
            $('.bx-loader-alt').addClass('bx-spin text-primary').removeClass('text-warning text-danger');
        }

        if (json.eta_string && pendingReal > 0) {
            $('#eta-badge').text('ETA: ' + json.eta_string).show();
        } else {
            $('#eta-badge').hide();
        }

        // Update counts
        $('#stat-total').text(total);
        $('#stat-sent').text(sent);
        $('#stat-pending').text(pending);
        $('#stat-failed').text(failed);
        $('#stat-delivered').text(delivered);
        $('#stat-read').text(read);
        $('#stat-replied').text(replied);
        $('#stat-credits').text(`₹ ${estCredits}`);
        
        $('#display-processed-rows').text(sent);

        // Update Tab Badges counts
        $('#tab-count-overview').text(total);
        $('#tab-count-sent').text(sent);
        $('#tab-count-delivered').text(delivered);
        $('#tab-count-read').text(read);
        $('#tab-count-replied').text(replied);
        $('#tab-count-failed').text(failed);

        // Calculate Percentages
        const getPct = (val) => total > 0 ? Math.round((val / total) * 100) : 0;
        
        $('#badge-sent-pct').text(`${getPct(sent)}%`);
        $('#badge-read-pct').text(`${getPct(read)}%`);
        $('#badge-replied-pct').text(`${getPct(replied)}%`);
        $('#badge-failed-pct').text(`${getPct(failed)}%`);

        $('#percent-sent').text(`${getPct(sent)}%`);
        $('#percent-delivered').text(`${getPct(delivered)}%`);
        $('#percent-read').text(`${getPct(read)}%`);
        $('#percent-replied').text(`${getPct(replied)}%`);
        $('#percent-failed').text(`${getPct(failed)}%`);
    };

    // Trigger DataTable loading when tab changes
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const targetId = $(e.target).attr('id');
        const tabKey = targetId.replace('-tab', '');
        
        if (tabKey !== 'overview') {
            initTabTable(tabKey);
        }
    });

    // Initialize the Sent tab table by default so there's active table metadata
    initTabTable('sent');

    // Polling progress function
    const pollProgress = () => {
        $.get("{{ route('whatsapp.campaign.progress', ['box_slug' => $box->slug, 'id' => $campaign->id]) }}", function(res) {
            if (res.success) {
                $('#progress-container').show();
                $('#progress-percent').text(res.percent);
                $('#progress-bar').css('width', res.percent + '%');
                
                if (res.eta_human) {
                    $('#eta-badge').text(res.eta_human).show();
                }

                if (res.is_completed || res.status === 'COMPLETED') {
                    clearInterval(pollInterval);
                    $('#progress-status').text('COMPLETED').removeClass('text-primary bg-soft-primary bg-soft-danger text-danger').addClass('text-success bg-soft-success');
                    $('#badge-status').text('Completed').removeClass('bg-soft-warning text-warning bg-soft-danger text-danger').addClass('bg-soft-success text-success');
                    $('#eta-badge').hide();
                    
                    // Reload all active tables
                    Object.values(tableInstances).forEach(tbl => tbl.ajax.reload(null, false));
                } else if (res.is_limit_reached) {
                    $('#progress-status').removeClass('bg-soft-primary text-primary bg-soft-warning text-warning').addClass('bg-soft-danger text-danger').html('<i class="bx bx-stop-circle"></i> PAUSED: LIMIT REACHED (Resumes Tomorrow)');
                    $('.bx-loader-alt').removeClass('bx-spin text-primary text-warning').addClass('text-danger');
                    $('#progress-bar').removeClass('bg-primary progress-bar-animated').addClass('bg-danger');
                } else {
                    $('#progress-status').removeClass('bg-soft-warning text-warning bg-soft-danger text-danger').addClass('bg-soft-primary text-primary').text('SENDING...');
                    $('.bx-loader-alt').addClass('bx-spin text-primary').removeClass('text-warning text-danger');
                    $('#progress-bar').removeClass('bg-danger bg-warning').addClass('bg-primary progress-bar-animated');
                }
            }
        });
    };

    @if($campaign->status === 'SENDING')
        pollInterval = setInterval(pollProgress, 3000);
    @endif

    // Bulk selection and action UI helpers
    const updateBulkUI = (tabKey) => {
        const count = selectedIds.size;
        $(`#bulk-actions-${tabKey} .selected-count-badge`).text(count);
        if (count > 0) {
            $(`#bulk-actions-${tabKey}`).fadeIn(200);
        } else {
            $(`#bulk-actions-${tabKey}`).fadeOut(200);
        }
    };

    // Global Checkbox click handlers across all tables
    $(document).on('change', '.recipient-checkbox', function() {
        const id = $(this).val();
        const isChecked = $(this).prop('checked');
        const tabKey = $(this).closest('.tab-pane').attr('id').replace('-pane', '');
        
        if (isChecked) {
            selectedIds.add(id);
            $(this).closest('tr').addClass('selected');
        } else {
            selectedIds.delete(id);
            $(this).closest('tr').removeClass('selected');
            $(`#pane-${tabKey} .select-all-recipients`).prop('checked', false);
        }
        
        // Sync master checkbox
        const totalVisible = $(`.tab-pane.active .recipient-checkbox`).length;
        const totalChecked = $(`.tab-pane.active .recipient-checkbox:checked`).length;
        if (totalVisible === totalChecked && totalVisible > 0) {
            $(`.tab-pane.active .select-all-recipients`).prop('checked', true);
        }
        
        updateBulkUI(tabKey);
    });

    $(document).on('change', '.select-all-recipients', function() {
        const checked = $(this).prop('checked');
        const tabKey = $(this).closest('.tab-pane').attr('id').replace('-pane', '');
        
        $(`#${tabKey}-pane .recipient-checkbox`).each(function() {
            const id = $(this).val();
            $(this).prop('checked', checked);
            if (checked) {
                selectedIds.add(id);
                $(this).closest('tr').addClass('selected');
            } else {
                selectedIds.delete(id);
                $(this).closest('tr').removeClass('selected');
            }
        });
        updateBulkUI(tabKey);
    });

    // Execute Campaign submission
    $('#btn-start-campaign').on('click', function() {
        $('#modal-confirm-execute').modal('show');
    });

    $('#btn-confirm-execute-submit').on('click', function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Starting...');

        $.post("{{ route('whatsapp.campaign.start', ['box_slug' => $box->slug, 'id' => $campaign->id]) }}", { 
            _token: '{{ csrf_token() }}' 
        }, function(res) {
            $('#modal-confirm-execute').modal('hide');
            btn.prop('disabled', false).html('<span class="fw-bold">Yes, Execute Now</span>');

            if (res.success) {
                $('#btn-start-campaign').fadeOut(); 
                $('#progress-container').fadeIn();
                $('#badge-status').text('Sending').removeClass('bg-soft-primary text-primary').addClass('bg-soft-warning text-warning');
                
                if (pollInterval) clearInterval(pollInterval);
                pollInterval = setInterval(pollProgress, 3000); 
                showToast(res.message);
            } else {
                showToast(res.message, 'danger');
            }
        });
    });

    // Conversion to Leads handlers
    let conversionData = { allMode: false, ids: [] };

    $(document).on('click', '.btn-convert-entire-campaign', function() {
        conversionData = { allMode: true, ids: [] };
        $('#convert-modal-text').text("Are you sure you want to convert ALL recipients in this campaign to leads?");
        $('#modal-confirm-convert').modal('show');
    });

    $(document).on('click', '.btn-bulk-convert', function() {
        const ids = Array.from(selectedIds);
        conversionData = { allMode: false, ids: ids };
        $('#convert-modal-text').text(`Are you sure you want to convert the ${ids.length} selected contacts to leads?`);
        $('#modal-confirm-convert').modal('show');
    });

    $('#btn-confirm-convert-submit').on('click', function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Converting...');
        
        $.post("{{ route('whatsapp.campaign.bulk-convert', ['box_slug' => $box->slug]) }}", {
            _token: '{{ csrf_token() }}',
            all_mode: conversionData.allMode,
            ids: conversionData.ids,
            campaign_id: '{{ $campaign->id }}'
        }, function(res) {
            $('#modal-confirm-convert').modal('hide');
            btn.prop('disabled', false).html('<span class="fw-bold">Yes, Convert Now</span>');
            
            if (res.success) {
                showToast(res.message);
                selectedIds.clear();
                
                // Reload active tab datatable
                const activeTab = $('.tab-pane.active').attr('id').replace('-pane', '');
                if (tableInstances[activeTab]) {
                    tableInstances[activeTab].ajax.reload(null, false);
                }
            } else {
                showToast(res.message, 'danger');
            }
        }).fail(function(xhr) {
            $('#modal-confirm-convert').modal('hide');
            btn.prop('disabled', false).html('<span class="fw-bold">Yes, Convert Now</span>');
            const err = xhr.responseJSON ? xhr.responseJSON.message : "Conversion failed.";
            showToast(err, 'danger');
        });
    });

    $(document).on('click', '.btn-convert-lead', function() {
        const id = $(this).data('id');
        let convUrl = "{{ route('whatsapp.campaign.convert-lead', ['box_slug' => $box->slug, 'id' => ':id']) }}".replace(':id', id);
        
        $.post(convUrl, { _token: '{{ csrf_token() }}', _method: 'POST' }, (res) => {
            if (res.success) { 
                showToast(res.message);
                
                // Reload active tab datatable
                const activeTab = $('.tab-pane.active').attr('id').replace('-pane', '');
                if (tableInstances[activeTab]) {
                    tableInstances[activeTab].ajax.reload(null, false);
                }
            } else { 
                showToast(res.message, 'danger'); 
            }
        });
    });
    // Handle export details button click
    $('.btn-export-details').on('click', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        const btn = $(this);
        const originalHtml = btn.html();
        
        btn.prop('disabled', true).html('<i class="ti ti-loader ti-spin me-1"></i> Exporting...');
        
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                btn.prop('disabled', false).html(originalHtml);
                if(response.success) {
                    showToast(response.message, 'success');
                } else {
                    showToast(response.message || 'Export failed.', 'danger');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalHtml);
                showToast(xhr.responseJSON?.message || "Failed to start export.", 'danger');
            }
        });
    });
});
</script>
@endpush