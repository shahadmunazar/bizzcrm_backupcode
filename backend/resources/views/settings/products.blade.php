@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@section('title', 'Products')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">Settings</h4>
                        </div>
                        <div class="col-4 text-end">
                            <div class="head-icons">
                                <a href="javascript:void(0);" onclick="reloadTable();" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-original-title="Refresh"><i
                                        class="ti ti-refresh-dot"></i></a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Collapse" id="collapse-header"><i
                                        class="ti ti-chevrons-up"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                @include('settings.partials.nav')

                <div class="row">
                    <div class="col-xl-3 col-lg-12 theiaStickySidebar">
                        @include('settings.partials.sidebar')
                    </div>

                    <div class="col-xl-9 col-lg-12">

                        <!-- Table Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <h4 class="fs-20 fw-semibold">Products</h4>
                                    <a href="javascript:void(0)" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal"
                                        data-bs-target="#add_product">
                                        <i class="ti ti-square-rounded-plus me-2 fs-18"></i>Add Product
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="products_table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Name</th>
                                                <th>MRP</th>
                                                <th>Status</th>
                                                <th>Created By</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /Table Card -->

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<!-- Add Product Modal -->
<div class="modal fade" id="add_product" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold">Add Product</h5>
                <button type="button" class="btn-close custom-btn-close border p-1" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="addProductForm" onsubmit="return false;">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-medium">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="product_name" class="form-control" placeholder="e.g. Premium Plan" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-medium">MRP</label>
                            <input type="number" step="0.01" name="mrp" class="form-control" placeholder="0.00">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-medium">Status</label>
                            <select name="status" class="form-select select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top p-3">
                    <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="saveAddProductBtn" class="btn btn-primary fw-medium px-4">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Product Modal -->

<!-- Edit Product Modal -->
<div class="modal fade" id="edit_product" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold">Edit Product</h5>
                <button type="button" class="btn-close custom-btn-close border p-1" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="editProductForm" onsubmit="return false;">
                @csrf
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-medium">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="product_name" id="edit_product_name" class="form-control" placeholder="e.g. Premium Plan" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-medium">MRP</label>
                            <input type="number" step="0.01" name="mrp" id="edit_mrp" class="form-control" placeholder="0.00">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-medium">Status</label>
                            <select name="status" id="edit_status" class="form-select select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top p-3">
                    <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="saveEditProductBtn" class="btn btn-primary fw-medium px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Edit Product Modal -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    var table;
    $(document).ready(function() {
        table = $('#products_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('settings.products') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'product_name', name: 'product_name'},
                {data: 'mrp', name: 'mrp'},
                {
                    data: 'status', 
                    name: 'status',
                    render: function(data) {
                        return data == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                    }
                },
                {data: 'created_by', name: 'created_by'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end'},
            ]
        });

        // Save Add Product
        $('#addProductForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = $('#saveAddProductBtn');
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            
            $.ajax({
                url: "{{ route('settings.products.store') }}",
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    btn.prop('disabled', false).html('Save');
                    if (response.status) {
                        toastr.success(response.message);
                        $('#add_product').modal('hide');
                        form[0].reset();
                        table.ajax.reload(null, false);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('Save');
                    toastr.error('Something went wrong.');
                }
            });
        });

        // Edit Product Modal populate
        $(document).on('click', '.edit-product-btn', function() {
            var id = $(this).data('id');
            var name = $(this).data('product_name');
            var mrp = $(this).data('mrp');
            var status = $(this).data('status');
            
            $('#edit_id').val(id);
            $('#edit_product_name').val(name);
            $('#edit_mrp').val(mrp);
            $('#edit_status').val(status).trigger('change');
            
            $('#edit_product').modal('show');
        });

        // Save Edit Product
        $('#editProductForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = $('#saveEditProductBtn');
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            
            $.ajax({
                url: "{{ route('settings.products.update') }}",
                type: 'PUT',
                data: form.serialize(),
                success: function(response) {
                    btn.prop('disabled', false).html('Save Changes');
                    if (response.status) {
                        toastr.success(response.message);
                        $('#edit_product').modal('hide');
                        table.ajax.reload(null, false);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('Save Changes');
                    toastr.error('Something went wrong.');
                }
            });
        });

        // Delete Product
        $(document).on('click', '.delete-product-btn', function() {
            var id = $(this).data('id');
            if (confirm('Are you sure you want to delete this product?')) {
                $.ajax({
                    url: "{{ route('settings.products.destroy') }}",
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            table.ajax.reload(null, false);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong.');
                    }
                });
            }
        });
    });

    function reloadTable() {
        table.ajax.reload(null, false);
    }
</script>
@endsection
