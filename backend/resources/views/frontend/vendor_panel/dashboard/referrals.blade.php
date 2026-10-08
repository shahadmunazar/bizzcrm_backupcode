@extends('layouts.vendor')

@section('content')
<style>
.stat-card { border: none; border-radius: 12px; transition: transform 0.2s; }
.stat-card:hover { transform: translateY(-5px); }
</style>

<div class="page-wrapper">
    <div class="content">
        <div class="col-12 mt-4">
            <div class="card stat-card shadow-sm p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold m-0">My Referrals</h5>
                        <p class="text-muted small m-0">Overview of customers referred by you.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="referralsTable">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-3 py-3 rounded-start">Company Name</th>
                                <th class="py-3">Email</th>
                                <th class="py-3">Phone</th>
                                <th class="py-3">Joined Date</th>
                                <th class="py-3 text-end rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="referralsTableBody">
                            <tr><td colspan="5" class="text-center py-4">Loading referrals...</td></tr>
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
    $(document).ready(function() {
        $.ajax({
            url: "{{ route('vendor.api.referrals') }}",
            method: 'GET',
            success: function(data) {
                let rows = '';
                if(data.referredBoxes && data.referredBoxes.length > 0) {
                    $.each(data.referredBoxes, function(index, box) {
                        let dateObj = new Date(box.created_at);
                        let dateStr = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        let detailUrl = "{{ route('vendor.referral_details', '') }}/" + box.id;
                        
                        rows += '<tr>';
                        rows += '<td class="py-3 ps-3"><h6 class="mb-0 fw-semibold text-dark">' + (box.name || 'N/A') + '</h6></td>';
                        rows += '<td class="py-3"><span class="text-muted">' + (box.email || 'N/A') + '</span></td>';
                        rows += '<td class="py-3"><span class="text-muted">' + (box.phone || 'N/A') + '</span></td>';
                        rows += '<td class="py-3"><span class="text-muted small">' + dateStr + '</span></td>';
                        rows += '<td class="py-3 text-end"><a href="' + detailUrl + '" class="btn btn-sm btn-light border text-secondary"><i class="fa-solid fa-eye"></i> View</a></td>';
                        rows += '</tr>';
                    });
                } else {
                    rows = '<tr><td colspan="5" class="text-center py-4">No referrals found.</td></tr>';
                }
                $('#referralsTableBody').html(rows);
            },
            error: function() {
                $('#referralsTableBody').html('<tr><td colspan="5" class="text-center py-4 text-danger">Error loading referrals.</td></tr>');
            }
        });
    });
</script>
@endsection
