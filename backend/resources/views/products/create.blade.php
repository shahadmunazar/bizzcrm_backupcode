@extends('layouts.main')
@section('title', 'Add Product')
@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm-12">
                    <h3 class="page-title">Add New Product</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ box_route('accounts.products.index') }}">Products</a></li>
                        <li class="breadcrumb-item active">Add Product</li>
                    </ul>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">NEW PRODUCT</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ box_route('accounts.products.store') }}" method="POST">
                            @csrf
                            
                            <!-- Block 1: Basic Info & Categorization -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="col-form-label py-0">STATUS</label>
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="CONTINUE">CONTINUE</option>
                                            <option value="DISCONTINUE">DISCONTINUE</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="col-form-label py-0">TYPE</label>
                                        <select name="type" class="form-select form-select-sm">
                                            <option value="NORMAL">NORMAL</option>
                                            <option value="SPECIAL">SPECIAL</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="col-form-label py-0">HIDE</label>
                                        <select name="hide" class="form-select form-select-sm">
                                            <option value="0">NO</option>
                                            <option value="1">YES</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="col-form-label py-0">CODE</label>
                                        <input type="text" name="code" class="form-control form-control-sm" value="14428">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="col-form-label py-0">PRODUCT</label>
                                        <input type="text" name="product_name" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="col-form-label py-0">PACKING</label>
                                        <input type="text" name="packing" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3"></div>

                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="col-form-label py-0">UNIT</label>
                                        <input type="text" name="unit" class="form-control form-control-sm" value="PCS">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="col-form-label py-0">DECIMAL</label>
                                        <select name="decimal" class="form-select form-select-sm">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6"></div>

                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="col-form-label py-0">FAST SEARCH</label>
                                        <input type="text" name="fast_search" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="col-form-label py-0">COLOR TYPE</label>
                                        <select name="color_type" class="form-select form-select-sm">
                                            <option value="NORMAL">NORMAL</option>
                                            <option value="BLUE">BLUE</option>
                                            <option value="ORANGE">ORANGE</option>
                                            <option value="PINK">PINK</option>
                                            <option value="GREEN">GREEN</option>
                                            <option value="RED">RED</option>
                                            <option value="WHITE">WHITE</option>
                                            <option value="YELLOW">YELLOW</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="col-form-label py-0">ITEM TYPE</label>
                                        <select name="item_type" class="form-select form-select-sm">
                                            <option value="1 NORMAL">1 NORMAL</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <div class="row align-items-end mb-2">
                                        <div class="col-md-2"><label class="col-form-label py-0">COMPANY</label></div>
                                        <div class="col-md-2"><select name="company_status" class="form-select form-select-sm"><option value="Yes">Yes</option><option value="No">No</option></select></div>
                                        <div class="col-md-7">
                                            <select name="company" id="company_select" class="form-select form-select-sm">
                                                <option value="">Select Company</option>
                                                @foreach($companies as $company)
                                                    <option value="{{ $company->bill_name }}">{{ $company->bill_name }}</option>
                                                @endforeach
                                                <option value="ADD_NEW" class="fw-bold text-primary">+ Add New Company</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1"><button type="button" class="btn btn-sm btn-primary py-0 px-2 w-100" data-bs-toggle="modal" data-bs-target="#addCompanyModal">+</button></div>
                                    </div>
                                    <div class="row align-items-end mb-2">
                                        <div class="col-md-2"><label class="col-form-label py-0">SALT</label></div>
                                        <div class="col-md-2"><select name="salt_status" class="form-select form-select-sm"><option value="Yes">Yes</option><option value="No">No</option></select></div>
                                        <div class="col-md-7">
                                            <select name="salt" id="salt_select" class="form-select form-select-sm">
                                                <option value="">Select Salt</option>
                                                @foreach($salts as $salt)
                                                    <option value="{{ $salt->name }}">{{ $salt->name }}</option>
                                                @endforeach
                                                <option value="ADD_NEW" class="fw-bold text-primary">+ Add New Salt</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1"><button type="button" class="btn btn-sm btn-primary py-0 px-2 w-100" data-bs-toggle="modal" data-bs-target="#addSaltModal">+</button></div>
                                    </div>
                                    <div class="row align-items-end mb-2">
                                        <div class="col-md-2"><label class="col-form-label py-0">HSN/SAC</label></div>
                                        <div class="col-md-2"><select name="hsn_status" class="form-select form-select-sm"><option value="Yes">Yes</option><option value="No">No</option></select></div>
                                        <div class="col-md-7">
                                            <select name="hsn_sac" id="hsn_select" class="form-select form-select-sm">
                                                <option value="">Select HSN/SAC</option>
                                                @foreach($hsns as $hsn)
                                                    <option value="{{ $hsn->code }}">{{ $hsn->code }}</option>
                                                @endforeach
                                                <option value="ADD_NEW" class="fw-bold text-primary">+ Add New HSN/SAC</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1"><button type="button" class="btn btn-sm btn-primary py-0 px-2 w-100" data-bs-toggle="modal" data-bs-target="#createHsnModal">+</button></div>
                                    </div>
                                    <div class="row align-items-end mb-2">
                                        <div class="col-md-2"><label class="col-form-label py-0">ERP TO ERP</label></div>
                                        <div class="col-md-10"><input type="text" name="erp_to_erp" class="form-control form-control-sm"></div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Block 2: 3 Columns Grid (Taxes, Quantities/Rates, Discounts) -->
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-4 border-end">
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>LOCAL</label></div><div class="col-7"><select name="local_tax" class="form-select form-select-sm"><option value="Taxable">Taxable</option></select></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>CENTRAL</label></div><div class="col-7"><select name="central_tax" class="form-select form-select-sm"><option value="Taxable">Taxable</option></select></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>C.S.R.</label></div><div class="col-7"><input type="text" step="0.01" name="csr" class="form-control form-control-sm text-end" value="0.00"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>M.R.P.</label></div><div class="col-7"><input type="text" step="0.01" name="mrp" class="form-control form-control-sm text-end" value="0.00"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>Rate-A</label></div><div class="col-7"><input type="text" step="0.01" name="rate_a" class="form-control form-control-sm text-end" value="0.00"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>CONV.BOX</label></div><div class="col-7"><input type="text" step="0.001" name="conv_box" class="form-control form-control-sm text-end" value="0.000"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>MINIMUM QTY.</label></div><div class="col-7"><input type="text" name="minimum_qty" class="form-control form-control-sm text-end" value="0"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>VOLUME DISCOUNT</label></div><div class="col-7"><input type="text" step="0.001" name="volume_discount" class="form-control form-control-sm text-end" value="0.000"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>ITEM DISCOUNT</label></div><div class="col-7"><input type="text" step="0.01" name="item_discount" class="form-control form-control-sm text-end" value="0.00"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>MAX DISCOUNT %</label></div><div class="col-7"><input type="text" step="0.01" name="maximum_discount_percent" class="form-control form-control-sm text-end" value="0.00"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>FREE SCHEME</label></div><div class="col-7"><input type="text" name="free_scheme" class="form-control form-control-sm text-end" value="0+0"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>MIN MARGIN %</label></div><div class="col-7"><input type="text" step="0.01" name="minimum_margin_percent" class="form-control form-control-sm text-end"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>NARCOTIC</label></div><div class="col-7"><select name="narcotic" class="form-select form-select-sm"><option value="0">No</option><option value="1">Yes</option></select></div></div>
                                </div>

                                <!-- Middle Column -->
                                <div class="col-md-4 border-end">
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>SGST %</label></div><div class="col-7"><input type="text" step="0.01" name="sgst_percent" class="form-control form-control-sm text-end" value="2.50" readonly></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>IGST %</label></div><div class="col-7"><input type="text" step="0.01" name="igst_percent" class="form-control form-control-sm text-end" value="5.00" readonly></div></div>
                                    <div class="row mb-1 align-items-center mt-4"><div class="col-5"><label>P.RATE</label></div><div class="col-7"><input type="text" step="0.01" name="purchase_rate" class="form-control form-control-sm text-end" value="0.00"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>Rate-B</label></div><div class="col-7"><input type="text" step="0.01" name="rate_b" class="form-control form-control-sm text-end" value="0.00"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>CONV.CAS</label></div><div class="col-7"><input type="text" step="0.001" name="conv_cas" class="form-control form-control-sm text-end" value="0.000"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>MAXIMUM QTY.</label></div><div class="col-7"><input type="text" name="maximum_qty" class="form-control form-control-sm text-end" value="0"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>On Qty.</label></div><div class="col-7"><input type="text" step="0.1" name="on_qty_volume" class="form-control form-control-sm text-end" value="0.0"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>SPECIAL DISC.</label></div><div class="col-7"><input type="text" step="0.01" name="special_disc" class="form-control form-control-sm text-end" value="0.00"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>PURC.Disc.</label></div><div class="col-7"><input type="text" step="0.01" name="purchase_disc" class="form-control form-control-sm text-end" value="0.00"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>VALID FROM</label></div><div class="col-7"><select name="valid_from" class="form-select form-select-sm"><option value="Full Scheme">Full Scheme</option></select></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>DISC. LESS</label></div><div class="col-7"><input type="text" step="0.01" name="disc_less" class="form-control form-control-sm text-end" value="0.00"></div></div>
                                    <div class="row mb-1 align-items-center mt-4"><div class="col-5"><label>SCHEDULE H</label></div><div class="col-7"><select name="schedule_h" class="form-select form-select-sm"><option value="0">No</option><option value="1">Yes</option></select></div></div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-4">
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>CGST %</label></div><div class="col-7"><input type="text" step="0.01" name="cgst_percent" class="form-control form-control-sm text-end" value="2.50" readonly></div></div>
                                    <div class="row mb-1 align-items-center">
                                        <div class="col-3"><label>CESS% A</label></div>
                                        <div class="col-4 pe-1"><input type="text" step="0.01" name="cess_percent" class="form-control form-control-sm text-end" value="0.00" readonly></div>
                                        <div class="col-1 p-0 text-center">+</div>
                                        <div class="col-4 ps-1"><input type="text" step="0.001" name="cess_amount" class="form-control form-control-sm text-end" value="0.000"></div>
                                    </div>
                                    <div class="row mb-1 align-items-center mt-4"><div class="col-5"><label>COST/</label></div><div class="col-7"><input type="text" step="0.00001" name="cost" class="form-control form-control-sm text-end" value="0.00000"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>Rate-C</label></div><div class="col-7"><input type="text" step="0.01" name="rate_c" class="form-control form-control-sm text-end" value="0.00"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>NEGATIVE</label></div><div class="col-7"><select name="negative_stock" class="form-select form-select-sm"><option value="0">No</option><option value="1">Yes</option></select></div></div>
                                    <div class="row mb-1 align-items-center">
                                        <div class="col-5"><label>REORDER QTY.</label></div>
                                        <div class="col-4 pe-1"><input type="text" name="reorder_days" class="form-control form-control-sm text-end" value="0" placeholder="DAYS"></div>
                                        <div class="col-3 ps-1"><input type="text" name="reorder_qty" class="form-control form-control-sm text-end" value="0"></div>
                                    </div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>DISCOUNT</label></div><div class="col-7"><select name="discount_type" class="form-select form-select-sm"><option value="Applicable">Applicable</option></select></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>ON QUANTITY</label></div><div class="col-7"><input type="text" name="on_quantity_disc" class="form-control form-control-sm text-end" value="0"></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>F6/RATE±</label></div><div class="col-7"><select name="f6_rate_plus_minus" class="form-select form-select-sm"><option value="YES">YES</option></select></div></div>
                                    <div class="row mb-1 align-items-center"><div class="col-5"><label>TO</label></div><div class="col-7"></div></div>
                                    <div class="row mb-1 align-items-center mt-4"><div class="col-5"><label>Manufactur F3</label></div><div class="col-7"><input type="text" name="manufacturer" class="form-control form-control-sm text-end"></div></div>
                                    <div class="row mb-1 align-items-center">
                                        <div class="col-4"><label>SCHEDULE H1</label></div>
                                        <div class="col-3 pe-1"><select name="schedule_h1" class="form-select form-select-sm"><option value="0">No</option><option value="1">Yes</option></select></div>
                                        <div class="col-5 ps-1"><select name="schedule_h1_type" class="form-select form-select-sm"><option value="Normal">Normal</option></select></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 text-center">
                                <button type="submit" class="btn btn-primary px-5">Save Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Salt Modal -->
<div class="modal fade" id="addSaltModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addSaltForm">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add Salt</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Salt Name</label>
            <input type="text" name="name" class="form-control">
          </div>
          <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-select">
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add HSN Modal -->
<!-- Include Generic Add HSN Modal -->
@include('partials.add_hsn_modal')

<style>
    .col-form-label { font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .form-control-sm, .form-select-sm { font-size: 12px; }
    .card-body label { font-size: 11px; font-weight: 600; text-transform: uppercase; padding-top: 4px; margin-bottom: 0;}
    .border-end { border-right: 1px solid #dee2e6 !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function handleModalSubmit(formId, modalId, selectId, url) {
        const form = document.getElementById(formId);
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById(selectId);
                    // Remove ADD_NEW option if exists, append new, then append ADD_NEW back
                    const addNewOpt = Array.from(select.options).find(opt => opt.value === 'ADD_NEW');
                    if (addNewOpt) select.removeChild(addNewOpt);
                    
                    const newOption = new Option(data.name || data.code, data.id, true, true);
                    select.add(newOption);
                    
                    if (addNewOpt) select.add(addNewOpt);
                    
                    const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                    modal.hide();
                    form.reset();
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }
    handleModalSubmit('addSaltForm', 'addSaltModal', 'salt_select', '{{ box_route("accounts.products.attributes.salt") }}');

    // Fetch HSN details on select
    $('#hsn_select').on('change', function() {
        var code = $(this).val();
        if (code && code !== 'ADD_NEW') {
            var url = '{{ box_route("accounts.product_hsns.get_details", ":code") }}'.replace(':code', code);
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        $('input[name="sgst_percent"]').val(response.data.sgst);
                        $('input[name="cgst_percent"]').val(response.data.cgst);
                        $('input[name="igst_percent"]').val(response.data.igst);
                        $('input[name="cess_percent"]').val(response.data.cess);
                    }
                }
            });
        } else {
            // Reset if empty or ADD_NEW
            $('input[name="sgst_percent"]').val('0.00');
            $('input[name="cgst_percent"]').val('0.00');
            $('input[name="igst_percent"]').val('0.00');
            $('input[name="cess_percent"]').val('0.00');
        }
    });

    const setupSelectModalTrigger = (selectId, statusName, createModalId, type, title) => {
        const $mainSelect = $('#' + selectId);
        const $statusSelect = $(`select[name="${statusName}"]`);
        
        $mainSelect.on('change', function() {
            if ($(this).val() === 'ADD_NEW') {
                $(this).val('').trigger('change.select2'); // Reset selection
                var modal = new bootstrap.Modal(document.getElementById(createModalId));
                modal.show();
            }
        });

        if ($statusSelect.length) {
            $statusSelect.on('change', function() {
                const $selectCol = $mainSelect.closest('.col-md-7');
                const $btnCol = $selectCol.next('.col-md-1');
                if ($(this).val() === 'No') {
                    $selectCol.hide();
                    $btnCol.hide();
                    // Show Selection Modal automatically
                    if (typeof window.openSelectionModal === 'function') {
                        window.openSelectionModal({
                            type: type,
                            title: title,
                            selectId: selectId,
                            statusName: statusName,
                            createModalId: createModalId
                        });
                    }
                } else {
                    $selectCol.show();
                    $btnCol.show();
                }
            });
            // trigger on load
            const $selectCol = $mainSelect.closest('.col-md-7');
            const $btnCol = $selectCol.next('.col-md-1');
            if ($statusSelect.val() === 'No') {
                $selectCol.hide();
                $btnCol.hide();
            } else {
                $selectCol.show();
                $btnCol.show();
            }
        }
    };
    setupSelectModalTrigger('salt_select', 'salt_status', 'addSaltModal', 'salt', 'Select Product Salt');
    setupSelectModalTrigger('hsn_select', 'hsn_status', 'createHsnModal', 'hsn', 'Select Product HSN');
    setupSelectModalTrigger('company_select', 'company_status', 'addCompanyModal', 'company', 'Select Company');

    // Initialize Select2 for searchable dropdowns
    $('#company_select, #salt_select, #hsn_select').select2({
        width: '100%'
    });

    $(document).on("company:added", function(e, company) {
        var newOption = new Option(company.bill_name, company.bill_name, true, true);
        $("#company_select").append(newOption).trigger("change");
    });
});
</script>
	@include("partials.selection_modal")
	@include("partials.add_company_modal")
	@include("partials.add_company_scripts")
@endsection
