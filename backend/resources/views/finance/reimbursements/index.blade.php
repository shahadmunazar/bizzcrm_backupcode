@extends('layouts.main')
@section('title', 'Reimbursements')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <h4 class="page-title">Reimbursements (Finance)</h4>
        </div>

        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-bottom mb-3">
                    <li class="nav-item"><a class="nav-link active" href="#pending-tab" data-bs-toggle="tab">Pending Reimbursements</a></li>
                    <li class="nav-item"><a class="nav-link" href="#paid-tab" data-bs-toggle="tab">Payment History</a></li>
                </ul>
                
                <div class="tab-content">
                    <div class="tab-pane show active" id="pending-tab">
                        <div class="table-responsive">
                            <table class="table custom-table" id="pending-table">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Total Amount</th>
                                        <th>Approved By</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="paid-tab">
                        <div class="table-responsive">
                            <table class="table custom-table" id="paid-table">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Total Amount</th>
                                        <th>Approved By</th>
                                        <th>Processed By</th>
                                        <th>Status</th>
                                        <th>Payment Date</th>
                                        <th>Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Process Payment Modal -->
<div class="modal fade" id="processPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Reimbursement Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="processPaymentForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="expense_id" id="payment_expense_id">
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="text" class="form-control" id="payment_amount" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Date</label>
                        <input type="date" class="form-control" name="payment_date" id="payment_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Reference / Transaction ID</label>
                        <input type="text" class="form-control" name="payment_reference" id="payment_reference" placeholder="Optional">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnProcessPayment">Process Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    var pendingTable = $('#pending-table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('finance.reimbursements.data', ['box_slug' => session('box_slug', request()->route('box_slug'))]) }}",
            type: "GET",
            data: { status: 'Approved' }
        },
        columns: [
            { 
                data: 'box_user', 
                name: 'boxUser.user.name',
                render: function(data, type, row) {
                    return data && data.user ? data.user.name : 'Unknown Employee';
                }
            },
            { 
                data: 'amount', 
                name: 'amount',
                render: function(data, type, row) {
                    return '₹' + parseFloat(data).toFixed(2);
                }
            },
            {
                data: 'approvals',
                name: 'approvals.approver.name',
                render: function(data, type, row) {
                    if (data && data.length > 0) {
                        var lastApproval = data[data.length - 1];
                        return lastApproval.approver ? lastApproval.approver.name : 'Unknown';
                    }
                    return 'N/A';
                }
            },
            { 
                data: 'status', 
                name: 'status',
                render: function(data, type, row) {
                    return '<span class="badge bg-warning">' + data + '</span>';
                }
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    return '<button class="btn btn-sm btn-info btn-process-payment" data-id="'+row.id+'" data-amount="'+row.amount+'">Process Payment</button>';
                }
            }
        ]
    });

    var paidTable = $('#paid-table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('finance.reimbursements.data', ['box_slug' => session('box_slug', request()->route('box_slug'))]) }}",
            type: "GET",
            data: { status: 'Paid' }
        },
        columns: [
            { 
                data: 'box_user', 
                name: 'boxUser.user.name',
                render: function(data, type, row) {
                    return data && data.user ? data.user.name : 'Unknown Employee';
                }
            },
            { 
                data: 'amount', 
                name: 'amount',
                render: function(data, type, row) {
                    return '₹' + parseFloat(data).toFixed(2);
                }
            },
            {
                data: 'approvals',
                name: 'approvals.approver.name',
                render: function(data, type, row) {
                    if (data && data.length > 0) {
                        var lastApproval = data[data.length - 1];
                        return lastApproval.approver ? lastApproval.approver.name : 'Unknown';
                    }
                    return 'N/A';
                }
            },
            {
                data: 'payment',
                name: 'payment.payer.name',
                render: function(data, type, row) {
                    return data && data.payer ? data.payer.name : 'Unknown';
                }
            },
            { 
                data: 'status', 
                name: 'status',
                render: function(data, type, row) {
                    return '<span class="badge bg-success">' + data + '</span>';
                }
            },
            { 
                data: 'payment', 
                name: 'payment.payment_date',
                render: function(data, type, row) {
                    return data && data.payment_date ? data.payment_date : 'N/A';
                }
            },
            { 
                data: 'payment', 
                name: 'payment.payment_reference',
                render: function(data, type, row) {
                    return data && data.payment_reference ? data.payment_reference : 'N/A';
                }
            }
        ]
    });

    // Open Modal
    $(document).on('click', '.btn-process-payment', function() {
        var id = $(this).data('id');
        var amount = parseFloat($(this).data('amount')).toFixed(2);
        $('#payment_expense_id').val(id);
        $('#payment_amount').val('₹' + amount);
        $('#payment_reference').val('');
        $('#processPaymentModal').modal('show');
    });

    // Handle form submit
    $('#processPaymentForm').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var btn = $('#btnProcessPayment');
        btn.prop('disabled', true).text('Processing...');

        $.ajax({
            url: "{{ route('finance.reimbursements.store', ['box_slug' => session('box_slug', request()->route('box_slug'))]) }}",
            type: "POST",
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#processPaymentModal').modal('hide');
                    pendingTable.ajax.reload();
                    paidTable.ajax.reload();
                } else {
                    toastr.error(response.message || 'Error processing payment');
                }
            },
            error: function(xhr) {
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('An error occurred');
                }
            },
            complete: function() {
                btn.prop('disabled', false).text('Process Payment');
            }
        });
    });
});
</script>
@endpush
@endsection
