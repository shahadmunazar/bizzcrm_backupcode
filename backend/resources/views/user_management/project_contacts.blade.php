@extends('layouts.main')
@section('title', 'Project Contacts')
@section('description', 'Manage and view all project contacts.')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="row">
            <div class="col-md-12">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">Project Contacts<span class="count-title">{{ $totalContacts ?? 0 }}</span></h4>
                        </div>
                        <div class="col-4 text-end">
                            <div class="head-icons">
                                <a href="javascript:void(0);" onclick="$('#contacts-datatable').DataTable().ajax.reload(null, false);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
                                    <i class="ti ti-refresh-dot"></i>
                                </a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
                                    <i class="ti ti-chevrons-up"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="card">
                    <div class="card-header border-bottom-0">
                        <div class="row align-items-center">
                            <div class="col-sm-4">
                                <div class="icon-form mb-3 mb-sm-0">
                                    <span class="form-icon"><i class="ti ti-search"></i></span>
                                    <input type="text" class="form-control" id="searchContacts" placeholder="Search Contacts">
                                </div>
                            </div>
                            <div class="col-sm-8 text-sm-end">
                                <div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
                                    <a href="{{ box_route('manage_users.projects') }}" class="btn btn-light me-2"><i class="ti ti-arrow-left me-2"></i>Back to Projects</a>
                                    <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_contact"><i class="ti ti-square-rounded-plus me-2"></i>Add Contact</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <!-- Contacts List -->
                        <div class="table-responsive custom-table">
                            <table class="table" id="contacts-datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Designation</th>
                                        <th>Project</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <!-- /Contacts List -->
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- Add Contact -->
<div class="offcanvas offcanvas-end offcanvas-large" id="offcanvas_add_contact">
    <div class="offcanvas-header border-bottom">
        <h4>Add New Contact</h4>
        <button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <form id="addContactForm" onsubmit="return false;">
            @csrf
            <div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="col-form-label">Select Project <span class="text-danger">*</span></label>
                            <select class="select" name="project_id" required>
                                <option value="">Choose Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="col-form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="col-form-label">Email</label>
                            <input class="form-control" name="email" type="email">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="col-form-label">Phone</label>
                            <input class="form-control" name="phone" type="text">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="col-form-label">Designation</label>
                            <input class="form-control" name="designation" type="text">
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>
<!-- /Add Contact -->

<!-- Edit Contact Offcanvas -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit_contact">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Edit Contact</h5>
        <button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <form id="editContactForm">
            @csrf
            <input type="hidden" id="edit_contact_id" name="id">
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="col-form-label">Select Project <span class="text-danger">*</span></label>
                        <select class="select" name="project_id" id="edit_project_id" required>
                            <option value="">Choose Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="col-form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="col-form-label">Email</label>
                        <input class="form-control" name="email" id="edit_email" type="email">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="col-form-label">Phone</label>
                        <input class="form-control" name="phone" id="edit_phone" type="text">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="col-form-label">Designation</label>
                        <input class="form-control" name="designation" id="edit_designation" type="text">
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
<!-- /Edit Contact Offcanvas -->

<!-- Delete Modal -->
<div class="modal fade" id="delete_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1" id="delete_title">Confirm Delete</h4>
                <p class="mb-3">
                    You want to delete the marked item. This action <strong>cannot</strong> be undone once you proceed.
                </p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete_btn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Modal -->

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#contacts-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ box_route('manage_users.projects.contacts') }}",
                type: 'GET'
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'phone', name: 'phone'},
                {data: 'designation', name: 'designation'},
                {data: 'project_name', name: 'project_name'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            language: {
                search: "",
                searchPlaceholder: "Search...",
            },
            bFilter: true
        });

        // Search Custom Input
        $('#searchContacts').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Submit Add Contact Form
        $('#addContactForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            
            // Clear previous errors
            $('#addContactForm .invalid-feedback').remove();
            $('#addContactForm .is-invalid').removeClass('is-invalid');
            
            $.ajax({
                url: "{{ box_route('manage_users.projects.contacts.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status) {
                        $('#offcanvas_add_contact').offcanvas('hide');
                        $('#addContactForm')[0].reset();
                        $('.select').val('').trigger('change');
                        table.ajax.reload(null, false);
                    } else {
                        showToast(response.message || 'Error occurred', 'danger');
                    }
                },
                error: function(xhr) {
                    showToast('An error occurred while adding the contact. Please check your inputs.', 'danger');
                }
            });
        });
        
        // Edit Contact Click
        $(document).on('click', '.edit-contact-btn', function() {
            var id = $(this).data('id');
            $('#edit_contact_id').val(id);
            
            $.ajax({
                url: "{{ box_route('manage_users.projects.contacts') }}/" + id,
                type: 'GET',
                success: function(response) {
                    if (response.status) {
                        var data = response.data;
                        $('#edit_project_id').val(data.project_id).trigger('change');
                        $('#edit_name').val(data.name);
                        $('#edit_email').val(data.email);
                        $('#edit_phone').val(data.phone);
                        $('#edit_designation').val(data.designation);
                        $('#offcanvas_edit_contact').offcanvas('show');
                    } else {
                        showToast(response.message || 'Failed to fetch details.', 'danger');
                    }
                },
                error: function(xhr) {
                    showToast('Something went wrong.', 'danger');
                }
            });
        });

        // Submit Edit Contact Form
        $('#editContactForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#edit_contact_id').val();
            var formData = new FormData(this);
            
            $.ajax({
                url: "{{ box_route('manage_users.projects.contacts') }}/" + id,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status) {
                        $('#offcanvas_edit_contact').offcanvas('hide');
                        $('#editContactForm')[0].reset();
                        table.ajax.reload(null, false);
                        showToast(response.message, 'success');
                    } else {
                        showToast(response.message || 'Error occurred', 'danger');
                    }
                },
                error: function(xhr) {
                    showToast('An error occurred while updating the contact. Please check your inputs.', 'danger');
                }
            });
        });

        // Delete Contact
        $(document).on('click', '.delete-contact-btn', function() {
            var id = $(this).data('id');
            $('#confirm_delete_btn').data('id', id);
            $('#delete_modal').modal('show');
        });

        $('#confirm_delete_btn').on('click', function() {
            var id = $(this).data('id');
            var $btn = $(this);
            $btn.prop('disabled', true).text('Deleting...');
            $.ajax({
                url: "{{ box_route('manage_users.projects.contacts') }}/" + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status) {
                        showToast(response.message, 'success');
                        table.ajax.reload(null, false);
                    } else {
                        showToast(response.message || 'Error occurred', 'danger');
                    }
                    $('#delete_modal').modal('hide');
                },
                error: function(xhr) {
                    showToast('Something went wrong. Please check your inputs.', 'danger');
                    $('#delete_modal').modal('hide');
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Yes, Delete');
                }
            });
        });

    });
</script>
@endpush
