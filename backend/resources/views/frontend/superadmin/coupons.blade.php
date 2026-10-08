@extends('layouts.main')
@section('content')
@section('title','Coupons')

<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									<h4 class="page-title">Coupons</h4>
								</div>
								<div class="col-4 text-end">
									<div class="head-icons">
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

						<div class="row">
							<!-- Total Coupons -->
							<div class="col-lg-4 col-md-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body d-flex align-items-center justify-content-between">
										<div class="d-flex align-items-center overflow-hidden">
											<div>
												<p class="fs-12 fw-medium mb-1 text-truncate">Total Coupons</p>
												<h4 id="total-coupons-count">{{ count($coupons) }}</h4>
											</div>
										</div>
										<div>
											<span class="avatar avatar-lg bg-primary flex-shrink-0">
												<i class="ti ti-ticket fs-16"></i>
											</span>
										</div>
									</div>
								</div>
							</div>
							<!-- /Total Coupons -->
							
							<!-- Active Coupons -->
							<div class="col-lg-4 col-md-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body d-flex align-items-center justify-content-between">
										<div class="d-flex align-items-center overflow-hidden">
											<div>
												<p class="fs-12 fw-medium mb-1 text-truncate">Active Coupons</p>
												<h4 id="active-coupons-count">{{ $coupons->where('status', 'active')->count() }}</h4>
											</div>
										</div>
										<div>
											<span class="avatar avatar-lg bg-success flex-shrink-0">
												<i class="ti ti-ticket fs-16"></i>
											</span>
										</div>
									</div>
								</div>
							</div>
							<!-- /Active Coupons -->

							<!-- Inactive Coupons -->
							<div class="col-lg-4 col-md-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body d-flex align-items-center justify-content-between">
										<div class="d-flex align-items-center overflow-hidden">
											<div>
												<p class="fs-12 fw-medium mb-1 text-truncate">Inactive Coupons</p>
												<h4 id="inactive-coupons-count">{{ $coupons->where('status', 'inactive')->count() }}</h4>
											</div>
										</div>
										<div>
											<span class="avatar avatar-lg bg-danger flex-shrink-0">
												<i class="ti ti-ticket fs-16"></i>
											</span>
										</div>
									</div>
								</div>
							</div>
							<!-- /Inactive Coupons -->
						</div>

						<div class="card">
							<div class="card-header">
								<div class="row align-items-center">
									<div class="col-sm-4">
										<div class="icon-form mb-3 mb-sm-0">
											<span class="form-icon"><i class="ti ti-search"></i></span>
											<input type="text" class="form-control" id="coupon-search-input" placeholder="Search Coupon">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
											<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-square-rounded-plus me-2"></i>Add New Coupon</a>
										</div>
									</div>
								</div>
							</div>
							<div class="card-body">
								<div class="table-responsive custom-table">
									<table class="table datatable">
										<thead class="thead-light">
											<tr>
												<th>Code</th>
                                                <th>Target Users</th>
												<th>Type</th>
												<th>Value</th>
												<th>Usage Limit</th>
												<th>Used Count</th>
												<th>Valid Until</th>
												<th>Status</th>
												<th class="text-end">Action</th>
											</tr>
										</thead>
										<tbody id="coupon-table-body">
                                            <!-- Data filled via AJAX -->
										</tbody>
									</table>
								</div>
							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Add Coupon -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Add New Coupon</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="{{ box_route('superadmin.coupons.store') }}" method="POST" id="add_coupon_form">
                    @csrf
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									<div class="mb-3">
										<label class="form-label">Coupon Code<span class="text-danger"> *</span></label>
										<input type="text" class="form-control" name="code" required>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Target Users<span class="text-danger"> *</span></label>
										<select class="select form-control" name="user_type" required>
											<option value="all">All Users</option>
											<option value="new">New Users Only</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Discount Type<span class="text-danger"> *</span></label>
										<select class="select form-control" name="type" required>
											<option value="percentage">Percentage</option>
											<option value="fixed">Fixed</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Discount Value<span class="text-danger"> *</span></label>
										<input type="number" step="0.01" class="form-control" name="value" required>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Valid Until</label>
										<input type="date" class="form-control" name="valid_until">
									</div>
								</div>
                                <div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Usage Limit</label>
										<input type="number" class="form-control" name="usage_limit" min="1">
									</div>
								</div>
                                <div class="col-md-12">
									<div class="mb-3 ">
										<label class="form-label">Status<span class="text-danger"> *</span></label>
										<select class="select form-control" name="status" required>
											<option value="active" selected>Active</option>
											<option value="inactive">Inactive</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary">Create Coupon</button>
					</div>
				</form>
			</div>
		</div>
		<!-- /Add Coupon -->

        <!-- Edit Coupon -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Edit Coupon</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="" method="POST" id="edit_coupon_form">
                    @csrf
                    @method('PUT')
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									<div class="mb-3">
										<label class="form-label">Coupon Code<span class="text-danger"> *</span></label>
										<input type="text" class="form-control" name="code" id="edit_code" required>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Target Users<span class="text-danger"> *</span></label>
										<select class="select form-control" name="user_type" id="edit_user_type" required>
											<option value="all">All Users</option>
											<option value="new">New Users Only</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Discount Type<span class="text-danger"> *</span></label>
										<select class="select form-control" name="type" id="edit_type" required>
											<option value="percentage">Percentage</option>
											<option value="fixed">Fixed</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Discount Value<span class="text-danger"> *</span></label>
										<input type="number" step="0.01" class="form-control" name="value" id="edit_value" required>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Valid Until</label>
										<input type="date" class="form-control" name="valid_until" id="edit_valid_until">
									</div>
								</div>
                                <div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Usage Limit</label>
										<input type="number" class="form-control" name="usage_limit" id="edit_usage_limit" min="1">
									</div>
								</div>
                                <div class="col-md-12">
									<div class="mb-3 ">
										<label class="form-label">Status<span class="text-danger"> *</span></label>
										<select class="select form-control" name="status" id="edit_status" required>
											<option value="active">Active</option>
											<option value="inactive">Inactive</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary">Update Coupon</button>
					</div>
				</form>
			</div>
		</div>
		<!-- /Edit Coupon -->


    <!-- Toast Notification Container -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 11000;">
        <div id="toastMessage" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastBody">
                    <!-- Toast message will be inserted here -->
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

		<!-- Delete Coupon -->
		<div class="modal fade" id="delete_coupon_modal" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Coupon?</h4>
							<p class="mb-0">Are you sure you want to remove the coupon <br> you selected?</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
								<button type="button" id="confirm_delete_coupon" class="btn btn-danger">Yes, Delete it</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Coupon -->

<style>
    .bg-soft-success {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }
    .smaller {
        font-size: 0.75rem;
    }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        let allCoupons = [];
        let couponToDelete = null;

        function showSuccessToast(message) {
            const $toast = $('#toastMessage');
            const $toastBody = $('#toastBody');
            $toast.removeClass('bg-danger bg-success bg-warning').addClass('bg-success');
            $toastBody.html(`
                <div class="d-flex align-items-center">
                    <i class="ti ti-check-circle me-2 fs-5"></i>
                    <div class="flex-grow-1">
                        <strong>Success!</strong><br>
                        <small>${message}</small>
                    </div>
                </div>
            `);
            if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                const toast = new bootstrap.Toast($toast[0], { autohide: true, delay: 5000 });
                toast.show();
            } else {
                alert(message);
            }
        }

        function showErrorToast(message) {
            const $toast = $('#toastMessage');
            const $toastBody = $('#toastBody');
            $toast.removeClass('bg-danger bg-success bg-warning').addClass('bg-danger');
            $toastBody.html(`
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-circle me-2 fs-5"></i>
                    <div class="flex-grow-1">
                        <strong>Error!</strong><br>
                        <small>${message}</small>
                    </div>
                </div>
            `);
            if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                const toast = new bootstrap.Toast($toast[0], { autohide: true, delay: 5000 });
                toast.show();
            } else {
                alert(message);
            }
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function getBoxRoute(id, action) {
            let baseUrl = "{{ box_route('superadmin.coupons.store') }}";
            if (action === 'update' || action === 'destroy') {
                return baseUrl + '/' + id;
            }
            return baseUrl;
        }

        function loadCoupons() {
            $.ajax({
                url: window.location.href,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        allCoupons = response.coupons;
                        renderTable(allCoupons);
                    }
                },
                error: function() {
                    showErrorToast('Failed to load coupons list.');
                }
            });
        }

        function renderTable(coupons) {
            let tbody = $('#coupon-table-body');
            tbody.empty();

            $('#total-coupons-count').text(coupons.length);
            $('#active-coupons-count').text(coupons.filter(c => c.status === 'active').length);
            $('#inactive-coupons-count').text(coupons.filter(c => c.status === 'inactive').length);

            if (coupons.length === 0) {
                tbody.append('<tr><td colspan="8" class="text-center">No coupons found.</td></tr>');
                return;
            }

            coupons.forEach(coupon => {
                let row = `
                    <tr>
                        <td><strong>${coupon.code}</strong></td>
                        <td>${coupon.user_type === 'new' ? '<span class="badge bg-info">New Users</span>' : '<span class="badge bg-secondary">All Users</span>'}</td>
                        <td>${coupon.type === 'percentage' ? 'Percentage (%)' : 'Fixed Amount'}</td>
                        <td>${coupon.value}</td>
                        <td>${coupon.usage_limit ? coupon.usage_limit : 'Unlimited'}</td>
                        <td>${coupon.used_count}</td>
                        <td>${coupon.valid_until ? coupon.valid_until : 'No Expiry'}</td>
                        <td>
                            <span class="badge badge-pill badge-status ${coupon.status === 'active' ? 'bg-success' : 'bg-danger'}">${coupon.status.charAt(0).toUpperCase() + coupon.status.slice(1)}</span>
                        </td>
                        <td class="text-end">
                            <div class="dropdown table-action">
                                <a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item edit-coupon-btn" href="#" data-id="${coupon.id}">
                                        <i class="ti ti-edit text-blue"></i> Edit
                                    </a>
                                    <a class="dropdown-item delete-coupon-btn" href="#" data-id="${coupon.id}">
                                        <i class="ti ti-trash text-danger"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }

        loadCoupons();

        // Add form submission
        $('#add_coupon_form').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if(response.success) {
                        showSuccessToast(response.message);
                        let offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvas_add'));
                        offcanvas.hide();
                        form[0].reset();
                        loadCoupons();
                    }
                },
                error: function(xhr) {
                    let msg = 'An error occurred';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    showErrorToast(msg);
                }
            });
        });

        // Edit button click
        $(document).on('click', '.edit-coupon-btn', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let coupon = allCoupons.find(c => c.id === id);
            if(coupon) {
                $('#edit_code').val(coupon.code);
                $('#edit_user_type').val(coupon.user_type).trigger('change');
                $('#edit_type').val(coupon.type).trigger('change');
                $('#edit_value').val(coupon.value);
                $('#edit_valid_until').val(coupon.valid_until);
                $('#edit_usage_limit').val(coupon.usage_limit);
                $('#edit_status').val(coupon.status).trigger('change');
                
                $('#edit_coupon_form').attr('action', getBoxRoute(id, 'update'));
                
                let offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvas_edit'));
                offcanvas.show();
            }
        });

        // Edit form submission
        $('#edit_coupon_form').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if(response.success) {
                        showSuccessToast(response.message);
                        let offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvas_edit'));
                        offcanvas.hide();
                        loadCoupons();
                    }
                },
                error: function(xhr) {
                    let msg = 'An error occurred';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    showErrorToast(msg);
                }
            });
        });

        // Delete button click
        $(document).on('click', '.delete-coupon-btn', function(e) {
            e.preventDefault();
            couponToDelete = $(this).data('id');
            let modal = new bootstrap.Modal(document.getElementById('delete_coupon_modal'));
            modal.show();
        });

        // Confirm delete
        $('#confirm_delete_coupon').on('click', function() {
            if(couponToDelete) {
                $.ajax({
                    url: getBoxRoute(couponToDelete, 'destroy'),
                    type: 'POST',
                    data: {
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if(response.success) {
                            showSuccessToast(response.message);
                            let modal = bootstrap.Modal.getInstance(document.getElementById('delete_coupon_modal'));
                            modal.hide();
                            couponToDelete = null;
                            loadCoupons();
                        }
                    },
                    error: function() {
                        showErrorToast('Failed to delete coupon.');
                    }
                });
            }
        });
        
        // Search functionality
        $('#coupon-search-input').on('keyup', function() {
            let value = $(this).val().toLowerCase();
            if(value === '') {
                renderTable(allCoupons);
            } else {
                let filtered = allCoupons.filter(c => 
                    (c.code && c.code.toLowerCase().includes(value)) ||
                    (c.status && c.status.toLowerCase().includes(value))
                );
                renderTable(filtered);
            }
        });

    });
</script>
@endpush
@endsection
