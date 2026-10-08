@extends('layouts.main')
@section('content')
@section('title', 'Box Branch Settings')

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

                <!-- Settings Menu -->
                @include('settings.partials.nav')

                <div class="row">
                    <div class="col-xl-3 col-lg-12 theiaStickySidebar">
                        @include('settings.partials.sidebar')
                    </div>

                    <div class="col-xl-9 col-lg-12">
                        <!-- Leaves list -->
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                <h5>List Of Branches </h5>
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
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#add_branch"
                                            class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add
                                            Branch </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">


                                <div class="custom-datatable-filter table-responsive">
                                    <table class="table" id="BranchCompany">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="no-sort">
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input" type="checkbox" id="select-all" />
                                                    </div>
                                                </th>
                                                <th>Branch Name </th>
                                                <th>Country </th>
                                                <th>State </th>
                                                <th>City </th>
                                                <th>Pin Code</th>
                                                <th>Address </th>
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
<div class="modal fade" id="add_branch">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Branch</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="branchForm" method="POST">
                @csrf
                <div class="modal-body pb-0">
                    <div class="row">

                        <!-- Branch Name -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="branch_name" class="form-label">Branch Name</label>
                                <input type="text" name="branch_name" id="branch_name" class="form-control"
                                    placeholder="Enter Branch Name" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="country_id" class="form-label">Country</label>
                                <select name="country_id" id="country_id" class="form-select" required>
                                    <option value="" disabled selected>Select Country</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="state_id" class="form-label">State</label>
                                <select name="state_id" id="state_id" class="form-select" required disabled>
                                    <option value="" disabled selected>Select State</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="city_id" class="form-label">City</label>
                                <select name="city_id" id="city_id" class="form-select" required disabled>
                                    <option value="" disabled selected>Select City / Branch</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pincode" class="form-label">Pin Code</label>
                                <input type="text" name="pincode" id="pincode" class="form-control"
                                    placeholder="Enter Pin Code" pattern="\d{5,10}"
                                    title="Please enter a valid Pin Code" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea name="address" id="address" class="form-control" rows="3" placeholder="Enter Full Address"
                                    required></textarea>
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

<!-- Edit Barnch -->
<div class="modal fade" id="edit_branch">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Branch </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form action="#">
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Branch Name </label>
                                <input type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Country </label>
                                <select class="select">
                                    <option disabled selected>Select Country</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">State </label>
                                <select class="select">
                                    <option disabled selected>Select State</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">City </label>
                                <input type="text" class="form-control" />
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Address </label>
                                <textarea class="form-control"></textarea>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary me-2">Save </button>
                    <button type="button" class="btn btn-light " data-bs-dismiss="modal">Cancel </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Edit Leaves -->

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


            let BranchCompanyTable = $('#BranchCompany').DataTable({
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
                    searchPlaceholder: "Search branches...",
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
                        data: 'country'
                    },
                    {
                        data: 'state'
                    },
                    {
                        data: 'city'
                    },
                    {
                        data: 'pincode'
                    },
                    {
                        data: 'address'
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            function loadBranchList() {
                $.ajax({
                    url: "{{ route('company-branches.list', ['box_slug' => request()->route('box_slug')]) }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        $('#BranchCompany tbody').html(
                            `<tr><td colspan="8" class="text-center py-3">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Loading branches...
                    </td></tr>`
                        );
                    },
                    success: function(response) {
                        if (response.status === "success") {
                            BranchCompanyTable.clear();

                            $.each(response.data, function(index, branch) {
                                BranchCompanyTable.row.add({
                                    select: `
                                <div class="form-check form-check-md">
                                    <input class="form-check-input branch-checkbox" type="checkbox" value="${branch.id}" />
                                </div>`,
                                    name: branch.name ?? '-',
                                    country: branch.country?.name ?? '-',
                                    state: branch.state?.name ?? '-',
                                    city: branch.city?.name ?? '-',
                                    pincode: branch.pincode ?? '-',
                                    address: branch.address ?? '-',
                                    actions: `
                                <div class="action-icon d-inline-flex">
                                    <a href="#" class="me-2 edit-branch" data-branch='${JSON.stringify(branch)}'>
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="delete-branch" data-id="${branch.id}">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                </div>`
                                });
                            });

                            BranchCompanyTable.draw();
                        } else {
                            showToast(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        let errorMessage = 'Failed to load branches.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showToast(errorMessage, 'error');
                    }
                });
            }

            // Initial load
            loadBranchList();

            function loadCountries(selector, selectedId = null, callback = null) {
                $.ajax({
                    url: '{{ route('get.all.country', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                    success: function(res) {
                        let select = $(selector);
                        select.empty().append('<option disabled selected>Select Country</option>');
                        $.each(res.data, function(i, country) {
                            let selected = selectedId == country.id ? 'selected' : '';
                            select.append(
                                `<option value="${country.id}" ${selected}>${country.name}</option>`
                                );
                        });
                        if (callback) callback();
                    }
                });
            }

            function loadStates(selector, countryId, selectedStateId = null, callback = null) {
                $.ajax({
                    url: '{{ route('company.states.select', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        country_id: countryId
                    },
                    success: function(states) {
                        let select = $(selector);
                        select.empty().append('<option disabled selected>Select State</option>');
                        $.each(states, function(key, name) {
                            let selected = key == selectedStateId ? 'selected' : '';
                            select.append(
                            `<option value="${key}" ${selected}>${name}</option>`);
                        });
                        select.prop('disabled', false);
                        if (callback) callback();
                    }
                });
            }

            function loadCities(selector, stateId, selectedCityId = null) {
                $.ajax({
                    url: '{{ route('company.city.select', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        state_id: stateId
                    },
                    success: function(cities) {
                        let select = $(selector);
                        select.empty().append('<option disabled selected>Select City</option>');
                        $.each(cities, function(key, name) {
                            let selected = key == selectedCityId ? 'selected' : '';
                            select.append(
                            `<option value="${key}" ${selected}>${name}</option>`);
                        });
                        select.prop('disabled', false);
                    }
                });
            }

            $('#country_id').on('change', function() {
                const countryId = $(this).val();
                $('#state_id').html('<option>Loading...</option>').prop('disabled', true);
                $('#city_id').html('<option>Select State First</option>').prop('disabled', true);
                loadStates('#state_id', countryId);
            });

            $('#state_id').on('change', function() {
                const stateId = $(this).val();
                $('#city_id').html('<option>Loading...</option>').prop('disabled', true);
                loadCities('#city_id', stateId);
            });

            $('#add_branch').on('show.bs.modal', function(e) {
                const button = $(e.relatedTarget);
                const mode = button.data('mode') || 'add';
                const branch = button.data('branch') || null;
                const form = $('#branchForm');
                form.trigger('reset');
                form.attr('data-mode', mode);
                $('#branch_id').remove();
                $('#country_id').html('<option value="" disabled selected>Select Country</option>');
                $('#state_id').html('<option value="" disabled selected>Select State</option>').prop(
                    'disabled', true);
                $('#city_id').html('<option value="" disabled selected>Select City</option>').prop(
                    'disabled', true);

                if (mode === 'edit' && branch) {
                    form.append(
                        `<input type="hidden" name="branch_id" id="branch_id" value="${branch.id}">`);
                    $('#branch_name').val(branch.name);
                    $('#pincode').val(branch.pincode);
                    $('#address').val(branch.address);
                    loadCountries('#country_id', branch.country_id, function() {
                        loadStates('#state_id', branch.country_id, branch.state_id, function() {
                            loadCities('#city_id', branch.state_id, branch.city_id);
                        });
                    });
                } else {
                    loadCountries('#country_id');
                }
            });

            $('#branchForm').on('submit', function(e) {
                e.preventDefault();
                const mode = $(this).attr('data-mode');
                const url = mode === 'edit' ?
                    "{{ route('company-branches.update', ['box_slug' => request()->route('box_slug')]) }}" :
                    "{{ route('company-branches.store', ['box_slug' => request()->route('box_slug')]) }}";

                $.ajax({
                    url: url,
                    type: "POST",
                    data: $(this).serialize() + "&_token={{ csrf_token() }}",
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#add_branch').modal('hide');
                            showToast(res.message, 'success');
                            loadBranchList();
                        } else {
                            showToast(res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        let errorMessage = 'Failed to save branch.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showToast(errorMessage, 'error');
                    }
                });
            });

            $(document).on('click', '.edit-branch', function() {
                const branch = $(this).data('branch');
                $('#add_branch').modal('show');
                $('#add_branch form').attr('data-mode', 'edit');
                $('#add_branch form').append(
                    `<input type="hidden" name="branch_id" id="branch_id" value="${branch.id}">`);
                $('#branch_name').val(branch.name);
                $('#pincode').val(branch.pincode);
                $('#address').val(branch.address);
                loadCountries('#country_id', branch.country_id, function() {
                    loadStates('#state_id', branch.country_id, branch.state_id, function() {
                        loadCities('#city_id', branch.state_id, branch.city_id);
                    });
                });
            });

            let deleteBranchId = null;

            $(document).on('click', '.delete-branch', function() {
                deleteBranchId = $(this).data('id'); // store id
                const deleteModal = new bootstrap.Modal(document.getElementById('delete_modal'));
                deleteModal.show();
            });

            $('#confirmDelete').on('click', function() {
                if (!deleteBranchId) return;

                $.ajax({
                    url: "{{ route('company-branches.delete', ['box_slug' => request()->route('box_slug')]) }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        branch_id: deleteBranchId
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            showToast(res.message, 'success');
                            loadBranchList();
                        } else {
                            showToast(res.message, 'error');
                        }
                        deleteBranchId = null;
                        $('#delete_modal').modal('hide');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        let errorMessage = 'Failed to delete branch.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showToast(errorMessage, 'error');
                        deleteBranchId = null;
                        $('#delete_modal').modal('hide');
                    }
                });
            });

            $('#select-all').on('click', function() {
                $('.branch-checkbox').prop('checked', $(this).prop('checked'));
            });

        });
    </script>
@endpush
@endsection
