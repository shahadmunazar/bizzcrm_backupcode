@extends('layouts.main')
@section('content')
@section('title', 'Tax Rates')
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
                                <a href="javascript:void(0);" onclick="loadTaxRates()" data-bs-toggle="tooltip"
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

                        <!-- Settings Info -->
                        <div class="card">
                            <div class="card-body pb-0">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <h4 class="fs-20">Tax Rates</h4>
                                    <a href="javascript:void(0)" class="btn btn-sm btn-icon border rounded"
                                        data-bs-toggle="modal" data-bs-target="#add_tax"><i class="ti ti-plus"></i></a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-stripped" id="tax-rates-table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Tax Rate %</th>
                                                <th>Status</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tax-rates-body">
                                            <!-- Data will be loaded via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /Settings Info -->

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<!-- Add Tax Rate -->
<div class="modal fade" id="add_tax" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Tax Rate</h5>
                <div class="d-flex align-items-center">
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
            </div>
            <form id="addTaxForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="col-form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">Tax Rate % <span class="text-danger">*</span></label>
                        <input type="number" name="rate" step="0.01" class="form-control" required>
                    </div>
                    <div class="mb-0">
                        <label class="col-form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="Enabled">Enabled</option>
                            <option value="Disabled">Disabled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center justify-content-end m-0">
                        <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Tax Rate -->

<!-- Edit Tax Rate -->
<div class="modal fade" id="edit_tax" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Tax Rate</h5>
                <div class="d-flex align-items-center">
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
            </div>
            <form id="editTaxForm">
                <input type="hidden" name="id" id="edit_tax_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="col-form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_tax_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">Tax Rate % <span class="text-danger">*</span></label>
                        <input type="number" name="rate" id="edit_tax_rate" step="0.01" class="form-control"
                            required>
                    </div>
                    <div class="mb-0">
                        <label class="col-form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="edit_tax_status" class="form-select" required>
                            <option value="Enabled">Enabled</option>
                            <option value="Disabled">Disabled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Edit Tax Rate -->

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
    <div id="dynamicToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="dynamicToastMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            loadTaxRates();

            $('#addTaxForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('financial.settings.tax_rates.store', ['box_slug' => request()->route('box_slug')]) }}",
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            showtoast(res.message);
                            $('#add_tax').modal('hide');
                            $('#addTaxForm')[0].reset();
                            loadTaxRates();
                        }
                    },
                    error: function(xhr) {
                        showtoast(xhr.responseJSON?.message || 'Error occurred', true);
                    }
                });
            });

            $('#editTaxForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('financial.settings.tax_rates.update', ['box_slug' => request()->route('box_slug')]) }}",
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            showtoast(res.message);
                            $('#edit_tax').modal('hide');
                            loadTaxRates();
                        }
                    },
                    error: function(xhr) {
                        showtoast(xhr.responseJSON?.message || 'Error occurred', true);
                    }
                });
            });
        });

        function loadTaxRates() {
            $.ajax({
                url: "{{ route('financial.settings.tax_rates.list', ['box_slug' => request()->route('box_slug')]) }}",
                method: 'GET',
                success: function(res) {
                    let html = '';
                    if (res.data && res.data.length > 0) {
                        res.data.forEach(tax => {
                            html += `
                                <tr>
                                    <td>${tax.name}</td>
                                    <td>${tax.rate}%</td>
                                    <td><span class="badge ${tax.status === 'Enabled' ? 'bg-success' : 'bg-danger'}">${tax.status}</span></td>
                                    <td class="text-end">
                                        <div class="dropdown dropdown-action">
                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="editTax(${tax.id}, '${tax.name}', ${tax.rate}, '${tax.status}')"><i class="ti ti-edit me-1"></i> Edit</a>
                                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteTax(${tax.id})"><i class="ti ti-trash me-1"></i> Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        html = '<tr><td colspan="4" class="text-center">No tax rates found</td></tr>';
                    }
                    $('#tax-rates-body').html(html);
                }
            });
        }

        function editTax(id, name, rate, status) {
            $('#edit_tax_id').val(id);
            $('#edit_tax_name').val(name);
            $('#edit_tax_rate').val(rate);
            $('#edit_tax_status').val(status);
            $('#edit_tax').modal('show');
        }

        function deleteTax(id) {
            if (confirm('Are you sure you want to delete this tax rate?')) {
                $.ajax({
                    url: "{{ route('financial.settings.tax_rates.delete', ['box_slug' => request()->route('box_slug')]) }}",
                    method: 'DELETE',
                    data: { id: id },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        showtoast(res.message);
                        loadTaxRates();
                    },
                    error: function(xhr) {
                        showtoast(xhr.responseJSON?.message || 'Error occurred', true);
                    }
                });
            }
        }

        function showtoast(message, isError = false) {
            $('#dynamicToastMessage').text(message);
            const toastElement = document.getElementById('dynamicToast');
            const toast = new bootstrap.Toast(toastElement);
            $('#dynamicToast').removeClass('bg-danger bg-success').addClass(isError ? 'bg-danger' : 'bg-success');
            toast.show();
        }
    </script>
@endpush
@endsection
