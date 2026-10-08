@extends('layouts.main')

@section('content')
<style>
        /* Profile Custom Styles */
        .profile-cover {
            height: 160px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 12px 12px 0 0;
        }
        .profile-avatar-wrapper {
            margin-top: -60px;
            padding-left: 30px;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border: 4px solid #fff;
            border-radius: 16px;
            background: #fff;
            object-fit: contain;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        
        .referral-box {
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 2rem 1.5rem;
            margin-top: 1.5rem;
        }
        .ref-title {
            color: #8b94a6;
            font-weight: 700;
            letter-spacing: 0.05em;
            font-size: 0.9rem;
            margin-bottom: 1.25rem;
        }
        .ref-code-badge {
            background-color: #ef4444; /* Red badge like the screenshot */
            color: white;
            font-family: 'Monaco', 'Consolas', monospace;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 2px;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(239, 68, 68, 0.2);
        }
        .action-btn {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            color: #475569;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .action-btn i {
            font-size: 1.25rem !important;
        }
        .action-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.06);
        }

        .stat-card { border: none; border-radius: 12px; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
</style>

<div class="page-wrapper">
    <div class="content">
        
        <div class="container-fluid p-0">
            
            <div class="mb-3">
                <a href="{{ route('superadmin.vendors.index', ['box_slug' => request()->route('box_slug')]) }}" class="btn btn-sm btn-light border shadow-sm text-secondary fw-medium">
                    <i class="fa-solid fa-arrow-left me-2"></i>Back to Vendors
                </a>
            </div>

            <!-- Profile Header -->
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="profile-cover"></div>
                <div class="profile-avatar-wrapper d-flex align-items-end justify-content-between pe-4 pb-4">
                    <div class="d-flex align-items-end gap-3">
                        <div class="bg-white rounded-3 p-1">
                            <div id="vd-avatar" class="profile-avatar bg-primary-subtle d-flex align-items-center justify-content-center text-primary fs-1 fw-bold">
                                --
                            </div>
                        </div>
                        <div class="mb-2">
                            <h3 id="vd-company-name-heading" class="fw-bold m-0">Loading...</h3>
                            <p class="text-muted m-0">
                                <i class="fa-solid fa-location-dot me-1"></i> 
                                <span id="vd-location">Loading...</span>
                            </p>
                        </div>
                    </div>
                    <div id="vd-actions">
                        <!-- Actions will be loaded via AJAX -->
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Left: Account Details -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm p-4 mb-4">
                        <h5 class="fw-bold mb-4">Business Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small text-muted mb-1">Company Name</label>
                                <p id="vd-company-name" class="fw-semibold">Loading...</p>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted mb-1">Contact Email</label>
                                <p id="vd-email" class="fw-semibold">Loading...</p>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted mb-1">Phone Number</label>
                                <p id="vd-phone" class="fw-semibold">Loading...</p>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted mb-1">Business Category</label>
                                <p id="vd-industry" class="fw-semibold">Loading...</p>
                            </div>
                            <div class="col-12 border-top pt-3">
                                <label class="small text-muted mb-1">Office Address</label>
                                <p id="vd-address" class="fw-semibold">Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Referral Section -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm p-4 text-center">
                        <div class="icon-box bg-primary-subtle text-primary mx-auto mb-3" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-gift fs-4"></i>
                        </div>
                        <h5 class="fw-bold">Refer a Vendor</h5>
                        <p class="text-muted small">Invite other businesses to join MarketCube and earn 5% commission on their first month sales.</p>
                        
                        <div class="referral-box">
                            <span class="ref-title d-block text-uppercase text-center">Your Referral Code</span>
                            <div class="d-flex flex-column align-items-center justify-content-center gap-3">
                                <span id="refCode" class="ref-code-badge text-center">...</span>
                                <div class="d-flex gap-2">
                                    <button onclick="copyCode()" class="btn action-btn" title="Copy Code">
                                        <i id="copyIcon" class="fa-regular fa-copy"></i>
                                    </button>
                                    <button onclick="shareCode()" class="btn action-btn" title="Share Code">
                                        <i class="fa-solid fa-share-nodes"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-3 border-top text-start">
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Successful Referrals:</span>
                                <span class="fw-bold">0</span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Total Rewards Earned:</span>
                                <span class="fw-bold text-success">₹0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    
    </div>
</div>
@endsection


@section('scripts')
<script>
    $(document).ready(function() {
        
        let url = window.location.pathname;
        let id = url.substring(url.lastIndexOf('/') + 1);
        
        $.ajax({
            url: '{{ route('superadmin.vendors.api.detail', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}'.replace(':id', id),
            method: 'GET',
            success: function(data) {
                if(data.vendor) {
                    $('#vd-company-name-heading').text(data.vendor.company_name || 'N/A');
                    $('#vd-company-name').text(data.vendor.company_name || 'N/A');
                    $('#vd-email').text(data.vendor.user ? data.vendor.user.email : 'N/A');
                    $('#vd-phone').text(data.vendor.mobile_number || 'N/A');
                    $('#vd-industry').text(data.vendor.industry ? data.vendor.industry.name : 'N/A');
                    $('#vd-address').text(data.vendor.address || 'N/A');
                    $('#vd-location').text(data.vendor.city ? data.vendor.city.name : 'N/A');
                    $('#refCode').text(data.vendor.referral_code || 'N/A');
                    
                    if(data.vendor.company_name) {
                        $('#vd-avatar').text(data.vendor.company_name.substring(0,2).toUpperCase());
                    }
                }
                
                let rows = '';
                if(data.transactions && data.transactions.length > 0) {
                    $.each(data.transactions, function(index, txn) {
                        let dateObj = new Date(txn.created_at);
                        let dateStr = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        let statusBadge = txn.status == 'paid' 
                            ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">Paid</span>'
                            : '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 small">Pending</span>';
                            
                        rows += '<tr>';
                        rows += '<td class="py-3 ps-3"><h6 class="mb-0 fw-semibold text-dark">#TXN-' + txn.id.toString().padStart(5, '0') + '</h6></td>';
                        rows += '<td class="py-3"><span class="text-dark fw-medium">₹' + parseFloat(txn.transaction_amount).toFixed(2) + '</span></td>';
                        rows += '<td class="py-3"><span class="text-success fw-bold">₹' + parseFloat(txn.commission_amount).toFixed(2) + ' (' + txn.commission_percentage + '%)</span></td>';
                        rows += '<td class="py-3"><span class="text-muted small">' + dateStr + '</span></td>';
                        rows += '<td class="py-3">' + statusBadge + '</td>';
                        rows += '</tr>';
                    });
                } else {
                    rows = '<tr><td colspan="5" class="text-center py-4">No transactions found.</td></tr>';
                }
                $('#transactionTableBody').html(rows);
            },
            error: function() {
                $('#transactionTableBody').html('<tr><td colspan="5" class="text-center py-4 text-danger">Error loading details.</td></tr>');
            }
        });
    });
</script>
@endsection

