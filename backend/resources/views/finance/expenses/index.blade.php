@extends('layouts.main')
@section('title', 'My Expenses')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-8">
                    <h4 class="page-title">My Expenses</h4>
                </div>
                <div class="col-4 text-end">
                    <button class="btn btn-primary" onclick="openAddModal()">
                        <i class="ti ti-plus"></i> Submit Expense
                    </button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table custom-table" id="expensesTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
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

<!-- Submit / Edit Modal -->
<div class="modal fade" id="expenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="expenseForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="expenseModalTitle">Submit New Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="expense_id" name="id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="e_expense_date" name="expense_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="e_expense_category_id" name="expense_category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="e_amount" name="amount" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" id="e_payment_method" name="payment_method">
                                <option value="">Select Method</option>
                                <option value="Cash">Cash</option>
                                <option value="Card">Card</option>
                                <option value="UPI">UPI</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" id="e_location" name="location">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="e_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Receipts (Images/PDF)</label>
                            <input type="file" class="form-control" id="e_receipts" name="receipts[]" multiple accept="image/*,.pdf">
                            <small class="text-muted">You can select multiple files. Max 5MB per file.</small>
                            <div id="existing_receipts_container" class="mt-2" style="display:none;">
                                <h6>Existing Receipts:</h6>
                                <div id="existing_receipts_list" class="d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveExpenseBtn">Save Expense</button>
                </div>
            </form>
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
                    <div class="col-md-6"><strong>Location:</strong> <span id="v_location"></span></div>
                    <div class="col-md-6"><strong>Status:</strong> <span id="v_status"></span></div>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let expensesTable;
let boxSlug = "{{ session('box_slug', request()->route('box_slug')) }}";

$(document).ready(function() {
    expensesTable = $('#expensesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('finance.expenses.data', ['box_slug' => session('box_slug', request()->route('box_slug'))]) }}",
            type: "GET"
        },
        columns: [
            { data: 'expense_date', name: 'expense_date' },
            { 
                data: 'category', 
                render: function(data) { return data ? data.name : 'N/A'; }
            },
            { 
                data: 'amount', 
                render: function(data) { return '₹' + parseFloat(data).toFixed(2); }
            },
            { data: 'payment_method', defaultContent: '-' },
            { 
                data: 'status', 
                render: function(data) {
                    let bg = data === 'Pending' ? 'bg-warning' : (data === 'Approved' ? 'bg-success' : (data === 'Rejected' ? 'bg-danger' : 'bg-secondary'));
                    return `<span class="badge ${bg}">${data}</span>`;
                }
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    let html = `<button class="btn btn-sm btn-outline-info view-btn" data-id="${row.id}" title="View"><i class="ti ti-eye"></i></button>`;
                    
                    if (row.status === 'Draft' || row.status === 'Pending') {
                        html += ` <button class="btn btn-sm btn-outline-primary edit-btn" data-id="${row.id}" title="Edit"><i class="ti ti-edit"></i></button>`;
                        html += ` <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${row.id}" title="Delete"><i class="ti ti-trash"></i></button>`;
                    }
                    return html;
                }
            }
        ]
    });

    // Form Submission
    $('#expenseForm').on('submit', function(e) {
        e.preventDefault();
        $('#saveExpenseBtn').prop('disabled', true).text('Saving...');
        
        let id = $('#expense_id').val();
        let storeRoute = "{{ route('finance.expenses.store', ['box_slug' => ':slug']) }}";
        let updateRoute = "{{ route('finance.expenses.update', ['box_slug' => ':slug', 'expense' => ':id']) }}";
        let url = id ? updateRoute.replace(':slug', boxSlug).replace(':id', id) : storeRoute.replace(':slug', boxSlug);
        
        let formData = new FormData(this);
        if (id) formData.append('_method', 'PUT');

        $.ajax({
            url: url,
            type: 'POST', // Always POST for FormData, fake PUT with _method
            data: formData,
            contentType: false,
            processData: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                if (response.success) {
                    $('#expenseModal').modal('hide');
                    expensesTable.ajax.reload();
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Error saving expense.');
            },
            complete: function() { $('#saveExpenseBtn').prop('disabled', false).text('Save Expense'); }
        });
    });

    // Edit Button
    $('#expensesTable tbody').on('click', '.edit-btn', function() {
        let data = expensesTable.row($(this).parents('tr')).data();
        $('#expense_id').val(data.id);
        $('#e_expense_date').val(data.expense_date);
        $('#e_expense_category_id').val(data.expense_category_id);
        $('#e_amount').val(data.amount);
        $('#e_payment_method').val(data.payment_method);
        $('#e_location').val(data.location);
        $('#e_description').val(data.description);
        
        // Handle receipts display for removal
        let receiptsHtml = '';
        if (data.receipts && data.receipts.length > 0) {
            data.receipts.forEach(r => {
                receiptsHtml += `
                    <div class="border p-2 rounded position-relative" id="receipt_box_${r.id}">
                        <span class="d-block mb-1 text-truncate" style="max-width: 150px;">${r.file_name}</span>
                        <a href="/storage/${r.file_path}" target="_blank" class="btn btn-xs btn-primary">View</a>
                        <button type="button" class="btn btn-xs btn-danger" onclick="removeReceipt(${r.id})">Remove</button>
                    </div>
                `;
            });
            $('#existing_receipts_list').html(receiptsHtml);
            $('#existing_receipts_container').show();
        } else {
            $('#existing_receipts_container').hide();
        }

        $('#expenseModalTitle').text('Edit Expense');
        $('#expenseModal').modal('show');
    });

    // View Button
    $('#expensesTable tbody').on('click', '.view-btn', function() {
        let data = expensesTable.row($(this).parents('tr')).data();
        $('#v_date').text(data.expense_date);
        $('#v_category').text(data.category ? data.category.name : 'N/A');
        $('#v_amount').text('₹' + parseFloat(data.amount).toFixed(2));
        $('#v_payment').text(data.payment_method || '-');
        $('#v_location').text(data.location || '-');
        $('#v_status').text(data.status);
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

    // Delete Button
    $('#expensesTable tbody').on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        let destroyRoute = "{{ route('finance.expenses.destroy', ['box_slug' => ':slug', 'expense' => ':id']) }}";
        if (confirm('Are you sure you want to delete this expense?')) {
            $.ajax({
                url: destroyRoute.replace(':slug', boxSlug).replace(':id', id),
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(res) { if (res.success) expensesTable.ajax.reload(); }
            });
        }
    });

    // Reset Form on close
    $('#expenseModal').on('hidden.bs.modal', function () {
        $('#expenseForm')[0].reset();
        $('#expense_id').val('');
        $('#existing_receipts_container').hide();
        // Remove hidden inputs for deleted receipts
        $('input[name="remove_receipts[]"]').remove();
    });
});

function openAddModal() {
    $('#expenseForm')[0].reset();
    $('#expense_id').val('');
    $('#existing_receipts_container').hide();
    $('#expenseModalTitle').text('Submit New Expense');
    $('#expenseModal').modal('show');
}

function removeReceipt(id) {
    if (confirm('Mark this receipt for deletion upon save?')) {
        $('#expenseForm').append(`<input type="hidden" name="remove_receipts[]" value="${id}">`);
        $(`#receipt_box_${id}`).hide();
    }
}
</script>
@endpush
@endsection
