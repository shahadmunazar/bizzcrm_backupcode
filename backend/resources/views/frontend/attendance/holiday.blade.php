@extends('layouts.main')
@section('content')
@section('title', 'Holiday')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Holidays </h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="index.html"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            Employee
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Holidays </li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">

                <div class="mb-2">
                    <button type="button" class="btn btn-sm btn-white d-inline-flex align-items-center me-2" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                        <i class="ti ti-columns me-1"></i>Manage Columns
                    </button>
                    {{-- <a href="#" data-bs-toggle="modal" data-bs-target="#add_holiday"
                        class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add
                        Holiday </a> --}}
                    <a href="javascript:void(0)" id="open_add_holiday_modal"
                        class="btn btn-primary d-flex align-items-center">
                        <i class="ti ti-circle-plus me-2"></i>Add Holiday
                    </a>

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


        <div class="card">
            <div class="card-body">
                <div class="tab-content custom-accordion-items" id="holidayAccordionContainer">
                    <!-- JS will populate here -->
                </div>
            </div>
        </div>




    </div>

    

</div>
<!-- /Page Wrapper -->


<!-- Add Holiday Plan -->
<div class="modal fade" id="add_holiday">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Holiday</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="add_holidays" method="POST">
                @csrf
                <div class="modal-body pb-0">
                    <div class="row">
                        <!-- Title -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="titles" id="titles" class="form-control" required />
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <div class="input-icon-end position-relative">
                                    <!--<input type="text" id="date" name="date"
                                        class="form-control datetimepicker" required /> -->

                                    <input type="text" id="date" name="date" class="MyDatePicker form-control" placeholder="MM/DD/YYYY" readonly>    
                                    <span class="input-icon-addon">
                                        <i class="ti ti-calendar text-gray-7"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Holiday Type and Code -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Select Holiday Type</label>
                                <select id="holiday_codes" name="holiday_type" class="form-select" required>
                                    <option value="">-- Select Type --</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Holiday Code</label>
                                <input type="text" id="holiday_code" name="holiday_code" class="form-control"
                                    readonly />
                            </div>
                        </div>

                        <input type="hidden" name="holiday_type_id" id="holiday_type_id" />


                        <!-- Country/Region -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Select Countries/Regions</label>
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        id="select_all_regions">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        id="deselect_all_regions">Deselect All</button>
                                </div>
                                <select id="country_region" name="country_region[]" class="form-control" multiple
                                    required></select>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status" id="status" required>
                                    <option value="">-- Select Status --</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                    </div> <!-- /.row -->
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Holiday</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- /Add Plan -->

<!-- Edit Plan -->
<div class="modal fade" id="edit_holiday">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Holiday</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="edit_holiday_form">
                <input type="hidden" name="id" id="holiday_id" />
                <input type="hidden" name="holiday_type_id" id="holiday_type_id" />
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" id="title" name="title" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <div class="input-icon-end position-relative">
                                    <input type="text" id="date" name="date"
                                        class="form-control datetimepicker" placeholder="dd/mm/yyyy" />
                                    <span class="input-icon-addon">
                                        <i class="ti ti-calendar text-gray-7"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Holiday Type</label>
                                    <select id="holiday_type_select" name="holiday_type" class="form-select">
                                        <option value="">Select Holiday</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Holiday Code</label>
                                    <input type="text" id="holiday_codess" name="holiday_code"
                                        class="form-control" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_country_region" class="form-label">Country/Region</label>
                            <select id="edit_country_region" name="country_region[]" class="form-select" multiple>
                                <!-- options populated dynamically -->
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="select" name="status" id="status">
                                    <option>Active</option>
                                    <option>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- /Edit Plan -->

<!-- Modal Structure -->
<!-- Country List Modal -->
<div id="countriesModal" class="modal-backdrop" style="display: none;">
    <div class="modal-container">
        <div class="modal-header">
            <h2 class="modal-title">Countries</h2>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div id="modalCountryList" class="modal-body">
            <!-- Country names will be injected here -->
        </div>
        <div class="modal-footer">
            <button class="modal-btn" onclick="closeModal()">Close</button>
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

<!-- Delete Modal -->
<div class="modal fade" id="delete_modal" tabindex="-1" aria-labelledby="delete_modal_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1">Confirm Delete</h4>
                <p class="mb-3" id="delete_modal_message">Are you sure you want to delete this holiday? This action
                    cannot be undone.</p>
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

@push('scripts')
    <script>
    // Column Definitions for Manage Columns feature
    const columnDefinitions = [
        { index: 0, name: 'Select', visible: true },
        { index: 1, name: 'Title', visible: true },
        { index: 2, name: 'Date', visible: true },
        { index: 3, name: 'Description', visible: true },
        { index: 4, name: 'Holiday Type', visible: true },
        { index: 5, name: 'Holiday Code', visible: true },
        { index: 6, name: 'Country/Region', visible: true },
        { index: 7, name: 'Status', visible: true },
        { index: 8, name: 'Action', visible: true }
    ];

    // Page identifier for column preferences
    const pageIdentifier = 'holiday';

    // Load column visibility preferences from database
    function loadColumnPreferences(callback) {
        $.ajax({
            url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug")]) }}',
            type: 'GET',
            data: {
                page_identifier: pageIdentifier
            },
            async: false, // Make synchronous to ensure preferences are loaded before rendering
            success: function(response) {
                if (response.success && response.data && Array.isArray(response.data) && response.data.length > 0) {
                    try {
                        const preferences = response.data;
                        preferences.forEach(pref => {
                            const prefIndex = parseInt(pref.index);
                            const col = columnDefinitions.find(c => c.index === prefIndex);
                            if (col) {
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
                        columnDefinitions.forEach(col => col.visible = true);
                        if (callback) callback();
                    }
                } else {
                    columnDefinitions.forEach(col => col.visible = true);
                    if (callback) callback();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading column preferences:', error);
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

    // Load preferences before initializing
    loadColumnPreferences(function() {
        // Preferences loaded, continue with normal initialization
    });

//         $(document).ready(function() {
//             // Setup CSRF token for AJAX
//             $.ajaxSetup({
//                 headers: {
//                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//                 }
//             });

//             // Initial load of years and accordions
//             fetchYears();

//             // Attach global event listeners for dynamic content
//             attachAccordionClickListeners();
//             attachEditHolidayListener();
//             attachDeleteHolidayListener();
//             attachAddHolidaySubmitListener();
//             attachEditHolidaySubmitListener();
//             attachDeleteConfirmListener();
//         });

//         // 1. Fetch all holiday years and render accordions
//         function fetchYears() {
//             $.ajax({
//                 url: '{{ route('get.holiday.data', ['box_slug' => request()->route('box_slug')]) }}',
//                 method: 'GET',
//                 success: function(response) {
//                     if (response.status && response.years.length > 0) {
//                         renderAccordions(response.years);
//                     } else {
//                         $('#holidayAccordionContainer').html('<p>No holiday years found.</p>');
//                     }
//                 },
//                 error: function() {
//                     $('#holidayAccordionContainer').html(
//                         '<p class="text-danger">Error loading holiday years.</p>');
//                 }
//             });
//         }

//         let holidayTypeCache = [];
//         let regionCache = [];

//         // Deselect All Button
//         $('#deselect_all_regions').on('click', function() {
//             $('#country_region').val([]).trigger('change');
//         });

//         // Select All Button
//         $('#select_all_regions').on('click', function() {
//             const allValues = $('#country_region option').map(function() {
//                 return $(this).val();
//             }).get();
//             $('#country_region').val(allValues).trigger('change');
//         });
//         $('#open_add_holiday_modal').on('click', function() {
//             const cachedHolidayTypes = localStorage.getItem('holiday_types');
//             const cachedRegions = localStorage.getItem('region_list');
//             if (cachedHolidayTypes) {
//                 try {
//                     holidayTypeCache = JSON.parse(cachedHolidayTypes);
//                     if (Array.isArray(holidayTypeCache) && holidayTypeCache.length > 0) {
//                         populateHolidayDropdown(holidayTypeCache);
//                     } else {
//                         localStorage.removeItem('holiday_types');
//                     }
//                 } catch (e) {
//                     console.error("Invalid holiday_types cache. Clearing...");
//                     localStorage.removeItem('holiday_types');
//                 }
//             }
//             if (cachedRegions) {
//                 try {
//                     regionCache = JSON.parse(cachedRegions);
//                     if (Array.isArray(regionCache) && regionCache.length > 0) {
//                         populateRegionDropdown(regionCache);
//                         $('#add_holiday').modal('show');
//                         $('#holiday_codes').trigger('change');
//                         return;
//                     } else {
//                         localStorage.removeItem('region_list');
//                     }
//                 } catch (e) {
//                     console.error("Invalid region_list cache. Clearing...");
//                     localStorage.removeItem('region_list');
//                 }
//             }
//             fetchHolidayTypes();
//         });


//         function renderCountries(countries) {
//             if (!countries || countries.length === 0) return '';

//             if (countries.length <= 3) {
//                 return countries.map(c => escapeHtml(c)).join(', ');
//             } else {
//                 const firstThree = countries.slice(0, 3).map(c => escapeHtml(c)).join(', ');
//                 const total = countries.length;
//                 return `
//       <span>${firstThree}</span>
//       <button class="view-more-btn btn btn-sm btn-link p-0" onclick='openModal(${JSON.stringify(countries)})' style="margin-left: 8px;">
//         View More (${total})
//       </button>
//     `;
//             }
//         }


//         function openModal(countries) {
//             const modal = document.getElementById('countriesModal');
//             const countryList = document.getElementById('modalCountryList');

//             countryList.innerHTML = countries.map(c => `<div>${escapeHtml(c)}</div>`).join('');
//             modal.style.display = 'flex';
//         }

//         function closeModal() {
//             const modal = document.getElementById('countriesModal');
//             modal.style.display = 'none';
//         }

//         function escapeHtml(text) {
//             const div = document.createElement('div');
//             div.textContent = text;
//             return div.innerHTML;
//         }

//         // Fetch holiday types if not cached
//         function fetchHolidayTypes() {
//             $.ajax({
//                 url: "{{ route('holiday.prefill', ['box_slug' => request()->route('box_slug')]) }}",
//                 method: "GET",
//                 success: function(response) {
//                     if (response.success && Array.isArray(response.codes)) {
//                         holidayTypeCache = response.codes;
//                         localStorage.setItem('holiday_types', JSON.stringify(holidayTypeCache));
//                         populateHolidayDropdown(holidayTypeCache);
//                     }
//                     fetchRegionsAndShowModal(); // Continue even if empty
//                 },
//                 error: function() {
//                     alert('Failed to fetch holiday types.');
//                     fetchRegionsAndShowModal(); // Still try to fetch regions
//                 }
//             });
//         }

//         function fetchRegionsAndShowModal() {
//             $.ajax({
//                 url: "{{ route('get.all.regional', ['box_slug' => request()->route('box_slug')]) }}",
//                 method: "GET",
//                 success: function(response) {
//                     if (response.status && Array.isArray(response.data)) {
//                         regionCache = response.data;
//                         localStorage.setItem('region_list', JSON.stringify(regionCache));
//                         populateRegionDropdown(regionCache);
//                     } else {
//                         $('#country_region').html('<option disabled>No regions available</option>');
//                     }
//                     $('#add_holiday').modal('show');
//                     $('#holiday_codes').trigger('change');
//                 },
//                 error: function() {
//                     alert('Failed to fetch country/region data.');
//                 }
//             });
//         }

//         function populateHolidayDropdown(data) {
//             const $select = $('#holiday_codes');
//             $select.empty().append('<option value="">-- Select Holiday Type --</option>');
//             data.forEach(item => {
//                 $select.append(`<option value="${item.value}">${item.label}</option>`);
//             });
//             if (data.length === 1) {
//                 $select.val(data[0].value);
//             }
//         }

//         function populateRegionDropdown(data) {
//             const regionSelect = $('#country_region');

//             if (regionSelect.hasClass('select2-hidden-accessible')) {
//                 regionSelect.select2('destroy');
//             }

//             regionSelect.empty();

//             data.forEach(country => {
//                 regionSelect.append(
//                     `<option value="${country.id}">${country.name}</option>`
//                 );
//             });

//             regionSelect.select2({
//                 placeholder: "Select countries/regions",
//                 width: '100%'
//             });
//         }

//         function populateHolidayDropdown(data) {
//             const $select = $('#holiday_codes');
//             $select.empty().append('<option value="">-- Select Holiday Type --</option>');
//             data.forEach(item => {
//                 $select.append(`<option value="${item.value}">${item.label}</option>`);
//             });
//             if (data.length === 1) {
//                 $select.val(data[0].value);
//             }
//         }

//         $('#holiday_codes').on('change', function() {
//             const selectedValue = $(this).val();
//             const selectedItem = holidayTypeCache.find(item => item.value === selectedValue);

//             if (selectedItem) {
//                 $('#holiday_code').val(selectedItem.holiday_code || '');
//                 $('#title').val(selectedItem.label || '');
//                 $('#holiday_type_id').val(selectedItem.id); // ✅ set hidden input
//             } else {
//                 $('#holiday_code').val('');
//                 $('#title').val('');
//                 $('#holiday_type_id').val(''); // clear hidden input
//             }
//         });





//         // 2. Render accordions dynamically for each year
//         //    Open the first year by default, load its holidays immediately
//         function renderAccordions(years) {
//             const container = $('#holidayAccordionContainer');
//             container.empty();

//             years.forEach(function(year, index) {
//                 const accordionId = `accordion-${year}`;
//                 const collapseId = `collapse-${year}`;
//                 const isOpen = index === 0;

//                 // Accordion HTML structure using Bootstrap 5 classes
//                 const accordionHtml = `
//             <div class="accordion mb-3" id="${accordionId}">
//                 <div class="accordion-item">
//                     <h2 class="accordion-header" id="heading-${year}">
//                         <button class="accordion-button ${isOpen ? '' : 'collapsed'}"
//                                 type="button"
//                                 data-bs-toggle="collapse"
//                                 data-bs-target="#${collapseId}"
//                                 aria-expanded="${isOpen}"
//                                 aria-controls="${collapseId}"
//                                 data-year="${year}">
//                             Holidays ${year}
//                         </button>
//                     </h2>
//                     <div id="${collapseId}" class="accordion-collapse collapse ${isOpen ? 'show' : ''}" aria-labelledby="heading-${year}" data-bs-parent="#holidayAccordionContainer">
//                         <div class="accordion-body" id="holiday-body-${year}">
//                             ${isOpen ? '<div class="text-center">Loading holidays...</div>' : ''}
//                         </div>
//                     </div>
//                 </div>
//             </div>
//         `;

//                 container.append(accordionHtml);
//                 if (isOpen) {
//                     fetchHolidayDataByYear(year);
//                 }
//             });
//         }

//         function attachAccordionClickListeners() {
//             $(document).on('show.bs.collapse', '.accordion-collapse', function() {
//                 const collapseId = $(this).attr('id');
//                 const year = collapseId.replace('collapse-', '');
//                 const body = $(`#holiday-body-${year}`);
//                 if (!body.hasClass('loaded')) {
//                     body.html('<div class="text-center">Loading holidays...</div>');
//                     fetchHolidayDataByYear(year);
//                 }
//             });
//         }

//         function fetchHolidayDataByYear(year) {
//             $.ajax({
//                 url: '{{ route('get.holiday.data', ['box_slug' => request()->route('box_slug')]) }}',
//                 method: 'GET',
//                 data: {
//                     year: year
//                 },
//                 success: function(response) {
//                     const container = $(`#holiday-body-${year}`);
//                     if (response.status && response.holidays) {
//                         container.addClass('loaded');
//                         container.html(renderHolidayTable(response.holidays, year));
//                     } else {
//                         container.html(`<p class="text-center">No holidays found for ${year}.</p>`);
//                     }
//                 },
//                 error: function() {
//                     $(`#holiday-body-${year}`).html('<p class="text-danger">Error loading holidays.</p>');
//                 }
//             });
//         }

//         function renderHolidayTable(holidays, year) {
//             if (!holidays.length) {
//                 return `<p class="text-center">No holidays for ${year}.</p>`;
//             }

//             let rows = holidays.map(holiday => `
//         <tr>
//             <td>
//                 <div class="form-check form-check-md">
//                     <input class="form-check-input" type="checkbox" />
//                 </div>
//             </td>
//             <td><h6 class="fw-medium"><a href="#">${escapeHtml(holiday.title)}</a></h6></td>
//             <td>${formatDate(holiday.date)}</td>
//             <td>${escapeHtml(holiday.description || '')}</td>
//             <td>${escapeHtml(holiday.holiday_type || '')}</td>
//             <td>${escapeHtml(holiday.holiday_code || '')}</td>
//             <td>${renderCountries(holiday.countries)}</td>
//             <td>
//                 <span class="badge badge-${holiday.status === 'Active' ? 'success' : 'danger'} d-inline-flex align-items-center badge-sm">
//                     <i class="ti ti-point-filled me-1"></i>${escapeHtml(holiday.status)}
//                 </span>
//             </td>
//             <td>
//     <div class="action-icon d-inline-flex">
//     <a href="#"
//        class="me-2 edit-holiday-btn"
//        data-bs-toggle="modal"
//        data-bs-target="#edit_holiday"
//        data-id="${holiday.id}"
//        data-title="${escapeHtml(holiday.title)}"
//        data-date="${holiday.date}"
//        data-description="${escapeHtml(holiday.description || '')}"
//        data-holiday_type="${escapeHtml(holiday.holiday_type)}"
//        data-code="${escapeHtml(holiday.holiday_code || '')}"
//        data-status="${holiday.status}"
//        data-holiday_type_id="${holiday.holiday_type_id}"
//        data-country_region='${JSON.stringify(holiday.country_region || [])}'
//     >
//         <i class="ti ti-edit"></i>
//     </a>
// </div>


//                     <a href="javascript:void(0);" class="delete-holiday-btn" data-id="${holiday.id}">
//                         <i class="ti ti-trash"></i>
//                     </a>
//                 </div>
//             </td>
//         </tr>
//     `).join('');

//             return `
//         <div class="table-responsive">
//             <table class="table table-hover align-middle mb-0">
//                 <thead class="thead-light">
//                     <tr>
//                         <th></th>
//                         <th>Title</th>
//                         <th>Date</th>
//                         <th>Description</th>
//                         <th>Holiday Type</th>
//                         <th>Holiday Code</th>
//                         <th>Country/Region</th>
//                         <th>Status</th>
//                         <th>Action</th>
//                     </tr>
//                 </thead>
//                 <tbody>${rows}</tbody>
//             </table>
//         </div>
//     `;
//         }

//         function formatDate(dateStr) {
//             if (!dateStr) return '';
//             const date = new Date(dateStr);
//             if (isNaN(date)) return dateStr;
//             return date.toLocaleDateString('en-GB', {
//                 day: '2-digit',
//                 month: 'short',
//                 year: 'numeric'
//             });
//         }

//         function escapeHtml(text) {
//             return $('<div>').text(text).html();
//         }

//         function attachAddHolidaySubmitListener() {
//             $('#add_holidays').on('submit', function(e) {
//                 e.preventDefault();
//                 const form = $(this);
//                 const dateVal = form.find('input[name="date"]').val();
//                 console.log("Date Input:", dateVal);
//                 let year = null;
//                 if (dateVal && /^\d{2}-\d{2}-\d{4}$/.test(dateVal)) {
//                     const [day, month, yearStr] = dateVal.split('-');
//                     const isoDate = `${yearStr}-${month}-${day}`;
//                     const parsedDate = new Date(isoDate);
//                     if (!isNaN(parsedDate)) {
//                         year = parsedDate.getFullYear();
//                     } else {
//                         console.warn("Invalid parsed date from input:", isoDate);
//                     }
//                 }
//                 $.ajax({
//                     url: '{{ route('holiday.post', ['box_slug' => request()->route('box_slug')]) }}',
//                     type: 'POST',
//                     data: form.serialize(),
//                     success: function(response) {
//                         if (response.status) {
//                             showToast(response.message || 'Holiday created successfully!', 'success');
//                             $('#add_holiday').modal('hide');
//                             form[0].reset();
//                             fetchYears();
//                             if (year) {
//                                 setTimeout(() => {
//                                     const collapseId = `collapse-${year}`;
//                                     const collapseElement = document.getElementById(collapseId);
//                                     if (collapseElement && !collapseElement.classList.contains(
//                                             'show')) {
//                                         const bsCollapse = new bootstrap.Collapse(
//                                             collapseElement, {
//                                                 toggle: true
//                                             });
//                                     } else if (collapseElement) {
//                                         fetchHolidayDataByYear(year);
//                                     }
//                                 }, 600);
//                             }
//                         } else {
//                             alert('Failed: ' + response.message);
//                         }
//                     },
//                     error: function(xhr) {
//                         const errors = xhr.responseJSON?.errors;
//                         if (errors) {
//                             const errorMessages = Object.values(errors).flat().join('\n');
//                             alert('Errors:\n' + errorMessages);
//                         } else {
//                             alert('Error submitting form.');
//                         }
//                     }
//                 });
//             });
//         }



//         function attachEditHolidayListener() {
//             $(document).on('click', '.edit-holiday-btn', function() {
//                 const button = $(this);
//                 const modal = $('#edit_holiday');
//                 const form = modal.find('#edit_holiday_form');

//                 // Fill basic fields
//                 form.find('input[name="id"]').val(button.data('id'));
//                 form.find('input[name="title"]').val(button.data('title'));
//                 form.find('input[name="date"]').val(button.data('date'));
//                 form.find('textarea[name="description"]').val(button.data('description'));
//                 form.find('input[name="holiday_code"]').val(button.data('code'));
//                 form.find('input[name="holiday_type_id"]').val(button.data('holiday_type_id'));
//                 form.find('select[name="status"]').val(button.data('status'));

//                 // Populate countries multi-select
//                 const selectedCountries = button.data('country_region') || []; // e.g. ["2", "4", "5"]
//                 const storedCountries = JSON.parse(localStorage.getItem('region_list') ||
//                 '[]'); // [{id: 2, name: '...'}, ...]

//                 const $countrySelect = $('#edit_country_region');
//                 $countrySelect.empty();

//                 storedCountries.forEach(country => {
//                     // Ensure comparing strings for matching
//                     const isSelected = selectedCountries.includes(String(country.id)) ? 'selected' : '';
//                     $countrySelect.append(
//                         `<option value="${country.id}" ${isSelected}>${country.name}</option>`);
//                 });

//                 $countrySelect.trigger('change'); // update UI if plugin is used


//                 // Populate holiday types dropdown
//                 const holidayTypeLabel = button.data('holiday_type') || '';
//                 if (holidayTypeCache && Array.isArray(holidayTypeCache) && holidayTypeCache.length > 0) {
//                     populateHolidayEditDropdown(holidayTypeCache, holidayTypeLabel);
//                     modal.modal('show');
//                 } else {
//                     $.ajax({
//                         url: "{{ route('holiday.prefill', ['box_slug' => request()->route('box_slug')]) }}",
//                         method: "GET",
//                         success: function(response) {
//                             if (response.success && Array.isArray(response.codes)) {
//                                 holidayTypeCache = response.codes;
//                                 try {
//                                     localStorage.setItem('holiday_types', JSON.stringify(
//                                         holidayTypeCache));
//                                 } catch (e) {
//                                     console.warn("Could not cache holiday types:", e);
//                                 }
//                                 populateHolidayEditDropdown(holidayTypeCache, holidayTypeLabel);
//                                 modal.modal('show');
//                             } else {
//                                 alert('No holiday types available');
//                             }
//                         },
//                         error: function() {
//                             alert('Failed to load holiday types');
//                         }
//                     });
//                 }
//             });
//         }

//         function populateHolidayEditDropdown(data, selectedLabel) {
//             const $select = $('#holiday_type_select');
//             $select.empty().append('<option value="">-- Select Holiday Type --</option>');

//             let selectedValue = '';

//             data.forEach(item => {
//                 $select.append(
//                     `<option value="${item.value}" data-holiday_code="${item.holiday_code}">${item.label}</option>`
//                     );
//                 if (item.label === selectedLabel) {
//                     selectedValue = item.value;
//                 }
//             });

//             if (selectedValue) {
//                 $select.val(selectedValue);
//                 updateHolidayCodeAndHidden(selectedValue, data);
//             } else if (data.length === 1 && !selectedLabel) {
//                 $select.val(data[0].value);
//                 updateHolidayCodeAndHidden(data[0].value, data);
//             }
//         }

//         // Update holiday code input and hidden holiday_type_id input when holiday type changes
//         $('#holiday_type_select').on('change', function() {
//             const selectedValue = $(this).val();
//             if (!holidayTypeCache) return;
//             updateHolidayCodeAndHidden(selectedValue, holidayTypeCache);
//         });

//         function updateHolidayCodeAndHidden(selectedValue, data) {
//             const selectedItem = data.find(item => item.value === selectedValue);
//             if (selectedItem) {
//                 $('#holiday_codess').val(selectedItem.holiday_code || '');
//                 $('#holiday_type_id').val(selectedItem.value || '');
//             } else {
//                 $('#holiday_codess').val('');
//                 $('#holiday_type_id').val('');
//             }
//         }




//         function attachEditHolidaySubmitListener() {
//             $('#edit_holiday_form').on('submit', function(e) {
//                 e.preventDefault();
//                 $.ajax({
//                     url: '{{ route('holiday.update', ['box_slug' => request()->route('box_slug')]) }}',
//                     type: 'POST',
//                     data: $(this).serialize(),
//                     success: function(response) {
//                         if (response.status) {
//                             $('#edit_holiday').modal('hide');
//                             showToast(response.message || 'Holiday updated successfully!', 'success');
//                             let updatedYear = null;
//                             if (response.data && response.data.date) {
//                                 updatedYear = new Date(response.data.date).getFullYear();
//                             }
//                             if (updatedYear) {
//                                 fetchHolidayDataByYear(updatedYear);
//                             } else {
//                                 fetchYears();
//                             }
//                         } else {
//                             alert('Update failed: ' + response.message);
//                         }
//                     },
//                     error: function(xhr) {
//                         let errors = xhr.responseJSON?.errors;
//                         if (errors) {
//                             let errorMessages = Object.values(errors).flat().join('\n');
//                             alert('Errors:\n' + errorMessages);
//                         } else {
//                             alert('Error updating holiday.');
//                         }
//                     }
//                 });
//             });
//         }
//         let deleteHolidayId = null;

//         function attachDeleteHolidayListener() {
//             $(document).on('click', '.delete-holiday-btn', function() {
//                 deleteHolidayId = $(this).data('id');
//                 $('#delete_modal').modal('show');
//             });
//         }

//         function attachDeleteConfirmListener() {
//             $('#confirm_delete_btn').on('click', function() {
//                 if (!deleteHolidayId) {
//                     alert('Invalid holiday ID.');
//                     return;
//                 }

//                 $.ajax({
//                     url: '{{ route('holiday.delete', ['box_slug' => request()->route('box_slug')]) }}',
//                     type: 'POST',
//                     data: {
//                         id: deleteHolidayId,
//                         _method: 'DELETE',
//                         _token: $('meta[name="csrf-token"]').attr('content')
//                     },
//                     success: function(response) {
//                         if (response.status) {
//                             showToast(response.message || 'Holiday deleted successfully!', 'danger');
//                             $('#delete_modal').modal('hide');
//                             fetchYears();
//                         } else {
//                             alert('Delete failed: ' + response.message);
//                         }
//                     },
//                     error: function() {
//                         alert('Error deleting holiday.');
//                     }
//                 });
//             });
//         }

//         function showToast(message, type = 'success') {
//             const toastContainer = $('#dynamicToast');
//             const toastMessage = $('#dynamicToastMessage');
//             toastMessage.text(message);
//             toastContainer.removeClass('bg-success bg-danger bg-warning');
//             if (type === 'success') toastContainer.addClass('bg-success');
//             else if (type === 'danger') toastContainer.addClass('bg-danger');
//             else if (type === 'warning') toastContainer.addClass('bg-warning');
//             const toast = new bootstrap.Toast(toastContainer[0]);
//             toast.show();
//         }
$(document).ready(function() {
    // Setup CSRF token for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initial load of years and accordions
    fetchYears();

    // Attach global event listeners for dynamic content
    attachAccordionClickListeners();
    attachEditHolidayListener();
    attachDeleteHolidayListener();
    attachAddHolidaySubmitListener();
    attachEditHolidaySubmitListener();
    attachDeleteConfirmListener();
});

// 1. Fetch all holiday years and render accordions
function fetchYears() {
    $.ajax({
        url: '{{ route('get.holiday.data', ['box_slug' => request()->route('box_slug')]) }}',
        method: 'GET',
        success: function(response) {
            if (response.status && response.years.length > 0) {
                renderAccordions(response.years);
            } else {
                $('#holidayAccordionContainer').html('<p>No holiday years found.</p>');
            }
        },
        error: function() {
            $('#holidayAccordionContainer').html(
                '<p class="text-danger">Error loading holiday years.</p>');
        }
    });
}

let holidayTypeCache = [];
let regionCache = [];

// Deselect All Button
$('#deselect_all_regions').on('click', function() {
    $('#country_region').val([]).trigger('change');
});

// Select All Button
$('#select_all_regions').on('click', function() {
    const allValues = $('#country_region option').map(function() {
        return $(this).val();
    }).get();
    $('#country_region').val(allValues).trigger('change');
});

$('#open_add_holiday_modal').on('click', function() {
    fetchHolidayTypes();
});

function renderCountries(countries) {
    if (!countries || countries.length === 0) return '';

    if (countries.length <= 3) {
        return countries.map(c => escapeHtml(c)).join(', ');
    } else {
        const firstThree = countries.slice(0, 3).map(c => escapeHtml(c)).join(', ');
        const total = countries.length;
        return `
      <span>${firstThree}</span>
      <button class="view-more-btn btn btn-sm btn-link p-0" onclick='openModal(${JSON.stringify(countries)})' style="margin-left: 8px;">
        View More (${total})
      </button>
    `;
    }
}

function openModal(countries) {
    const modal = document.getElementById('countriesModal');
    const countryList = document.getElementById('modalCountryList');

    countryList.innerHTML = countries.map(c => `<div>${escapeHtml(c)}</div>`).join('');
    modal.style.display = 'flex';
}

function closeModal() {
    const modal = document.getElementById('countriesModal');
    modal.style.display = 'none';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Fetch holiday types from API
function fetchHolidayTypes() {
    $.ajax({
        url: "{{ route('holiday.prefill', ['box_slug' => request()->route('box_slug')]) }}",
        method: "GET",
        success: function(response) {
            if (response.success && Array.isArray(response.codes)) {
                holidayTypeCache = response.codes;
                populateHolidayDropdown(holidayTypeCache);
            }
            fetchRegionsAndShowModal(); // Continue even if empty
        },
        error: function() {
            alert('Failed to fetch holiday types.');
            fetchRegionsAndShowModal(); // Still try to fetch regions
        }
    });
}

function fetchRegionsAndShowModal() {
    $.ajax({
        url: "{{ route('get.all.regional', ['box_slug' => request()->route('box_slug')]) }}",
        method: "GET",
        success: function(response) {
            if (response.status && Array.isArray(response.data)) {
                regionCache = response.data;
                populateRegionDropdown(regionCache);
            } else {
                $('#country_region').html('<option disabled>No regions available</option>');
            }
            $('#add_holiday').modal('show');
            $('#holiday_codes').trigger('change');
        },
        error: function() {
            alert('Failed to fetch country/region data.');
        }
    });
}

// Fetch regions from API for edit modal
function fetchRegionsForEdit(callback) {
    $.ajax({
        url: "{{ route('get.all.regional', ['box_slug' => request()->route('box_slug')]) }}",
        method: "GET",
        success: function(response) {
            if (response.status && Array.isArray(response.data)) {
                regionCache = response.data;
                if (callback) callback(regionCache);
            } else {
                if (callback) callback([]);
            }
        },
        error: function() {
            console.error('Failed to fetch regions for edit.');
            if (callback) callback([]);
        }
    });
}

function populateHolidayDropdown(data) {
    const $select = $('#holiday_codes');
    $select.empty().append('<option value="">-- Select Holiday Type --</option>');
    data.forEach(item => {
        $select.append(`<option value="${item.value}">${item.label}</option>`);
    });
    if (data.length === 1) {
        $select.val(data[0].value);
    }
}

function populateRegionDropdown(data) {
    const regionSelect = $('#country_region');

    if (regionSelect.hasClass('select2-hidden-accessible')) {
        regionSelect.select2('destroy');
    }

    regionSelect.empty();

    data.forEach(country => {
        regionSelect.append(
            `<option value="${country.id}">${country.name}</option>`
        );
    });

    regionSelect.select2({
        placeholder: "Select countries/regions",
        width: '100%'
    });
}

$('#holiday_codes').on('change', function() {
    const selectedValue = $(this).val();
    const selectedItem = holidayTypeCache.find(item => item.value === selectedValue);

    if (selectedItem) {
        $('#holiday_code').val(selectedItem.holiday_code || '');
        $('#title').val(selectedItem.label || '');
        $('#holiday_type_id').val(selectedItem.id); // ✅ set hidden input
    } else {
        $('#holiday_code').val('');
        $('#title').val('');
        $('#holiday_type_id').val(''); // clear hidden input
    }
});

// 2. Render accordions dynamically for each year
//    Open the first year by default, load its holidays immediately
function renderAccordions(years) {
    const container = $('#holidayAccordionContainer');
    container.empty();

    years.forEach(function(year, index) {
        const accordionId = `accordion-${year}`;
        const collapseId = `collapse-${year}`;
        const isOpen = index === 0;

        // Accordion HTML structure using Bootstrap 5 classes
        const accordionHtml = `
            <div class="accordion mb-3" id="${accordionId}">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-${year}">
                        <button class="accordion-button ${isOpen ? '' : 'collapsed'}"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#${collapseId}"
                                aria-expanded="${isOpen}"
                                aria-controls="${collapseId}"
                                data-year="${year}">
                            Holidays ${year}
                        </button>
                    </h2>
                    <div id="${collapseId}" class="accordion-collapse collapse ${isOpen ? 'show' : ''}" aria-labelledby="heading-${year}" data-bs-parent="#holidayAccordionContainer">
                        <div class="accordion-body" id="holiday-body-${year}">
                            ${isOpen ? '<div class="text-center">Loading holidays...</div>' : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.append(accordionHtml);
        if (isOpen) {
            fetchHolidayDataByYear(year);
        }
    });
}

function attachAccordionClickListeners() {
    $(document).on('show.bs.collapse', '.accordion-collapse', function() {
        const collapseId = $(this).attr('id');
        const year = collapseId.replace('collapse-', '');
        const body = $(`#holiday-body-${year}`);
        if (!body.hasClass('loaded')) {
            body.html('<div class="text-center">Loading holidays...</div>');
            fetchHolidayDataByYear(year);
        }
    });
}

function fetchHolidayDataByYear(year) {
    $.ajax({
        url: '{{ route('get.holiday.data', ['box_slug' => request()->route('box_slug')]) }}',
        method: 'GET',
        data: {
            year: year
        },
        success: function(response) {
            const container = $(`#holiday-body-${year}`);
            if (response.status && response.holidays) {
                container.addClass('loaded');
                container.html(renderHolidayTable(response.holidays, year));
            } else {
                container.html(`<p class="text-center">No holidays found for ${year}.</p>`);
            }
        },
        error: function() {
            $(`#holiday-body-${year}`).html('<p class="text-danger">Error loading holidays.</p>');
        }
    });
}

function renderHolidayTable(holidays, year) {
    if (!holidays.length) {
        return `<p class="text-center">No holidays for ${year}.</p>`;
    }

    // Build table rows with column visibility support
    let rows = holidays.map(holiday => {
        let rowCells = [];

        // Column 0: Select checkbox
        if (columnDefinitions[0].visible) {
            rowCells.push(`
            <td>
                <div class="form-check form-check-md">
                    <input class="form-check-input" type="checkbox" />
                </div>
            </td>
            `);
        }

        // Column 1: Title
        if (columnDefinitions[1].visible) {
            rowCells.push(`<td><h6 class="fw-medium"><a href="#">${escapeHtml(holiday.title)}</a></h6></td>`);
        }

        // Column 2: Date
        if (columnDefinitions[2].visible) {
            rowCells.push(`<td>${formatDate(holiday.date)}</td>`);
        }

        // Column 3: Description
        if (columnDefinitions[3].visible) {
            rowCells.push(`<td>${escapeHtml(holiday.description || '')}</td>`);
        }

        // Column 4: Holiday Type
        if (columnDefinitions[4].visible) {
            rowCells.push(`<td>${escapeHtml(holiday.holiday_type || '')}</td>`);
        }

        // Column 5: Holiday Code
        if (columnDefinitions[5].visible) {
            rowCells.push(`<td>${escapeHtml(holiday.holiday_code || '')}</td>`);
        }

        // Column 6: Country/Region
        if (columnDefinitions[6].visible) {
            rowCells.push(`<td>${renderCountries(holiday.countries)}</td>`);
        }

        // Column 7: Status
        if (columnDefinitions[7].visible) {
            rowCells.push(`
            <td>
                <span class="badge badge-${holiday.status === 'Active' ? 'success' : 'danger'} d-inline-flex align-items-center badge-sm">
                    <i class="ti ti-point-filled me-1"></i>${escapeHtml(holiday.status)}
                </span>
            </td>
            `);
        }

        // Column 8: Action
        if (columnDefinitions[8].visible) {
            rowCells.push(`
            <td>
                <div class="action-icon d-inline-flex">
                    <a href="#"
                       class="me-2 edit-holiday-btn"
                       data-bs-toggle="modal"
                       data-bs-target="#edit_holiday"
                       data-id="${holiday.id}"
                       data-title="${escapeHtml(holiday.title)}"
                       data-date="${holiday.date}"
                       data-description="${escapeHtml(holiday.description || '')}"
                       data-holiday_type="${escapeHtml(holiday.holiday_type)}"
                       data-code="${escapeHtml(holiday.holiday_code || '')}"
                       data-status="${holiday.status}"
                       data-holiday_type_id="${holiday.holiday_type_id}"
                       data-country_region='${JSON.stringify(holiday.country_region || [])}'>
                        <i class="ti ti-edit"></i>
                    </a>
                    <a href="javascript:void(0);" class="delete-holiday-btn" data-id="${holiday.id}">
                        <i class="ti ti-trash"></i>
                    </a>
                </div>
            </td>
            `);
        }

        return `<tr>${rowCells.join('')}</tr>`;
    }).join('');

    // Build table header with column visibility support
    let headerCells = [];
    if (columnDefinitions[0].visible) headerCells.push('<th></th>');
    if (columnDefinitions[1].visible) headerCells.push('<th>Title</th>');
    if (columnDefinitions[2].visible) headerCells.push('<th>Date</th>');
    if (columnDefinitions[3].visible) headerCells.push('<th>Description</th>');
    if (columnDefinitions[4].visible) headerCells.push('<th>Holiday Type</th>');
    if (columnDefinitions[5].visible) headerCells.push('<th>Holiday Code</th>');
    if (columnDefinitions[6].visible) headerCells.push('<th>Country/Region</th>');
    if (columnDefinitions[7].visible) headerCells.push('<th>Status</th>');
    if (columnDefinitions[8].visible) headerCells.push('<th>Action</th>');

    return `
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="thead-light">
                    <tr>${headerCells.join('')}</tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
        </div>
    `;
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    if (isNaN(date)) return dateStr;
    return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
}

function escapeHtml(text) {
    return $('<div>').text(text).html();
}

function attachAddHolidaySubmitListener() {
    $('#add_holidays').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const dateVal = form.find('input[name="date"]').val();
        console.log("Date Input:", dateVal);
        let year = null;
        if (dateVal) {
            const parsedDate = new Date(dateVal);
            if (!isNaN(parsedDate)) {
                year = parsedDate.getFullYear();
            } else {
                console.warn("Invalid parsed date from input:", dateVal);
            }
        }
        $.ajax({
            url: '{{ route('holiday.post', ['box_slug' => request()->route('box_slug')]) }}',
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.status) {
                    showToast(response.message || 'Holiday created successfully!', 'success');
                    $('#add_holiday').modal('hide');
                    form[0].reset();
                    fetchYears();
                    if (year) {
                        setTimeout(() => {
                            const collapseId = `collapse-${year}`;
                            const collapseElement = document.getElementById(collapseId);
                            if (collapseElement && !collapseElement.classList.contains(
                                    'show')) {
                                const bsCollapse = new bootstrap.Collapse(
                                    collapseElement, {
                                        toggle: true
                                    });
                            } else if (collapseElement) {
                                fetchHolidayDataByYear(year);
                            }
                        }, 600);
                    }
                } else {
                    alert('Failed: ' + response.message);
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    const errorMessages = Object.values(errors).flat().join('\n');
                    alert('Errors:\n' + errorMessages);
                } else {
                    alert('Error submitting form.');
                }
            }
        });
    });
}

function attachEditHolidayListener() {
    $(document).on('click', '.edit-holiday-btn', function() {
        const button = $(this);
        const modal = $('#edit_holiday');
        const form = modal.find('#edit_holiday_form');

        // Fill basic fields
        form.find('input[name="id"]').val(button.data('id'));
        form.find('input[name="title"]').val(button.data('title'));
        form.find('input[name="date"]').val(button.data('date'));
        form.find('textarea[name="description"]').val(button.data('description'));
        form.find('input[name="holiday_code"]').val(button.data('code'));
        form.find('input[name="holiday_type_id"]').val(button.data('holiday_type_id'));
        form.find('select[name="status"]').val(button.data('status'));

        // Populate countries multi-select - always fetch from API
        const selectedCountries = button.data('country_region') || [];
        fetchRegionsForEdit(function(storedCountries) {
            const $countrySelect = $('#edit_country_region');
            if ($countrySelect.hasClass('select2-hidden-accessible')) {
                $countrySelect.select2('destroy');
            }
            $countrySelect.empty();

            storedCountries.forEach(country => {
                const isSelected = selectedCountries.includes(String(country.id)) ? 'selected' : '';
                $countrySelect.append(`<option value="${country.id}" ${isSelected}>${country.name}</option>`);
            });

            $countrySelect.select2({
                placeholder: "Select countries/regions",
                width: '100%'
            });
            $countrySelect.val(selectedCountries).trigger('change');
        });

        // Populate holiday types dropdown - always fetch from API
        const holidayTypeLabel = button.data('holiday_type') || '';
        $.ajax({
            url: "{{ route('holiday.prefill', ['box_slug' => request()->route('box_slug')]) }}",
            method: "GET",
            success: function(response) {
                if (response.success && Array.isArray(response.codes)) {
                    holidayTypeCache = response.codes;
                    populateHolidayEditDropdown(holidayTypeCache, holidayTypeLabel);
                } else {
                    alert('No holiday types available');
                }
                modal.modal('show');
            },
            error: function() {
                alert('Failed to load holiday types');
            }
        });
    });
}

function populateHolidayEditDropdown(data, selectedLabel) {
    const $select = $('#holiday_type_select');
    $select.empty().append('<option value="">-- Select Holiday Type --</option>');

    let selectedValue = '';

    data.forEach(item => {
        $select.append(
            `<option value="${item.value}" data-holiday_code="${item.holiday_code}">${item.label}</option>`
            );
        if (item.label === selectedLabel) {
            selectedValue = item.value;
        }
    });

    if (selectedValue) {
        $select.val(selectedValue);
        updateHolidayCodeAndHidden(selectedValue, data);
    } else if (data.length === 1 && !selectedLabel) {
        $select.val(data[0].value);
        updateHolidayCodeAndHidden(data[0].value, data);
    }
}

// Update holiday code input and hidden holiday_type_id input when holiday type changes
$('#holiday_type_select').on('change', function() {
    const selectedValue = $(this).val();
    if (!holidayTypeCache) return;
    updateHolidayCodeAndHidden(selectedValue, holidayTypeCache);
});

function updateHolidayCodeAndHidden(selectedValue, data) {
    const selectedItem = data.find(item => item.value === selectedValue);
    if (selectedItem) {
        $('#holiday_codess').val(selectedItem.holiday_code || '');
        $('#holiday_type_id').val(selectedItem.value || '');
    } else {
        $('#holiday_codess').val('');
        $('#holiday_type_id').val('');
    }
}

function attachEditHolidaySubmitListener() {
    $('#edit_holiday_form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route('holiday.update', ['box_slug' => request()->route('box_slug')]) }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status) {
                    $('#edit_holiday').modal('hide');
                    showToast(response.message || 'Holiday updated successfully!', 'success');
                    let updatedYear = null;
                    if (response.data && response.data.date) {
                        updatedYear = new Date(response.data.date).getFullYear();
                    }
                    if (updatedYear) {
                        fetchHolidayDataByYear(updatedYear);
                    } else {
                        fetchYears();
                    }
                } else {
                    alert('Update failed: ' + response.message);
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessages = Object.values(errors).flat().join('\n');
                    alert('Errors:\n' + errorMessages);
                } else {
                    alert('Error updating holiday.');
                }
            }
        });
    });
}

let deleteHolidayId = null;

function attachDeleteHolidayListener() {
    $(document).on('click', '.delete-holiday-btn', function() {
        deleteHolidayId = $(this).data('id');
        $('#delete_modal_message').text('Are you sure you want to delete this holiday? This action cannot be undone.');
        $('#confirm_delete_btn').data('force', false);
        $('#confirm_delete_btn').text('Yes, Delete');
        $('#delete_modal').modal('show');
    });
}

function attachDeleteConfirmListener() {
    $('#confirm_delete_btn').on('click', function() {
        if (!deleteHolidayId) {
            alert('Invalid holiday ID.');
            return;
        }
        const force = $(this).data('force') || false;
        $.ajax({
            url: '{{ route('holiday.delete', ['box_slug' => request()->route('box_slug')]) }}',
            type: 'POST',
            data: {
                id: deleteHolidayId,
                _method: 'DELETE',
                _token: $('meta[name="csrf-token"]').attr('content'),
                force: force
            },
            success: function(response) {
                if (response.status === 'warning') {
                    $('#delete_modal_message').html('<strong class="text-danger">Warning:</strong> ' + response.message);
                    $('#confirm_delete_btn').data('force', true);
                    $('#confirm_delete_btn').text('Yes, Force Delete');
                    showToast(response.message, 'warning');
                } else if (response.status === true) {
                    showToast(response.message || 'Holiday deleted successfully!', 'danger');
                    $('#delete_modal').modal('hide');
                    $('#confirm_delete_btn').data('force', false);
                    $('#confirm_delete_btn').text('Yes, Delete');
                    fetchYears();
                } else {
                    alert('Delete failed: ' + response.message);
                }
            },
            error: function() {
                alert('Error deleting holiday.');
            }
        });
    });
}

function showToast(message, type = 'success') {
    const toastContainer = $('#dynamicToast');
    const toastMessage = $('#dynamicToastMessage');
    toastMessage.text(message);
    toastContainer.removeClass('bg-success bg-danger bg-warning');
    if (type === 'success') toastContainer.addClass('bg-success');
    else if (type === 'danger') toastContainer.addClass('bg-danger');
    else if (type === 'warning') toastContainer.addClass('bg-warning');
    const toast = new bootstrap.Toast(toastContainer[0]);
    toast.show();
}

// Manage Columns functionality
$(document).ready(function() {
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

        // Reload all accordion tables to apply column visibility
        $('.accordion-collapse.show').each(function() {
            const collapseId = $(this).attr('id');
            const year = collapseId.replace('collapse-', '');
            fetchHolidayDataByYear(year);
        });

        // Close modal
        $('#manageColumnsModal').modal('hide');
    });
});
    </script>
@endpush


@endsection
