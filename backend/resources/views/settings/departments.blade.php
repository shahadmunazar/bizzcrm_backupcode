@extends('layouts.main')
@section('content')
@section('title','Departments')
	<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									<h4 class="page-title">Departments</h4>
								</div>
								<div class="col-4 text-end">
									<div class="head-icons">
										<a href="{{ box_route('manage_users.departments') }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
									</div>
								</div>
							</div>
						</div>
                                <div class="row">
                                    <!-- Departments Section -->
                                    <div class="col-md-12 mb-4">
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5 class="card-title mb-0">Departments for {{ $box->name }}</h5>
                                                <div>
                                                    <a href="javascript:void(0);" class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                                                        <i class="ti ti-columns me-1"></i>Manage Columns
                                                    </a>
                                                    <a href="javascript:void(0);" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add_department">
                                                        <i class="ti ti-plus me-1"></i>Add Department
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-hover" id="departmentsTable" style="width:100%">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Name</th>
                                                                <th>Left (_lft)</th>
                                                                <th>Right (_rgt)</th>
                                                                <th>Status</th>
                                                                <th class="text-end">Action</th>
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

			</div>
		</div>

	</div>
</div>
		<!-- /Page Wrapper -->


		<!-- Add Department Modal -->
		<div class="modal fade" id="add_department" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Create New Department</h5>
						<button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form id="add_department_form">
                        @csrf
						<div class="modal-body">
							<div class="mb-3">
								<label class="col-form-label">Department Name <span class="text-danger">*</span></label>
								<input type="text" name="name" class="form-control" placeholder="e.g. Sales, Marketing, IT" required>
							</div>
                            <div class="mb-3">
                                <label class="col-form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary" id="btn_save_department">Create Department</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- Edit Department Modal -->
		<div class="modal fade" id="edit_department" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Edit Department</h5>
						<button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form id="edit_department_form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="edit_department_id">
						<div class="modal-body">
							<div class="mb-3">
								<label class="col-form-label">Department Name <span class="text-danger">*</span></label>
								<input type="text" name="name" id="edit_department_name" class="form-control" required>
							</div>
                            <div class="mb-3">
                                <label class="col-form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="edit_department_status" class="form-select" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary" id="btn_update_department">Save Changes</button>
						</div>
					</form>
				</div>
			</div>
		</div>

        <!-- Manage Columns Modal -->
        <div class="modal fade" id="manageColumnsModal" tabindex="-1" aria-labelledby="manageColumnsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="manageColumnsModalLabel">Manage Columns</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="showAllColumns">Show All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="hideAllColumns">Hide All</button>
                        </div>
                        <div class="list-group" id="columnsList">
                            <!-- Columns will be populated dynamically -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="applyColumnChanges">Apply Changes</button>
                    </div>
                </div>
            </div>
        </div>

        @endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#departmentsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ box_route('manage_users.departments.list') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: '_lft', name: '_lft'},
                {data: '_rgt', name: '_rgt'},
                {data: 'status_badge', name: 'status', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end'}
            ]
        });

        const columnDefinitions = [
            { index: 0, title: 'ID', visible: true },
            { index: 1, title: 'Name', visible: true },
            { index: 2, title: 'Left (_lft)', visible: true },
            { index: 3, title: 'Right (_rgt)', visible: true },
            { index: 4, title: 'Status', visible: true },
            { index: 5, title: 'Action', visible: true }
        ];

        function populateColumnsModal() {
            const columnsList = $('#columnsList');
            columnsList.empty();

            columnDefinitions.forEach(col => {
                const isActionColumn = col.index === 5;
                const listItem = `
                    <div class="list-group-item">
                        <div class="form-check">
                            <input class="form-check-input column-toggle" type="checkbox"
                                   data-column-index="${col.index}"
                                   id="col_${col.index}"
                                   ${col.visible ? 'checked' : ''}
                                   ${isActionColumn ? 'disabled' : ''}>
                            <label class="form-check-label" for="col_${col.index}">
                                ${col.title}
                            </label>
                        </div>
                    </div>
                `;
                columnsList.append(listItem);
            });
        }

        $('#manageColumnsModal').on('show.bs.modal', function() {
            populateColumnsModal();
        });

        $('#showAllColumns').on('click', function() {
            $('.column-toggle').not(':disabled').prop('checked', true);
        });

        $('#hideAllColumns').on('click', function() {
            $('.column-toggle').not(':disabled').prop('checked', false);
        });

        $('#applyColumnChanges').on('click', function() {
            $('.column-toggle').each(function() {
                const columnIndex = parseInt($(this).data('column-index'));
                const isVisible = $(this).is(':checked');
                const col = columnDefinitions.find(c => c.index === columnIndex);
                if (col) {
                    col.visible = isVisible;
                    table.column(columnIndex).visible(isVisible);
                }
            });
            table.columns.adjust().draw(false);
            $('#manageColumnsModal').modal('hide');
        });

        $('#add_department_form').submit(function(e) {
            e.preventDefault();
            var btn = $('#btn_save_department');
            btn.prop('disabled', true).text('Saving...');
            
            $.ajax({
                url: "{{ box_route('manage_users.departments.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#add_department').modal('hide');
                        $('#add_department_form')[0].reset();
                        table.ajax.reload();
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                    btn.prop('disabled', false).text('Create Department');
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Something went wrong.');
                    btn.prop('disabled', false).text('Create Department');
                }
            });
        });

        $(document).on('click', '.edit-department', function() {
            $('#edit_department_id').val($(this).data('id'));
            $('#edit_department_name').val($(this).data('name'));
            $('#edit_department_status').val($(this).data('status'));
            $('#edit_department').modal('show');
        });

        $('#edit_department_form').submit(function(e) {
            e.preventDefault();
            var id = $('#edit_department_id').val();
            var btn = $('#btn_update_department');
            btn.prop('disabled', true).text('Updating...');
            
            let updateUrl = "{{ box_route('manage_users.departments.update', ':id') }}";
            updateUrl = updateUrl.replace(':id', id);
            
            $.ajax({
                url: updateUrl,
                type: "PUT",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#edit_department').modal('hide');
                        table.ajax.reload();
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                    btn.prop('disabled', false).text('Save Changes');
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Something went wrong.');
                    btn.prop('disabled', false).text('Save Changes');
                }
            });
        });

        $(document).on('click', '.delete-department', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let deleteUrl = "{{ box_route('manage_users.departments.delete', ':id') }}";
                    deleteUrl = deleteUrl.replace(':id', id);

                    $.ajax({
                        url: deleteUrl,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload();
                                Swal.fire('Deleted!', response.message, 'success');
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
