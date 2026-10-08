@extends('layouts.main')

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Request Details #DEL-{{ str_pad($deletionRequest->id, 6, '0', STR_PAD_LEFT) }}</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ box_route('superadmin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ box_route('superadmin.account-deletion-requests.index') }}">Account Deletion Requests</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                <div class="ms-2 head-icons">
                    <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
                        <i class="ti ti-chevrons-up"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <a href="{{ box_route('superadmin.account-deletion-requests.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        
        <div>
            @if($deletionRequest->status == 'pending_verification')
                <span class="badge bg-secondary fs-6">Pending Verification</span>
            @elseif($deletionRequest->status == 'pending_review')
                <span class="badge bg-warning text-dark fs-6">Pending Review</span>
            @elseif($deletionRequest->status == 'approved')
                <span class="badge bg-primary fs-6">Approved</span>
            @elseif($deletionRequest->status == 'processing')
                <span class="badge bg-info text-dark fs-6">Processing</span>
            @elseif($deletionRequest->status == 'completed')
                <span class="badge bg-success fs-6">Completed</span>
            @elseif($deletionRequest->status == 'rejected')
                <span class="badge bg-danger fs-6">Rejected</span>
            @elseif($deletionRequest->status == 'cancelled')
                <span class="badge bg-secondary fs-6">Cancelled</span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Left Column: User Info & Questionnaire -->
        <div class="col-lg-8 mb-4">
            
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-user text-primary me-2"></i> User Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small">Name</label>
                            <div class="fw-bold">{{ $deletionRequest->user->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small">Email</label>
                            <div class="fw-bold">{{ $deletionRequest->email }}</div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small">Mobile</label>
                            <div class="fw-bold">{{ $deletionRequest->user->mobile ?? 'N/A' }}</div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small">User ID</label>
                            <div class="fw-bold">#{{ $deletionRequest->user_id }}</div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small">Account Created</label>
                            <div class="fw-bold">{{ $deletionRequest->user->created_at ? $deletionRequest->user->created_at->format('d M Y, h:i A') : 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-clipboard-list text-primary me-2"></i> Deletion Questionnaire</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="text-muted small">Reason</label>
                        <div class="fw-bold">{{ $deletionRequest->reason }}</div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <label class="text-muted small">Experience</label>
                            <div class="fw-bold">{{ $deletionRequest->experience ?: 'Not provided' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small">Problems Experienced</label>
                            @if($deletionRequest->problems && count($deletionRequest->problems) > 0)
                                <ul class="mb-0 ps-3">
                                    @foreach($deletionRequest->problems as $problem)
                                        <li class="fw-bold">{{ $problem }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="fw-bold">None</div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="text-muted small">Additional Feedback</label>
                        <div class="p-3 bg-light rounded text-dark">
                            {{ $deletionRequest->feedback ?: 'No additional feedback provided.' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-check-square text-primary me-2"></i> Confirmation Checklist</h6>
                </div>
                <div class="card-body">
                    @if($deletionRequest->confirmations && count($deletionRequest->confirmations) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($deletionRequest->confirmations as $confirm)
                                <li class="list-group-item px-0 border-0 d-flex">
                                    <i class="fas fa-check text-success mt-1 me-3"></i>
                                    <div>
                                        <div class="fw-bold">{{ $confirm }}</div>
                                        <div class="text-muted small">✓ Confirmed</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted">No confirmations recorded.</div>
                    @endif
                </div>
            </div>
            
            @if($deletionRequest->status == 'rejected')
            <div class="card shadow-sm border-0 mb-4 bg-danger text-white">
                <div class="card-header bg-danger py-3 border-0">
                    <h6 class="mb-0 fw-bold text-white"><i class="fas fa-times-circle me-2"></i> Rejection Details</h6>
                </div>
                <div class="card-body bg-white text-dark rounded-bottom">
                    <label class="text-muted small">Reason for Rejection</label>
                    <div class="fw-bold">{{ $deletionRequest->rejection_reason }}</div>
                    <div class="mt-2 text-muted small">
                        Rejected by: {{ $deletionRequest->reviewer->name ?? 'Unknown' }} on {{ $deletionRequest->rejected_at->format('d M Y, h:i A') }}
                    </div>
                </div>
            </div>
            @endif

        </div>

        <!-- Right Column: Verification & Audit & Actions -->
        <div class="col-lg-4">
            
            <!-- Actions -->
            @if($deletionRequest->status == 'pending_review')
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-gavel text-primary me-2"></i> Review Actions</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">This request is pending Super Admin review. Approving it will schedule the account for deletion in 7 days.</p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#approveModal">
                            Approve Deletion
                        </button>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            Reject Request
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Verification -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-shield-alt text-primary me-2"></i> Verification</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Email OTP</span>
                        @if($deletionRequest->otp_verified_at)
                            <span class="badge bg-success"><i class="fas fa-check me-1"></i> Verified</span>
                        @else
                            <span class="badge bg-secondary">Pending</span>
                        @endif
                    </div>
                    @if($deletionRequest->otp_verified_at)
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Verified At</span>
                        <span class="fw-bold small">{{ $deletionRequest->otp_verified_at->format('d M Y, h:i A') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Audit Timeline -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-history text-primary me-2"></i> Audit Timeline</h6>
                </div>
                <div class="card-body p-0">
                    <div class="audit-timeline p-4">
                        @forelse($deletionRequest->audits as $audit)
                        <div class="audit-item pb-4 {{ $loop->last ? 'border-0 pb-0' : '' }}">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold text-dark">{{ $audit->event }}</span>
                                <span class="text-muted small">{{ $audit->created_at->format('d M Y H:i') }}</span>
                            </div>
                            <p class="text-muted small mb-0">{{ $audit->description }}</p>
                            @if($audit->user_id)
                                <div class="text-primary small mt-1"><i class="fas fa-user-circle me-1"></i> {{ $audit->user->name ?? 'User' }}</div>
                            @endif
                        </div>
                        @empty
                        <div class="text-muted text-center small">No audit logs found.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary">Approve Deletion Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveForm">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to approve this account deletion request?</p>
                    
                    <div class="bg-light p-3 rounded mb-3">
                        <div class="row mb-2">
                            <div class="col-5 text-muted small">User</div>
                            <div class="col-7 fw-bold">{{ $deletionRequest->user->name ?? 'N/A' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-muted small">Request ID</div>
                            <div class="col-7 fw-bold">#DEL-{{ str_pad($deletionRequest->id, 6, '0', STR_PAD_LEFT) }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-muted small">Requested Date</div>
                            <div class="col-7 fw-bold">{{ $deletionRequest->created_at->format('d M Y') }}</div>
                        </div>
                        <div class="row">
                            <div class="col-5 text-muted small">Scheduled Deletion</div>
                            <div class="col-7 fw-bold text-primary">{{ now()->addDays(7)->format('d M Y') }}</div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info py-2 d-flex align-items-center mb-0">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <span class="small">The account will not be deleted immediately. A 7-day processing period will commence upon approval.</span>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="approveBtn">Confirm Approval</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">Reject Deletion Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason for rejection <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Please provide a reason for rejecting this request..." required></textarea>
                    </div>
                    <p class="text-muted small mb-0">The user will be notified of this rejection and the reason provided.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="rejectBtn">Reject Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function showAlert(type, message) {
        // Optionally insert an alert div if you want, or use toastr if it's available in the CRM
        alert(message);
    }

    $('#approveForm').on('submit', function(e) {
        e.preventDefault();
        let $btn = $('#approveBtn');
        let originalText = $btn.text();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Approving...').prop('disabled', true);

        $.ajax({
            url: '{{ box_route('superadmin.account-deletion-requests.approve', $deletionRequest->id) }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if (res.status === 'success') {
                    window.location.reload();
                } else {
                    showAlert('error', res.message);
                    $btn.text(originalText).prop('disabled', false);
                }
            },
            error: function(xhr) {
                showAlert('error', xhr.responseJSON?.message || 'Error approving request.');
                $btn.text(originalText).prop('disabled', false);
            }
        });
    });

    $('#rejectForm').on('submit', function(e) {
        e.preventDefault();
        let $btn = $('#rejectBtn');
        let originalText = $btn.text();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Rejecting...').prop('disabled', true);

        $.ajax({
            url: '{{ box_route('superadmin.account-deletion-requests.reject', $deletionRequest->id) }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if (res.status === 'success') {
                    window.location.reload();
                } else {
                    showAlert('error', res.message);
                    $btn.text(originalText).prop('disabled', false);
                }
            },
            error: function(xhr) {
                showAlert('error', xhr.responseJSON?.message || 'Error rejecting request.');
                $btn.text(originalText).prop('disabled', false);
            }
        });
    });
</script>
<style>
    .audit-timeline {
        position: relative;
    }
    .audit-timeline::before {
        content: '';
        position: absolute;
        top: 24px;
        bottom: 24px;
        left: 24px;
        width: 2px;
        background: #e9ecef;
    }
    .audit-item {
        position: relative;
        padding-left: 20px;
        border-left: 2px solid transparent; /* spacing */
    }
    .audit-item::before {
        content: '';
        position: absolute;
        left: -21px;
        top: 5px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #0d6efd;
    }
</style>
@endsection
