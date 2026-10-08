@extends('layouts.main')

@section('content')
<style>

/* Clean up variables that might conflict or are unnecessary if layout.main handles them */
.stat-card { border: none; border-radius: 12px; transition: transform 0.2s; }
.stat-card:hover { transform: translateY(-5px); }
</style>

<div class="page-wrapper">
    <div class="content">
        
<!-- Recently Registered Vendors Table Section -->
<div class="col-12 mt-4">
    <div class="card stat-card shadow-sm p-4 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold m-0">Vendors</h5>
                <p class="text-muted small m-0">Overview of the latest suppliers onboarding into your network</p>
            </div>
            <button class="btn btn-sm btn-light border fw-medium text-secondary px-3 py-1.5">
                View All Vendors
            </button>
        </div>

        <!-- Responsive Table Container Wrapper -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary small text-uppercase">
                    <tr>
                        <th class="py-3 border-0 ps-3" style="font-size: 0.75rem; tracking: 0.05em;">Company / Contact</th>
                        <th class="py-3 border-0" style="font-size: 0.75rem; tracking: 0.05em;">User Account</th>
                        <th class="py-3 border-0" style="font-size: 0.75rem; tracking: 0.05em;">Phone</th>
                        <th class="py-3 border-0" style="font-size: 0.75rem; tracking: 0.05em;">Status</th>
                        <th class="py-3 border-0 text-center" style="font-size: 0.75rem; tracking: 0.05em;">Referrals</th>
                        <th class="py-3 border-0" style="font-size: 0.75rem; tracking: 0.05em;">Registration Date</th>
                        <th class="py-3 border-0 text-end pe-3" style="font-size: 0.75rem; tracking: 0.05em;">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-dark small" id="vendorTableBody">
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-spinner fa-spin me-2"></i> Loading vendors...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Vendor Action Modal -->
<div class="modal fade" id="vendorActionModal" tabindex="-1" aria-labelledby="vendorActionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold" id="vendorActionModalLabel">Review Application</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted small mb-4">You are reviewing the application for <strong id="modalCompanyName" class="text-dark"></strong>.</p>
        
        <form id="vendorActionForm">
            <input type="hidden" id="modalVendorId" value="">
            
            <div class="mb-4">
                <label class="form-label fw-semibold small">Target Amount (₹) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control" id="modalMonthlyTarget" value="0" required>
                <div class="form-text text-muted">Set the sales target limit for this vendor.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small">Target Duration</label>
                <select class="form-select" id="modalTargetType" onchange="toggleCustomDates()">
                    <option value="monthly">Monthly</option>
                    <option value="custom">Custom Date Range</option>
                </select>
            </div>

            <div class="mb-4 row" id="customDatesContainer" style="display: none;">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Start Date</label>
                    <input type="date" class="form-control" id="modalTargetStartDate">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">End Date</label>
                    <input type="date" class="form-control" id="modalTargetEndDate">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small text-uppercase text-muted">Select Action</label>
                <div class="d-flex gap-2">
                    <div class="form-check form-check-inline custom-radio flex-fill border rounded-3 p-3 m-0" style="cursor: pointer;" onclick="$('#actionApprove').prop('checked', true); toggleRemarks();">
                        <input class="form-check-input mt-1" type="radio" name="action_type" id="actionApprove" value="approve" checked onchange="toggleRemarks()">
                        <label class="form-check-label ms-1 fw-semibold text-success small" for="actionApprove" style="cursor: pointer;">Approve Vendor</label>
                    </div>
                    <div class="form-check form-check-inline custom-radio flex-fill border rounded-3 p-3 m-0" style="cursor: pointer;" onclick="$('#actionTarget').prop('checked', true); toggleRemarks();">
                        <input class="form-check-input mt-1" type="radio" name="action_type" id="actionTarget" value="target" onchange="toggleRemarks()">
                        <label class="form-check-label ms-1 fw-semibold text-primary small" for="actionTarget" style="cursor: pointer;">Update Target Only</label>
                    </div>
                    <div class="form-check form-check-inline custom-radio flex-fill border rounded-3 p-3 m-0" style="cursor: pointer;" onclick="$('#actionReject').prop('checked', true); toggleRemarks();">
                        <input class="form-check-input mt-1" type="radio" name="action_type" id="actionReject" value="reject" onchange="toggleRemarks()">
                        <label class="form-check-label ms-1 fw-semibold text-danger small" for="actionReject" style="cursor: pointer;">Reject Vendor</label>
                    </div>
                </div>
            </div>

            <div class="mb-3" id="remarksContainer" style="display: none;">
                <label class="form-label fw-semibold small">Rejection Remarks <span class="text-danger">*</span></label>
                <textarea class="form-control" id="modalRemarks" rows="3" placeholder="Please provide a reason for rejection..."></textarea>
                <div class="form-text text-muted">These remarks will be included in the email sent to the vendor.</div>
            </div>
        </form>
      </div>
      <div class="modal-footer border-top-0 pt-0">
        <button type="button" class="btn btn-light shadow-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary shadow-sm px-4" onclick="submitVendorAction()">Confirm Action</button>
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
        $.ajax({
            url: '{{ route('superadmin.vendors.api.list', ['box_slug' => request()->route('box_slug')]) }}',
            method: 'GET',
            success: function(data) {
                let rows = '';
                if(data.vendors && data.vendors.length > 0) {
                    $.each(data.vendors, function(index, vendor) {
                        let dateObj = new Date(vendor.created_at);
                        let dateStr = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        let detailUrl = "{{ route('superadmin.vendors.detail', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}".replace(':id', vendor.id);
                        
                        let statusBadge = '';
                        if(vendor.status == 'approved') {
                            statusBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">Approved</span>';
                        } else if(vendor.status == 'rejected') {
                            statusBadge = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 small">Rejected</span>';
                        } else {
                            statusBadge = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 small">Pending</span>';
                        }

                        let avatarUrl = vendor.profile_image 
                            ? '{{ asset('storage') }}/' + vendor.profile_image
                            : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(vendor.company_name) + '&background=random';

                        rows += '<tr>';
                        rows += '<td class="py-3 ps-3">';
                        rows += '  <div class="d-flex align-items-center gap-3">';
                        rows += '    <img src="' + avatarUrl + '" class="rounded-circle object-fit-cover" width="40" height="40" alt="">';
                        rows += '    <div>';
                        rows += '      <h6 class="mb-0 fw-semibold text-dark">' + (vendor.company_name || 'N/A') + '</h6>';
                        rows += '      <span class="text-muted small">' + (vendor.referral_code || 'N/A') + '</span>';
                        rows += '    </div>';
                        rows += '  </div>';
                        rows += '</td>';
                        
                        rows += '<td class="py-3"><div class="d-flex flex-column"><span class="text-dark fw-medium">' + (vendor.user ? vendor.user.name : 'N/A') + '</span><span class="text-muted small">' + (vendor.user ? vendor.user.email : 'N/A') + '</span></div></td>';
                        rows += '<td class="py-3"><span class="text-dark">' + (vendor.mobile_number || 'N/A') + '</span></td>';
                        rows += '<td class="py-3">' + statusBadge + '</td>';
                        rows += '<td class="py-3 text-center"><span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold">' + (vendor.referred_boxes_count || 0) + '</span></td>';
                        rows += '<td class="py-3"><span class="text-muted small">' + dateStr + '</span></td>';
                        
                        rows += '<td class="py-3 text-end pe-3">';
                        rows += '  <a href="' + detailUrl + '" class="btn btn-sm btn-light border shadow-sm text-secondary"><i class="fa-solid fa-arrow-right"></i></a>';
                        rows += '  <button class="btn btn-sm btn-primary ms-1" onclick="openActionModal(' + vendor.id + ', \'' + (vendor.company_name ? vendor.company_name.replace(/'/g, "\\'") : '') + '\', ' + (vendor.monthly_target || 0) + ', \'' + (vendor.target_type || 'monthly') + '\', \'' + (vendor.target_start_date || '') + '\', \'' + (vendor.target_end_date || '') + '\')">Action</button>';
                        rows += '</td>';
                        rows += '</tr>';
                    });
                } else {
                    rows = '<tr><td colspan="7" class="text-center py-4">No vendors found.</td></tr>';
                }
                $('#vendorTableBody').html(rows);
            },
            error: function() {
                $('#vendorTableBody').html('<tr><td colspan="7" class="text-center py-4 text-danger">Error loading vendors.</td></tr>');
            }
        });
    });

    function openActionModal(id, companyName, target, type, start, end) {
        $('#modalVendorId').val(id);
        $('#modalCompanyName').text(companyName);
        $('#modalMonthlyTarget').val(target);
        $('#modalTargetType').val(type);
        $('#modalTargetStartDate').val(start);
        $('#modalTargetEndDate').val(end);
        
        $('#actionApprove').prop('checked', true);
        toggleRemarks();
        toggleCustomDates();
        
        var actionModal = new bootstrap.Modal(document.getElementById('vendorActionModal'));
        actionModal.show();
    }

    function toggleCustomDates() {
        if ($('#modalTargetType').val() === 'custom') {
            $('#customDatesContainer').slideDown(200);
        } else {
            $('#customDatesContainer').slideUp(200);
        }
    }

    function toggleRemarks() {
        if ($('#actionReject').is(':checked')) {
            $('#remarksContainer').slideDown(200);
        } else {
            $('#remarksContainer').slideUp(200);
            $('#modalRemarks').val('');
        }
    }

    function submitVendorAction() {
        var id = $('#modalVendorId').val();
        var action = $('input[name="action_type"]:checked').val();
        var remarks = $('#modalRemarks').val();
        var target = $('#modalMonthlyTarget').val();
        var targetType = $('#modalTargetType').val();
        var targetStart = $('#modalTargetStartDate').val();
        var targetEnd = $('#modalTargetEndDate').val();

        if (action === 'reject' && remarks.trim() === '') {
            alert("Please provide rejection remarks.");
            return;
        }

        var url = "";
        if (action === 'approve') {
            url = "{{ route('superadmin.vendors.approve', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}".replace(':id', id);
        } else if (action === 'target') {
            url = "{{ route('superadmin.vendors.target', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}".replace(':id', id);
        } else {
            url = "{{ route('superadmin.vendors.reject', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}".replace(':id', id);
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                remarks: remarks,
                monthly_target: target,
                target_type: targetType,
                target_start_date: targetStart,
                target_end_date: targetEnd
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert("An error occurred.");
                }
            },
            error: function() {
                alert("Server error occurred.");
            }
        });
    }
</script>
@endsection
