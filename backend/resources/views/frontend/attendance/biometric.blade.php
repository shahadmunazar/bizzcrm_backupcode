@extends('layouts.main')
@section('content')
@section('title', 'Biometric Machine')



<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Biometric Machine Setup </h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="index.html"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            Attendence
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Biometric Machine Setup </li>
                    </ol>
                </nav>
            </div>
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
                                <a href="javascript:void(0);" id="export-pdf" class="dropdown-item rounded-1"><i
                                        class="ti ti-file-type-pdf me-1"></i>Export as PDF </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" id="export-excel" class="dropdown-item rounded-1"><i
                                        class="ti ti-file-type-xls me-1"></i>Export as Excel </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="mb-2">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#add_machine"
                        class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add
                        Biometric Machine </a>
                </div>
                <div class="head-icons ms-2">
                    <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-original-title="Collapse" id="collapse-header">
                        <i class="ti ti-chevrons-up"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- /Breadcrumb -->



        <!-- Leaves list -->
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                <h5>List Of Machine </h5>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="me-3">
                        <button type="button" class="btn btn-white d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                            <i class="ti ti-columns me-1"></i>Manage Columns
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="custom-datatable-filter table-responsive">
                    <table class="table" id="biometric-machines-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="no-sort">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox" id="select-all" />
                                    </div>
                                </th>
                                <th>Machine Number</th>
                                <th>Machine Name</th>
                                <th>Machine Model</th>
                                <th>Machine Serial Number</th>
                                <th>Branch</th>
                                <th>Log Sync</th>
                                <th>Attendance Sync</th>
                                <th>Sync Status</th>
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
        <!-- /Leaves list -->

    </div>

</div>
<!-- /Page Wrapper -->

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


<!-- Add Machine -->
<div class="modal fade" id="add_machine">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Biometric Machine </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="addBiometric" method="POST">
                @csrf
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Machine No. </label>
                                <input type="text" class="form-control" name="machine_no" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Machine Type </label>
                                <select class="select" name="machine_type">
                                    <option disabled selected>Select Machine Type</option>
                                    <option>T52</option>
                                    <option>T16</option>
                                    <option>T28-OLD</option>
                                    <option>T62</option>
                                    <option>T61</option>
                                    <option>RS20-OLD</option>
                                    <option>RS70-OLD</option>
                                    <option>RS10-OLD</option>
                                    <option>T61N</option>
                                    <option>C121</option>
                                    <option>C101W</option>
                                    <option>T60</option>
                                    <option>S02 CONTROLER(T)</option>
                                    <option>S04 CONTROLER(T)</option>
                                    <option>T302</option>
                                    <option>T304</option>
                                    <option>T304F MINI</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Serial No. </label>
                                <input type="text" class="form-control" name="serial_no" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Server Port No. </label>
                                <input type="text" class="form-control" name="server_port_no" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Machine Name </label>
                                <input type="text" class="form-control" name="machine_name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Select Branch</label>
                                <select class="form-control" name="branch_id" id="branch_id_select">
                                    <option disabled selected>Select Branch</option>
                                    <!-- Options will be loaded dynamically -->
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <select id="country_id" class="form-select" name="country_id">
                                    <option disabled selected>Loading countries...</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">State</label>
                                <select id="state_id" class="form-select" name="state_id" disabled>
                                    <option disabled selected>Select Country First</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">City / Branch</label>
                                <select id="city_id" class="form-select" name="city_id" disabled>
                                    <option disabled selected>Select State First</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Time Zone</label>
                                <select class="select" name="timezone" id="timezone">
                                    <option disabled selected>Select Time Zone</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">In/Out Only for Access Control </label>
                                <select class="select" name="access_control">
                                    <option disabled selected>Select Access Control</option>
                                    <option value="In">In</option>
                                    <option value="Out">Out</option>
                                    <option value="In/Out">In/Out</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status </label>
                                <select class="select" name="status">
                                    <option disabled selected>Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary me-2">Save </button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- /Add Machine -->


<div class="modal fade" id="edit_machine">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Biometric Machine</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="editBiometric" method="POST">
                @csrf
                <input type="hidden" name="id" id="edit_id" />
                <div class="modal-body pb-0">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Machine No.</label>
                                <input type="text" class="form-control" name="machine_no" id="edit_machine_no" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Machine Type</label>
                                <select class="select" name="machine_type" id="edit_machine_type">
                                    <option disabled selected>Select Machine Type</option>
                                    <option>T52</option>
                                    <option>T16</option>
                                    <option>T28-OLD</option>
                                    <option>T62</option>
                                    <option>T61</option>
                                    <option>RS20-OLD</option>
                                    <option>RS70-OLD</option>
                                    <option>RS10-OLD</option>
                                    <option>T61N</option>
                                    <option>C121</option>
                                    <option>C101W</option>
                                    <option>T60</option>
                                    <option>S02 CONTROLER(T)</option>
                                    <option>S04 CONTROLER(T)</option>
                                    <option>T302</option>
                                    <option>T304</option>
                                    <option>T304F MINI</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Serial No.</label>
                                <input type="text" class="form-control" name="serial_no" id="edit_serial_no" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Server Port No.</label>
                                <input type="text" class="form-control" name="server_port_no"
                                    id="edit_server_port_no" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Machine Name</label>
                                <input type="text" class="form-control" name="machine_name"
                                    id="edit_machine_name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Select Branch</label>
                                <select class="form-control" name="branch_id" id="edit_branch_id_select">
                                    <option disabled selected>Select Branch</option>
                                    <!-- Options will be loaded dynamically -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <select id="edit_country_id" class="form-select" name="country_id">
                                    <option disabled selected>Loading countries...</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">State</label>
                                <select id="edit_state_id" class="form-select" name="state_id" disabled>
                                    <option disabled selected>Select Country First</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">City / Branch</label>
                                <select id="edit_city_id" class="form-select" name="city_id" disabled>
                                    <option disabled selected>Select State First</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Time Zone</label>
                                <select class="select" name="timezone" id="edit_timezone">
                                    <option disabled selected>Select Time Zone</option>

                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Access Control</label>
                                <select class="select" name="access_control" id="edit_access_control">
                                    <option disabled selected>Select Access Control</option>
                                    <option value="In">In</option>
                                    <option value="Out">Out</option>
                                    <option value="In/Out">In/Out</option>
                                </select>
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status </label>
                                <select class="select" name="status" id="status">
                                    <option disabled selected>Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary me-2">Save</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
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
                <p class="mb-3">Are you sure you want to delete this item? This action cannot be undone.</p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
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

<style>
/* green blinking indicator */
.green-blink {
    height: 12px;
    width: 12px;
    background-color: #28a745;
    border-radius: 50%;
    display: inline-block;
    margin-right: 6px;
    animation: greenBlink 1s infinite;
}

@keyframes greenBlink {
    0% { opacity: 0.2; }
    50% { opacity: 1; }
    100% { opacity: 0.2; }
}

/* solid red alert indicator */
.red-alert {
    height: 12px;
    width: 12px;
    background-color: #dc3545;
    border-radius: 50%;
    display: inline-block;
    margin-right: 6px;
    box-shadow: 0px 0px 6px rgba(255,0,0,0.6);
}
</style>



@push('scripts')
    <script>
        $(document).ready(function() {
            // ------------------------- Toast Notification Function -------------------------
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
            // ------------------------- Add Biometric Modal -------------------------
            $('#add_machine').on('show.bs.modal', function() {
                // ------------------------- Pre-fill text inputs -------------------------
                $('#machine_name').val(localStorage.getItem('machine_name') || '');
                $('#serial_no').val(localStorage.getItem('serial_no') || '');
                // ------------------------- Load Countries -------------------------
                function loadCountries(callback = null) {
                    let countries = localStorage.getItem('countries');
                    if (countries) {
                        populateCountries(JSON.parse(countries));
                        if (callback) callback();
                    } else {
                        $.ajax({
                            url: '{{ route('get.all.country', ['box_slug' => request()->route('box_slug')]) }}',
                            type: 'GET',
                            success: function(res) {
                                localStorage.setItem('countries', JSON.stringify(res.data));
                                populateCountries(res.data);
                                if (callback) callback();
                            },
                            error: function(xhr) {
                                showToast('Failed to load countries.', 'error');
                                console.error(xhr.responseText);
                            }
                        });
                    }
                }
                function populateCountries(data) {
                    let select = $('#country_id');
                    select.empty().append('<option disabled selected>Select Country</option>');
                    $.each(data, function(i, country) {
                        select.append(`<option value="${country.id}">${country.name}</option>`);
                    });
                    // Pre-select if localStorage has value
                    let selectedCountry = localStorage.getItem('country_id');
                    if (selectedCountry) {
                        select.val(selectedCountry).trigger('change');
                        loadStates(selectedCountry);
                    }
                }
                // ------------------------- Load States -------------------------
                function loadStates(countryId, callback = null) {
                    let states = localStorage.getItem(`states_${countryId}`);
                    if (states) {
                        populateStates(JSON.parse(states));
                        if (callback) callback();
                    } else {
                        $.ajax({
                            url: '{{ route('company.states.select', ['box_slug' => request()->route('box_slug')]) }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                country_id: countryId
                            },
                            success: function(res) {
                                localStorage.setItem(`states_${countryId}`, JSON.stringify(
                                    res));
                                populateStates(res);
                                if (callback) callback();
                            },
                            error: function(xhr) {
                                showToast('Failed to load states.', 'error');
                            }
                        });
                    }
                }
                function populateStates(states) {
                    let select = $('#state_id');
                    select.empty().append('<option disabled selected>Select State</option>');
                    $.each(states, function(k, v) {
                        select.append(`<option value="${k}">${v}</option>`);
                    });
                    let selectedState = localStorage.getItem('state_id');
                    if (selectedState) {
                        select.val(selectedState).trigger('change');
                        loadCities(selectedState);
                    }
                }
                // ------------------------- Load Cities -------------------------
                function loadCities(stateId, callback = null) {
                    let cities = localStorage.getItem(`cities_${stateId}`);
                    if (cities) {
                        populateCities(JSON.parse(cities));
                        if (callback) callback();
                    } else {
                        $.ajax({
                            url: '{{ route('company.city.select', ['box_slug' => request()->route('box_slug')]) }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                state_id: stateId
                            },
                            success: function(res) {
                                localStorage.setItem(`cities_${stateId}`, JSON.stringify(res));
                                populateCities(res);
                                if (callback) callback();
                            },
                            error: function(xhr) {
                                showToast('Failed to load cities.', 'error');
                            }
                        });
                    }
                }
                function populateCities(cities) {
                    let select = $('#city_id');
                    select.empty().append('<option disabled selected>Select City</option>');
                    $.each(cities, function(k, v) {
                        select.append(`<option value="${k}">${v}</option>`);
                    });
                    let selectedCity = localStorage.getItem('city_id');
                    if (selectedCity) {
                        select.val(selectedCity).trigger('change');
                        // No longer load branches here
                    }
                }
                // ------------------------- Load Branches (independent for add) -------------------------
                function loadBranches(callback = null) {
                    $.ajax({
                        url: '{{ route('getall.biometric', ['box_slug' => request()->route('box_slug')]) }}',
                        type: 'GET',
                        success: function(res) {
                            if (res.status === 'success') {
                                populateBranches(res.data);
                                if (callback) callback();
                            } else {
                                showToast('Failed to load branches.', 'error');
                            }
                        },
                        error: function(xhr) {
                            showToast('Failed to load branches.', 'error');
                        }
                    });
                }
                function populateBranches(branches) {
                    let select = $('#branch_id_select');
                    select.empty().append('<option disabled selected>Select Branch</option>');
                    $.each(branches, function(i, branch) {
                        select.append(`<option value="${branch.id}"
                                       data-country-id="${branch.country_id || ''}"
                                       data-state-id="${branch.state_id || ''}"
                                       data-city-id="${branch.city_id || ''}">${branch.name}</option>`);
                    });
                    let selectedBranch = localStorage.getItem('branch_id');
                    if (selectedBranch) {
                        select.val(selectedBranch).trigger('change'); // Trigger to auto-populate if needed
                    }
                }
                // ------------------------- Auto-populate Location on Branch Selection (for add) -------------------------
                $('#branch_id_select').off('change').on('change', function() {
                    let selectedOption = $(this).find('option:selected');
                    let branchId = $(this).val();
                    let countryId = selectedOption.data('country-id');
                    let stateId = selectedOption.data('state-id');
                    let cityId = selectedOption.data('city-id');

                    if (countryId && stateId && cityId && branchId) {
                        // Ensure countries are loaded, then set and chain loads
                        loadCountries(() => {
                            // Set country and disable
                            $('#country_id').val(countryId).prop('disabled', true);
                            localStorage.setItem('country_id', countryId);

                            // Load states for country, select state, then load cities
                            loadStates(countryId, () => {
                                $('#state_id').val(stateId).prop('disabled', true);
                                localStorage.setItem('state_id', stateId);

                                // Load cities for state, select city
                                loadCities(stateId, () => {
                                    $('#city_id').val(cityId).prop('disabled', true);
                                    localStorage.setItem('city_id', cityId);
                                });
                            });
                        });

                        localStorage.setItem('branch_id', branchId);
                    }
                });
                // ------------------------- Load Timezones -------------------------
                function loadTimezones() {
                    let timezones = localStorage.getItem('timezones');
                    if (timezones) {
                        populateTimezones(JSON.parse(timezones));
                    } else {
                        $.ajax({
                            url: '{{ route('timezones', ['box_slug' => request()->route('box_slug')]) }}',
                            type: 'GET',
                            success: function(res) {
                                if (res.success) {
                                    localStorage.setItem('timezones', JSON.stringify(res.data));
                                    populateTimezones(res.data);
                                } else {
                                    showToast('Failed to load timezones.', 'error');
                                }
                            },
                            error: function(xhr) {
                                showToast('Failed to load timezones.', 'error');
                            }
                        });
                    }
                }
                function populateTimezones(data) {
                    let select = $('#timezone');
                    select.empty().append('<option disabled selected>Select Time Zone</option>');
                    $.each(data, function(i, tz) {
                        select.append(`<option value="${tz}">${tz}</option>`);
                    });
                    let selectedTz = localStorage.getItem('timezone');
                    if (selectedTz) select.val(selectedTz);
                }
                // ------------------------- Trigger Loading (chain countries -> branches) -------------------------
                loadCountries(() => {
                    loadBranches();
                });
                loadTimezones();
                // ------------------------- Event Listeners (with disable checks) -------------------------
                $('#country_id').off('change').on('change', function() {
                    let countryId = $(this).val();
                    if (!$(this).prop('disabled') && countryId) {
                        localStorage.setItem('country_id', countryId);
                        loadStates(countryId);
                    }
                });
                $('#state_id').off('change').on('change', function() {
                    let stateId = $(this).val();
                    if (!$(this).prop('disabled') && stateId) {
                        localStorage.setItem('state_id', stateId);
                        loadCities(stateId);
                    }
                });
                $('#city_id').off('change').on('change', function() {
                    let cityId = $(this).val();
                    if (!$(this).prop('disabled')) {
                        localStorage.setItem('city_id', cityId);
                    }
                });
                $('#timezone').off('change').on('change', function() {
                    localStorage.setItem('timezone', $(this).val());
                });
            });
            // ------------------------- Handle Country/State Change for Add (with disable check) -------------------------
            $('#country_id').on('change', function() {
                let countryId = $(this).val();
                if (!$(this).prop('disabled') && countryId) {
                    localStorage.setItem('country_id', countryId);
                    $('#state_id').prop('disabled', false).html('<option disabled selected>Loading...</option>'); // Enable for load
                    loadStates(countryId);
                }
            });
            $('#state_id').on('change', function() {
                let stateId = $(this).val();
                if (!$(this).prop('disabled') && stateId) {
                    localStorage.setItem('state_id', stateId);
                    $('#city_id').prop('disabled', false).html('<option disabled selected>Loading...</option>');
                    loadCities(stateId);
                }
            });
            $('#city_id').on('change', function() {
                if (!$(this).prop('disabled')) {
                    localStorage.setItem('city_id', $(this).val());
                }
            });
            $('#timezone').on('change', function() {
                localStorage.setItem('timezone', $(this).val());
            });
            // ------------------------- Edit Biometric Modal Population -------------------------
            function loadBranchesForEdit(callback = null) {
                $.ajax({
                    url: '{{ route('getall.biometric', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                    success: function(res) {
                        if (res.status === 'success') {
                            populateBranchesForEdit(res.data);
                            if (callback) callback();
                        } else {
                            showToast('Failed to load branches.', 'error');
                        }
                    },
                    error: function(xhr) {
                        showToast('Failed to load branches.', 'error');
                    }
                });
            }
            function populateBranchesForEdit(branches) {
                let select = $('#edit_branch_id_select');
                select.empty().append('<option disabled selected>Select Branch</option>');
                $.each(branches, function(i, branch) {
                    select.append(`<option value="${branch.id}"
                                   data-country-id="${branch.country_id || ''}"
                                   data-state-id="${branch.state_id || ''}"
                                   data-city-id="${branch.city_id || ''}">${branch.name}</option>`);
                });
            }
            // ------------------------- Auto-populate Location on Branch Selection (for edit) -------------------------
            $('#edit_branch_id_select').off('change').on('change', function() {
                let selectedOption = $(this).find('option:selected');
                let branchId = $(this).val();
                let countryId = selectedOption.data('country-id');
                let stateId = selectedOption.data('state-id');
                let cityId = selectedOption.data('city-id');

                if (countryId && stateId && cityId && branchId) {
                    // Ensure countries are loaded for edit, then set and chain loads
                    loadCountriesForEdit('#edit_country_id', null, () => {
                        // Set country and disable
                        $('#edit_country_id').val(countryId).prop('disabled', true);

                        // Load states for country, select state, then load cities
                        loadStatesForEdit('#edit_state_id', countryId, null, () => {
                            $('#edit_state_id').val(stateId).prop('disabled', true);

                            // Load cities for state, select city
                            loadCitiesForEdit('#edit_city_id', stateId, null, () => {
                                $('#edit_city_id').val(cityId).prop('disabled', true);
                            });
                        });
                    });
                }
            });
            // Column definitions with names
            const columnDefinitions = [
                { index: 0, name: 'Checkbox', visible: true },
                { index: 1, name: 'Machine Number', visible: true },
                { index: 2, name: 'Machine Name', visible: true },
                { index: 3, name: 'Machine Model', visible: true },
                { index: 4, name: 'Machine Serial Number', visible: true },
                { index: 5, name: 'Branch', visible: true },
                { index: 6, name: 'Log Sync', visible: true },
                { index: 7, name: 'Attendance Sync', visible: true },
                { index: 8, name: 'Sync Status', visible: true },
                { index: 9, name: 'Action', visible: true }
            ];

            // Page identifier for column preferences
            const pageIdentifier = 'biometric_machines';

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
                        console.error('Error fetching column preferences:', error);
                        // Fallback: show all columns on error
                        columnDefinitions.forEach(col => col.visible = true);
                        if (callback) callback();
                    }
                });
            }

            // Save column visibility preferences to database
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

            // Apply column visibility (without triggering drawCallback)
            function applyColumnVisibility() {
                if (typeof table !== 'undefined' && table && $.fn.DataTable.isDataTable('#biometric-machines-table')) {
                    columnDefinitions.forEach(col => {
                        try {
                            table.column(col.index).visible(col.visible, false);
                        } catch (e) {
                            console.error('Error applying column visibility:', e);
                        }
                    });
                    // Only adjust columns, don't redraw to avoid infinite loop
                    table.columns.adjust();
                }
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

            // Global table variable - must be declared before any functions that use it
            var table;

            // Load preferences on page load BEFORE table initialization (synchronous)
            loadColumnPreferences(function() {
                // Preferences loaded, now initialize table
                initializeBiometricTable();
            });

            // ------------------------- Initialize DataTable -------------------------
            function initializeBiometricTable() {
                // Check if DataTable already exists and destroy it first
                if ($.fn.DataTable.isDataTable('#biometric-machines-table')) {
                    $('#biometric-machines-table').DataTable().destroy();
                }
                
                table = $('#biometric-machines-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('biometric.index', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [{
                        data: 'id',
                        render: function(data) {
                            return `<div class="form-check form-check-md">
                            <input class="form-check-input machine-checkbox" type="checkbox" value="${data}" />
                        </div>`;
                        },
                        orderable: false,
                        searchable: false,
                        visible: Boolean(columnDefinitions[0] && columnDefinitions[0].visible)
                    },
                    {
                        data: 'machine_no',
                        visible: Boolean(columnDefinitions[1] && columnDefinitions[1].visible)
                    },
                    {
                        data: 'machine_name',
                        visible: Boolean(columnDefinitions[2] && columnDefinitions[2].visible)
                    },
                    {
                        data: 'machine_type',
                        visible: Boolean(columnDefinitions[3] && columnDefinitions[3].visible)
                    },
                    {
                        data: 'serial_no',
                        visible: Boolean(columnDefinitions[4] && columnDefinitions[4].visible)
                    },
                    {
                        data: 'branch.name',
                        name: 'branch.name',
                        defaultContent: '<span class="text-muted">No Branch</span>',
                        visible: Boolean(columnDefinitions[5] && columnDefinitions[5].visible)
                    },
                    {
                        data: 'last_sync',
                        visible: Boolean(columnDefinitions[6] && columnDefinitions[6].visible),
                        render: function(last_sync) {
                            if (!last_sync) {
                                return `<span class="text-danger">No Data</span>`;
                            }

                            let syncTime = new Date(last_sync);
                            let now = new Date();
                            let diffMs = now - syncTime;

                            let diffSec = Math.floor(diffMs / 1000);
                            let diffMin = Math.floor(diffSec / 60);
                            let diffHr = Math.floor(diffMin / 60);

                            let displayTime = "";

                            if (diffSec < 60) {
                                // less than 1 minute
                                displayTime = `${diffSec} sec ago`;
                            } else if (diffMin < 60) {
                                // 1–59 min
                                let sec = diffSec % 60;
                                displayTime = `${diffMin} min ${sec} sec ago`;
                            } else {
                                // 1 hour or more
                                let min = diffMin % 60;
                                displayTime = `${diffHr} hr ${min} min ago`;
                            }

                            return `<span class="text-primary fw-bold">${displayTime}</span>`;
                        }
                    },
                    {
                        data: 'last_sync_attendance',
                        visible: Boolean(columnDefinitions[7] && columnDefinitions[7].visible),
                        render: function(last_sync_attendance) {
                            if (!last_sync_attendance) {
                                return `<span class="text-danger">No Data</span>`;
                            }

                            let syncTime = new Date(last_sync_attendance);
                            let now = new Date();
                            let diffMs = now - syncTime;

                            let diffSec = Math.floor(diffMs / 1000);
                            let diffMin = Math.floor(diffSec / 60);
                            let diffHr = Math.floor(diffMin / 60);

                            let displayTime = "";

                            if (diffSec < 60) {
                                // less than 1 minute
                                displayTime = `${diffSec} sec ago`;
                            } else if (diffMin < 60) {
                                // 1–59 min
                                let sec = diffSec % 60;
                                displayTime = `${diffMin} min ${sec} sec ago`;
                            } else {
                                // 1 hour or more
                                let min = diffMin % 60;
                                displayTime = `${diffHr} hr ${min} min ago`;
                            }

                            return `<span class="text-info fw-bold">${displayTime}</span>`;
                        }
                    },
                    {
                        data: 'sync_status',
                        visible: Boolean(columnDefinitions[8] && columnDefinitions[8].visible),
                        render: function(status) {
        if (status === 'green') {
            return `
                <span class="status-indicator green-blink"></span>
                <span class="text-success fw-bold">Active</span>
            `;
        } else {
            return `
                <span class="status-indicator red-alert"></span>
                <span class="text-danger fw-bold">Offline</span>
            `;
        }
    }
},

                    {
                        data: 'id',
                        visible: Boolean(columnDefinitions[9] && columnDefinitions[9].visible),
                        render: function(data) {
                            return `<div class="action-icon d-inline-flex">
                            <a href="#" class="me-2 edit-machine" data-id="${data}" data-bs-toggle="modal" data-bs-target="#edit_machine">
                                <i class="ti ti-edit"></i>
                            </a>
                            <a href="javascript:void(0);" class="delete-machine" data-id="${data}" data-bs-toggle="modal" data-bs-target="#delete_modal">
                                <i class="ti ti-trash"></i>
                            </a>
                        </div>`;
                        },
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                responsive: true,
                drawCallback: function() {
                    // Note: Don't call applyColumnVisibility here to avoid infinite loop
                }
            });
            } // End of initializeBiometricTable function
            
            // Note: Column visibility is already set during DataTable initialization via visible: columnDefinitions[X].visible
            // No need to call applyColumnVisibility here as it would be redundant

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
                if (table && $.fn.DataTable.isDataTable('#biometric-machines-table')) {
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
            // ------------------------- Centralized Table Refresh -------------------------
            function refreshTable() {
                table.ajax.reload(null, false); // reload without resetting pagination
            }
            // ------------------------- Load Countries (global, with callback) -------------------------
            function loadCountries(selectElementId, selectedId = null, callback = null) {
                let countries = localStorage.getItem('countries');
                if (countries) {
                    let data = JSON.parse(countries);
                    let select = $(selectElementId);
                    select.empty().append('<option selected disabled>Select Country</option>');
                    $.each(data, function(key, country) {
                        let selected = selectedId && selectedId == country.id ? 'selected' : '';
                        select.append(`<option value="${country.id}" ${selected}>${country.name}</option>`);
                    });
                    if (callback) callback();
                    return;
                }
                $.ajax({
                    url: '{{ route('get.all.country', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                    success: function(response) {
                        localStorage.setItem('countries', JSON.stringify(response.data));
                        let select = $(selectElementId);
                        select.empty().append('<option selected disabled>Select Country</option>');
                        $.each(response.data, function(key, country) {
                            let selected = selectedId && selectedId == country.id ? 'selected' : '';
                            select.append(`<option value="${country.id}" ${selected}>${country.name}</option>`);
                        });
                        if (callback) callback();
                    },
                    error: function(xhr) {
                        showToast('Failed to load countries.', 'error');
                        console.error(xhr.responseText);
                    }
                });
            }
            // Separate for edit to avoid conflicts, but actually same as global
            function loadCountriesForEdit(selectElementId, selectedId = null, callback = null) {
                loadCountries(selectElementId, selectedId, callback);
            }
            // ------------------------- Load States (global) -------------------------
            function loadStates(selectElementId, countryId, selectedStateId = null, callback = null) {
                let statesKey = `states_${countryId}`;
                let states = localStorage.getItem(statesKey);
                if (states) {
                    let stateData = JSON.parse(states);
                    let stateSelect = $(selectElementId);
                    stateSelect.empty().append('<option disabled selected>Select State</option>');
                    $.each(stateData, function(key, name) {
                        let selected = selectedStateId && key == selectedStateId ? 'selected' : '';
                        stateSelect.append(`<option value="${key}" ${selected}>${name}</option>`);
                    });
                    stateSelect.prop('disabled', false);
                    if (callback) callback();
                    return;
                }
                $.ajax({
                    url: '{{ route('company.states.select', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        country_id: countryId
                    },
                    success: function(stateData) {
                        localStorage.setItem(statesKey, JSON.stringify(stateData));
                        let stateSelect = $(selectElementId);
                        stateSelect.empty().append('<option disabled selected>Select State</option>');
                        $.each(stateData, function(key, name) {
                            let selected = selectedStateId && key == selectedStateId ? 'selected' : '';
                            stateSelect.append(`<option value="${key}" ${selected}>${name}</option>`);
                        });
                        stateSelect.prop('disabled', false);
                        if (callback) callback();
                    },
                    error: function(xhr) {
                        showToast('Failed to load states.', 'error');
                    }
                });
            }
            function loadStatesForEdit(selectElementId, countryId, selectedStateId = null, callback = null) {
                loadStates(selectElementId, countryId, selectedStateId, callback);
            }
            // ------------------------- Load Cities (global, added callback) -------------------------
            function loadCities(selectElementId, stateId, selectedCityId = null, callback = null) {
                let citiesKey = `cities_${stateId}`;
                let cities = localStorage.getItem(citiesKey);
                if (cities) {
                    let cityData = JSON.parse(cities);
                    let citySelect = $(selectElementId);
                    citySelect.empty().append('<option disabled selected>Select City / Branch</option>');
                    $.each(cityData, function(key, name) {
                        let selected = selectedCityId && key == selectedCityId ? 'selected' : '';
                        citySelect.append(`<option value="${key}" ${selected}>${name}</option>`);
                    });
                    citySelect.prop('disabled', false);
                    if (callback) callback();
                    return;
                }
                $.ajax({
                    url: '{{ route('company.city.select', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        state_id: stateId
                    },
                    success: function(cityData) {
                        localStorage.setItem(citiesKey, JSON.stringify(cityData));
                        let citySelect = $(selectElementId);
                        citySelect.empty().append('<option disabled selected>Select City / Branch</option>');
                        $.each(cityData, function(key, name) {
                            let selected = selectedCityId && key == selectedCityId ? 'selected' : '';
                            citySelect.append(`<option value="${key}" ${selected}>${name}</option>`);
                        });
                        citySelect.prop('disabled', false);
                        if (callback) callback();
                    },
                    error: function(xhr) {
                        showToast('Failed to load cities.', 'error');
                    }
                });
            }
            function loadCitiesForEdit(selectElementId, stateId, selectedCityId = null, callback = null) {
                loadCities(selectElementId, stateId, selectedCityId, callback);
            }
            // ------------------------- Handle Country Change -------------------------
            function handleCountryChange(countrySelector, stateSelector, citySelector) {
                $(countrySelector).on('change', function() {
                    if (!$(this).prop('disabled')) {
                        let countryId = $(this).val();
                        $(stateSelector).prop('disabled', true).html('<option disabled selected>Select State</option>');
                        $(citySelector).prop('disabled', true).html('<option disabled selected>Select State First</option>');
                        if (countryId) loadStates(stateSelector, countryId, null, () => {
                            $(citySelector).prop('disabled', true).html('<option disabled selected>Select State First</option>');
                        });
                    }
                });
            }
            handleCountryChange('#country_id', '#state_id', '#city_id');
            handleCountryChange('#edit_country_id', '#edit_state_id', '#edit_city_id');
            // ------------------------- Handle State Change -------------------------
            function handleStateChange(stateSelector, citySelector) {
                $(stateSelector).on('change', function() {
                    if (!$(this).prop('disabled')) {
                        let stateId = $(this).val();
                        $(citySelector).prop('disabled', true).html('<option disabled selected>Select State First</option>');
                        if (stateId) loadCities(citySelector, stateId);
                    }
                });
            }
            handleStateChange('#state_id', '#city_id');
            handleStateChange('#edit_state_id', '#edit_city_id');
            // ------------------------- Load Timezones -------------------------
            function loadTimezones(selectAdd = '#timezone', selectEdit = '#edit_timezone', selectedValue = null) {
                let timezones = localStorage.getItem('timezones');
                if (timezones) return populateTimezones(JSON.parse(timezones), selectedValue);
                $.ajax({
                    url: '{{ route('timezones', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            localStorage.setItem('timezones', JSON.stringify(response.data));
                            populateTimezones(response.data, selectedValue);
                        } else {
                            showToast('Failed to load timezones.', 'error');
                        }
                    },
                    error: function(err) {
                        showToast('Failed to load timezones.', 'error');
                        console.error('Failed to fetch timezones', err);
                    }
                });
                function populateTimezones(data, selectedValue) {
                    // Add form
                    $(selectAdd).empty().append('<option disabled selected>Select Time Zone</option>');
                    $.each(data, (i, tz) => {
                        $(selectAdd).append(`<option value="${tz}">${tz}</option>`);
                    });
                    // Edit form
                    $(selectEdit).empty().append('<option disabled>Select Time Zone</option>');
                    $.each(data, (i, tz) => {
                        let selected = selectedValue && selectedValue === tz ? 'selected' : '';
                        $(selectEdit).append(`<option value="${tz}" ${selected}>${tz}</option>`);
                    });
                    // Trigger change so dependent logic works
                    $(selectEdit).trigger('change');
                }
            }
            // ------------------------- Add Biometric -------------------------
            $('#addBiometric').submit(function(e) {
                e.preventDefault();
                let form = $(this);
                $.ajax({
                    url: "{{ route('biometric.store', ['box_slug' => request()->route('box_slug')]) }}",
                    type: "POST",
                    data: form.serialize(),
                    beforeSend: () => form.find('button[type="submit"]').prop('disabled', true),
                    success: res => {
                        if (res.message) {
                            // 1. Close modal
                            $('#add_machine').modal('hide');
                            // 2. Reset form and re-enable fields for next use
                            form[0].reset();
                            $('#country_id, #state_id, #city_id, #branch_id_select').prop('disabled', false).html(''); // Clear and enable
                            // 3. Refresh table
                            $('#biometric-machines-table').DataTable().ajax.reload(null, false);
                            // 4. Show success toast
                            showToast(res.message, 'success');
                        }
                    },
                    error: xhr => {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, (k, v) => {
                                form.find(`[name="${k}"]`).after(
                                    `<span class="text-danger">${v[0]}</span>`);
                            });
                            showToast('Validation failed. Please check the form.', 'error');
                        } else {
                            showToast('Something went wrong. Please try again.', 'error');
                        }
                    },
                    complete: () => form.find('button[type="submit"]').prop('disabled', false)
                });
            });
            $('#biometric-machines-table').on('click', '.edit-machine', function() {
                let data = table.row($(this).parents('tr')).data();
                $('#edit_id').val(data.id);
                $('#edit_machine_no').val(data.machine_no);
                $('#edit_serial_no').val(data.serial_no);
                $('#edit_server_port_no').val(data.server_port_no);
                $('#edit_machine_name').val(data.machine_name);
                $('#edit_machine_type').val(data.machine_type).trigger('change');
                $('#edit_access_control').val(data.access_control).trigger('change');
                $('#status').val(data.status).trigger('change');

                // Load timezones with selected
                loadTimezones('#timezone', '#edit_timezone', data.timezone);

                // Reset location fields before populating
                $('#edit_country_id, #edit_state_id, #edit_city_id').prop('disabled', false).empty();
                $('#edit_branch_id_select').empty();

                // Load branches for edit, then select branch if exists to trigger auto-populate
                loadBranchesForEdit(() => {
                    if (data.branch && data.branch.id) {
                        // Select the existing branch and trigger change to auto-populate location from branch data
                        $('#edit_branch_id_select').val(data.branch.id).trigger('change');
                    } else if (data.country_id) {
                        // Fallback: load location manually if no branch but direct IDs present
                        loadCountriesForEdit('#edit_country_id', data.country_id, () => {
                            loadStatesForEdit('#edit_state_id', data.country_id, data.state_id, () => {
                                loadCitiesForEdit('#edit_city_id', data.state_id, data.city_id);
                            });
                        });
                    } else {
                        // If no location data, just load countries
                        loadCountriesForEdit('#edit_country_id');
                    }
                });
            });
            $('#editBiometric').submit(function(e) {
                e.preventDefault();
                let form = $(this);
                $.ajax({
                    url: "{{ route('biometric.update', ['box_slug' => request()->route('box_slug')]) }}",
                    type: "PUT",
                    data: form.serialize(),
                    beforeSend: () => form.find('button[type="submit"]').prop('disabled', true),
                    success: res => {
                        if (res.message) {
                            // Close modal
                            $('#edit_machine').modal('hide');
                            // Reset and re-enable fields for next use
                            form[0].reset();
                            $('#edit_country_id, #edit_state_id, #edit_city_id, #edit_branch_id_select').prop('disabled', false).html('');
                            // Refresh table
                            $('#biometric-machines-table').DataTable().ajax.reload(null, false);
                            // Show success toast
                            showToast(res.message, 'success');
                        }
                    },
                    error: xhr => {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, (k, v) => {
                                form.find(`[name="${k}"]`).after(
                                    `<span class="text-danger">${v[0]}</span>`);
                            });
                            showToast('Validation failed. Please check the form.', 'error');
                        } else {
                            showToast('Something went wrong. Please try again.', 'error');
                        }
                    },
                    complete: () => form.find('button[type="submit"]').prop('disabled', false)
                });
            });
            // ------------------------- Delete Biometric -------------------------
            var deleteId;
            $('#biometric-machines-table').on('click', '.delete-machine', function() {
                deleteId = $(this).data('id');
            });
            $('#confirmDelete').click(function() {
                $.ajax({
                    url: "{{ route('biometric.destroy', ['box_slug' => request()->route('box_slug')]) }}",
                    type: "DELETE",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: deleteId
                    },
                    success: res => {
                        if (res.message) {
                            // Close modal
                            $('#delete_modal').modal('hide');
                            // Refresh table
                            $('#biometric-machines-table').DataTable().ajax.reload(null, false);
                            // Show success toast
                            showToast(res.message, 'success');
                        }
                    },
                    error: xhr => {
                        showToast('Failed to delete. Please try again.', 'error');
                    }
                });
            });
            // ------------------------- Edit Country/State Change Handlers (with disable check) -------------------------
            $('#edit_country_id').on('change', function() {
                let countryId = $(this).val();
                if (!$(this).prop('disabled') && countryId) {
                    $('#edit_state_id').prop('disabled', false).html('<option disabled selected>Loading...</option>');
                    loadStatesForEdit('#edit_state_id', countryId);
                }
            });
            $('#edit_state_id').on('change', function() {
                let stateId = $(this).val();
                if (!$(this).prop('disabled') && stateId) {
                    $('#edit_city_id').prop('disabled', false).html('<option disabled selected>Loading...</option>');
                    loadCitiesForEdit('#edit_city_id', stateId);
                }
            });
            $('#edit_city_id').on('change', function() {
                if (!$(this).prop('disabled')) {
                    // No localStorage for edit
                }
            });
            $('#edit_timezone').on('change', function() {
                // No localStorage for edit
            });


            // ------------------------- Export Functionality (Queue + Polling) -------------------------
            function startExport(type) {
                showToast('Starting export...', 'info');
                $.ajax({
                    url: '{{ route('biometric.export.start', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        type: type
                    },
                    success: function(response) {
                        if (response.status === 'success' && response.file_name) {
                            showToast('Export processed. Waiting for file to be ready...', 'info');
                            pollForFile(response.file_name);
                        } else {
                            showToast(response.message || 'Export failed.', 'error');
                        }
                    },
                    error: function(xhr) {
                        showToast('Failed to start export. Please try again.', 'error');
                    }
                });
            }

            function pollForFile(fileName) {
                let attempts = 0;
                const maxAttempts = 30; // 30 * 2s = 60 seconds max wait
                
                function checkFile() {
                    attempts++;
                    // Construct URL securely
                    const baseUrl = '{{ route("biometric.export.download", ["box_slug" => request()->route("box_slug") ?? "default", "file" => "FILE_PLACEHOLDER"]) }}';
                    const downloadUrl = baseUrl.replace('FILE_PLACEHOLDER', fileName);
                    
                    $.ajax({
                        url: downloadUrl,
                        type: 'HEAD',
                        success: function() {
                            showToast('Export ready! Downloading...', 'success');
                            window.location.href = downloadUrl;
                        },
                        error: function(xhr) {
                            if (xhr.status === 404 && attempts < maxAttempts) {
                                // Retry after 2 seconds
                                setTimeout(checkFile, 2000);
                            } else {
                                showToast('Export timed out. Please try again or check "Downloads" later.', 'error');
                            }
                        }
                    });
                }
                
                // Start polling
                setTimeout(checkFile, 2000);
            }

            $('#export-excel').on('click', function() {
                startExport('excel');
            });

            $('#export-pdf').on('click', function() {
                startExport('pdf');
            });

        });
    </script>
@endpush

@endsection
