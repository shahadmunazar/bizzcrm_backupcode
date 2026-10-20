@extends('layouts.main')

@section('content')
<style>
.stat-card {
    border: none;
    border-radius: 12px;
    transition: transform 0.2s;
}
.stat-card:hover {
    transform: translateY(-3px);
}
.icon-box {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.btn-xs {
    padding: 0.22rem 0.55rem;
    font-size: 0.75rem;
    border-radius: 6px;
    font-weight: 600;
}
</style>

<div class="page-wrapper">
    <div class="content">
        
        <header class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold m-0 text-dark">Vendor Commission Settlements & Payouts</h3>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small">Superadmin Ledger</span>
                </div>
                <p class="text-muted small m-0">Review, verify, and process commission payouts for vendor partner referrals.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('superadmin.vendors.index', ['box_slug' => request()->route('box_slug')]) }}" class="btn btn-sm btn-light border shadow-sm fw-medium text-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Vendors
                </a>
                <button class="btn btn-sm btn-white border shadow-sm rounded-3" onclick="location.reload()">
                    <i class="fa-solid fa-rotate me-1"></i> Refresh
                </button>
            </div>
        </header>

        <!-- KPI Metric Grid -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card stat-card shadow-sm p-4 bg-white border-0" style="border-left: 4px solid #4f46e5 !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-primary-subtle text-primary fs-5">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-bold text-uppercase">Gross Referral Sales</span>
                            <h4 class="fw-bold m-0 text-dark mt-1">₹{{ number_format($grossSales, 2) }}</h4>
                            <small class="text-muted">Total subscriptions under vendor refs</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card shadow-sm p-4 bg-white border-0" style="border-left: 4px solid #059669 !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-success-subtle text-success fs-5">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-bold text-uppercase">Net Cleared Payouts (Paid)</span>
                            <h4 class="fw-bold m-0 text-success mt-1">₹{{ number_format($netCleared, 2) }}</h4>
                            <small class="text-muted">Commissions paid out to vendors</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card shadow-sm p-4 bg-white border-0" style="border-left: 4px solid #f59e0b !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-warning-subtle text-warning fs-5">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-bold text-uppercase">Unpaid & Pending Payouts</span>
                            <h4 class="fw-bold m-0 text-warning mt-1">₹{{ number_format($pendingBalance, 2) }}</h4>
                            <small class="text-muted">Awaiting admin verification / settlement</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settlements Table -->
        <div class="card stat-card shadow-sm p-4 bg-white border-0" style="border-radius: 14px;">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold m-0 text-dark d-flex align-items-center gap-2">
                        <i class="fa-solid fa-receipt text-primary"></i> Commission Payout Settlements Log
                    </h5>
                    <p class="text-muted small m-0">Commissions default to Unpaid until verified and settled by superadmin.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="settlementsTable">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="py-3 border-0 ps-3">Reference ID</th>
                            <th class="py-3 border-0">Vendor Partner</th>
                            <th class="py-3 border-0">Referred Company</th>
                            <th class="py-3 border-0">Plan Volume</th>
                            <th class="py-3 border-0">Commission</th>
                            <th class="py-3 border-0 text-center">Status</th>
                            <th class="py-3 border-0">Timeline / Details</th>
                            <th class="py-3 border-0 text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                    @forelse($transactions as $txn)
                    <tr id="row-txn-{{ $txn->id }}">
                        <td class="py-3 ps-3">
                            <span class="fw-bold text-dark font-monospace">#TXN-{{ str_pad($txn->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="py-3">
                            @if($txn->vendor)
                                <a href="{{ route('superadmin.vendors.detail', ['box_slug' => request()->route('box_slug'), 'id' => $txn->vendor->id]) }}" class="text-dark fw-semibold text-decoration-none">
                                    {{ $txn->vendor->company_name ?: ($txn->vendor->user->name ?? 'Vendor #' . $txn->vendor->id) }}
                                </a>
                                <div class="small text-muted font-monospace">{{ $txn->vendor->referral_code ?? '' }}</div>
                            @else
                                <span class="text-muted">Vendor #{{ $txn->vendor_id }}</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <span class="text-dark fw-medium">{{ $txn->box->name ?? 'N/A' }}</span>
                            @if($txn->box)
                                <div class="small text-muted font-monospace">{{ $txn->box->slug }}</div>
                            @endif
                        </td>
                        <td class="py-3">
                            <span class="text-dark fw-medium">₹{{ number_format($txn->transaction_amount, 2) }}</span>
                        </td>
                        <td class="py-3">
                            <span class="text-primary fw-bold">₹{{ number_format($txn->commission_amount, 2) }}</span>
                            <span class="small text-muted">({{ $txn->commission_percentage }}%)</span>
                        </td>
                        <td class="py-3 text-center" id="status-cell-{{ $txn->id }}">
                            @if($txn->status === 'paid')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                                    <i class="fa-solid fa-check-double me-1"></i> Paid
                                </span>
                            @elseif($txn->status === 'verified')
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 small">
                                    <i class="fa-solid fa-check-circle me-1"></i> Verified
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 small">
                                    <i class="fa-solid fa-clock me-1"></i> Unpaid
                                </span>
                            @endif
                        </td>
                        <td class="py-3" id="timeline-cell-{{ $txn->id }}">
                            <div class="text-dark small">{{ $txn->created_at->format('M d, Y h:i A') }}</div>
                            @if($txn->status === 'paid' && $txn->paid_at)
                                <div class="small text-success fw-medium">Paid: {{ \Carbon\Carbon::parse($txn->paid_at)->format('M d, Y h:i A') }}</div>
                                @if($txn->payment_reference)
                                    <div class="small text-muted font-monospace">Ref: {{ $txn->payment_reference }}</div>
                                @endif
                            @elseif($txn->status === 'verified' && $txn->verified_at)
                                <div class="small text-info fw-medium">Verified: {{ \Carbon\Carbon::parse($txn->verified_at)->format('M d, Y h:i A') }}</div>
                            @else
                                <div class="small text-muted">Awaiting Admin Action</div>
                            @endif
                        </td>
                        <td class="py-3 text-end pe-3" id="action-cell-{{ $txn->id }}">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                @if($txn->status === 'paid')
                                    <span class="badge bg-light text-muted border small me-1"><i class="fa-solid fa-check text-success me-1"></i> Settled</span>
                                    <button class="btn btn-xs btn-outline-secondary" onclick="markCommissionUnpaid({{ $txn->id }})" title="Revert to Unpaid">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                @elseif($txn->status === 'verified')
                                    <button class="btn btn-xs btn-success text-white shadow-sm" onclick="openPayCommissionModal({{ $txn->id }}, {{ $txn->commission_amount }})" title="Pay Vendor">
                                        <i class="fa-solid fa-wallet me-1"></i> Pay
                                    </button>
                                    <button class="btn btn-xs btn-outline-secondary" onclick="markCommissionUnpaid({{ $txn->id }})" title="Revert to Unpaid">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                @else
                                    <button class="btn btn-xs btn-outline-info" onclick="verifyCommission({{ $txn->id }})" title="Verify Commission">
                                        <i class="fa-solid fa-check-circle me-1"></i> Verify
                                    </button>
                                    <button class="btn btn-xs btn-success text-white shadow-sm" onclick="openPayCommissionModal({{ $txn->id }}, {{ $txn->commission_amount }})" title="Pay Vendor">
                                        <i class="fa-solid fa-wallet me-1"></i> Pay
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted"><i class="fa-solid fa-receipt me-2"></i> No commission transactions found.</td></tr>
                    @endforelse
                </tbody>
                </table>
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
    let statusUpdateUrlTemplate = "{{ route('superadmin.vendors.commission.status', ['box_slug' => request()->route('box_slug'), 'commissionId' => ':commissionId']) }}";

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
                    setTimeout(function() { location.reload(); }, 600);
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
        $('#payModalAmount').text('₹' + parseFloat(amount).toFixed(2));
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
                    setTimeout(function() { location.reload(); }, 600);
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
                    setTimeout(function() { location.reload(); }, 600);
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
</script>
@endsection
