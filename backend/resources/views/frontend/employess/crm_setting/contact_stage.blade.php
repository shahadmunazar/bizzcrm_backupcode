@extends('layouts.main')
@section('content')
@section('title','Lead Status')
	<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-sm-8">
									<h4 class="page-title">Lead Status <span class="count-title" id="contact-id">0</span></h4>
								</div>
								<div class="col-sm-4 text-sm-end">
									<div class="head-icons">
										<a href="contact-stage.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
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
										<div class="icon-form mb-3 mb-sm-0">
											<span class="form-icon"><i class="ti ti-search"></i></span>
											<input type="text" class="form-control" id="leadSeachInput" placeholder="Search Contact Stages">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="text-sm-end">
											<button type="button" class="btn btn-sm btn-white d-inline-flex align-items-center me-2" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
												<i class="ti ti-columns me-1"></i>Manage Columns
											</button>
											<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#global_add_contact_stage"><i class="ti ti-square-rounded-plus me-2"></i>Add Lead Status</a>
										</div>
									</div>
								</div>
								<!-- /Search -->
							</div>
							<div class="card-body">
								<!-- Contact Stage List -->
								<div class="table-responsive custom-table">
									<table class="table" id="contact-stage">
										<thead class="thead-light">
											<tr>
												<th class="no-sort">
													<label class="checkboxs">
														<input type="checkbox" id="select-all"><span class="checkmarks"></span>
													</label>
												</th>
												<th>Name</th>
												<th>Color Code</th>
												<th>Is Default</th>
												<th>Is Final</th>
												<th>Display Amount Field</th>
												<th>Notification</th>
												<th>Reminder Mandatory</th>
												<th>No Activity Alert</th>
												<th>Approval</th>
												<th>Department</th>
												<th>Designation</th>
												<th>Added On</th>
												<th>Actions</th>
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
								<!-- /Contact Stage List -->

							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Global Add Contact Stage Modal Included in Header -->

		<!-- Edit Contact Stage -->
		<div class="modal fade" id="edit_contact_stage" role="dialog">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Contact Stage</h5>
        <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
          <i class="ti ti-x"></i>
        </button>
      </div>
      <form id="editContactStageForm">
        <div class="modal-body">
          <input type="hidden" id="editContactStageId">
          <div class="mb-3">
            <label class="col-form-label">Contact Stage <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="editContactStageName">
          </div>
          <div class="mb-3">
            <label class="col-form-label">Color</label>
            <input type="color" class="form-control form-control-color" id="editContactStageColor" value="#000000" title="Choose color">
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="edit_is_task_priority" value="1">
              <label class="form-check-label" for="edit_is_task_priority">
                Create Task with Priority
              </label>
            </div>
          </div>
          <div class="mb-3" id="edit_priority_wrapper" style="display: none;">
            <div class="d-flex justify-content-between align-items-center">
              <label class="col-form-label">Task Priority</label>
              <a href="javascript:void(0);" class="fs-12 select-all-priorities" data-target="editContactStagePriority">Select All</a>
            </div>
            <select class="form-control select2-priority" id="editContactStagePriority" multiple="multiple">
              <option value="Low">Low</option>
              <option value="Medium">Medium</option>
              <option value="High">High</option>
            </select>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="col-form-label">Priority</label>
              <input type="number" class="form-control" id="editPriority" value="0">
            </div>
            <div class="col-md-6 mb-3">
              <label class="col-form-label">Reminder Mandatory</label>
              <select class="form-select" id="editReminderMandatory">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="col-form-label">Notification</label>
              <select class="form-select" id="editNotification">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="col-form-label">Is Default</label>
              <select class="form-select" id="editIsDefault">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="col-form-label">Is Final</label>
              <select class="form-select" id="editIsFinal">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="col-form-label">Display Amount Field</label>
              <select class="form-select" id="editDisplayAmountField">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="col-form-label">No Activity Alert</label>
              <div class="input-group">
                <input type="number" class="form-control" id="editNoActivityAlert" value="0">
                <select class="form-select" id="editNoActivityAlertUnit" style="max-width: 100px;">
                  <option value="Days">Days</option>
                  <option value="Hours">Hours</option>
                  <option value="Minutes">Minutes</option>
                </select>
              </div>
            </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="col-form-label">Requires Approval</label>
                  <select class="form-select" id="editRequiresApproval">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                  </select>
                </div>
            </div>
            <div class="row" id="editApprovalFilters" style="display: none;">
                <div class="col-md-6 mb-3">
                    <label class="col-form-label">Approval Department</label>
                    <select class="form-select" id="editApprovalDepartment" name="approval_department_id">
                        <option value="">Select Department</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="col-form-label">Approval Designation</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="editApprovalDesignation_visible" placeholder="Select Designation" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                        <input type="hidden" id="editApprovalDesignation" name="approval_designation_id">
                        <button class="btn btn-outline-secondary" type="button" onclick="if($('#editApprovalDepartment').val()) { window.openDesignationTree($('#editApprovalDepartment').val(), '#editApprovalDesignation'); } else { showToast('Please select a department first', 'error'); }"><i class="ti ti-sitemap"></i> Select</button>
                    </div>
                </div>
            </div>
          <div class="mb-0">
            <label class="col-form-label">Status</label>
            <div class="d-flex flex-wrap radio-wrap">
              <div class="radio-btn">
                <input type="radio" class="status-radio" id="edit-stage-active" name="status" value="1">
                <label for="edit-stage-active">Active</label>
              </div>
              <div class="radio-btn">
                <input type="radio" class="status-radio" id="edit-stage-inactive" name="status" value="0">
                <label for="edit-stage-inactive">Inactive</label>
              </div>
            </div>
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

		<!-- /Edit Contact Stage -->

		<!-- Delete Contact Stage -->
		<div class="modal fade" id="delete_contact_stage" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
                    <input type="hidden" id="deleteSourceId" />
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Contact Stage?</h4>
							<p class="mb-0">AAre you sure you want to remove it.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Yes, Delete it</button>
                    </div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Contact Stage -->



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
                const activityTypeUrlBase = "{{ route('app.settings.leads.leads_sub_status',['box_slug' => request()->route('box_slug')]) }}";

    let sourceTable;

    // Get route URL for source listing
    function getSourceRouteUrl() {
        return "{{ route('get.contact-stage', ['box_slug' => request()->route('box_slug')]) }}";
    }

    // Reload DataTable without resetting pagination
    function reloadSourceTable() {
        if (sourceTable) {
            sourceTable.ajax.reload(null, false);
        }
    }

    // Show Bootstrap toast messages
    function showToast(message, type = 'success') {
        const $toast = $('#dynamicToast');
        const $message = $('#dynamicToastMessage');

        $message.text(message);
        $toast.removeClass('bg-success bg-danger bg-warning');

        if (type === 'success') $toast.addClass('bg-success');
        else if (type === 'error') $toast.addClass('bg-danger');
        else $toast.addClass('bg-warning');

        new bootstrap.Toast($toast[0]).show();
    }

        const columnDefinitions = [
            { index: 0, name: 'Checkbox', visible: true },
            { index: 1, name: 'Name', visible: true },
            { index: 2, name: 'Color Code', visible: true },
            { index: 3, name: 'Is Default', visible: true },
            { index: 4, name: 'Is Final', visible: true },
            { index: 5, name: 'Display Amount Field', visible: true },
            { index: 6, name: 'Notification', visible: true },
            { index: 7, name: 'Reminder Mandatory', visible: true },
            { index: 8, name: 'No Activity Alert', visible: true },
            { index: 9, name: 'Approval', visible: true },
            { index: 10, name: 'Department', visible: true },
            { index: 11, name: 'Designation', visible: true },
            { index: 12, name: 'Added On', visible: true },
            { index: 13, name: 'Actions', visible: true }
        ];

        // Page identifier for column preferences
        const pageIdentifier = 'contact-stage';

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

    $(document).ready(function () {

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
                if (sourceTable) {
                    columnDefinitions.forEach(col => {
                        try {
                            sourceTable.column(col.index).visible(col.visible, false);
                        } catch (e) {
                            console.error('Error applying column visibility:', e);
                        }
                    });
                    sourceTable.columns.adjust();
                    sourceTable.draw(false);
                }

                // Close modal
                $('#manageColumnsModal').modal('hide');
            });

 if ($('#contact-stage').length) {
    // Inject Laravel route URL into JS if in Blade, else adjust your URL accordingly
    const activityTypeUrlBase = "{{ route('app.settings.leads.leads_sub_status', ['box_slug' => request()->route('box_slug')]) }}";

    loadColumnPreferences(function() {
        sourceTable = $('#contact-stage').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: getSourceRouteUrl(),  // Your route to fetch data
                type: 'GET',
                data: function(d) {
                    // Use external input as the search value for DataTables server-side search param
                    d.search.value = $('#leadSeachInput').val();
                },
                error: function(xhr) {
                    console.error("DataTable Load Error:", xhr.responseText);
                }
            },
            order: [[1, 'desc']],
            ordering: true,
            bFilter: false, // disable default DataTable search box
            bInfo: false,
            language: {
                search: " ",
                searchPlaceholder: "Search",
                sLengthMenu: "MENU",
                lengthMenu: "Show MENU entries",
                info: "START - END of TOTAL items",
                paginate: {
                    next: 'Next <i class="fa fa-angle-right"></i>',
                    previous: '<i class="fa fa-angle-left"></i> Prev'
                }
            },
            initComplete: function() {
                $(".dataTables_paginate").appendTo(".datatable-paginate");
                $(".dataTables_length").appendTo(".datatable-length");
            },
            drawCallback: function(settings) {
                const api = this.api();
                const totalRecords = api.page.info().recordsTotal;
                $('#contact-id').text(totalRecords);
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    visible: columnDefinitions[0].visible,
                    render: () => '<label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label>'
                },
                {
                    data: 'name',
                    visible: columnDefinitions[1].visible,
                    render: data => `<span class="title-name">${data}</span>`
                },
                {
                    data: 'color',
                    visible: columnDefinitions[2].visible,
                    render: data => data ? `<div style="width: 40px; height: 15px; background-color: ${data}; border-radius: 4px; display: inline-block;"></div>` : '-'
                },
                { data: 'is_default', visible: columnDefinitions[3].visible },
                { data: 'is_final', visible: columnDefinitions[4].visible },
                { data: 'display_amount_field', visible: columnDefinitions[5].visible },
                { data: 'notification', visible: columnDefinitions[6].visible },
                { data: 'reminder_mandatory', visible: columnDefinitions[7].visible },
                { 
                    data: 'no_activity_alert', 
                    visible: columnDefinitions[8].visible,
                    render: (data, type, row) => {
                        return (data || 0) + ' ' + (row.no_activity_alert_unit || 'Days');
                    }
                },
                {
                    data: 'requires_approval_badge',
                    visible: columnDefinitions[9].visible
                },
                {
                    data: 'department_name',
                    visible: columnDefinitions[10].visible
                },
                {
                    data: 'designation_name',
                    visible: columnDefinitions[11].visible
                },
                {
                    data: 'created_at',
                    visible: columnDefinitions[12].visible,
                    render: data => {
                        const d = new Date(data);
                        const options = {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        };
                        return d.toLocaleDateString('en-GB', options) + ' ' + d.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}); // Format as shown in screenshot approximately
                    }
                },
                {
                    data: null,
                    orderable: false,
                    visible: columnDefinitions[13].visible,
                    render: (data, type, row) => {
                        const activityTypeUrl = `${activityTypeUrlBase}?lead_status_id=${row.id}`;
                        return `
                                    <a href="#" class="btn btn-sm btn-icon btn-warning edit_contact_stage-btn"
                                       data-id="${row.id}"
                                       data-title="${row.name}" 
                                       data-status="${row.status}"
                                       data-color="${row.color || ''}"
                                       data-is_priority="${row.is_task_priority}"
                                       data-priority='${JSON.stringify(row.task_priority || [])}'
                                       data-reminder_mandatory="${row.reminder_mandatory}"
                                       data-notification="${row.notification}"
                                       data-priority_val="${row.priority}"
                                       data-is_default="${row.is_default}"
                                       data-is_final="${row.is_final}"
                                       data-display_amount_field="${row.display_amount_field}"
                                       data-no_activity_alert="${row.no_activity_alert || 0}"
                                       data-no_activity_alert_unit="${row.no_activity_alert_unit || 'Days'}"
                                       data-requires_approval="${row.requires_approval || 0}"
                                       data-approval_department_id="${row.approval_department_id || ''}"
                                       data-approval_designation_id="${row.approval_designation_id || ''}"
                                       data-bs-toggle="modal"
                                       data-bs-target="#edit_contact_stage">
                                       <i class="ti ti-edit text-white"></i>
                                    </a>`;
                    }
                }
            ]
        });
    });

    // Debounced search input to reduce number of AJAX calls while typing
    let debounceTimer;
    $('#leadSeachInput').on('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            if (sourceTable) sourceTable.draw();
        }, 300);
    });

    // Toggle Priority Wrappers
    $(document).on('change', '#add_is_task_priority', function() {
        if ($(this).is(':checked')) {
            $('#add_priority_wrapper').slideDown();
        } else {
            $('#add_priority_wrapper').slideUp();
        }
    });

    $(document).on('change', '#edit_is_task_priority', function() {
        if ($(this).is(':checked')) {
            $('#edit_priority_wrapper').slideDown();
        } else {
            $('#edit_priority_wrapper').slideUp();
        }
    });

    $(document).on('change', '#editRequiresApproval', function() {
        if ($(this).val() == '1') {
            $('#editApprovalFilters').slideDown();
        } else {
            $('#editApprovalFilters').slideUp();
        }
    });

    $(document).on('change', '#global_add_requires_approval', function() {
        if ($(this).val() == '1') {
            $('#global_add_approval_filters').slideDown();
        } else {
            $('#global_add_approval_filters').slideUp();
        }
    });

    // Fetch Departments for Add Modal
    $('#global_add_contact_stage').on('shown.bs.modal', function () {
        $.ajax({
            url: "{{ route('department.manage', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
            method: 'GET',
            success: function(response) {
                if (response.status === 200 && response.data) {
                    const $deptSelect = $('#global_add_approval_department');
                    $deptSelect.empty().append('<option value="">Select Department</option>');
                    window.teamOptions = window.teamOptions || {};
                    response.data.forEach(dept => {
                        window.teamOptions[dept.id] = dept.all_teams || [];
                        $deptSelect.append(`<option value="${dept.id}">${dept.name} (Dept)</option>`);
                    });
                    $deptSelect.trigger('change');
                }
            }
        });
    });

    $('#edit_contact_stage').on('shown.bs.modal', function () {
        if ($('#editContactStagePriority').length) {
            $('#editContactStagePriority').select2({
                placeholder: "Select Priorities",
                width: '100%',
                allowClear: true,
                dropdownParent: $('#edit_contact_stage')
            });
        }
        
        $.ajax({
            url: "{{ route('department.manage', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
            method: 'GET',
            success: function(response) {
                if (response.status === 200 && response.data) {
                    const $deptSelect = $('#editApprovalDepartment');
                    const currentVal = $deptSelect.data('selected-id');
                    $deptSelect.empty().append('<option value="">Select Department</option>');
                    window.teamOptions = window.teamOptions || {};
                    response.data.forEach(dept => {
                        window.teamOptions[dept.id] = dept.all_teams || [];
                        $deptSelect.append(`<option value="${dept.id}">${dept.name} (Dept)</option>`);
                    });
                    if (currentVal) {
                        $deptSelect.val(currentVal).trigger('change');
                    }
                }
            }
        });
    });

    // Initialize Select2 for multi-select
    if ($('.select2-priority').length) {
        $('.select2-priority').select2({
            placeholder: "Select Priorities",
            width: '100%',
            allowClear: true
        });
    }

    // Select All Priorities Logic
    $(document).on('click', '.select-all-priorities', function() {
        const targetId = $(this).data('target');
        const $select = $('#' + targetId);
        const allValues = $select.find('option').map(function() { return $(this).val(); }).get();
        
        if ($select.val() && $select.val().length === allValues.length) {
            $select.val([]).trigger('change');
            $(this).text('Select All');
        } else {
            $select.val(allValues).trigger('change');
            $(this).text('Deselect All');
        }
    });
}




        // Add Contact Stage form submission
        $('#add_contact_stageForm').on('submit', function (e) {
    e.preventDefault();
    const $form = $(this);

    const url = "{{ route('store.source', ['box_slug' => request()->route('box_slug')]) }}";
    const data = {
        name: $form.find('input[name="stage"]').val().trim(),
        status: $('#add-active').is(':checked') ? 1 : 0,
        type: 'contact_stage',
        color: $form.find('input[name="color"]').val(),
        is_task_priority: $('#add_is_task_priority').is(':checked') ? 1 : 0,
        task_priority: $('#add_is_task_priority').is(':checked') ? $form.find('select[name="task_priority[]"]').val() : null,
        reminder_mandatory: $form.find('select[name="reminder_mandatory"]').val(),
        notification: $form.find('select[name="notification"]').val(),
        priority: $form.find('input[name="priority"]').val(),
        is_default: $form.find('select[name="is_default"]').val(),
        is_final: $form.find('select[name="is_final"]').val(),
        display_amount_field: $form.find('select[name="display_amount_field"]').val(),
        no_activity_alert: $form.find('input[name="no_activity_alert"]').val(),
        no_activity_alert_unit: $form.find('select[name="no_activity_alert_unit"]').val(),
        requires_approval: $('#global_add_requires_approval').val(),
        approval_department_id: $('#global_add_approval_department').val(),
        approval_designation_id: $('#global_add_approval_designation').val(),
    };

    $.ajax({
        url,
        type: 'POST',
        data,
        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
        success: function (response) {
            if (response.success) {
                $('#global_add_contact_stage').modal('hide');
                $form[0].reset();
                showToast(response.message || 'Contact stage added!', 'success');
                reloadSourceTable();
            }
        },
        error: function (xhr) {
            const res = xhr.responseJSON;
            if (xhr.status === 422 && res?.errors) {
                const messages = Object.values(res.errors).flat().join(' ');
                showToast(messages, 'error');
            } else {
                showToast(res?.message || 'Failed to add contact stage.', 'error');
            }
        }
    });
});

        // Populate Edit Modal
        $(document).on('click', '.edit_contact_stage-btn', function () {
            $('#editContactStageId').val($(this).data('id'));
            $('#editContactStageName').val($(this).data('title'));
            $('#editContactStageColor').val($(this).data('color') || '#000000');
            const isPriority = $(this).data('is_priority') == 1;
            $('#edit_is_task_priority').prop('checked', isPriority).trigger('change');
            
            if (isPriority) {
                let priorities = $(this).data('priority');
                if (typeof priorities === 'string' && (priorities.startsWith('[') || priorities.startsWith('{'))) {
                    try { priorities = JSON.parse(priorities); } catch(e) {}
                }
                $('#editContactStagePriority').val(priorities).trigger('change');
            } else {
                $('#editContactStagePriority').val([]).trigger('change');
            }
            $('#editReminderMandatory').val($(this).data('reminder_mandatory'));
            $('#editNotification').val($(this).data('notification'));
            $('#editPriority').val($(this).data('priority_val'));
            $('#editIsDefault').val($(this).data('is_default'));
            $('#editIsFinal').val($(this).data('is_final'));
            $('#editDisplayAmountField').val($(this).data('display_amount_field'));
            $('#editNoActivityAlert').val($(this).data('no_activity_alert'));
            $('#editNoActivityAlertUnit').val($(this).data('no_activity_alert_unit') || 'Days');
            $('#editRequiresApproval').val($(this).data('requires_approval') ? 1 : 0).trigger('change');
            
            const approvalDepartmentId = $(this).data('approval_department_id');
            const approvalDesignationId = $(this).data('approval_designation_id');
            
            if(approvalDepartmentId) {
                $('#editApprovalDepartment').data('selected-id', approvalDepartmentId);
                $('#editApprovalDepartment').val(approvalDepartmentId).trigger('change');
                // The name for designation would ideally be fetched, but we can set the hidden id at least
                $('#editApprovalDesignation').val(approvalDesignationId);
                $('#editApprovalDesignation_visible').val(approvalDesignationId ? 'Designation Selected' : '');
            } else {
                $('#editApprovalDepartment').data('selected-id', '');
                $('#editApprovalDepartment').val('').trigger('change');
                $('#editApprovalDesignation').val('');
                $('#editApprovalDesignation_visible').val('');
            }

            $(`#edit_contact_stage input[name="status"][value="${$(this).data('status')}"]`).prop('checked', true);
        });

        // Edit Contact Stage form submission
        $('#editContactStageForm').on('submit', function (e) {
            e.preventDefault();

            const id = $('#editContactStageId').val();
            const name = $('#editContactStageName').val().trim();
            const status = $('#edit_contact_stage input[name="status"]:checked').val();
            const color = $('#editContactStageColor').val();
            const is_task_priority = $('#edit_is_task_priority').is(':checked') ? 1 : 0;
            const task_priority = is_task_priority ? $('#editContactStagePriority').val() : null;
            const reminder_mandatory = $('#editReminderMandatory').val();
            const notification = $('#editNotification').val();
            const priority = $('#editPriority').val();
            const is_default = $('#editIsDefault').val();
            const is_final = $('#editIsFinal').val();
            const display_amount_field = $('#editDisplayAmountField').val();
            const no_activity_alert = $('#editNoActivityAlert').val();
            const no_activity_alert_unit = $('#editNoActivityAlertUnit').val();
            const requires_approval = $('#editRequiresApproval').val();
            const approval_department_id = $('#editApprovalDepartment').val();
            const approval_designation_id = $('#editApprovalDesignation').val();
            const type = 'contact_stage';

            $.ajax({
                url: "{{ route('source.update', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'POST',
                data: {
                    id,
                    name,
                    type,
                    status,
                    color,
                    is_task_priority,
                    task_priority,
                    reminder_mandatory,
                    notification,
                    priority,
                    is_default,
                    is_final,
                    display_amount_field,
                    no_activity_alert,
                    no_activity_alert_unit,
                    requires_approval,
                    approval_department_id,
                    approval_designation_id,
                    _token: $('meta[name="csrf-token"]').attr("content")
                },
                success: function (res) {
                    $('#edit_contact_stage').modal('hide');
                    if (res.success) {
                        showToast(res.message || 'Contact stage updated successfully!', 'success');
                        reloadSourceTable();
                    } else {
                        showToast(res.message || 'Update failed.', 'error');
                    }
                },
                error: function () {
                    showToast('Error updating contact stage.', 'error');
                }
            });
        });

        // Populate Delete Modal
        $(document).on('click', '.delete_reason-btn', function () {
    const id = $(this).data('id');
    $('#deleteSourceId').val(id);
});

// Confirm Deletion
$('#confirmDeleteBtn').on('click', function () {
    const id = $('#deleteSourceId').val();
    const type = 'contact_stage';
    if (!id) {
        showToast('Invalid contact stage ID.', 'error');
        return;
    }
    $.ajax({
        url: "{{ route('source.delete', ['box_slug' => request()->route('box_slug')]) }}",
        method: 'DELETE',
        data: {
            id: id,
            type:type,
            _token: $('meta[name="csrf-token"]').attr("content")
        },
        success: function (res) {
            if (res.success) {
                showToast(res.message || 'Contact stage deleted successfully.', 'success');
                $('#delete_contact_stage').modal('hide');
                reloadSourceTable();
            } else {
                showToast(res.message || 'Delete failed.', 'error');
            }
        },
        error: function () {
            showToast('Server error while deleting.', 'error');
        }
    });
});


        // Toggle Status
        $(document).on('change', '.toggle-status', function () {
            const id = $(this).data("id");
            const status = $(this).is(":checked") ? 1 : 0;

            $.ajax({
                url: "{{ route('source.delete', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'POST',
                data: {
                    id,
                    status,
                    _token: $('meta[name="csrf-token"]').attr("content")
                },
                success: function (res) {
                    if (res.success) {
                        showToast(res.message || 'Status updated.', 'success');
                        reloadSourceTable();
                    } else {
                        showToast(res.message || 'Toggle failed.', 'warning');
                    }
                },
                error: function (err) {
                    console.error('Toggle error:', err.responseText);
                    showToast('Toggle AJAX error. Please try again.', 'error');
                }
            });
        });

    });
</script>
@endpush
@endsection
