@extends('layouts.main')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="col-12 mt-4">
            


            <div class="card stat-card shadow-sm p-4 bg-white border-0" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold m-0">Vendor Percentages</h5>
                        <p class="text-muted small m-0">Set and manage commission percentages for each vendor.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary small text-uppercase">
                            <tr>
                                <th class="py-3 border-0 ps-3" style="font-size: 0.75rem; tracking: 0.05em;">Company / Contact</th>
                                <th class="py-3 border-0" style="font-size: 0.75rem; tracking: 0.05em;">Status</th>
                                <th class="py-3 border-0" style="font-size: 0.75rem; tracking: 0.05em;">Current Percentage</th>
                                <th class="py-3 border-0 text-end pe-3" style="font-size: 0.75rem; tracking: 0.05em;">Update Percentage</th>
                            </tr>
                        </thead>
                        <tbody class="text-dark small">
                            @forelse($vendors as $vendor)
                            <tr>
                                <td class="py-3 ps-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar rounded-3 bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                            {{ strtoupper(substr($vendor->company_name, 0, 2)) }}
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
                                    <span id="percentage-display-{{ $vendor->id }}" class="fw-bold {{ $vendor->vendorPercentage ? 'text-success' : 'text-muted' }}">
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
                                        <button type="submit" class="btn btn-sm btn-primary save-btn">Save</button>
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

@section('script')
<script>
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
                success: function(response) {
                    btn.html(originalText).prop('disabled', false);
                    if(response.status === 'success') {
                        // Update the text in the UI
                        var displaySpan = $('#percentage-display-' + vendorId);
                        displaySpan.text(response.percentage + '%');
                        displaySpan.removeClass('text-muted').addClass('text-success');
                        
                        // Show success alert
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        } else {
                            alert(response.message);
                        }
                    }
                },
                error: function(xhr) {
                    btn.html(originalText).prop('disabled', false);
                    var errorMessage = "An error occurred.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                }
            });
        });
    });
</script>
@endsection
