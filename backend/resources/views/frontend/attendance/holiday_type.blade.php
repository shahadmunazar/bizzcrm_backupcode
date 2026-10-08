@extends('layouts.main')
@section('content')
@section('title','Holiday Type')

<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									<h4 class="page-title">Holiday Type</h4>
								</div>
								<div class="col-4 text-end">
									<div class="head-icons">
										<a href="roles-permissions.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

						<div class="card">
							<div class="card-header">
								<!-- Search -->
								<div class="row align-items-center">
									<div class="col-sm-4">
										<!--<div class="icon-form mb-3 mb-sm-0">
											<span class="form-icon"><i class="ti ti-search"></i></span>
											<input type="text" class="form-control" placeholder="Search Holiday">
										</div> -->
									</div>
									<div class="col-sm-8">
										<div class="text-sm-end">
											<button type="button" class="btn btn-sm btn-white d-inline-flex align-items-center me-2" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
												<i class="ti ti-columns me-1"></i>Manage Columns
											</button>
											<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_role"><i class="ti ti-square-rounded-plus me-2"></i>Add Holiday Type</a>
										</div>
									</div>
								</div>
								<!-- /Search -->
							</div>
							<div class="card-body">
								<!-- Roles List -->
								<div class="table-responsive custom-table">
									<table class="table" id="holiday_type_list">
										<thead class="thead-light">
											<tr>
												<th class="no-sort">
													<label class="checkboxs">
														<input type="checkbox" id="select-all"><span class="checkmarks"></span>
													</label>
												</th>
												<th>Holiday Type</th>
                                                <th>Holiday Code</th>
                                                <th>Is Mandatory</th>
                                                <th>Max yearly Limit</th>
                                                <th>Calculate in calendar if applied only</th>
												<th>Created at</th>
												<th class="no-sort">Action</th>
											</tr>
										</thead>
										<tbody>

										</tbody>
									</table>
								</div>
								<div class="row align-items-center">
									<div class="col-md-6">
										<div class="datatable-length"></div>
									</div>
									<div class="col-md-6">
										<div class="datatable-paginate"></div>
									</div>
								</div>
								<!-- /Roles List -->

							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Add Role -->
		<div class="modal fade" id="add_role" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add Holiday</h5>
                <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <form id="add_holiday" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="col-form-label">Holiday Type <span class="text-danger">*</span></label>
                        <input type="text" name="holiday_type" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">Holiday Code <span class="text-danger">*</span></label>
                        <input type="text" name="holiday_code" class="form-control" required>
                    </div>
                    <div class="mb-3">
    <label class="col-form-label d-block">Is Mandatory <span class="text-danger">*</span></label>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="is_mandatory" id="mandatoryYes" value="Yes" required>
        <label class="form-check-label" for="mandatoryYes">Yes</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="is_mandatory" id="mandatoryNo" value="No">
        <label class="form-check-label" for="mandatoryNo">No</label>
    </div>
</div>

<div class="mb-3">
    <label class="col-form-label d-block">Calculate in calendar if applied only <span class="text-danger">*</span></label>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="calculate_in_calendar" id="calcCalendarYes" value="Yes" required>
        <label class="form-check-label" for="calcCalendarYes">Yes</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="calculate_in_calendar" id="calcCalendarNo" value="No" checked>
        <label class="form-check-label" for="calcCalendarNo">No</label>
    </div>
</div>

<div class="mb-3">
    <label class="col-form-label">Maximum Yearly Limit <span class="text-danger">*</span></label>
    <input type="number" name="max_limit" class="form-control">
</div>

                    <div class="mb-3">
                        <label class="col-form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <div class="d-flex align-items-center justify-content-end m-0">
                        <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

		<!-- /Add Role -->

		<!-- Edit Role -->
		<div class="modal fade" id="edit_holiday_type" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Holiday Type</h5>
                <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="editHolidayTypeForm" method="POST">
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="holiday_type" class="col-form-label">Holiday Type <span class="text-danger">*</span></label>
                        <input type="text" id="holiday_type" name="holiday_type" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="holiday_code" class="col-form-label">Holiday Code <span class="text-danger">*</span></label>
                        <input type="text" id="holiday_code" name="holiday_code" class="form-control" required>
                    </div>

                                        <div class="mb-3">
    <label class="col-form-label d-block">Is Mandatory <span class="text-danger">*</span></label>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="is_mandatory" id="is_mandatory" value="Yes" required>
        <label class="form-check-label" for="mandatoryYes">Yes</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="is_mandatory" id="is_mandatory" value="No">
        <label class="form-check-label" for="mandatoryNo">No</label>
    </div>
</div>

<div class="mb-3">
    <label class="col-form-label d-block">Calculate in calendar if applied only <span class="text-danger">*</span></label>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="calculate_in_calendar" id="edit_calcCalendarYes" value="Yes" required>
        <label class="form-check-label" for="edit_calcCalendarYes">Yes</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="calculate_in_calendar" id="edit_calcCalendarNo" value="No">
        <label class="form-check-label" for="edit_calcCalendarNo">No</label>
    </div>
</div>

<div class="mb-3">
    <label class="col-form-label">Maximum Yearly Limit <span class="text-danger">*</span></label>
    <input type="number" name="max_limit" id="max_limit" class="form-control">
</div>
                    <div class="mb-3">
                        <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center justify-content-end m-0">
                        <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="delete_modal" tabindex="-1" aria-labelledby="delete_modal_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1">Confirm Delete</h4>
                <p class="mb-3" id="delete_modal_message">Are you sure you want to delete this holiday? This action cannot be undone.</p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete_btn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

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

@push('scripts')
<script>
$(document).ready(function () {
    let deleteId = null;
    let searchTimeout;

    // Column Definitions for Manage Columns feature
    const columnDefinitions = [
        { index: 0, name: 'Select', visible: true },
        { index: 1, name: 'Holiday Type', visible: true },
        { index: 2, name: 'Holiday Code', visible: true },
        { index: 3, name: 'Is Mandatory', visible: true },
        { index: 4, name: 'Max yearly Limit', visible: true },
        { index: 5, name: 'Calculate in calendar if applied only', visible: true },
        { index: 6, name: 'Created at', visible: true },
        { index: 7, name: 'Action', visible: true }
    ];

    // Page identifier for column preferences
    const pageIdentifier = 'holiday-type';

    // Load column visibility preferences from database
    function loadColumnPreferences(callback) {
        $.ajax({
            url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug")]) }}',
            type: 'GET',
            data: {
                page_identifier: pageIdentifier
            },
            async: false, // Make synchronous to ensure preferences are loaded before table initialization
            success: function(response) {
                if (response.success && response.data && Array.isArray(response.data) && response.data.length > 0) {
                    try {
                        const preferences = response.data;
                        preferences.forEach(pref => {
                            // Convert string index to number
                            const prefIndex = parseInt(pref.index);
                            const col = columnDefinitions.find(c => c.index === prefIndex);
                            if (col) {
                                // Handle both boolean and string values ("true"/"false")
                                if (typeof pref.visible === 'string') {
                                    col.visible = pref.visible === 'true' || pref.visible === '1';
                                } else {
                                    col.visible = pref.visible !== undefined ? Boolean(pref.visible) : true;
                                }
                            }
                        });
                        if (callback) callback();
                    } catch (e) {
                        console.error('Error loading column preferences:', e);
                        // Fallback: show all columns
                        columnDefinitions.forEach(col => col.visible = true);
                        if (callback) callback();
                    }
                } else {
                    // No preferences found or empty response - show all columns by default
                    columnDefinitions.forEach(col => col.visible = true);
                    if (callback) callback();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading column preferences:', error);
                // Fallback: show all columns
                columnDefinitions.forEach(col => col.visible = true);
                if (callback) callback();
            }
        });
    }

    // Save column preferences to database
    function saveColumnPreferences() {
        $.ajax({
            url: '{{ route("settings.column.preferences.save", ["box_slug" => request()->route("box_slug")]) }}',
            type: 'POST',
            data: {
                page_identifier: pageIdentifier,
                column_preferences: columnDefinitions,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    console.log('Column preferences saved successfully');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error saving column preferences:', error);
            }
        });
    }

    // Global table variable
    let table;

    // Populate columns modal
    function populateColumnsModal() {
        const columnsList = $('#columnsList');
        columnsList.empty();

        columnDefinitions.forEach(col => {
            const listItem = $(`
                <div class="list-group-item">
                    <div class="form-check">
                        <input class="form-check-input column-toggle" type="checkbox"
                               data-column-index="${col.index}"
                               id="col_${col.index}"
                               ${col.visible ? 'checked' : ''}>
                        <label class="form-check-label" for="col_${col.index}">
                            ${col.name}
                        </label>
                    </div>
                </div>
            `);
            columnsList.append(listItem);
        });
    }

    // Load preferences before initializing table
    loadColumnPreferences(function() {
        table = $('#holiday_type_list').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('holiday.fetch', ['box_slug' => request()->route('box_slug')]) }}",
            dataSrc: 'data'
        },
        columns: [
            {
                data: 'id',
                visible: columnDefinitions[0].visible,
                render: function (data) {
                    return `<label class="checkboxs"><input type="checkbox" class="select-checkbox" value="${data}"><span class="checkmarks"></span></label>`;
                },
                orderable: false,
                searchable: false
            },

            { data: 'holiday_type', name: 'holiday_type', visible: columnDefinitions[1].visible },
            {data: 'holiday_code', name:'holiday_code', visible: columnDefinitions[2].visible},
            { data: 'is_mandatory', name: 'is_mandatory', visible: columnDefinitions[3].visible },
            {data:'max_limit', name:'max_limit', visible: columnDefinitions[4].visible},
            { data: 'calculate_in_calendar', name: 'calculate_in_calendar', visible: columnDefinitions[5].visible },
            { data: 'created_at', name: 'created_at', visible: columnDefinitions[6].visible },

            {
                data: 'id',
                visible: columnDefinitions[7].visible,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-sm btn-warning edit-btn"
                            data-id="${data}"
                            data-type="${row.holiday_type}"
                            data-code="${row.holiday_code}"
                            data-is_mandatory="${row.is_mandatory}"
                            data-max_limit="${row.max_limit}"
                            data-calculate_in_calendar="${row.calculate_in_calendar || 'No'}"
                            data-status="${row.status}">
                            Edit
                        </button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">
                            Delete
                        </button>
                    `;
                },
                orderable: false,
                searchable: false
            }
        ]
    });
    }); // End of loadColumnPreferences callback

    // Debounce DataTable Search (300ms)
    $('#holiday_type_list_filter input[type="search"]').off().on('keyup', function () {
        clearTimeout(searchTimeout);
        const searchTerm = this.value;
        searchTimeout = setTimeout(() => {
            table.search(searchTerm).draw();
        }, 300);
    });;

    // Add Holiday Form Submit
    $('#add_holiday').submit(function (e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: "{{ route('HolidayStore', ['box_slug' => request()->route('box_slug')]) }}",
            method: "POST",
            data: formData,
            success: function (response) {
                $('#add_role').modal('hide');
                $('#add_holiday')[0].reset();
                table.ajax.reload();
                showToast(response.message || 'Holiday Type created successfully', 'success');
            },
            error: function (xhr) {
                const errors = xhr.responseJSON.errors;
                let message = 'Validation error';
                if (errors) {
                    message = Object.values(errors).flat().join(', ');
                }
                showToast(message, 'danger');
            }
        });
    });

    // Edit Button Click
    $(document).on('click', '.edit-btn', function () {
    const id = $(this).data('id');
    const isMandatory = $(this).data('is_mandatory');
    const calcCalendar = $(this).data('calculate_in_calendar') || 'No';

    $('#editHolidayTypeForm').attr('data-id', id);
    $('#editHolidayTypeForm #holiday_type').val($(this).data('type'));
    $('#editHolidayTypeForm #holiday_code').val($(this).data('code'));

    // Set the appropriate radio button as checked
    $('#editHolidayTypeForm input[name="is_mandatory"][value="' + isMandatory + '"]').prop('checked', true);
    $('#editHolidayTypeForm input[name="calculate_in_calendar"][value="' + calcCalendar + '"]').prop('checked', true);

    $('#editHolidayTypeForm #max_limit').val($(this).data('max_limit'));
    $('#editHolidayTypeForm #status').val($(this).data('status'));

    $('#edit_holiday_type').modal('show');
});


    // Edit Form Submit
    $('#editHolidayTypeForm').submit(function (e) {
        e.preventDefault();
        const id = $(this).attr('data-id');
        const formData = $(this).serialize() + '&id=' + id;

        $.ajax({
            url: "{{ route('holidayUpdate', ['box_slug' => request()->route('box_slug')]) }}",
            method: "PUT",
            data: formData,
            success: function (response) {
                $('#edit_holiday_type').modal('hide');
                table.ajax.reload();
                showToast(response.message || 'Holiday Type updated successfully', 'success');
            },
            error: function (xhr) {
                const errors = xhr.responseJSON.errors;
                let message = 'Validation error';
                if (errors) {
                    message = Object.values(errors).flat().join(', ');
                }
                showToast(message, 'danger');
            }
        });
    });

    // Delete Button Click - Show Modal
    $(document).on('click', '.delete-btn', function () {
        deleteId = $(this).data('id');
        $('#delete_modal').modal('show');
    });

    // Confirm Delete
    $('#confirm_delete_btn').on('click', function () {
        if (!deleteId) return;

        $.ajax({
            url: "{{ route('holidayDelete', ['box_slug' => request()->route('box_slug')]) }}",
            method: "DELETE",
            data: {
                id: deleteId,
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                $('#delete_modal').modal('hide');
                table.ajax.reload();
                showToast(response.message || 'Holiday Type deleted successfully', 'success');
                deleteId = null;
            },
            error: function (xhr) {
                $('#delete_modal').modal('hide');
                let message = 'Something went wrong';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showToast(message, 'danger');
                deleteId = null;
            }
        });
    });

    // Toast Function
    function showToast(message, type = 'success') {
        const toast = new bootstrap.Toast(document.getElementById('dynamicToast'));
        const toastEl = $('#dynamicToast');

        const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';

        toastEl.removeClass('bg-success bg-danger').addClass(bgClass);
        $('#dynamicToastMessage').text(message);

        toast.show();
    }

    // Open Manage Columns Modal
    $('#manageColumnsModal').on('show.bs.modal', function() {
        populateColumnsModal();
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
        $('.column-toggle').each(function() {
            const columnIndex = parseInt($(this).data('column-index'));
            const isVisible = $(this).is(':checked');
            const col = columnDefinitions.find(c => c.index === columnIndex);
            if (col) {
                col.visible = isVisible;
            }
        });

        saveColumnPreferences();

        // Apply column visibility and redraw table immediately
        if (table) {
            columnDefinitions.forEach(col => {
                try {
                    table.column(col.index).visible(col.visible, false);
                } catch (e) {
                    console.error('Error applying column visibility:', e);
                }
            });
            table.columns.adjust();
            // Redraw only once after all changes
            table.draw(false);
        }

        // Close modal
        $('#manageColumnsModal').modal('hide');
    });
});
</script>
@endpush



@endsection
