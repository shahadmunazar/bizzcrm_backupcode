@push('scripts')
<script>
$(document).ready(function() {
    
    // Function to fetch and reload invoices
    function loadInvoices() {
        $.ajax({
            url: "{{ box_route('accounts.ajax_invoices') }}",
            type: 'GET',
            data: { company_id: '{{ $company_id ?? "" }}' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var baseUrl = "{{ url(request()->segment(1)) }}";
                
                // Destroy existing DataTable instance and clean up placeholders
                    if ($.fn.DataTable.isDataTable('.datatable')) {
                        $('.datatable').DataTable().destroy();
                        $('.datatable-paginate').empty();
                        $('.datatable-length').empty();
                    }
                    
                    var tbody = $('#invoices-table-body');
                    tbody.empty();
                    
                    if (response.data.length > 0) {
                        $.each(response.data, function(index, invoice) {
                            // Format dates
                            var dateObj = new Date(invoice.invoice_date);
                            var dateStr = dateObj.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                            
                            var dueDateStr = '';
                            if (invoice.due_date) {
                                var dueObj = new Date(invoice.due_date);
                                dueDateStr = dueObj.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                            } else {
                                dueDateStr = '-';
                            }
                            
                            // Status badge logic
                            var statusBadge = '';
                            if (invoice.status === 'Approved') {
                                statusBadge = '<span class="badge badge-pill badge-status bg-success">Approved</span>';
                            } else if (invoice.status === 'DisApproved') {
                                statusBadge = '<span class="badge badge-pill badge-status bg-danger">DisApproved</span>';
                            } else {
                                statusBadge = '<span class="badge badge-pill badge-status bg-warning">' + (invoice.status || 'Pending') + '</span>';
                            }
                            
                            // Store full invoice data as JSON for the edit modal
                            var invoiceData = JSON.stringify(invoice).replace(/'/g, "&apos;").replace(/"/g, "&quot;");
                            
                            var productNames = '-';
                            if (invoice.items && invoice.items.length > 0) {
                                var namesArray = invoice.items.map(function(item) {
                                    return item.product_name;
                                });
                                productNames = namesArray.join(', ');
                                if (productNames.length > 40) {
                                    productNames = '<span title="' + productNames + '">' + productNames.substring(0, 40) + '...</span>';
                                }
                            }
                            
                            var tr = `
                                <tr>
                                    <td><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
                                    <td><div class="set-star rating-select"><i class="fa fa-star"></i></div></td>
                                    <td><a href="#" class="title-name">${invoice.invoice_no ? invoice.invoice_no : ('#' + invoice.id)}</a></td>
                                    <td>
                                        <h2 class="d-flex align-items-center"><a href="#" class="avatar avatar-sm border me-2"><img class="w-auto h-auto" src="{{ asset('assets/img/icons/company-icon-01.svg') }}" alt="Company Image"></a><a href="#" class="d-flex flex-column">${invoice.company_name}</a></h2>
                                    </td>
                                    <td>
                                        <h2 class="d-flex align-items-center"><a href="#" class="avatar avatar-sm border me-2"><img class="w-auto h-auto" src="{{ asset('assets/img/priority/project-01.svg') }}" alt="Lead Image"></a><a href="#" class="d-flex flex-column">${invoice.lead_name}</a></h2>
                                    </td>
                                    <td>${productNames}</td>
                                    <td>${dateStr}</td>
                                    <td>${dueDateStr}</td>
                                    <td>$${parseFloat(invoice.amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
                                    <td>$${parseFloat(invoice.paid_amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
                                    <td>$${parseFloat(invoice.balance_amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
                                    <td>${statusBadge}</td>
                                    <td>
                                        <div class="dropdown table-action">
                                            <a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item edit-invoice-btn" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit" data-invoice="${invoiceData}"><i class="ti ti-edit text-blue"></i> Edit</a>
                                                <a class="dropdown-item delete-invoice-btn" href="#" data-bs-toggle="modal" data-bs-target="#delete_invoices" data-id="${invoice.id}"><i class="ti ti-trash text-danger"></i>Delete</a>
                                                <a class="dropdown-item" href="${baseUrl}/invoice-details/${invoice.id}"><i class="ti ti-clipboard-copy text-green"></i> View Invoices</a>
                                                <a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-checks text-success"></i> Mark as Paid</a>
                                                <a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-file text-tertiary"></i> Mark as Partially Paid</a>
                                                <a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-sticker text-blue"></i> Mark as Unpaid</a>
                                                <a class="dropdown-item" href="${baseUrl}/invoice-details/${invoice.id}?print=true"><i class="ti ti-printer text-info"></i> Print</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            tbody.append(tr);
                        });
                    }
                    
                    // Re-initialize DataTable
                    $('.datatable').DataTable({
                        "bFilter": false,
                        "bInfo": false,
                        "autoWidth": true,
                        "ordering": true,
                        "language": {
                            search: ' ',
                            sLengthMenu: '_MENU_',
                            searchPlaceholder: "Search",
                            info: "_START_ - _END_ of _TOTAL_ items",
                            "lengthMenu": "Show _MENU_ entries",
                            paginate: {
                                next: 'Next <i class=" fa fa-angle-right"></i> ',
                                previous: '<i class="fa fa-angle-left"></i> Prev '
                            },
                        },
                        initComplete: (settings, json) => {
                            $('.dataTables_paginate').appendTo('.datatable-paginate');
                            $('.dataTables_length').appendTo('.datatable-length');
                        },
                    });
                }
            },
            error: function() {
                $('#invoices-table-body').html('<tr><td colspan="12" class="text-center text-danger">Error loading invoices.</td></tr>');
            }
        });
    }

    // Initial load
    loadInvoices();

    // Configure toastr
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000",
        };
    }

    // Initialize add form with one empty row
    $('#offcanvas_add_invoice').on('show.bs.offcanvas', function () {
        $('#add-invoice-form')[0].reset();
        $('#add-invoice-items-list').empty();
        $('#add_lead_id').trigger('change');
        addInvoiceItemRow('add');
    });

    // Handle Add Invoice Form Submission
    $('#add-invoice-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var submitBtn = $('#btn-save-invoice');
        var originalText = submitBtn.text();
        
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: "{{ box_route('accounts.invoices.store') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    if (typeof toastr !== 'undefined') toastr.success(response.message);
                    $('#offcanvas_add_invoice').offcanvas('hide');
                    $('#add-invoice-form')[0].reset();
                    @if(isset($onSuccess)) {!! $onSuccess !!} @else loadInvoices(); @endif
                }
            },
            error: function(xhr) {
                if (typeof toastr !== 'undefined') toastr.error(xhr.responseJSON?.message || 'An error occurred.');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Handle Edit Invoice Button Click (Populate Form)
    $(document).on('click', '.edit-invoice-btn', function() {
        var invoice = $(this).data('invoice');
        
        // Ensure invoice is a parsed object
        if (typeof invoice === 'string') {
            invoice = JSON.parse(invoice);
        }
        
        $('#edit_invoice_id').val(invoice.id);
        
        // This relies on the original object having company_id and lead_id.
        // Wait, getInvoicesData in controller doesn't return company_id right now. Let me fix that.
        // I will assume company_name is enough to find it, but it's better if I just set them to empty 
        // and let the user select. Actually, I will need to update the controller to return company_id and lead_id.
        // For now, I will just fill what I have.
        if (invoice.company_id) {
            if ($('#edit_company_id').find("option[value='" + invoice.company_id + "']").length == 0) {
                var newOption = new Option(invoice.company_name, invoice.company_id, true, true);
                $('#edit_company_id').append(newOption);
            }
            $('#edit_company_id').val(invoice.company_id).trigger('change');
        }
        if (invoice.lead_id) {
            if ($('#edit_lead_id').find("option[value='" + invoice.lead_id + "']").length == 0) {
                var newOption = new Option(invoice.lead_name, invoice.lead_id, true, true);
                $('#edit_lead_id').append(newOption);
            }
            $('#edit_lead_id').val(invoice.lead_id).trigger('change');
        }
        
        $('#edit_amount').val(invoice.amount);
        $('#edit_paid_amount').val(invoice.paid_amount || 0);
        $('#edit_balance_amount').val(invoice.balance_amount || 0);
        if (invoice.currency) $('#edit_currency').val(invoice.currency).trigger('change');
        
        // Format invoice date for input
        if (invoice.invoice_date) {
            var dateObj = new Date(invoice.invoice_date);
            var dateStr = dateObj.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
            $('#edit_invoice_date').val(dateStr);
        }
        
        // Format due date for input
        if (invoice.due_date) {
            var dueObj = new Date(invoice.due_date);
            var dueDateStr = dueObj.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
            $('#edit_due_date').val(dueDateStr);
        } else {
            $('#edit_due_date').val('');
        }
        
        if (invoice.payment_method) $('#edit_payment_method').val(invoice.payment_method).trigger('change');
        if (invoice.status) $('#edit_status').val(invoice.status).trigger('change');
        if (invoice.remarks) $('#edit_remarks').val(invoice.remarks);
        
        $('#edit-invoice-items-list').empty();
        // Note: itemIndex is global, we can just let it increment.
        if (invoice.items && invoice.items.length > 0) {
            invoice.items.forEach(function(item) {
                addInvoiceItemRow('edit', {
                    product_id: item.package_id,
                    product_name: item.product_name || 'Product',
                    qty: item.qty,
                    rate: item.msp,
                    amount: item.amount
                });
            });
        }
    });

    // Handle Edit Invoice Form Submission
    $('#edit-invoice-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var invoiceId = $('#edit_invoice_id').val();
        var submitBtn = $('#btn-update-invoice');
        var originalText = submitBtn.text();
        
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
        
        $.ajax({
            url: "{{ box_route('accounts.invoices.update', ['id' => ':id']) }}".replace(':id', invoiceId),
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    if (typeof toastr !== 'undefined') toastr.success(response.message);
                    $('#offcanvas_edit').offcanvas('hide');
                    loadInvoices(); // Reload table
                }
            },
            error: function(xhr) {
                if (typeof toastr !== 'undefined') toastr.error(xhr.responseJSON?.message || 'An error occurred.');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Handle Delete Button Click (Set ID)
    $(document).on('click', '.delete-invoice-btn', function() {
        var invoiceId = $(this).data('id');
        $('#delete_invoice_id').val(invoiceId);
    });

    // Handle Confirm Delete
    $('#btn-confirm-delete').on('click', function() {
        var invoiceId = $('#delete_invoice_id').val();
        var submitBtn = $(this);
        var originalText = submitBtn.text();
        
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');
        
        $.ajax({
            url: "{{ box_route('accounts.invoices.destroy', ['id' => ':id']) }}".replace(':id', invoiceId),
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    if (typeof toastr !== 'undefined') toastr.success(response.message);
                    $('#delete_invoices').modal('hide');
                    loadInvoices(); // Reload table
                }
            },
            error: function(xhr) {
                if (typeof toastr !== 'undefined') toastr.error(xhr.responseJSON?.message || 'An error occurred.');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Invoice Items Logic
    var itemIndex = 0;
    
    window.addInvoiceItemRow = function(prefix, data = null) {
        var listContainer = $('#' + prefix + '-invoice-items-list');
        var idx = itemIndex++;
        var rowHtml = `
            <div class="row gx-3 mb-4 pb-3 border-bottom item-row align-items-start" id="item-row-${idx}">
                <div class="col-auto text-center" style="width: 30px; padding-top: 25px;">
                    <i class="ti ti-x text-danger fs-6 cursor-pointer" onclick="$('#item-row-${idx}').remove(); calculateTotal('${prefix}');"></i>
                </div>
                <div class="col-md-4">
                    <label style="font-size: 11px; font-weight: 700; color: #e02a11; margin-bottom: 5px;">Package <span class="text-danger">*</span></label>
                    <select class="form-control item-product-select" name="products[${idx}][product_id]" required>
                        ${data && data.product_id ? `<option value="${data.product_id}" selected>${data.product_name}</option>` : `<option value="">Search Product...</option>`}
                    </select>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size: 11px; font-weight: 600;">IGST</button>
                    </div>
                </div>
                <div class="col">
                    <label style="font-size: 11px; font-weight: 700; color: #888; margin-bottom: 5px;">MRP</label>
                    <input type="number" step="0.01" class="form-control" value="0" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; padding-left: 0; padding-right: 0; background: transparent;" readonly>
                </div>
                <div class="col">
                    <label style="font-size: 11px; font-weight: 700; color: #888; margin-bottom: 5px;">MSP <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control item-rate" name="products[${idx}][rate]" value="${data ? data.rate : 0}" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; padding-left: 0; padding-right: 0; background: transparent;" required>
                </div>
                <div class="col">
                    <label style="font-size: 11px; font-weight: 700; color: #888; margin-bottom: 5px;">Qty <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="1" class="form-control item-qty" name="products[${idx}][qty]" value="${data ? data.qty : 1}" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; padding-left: 0; padding-right: 0; background: transparent;" required>
                </div>
                <div class="col">
                    <label style="font-size: 11px; font-weight: 700; color: #888; margin-bottom: 5px;">Total</label>
                    <input type="text" class="form-control item-amount" name="products[${idx}][amount]" value="${data ? data.amount : 0}" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; padding-left: 0; padding-right: 0; background: transparent;" readonly>
                </div>
            </div>
        `;
        listContainer.append(rowHtml);
        
        var newRow = $('#item-row-' + idx);
        var select = newRow.find('.item-product-select');
        var parent = prefix === 'add' ? $('#offcanvas_add_invoice') : $('#offcanvas_' + prefix);
        
        select.select2({
            dropdownParent: parent,
            placeholder: 'Search Product...',
            ajax: {
                url: "{{ box_route('accounts.ajax_products') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term, page: params.page };
                },
                processResults: function (res, params) {
                    params.page = params.page || 1;
                    return {
                        results: res.results,
                        pagination: { more: res.pagination.more }
                    };
                },
                cache: true
            }
        });
        
        if (data && data.product_id) {
            var newOption = new Option(data.product_name, data.product_id, true, true);
            select.append(newOption).trigger('change');
        }
        
        select.on('select2:select', function(e) {
            var selectedData = e.params.data;
            if (selectedData.rate) {
                newRow.find('.item-rate').val(selectedData.rate);
                calculateRowAmount(newRow, prefix);
            }
        });
        
        newRow.find('.item-qty, .item-rate').on('input', function() {
            calculateRowAmount(newRow, prefix);
        });
    };

    function calculateRowAmount(row, prefix) {
        var qty = parseFloat(row.find('.item-qty').val()) || 0;
        var rate = parseFloat(row.find('.item-rate').val()) || 0;
        var amt = qty * rate;
        row.find('.item-amount').val(amt.toFixed(2));
        calculateTotal(prefix);
    }
    
    window.calculateTotal = function(prefix) {
        var total = 0;
        $('#' + prefix + '-invoice-items-table .item-amount').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#' + prefix + '_amount').val(total.toFixed(2));
        calculateBalance(prefix);
    };

    // Auto-calculate balance amounts
    function calculateBalance(prefix) {
        var total = parseFloat($('#' + prefix + '_amount').val()) || 0;
        var paid = parseFloat($('#' + prefix + '_paid_amount').val()) || 0;
        $('#' + prefix + '_balance_amount').val((total - paid).toFixed(2));
    }
    
    $('#add_amount, #add_paid_amount').on('input', function() { calculateBalance('add'); });
    $('#edit_amount, #edit_paid_amount').on('input', function() { calculateBalance('edit'); });

});

    
    function initAjaxSelects() {
        $('.custom-ajax-select').each(function() {
            var $this = $(this);
            var isClient = $this.attr('id').indexOf('company') !== -1;
            var parent = $this.closest('.offcanvas');
            
            $this.select2({
                dropdownParent: parent,
                placeholder: isClient ? 'Choose Client' : 'Choose Project',
                ajax: {
                    url: isClient ? "{{ box_route('accounts.ajax_clients') }}" : "{{ box_route('accounts.ajax_leads') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                }
            });
        });
    }

    
    $(document).ready(function() {
        initAjaxSelects();
    });
    
</script>
@endpush
