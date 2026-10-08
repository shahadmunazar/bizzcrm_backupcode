@extends('layouts.main')
@section('content')
@section('title','Work Mode Settings')

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
                                <a href="{{ url()->current() }}" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Refresh"><i class="ti ti-refresh-dot"></i></a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="row">
                    <div class="col-md-12">
                        <!-- Leaves list -->
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                <h5>List Of Work Mode </h5>
                                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                                    <div class="me-2 mb-2">
                                        <div class="dropdown">
                                            <a href="javascript:void(0);"
                                                class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                                                data-bs-toggle="dropdown">
                                                <i class="ti ti-file-export me-1"></i>Export
                                            </a>
                                            <ul class="dropdown-menu  dropdown-menu-end p-3">
                                                <li>
                                                    <a href="javascript:void(0);" class="dropdown-item rounded-1"><i
                                                            class="ti ti-file-type-pdf me-1"></i>Export as PDF </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);" class="dropdown-item rounded-1"><i
                                                            class="ti ti-file-type-xls me-1"></i>Export as Excel </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#add_workmode"
                                            class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add
                                            Work Mode</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">


                                <div class="custom-datatable-filter table-responsive">
                                    <table class="table" id="workModeTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="no-sort">
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input" type="checkbox" id="select-all" />
                                                    </div>
                                                </th>
                                                <th>Work Mode Name </th>
                                                <th>Status</th>
                                                <th>Action </th>
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


                            </div>
                        </div>
                        <!-- /Leaves list -->
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<!-- Add Branch -->
<div class="modal fade" id="add_workmode">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Work Mode</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <form id="workModeForm" method="POST">
                @csrf
                <div class="modal-body pb-0">
                    <div class="row">

                        <!-- Work Mode Name -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="work_mode_name" class="form-label">Work Mode Name</label>
                                <input type="text" name="name" id="work_mode_name" class="form-control"
                                    placeholder="Enter Work Mode Name" required />
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" id="work_mode_status" name="status">
                                <label class="form-check-label" for="work_mode_status">Active</label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary me-2">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- /Add Barnch -->


<!-- Delete Modal -->
<div class="modal fade" id="delete_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1">Confirm Delete</h4>
                <p class="mb-3">You want to delete this branch. This action cannot be undone.</p>
                <div class="d-flex justify-content-center">
                    <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
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

    // ✅ Toast helper
    function showToast(message, type = 'success') {
        const $toast = $('#dynamicToast');
        const $message = $('#dynamicToastMessage');

        $message.text(message);
        $toast.removeClass('bg-success bg-danger bg-warning')
            .addClass(
                type === 'success' ? 'bg-success' :
                type === 'error' ? 'bg-danger' : 'bg-warning'
            );

        new bootstrap.Toast($toast[0]).show();
    }

    // ✅ Initialize DataTable
    let workModeTable = $('#workModeTable').DataTable({
        processing: true,
        serverSide: false,
        responsive: true,
        searching: true,
        lengthChange: true,
        pageLength: 10,
        order: [[1, 'asc']],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search work modes...",
            lengthMenu: "Show _MENU_ entries"
        },
        columns: [
            {
                data: 'select',
                orderable: false,
                searchable: false
            },
            { data: 'name' },
            { data: 'status' },
            {
                data: 'actions',
                orderable: false,
                searchable: false
            }
        ]
    });

    // ✅ Load work mode list
    function loadWorkModeList() {
        $.ajax({
            url: "{{ route('work-mode-settings.list', ['box_slug' => request()->route('box_slug')]) }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            beforeSend: function() {
                $('#workModeTable tbody').html(`
                    <tr><td colspan="4" class="text-center py-3">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Loading work modes...
                    </td></tr>
                `);
            },
            success: function(response) {
                if (response.status === "success") {
                    workModeTable.clear();

                    $.each(response.data, function(index, item) {
                        workModeTable.row.add({
                            select: `
                                <div class="form-check form-check-md">
                                    <input class="form-check-input workmode-checkbox" type="checkbox" value="${item.id}" />
                                </div>
                            `,
                            name: item.name ?? '-',
                            status: item.status ? 'Active' : 'Inactive',
                            actions: `
                                <div class="action-icon d-inline-flex">
                                    <a href="#" class="me-2 edit-workmode" data-workmode='${JSON.stringify(item)}'>
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="delete-workmode" data-id="${item.id}">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                </div>
                            `
                        });
                    });

                    workModeTable.draw();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                showToast(xhr.responseJSON?.message || 'Failed to load work mode list.', 'error');
            }
        });
    }

    // 🔄 Initial load
    loadWorkModeList();

    // ✅ Add / Edit Modal
    $('#add_workmode').on('show.bs.modal', function(e) {
        const button = $(e.relatedTarget);
        const mode = button.data('mode') || 'add';
        const workmode = button.data('workmode') || null;
        const form = $('#workModeForm');

        form.trigger('reset');
        form.attr('data-mode', mode);
        $('#work_mode_id').remove();

        if (mode === 'edit' && workmode) {
            form.append(`<input type="hidden" name="id" id="work_mode_id" value="${workmode.id}">`);
            $('#work_mode_name').val(workmode.name);
            $('#work_mode_status').prop('checked', workmode.status == 1);
        }
    });

    // ✅ Add / Update Work Mode
    $('#workModeForm').on('submit', function(e) {
        e.preventDefault();

        const mode = $(this).attr('data-mode');
        const url = mode === 'edit'
            ? "{{ route('work-mode-settings.update', ['box_slug' => request()->route('box_slug')]) }}"
            : "{{ route('work-mode-settings.store', ['box_slug' => request()->route('box_slug')]) }}";

        const formData = {
            _token: "{{ csrf_token() }}",
            name: $('#work_mode_name').val(),
            status: $('#work_mode_status').is(':checked') ? 1 : 0
        };

        if (mode === 'edit') {
            formData.id = $('#work_mode_id').val();
        }

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            success: function(res) {
                if (res.status === 'success') {
                    $('#add_workmode').modal('hide');
                    showToast(res.message, 'success');
                    loadWorkModeList();
                } else {
                    showToast(res.message, 'error');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                showToast(xhr.responseJSON?.message || 'Failed to save work mode.', 'error');
            }
        });
    });

    // ✅ Edit Work Mode
    $(document).on('click', '.edit-workmode', function() {
        const workmode = $(this).data('workmode');
        $('#add_workmode').modal('show');
        $('#add_workmode form').attr('data-mode', 'edit');
        $('#add_workmode form').append(
            `<input type="hidden" name="id" id="work_mode_id" value="${workmode.id}">`
        );
        $('#work_mode_name').val(workmode.name);
        $('#work_mode_status').prop('checked', workmode.status == 1);
    });

    // ✅ Delete Work Mode
    let deleteWorkModeId = null;

    $(document).on('click', '.delete-workmode', function() {
        deleteWorkModeId = $(this).data('id');
        const deleteModal = new bootstrap.Modal(document.getElementById('delete_modal'));
        deleteModal.show();
    });

    $('#confirmDelete').on('click', function() {
        if (!deleteWorkModeId) return;

        $.ajax({
            url: "{{ route('work-mode-settings.delete', ['box_slug' => request()->route('box_slug')]) }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: deleteWorkModeId
            },
            success: function(res) {
                if (res.status === 'success') {
                    showToast(res.message, 'success');
                    loadWorkModeList();
                } else {
                    showToast(res.message, 'error');
                }
                deleteWorkModeId = null;
                $('#delete_modal').modal('hide');
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                showToast(xhr.responseJSON?.message || 'Failed to delete work mode.', 'error');
                deleteWorkModeId = null;
                $('#delete_modal').modal('hide');
            }
        });
    });

    // ✅ Select All Checkbox
    $('#select-all').on('click', function() {
        $('.workmode-checkbox').prop('checked', $(this).prop('checked'));
    });
});

    </script>
@endpush



@endsection
