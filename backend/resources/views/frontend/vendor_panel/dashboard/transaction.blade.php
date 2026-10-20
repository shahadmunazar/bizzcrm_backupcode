@extends('layouts.vendor')

@section('content')
<style>
        .stat-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s;
        }
        .icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @media (max-width: 768px) {
            
            
        }
    
/* Clean up variables that might conflict or are unnecessary if layout.main handles them */
.stat-card { border: none; border-radius: 12px; transition: transform 0.2s; }
.stat-card:hover { transform: translateY(-5px); }
</style>

<div class="page-wrapper">
    <div class="content">
        
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold m-0">My Store Settlements</h3>
                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1 small">Vendor Ledger View</span>
                </div>
                <p class="text-muted small m-0">Monitor clear payouts and pending balance deposits for your company.</p>
            </div>
            <button class="btn btn-white border shadow-sm rounded-3">
                <i class="fa-regular fa-calendar me-2"></i> June 2026
            </button>
        </header>

        <!-- Vendor KPI Metric Grid -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card stat-card shadow-sm p-4 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-primary-subtle text-primary">
                            <i class="fa-solid fa-wallet fs-5"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small fw-bold mb-1">Your Gross Sales</h6>
                            <h4 class="fw-bold m-0"><span id="grossSales">Loading...</span></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card shadow-sm p-4 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-success-subtle text-success">
                            <i class="fa-solid fa-arrow-up-from-bracket fs-5"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small fw-bold mb-1">Net Cleared Payouts</h6>
                            <h4 class="fw-bold m-0"><span id="netCleared">Loading...</span></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card shadow-sm p-4 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-warning-subtle text-warning">
                            <i class="fa-solid fa-clock fs-5"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small fw-bold mb-1">Escrow Holding Balance</h6>
                            <h4 class="fw-bold m-0"><span id="pendingBalance">Loading...</span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Isolated Transaction Data Table Block -->
        <div class="card stat-card shadow-sm p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0">Your Store Settlements Log</h5>
                <button class="btn btn-sm btn-light border fw-medium text-secondary px-3 py-1.5">
                    <i class="fa-solid fa-download me-2"></i>Export My CSV
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="py-3 border-0 ps-3">Reference ID</th>
                            <th class="py-3 border-0">Settlement Type</th>
                            <th class="py-3 border-0">Timestamp Date</th>
                            <th class="py-3 border-0">Amount</th>
                            <th class="py-3 border-0">Status</th>
                        </tr>
                    </thead>
                    <tbody id="transactionTableBody"><tr><td colspan="6" class="text-center py-4">Loading transactions...</td></tr></tbody>
                </table>
            </div>
        </div>
    
    </div>
</div>
@endsection


@section('scripts')
<script>
    $(document).ready(function() {
        $.ajax({
            url: '{{ route('vendor.api.transaction') }}',
            method: 'GET',
            success: function(data) {
                $('#grossSales').text('₹' + parseFloat(data.grossSales || 0).toFixed(2));
                $('#netCleared').text('₹' + parseFloat(data.netCleared || 0).toFixed(2));
                $('#pendingBalance').text('₹' + parseFloat(data.pendingBalance || 0).toFixed(2));
                
                let rows = '';
                if(data.transactions && data.transactions.length > 0) {
                    $.each(data.transactions, function(index, txn) {
                        let statusBadge = '';
                        if (txn.status === 'paid') {
                            statusBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small"><i class="fa-solid fa-check-double me-1"></i> Paid</span>';
                        } else if (txn.status === 'verified') {
                            statusBadge = '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 small"><i class="fa-solid fa-check-circle me-1"></i> Verified by Admin</span>';
                        } else {
                            statusBadge = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 small"><i class="fa-solid fa-clock me-1"></i> Unpaid (Pending Review)</span>';
                        }
                            
                        rows += '<tr>';
                        rows += '<td class="py-3 ps-3"><h6 class="mb-0 fw-semibold text-dark" style="font-size: 0.9rem;">#TXN-' + txn.id.toString().padStart(5, '0') + '</h6></td>';
                        rows += '<td class="py-3"><span class="text-muted small">' + (txn.box ? txn.box.name : 'N/A') + '</span></td>';
                        rows += '<td class="py-3"><span class="text-dark fw-medium">₹' + parseFloat(txn.transaction_amount).toFixed(2) + '</span></td>';
                        rows += '<td class="py-3"><span class="text-success fw-bold">₹' + parseFloat(txn.commission_amount).toFixed(2) + ' (' + txn.commission_percentage + '%)</span></td>';
                        rows += '<td class="py-3"><span class="text-muted small">' + dateStr + '</span></td>';
                        rows += '<td class="py-3">' + statusBadge + '</td>';
                        rows += '</tr>';
                    });
                } else {
                    rows = '<tr><td colspan="6" class="text-center py-4">No transactions found.</td></tr>';
                }
                $('#transactionTableBody').html(rows);
            },
            error: function() {
                $('#transactionTableBody').html('<tr><td colspan="6" class="text-center py-4 text-danger">Error loading transactions.</td></tr>');
            }
        });
    });
</script>
@endsection

