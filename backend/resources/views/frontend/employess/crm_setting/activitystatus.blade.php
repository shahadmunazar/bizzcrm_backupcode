@extends('layouts.main')
@section('content')
@section('title','Activity Status')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content">

            <div class="row">
                <div class="col-md-12">

                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col-sm-8">
                                <h4 class="page-title">Activity Status </h4>
                            </div>
                            <div class="col-sm-4 text-sm-end">
                                <div class="head-icons">
                                    <a href="{{ box_route('app.settings.calls') }}" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-original-title="Activity Settings"><i class="ti ti-settings"></i></a>
                                    <a href="{{ box_route('activitystatus.show') }}" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
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
                                        <input type="text" class="form-control" placeholder="Search Activity">
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="text-sm-end">

                                        <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#add_calls"><i class="ti ti-square-rounded-plus me-2"></i>Add
                                            New</a>


                                    </div>





                                </div>
                            </div>
                            <!-- /Search -->
                        </div>
                        <div class="card-body">





                            <!-- Calls List -->
                            <div class="table-responsive custom-table">
                                <table class="table" id="activity-status-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="no-sort">
                                                <label class="checkboxs">
                                                    <input type="checkbox" id="select-all"><span class="checkmarks"></span>
                                                </label>
                                            </th>
                                            <th>Activity Status</th>
                                            <th>Activity Setting</th>
                                            <th>Activity Type</th>
                                            <th>Created at</th>
                                            <th>Status</th>
                                            <th>Action</th>
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
                            <!-- /Calls List -->

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <!-- /Page Wrapper -->

    <!-- Add New Calls -->
    <div class="modal fade" id="add_calls" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Activity Status</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="AddActivityStatus" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="col-form-label">Activity Status <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title">
                        </div>
                        <div class="mb-0">
                            <label class="col-form-label">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <input type="radio" class="status-radio" id="add-active" value="1" name="status"
                                        checked="">
                                    <label for="add-active">Active</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="add-inactive" value="0"
                                        name="status">
                                    <label for="add-inactive">Inactive</label>
                                </div>
                            </div>
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
    <!-- /Add New Calls -->

    <!-- Edit Calls -->
    <div class="modal fade" id="status_edit" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Activity Status</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="EditActivityForm" method="POST">
                    @csrf
                    <input type="hidden" id="editActivtityStatus" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="col-form-label">Activity Status <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editActivitiyStatusName" name="title">
                        </div>
                        <div class="mb-0">
                            <label class="col-form-label">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <input type="radio" class="status-radio" id="edit-active" name="status"
                                        value="1">
                                    <label for="edit-active">Active</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="edit-inactive" name="status"
                                        value="0">
                                    <label for="edit-inactive">Inactive</label>
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

    <!-- /Edit Calls -->

    <!-- Delete Calls -->
    {{-- <div class="modal fade" id="delete_calls" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Activity Status?</h4>
							<p class="mb-0">Are you sure you want to remove it.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="calls.html" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> --}}
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="delete_calls" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <input type="hidden" id="deleteActivityType"> <!-- ✅ Correct ID -->
                <div class="modal-body">
                    <div class="text-center">
                        <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                            <i class="ti ti-trash-x fs-36 text-danger"></i>
                        </div>
                        <h4 class="mb-2">Remove Activity Status?</h4>
                        <p class="mb-0">Are you sure you want to remove it?</p>
                        <div class="d-flex align-items-center justify-content-center mt-4">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="button" class="btn btn-danger" id="confirmdeleteActivityType">
                                Yes, Delete it
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- /Delete Calls -->

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
            const activityTypeUrlBase = "{{ route('activitystatus.show', ['box_slug' => request()->route('box_slug')]) }}";

            const ActivityManager = {
                sourceTable: null,

                init() {
                    if (!this.validateTable()) return;
                    this.bindEvents();
                    this.initDataTable();
                },

                validateTable() {
                    const tableEl = $('#activity-status-table');
                    return tableEl.length > 0 && tableEl.prop('tagName') === 'TABLE';
                },

                getRouteUrl() {
                    const activityId = new URLSearchParams(window.location.search).get('activity_type_id') || '';
                    return `{{ route('getActivtiyStatus', ['box_slug' => request()->route('box_slug')]) }}?activity_type_id=${activityId}`;
                },

                reloadTable() {
                    if (this.sourceTable) {
                        this.sourceTable.ajax.reload(null, false);
                    }
                },

                showToast(message, type = 'success') {
                    const $toast = $('#dynamicToast');
                    const $message = $('#dynamicToastMessage');
                    $message.text(message);
                    $toast.removeClass('bg-success bg-danger bg-warning')
                        .addClass(type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-warning');
                    new bootstrap.Toast($toast[0]).show();
                },

                bindEvents() {
                    let debounceTimer;
                    $('#ActiviySearchInput').on('input', () => {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() => this.reloadTable(), 300);
                    });

                    $('#AddActivityStatus').on('submit', this.handleAddFormSubmit.bind(this));
                    $('#EditActivityForm').on('submit', this.handleEditFormSubmit.bind(this));

                    $(document).on('click', '.status_edit-btn', this.populateEditModal);
                    $(document).on('click', '.delete_reason-btn', function() {
                        $('#deleteActivityType').val($(this).data('id'));
                    });
                    $('#confirmdeleteActivityType').on('click', this.handleDelete.bind(this));
                },

                initDataTable() {
                    const tableEl = $('#activity-status-table');
                    if ($.fn.DataTable.isDataTable(tableEl)) {
                        tableEl.DataTable().clear().destroy();
                    }

                    this.sourceTable = tableEl.DataTable({
                        ajax: {
                            url: this.getRouteUrl(),
                            data: d => {
                                d.search = $('#ActiviySearchInput').val();
                            },
                            dataSrc: json => json.success ? json.data : [],
                            error: xhr => {
                                console.error("DataTable Error:", xhr.responseText);
                                this.showToast("Failed to load data.", "error");
                            }
                        },
                        ordering: true,
                        bFilter: false,
                        bInfo: false,
                        columns: [{
                                render: () => '<input type="checkbox" class="select-row">',
                                orderable: false
                            },
                            {
                                data: "title",
                                render: data => `<span class="title-name">${data}</span>`
                            },
                            {
                                data: "setting_name",
                                render: data => `<span class="badge bg-soft-info text-info">${data || '---'}</span>`
                            },
                            {
                                data: "type_name",
                                render: data => `<span class="badge bg-soft-warning text-warning">${data || '---'}</span>`
                            },
                            {
                                data: "status",
                                render: data => `<span class="badge badge-pill ${data == 1 ? "bg-success" : "bg-danger"}">
                            ${data == 1 ? "Active" : "Inactive"}
                        </span>`
                            },
                            {
                                orderable: false,
                                render: (data, type, row) => {
                                    const viewUrl =
                                        `${activityTypeUrlBase}?activity_type_id=${row.activity_setting_id}`;
                                    return `
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="dropdown table-action">
                                        <a href="#" class="action-icon" data-bs-toggle="dropdown">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item status_edit-btn"
                                                href="#"
                                                data-id="${row.id}"
                                                data-title="${row.title}"
                                                data-status="${row.status}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#status_edit">
                                                <i class="ti ti-edit text-blue"></i> Edit
                                            </a>
                                            <a class="dropdown-item delete_reason-btn"
                                                href="#"
                                                data-id="${row.id}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#delete_calls">
                                                <i class="ti ti-trash text-danger"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>`;
                                }
                            }
                        ],
                        initComplete: function() {
                            $(".dataTables_paginate").appendTo(".datatable-paginate");
                            $(".dataTables_length").appendTo(".datatable-length");
                        },
                        language: {
                            search: " ",
                            searchPlaceholder: "Search",
                            paginate: {
                                next: 'Next <i class="fa fa-angle-right"></i>',
                                previous: '<i class="fa fa-angle-left"></i> Prev'
                            }
                        }
                    });
                },

                handleAddFormSubmit(e) {
                    e.preventDefault();
                    const $form = $(e.currentTarget);
                    const url = `{{ route('ActivitStatus.store', ['box_slug' => request()->route('box_slug')]) }}`;

                    const data = {
                        title: $form.find('input[name="title"]').val().trim(),
                        status: $form.find('input[name="status"]:checked').val(),
                        activity_type_id: new URLSearchParams(window.location.search).get('activity_type_id'),
                        _token: $('meta[name="csrf-token"]').attr("content")
                    };

                    $.post(url, data).done(res => {
                        if (res.success) {
                            $('#add_calls').modal('hide');
                            $form[0].reset();
                            this.showToast('Activity added!');
                            this.reloadTable();
                        }
                    }).fail(xhr => {
                        const res = xhr.responseJSON;
                        const errorMsg = (xhr.status === 422 && res?.errors) ?
                            Object.values(res.errors).flat().join(' ') :
                            res?.message || 'Add failed.';
                        this.showToast(errorMsg, 'error');
                    });
                },

                populateEditModal() {
                    const id = $(this).data('id');
                    const title = $(this).data('title');
                    const status = $(this).data('status');

                    $('#editActivtityStatus').val(id);
                    $('#editActivitiyStatusName').val(title);
                    $(`#status_edit input[name="status"][value="${status}"]`).prop('checked', true);
                },

                handleEditFormSubmit(e) {
                    e.preventDefault();
                    const id = $('#editActivtityStatus').val();
                    const title = $('#editActivitiyStatusName').val().trim();
                    const status = $('#status_edit input[name="status"]:checked').val();

                    $.post("{{ route('activityStatus.update', ['box_slug' => request()->route('box_slug')]) }}", {
                        id,
                        title,
                        status,
                        _token: $('meta[name="csrf-token"]').attr("content")
                    }).done(res => {
                        $('#status_edit').modal('hide');
                        this.showToast(res.message || 'Updated.', res.success ? 'success' : 'error');
                        if (res.success) this.reloadTable();
                    }).fail(() => {
                        this.showToast('Update failed.', 'error');
                    });
                },

                handleDelete() {
                    const id = $('#deleteActivityType').val();
                    if (!id) return this.showToast('Invalid ID.', 'error');

                    $.ajax({
                        url: `{{ route('activityStatus.delete', ['box_slug' => request()->route('box_slug')]) }}`,
                        method: 'DELETE',
                        data: {
                            id,
                            _token: $('meta[name="csrf-token"]').attr("content")
                        },
                        success: (res) => {
                            $('#delete_calls').modal('hide');
                            this.showToast(res.message || 'Deleted.', res.success ? 'success' : 'error');
                            if (res.success) this.reloadTable();
                        },
                        error: () => {
                            this.showToast('Delete failed.', 'error');
                        }
                    });
                }
            };

            $(document).ready(() => {
                ActivityManager.init();
            });
        </script>
    @endpush
@endsection
