@extends('layouts.main')
@section('content')
<style>
    .toggle-required-btn {
        transition: all 0.2s ease;
    }
    .toggle-required-btn:not(:disabled):hover {
        transform: scale(1.05);
        opacity: 0.9;
        cursor: pointer;
    }
</style>
@section('title', 'Manage-Form')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <h4 class="page-title">Manage Leads Form<span class="count-title"></span></h4>
                        </div>
                        <div class="col-8 text-end">
                            <div class="head-icons">
                                <a href="leads.html" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Refresh">
                                    <i class="ti ti-refresh-dot"></i>
                                </a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Collapse" id="collapse-header">
                                    <i class="ti ti-chevrons-up"></i>
                                </a>
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
                                    <input type="text" class="form-control" placeholder="Search Leads">
                                </div>
                            </div>
                            <div class="col-sm-8 text-end">
                                <button type="button" class="btn btn-outline-secondary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                                    <i class="ti ti-columns"></i> Manage Columns
                                </button>
                                <button id="sync-leads-btn" class="btn btn-primary btn-sm">
                                    <i class="ti ti-refresh"></i> Sync Leads Fields
                                </button>
                            </div>
                        </div>
                        <!-- /Search -->
                    </div>
                    <div class="card-body">

                        <!-- Contact List -->
                        <div class="table-responsive custom-table">
                            <table class="table" id="leads_list">
                                <thead class="thead-light">
                                    <tr>

                                        <th class="no-sort"></th>
                                        <th>Name</th>
                                        <th>Object Name</th>
                                        <th>Category</th>
                                        <th>Required</th>
                                        <th>Field</th>
                                        <th>Field Name</th>
                                        <th>Field Type</th>
                                        <th>Placeholder</th>
                                        <th>Created On</th>
                                        <th class="text-end">Action</th>
                                    </tr>
</thead>
                                <tbody id="sortable-fields">
                                    <tr>
                                        <td colspan="11" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
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
                        <!-- /Contact List -->

                    </div>
                </div>

            </div>
        </div>

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




@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
            const tbody = document.getElementById('sortable-fields');
            const scrollContainer = tbody.closest('.table-responsive');
            let scrollSpeed = 10;
            let scrollInterval;

            function startAutoScroll(direction) {
                if (scrollInterval) clearInterval(scrollInterval);
                scrollInterval = setInterval(() => {
                    scrollContainer.scrollTop += direction * scrollSpeed;
                }, 20);
            }

            function stopAutoScroll() {
                if (scrollInterval) {
                    clearInterval(scrollInterval);
                    scrollInterval = null;
                }
            }

            // Initialize Sortable
            new Sortable(tbody, {
                animation: 200,
                ghostClass: 'sortable-ghost',
                handle: '.handle',
                onMove: function(evt) {
                    const rect = scrollContainer.getBoundingClientRect();
                    const mouseY = evt.originalEvent.clientY;
                    const threshold = 40;
                    if (mouseY < rect.top + threshold) {
                        startAutoScroll(-1);
                    } else if (mouseY > rect.bottom - threshold) {
                        startAutoScroll(1);
                    } else {
                        stopAutoScroll();
                    }
                },
                onEnd: function() {
                    stopAutoScroll();
                    const orderedFields = Array.from(tbody.querySelectorAll('tr')).map((row, index) => ({
                        id: row.dataset.id,
                        sequence: index + 1
                    }));
                    fetch("{{ route('admin.system-fields.reorder', ['box_slug' => request()->route('box_slug')]) }}", {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            fields: orderedFields
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status) {
                            console.log("Order updated");
                        } else {
                            if (typeof showToast === 'function') {
                                showToast(data.message || "Failed to update order", "error");
                            } else {
                                alert(data.message || "Failed to update order");
                            }
                        }
                    })
                    .catch(err => {
                        console.error("Reorder error", err);
                        if (typeof showToast === 'function') {
                            showToast("An error occurred while reordering.", "error");
                        } else {
                            alert("An error occurred while reordering.");
                        }
                    });
                }
            });

            // Column Definitions
            const columnDefinitions = [
                { index: 0, name: 'Drag', visible: true },
                { index: 1, name: 'Name', visible: true },
                { index: 2, name: 'Object Name', visible: true },
                { index: 3, name: 'Category', visible: true },
                { index: 4, name: 'Required', visible: true },
                { index: 5, name: 'Field', visible: true },
                { index: 6, name: 'Field Name', visible: true },
                { index: 7, name: 'Field Type', visible: true },
                { index: 8, name: 'Placeholder', visible: true },
                { index: 9, name: 'Created On', visible: true },
                { index: 10, name: 'Action', visible: true }
            ];

            const pageIdentifier = 'manage_leads_form';

            function loadColumnPreferences() {
                $.ajax({
                    url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug")]) }}',
                    type: 'GET',
                    data: { page_identifier: pageIdentifier },
                    success: function(response) {
                        if (response.success && response.data && response.data.length > 0) {
                            response.data.forEach(pref => {
                                const col = columnDefinitions.find(c => c.index == pref.index);
                                if (col) {
                                    col.visible = (pref.visible === true || pref.visible === 'true' || pref.visible === 1 || pref.visible === '1');
                                }
                            });
                        }
                        applyColumnVisibility();
                    },
                    error: function() {
                        applyColumnVisibility();
                    }
                });
            }

            function saveColumnPreferences() {
                $.ajax({
                    url: '{{ route("settings.column.preferences.save", ["box_slug" => request()->route("box_slug")]) }}',
                    type: 'POST',
                    data: {
                        page_identifier: pageIdentifier,
                        column_preferences: columnDefinitions,
                        _token: '{{ csrf_token() }}'
                    }
                });
            }

            function applyColumnVisibility() {
                columnDefinitions.forEach(col => {
                    const index = col.index + 1;
                    if (col.visible) {
                        $(`#leads_list th:nth-child(${index}), #leads_list td:nth-child(${index})`).show();
                    } else {
                        $(`#leads_list th:nth-child(${index}), #leads_list td:nth-child(${index})`).hide();
                    }
                });
            }

            $('#manageColumnsModal').on('show.bs.modal', function() {
                const list = $('#columnsList');
                list.empty();
                columnDefinitions.forEach(col => {
                    list.append(`
                        <div class="list-group-item">
                            <div class="form-check">
                                <input class="form-check-input column-toggle" type="checkbox" data-index="${col.index}" id="col_${col.index}" ${col.visible ? 'checked' : ''}>
                                <label class="form-check-label w-100" for="col_${col.index}">${col.name}</label>
                            </div>
                        </div>
                    `);
                });
            });

            $('#showAllColumns').on('click', function() { $('.column-toggle').prop('checked', true); });
            $('#hideAllColumns').on('click', function() { $('.column-toggle').prop('checked', false); });

            $('#applyColumnChanges').on('click', function() {
                $('.column-toggle').each(function() {
                    const idx = $(this).data('index');
                    const col = columnDefinitions.find(c => c.index == idx);
                    if (col) col.visible = $(this).is(':checked');
                });
                applyColumnVisibility();
                saveColumnPreferences();
                $('#manageColumnsModal').modal('hide');
            });

            // AJAX Loading function
            function loadFields() {
                const $tbody = $('#sortable-fields');
                $tbody.html('<tr><td colspan="11" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
                
                $.ajax({
                    url: "{{ route('app.settings.manage.form.fields', ['box_slug' => request()->route('box_slug')]) }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            $tbody.empty();
                            const fields = response.fields;
                            const finduniqefields = response.finduniqefields;
                            
                            let requiredFields = [];
                            if (typeof finduniqefields === 'string' && finduniqefields !== '') {
                                requiredFields = [finduniqefields.toLowerCase()];
                            } else if (Array.isArray(finduniqefields)) {
                                requiredFields = finduniqefields.map(f => String(f).toLowerCase());
                            }
                            
                            let onlyDisableThisField = null;
                            if (requiredFields.includes('email')) {
                                onlyDisableThisField = 'email';
                            } else if (requiredFields.includes('phone')) {
                                onlyDisableThisField = 'phone';
                            }

                            if (!fields || fields.length === 0) {
                                $tbody.html('<tr><td colspan="11" class="text-center">No fields found.</td></tr>');
                                return;
                            }

                            fields.forEach(field => {
                                const currentField = String(field.field || '').toLowerCase();
                                const isDisableOnlyOne = currentField === onlyDisableThisField;
                                
                                const isCustom = !!field.is_custom;
                                const isRequired = !!(field.is_required || field.required);
                                const isVisible = !!field.is_visible;
                                
                                const rowBg = isCustom ? 'style="background-color: #fff8e1;"' : '';
                                const badgeClass = isCustom ? 'bg-info' : 'bg-primary';
                                const badgeText = isCustom ? 'Custom' : 'System';
                                
                                const requiredBtnClass = isRequired ? 'btn-danger' : 'btn-secondary';
                                const requiredText = isRequired ? 'Yes' : 'No';
                                
                                const requiredDisabledAttr = isDisableOnlyOne ? 'disabled title="This field must remain required"' : 'title="Click to toggle required status"';
                                
                                const visibleBtnClass = isVisible ? 'btn-success' : 'btn-secondary';
                                const visibleText = isVisible ? 'Active' : 'Inactive';
                                const visibleDisabledAttr = isDisableOnlyOne ? 'disabled title="This field is required and cannot be changed"' : '';
                                
                                const deleteBtn = isCustom ? 
                                    `<button class="btn btn-sm btn-danger delete-field-btn" data-id="${field.id}" data-type="custom" title="Delete Custom Field"><i class="ti ti-trash"></i></button>` : 
                                    `<button class="btn btn-sm btn-secondary" disabled title="System fields cannot be deleted"><i class="ti ti-trash"></i></button>`;
                                    
                                const createdAt = field.created_at ? field.created_at.substring(0, 10) : '-';

                                const rowHtml = `
                                    <tr class="draggable-field" data-id="${field.id}" ${rowBg}>
                                        <td><i class="fas fa-grip-vertical text-muted handle" style="cursor: move;"></i></td>
                                        <td>${field.name || '-'}</td>
                                        <td>${field.object || '-'}</td>
                                        <td>
                                            <span class="badge ${badgeClass}">${badgeText}</span>
                                        </td>
                                        <td>
                                            <button
                                                class="btn btn-sm toggle-required-btn ${requiredBtnClass}"
                                                data-id="${field.id}"
                                                data-required="${isRequired ? 1 : 0}"
                                                data-type="${isCustom ? 'custom' : 'system'}"
                                                ${requiredDisabledAttr}>
                                                ${requiredText}
                                            </button>
                                        </td>
                                        <td>${field.field || '-'}</td>
                                        <td>${field.field_name || '-'}</td>
                                        <td>${field.field_type || '-'}</td>
                                        <td>${field.placeholder || '-'}</td>
                                        <td>${createdAt}</td>
                                        <td class="text-end">
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <button
                                                    class="btn btn-sm toggle-visibility-btn ${visibleBtnClass}"
                                                    data-id="${field.id}"
                                                    data-visible="${isVisible ? 1 : 0}"
                                                    data-type="${isCustom ? 'custom' : 'system'}"
                                                    ${visibleDisabledAttr}>
                                                    ${visibleText}
                                                </button>
                                                ${deleteBtn}
                                            </div>
                                        </td>
                                    </tr>
                                `;
                                $tbody.append(rowHtml);
                            });

                            // Apply column visibility preferences
                            applyColumnVisibility();
                        }
                    },
                    error: function() {
                        $tbody.html('<tr><td colspan="11" class="text-center text-danger">Failed to load fields. Please try again.</td></tr>');
                    }
                });
            }

            // Event Delegation for dynamic buttons
            $(document).on('click', '.toggle-visibility-btn', function() {
                var button = $(this);
                var systemId = button.data('id');
                var currentVisible = button.data('visible');
                var type = button.data('type'); 

                $.ajax({
                    url: "{{ route('fields.changefromStatus', ['box_slug' => request()->route('box_slug')]) }}",
                    method: "POST",
                    data: {
                        system_id: systemId,
                        is_visible: currentVisible ? 0 : 1,
                        type: type, 
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status) {
                            var newState = response.is_visible ? 'Active' : 'Inactive';
                            if (typeof showToast === 'function') {
                                showToast('Field is now ' + newState, 'success');
                            }
                            button.data('visible', response.is_visible);
                            if (response.is_visible) {
                                button.removeClass('btn-secondary').addClass('btn-success')
                                    .text('Active');
                            } else {
                                button.removeClass('btn-success').addClass('btn-secondary')
                                    .text('Inactive');
                            }
                        } else {
                            if (typeof showToast === 'function') {
                                showToast('Failed to update status.', 'error');
                            } else {
                                alert('Failed to update status.');
                            }
                        }
                    },
                    error: function() {
                        if (typeof showToast === 'function') {
                            showToast('Something went wrong!', 'error');
                        } else {
                            alert('Something went wrong!');
                        }
                    }
                });
            });

            $(document).on('click', '.toggle-required-btn', function() {
                var button = $(this);
                var fieldId = button.data('id');
                var currentRequired = button.data('required');
                var type = button.data('type');

                $.ajax({
                    url: "{{ route('fields.toggleRequired', ['box_slug' => request()->route('box_slug')]) }}",
                    method: "POST",
                    data: {
                        field_id: fieldId,
                        is_required: currentRequired ? 0 : 1,
                        type: type,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status) {
                            var newState = response.is_required ? 'Required' : 'Optional';
                            if (typeof showToast === 'function') {
                                showToast('Field is now ' + newState, 'success');
                            }
                            button.data('required', response.is_required);
                            if (response.is_required) {
                                button.removeClass('btn-secondary').addClass('btn-danger')
                                    .text('Yes');
                            } else {
                                button.removeClass('btn-danger').addClass('btn-secondary')
                                    .text('No');
                            }
                        } else {
                            if (typeof showToast === 'function') {
                                showToast(response.message || 'Failed to update required status.', 'error');
                            } else {
                                alert(response.message || 'Failed to update required status.');
                            }
                        }
                    },
                    error: function() {
                        if (typeof showToast === 'function') {
                            showToast('Something went wrong!', 'error');
                        } else {
                            alert('Something went wrong!');
                        }
                    }
                });
            });

            $(document).on('click', '.delete-field-btn', function() {
                if(!confirm('Are you sure you want to delete this custom field? This action cannot be undone.')) return;
                
                var button = $(this);
                var id = button.data('id');
                var type = button.data('type');
                
                $.ajax({
                    url: "{{ route('admin.fields.delete', ['box_slug' => request()->route('box_slug')]) }}",
                    method: "DELETE",
                    data: {
                        id: id,
                        type: type,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status) {
                            button.closest('tr').remove();
                            if (typeof showToast === 'function') {
                                showToast(response.message || 'Field deleted successfully', 'success');
                            }
                        } else {
                            if (typeof showToast === 'function') {
                                showToast(response.message || 'Failed to delete field.', 'error');
                            } else {
                                alert(response.message || 'Failed to delete field.');
                            }
                        }
                    },
                    error: function(xhr) {
                        var msg = 'Something went wrong!';
                        if(xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        if (typeof showToast === 'function') {
                            showToast(msg, 'error');
                        } else {
                            alert(msg);
                        }
                    }
                });
            });

            $('#sync-leads-btn').click(function() {
                var btn = $(this);
                btn.prop('disabled', true).html('<i class="ti ti-loader animate-spin"></i> Syncing...');
                
                $.ajax({
                    url: "{{ route('settings.sync.lead_fields', ['box_slug' => request()->route('box_slug')]) }}",
                    method: "GET",
                    success: function(response) {
                        if (response.success) {
                            if (typeof showToast === 'function') {
                                showToast(response.message || 'Sync successful!', 'success');
                            }
                            setTimeout(function() {
                                loadFields();
                                btn.prop('disabled', false).html('<i class="ti ti-refresh"></i> Sync Leads Fields');
                            }, 1000);
                        } else {
                            if (typeof showToast === 'function') {
                                showToast('Sync failed: ' + (response.error || 'Unknown error'), 'error');
                            } else {
                                alert('Sync failed: ' + (response.error || 'Unknown error'));
                            }
                            btn.prop('disabled', false).html('<i class="ti ti-refresh"></i> Sync Leads Fields');
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = xhr.responseJSON?.error || 'Unknown error';
                        if (typeof showToast === 'function') {
                            showToast('Sync failed: ' + errorMsg, 'error');
                        } else {
                            alert('Sync failed: ' + errorMsg);
                        }
                        btn.prop('disabled', false).html('<i class="ti ti-refresh"></i> Sync Leads Fields');
                    }
                });
            });

            // Initial load of columns and fields
            loadColumnPreferences();
            loadFields();
        });
    </script>
@endpush
@endsection
