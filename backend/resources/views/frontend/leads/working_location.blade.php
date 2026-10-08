@extends('layouts.main')
@section('content')
@section('title', 'Working Location')

<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <h4 class="page-title">
                                Working Location List
                            </h4>
                        </div>
                        <div class="col-8 text-end">
                            <div class="head-icons">
                                <a href="javascript:void(0);" onclick="loadWorkingLocations()" data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh">
                                    <i class="ti ti-refresh-dot"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6>Working Location List</h6>
                        <div>
                            <button type="button" class="btn btn-sm btn-white d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                                <i class="ti ti-columns me-1"></i>Manage Columns
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive custom-table">
                            <table class="table" id="working_locations_table">
                                <thead>
                                    <tr>
                                        <th data-column="0">ID</th>
                                        <th data-column="1">Added On</th>
                                        <th data-column="2">Status</th>
                                        <th data-column="3">City</th>
                                        <th data-column="4">State</th>
                                        <th data-column="5">Party Name</th>
                                        <th data-column="6">Company</th>
                                        <th data-column="7">Assigned To</th>
                                        <th data-column="8">Member Since</th>
                                        <th data-column="9">Matured Date</th>
                                        <th data-column="10">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="working_locations_body">
                                    <tr>
                                        <td colspan="11" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="delete_working_location_modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                        <i class="ti ti-trash-x fs-36 text-danger"></i>
                    </div>
                    <h4 class="mb-2">Remove Location?</h4>
                    <p class="mb-0">Are you sure you want to remove this location?</p>
                    <div class="d-flex align-items-center justify-content-center mt-4">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirm_delete_location_btn" class="btn btn-danger">Yes, Delete it</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const pageIdentifier = 'working-location';
    var columnDefinitions = [
        { index: 0, name: 'ID', data: 'id', visible: true },
        { index: 1, name: 'Added On', data: 'added_on', visible: true },
        { index: 2, name: 'Status', data: 'status', visible: true },
        { index: 3, name: 'City', data: 'city', visible: true },
        { index: 4, name: 'State', data: 'state', visible: true },
        { index: 5, name: 'Party Name', data: 'party_name', visible: true },
        { index: 6, name: 'Company', data: 'company', visible: true },
        { index: 7, name: 'Assigned To', data: 'assigned', visible: true },
        { index: 8, name: 'Member Since', data: 'member_since', visible: true },
        { index: 9, name: 'Matured Date', data: 'matureddate', visible: true },
        { index: 10, name: 'Action', data: 'action', visible: true }
    ];

    let workingLocationTable;

    $(document).ready(function() {
        loadColumnPreferences(function() {
            initDataTable();
        });
    });

    function loadColumnPreferences(callback) {
        $.ajax({
            url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug")]) }}',
            type: 'GET',
            data: {
                page_identifier: pageIdentifier
            },
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
                    } catch (e) {
                        console.error('Error parsing preferences:', e);
                    }
                }
                if (callback) callback();
            },
            error: function() {
                console.error('Error loading preferences');
                if (callback) callback();
            }
        });
    }

    function saveColumnPreferences() {
        $.ajax({
            url: '{{ route("settings.column.preferences.save", ["box_slug" => request()->route("box_slug")]) }}',
            type: 'POST',
            data: {
                page_identifier: pageIdentifier,
                column_preferences: columnDefinitions.map(c => ({ index: c.index, visible: c.visible })),
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Preferences saved.');
            }
        });
    }

    function initDataTable() {
        workingLocationTable = $('#working_locations_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('get.working.locations.list', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'GET'
            },
            columns: columnDefinitions.map(col => {
                var colDef = { 
                    data: col.data, 
                    name: col.data, 
                    visible: col.visible 
                };
                if (col.data === 'action') {
                    colDef.orderable = false;
                    colDef.searchable = false;
                }
                if (col.data === 'status') {
                   colDef.render = function(data, type, row) {
                        return `<span class="badge bg-${data === 'Active' ? 'success' : 'danger'}">${data}</span>`;
                   };
                }
                return colDef;
            }),
            order: [[0, 'desc']], // Default sort by ID descending
            lengthMenu: [10, 25, 50, 100],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records..."
            }
        });
    }

    // Refresh function exposed globally if needed
    window.loadWorkingLocations = function() {
        if (workingLocationTable) {
            workingLocationTable.ajax.reload(null, false);
        }
    };

    // --- Manage Columns UI Logic ---
    $('#manageColumnsModal').on('show.bs.modal', function () {
        var listHtml = '';
        columnDefinitions.forEach(function(col) {
            var checked = col.visible ? 'checked' : '';
            listHtml += `
                <div class="list-group-item">
                    <div class="form-check">
                        <input class="form-check-input column-toggle" type="checkbox" value="${col.index}" id="col_${col.index}" ${checked}>
                        <label class="form-check-label" for="col_${col.index}">${col.name}</label>
                    </div>
                </div>
            `;
        });
        $('#columnsList').html(listHtml);
    });

    $('#showAllColumns').on('click', function() {
        $('.column-toggle').prop('checked', true);
    });

    $('#hideAllColumns').on('click', function() {
        $('.column-toggle').prop('checked', false);
    });

    $('#applyColumnChanges').on('click', function() {
        $('.column-toggle').each(function() {
            var index = parseInt($(this).val());
            var isChecked = $(this).is(':checked');
            var col = columnDefinitions.find(c => c.index === index);
            if(col) col.visible = isChecked;
        });
        
        // Update DataTable visibility
        if(workingLocationTable) {
            columnDefinitions.forEach(col => {
                var column = workingLocationTable.column(col.index);
                 // Note: DataTable column index might strictly follow initialization order which matches our array
                if(column.visible() !== col.visible) {
                    column.visible(col.visible);
                }
            });
        }
        
        saveColumnPreferences(); // Save to API
        $('#manageColumnsModal').modal('hide');
    });

    // --- Delete Logic ---
    var locationToDeleteId = null;

    $(document).on('click', '.delete-working-location-btn', function(e) {
        e.preventDefault();
        locationToDeleteId = $(this).data('id');
        $('#delete_working_location_modal').modal('show');
    });

    $('#confirm_delete_location_btn').on('click', function() {
        if (!locationToDeleteId) return;

        var btn = $(this);
        btn.prop('disabled', true).text('Deleting...');

        $.ajax({
            url: "{{ route('activities.delete_location', ['box_slug' => request()->route('box_slug')]) }}",
            type: 'DELETE',
            data: {
                id: locationToDeleteId,
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                if (res.success) {
                    window.loadWorkingLocations();
                    $('#delete_working_location_modal').modal('hide');
                } else {
                    alert(res.message || 'Failed to delete.');
                }
                btn.prop('disabled', false).text('Yes, Delete it');
            },
            error: function(err) {
                alert('Error deleting location.');
                btn.prop('disabled', false).text('Yes, Delete it');
            }
        });
    });
</script>
@endpush

