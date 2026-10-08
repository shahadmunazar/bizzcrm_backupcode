@forelse($creditNotes as $note)
<tr>
    <td class="text-dark fw-bold">{{ $note->note_id }}</td>
    <td><span class="badge {{ $note->type == 'Refund' ? 'bg-soft-info text-info' : 'bg-soft-primary text-primary' }}">{{ $note->type }}</span></td>
    <td>{{ $note->created_at->format('m/d/Y') }}</td>
    <td>@if($note->currency == 'INR') ₹ @else $ @endif{{ number_format($note->amount, 2) }}</td>
    <td>
        @if($note->status == 'Issued')
            <span class="text-warning"><i class="ti ti-point-filled"></i> Issued</span>
        @elseif($note->status == 'Refunded')
            <span class="text-success"><i class="ti ti-point-filled"></i> Refunded</span>
        @else
            <span class="text-muted"><i class="ti ti-point-filled"></i> {{ ucfirst($note->status) }}</span>
        @endif
    </td>
    <td><button class="btn btn-sm btn-light border dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button></td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center text-muted py-4">No credit notes found</td>
</tr>
@endforelse
