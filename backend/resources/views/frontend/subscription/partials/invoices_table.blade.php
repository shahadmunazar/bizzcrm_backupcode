@forelse($invoices as $inv)
    <tr>
        <td class="fw-bold text-dark">{{ $inv['id'] ?? '-' }}</td>
        <td>{{ $inv['plan_name'] ?? '-' }}</td>
        <td>{{ isset($inv['created_at']) ? date('m/d/Y', $inv['created_at']) : '-' }}</td>
        <td>{{ isset($inv['currency']) && $inv['currency'] !== 'INR' ? '$' : '₹' }}{{ number_format(($inv['amount'] ?? 0) / 100, 2) }}</td>
        <td>
            @if(($inv['status'] ?? '') === 'paid')
                <span class="text-success"><i class="ti ti-point-filled"></i> Paid</span>
            @elseif(($inv['status'] ?? '') === 'issued')
                <span class="text-warning"><i class="ti ti-point-filled"></i> Issued</span>
            @else
                <span class="text-muted"><i class="ti ti-point-filled"></i> {{ ucfirst($inv['status'] ?? '') }}</span>
            @endif
        </td>
        <td>
            @if(!empty($inv['short_url']))
                <a href="{{ $inv['short_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-external-link"></i> View</a>
            @else
                -
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center text-muted py-4">No payment history found</td>
    </tr>
@endforelse
