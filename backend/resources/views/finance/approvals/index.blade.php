@extends('layouts.main')
@section('title', 'Expense Approvals')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-8">
                    <h4 class="page-title">Expense Approvals</h4>
                    <p class="text-muted">Review and process expenses pending your approval.</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table custom-table" id="approvalsTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Employee</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Expense Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6"><strong>Date:</strong> <span id="v_date"></span></div>
                    <div class="col-md-6"><strong>Category:</strong> <span id="v_category"></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6"><strong>Amount:</strong> <span id="v_amount"></span></div>
                    <div class="col-md-6"><strong>Payment Method:</strong> <span id="v_payment"></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6"><strong>Employee:</strong> <span id="v_employee"></span></div>
                    <div class="col-md-6"><strong>Location:</strong> <span id="v_location"></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12"><strong>Description:</strong> <p id="v_description"></p></div>
                </div>
                <hr>
                <h6>Receipts</h6>
                <div id="v_receipts_container" class="d-flex flex-wrap gap-2">
                    <!-- Loaded dynamically -->
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div>
                    <button class="btn btn-success me-2" id="modalApproveBtn">Approve</button>
                    <button class="btn btn-danger" id="modalRejectBtn">Reject</button>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Reject Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <p>Please provide a reason for rejecting this expense. This will be recorded and shown to the employee.</p>
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_comment" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="confirmRejectBtn">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Approve Confirm Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-success text-success mb-3">
                    <i class="ti ti-check fs-36"></i>
                </span>
                <h4 class="mb-1">Approve Expense</h4>
                <p class="mb-3">Are you sure you want to approve this expense?</p>
                <div class="d-flex justify-content-center">
                    <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                    <button type="button" class="btn btn-success" id="confirmApproveBtn">Yes, Approve</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let approvalsTable;
let boxSlug = "{{ session('box_slug', request()->route('box_slug')) }}";
let currentExpenseId = null;

$(document).ready(function() {
    function showToast(message, type = 'success') {
        const $toast = $('#dynamicToast');
        const $message = $('#dynamicToastMessage');
        if($toast.length === 0) return;
        $message.text(message);
        $toast.removeClass('bg-success bg-danger bg-warning').addClass(type === 'success' ? 'bg-success' : 'bg-danger');
        const toast = new bootstrap.Toast($toast[0]);
        toast.show();
    }

    approvalsTable = $('#approvalsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('finance.approvals.data', ['box_slug' => session('box_slug', request()->route('box_slug'))]) }}",
            type: "GET"
        },
        columns: [
            { data: 'expense_date', name: 'expense_date' },
            { 
                data: 'box_user.user.name', 
                name: 'box_user.user.name',
                render: function(data) { return data ? data : 'Unknown Employee'; }
            },
            { 
                data: 'category', 
                render: function(data) { return data ? data.name : 'N/A'; }
            },
            { 
                data: 'amount', 
                render: function(data) { return '₹' + parseFloat(data).toFixed(2); }
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-outline-info view-btn" data-id="${row.id}" title="View Details">
                            <i class="ti ti-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-success approve-btn" data-id="${row.id}" title="Approve">
                            <i class="ti ti-check"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger reject-btn" data-id="${row.id}" title="Reject">
                            <i class="ti ti-x"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    function processExpense(id, status, comment = null) {
        let storeRoute = "{{ route('finance.approvals.store', ['box_slug' => ':slug', 'expense' => ':id']) }}";
        let url = storeRoute.replace(':slug', boxSlug).replace(':id', id);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                status: status,
                comment: comment,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    approvalsTable.ajax.reload();
                    $('#approveModal').modal('hide');
                    $('#rejectModal').modal('hide');
                    $('#viewExpenseModal').modal('hide');
                }
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Error processing request.', 'error');
            }
        });
    }

    // View Modal
    $('#approvalsTable tbody').on('click', '.view-btn', function() {
        let data = approvalsTable.row($(this).parents('tr')).data();
        currentExpenseId = data.id;

        $('#v_date').text(data.expense_date);
        $('#v_category').text(data.category ? data.category.name : 'N/A');
        $('#v_amount').text('₹' + parseFloat(data.amount).toFixed(2));
        $('#v_payment').text(data.payment_method || '-');
        $('#v_employee').text(data.box_user && data.box_user.user ? data.box_user.user.name : '-');
        $('#v_location').text(data.location || '-');
        $('#v_description').text(data.description || 'No description provided.');
        
        let receiptsHtml = '';
        if (data.receipts && data.receipts.length > 0) {
            data.receipts.forEach(r => {
                receiptsHtml += `<a href="/storage/${r.file_path}" target="_blank" class="btn btn-outline-primary m-1"><i class="ti ti-file"></i> ${r.file_name}</a>`;
            });
        } else {
            receiptsHtml = '<span class="text-muted">No receipts attached.</span>';
        }
        $('#v_receipts_container').html(receiptsHtml);
        
        $('#viewExpenseModal').modal('show');
    });

    // Directly Approve
    $('#approvalsTable tbody').on('click', '.approve-btn', function() {
        currentExpenseId = $(this).data('id');
        $('#approveModal').modal('show');
    });

    // Directly Reject
    $('#approvalsTable tbody').on('click', '.reject-btn', function() {
        currentExpenseId = $(this).data('id');
        $('#reject_comment').val('');
        $('#rejectModal').modal('show');
    });

    // Modal Approve/Reject Buttons
    $('#modalApproveBtn').on('click', function() {
        $('#viewExpenseModal').modal('hide');
        $('#approveModal').modal('show');
    });

    $('#modalRejectBtn').on('click', function() {
        $('#viewExpenseModal').modal('hide');
        $('#reject_comment').val('');
        $('#rejectModal').modal('show');
    });

    // Confirm Actions
    $('#confirmApproveBtn').on('click', function() {
        processExpense(currentExpenseId, 'Approved');
    });

    $('#rejectForm').on('submit', function(e) {
        e.preventDefault();
        let comment = $('#reject_comment').val();
        processExpense(currentExpenseId, 'Rejected', comment);
    });
});
</script>
@endpush
@endsection
