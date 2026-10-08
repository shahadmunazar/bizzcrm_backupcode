@extends('layouts.main')
@section('content')
@section('title','Custom Fields')
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
                                    <a href="{{ route('settings.general.custom_fields', ['box_slug' => request()->route('box_slug')]) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
                                    <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-original-title="Collapse" id="collapse-header"><i
                                            class="ti ti-chevrons-up"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Page Header -->

                    @include('settings.partials.nav')

                    <div class="row">
                        <div class="col-xl-3 col-lg-12 theiaStickySidebar">

                            @include('settings.partials.sidebar')

                        </div>

                        <div class="col-xl-9 col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h5>Custom Fields</h5>
                                        <a href="" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#add_fields">
                                            <i class="ti ti-square-rounded-plus me-2"></i>Add Field
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
                                        <div class="dropdown">
                                            <a href="javascript:void(0);" class="dropdown-toggle"
                                                data-bs-toggle="dropdown"><i class="ti ti-sort-ascending-2 me-2"></i>Sort
                                            </a>
                                            <div class="dropdown-menu  dropdown-menu-start">
                                                <ul>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i class="ti ti-circle-chevron-right me-1"></i>Ascending
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i class="ti ti-circle-chevron-right me-1"></i>Descending
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i class="ti ti-circle-chevron-right me-1"></i>Recently Viewed
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i class="ti ti-circle-chevron-right me-1"></i>Recently Added
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="dropdown">
                                            <a href="javascript:void(0);" class="btn bg-soft-purple text-purple"
                                                data-bs-toggle="dropdown" data-bs-auto-close="outside"><i
                                                    class="ti ti-columns-3 me-2"></i>Manage Columns</a>
                                            <div class="dropdown-menu  dropdown-menu-md-end dropdown-md p-3">
                                                <h4 class="mb-2 fw-semibold">Want to manage datatables?</h4>
                                                <p class="mb-3">Please drag and drop your column to reorder your table and
                                                    enable see option as you want.</p>
                                                <div class="border-top pt-3">
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <p class="mb-0 d-flex align-items-center"><i
                                                                class="ti ti-grip-vertical me-2"></i>Module</p>
                                                        <div class="status-toggle">
                                                            <input type="checkbox" id="col-name" class="check">
                                                            <label for="col-name" class="checktoggle"></label>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <p class="mb-0 d-flex align-items-center"><i
                                                                class="ti ti-grip-vertical me-2"></i>Label</p>
                                                        <div class="status-toggle">
                                                            <input type="checkbox" id="col-phone" class="check">
                                                            <label for="col-phone" class="checktoggle"></label>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <p class="mb-0 d-flex align-items-center"><i
                                                                class="ti ti-grip-vertical me-2"></i>Type</p>
                                                        <div class="status-toggle">
                                                            <input type="checkbox" id="col-email" class="check">
                                                            <label for="col-email" class="checktoggle"></label>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <p class="mb-0 d-flex align-items-center"><i
                                                                class="ti ti-grip-vertical me-2"></i>Default Value</p>
                                                        <div class="status-toggle">
                                                            <input type="checkbox" id="col-tag" class="check">
                                                            <label for="col-tag" class="checktoggle"></label>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <p class="mb-0 d-flex align-items-center"><i
                                                                class="ti ti-grip-vertical me-2"></i>Required/Disable</p>
                                                        <div class="status-toggle">
                                                            <input type="checkbox" id="col-loc" class="check">
                                                            <label for="col-loc" class="checktoggle"></label>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <p class="mb-0 d-flex align-items-center"><i
                                                                class="ti ti-grip-vertical me-2"></i>Status</p>
                                                        <div class="status-toggle">
                                                            <input type="checkbox" id="col-rate" class="check">
                                                            <label for="col-rate" class="checktoggle"></label>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <p class="mb-0 d-flex align-items-center"><i
                                                                class="ti ti-grip-vertical me-2"></i>Action</p>
                                                        <div class="status-toggle">
                                                            <input type="checkbox" id="col-action" class="check">
                                                            <label for="col-action" class="checktoggle"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive custom-table no-pagination remove-scroll">
                                        <table id="custom-fieldsss" class="table datatable">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th class="no-sort">
                                                        <label class="checkboxs"><input type="checkbox"
                                                                id="select-all"><span class="checkmarks"></span></label>
                                                    </th>
                                                    <th>Module</th>
                                                    <th>Label</th>
                                                    <th>Type</th>
                                                    <th>Default Value</th>
                                                    <th>Required/Disable</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Wrapper -->

    <!-- Add Custom Fields -->
    <div class="modal fade" id="add_fields" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Custom Fields</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="custom-field-form" method="POST">
                    @csrf
                    <div class="modal-body pb-0">
                        <div class="mb-3">
                            <label class="col-form-label">Custom Fields For <span class="text-danger">*</span></label>
                            <select name="custom_type" class="form-select">
                                <option value="">Choose</option>
                                <option value="contacts">Contacts</option>
                                <option value="projects">Projects</option>
                                <option value="tasks">Tasks</option>
                                <option value="leads">Leads</option>
                                <option value="salary_earnings">Salary - Earnings</option>
                                <option value="salary_reimbursements">Salary - Reimbursements</option>
                                <option value="salary_contributions">Salary - Contributions</option>
                                <option value="salary_deductions">Salary - Deductions</option>
                                <option value="salary_onetime">Salary - One Time</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Label (name) <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="label_name" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Input Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select">
                                <option value="">Choose</option>
                                <option value="text">Text</option>
                                <option value="textarea">Text Area</option>
                                <option value="select">Select</option>
                                <option value="number">Number</option>
                                <option value="checkbox">MultiCheckBox</option>
                                <option value="date">Date</option>
                                <option value="month">Month</option>
                                <option value="number">Number</option>
                                <option value="radio">Radio</option>
                                <option value="tel">tel</option>
                                <option value="range">Range</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Column Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="col_name" id="col_name" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Error Message</label>
                            <textarea name="error_message" class="form-control"></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Quick Edit</label><br>
                            <input type="radio" name="quick_edit" value="1" checked> Yes
                            <input type="radio" name="quick_edit" value="0"> No
                        </div>

                        {{-- <div class="mb-3">
                            <label class="col-form-label">Ordering</label>
                            <input type="number" name="ordering" class="form-control" value="0">
                        </div> --}}

                        <div class="mb-3 d-flex gap-4">
                            <div>
                                <label>Required</label><br>
                                <input type="radio" name="required" value="1" checked> Yes
                                <input type="radio" name="required" value="0"> No
                            </div>
                            <div>
                                <label>Status</label><br>
                                <input type="radio" name="status" value="Enabled" checked> Enabled
                                <input type="radio" name="status" value="Disabled"> Disabled
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 d-none" id="field-options-wrapper">
                        <label class="col-form-label">Options <span class="text-danger">*</span></label>
                        <div class="option-tags-container border rounded p-2 d-flex flex-wrap" id="options-container">
                            <input type="text" id="option-input" class="form-control border-0 p-1 flex-grow-1"
                                placeholder="Type & press Enter" />
                        </div>
                        <input type="hidden" name="params" id="params-hidden" />
                    </div>


                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- /Add Custom Fields -->

    <!-- Edit Custom Fields -->
    <div class="modal fade" id="edit_fields" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Custom Fields</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
    <form id="edit-field-form" method="POST">
    @csrf
    <input type="hidden" name="id">

    <div class="modal-body">
        <div class="row">
            <!-- Custom Fields For -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Custom Fields For <span class="text-danger">*</span></label>
                <select name="custom_type" class="form-select" required>
                    <option value="">Choose</option>
                    <option value="contacts">Contacts</option>
                    <option value="projects">Projects</option>
                    <option value="tasks">Tasks</option>
                    <option value="leads">Leads</option>
                    <option value="salary_earnings">Salary - Earnings</option>
                    <option value="salary_reimbursements">Salary - Reimbursements</option>
                    <option value="salary_contributions">Salary - Contributions</option>
                    <option value="salary_deductions">Salary - Deductions</option>
                    <option value="salary_onetime">Salary - One Time</option>
                </select>
            </div>

            <!-- Label -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Label <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <!-- Input Type -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Input Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required>
                    <option value="">Choose</option>
                    <option value="text">Text</option>
                    <option value="textarea">Text Area</option>
                    <option value="select">Select</option>
                    <option value="number">Number</option>
                    <option value="checkbox">MultiCheckBox</option>
                    <option value="date">Date</option>
                    <option value="month">Month</option>
                    <option value="radio">Radio</option>
                    <option value="tel">Tel</option>
                    <option value="range">Range</option>
                </select>
            </div>

            <div class="mb-3">
                            <label class="col-form-label">Column Name (unique key) <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="col_name" class="form-control" disabled>
                        </div>
            <!-- Default Value -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Default Value</label>
                <input type="text" name="default_value" class="form-control">
            </div>

            <!-- Required -->
            <div class="col-md-4 mb-3">
                <label class="form-label d-block">Required</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="required" id="edit-required-yes" value="1">
                    <label class="form-check-label" for="edit-required-yes">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="required" id="edit-required-no" value="0">
                    <label class="form-check-label" for="edit-required-no">No</label>
                </div>
            </div>

            <!-- Quick Edit -->
            <div class="col-md-4 mb-3">
                <label class="form-label d-block">Quick Edit</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="quick_edit" value="1" id="edit-quick-yes">
                    <label class="form-check-label" for="edit-quick-yes">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="quick_edit" value="0" id="edit-quick-no">
                    <label class="form-check-label" for="edit-quick-no">No</label>
                </div>
            </div>

            <!-- Status -->
            <div class="col-md-4 mb-3">
                <label class="form-label d-block">Status</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" value="Enabled" id="edit-status-enabled">
                    <label class="form-check-label" for="edit-status-enabled">Active</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" value="Disabled" id="edit-status-disabled">
                    <label class="form-check-label" for="edit-status-disabled">Inactive</label>
                </div>
            </div>

            <!-- Options (Hidden by default, shown for select/radio/checkbox) -->
            <div class="col-md-12 mb-3 d-none" id="edit-field-options-wrapper">
                <label class="form-label">Options <span class="text-danger">*</span></label>
                <div class="option-tags-container border rounded p-2 d-flex flex-wrap" id="edit-options-container">
                    <input type="text" id="edit-option-input" class="form-control border-0 p-1 flex-grow-1"
                        placeholder="Type & press Enter" />
                </div>
                <input type="hidden" name="params" id="edit-params-hidden" />
            </div>
        </div>
    </div>

    <!-- Footer Buttons -->
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>


            </div>
        </div>
    </div>
    <!-- /Edit Custom Fields -->

    <!-- Delete Fields -->
    <div class="modal fade" id="delete_fields" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <form id="delete-field-form">
                    @csrf
                    @method('DELETE')
                    <div class="text-center">
                        <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                            <i class="ti ti-trash-x fs-36 text-danger"></i>
                        </div>
                        <h4 class="mb-2">Remove Field?</h4>
                        <p class="mb-0">Are you sure you want to remove it?</p>
                        <div class="d-flex align-items-center justify-content-center mt-4">
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Yes, Delete it</button>
                        </div>
                    </div>
                    <input type="hidden" id="delete-field-id">
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- /Delete Fields -->
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> --}}

    @push('scripts')
    <script>
        function reloadSourceTable() {
        if (sourceTable) {
            sourceTable.ajax.reload(null, false);
        }
    }
    function getSourceRouteUrl() {
        return "{{ route('settings.general.custom_fields', ['box_slug' => request()->route('box_slug')]) }}";
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
        $(document).ready(function() {
            const $optionWrapper = $('#field-options-wrapper');
            const $optionInput = $('#option-input');
            const $paramsHidden = $('#params-hidden');
            const $optionsContainer = $('#options-container');

            const $editOptionWrapper = $('#edit-field-options-wrapper');
            const $editOptionInput = $('#edit-option-input');
            const $editParamsHidden = $('#edit-params-hidden');
            const $editOptionsContainer = $('#edit-options-container');

            let options = [];
            let editOptions = [];

            // Check if DataTable is already initialized and destroy it first
            const tableElement = $('#custom-fieldsss');
            if ($.fn.DataTable.isDataTable(tableElement)) {
                tableElement.DataTable().destroy();
            }

            const table = tableElement.DataTable({
    ajax: {
        url: "{{ route('settings.general.custom_fields', ['box_slug' => request()->route('box_slug')]) }}",
        dataSrc: function (json) {
            return json.success ? json.data : [];
        },
        error: xhr => console.error("Error loading data:", xhr.responseText)
    },
    columns: [
        {
            data: null,
            orderable: false,
            render: () => `
                <label class="checkboxs">
                    <input type="checkbox"><span class="checkmarks"></span>
                </label>
            `
        },
        {
            data: 'object',
            render: data => `<span class="text-capitalize">${data}</span>`
        },
        {
            data: 'name',
            render: data => `<strong>${data}</strong>`
        },
        {
            data: 'type',
            render: data => `<span class="badge bg-light text-dark">${data}</span>`
        },
        {
            data: 'params',
            render: data => {
                if (!data) return '-';
                try {
                    const parsed = JSON.parse(data);
                    return parsed.options?.join(', ') || '-';
                } catch (e) {
                    return '-';
                }
            }
        },
        {
            data: 'required',
            render: data => data ? 'Required' : 'Optional'
        },
        {
            data: 'status',
            render: data =>
                `<span class="badge ${data === 'Enabled' ? 'bg-success' : 'bg-danger'}">${data}</span>`
        },
        {
            data: null,
            orderable: false,
            render: (data, type, row) => `
                <div class="d-flex justify-content-start gap-2">
                    <button class="btn btn-sm btn-primary edit-btn"
                        data-id="${row.id}"
                        data-object="${row.object}"
                        data-name="${row.name}"
                        data-type="${row.type}"
                        data-required="${row.required}"
                        data-status="${row.status}">
                        <i class="ti ti-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#delete_fields">
    <i class="ti ti-trash"></i> Delete
</button>
                </div>
            `
        }
    ]
});

$('#label_name').on('input', function () {
            let labelText = $(this).val().trim().toLowerCase();
            let slugified = labelText
                .replace(/\s+/g, '_')
                .replace(/[^\w]/g, '');

            $('#col_name').val(slugified);
        });

    let deleteId = null;
    $(document).on('click', '.delete-btn', function () {
        deleteId = $(this).data('id');
        $('#delete-field-id').val(deleteId);
        $('#delete_fields').modal('show');
    });
    $('#delete-field-form').on('submit', function (e) {
        e.preventDefault();

        let fieldId = $('#delete-field-id').val();

        $.ajax({
            url: "{{ route('settings.custom.delete',['box_slug' => request()->route('box_slug')]) }}",
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}',
                id: fieldId,
            },
            success: function (response) {
                $('#delete_fields').modal('hide');
                showToast(response.message || 'Field deleted successfully.');
                // Reload DataTable instead of full page reload
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                showToast('Error deleting field.', 'error');
            }
        });
    });

$(document).on('click', '.edit-btn', function () {
    const rowData = table.row($(this).closest('tr')).data();
    populateEditFieldForm(rowData);
    $('#edit_fields').modal('show');
});
function populateEditFieldForm(data) {
    const form = document.getElementById('edit-field-form');
    if (!form || !data) return;
    form.querySelector('input[name="id"]').value = data.id ?? '';
    form.querySelector('select[name="custom_type"]').value = data.object || '';
    form.querySelector('select[name="type"]').value = data.type || '';
    form.querySelector('input[name="name"]').value = data.name || '';
    form.querySelector('input[name="col_name"]').value = data.col_name || '';
    form.querySelector('input[name="default_value"]').value = data.default_value || '';
    if (data.required !== undefined) {
        form.querySelector(`input[name="required"][value="${data.required ? 1 : 0}"]`).checked = true;
    }
    if (data.quick_edit !== undefined) {
        form.querySelector(`input[name="quick_edit"][value="${data.quick_edit ? 1 : 0}"]`).checked = true;
    }
    if (data.status) {
        form.querySelector(`input[name="status"][value="${data.status}"]`).checked = true;
    }

    // Handle Options
    const optionTypes = ['select', 'checkbox', 'multi_checkbox', 'radio'];
    if (optionTypes.includes(data.type)) {
        $('#edit-field-options-wrapper').removeClass('d-none');
        try {
            // Data might be stringified JSON or already an object depending on how it comes from server
            let params = data.params;
            if (typeof params === 'string') {
                params = JSON.parse(params);
            }
            
            // Handle both structure: {options: [...]} or just {0: 'val1', 1: 'val2'}
            // Based on our tinker test, it was {options: [{value: '...', label: '...'}, ...]}
            editOptions = [];
            if (params && params.options) {
                params.options.forEach(opt => {
                    const val = typeof opt === 'object' ? (opt.label || opt.name || opt.value || '') : opt;
                    if (val) editOptions.push(val);
                });
            }
            renderEditOptions();
        } catch (e) {
            console.error("Error parsing params:", e);
            editOptions = [];
            renderEditOptions();
        }
    } else {
        $('#edit-field-options-wrapper').addClass('d-none');
        editOptions = [];
        renderEditOptions();
    }
}
        const optionTypes = ['select', 'checkbox', 'multi_checkbox', 'radio'];
        
        // Add form change listener
        $('#custom-field-form select[name="type"]').on('change', function() {
            const selected = $(this).val();
            if (optionTypes.includes(selected)) {
                $optionWrapper.removeClass('d-none');
            } else {
                $optionWrapper.addClass('d-none');
                options = [];
                renderOptions();
            }
        });
            $optionInput.on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const value = $optionInput.val().trim();
                    if (value !== '' && !options.includes(value)) {
                        options.push(value);
                        $optionInput.val('');
                        renderOptions();
                    }
                }
            });
            function renderOptions() {
                $optionsContainer.find('.option-tag').remove();
                options.forEach(function(option, index) {
                    const tag = $(`
                <span class="option-tag badge bg-primary text-white me-2 mb-2 d-flex align-items-center">
                    ${option}
                    <button type="button" class="btn-close btn-close-white btn-sm ms-2" aria-label="Remove" data-index="${index}"></button>
                </span>
            `);
                    $optionsContainer.prepend(tag);
                });
                $paramsHidden.val(JSON.stringify({
                    options
                }));
            }
            $optionsContainer.on('click', '.btn-close', function() {
                const index = $(this).data('index');
                options.splice(index, 1);
                renderOptions();
            });

            // Edit Options Logic
            $('#edit-field-form select[name="type"]').on('change', function() {
                const selected = $(this).val();
                if (optionTypes.includes(selected)) {
                    $editOptionWrapper.removeClass('d-none');
                } else {
                    $editOptionWrapper.addClass('d-none');
                    editOptions = [];
                    renderEditOptions();
                }
            });

            $editOptionInput.on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const value = $editOptionInput.val().trim();
                    if (value !== '' && !editOptions.includes(value)) {
                        editOptions.push(value);
                        $editOptionInput.val('');
                        renderEditOptions();
                    }
                }
            });

            function renderEditOptions() {
                $editOptionsContainer.find('.option-tag').remove();
                editOptions.forEach(function(option, index) {
                    const tag = $(`
                        <span class="option-tag badge bg-primary text-white me-2 mb-2 d-flex align-items-center">
                            ${option}
                            <button type="button" class="btn-close btn-close-white btn-sm ms-2" aria-label="Remove" data-index="${index}"></button>
                        </span>
                    `);
                    $editOptionsContainer.prepend(tag);
                });
                $editParamsHidden.val(JSON.stringify({
                    options: editOptions
                }));
            }

            $editOptionsContainer.on('click', '.btn-close', function() {
                const index = $(this).data('index');
                editOptions.splice(index, 1);
                renderEditOptions();
            });
            //edit-field-form
            $('#edit-field-form').on('submit', function(e) {
                e.preventDefault();
                
                const type = $(this).find('select[name="type"]').val();
                if (optionTypes.includes(type) && editOptions.length === 0) {
                    showToast('At least one option is required.', 'error');
                    return;
                }

                const formData = new FormData(this);
                console.log("formm",formData);
                $.ajax({
                    url: "{{ route('settings.custom_fields.update', ['box_slug' => request()->route('box_slug')]) }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        showToast('Custom field updated successfully!');
                        // Reload DataTable instead of full page reload
                        table.ajax.reload(null, false);
                        $('#edit_fields').modal('hide');
                        $('#edit-field-form')[0].reset();
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || {};
                        const errorMsg = xhr.responseJSON?.message || 'Error updating custom field';
                        showToast('Error: ' + errorMsg, 'error');
                    }
                });
            });
            $('#custom-field-form').on('submit', function(e) {
                e.preventDefault();

                const type = $(this).find('select[name="type"]').val();
                if (optionTypes.includes(type) && options.length === 0) {
                    showToast('At least one option is required.', 'error');
                    return;
                }

                const formData = new FormData(this);

                $.ajax({
                    url: "{{ route('settings.custom_fields.store', ['box_slug' => request()->route('box_slug')]) }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        showToast('Custom field saved successfully!');
                        // Reload DataTable instead of full page reload
                        table.ajax.reload(null, false);
                        $('#add_fields').modal('hide');
                        $('#custom-field-form')[0].reset();
                        options = [];
                        renderOptions();
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || {};
                        const errorMsg = xhr.responseJSON?.message || 'Error saving custom field';
                        showToast('Error: ' + errorMsg, 'error');
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
