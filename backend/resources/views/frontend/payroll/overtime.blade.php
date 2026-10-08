@extends('layouts.main')
@section('content')
@section('title', 'PayRoll OverTime')
 <!-- Page Wrapper -->
		 <div class="page-wrapper">
			 <div class="content">

				 <!-- Breadcrumb -->
				 <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
					 <div class="my-auto mb-2">
						 <h2 class="mb-1">Payroll Items </h2>
						 <nav>
							 <ol class="breadcrumb mb-0">
								 <li class="breadcrumb-item">
									 <a href="index.html"><i class="ti ti-smart-home"></i></a>
								 </li>
								 <li class="breadcrumb-item">
									HR
								 </li>
								 <li class="breadcrumb-item active" aria-current="page">Payroll Items </li>
							 </ol>
						 </nav>
					 </div>
					 <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
						 <div class="mb-2">
							 <div class="dropdown">
								 <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
									 <i class="ti ti-file-export me-1"></i>Export
								 </a>
								 <ul class="dropdown-menu  dropdown-menu-end p-3">
									 <li>
										 <a href="javascript:void(0);" class="dropdown-item rounded-1"><i class="ti ti-file-type-pdf me-1"></i>Export as PDF </a>
									 </li>
									 <li>
										 <a href="javascript:void(0);" class="dropdown-item rounded-1"><i class="ti ti-file-type-xls me-1"></i>Export as Excel  </a>
									 </li>
								 </ul>
							 </div>
						 </div>
						 <div class="head-icons ms-2">
							 <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
								 <i class="ti ti-chevrons-up"></i>
							 </a>
						 </div>
					 </div>
				 </div>
				 <div class="d-flex justify-content-between my-4">
					 <div class="payroll-btns">
						<a href="{{ box_route('payroll.payroll.items') }}" class="btn btn-white border me-2">Additions </a>
						 <a href="{{ box_route('payroll.over.time') }}" class="btn btn-white active border me-2">Overtime </a>
						 <a href="{{ box_route('payroll.deduction') }}" class="btn btn-white border">Deductions </a>
					 </div>
					 <div class="mb-2">
						 <a href="#" data-bs-toggle="modal" data-bs-target="#add_overtime" class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add Overtime </a>
					 </div>
				 </div>

				 <!-- /Breadcrumb -->

				 <!-- Policy list -->
				 <div class="card">
					 <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
						 <h5>OverTime List </h5>
						 <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">

							 <div class="dropdown">
								 <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
									Sort By : Last 7 Days
								 </a>
								 <ul class="dropdown-menu  dropdown-menu-end p-3">
									 <li>
										 <a href="javascript:void(0);" class="dropdown-item rounded-1">Recently Added </a>
									 </li>
									 <li>
										 <a href="javascript:void(0);" class="dropdown-item rounded-1">Ascending </a>
									 </li>
									 <li>
										 <a href="javascript:void(0);" class="dropdown-item rounded-1">Desending </a>
									 </li>
								 </ul>
							 </div>
						 </div>
					 </div>
					 <div class="card-body p-0">
						 <div class="custom-datatable-filter table-responsive">
							 <table class="table" id="payroll-table-overtime">
								 <thead class="thead-light">
									 <tr>
										 <th class="no-sort">
											 <div class="form-check form-check-md">
												 <input class="form-check-input" type="checkbox" id="select-all" />
											 </div>
										 </th>
										 <th>Name </th>
										 <th>Rate </th>
										 <th></th>
									 </tr>
								 </thead>
								 <tbody>
									 <tr>
										 <td>
											 <div class="form-check form-check-md">
												 <input class="form-check-input" type="checkbox" />
											 </div>
										 </td>
										 <td>
											 <h6 class="fs-14 fw-medium text-gray-9">Normal day OT 1.5x </h6>
										 </td>
										 <td>Hourly 1.5 </td>
										 <td>
											 <div class="action-icon d-inline-flex">
												 <a href="#" class="me-2" data-bs-toggle="modal" data-bs-target="#edit_overtime"><i class="ti ti-edit"></i></a>
												 <a href="#" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
											 </div>
										 </td>
									 </tr>
									 <tr>
										 <td>
											 <div class="form-check form-check-md">
												 <input class="form-check-input" type="checkbox" />
											 </div>
										 </td>
										 <td>
											 <h6 class="fs-14 fw-medium text-gray-9">Public holiday OT 3.0x </h6>
										 </td>
										 <td>Hourly 3 </td>
										 <td>
											 <div class="action-icon d-inline-flex">
												 <a href="#" class="me-2" data-bs-toggle="modal" data-bs-target="#edit_overtime"><i class="ti ti-edit"></i></a>
												 <a href="#" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
											 </div>
										 </td>
									 </tr>
									 <tr>
										 <td>
											 <div class="form-check form-check-md">
												 <input class="form-check-input" type="checkbox" />
											 </div>
										 </td>
										 <td>
											 <h6 class="fs-14 fw-medium text-gray-9">Rest day OT 2.0x </h6>
										 </td>
										 <td>Hourly 2 </td>
										 <td>
											 <div class="action-icon d-inline-flex">
												 <a href="#" class="me-2" data-bs-toggle="modal" data-bs-target="#edit_overtime"><i class="ti ti-edit"></i></a>
												 <a href="#" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
											 </div>
										 </td>
									 </tr>
								 </tbody>
							 </table>
						 </div>
					 </div>
				 </div>
				 <!-- /Policylist list -->

			 </div>

			 

		 </div>
		 <!-- /Page Wrapper -->

		 <!-- Add Payroll Overtime -->
		 <div class="modal fade" id="add_overtime">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Overtime</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="addOvertimeForm">
                <div class="modal-body pb-0">
                        <!-- Name -->
                    <div class="mb-3">
                        <label for="addition_name" class="form-label fw-semibold">Name</label>
                        <input type="text" name="name" id="addition_name" class="form-control"
                            placeholder="Enter addition name" required>
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Input Type <span class="text-danger">*</span></label>
                        <select class="select">
							<option disabled selected>Select input Type</option>
							<option>Amount</option>
							<option>Percentage</option>
						</select>
                    </div>

                    <div class="mb-3">
                            <label for="addition_amount" class="form-label fw-semibold">Value</label>
                            <input type="text" placeholder="Value" class="form-control">
                    </div>


                    <!-- Assignee -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assignee</label>
                        <div class="d-flex gap-3 flex-wrap">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="assignee_type"
                                    value="no_assignee" checked>
                                <label class="form-check-label">No Assignee</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="assignee_type"
                                    value="all_employees">
                                <label class="form-check-label">All Employees</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="assignee_type"
                                    value="select_employee">
                                <label class="form-check-label">Select Employee</label>
                            </div>
                        </div>
                    </div>
                

                    <div class="mb-3" id="select_employee_field" style="display: none;">
                        <label for="select_employees" class="form-label fw-semibold">Select Employees</label>
                        <select name="select_employees[]" id="select_employees" class="form-select" multiple></select>
                        <div id="selected_employees" class="mt-2"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="">Select</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Overtime</button>
                </div>
            </form>
        </div>
    </div>
</div>


		 <!-- /Add Payroll -->

		 <!-- Edit  Payroll Overtime -->
		 <div class="modal fade" id="edit_overtime">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Overtime</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="editOvertimeForm">
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" required />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Rate Type</label>
                                <select name="rate_type" class="select form-select" required>
                                    <option value="">Select</option>
                                    <option value="Hourly 1.5">Hourly 1.5</option>
                                    <option value="Hourly 2">Hourly 2</option>
                                    <option value="Hourly 3">Hourly 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Rate</label>
                                <input type="number" name="rate" class="form-control" required />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="">Select</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

		 <!-- /Edit  Payroll Overtime -->

		 <!-- Delete Modal -->
		 <div class="modal fade" id="delete_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1">Confirm Delete</h4>
                <p class="mb-3"></p> <!-- text set dynamically -->
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirm_delete" class="btn btn-danger">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    <div id="dynamicToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="dynamicToastMessage">
                Success!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

		 <!-- /Delete Modal -->



        @push('scripts')
<script>
function showToast(message, type = 'success') {
    const toastEl = $('#dynamicToast');
    const toast = new bootstrap.Toast(toastEl[0]);
    const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
    toastEl.removeClass('bg-success bg-danger').addClass(bgClass);
    $('#dynamicToastMessage').text(message);
    toast.show();
}

$(document).ready(function() {
    // -------------------- Initialize DataTable --------------------
    var table = $('#payroll-table-overtime').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("payroll.list.overtime", ["box_slug" => request()->route("box_slug")]) }}',
        columns: [
            { data: 'id', render: data => `<input type="checkbox" class="form-check-input" value="${data}">`, orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'rate_type', name: 'rate_type' },
            { data: 'id', render: data => `
                <div class="action-icon d-inline-flex">
                    <a href="javascript:void(0)" class="me-2 edit-overtime" data-id="${data}"><i class="ti ti-edit"></i></a>
                    <a href="javascript:void(0)" class="text-danger delete-overtime" data-id="${data}"><i class="ti ti-trash"></i></a>
                </div>`, orderable: false, searchable: false
            }
        ]
    });

    // -------------------- Edit Overtime --------------------
    $('#payroll-table-overtime tbody').on('click', '.edit-overtime', function() {
    const rowData = table.row($(this).closest('tr')).data();
    const $modal = $('#edit_overtime');

    // Set input fields
    $modal.find('input[name="name"]').val(rowData.name);
    $modal.find('input[name="rate"]').val(rowData.rate);

    // Set select fields with trigger
    $modal.find('select[name="rate_type"]')
        .val(rowData.rate_type)
        .trigger('change');

    $modal.find('select[name="status"]')
        .val(rowData.status)
        .trigger('change');

    // Store the row id for form submission
    $modal.data('id', rowData.id);

    // Show the modal
    $modal.modal('show');
});


    $('#editOvertimeForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_overtime').data('id');
        const formData = {
            id: id,
            name: $('#edit_overtime input[name="name"]').val(),
            rate_type: $('#edit_overtime select[name="rate_type"]').val(),
            rate: $('#edit_overtime input[name="rate"]').val(),
            status: $('#edit_overtime select[name="status"]').val(),
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'PUT'
        };

        $.ajax({
            url: "{{ route('payroll.update.overtime', ['box_slug' => request()->route('box_slug')]) }}",
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#edit_overtime').modal('hide');
                table.ajax.reload(null, false);
                showToast(response.message || 'Overtime updated successfully!', 'success');
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                const errorMsg = xhr.responseJSON?.message || 'Failed to update overtime.';
                showToast(errorMsg, 'error');
            }
        });
    });

    // -------------------- Add Overtime --------------------
    $('#addOvertimeForm').on('submit', function(e) {
        e.preventDefault();
        const formData = {
            name: $('#addOvertimeForm input[name="name"]').val(),
            rate_type: $('#addOvertimeForm select[name="rate_type"]').val(),
            rate: $('#addOvertimeForm input[name="rate"]').val(),
            status: $('#addOvertimeForm select[name="status"]').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: "{{ route('store.payroll.overtime', ['box_slug' => request()->route('box_slug')]) }}",
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#add_overtime').modal('hide');
                table.ajax.reload(null, false);
                showToast(response.message || 'Overtime added successfully!', 'success');
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                const errorMsg = xhr.responseJSON?.message || 'Failed to add overtime.';
                showToast(errorMsg, 'error');
            }
        });
    });

    // -------------------- Delete Overtime --------------------
    let deleteId = null;
    $('#payroll-table-overtime tbody').on('click', '.delete-overtime', function() {
        const rowData = table.row($(this).closest('tr')).data();
        deleteId = rowData.id;
        $('#delete_modal').data('id', deleteId);
        $('#delete_modal .modal-body p').text(`Are you sure you want to delete "${rowData.name}"? This action cannot be undone.`);
        $('#delete_modal').modal('show');
    });

    $('#confirm_delete').on('click', function() {
        const id = $('#delete_modal').data('id');
        if (!id) return;

        $.ajax({
            url: "{{ route('payroll.destroy.overtime', ['box_slug' => request()->route('box_slug')]) }}",
            type: 'DELETE',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), id: id },
            success: function(response) {
                $('#delete_modal').modal('hide');
                table.ajax.reload(null, false);
                showToast(response.message || 'Overtime deleted successfully!', 'success');
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                const errorMsg = xhr.responseJSON?.message || 'Failed to delete overtime.';
                showToast(errorMsg, 'error');
            }
        });
    });

});
</script>
@endpush



@endsection
