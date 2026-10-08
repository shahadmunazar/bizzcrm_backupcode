@extends('layouts.vendor')

@section('content')
<div class="page-wrapper">
    <div class="content">
        
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h3 class="fw-bold m-0">My Commissions</h3>
                <p class="text-muted small m-0">A detailed log of all commissions earned from referrals.</p>
            </div>
        </header>

        <div class="card shadow-sm p-4 bg-white border-0" style="border-radius: 12px;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="py-3 border-0 ps-3">Box / Package</th>
                            <th class="py-3 border-0">Transaction Amount</th>
                            <th class="py-3 border-0">Commission Earned</th>
                            <th class="py-3 border-0">Status</th>
                            <th class="py-3 border-0 text-end pe-3">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commissions as $commission)
                        <tr>
                            <td class="py-3 ps-3">
                                <span class="fw-medium text-dark">{{ $commission->box ? $commission->box->name : 'N/A' }}</span>
                            </td>
                            <td class="py-3">
                                <span class="text-dark">₹{{ number_format($commission->transaction_amount, 2) }}</span>
                            </td>
                            <td class="py-3">
                                <span class="fw-bold text-success">₹{{ number_format($commission->commission_amount, 2) }}</span>
                            </td>
                            <td class="py-3">
                                @if($commission->status === 'paid')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Paid</span>
                                @elseif($commission->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Pending</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">{{ ucfirst($commission->status) }}</span>
                                @endif
                            </td>
                            <td class="py-3 text-end pe-3">
                                <span class="text-muted small">{{ $commission->created_at->format('M d, Y') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No commissions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</div>
@endsection
