@extends('layouts.main')

@section('content')
<style>
.stat-card { border: none; border-radius: 12px; transition: transform 0.2s; }
.stat-card:hover { transform: translateY(-3px); }
.percentage-highlight {
    animation: highlightPulse 1.5s ease-out;
}
@keyframes highlightPulse {
    0% { background-color: #d1fae5; transform: scale(1.08); }
    50% { background-color: #ecfdf5; }
    100% { background-color: transparent; transform: scale(1); }
}
</style>

<div class="page-wrapper">
    <div class="content">
        <div class="col-12 mt-4">
            
            <div class="mb-3">
                <a href="{{ route('superadmin.vendors.index', ['box_slug' => request()->route('box_slug')]) }}" class="btn btn-sm btn-light border shadow-sm text-secondary fw-medium">
                    <i class="fa-solid fa-arrow-left me-2"></i>Back to Vendors
                </a>
            </div>

            <div class="card stat-card shadow-sm p-4 bg-white border-0" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold m-0 text-dark d-flex align-items-center gap-2">
                            <i class="fa-solid fa-percent text-primary"></i> Vendor Commission Percentages
                        </h5>
                        <p class="text-muted small m-0">Set and manage dynamic commission percentages for each vendor.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary small text-uppercase">
                            <tr>
                                <th class="py-3 border-0 ps-3" style="font-size: 0.75rem; letter-spacing: 0.05em;">Company / Contact</th>
                                <th class="py-3 border-0" style="font-size: 0.75rem; letter-spacing: 0.05em;">Status</th>
                                <th class="py-3 border-0" style="font-size: 0.75rem; letter-spacing: 0.05em;">Current Percentage</th>
                                <th class="py-3 border-0 text-end pe-3" style="font-size: 0.75rem; letter-spacing: 0.05em;">Update Percentage</th>
                            </tr>
                        </thead>
                        <tbody class="text-dark small">
                            @forelse($vendors as $vendor)
                            <tr>
                                <td class="py-3 ps-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar rounded-3 bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                            {{ strtoupper(substr($vendor->company_name ?? 'VE', 0, 2)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold text-dark" style="font-size: 0.9rem;">{{ $vendor->company_name ?? 'N/A' }}</h6>
                                            <span class="text-muted small">{{ $vendor->user->email ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    @if($vendor->status == 'approved')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-2 px-2.5 py-1.5 small fw-medium">Active</span>
                                    @elseif($vendor->status == 'pending')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-2 px-2.5 py-1.5 small fw-medium">Pending</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-2 px-2.5 py-1.5 small fw-medium">{{ ucfirst($vendor->status) }}</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <span id="percentage-display-{{ $vendor->id }}" class="fw-bold px-2 py-1 rounded d-inline-block {{ $vendor->vendorPercentage ? 'text-success' : 'text-muted' }}">
                                        {{ $vendor->vendorPercentage ? $vendor->vendorPercentage->percentage . '%' : 'Not Set' }}
                                    </span>
                                </td>
                                <td class="py-3 text-end pe-3">
                                    <form action="{{ box_route('superadmin.vendors.percentages.store') }}" method="POST" class="d-flex align-items-center justify-content-end gap-2 ajax-percentage-form">
                                        @csrf
                                        <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                                        <div class="input-group input-group-sm" style="max-width: 150px;">
                                            <input type="number" step="0.01" min="0" max="100" class="form-control" name="percentage" 
                                                value="{{ $vendor->vendorPercentage ? $vendor->vendorPercentage->percentage : '' }}" 
                                                placeholder="e.g. 15.00" required>
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-primary save-btn shadow-sm">Save</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No vendors found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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

    $(document).ready(function() {
        $('.ajax-percentage-form').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var btn = form.find('.save-btn');
            var originalText = btn.html();
            var vendorId = form.find('input[name="vendor_id"]').val();
            
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if(response.status === 'success') {
                        // Success state on button
                        btn.removeClass('btn-primary').addClass('btn-success')
                           .html('<i class="fa-solid fa-check me-1"></i>Saved!').prop('disabled', false);

                        setTimeout(function() {
                            btn.removeClass('btn-success').addClass('btn-primary').html(originalText);
                        }, 2000);

                        // Update the text in the UI
                        var displaySpan = $('#percentage-display-' + vendorId);
                        displaySpan.text(response.percentage + '%');
                        displaySpan.removeClass('text-muted').addClass('text-success percentage-highlight');
                        
                        setTimeout(function() {
                            displaySpan.removeClass('percentage-highlight');
                        }, 1600);
                        
                        // Show success toast
                        showToast(response.message, 'success');
                    } else {
                        btn.html(originalText).prop('disabled', false);
                        showToast(response.message || "An error occurred.", 'danger');
                    }
                },
                error: function(xhr) {
                    btn.html(originalText).prop('disabled', false);
                    var errorMessage = "An error occurred.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showToast(errorMessage, 'danger');
                }
            });
        });
    });
</script>
@endsection
