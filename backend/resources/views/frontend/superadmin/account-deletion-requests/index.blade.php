@extends('layouts.main')

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Account Deletion Requests</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ box_route('superadmin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Account Deletion Requests</li>
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

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-warning text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-white text-uppercase mb-1">Pending Review</h6>
                    <h2 class="mb-0 font-weight-bold">{{ $stats['pending_review'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-secondary text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-white text-uppercase mb-1">Verified (OTP)</h6>
                    <h2 class="mb-0 font-weight-bold">{{ $stats['verified'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-primary text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-white text-uppercase mb-1">Approved</h6>
                    <h2 class="mb-0 font-weight-bold">{{ $stats['approved'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-info text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-white text-uppercase mb-1">Processing</h6>
                    <h2 class="mb-0 font-weight-bold">{{ $stats['processing'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-success text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-white text-uppercase mb-1">Completed</h6>
                    <h2 class="mb-0 font-weight-bold">{{ $stats['completed'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-danger text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-white text-uppercase mb-1">Rejected</h6>
                    <h2 class="mb-0 font-weight-bold">{{ $stats['rejected'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Requests</h5>
            <form action="{{ box_route('superadmin.account-deletion-requests.index') }}" method="GET" class="d-flex align-items-center">
                <select name="status" class="form-select form-select-sm w-auto me-2" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending_verification" {{ request('status') == 'pending_verification' ? 'selected' : '' }}>Pending Verification</option>
                    <option value="pending_review" {{ request('status') == 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="ps-4">Request ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Reason</th>
                            <th>Requested Date</th>
                            <th>Current Status</th>
                            <th>Scheduled Deletion</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                        <tr>
                            <td class="ps-4">#DEL-{{ str_pad($req->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $req->user->name ?? 'N/A' }}</td>
                            <td>{{ $req->email }}</td>
                            <td><span class="text-truncate d-inline-block" style="max-width: 150px;">{{ $req->reason }}</span></td>
                            <td>{{ $req->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                @if($req->status == 'pending_verification')
                                    <span class="badge bg-secondary">Pending Verification</span>
                                @elseif($req->status == 'pending_review')
                                    <span class="badge bg-warning text-dark">Pending Review</span>
                                @elseif($req->status == 'approved')
                                    <span class="badge bg-primary">Approved</span>
                                @elseif($req->status == 'processing')
                                    <span class="badge bg-info text-dark">Processing</span>
                                @elseif($req->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($req->status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @elseif($req->status == 'cancelled')
                                    <span class="badge bg-secondary">Cancelled</span>
                                @endif
                            </td>
                            <td>{{ $req->scheduled_deletion_at ? $req->scheduled_deletion_at->format('d M Y') : '-' }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ box_route('superadmin.account-deletion-requests.show', $req->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No deletion requests found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            {{ $requests->appends(request()->query())->links() }}
        </div>
    </div>
</div>
</div>
@endsection
