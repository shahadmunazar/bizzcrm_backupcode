@extends('layouts.main')
@section('title', 'Products')
@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Products</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ box_route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ul>
                </div>
                <div class="col-auto float-end ms-auto">
                    <button type="button" class="btn btn-danger me-2 d-none" id="bulkDeleteBtn">
                        <i class="fa fa-trash"></i> Bulk Delete
                    </button>
                    <a href="javascript:void(0);" class="btn add-btn me-2" data-bs-toggle="modal" data-bs-target="#manageColumnsModal"><i class="fa fa-columns me-1"></i> Manage Columns</a>
                    <button type="button" class="btn add-btn me-2" data-bs-toggle="modal" data-bs-target="#importProductModal">
                        <i class="fa fa-upload"></i> Import Products
                    </button>
                    <button type="button" class="btn add-btn" id="add-product-btn" data-bs-toggle="modal" data-bs-target="#createProductModal">
                        <i class="fa fa-plus"></i> Add Product
                    </button>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive" style="overflow-x: auto; white-space: nowrap;">
                            <table class="table table-striped custom-table mb-0 datatable">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAllProduct">
                                        </th>
                                        <th>Status</th>
                                        <th>Type</th>
                                        <th>Hide</th>
                                        <th>Code</th>
                                        <th>Product Name</th>
                                        <th>Packing</th>
                                        <th>Unit</th>
                                        <th>Decimal</th>
                                        <th>Fast Search</th>
                                        <th>Color Type</th>
                                        <th>Item Type</th>
                                        <th>Company</th>
                                        <th>Company Status</th>
                                        <th>Salt</th>
                                        <th>Salt Status</th>
                                        <th>Hsn Sac</th>
                                        <th>Hsn Status</th>
                                        <th>Erp To Erp</th>
                                        <th>Local Tax</th>
                                        <th>Central Tax</th>
                                        <th>Sgst Percent</th>
                                        <th>Cgst Percent</th>
                                        <th>Igst Percent</th>
                                        <th>Cess Percent</th>
                                        <th>Cess Amount</th>
                                        <th>Csr</th>
                                        <th>Mrp</th>
                                        <th>Purchase Rate</th>
                                        <th>Cost</th>
                                        <th>Rate A</th>
                                        <th>Rate B</th>
                                        <th>Rate C</th>
                                        <th>Conv Box</th>
                                        <th>Conv Cas</th>
                                        <th>Negative Stock</th>
                                        <th>Minimum Qty</th>
                                        <th>Maximum Qty</th>
                                        <th>Reorder Qty</th>
                                        <th>Reorder Days</th>
                                        <th>Volume Discount</th>
                                        <th>On Qty Volume</th>
                                        <th>Item Discount</th>
                                        <th>Special Disc</th>
                                        <th>Discount Type</th>
                                        <th>On Quantity Disc</th>
                                        <th>Maximum Discount Percent</th>
                                        <th>Purchase Disc</th>
                                        <th>F6 Rate Plus Minus</th>
                                        <th>Free Scheme</th>
                                        <th>Valid From</th>
                                        <th>Minimum Margin Percent</th>
                                        <th>Disc Less</th>
                                        <th>Manufacturer</th>
                                        <th>Narcotic</th>
                                        <th>Schedule H</th>
                                        <th>Schedule H1</th>
                                        <th>Schedule H1 Type</th>                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                    <tr id="product-row-{{ $product->id }}">
                                        <td>
                                            <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                                        </td>
                                        <td>
                                            <span class="badge bg-inverse-{{ $product->status == 'CONTINUE' ? 'success' : 'danger' }}">{{ $product->status }}</span>
                                        </td>
                                        <td>{{ $product->type }}</td>
                                        <td>{{ $product->hide ? 'Yes' : 'No' }}</td>
                                        <td><span class="badge bg-primary text-white">{{ $product->code }}</span></td>
                                        <td>{{ $product->product_name }}</td>
                                        <td>{{ $product->packing }}</td>
                                        <td>{{ $product->unit }}</td>
                                        <td>{{ $product->decimal ? 'Yes' : 'No' }}</td>
                                        <td>{{ $product->fast_search }}</td>
                                        <td>{{ $product->color_type }}</td>
                                        <td>{{ $product->item_type }}</td>
                                        <td>{{ $product->company }}</td>
                                        <td>{{ $product->company_status }}</td>
                                        <td>{{ $product->salt }}</td>
                                        <td>{{ $product->salt_status }}</td>
                                        <td>{{ $product->hsn_sac }}</td>
                                        <td>{{ $product->hsn_status }}</td>
                                        <td>{{ $product->erp_to_erp }}</td>
                                        <td>{{ $product->local_tax }}</td>
                                        <td>{{ $product->central_tax }}</td>
                                        <td>{{ $product->sgst_percent }}</td>
                                        <td>{{ $product->cgst_percent }}</td>
                                        <td>{{ $product->igst_percent }}</td>
                                        <td>{{ $product->cess_percent }}</td>
                                        <td>{{ $product->cess_amount }}</td>
                                        <td>{{ $product->csr }}</td>
                                        <td>{{ $product->mrp }}</td>
                                        <td>{{ $product->purchase_rate }}</td>
                                        <td>{{ $product->cost }}</td>
                                        <td>{{ $product->rate_a }}</td>
                                        <td>{{ $product->rate_b }}</td>
                                        <td>{{ $product->rate_c }}</td>
                                        <td>{{ $product->conv_box }}</td>
                                        <td>{{ $product->conv_cas }}</td>
                                        <td>{{ $product->negative_stock ? 'Yes' : 'No' }}</td>
                                        <td>{{ $product->minimum_qty }}</td>
                                        <td>{{ $product->maximum_qty }}</td>
                                        <td>{{ $product->reorder_qty }}</td>
                                        <td>{{ $product->reorder_days }}</td>
                                        <td>{{ $product->volume_discount }}</td>
                                        <td>{{ $product->on_qty_volume }}</td>
                                        <td>{{ $product->item_discount }}</td>
                                        <td>{{ $product->special_disc }}</td>
                                        <td>{{ $product->discount_type }}</td>
                                        <td>{{ $product->on_quantity_disc }}</td>
                                        <td>{{ $product->maximum_discount_percent }}</td>
                                        <td>{{ $product->purchase_disc }}</td>
                                        <td>{{ $product->f6_rate_plus_minus }}</td>
                                        <td>{{ $product->free_scheme }}</td>
                                        <td>{{ $product->valid_from }}</td>
                                        <td>{{ $product->minimum_margin_percent }}</td>
                                        <td>{{ $product->disc_less }}</td>
                                        <td>{{ $product->manufacturer }}</td>
                                        <td>{{ $product->narcotic ? 'Yes' : 'No' }}</td>
                                        <td>{{ $product->schedule_h ? 'Yes' : 'No' }}</td>
                                        <td>{{ $product->schedule_h1 ? 'Yes' : 'No' }}</td>
                                        <td>{{ $product->schedule_h1_type }}</td>                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item edit-product-btn" href="{{ box_route('accounts.products.edit', $product->id) }}"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                                    <form action="{{ box_route('accounts.products.destroy', $product->id) }}" method="POST" class="d-inline delete-product-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger"><i class="fa fa-trash-o m-r-5"></i> Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

@include('products.create_modal')

<!-- Bulk Delete Product Modal -->
<div class="modal fade" id="bulkDeleteProductModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1">Bulk Delete Products</h4>
                <p class="mb-3">
                    Are you sure you want to delete <strong id="bulkDeleteCount">0</strong> Product(s)? This action <strong>cannot</strong> be undone.
                </p>
                <div class="d-flex justify-content-center">
                    <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                    <a href="javascript:void(0);" class="btn btn-danger" id="confirmBulkDeleteProductBtn">Yes, Delete them!</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Bulk Delete Product Modal -->

<!-- Manage Columns Modal -->
<div class="modal custom-modal fade" id="manageColumnsModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Columns</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="showAllColumns">Show All</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="hideAllColumns">Hide All</button>
                </div>
                <div class="list-group" id="columnsList">                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="0" id="col_status" checked>
                                        <label class="form-check-label" for="col_status">Status</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="1" id="col_type" checked>
                                        <label class="form-check-label" for="col_type">Type</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="2" id="col_hide" checked>
                                        <label class="form-check-label" for="col_hide">Hide</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="3" id="col_code" checked>
                                        <label class="form-check-label" for="col_code">Code</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="4" id="col_product_name" checked>
                                        <label class="form-check-label" for="col_product_name">Product Name</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="5" id="col_packing" checked>
                                        <label class="form-check-label" for="col_packing">Packing</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="6" id="col_unit" checked>
                                        <label class="form-check-label" for="col_unit">Unit</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="7" id="col_decimal" checked>
                                        <label class="form-check-label" for="col_decimal">Decimal</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="8" id="col_fast_search" checked>
                                        <label class="form-check-label" for="col_fast_search">Fast Search</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="9" id="col_color_type" checked>
                                        <label class="form-check-label" for="col_color_type">Color Type</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="10" id="col_item_type" checked>
                                        <label class="form-check-label" for="col_item_type">Item Type</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="11" id="col_company" checked>
                                        <label class="form-check-label" for="col_company">Company</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="12" id="col_company_status" checked>
                                        <label class="form-check-label" for="col_company_status">Company Status</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="13" id="col_salt" checked>
                                        <label class="form-check-label" for="col_salt">Salt</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="14" id="col_salt_status" checked>
                                        <label class="form-check-label" for="col_salt_status">Salt Status</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="15" id="col_hsn_sac" checked>
                                        <label class="form-check-label" for="col_hsn_sac">Hsn Sac</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="16" id="col_hsn_status" checked>
                                        <label class="form-check-label" for="col_hsn_status">Hsn Status</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="17" id="col_erp_to_erp" checked>
                                        <label class="form-check-label" for="col_erp_to_erp">Erp To Erp</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="18" id="col_local_tax" checked>
                                        <label class="form-check-label" for="col_local_tax">Local Tax</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="19" id="col_central_tax" checked>
                                        <label class="form-check-label" for="col_central_tax">Central Tax</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="20" id="col_sgst_percent" checked>
                                        <label class="form-check-label" for="col_sgst_percent">Sgst Percent</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="21" id="col_cgst_percent" checked>
                                        <label class="form-check-label" for="col_cgst_percent">Cgst Percent</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="22" id="col_igst_percent" checked>
                                        <label class="form-check-label" for="col_igst_percent">Igst Percent</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="23" id="col_cess_percent" checked>
                                        <label class="form-check-label" for="col_cess_percent">Cess Percent</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="24" id="col_cess_amount" checked>
                                        <label class="form-check-label" for="col_cess_amount">Cess Amount</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="25" id="col_csr" checked>
                                        <label class="form-check-label" for="col_csr">Csr</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="26" id="col_mrp" checked>
                                        <label class="form-check-label" for="col_mrp">Mrp</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="27" id="col_purchase_rate" checked>
                                        <label class="form-check-label" for="col_purchase_rate">Purchase Rate</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="28" id="col_cost" checked>
                                        <label class="form-check-label" for="col_cost">Cost</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="29" id="col_rate_a" checked>
                                        <label class="form-check-label" for="col_rate_a">Rate A</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="30" id="col_rate_b" checked>
                                        <label class="form-check-label" for="col_rate_b">Rate B</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="31" id="col_rate_c" checked>
                                        <label class="form-check-label" for="col_rate_c">Rate C</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="32" id="col_conv_box" checked>
                                        <label class="form-check-label" for="col_conv_box">Conv Box</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="33" id="col_conv_cas" checked>
                                        <label class="form-check-label" for="col_conv_cas">Conv Cas</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="34" id="col_negative_stock" checked>
                                        <label class="form-check-label" for="col_negative_stock">Negative Stock</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="35" id="col_minimum_qty" checked>
                                        <label class="form-check-label" for="col_minimum_qty">Minimum Qty</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="36" id="col_maximum_qty" checked>
                                        <label class="form-check-label" for="col_maximum_qty">Maximum Qty</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="37" id="col_reorder_qty" checked>
                                        <label class="form-check-label" for="col_reorder_qty">Reorder Qty</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="38" id="col_reorder_days" checked>
                                        <label class="form-check-label" for="col_reorder_days">Reorder Days</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="39" id="col_volume_discount" checked>
                                        <label class="form-check-label" for="col_volume_discount">Volume Discount</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="40" id="col_on_qty_volume" checked>
                                        <label class="form-check-label" for="col_on_qty_volume">On Qty Volume</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="41" id="col_item_discount" checked>
                                        <label class="form-check-label" for="col_item_discount">Item Discount</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="42" id="col_special_disc" checked>
                                        <label class="form-check-label" for="col_special_disc">Special Disc</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="43" id="col_discount_type" checked>
                                        <label class="form-check-label" for="col_discount_type">Discount Type</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="44" id="col_on_quantity_disc" checked>
                                        <label class="form-check-label" for="col_on_quantity_disc">On Quantity Disc</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="45" id="col_maximum_discount_percent" checked>
                                        <label class="form-check-label" for="col_maximum_discount_percent">Maximum Discount Percent</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="46" id="col_purchase_disc" checked>
                                        <label class="form-check-label" for="col_purchase_disc">Purchase Disc</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="47" id="col_f6_rate_plus_minus" checked>
                                        <label class="form-check-label" for="col_f6_rate_plus_minus">F6 Rate Plus Minus</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="48" id="col_free_scheme" checked>
                                        <label class="form-check-label" for="col_free_scheme">Free Scheme</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="49" id="col_valid_from" checked>
                                        <label class="form-check-label" for="col_valid_from">Valid From</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="50" id="col_minimum_margin_percent" checked>
                                        <label class="form-check-label" for="col_minimum_margin_percent">Minimum Margin Percent</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="51" id="col_disc_less" checked>
                                        <label class="form-check-label" for="col_disc_less">Disc Less</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="52" id="col_manufacturer" checked>
                                        <label class="form-check-label" for="col_manufacturer">Manufacturer</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="53" id="col_narcotic" checked>
                                        <label class="form-check-label" for="col_narcotic">Narcotic</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="54" id="col_schedule_h" checked>
                                        <label class="form-check-label" for="col_schedule_h">Schedule H</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="55" id="col_schedule_h1" checked>
                                        <label class="form-check-label" for="col_schedule_h1">Schedule H1</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-toggle" type="checkbox" value="56" id="col_schedule_h1_type" checked>
                                        <label class="form-check-label" for="col_schedule_h1_type">Schedule H1 Type</label>
                                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="applyColumnChanges">Apply Changes</button>
            </div>
        </div>
    </div>
</div>
<!-- /Manage Columns Modal -->

<!-- Import Product Modal -->
<div id="importProductModal" class="modal custom-modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Import Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="importProductForm" action="{{ box_route('accounts.products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label>Upload Excel/CSV File <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" name="file" accept=".csv, .xlsx, .xls" required>
                        <small class="text-muted mt-1 d-block">Required headers: product_name, code. Optional headers: packing, unit, company, mrp, rate_a, sgst_percent, etc.</small>
                        <div class="mt-2">
                            <a href="{{ box_route('accounts.products.download_template') }}" class="text-primary"><i class="fa-solid fa-download me-1"></i> Download Sample Template</a>
                        </div>
                    </div>
                    <div class="submit-section mt-4 text-end">
                        <button type="submit" class="btn btn-primary submit-btn">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Import Product Modal -->

<!-- Import Progress Modal -->
<div id="importProgressModal" class="modal custom-modal fade" role="dialog" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importProgressModalTitle"><i class="fa fa-spinner fa-spin me-2"></i> Bulk Import in Progress...</h5>
                <button type="button" class="btn-close d-none" id="importProgressCloseBtn" data-bs-dismiss="modal" aria-label="Close" onclick="location.reload()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3 text-center">
                    <div class="col-3">
                        <h6 class="text-muted">Total Records</h6>
                        <h4 id="importTotalCount" class="text-primary">Calculating...</h4>
                    </div>
                    <div class="col-3">
                        <h6 class="text-muted">Inserted</h6>
                        <h4 id="importSuccessCount" class="text-success">0</h4>
                    </div>
                    <div class="col-3">
                        <h6 class="text-muted">Skipped</h6>
                        <h4 id="importSkippedCount" class="text-warning">0</h4>
                    </div>
                    <div class="col-3">
                        <h6 class="text-muted">Failed</h6>
                        <h4 id="importFailedCount" class="text-danger">0</h4>
                    </div>
                </div>
                
                <div class="progress mb-3" style="height: 25px;">
                    <div id="importProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%">0%</div>
                </div>

                <div id="importErrorsList" class="d-none mt-3">
                    <strong class="text-danger">Errors encountered:</strong>
                    <div class="border p-2 mt-1 bg-light" style="max-height: 200px; overflow-y: auto;">
                        <ul class="mb-0 text-danger small" id="importErrorsUl"></ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-none" id="importProgressFooter">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="location.reload()">Close & Refresh</button>
            </div>
        </div>
    </div>
</div>
<!-- /Import Progress Modal -->
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var table = $('.datatable').DataTable();

        // Handle Edit Click
        let currentRowId = null;
        $('body').on('click', '.edit-product-btn', function(e) {
            e.preventDefault();
            var editUrl = $(this).attr('href');
            var originalHtml = $(this).html();
            $(this).html('<i class="fa fa-spinner fa-spin m-r-5"></i> Loading');
            
            fetch(editUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    var p = data.product;
                    currentRowId = p.id;
                    var form = document.getElementById('createProductForm');
                    
                    Object.keys(p).forEach(key => {
                        var el = form.elements[key];
                        if (el) {
                            if (el.type === 'checkbox' || el.type === 'radio') {
                                // handle if needed
                            } else {
                                el.value = p[key];
                            }
                        }
                    });

                    form.action = data.update_url;
                    
                    if (!form.querySelector('input[name="_method"]')) {
                        form.insertAdjacentHTML('beforeend', '<input type="hidden" name="_method" value="PUT">');
                    } else {
                        form.querySelector('input[name="_method"]').value = 'PUT';
                    }

                    document.getElementById('createProductModalTitle').innerText = 'Edit Product';
                    form.querySelector('button[type="submit"]').innerText = 'Update Product';

                    var modal = new bootstrap.Modal(document.getElementById('createProductModal'));
                    modal.show();
                }
            })
            .catch(err => {
                console.error(err);
                alert("Error fetching product data.");
            })
            .finally(() => {
                $(this).html(originalHtml);
            });
        });

        // Handle Add Click
        $('#add-product-btn').on('click', function() {
            currentRowId = null;
            var form = document.getElementById('createProductForm');
            form.reset();
            form.action = "{{ box_route('accounts.products.store') }}";
            
            var methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) {
                methodInput.remove();
            }

            document.getElementById('createProductModalTitle').innerText = 'Add New Product';
            form.querySelector('button[type="submit"]').innerText = 'Save Product';
        });

        // Expose currentRowId to create_modal script
        window.currentEditRowId = () => currentRowId;


        // Handle Delete via AJAX
        $('body').on('submit', '.delete-product-form', function(e) {
            e.preventDefault();
            // Confirm has already happened natively because of onsubmit attr, wait, we removed onsubmit in earlier sed command
            if (!confirm('Are you sure you want to delete this product?')) {
                return;
            }
            
            var form = this;
            var url = form.action;
            var formData = new FormData(form);
            var submitBtn = $(form).find('button[type="submit"]');
            var originalHtml = submitBtn.html();
            
            submitBtn.html('<i class="fa fa-spinner fa-spin m-r-5"></i> Deleting...').prop('disabled', true);
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    var tr = $(form).closest('tr');
                    // Find DataTables API instance
                    var tableApi = $('.datatable').DataTable();
                    tableApi.row(tr).remove().draw(false);
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message || 'Product deleted successfully.');
                    } else {
                        alert(data.message || 'Product deleted successfully.');
                    }
                } else {
                    alert("Error deleting product.");
                    submitBtn.html(originalHtml).prop('disabled', false);
                }
            })
            .catch(err => {
                console.error(err);
                alert("Error deleting product.");
                submitBtn.html(originalHtml).prop('disabled', false);
            });
        });

        // Bulk Delete Logic
        var bulkDeleteProductIds = [];

        $('#bulkDeleteBtn').on('click', function() {
            bulkDeleteProductIds = [];
            $('.product-checkbox:checked').each(function() {
                bulkDeleteProductIds.push($(this).val());
            });

            if (bulkDeleteProductIds.length > 0) {
                $('#bulkDeleteCount').text(bulkDeleteProductIds.length);
                $('#bulkDeleteProductModal').modal('show');
            }
        });

        $('#confirmBulkDeleteProductBtn').on('click', function() {
            if (bulkDeleteProductIds.length > 0) {
                var btn = $(this);
                var originalHtml = btn.html();
                btn.html('<i class="fa fa-spinner fa-spin"></i> Deleting...').prop('disabled', true);

                $.ajax({
                    url: "{{ box_route('accounts.products.bulk_delete') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        ids: bulkDeleteProductIds
                    },
                    success: function(response) {
                        btn.html(originalHtml).prop('disabled', false);
                        $('#bulkDeleteProductModal').modal('hide');
                        if (response.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message);
                            } else {
                                alert(response.message);
                            }
                            // Remove rows from DataTable
                            var tableApi = $('.datatable').DataTable();
                            bulkDeleteProductIds.forEach(function(id) {
                                var row = $('#product-row-' + id);
                                if (row.length) {
                                    tableApi.row(row).remove();
                                }
                            });
                            tableApi.draw(false);
                            $('#selectAllProduct').prop('checked', false);
                            toggleBulkDeleteBtn();
                            bulkDeleteProductIds = [];
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message);
                            } else {
                                alert(response.message);
                            }
                        }
                    },
                    error: function(xhr) {
                        btn.html(originalHtml).prop('disabled', false);
                        $('#bulkDeleteProductModal').modal('hide');
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong';
                        if (typeof toastr !== 'undefined') {
                            toastr.error(msg);
                        } else {
                            alert(msg);
                        }
                        $('#selectAllProduct').prop('checked', false);
                        toggleBulkDeleteBtn();
                        bulkDeleteProductIds = [];
                    }
                });
            }
        });

        $('#selectAllProduct').on('click', function() {
            var isChecked = $(this).prop('checked');
            $('.product-checkbox').prop('checked', isChecked);
            toggleBulkDeleteBtn();
        });

        $(document).on('change', '.product-checkbox', function() {
            if ($('.product-checkbox:checked').length === $('.product-checkbox').length) {
                $('#selectAllProduct').prop('checked', true);
            } else {
                $('#selectAllProduct').prop('checked', false);
            }
            toggleBulkDeleteBtn();
        });

        function toggleBulkDeleteBtn() {
            if ($('.product-checkbox:checked').length > 0) {
                $('#bulkDeleteBtn').removeClass('d-none');
            } else {
                $('#bulkDeleteBtn').addClass('d-none');
            }
        }

        // Uncheck select all on table redraw
        table.on('draw', function() {
            $('#selectAllProduct').prop('checked', false);
            toggleBulkDeleteBtn();
        });

        // Show All Columns
        $('#showAllColumns').on('click', function() {
            $('.column-toggle').prop('checked', true);
        });

        // Hide All Columns
        $('#hideAllColumns').on('click', function() {
            $('.column-toggle').prop('checked', false);
        });

        // Apply Column Changes
        $('#applyColumnChanges').on('click', function() {
            var columnPreferences = [];
            $('.column-toggle').each(function() {
                const index = parseInt($(this).val());
                const isVisible = $(this).is(':checked');
                columnPreferences.push({ index: index, visible: isVisible });
                
                var column = table.column(index);
                if (column && column.length) {
                    column.visible(isVisible, false);
                }
            });
            table.columns.adjust().draw(false);
            
            $.ajax({
                url: '{{ route("settings.column.preferences.save", ["box_slug" => request()->route("box_slug")]) }}',
                type: 'POST',
                data: {
                    page_identifier: 'products-list',
                    column_preferences: columnPreferences,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Column preferences saved successfully');
                }
            });
            $('#manageColumnsModal').modal('hide');
        });

        // Load initial preferences
        $.ajax({
            url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug")]) }}',
            type: 'GET',
            data: { page_identifier: 'products-list' },
            success: function(response) {
                if (response.success && response.data && Array.isArray(response.data)) {
                    response.data.forEach(pref => {
                        const index = parseInt(pref.index);
                        const isVisible = pref.visible === 'true' || pref.visible === '1' || pref.visible === true;
                        $(`.column-toggle[value="${index}"]`).prop('checked', isVisible);
                        var col = table.column(index);
                        if (col && col.length) {
                            col.visible(isVisible, false);
                        }
                    });
                    table.columns.adjust().draw(false);
                }
            }
        });
    }, 1000);

    // Bulk Import Polling
    let importPollingInterval = null;

    function checkImportStatus() {
        $.ajax({
            url: "{{ box_route('accounts.imports.status', 'Product') }}",
            type: "GET",
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;
                    
                    let total = data.total_rows || 0;
                    let processed = data.processed_rows || 0;
                    let success = data.success_rows || 0;
                    let skipped = data.skipped_rows || 0;
                    let failed = data.failed_rows || 0;

                    let percent = 0;
                    if (total > 0) {
                        percent = Math.min(100, Math.round((processed / total) * 100));
                    }

                    $('#importTotalCount').text(total > 0 ? total : 'Calculating...');
                    $('#importSuccessCount').text(success);
                    $('#importSkippedCount').text(skipped);
                    $('#importFailedCount').text(failed);
                    
                    $('#importProgressBar').css('width', percent + '%').text(percent + '%');

                    if (data.status === 'completed' || data.status === 'failed') {
                        clearInterval(importPollingInterval);
                        
                        $('#importProgressBar').removeClass('progress-bar-animated progress-bar-striped');
                        
                        if (data.status === 'completed') {
                            $('#importProgressModalTitle').html('<i class="fa fa-check-circle me-2 text-success"></i> Import Completed Successfully');
                            $('#importProgressBar').removeClass('bg-primary').addClass('bg-success');
                        } else {
                            $('#importProgressModalTitle').html('<i class="fa fa-times-circle me-2 text-danger"></i> Import Failed');
                            $('#importProgressBar').removeClass('bg-primary').addClass('bg-danger');
                        }

                        if (data.errors && data.errors.length > 0) {
                            $('#importErrorsList').removeClass('d-none');
                            $('#importErrorsUl').empty();
                            data.errors.forEach(function(err) {
                                $('#importErrorsUl').append(`<li>${err}</li>`);
                            });
                        }
                        
                        $('#importProgressCloseBtn').removeClass('d-none');
                        $('#importProgressFooter').removeClass('d-none');
                    }
                }
            }
        });
    }

    // Handle AJAX import submission
    $('#importProductForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var submitBtn = $(this).find('.submit-btn');
        var originalText = submitBtn.text();
        
        submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Uploading...').prop('disabled', true);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                submitBtn.html(originalText).prop('disabled', false);
                $('#importProductModal').modal('hide');
                
                if (response.success) {
                    // Reset modal state
                    $('#importProgressModalTitle').html('<i class="fa fa-spinner fa-spin me-2"></i> Bulk Import in Progress...');
                    $('#importTotalCount').text('Calculating...');
                    $('#importSuccessCount').text('0');
                    $('#importSkippedCount').text('0');
                    $('#importFailedCount').text('0');
                    $('#importProgressBar').css('width', '0%').text('0%').removeClass('bg-success bg-danger').addClass('bg-primary progress-bar-animated progress-bar-striped');
                    $('#importErrorsList').addClass('d-none');
                    $('#importProgressCloseBtn').addClass('d-none');
                    $('#importProgressFooter').addClass('d-none');

                    // Show Progress Modal
                    $('#importProgressModal').modal('show');
                    
                    if (!importPollingInterval) {
                        importPollingInterval = setInterval(checkImportStatus, 3000);
                        checkImportStatus();
                    }
                    
                    $('#importProductForm')[0].reset();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                submitBtn.html(originalText).prop('disabled', false);
                $('#importProductModal').modal('hide');
                
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong';
                showToast(msg, 'error');
            }
        });
    });

});
</script>
@endsection
