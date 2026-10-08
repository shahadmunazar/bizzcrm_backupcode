@extends('layouts.main')
@section('content')
@section('title', 'Employee Type Settings')

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
                        <!-- Employee Type list -->
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                <h5>List Of Employee Types</h5>
                                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                                    <div class="mb-2">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#add_employee_type"
                                            class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add
                                            Employee Type</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">


                                <div class="custom-datatable-filter table-responsive">
                                    <table class="table" id="EmployeeTypeTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="no-sort">
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input" type="checkbox" id="select-all" />
                                                    </div>
                                                </th>
                                                <th>Name</th>
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


                            </div>
                        </div>
                        <!-- /Employee Type list -->
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<!-- Add Employee Type -->
<div class="modal fade" id="add_employee_type">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Employee Type</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="employeeTypeForm" method="POST">
                @csrf
                <div class="modal-body pb-0">
                    <div class="row">

                        <!-- Name -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Enter Employee Type Name" required />
                            </div>
                        </div>
                        <div class="col-md-12">
                             <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
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
<!-- /Add Employee Type -->

<!-- Delete Modal -->
<div class="modal fade" id="delete_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1">Confirm Delete</h4>
                <p class="mb-3">You want to delete this employee type. This action cannot be undone.</p>
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

            function showToast(message, type = 'success') {
                const $toast = $('#dynamicToast');
                const $message = $('#dynamicToastMessage');

                $message.text(message);
                $toast.removeClass('bg-success bg-danger bg-warning')
                    .addClass(
                        type === 'success' ? 'bg-success' :
                        type === 'error' ? 'bg-danger' : 'bg-warning'
                    );

                const toast = new bootstrap.Toast($toast[0]);
                toast.show();
            }

            let EmployeeTypeTable = $('#EmployeeTypeTable').DataTable({
                processing: true,
                serverSide: false,
                responsive: true,
                searching: true,
                lengthChange: true,
                pageLength: 10,
                order: [
                    [1, 'asc']
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                    lengthMenu: "Show _MENU_ entries"
                },
                columns: [{
                        data: 'select',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'status',
                        render: function(data, type, row) {
                            if (data === 'active') {
                                return '<span class="badge bg-success">Active</span>';
                            } else {
                                return '<span class="badge bg-danger">Inactive</span>';
                            }
                        }
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            function loadEmployeeTypeList() {
                $.ajax({
                    url: "{{ route('employee-type.list', ['box_slug' => request()->route('box_slug')]) }}",
                    type: "GET",
                    success: function(response) {
                        if (response.status === "success") {
                            EmployeeTypeTable.clear();

                            $.each(response.data, function(index, type) {
                                let editData = JSON.stringify(type).replace(/"/g, '&quot;');
                                EmployeeTypeTable.row.add({
                                    select: `
                                <div class="form-check form-check-md">
                                    <input class="form-check-input type-checkbox" type="checkbox" value="${type.id}" />
                                </div>`,
                                    name: type.name ?? '-',
                                    status: type.status ?? 'active',
                                    actions: `
                                <div class="action-icon d-inline-flex">
                                    <a href="javascript:void(0);" class="me-2 edit-type" data-type="${editData}">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="delete-type" data-id="${type.id}">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                </div>`
                                });
                            });

                            EmployeeTypeTable.draw();
                        } else {
                            showToast(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        let msg = 'Failed to load data';
                        if(xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showToast(msg, 'error');
                    }
                });
            }

            // Initial load
            loadEmployeeTypeList();

            // Open Modal for ADD
            $('[data-bs-target="#add_employee_type"]').on('click', function() {
                const form = $('#employeeTypeForm');
                form.trigger('reset');
                form.attr('data-mode', 'add');
                $('#type_id').remove();
                $('.modal-title').text('Add Employee Type');
            });

            // Open Modal for EDIT (Delegated event)
            $(document).on('click', '.edit-type', function() {
                const typeData = $(this).data('type');
                // Handle case where data-type might be automatically parsed or string
                const type = (typeof typeData === 'string') ? JSON.parse(typeData) : typeData;

                const form = $('#employeeTypeForm');
                form.trigger('reset');
                form.attr('data-mode', 'edit');
                
                $('#type_id').remove();
                form.append(`<input type="hidden" name="type_id" id="type_id" value="${type.id}">`);
                
                $('#name').val(type.name);
                $('#status').val(type.status);
                
                $('.modal-title').text('Edit Employee Type');
                
                const modal = new bootstrap.Modal(document.getElementById('add_employee_type'));
                modal.show();
            });

            $('#employeeTypeForm').on('submit', function(e) {
                e.preventDefault();
                const mode = $(this).attr('data-mode');
                
                let url;
                if(mode === 'edit') {
                     url = "{{ route('employee-type.update', ['box_slug' => request()->route('box_slug')]) }}";
                } else {
                     url = "{{ route('employee-type.store', ['box_slug' => request()->route('box_slug')]) }}";
                }

                $.ajax({
                    url: url,
                    type: "POST",
                    data: $(this).serialize() + "&_token={{ csrf_token() }}",
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#add_employee_type').modal('hide');
                            // If using the bootstrap instance manually created, we might need to get the existing instance
                            // or rely on data-bs-dismiss to clean up if the user clicked cancel, but here we close programmatically.
                             const modalEl = document.getElementById('add_employee_type');
                             const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                             modal.hide();

                            showToast(res.message, 'success');
                            loadEmployeeTypeList();
                        } else {
                            showToast(res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                         let msg = 'Operation failed';
                         if(xhr.responseJSON && xhr.responseJSON.message) {
                             msg = xhr.responseJSON.message;
                         }
                        showToast(msg, 'error');
                    }
                });
            });

            let deleteTypeId = null;

            $(document).on('click', '.delete-type', function() {
                deleteTypeId = $(this).data('id'); // store id
                const deleteModal = new bootstrap.Modal(document.getElementById('delete_modal'));
                deleteModal.show();
            });

            $('#confirmDelete').on('click', function() {
                if (!deleteTypeId) return;

                $.ajax({
                    url: "{{ route('employee-type.delete', ['box_slug' => request()->route('box_slug')]) }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        type_id: deleteTypeId
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            showToast(res.message, 'success');
                            loadEmployeeTypeList();
                        } else {
                            showToast(res.message, 'error');
                        }
                        deleteTypeId = null;
                        $('#delete_modal').modal('hide');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        let msg = 'Failed to delete employee type.';
                        if(xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showToast(msg, 'error');
                        deleteTypeId = null;
                        $('#delete_modal').modal('hide');
                    }
                });
            });

            $('#select-all').on('click', function() {
                $('.type-checkbox').prop('checked', $(this).prop('checked'));
            });

        });
    </script>
@endpush
@endsection
