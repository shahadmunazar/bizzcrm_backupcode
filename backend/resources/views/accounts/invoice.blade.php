@extends('layouts.main')
@section('title', 'Create Invoice')
@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <h4 class="page-title">Create New Invoice</h4>
                        </div>
                        <div class="col-8 text-end">
                            <a href="{{ box_route('accounts.invoices') }}" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-2"></i> Back to Invoices
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="card">
                    <div class="card-body">
                        <form id="create_invoice_form" action="{{ box_route('accounts.invoices.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Client (Company) <span class="text-danger">*</span></label>
                                    <select name="company_id" class="form-control select2" required>
                                        <option value="">Select Company</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->bill_name ?? $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Project (Lead) <span class="text-danger">*</span></label>
                                    <select name="lead_id" class="form-control select2" required>
                                        <option value="">Select Lead</option>
                                        @foreach($leads as $lead)
                                            <option value="{{ $lead->id }}">{{ $lead->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Invoice Date <span class="text-danger">*</span></label>
                                    <div class="icon-form">
                                        <span class="form-icon"><i class="ti ti-calendar-check"></i></span>
                                        <input type="date" name="invoice_date" class="form-control" required value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Due Date <span class="text-danger">*</span></label>
                                    <div class="icon-form">
                                        <span class="form-icon"><i class="ti ti-calendar-check"></i></span>
                                        <input type="date" name="due_date" class="form-control" required value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Payment Method</label>
                                    <select name="payment_option" class="form-control select2">
                                        <option value="Credit Card">Credit Card</option>
                                        <option value="Paypal">Paypal</option>
                                        <option value="Debit Card">Debit Card</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Status</label>
                                    <select name="status" class="form-control select2">
                                        <option value="Pending">Pending</option>
                                        <option value="Paid">Paid</option>
                                        <option value="Unpaid">Unpaid</option>
                                        <option value="Overdue">Overdue</option>
                                    </select>
                                </div>

                            </div>
                            
                            <!-- Line Items -->
                            <h5 class="mt-4 mb-3">Line Items</h5>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-view" id="invoice-items-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Package / Item</th>
                                            <th style="width: 100px;">Quantity</th>
                                            <th style="width: 150px;">Price</th>
                                            <th style="width: 150px;">Discount (%)</th>
                                            <th style="width: 150px;">Tax (%)</th>
                                            <th style="width: 150px;">Amount</th>
                                            <th style="width: 50px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="line-items-body">
                                        <tr class="line-item-row">
                                            <td>
                                                <input type="text" name="items[0][package_id]" class="form-control" placeholder="Item Name or ID" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][qty]" class="form-control item-qty" value="1" min="1" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][amount]" class="form-control item-price" value="0.00" step="0.01" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][discount]" class="form-control item-discount" value="0" step="0.01">
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][tax]" class="form-control item-tax" value="0" step="0.01">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control item-total" value="0.00" readonly>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm remove-item"><i class="ti ti-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" id="add-item-btn" class="btn btn-primary mt-2">
                                    <i class="ti ti-plus me-1"></i> Add Item
                                </button>
                            </div>

                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <label class="col-form-label">Notes</label>
                                        <textarea name="remarks" class="form-control" rows="4"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="col-form-label">Terms & Conditions</label>
                                        <textarea name="terms_conditions" class="form-control" rows="4"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="card bg-light p-3 border">
                                        <div class="d-flex justify-content-between mb-2">
                                            <h6 class="mb-0">Subtotal:</h6>
                                            <span id="summary-subtotal">$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <h6 class="mb-0">Discount:</h6>
                                            <span id="summary-discount">-$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <h6 class="mb-0">Tax:</h6>
                                            <span id="summary-tax">+$0.00</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between mb-0">
                                            <h5 class="mb-0 fw-bold">Total Amount:</h5>
                                            <h5 class="mb-0 fw-bold" id="summary-total">$0.00</h5>
                                            <input type="hidden" name="amount" id="form-subtotal">
                                            <input type="hidden" name="amount_with_tax" id="form-total">
                                            <input type="hidden" name="tax_amount" id="form-tax">
                                            <input type="hidden" name="discount" id="form-discount">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-end mt-4">
                                <a href="{{ box_route('accounts.invoices') }}" class="btn btn-light me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Create Invoice</button>
                            </div>

                        </form>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let itemIndex = 1;

        function calculateTotals() {
            let subtotal = 0;
            let totalDiscount = 0;
            let totalTax = 0;
            let grandTotal = 0;

            $('.line-item-row').each(function() {
                let qty = parseFloat($(this).find('.item-qty').val()) || 0;
                let price = parseFloat($(this).find('.item-price').val()) || 0;
                let discountPct = parseFloat($(this).find('.item-discount').val()) || 0;
                let taxPct = parseFloat($(this).find('.item-tax').val()) || 0;

                let rowAmount = qty * price;
                let rowDiscount = rowAmount * (discountPct / 100);
                let rowAfterDiscount = rowAmount - rowDiscount;
                let rowTax = rowAfterDiscount * (taxPct / 100);
                let rowTotal = rowAfterDiscount + rowTax;

                $(this).find('.item-total').val(rowTotal.toFixed(2));

                subtotal += rowAmount;
                totalDiscount += rowDiscount;
                totalTax += rowTax;
                grandTotal += rowTotal;
            });

            $('#summary-subtotal').text('$' + subtotal.toFixed(2));
            $('#summary-discount').text('-$' + totalDiscount.toFixed(2));
            $('#summary-tax').text('+$' + totalTax.toFixed(2));
            $('#summary-total').text('$' + grandTotal.toFixed(2));

            $('#form-subtotal').val(subtotal.toFixed(2));
            $('#form-discount').val(totalDiscount.toFixed(2));
            $('#form-tax').val(totalTax.toFixed(2));
            $('#form-total').val(grandTotal.toFixed(2));
        }

        $('#add-item-btn').click(function() {
            let row = `
                <tr class="line-item-row">
                    <td><input type="text" name="items[${itemIndex}][package_id]" class="form-control" required></td>
                    <td><input type="number" name="items[${itemIndex}][qty]" class="form-control item-qty" value="1" min="1" required></td>
                    <td><input type="number" name="items[${itemIndex}][amount]" class="form-control item-price" value="0.00" step="0.01" required></td>
                    <td><input type="number" name="items[${itemIndex}][discount]" class="form-control item-discount" value="0" step="0.01"></td>
                    <td><input type="number" name="items[${itemIndex}][tax]" class="form-control item-tax" value="0" step="0.01"></td>
                    <td><input type="text" class="form-control item-total" value="0.00" readonly></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-item"><i class="ti ti-trash"></i></button>
                    </td>
                </tr>
            `;
            $('#line-items-body').append(row);
            itemIndex++;
            calculateTotals();
        });

        $(document).on('click', '.remove-item', function() {
            if ($('.line-item-row').length > 1) {
                $(this).closest('.line-item-row').remove();
                calculateTotals();
            } else {
                alert("You must have at least one line item.");
            }
        });

        $(document).on('input', '.item-qty, .item-price, .item-discount, .item-tax', function() {
            calculateTotals();
        });
        
        calculateTotals();
    });
</script>
@endsection
