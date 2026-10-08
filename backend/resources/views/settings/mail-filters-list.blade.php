@extends('layouts.main')
@section('content')
@section('title', 'Filters - ' . $inbox->email)
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">Filters for {{ $inbox->email }}</h4>
                        </div>
                        <div class="col-4 text-end">
                            <a href="{{ box_route('settings.general.email_inboxes') }}" class="btn btn-secondary btn-sm">
                                <i class="ti ti-arrow-left me-2"></i>Back to Inboxes
                            </a>
                        </div>
                    </div>
                </div>

                @include('settings.partials.nav')

                <div class="row">
                    <div class="col-xl-3 col-lg-12 theiaStickySidebar">
                        @include('settings.partials.sidebar')
                    </div>
                    <div class="col-xl-9 col-lg-12">
                        <div class="card mb-4">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Active Multi-Criteria Filters</h5>
                                <button type="button" class="btn btn-primary" onclick="openFilterModal()">
                                    <i class="ti ti-square-rounded-plus me-2"></i>Add New Filter
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Filter Name</th>
                                                <th>Criteria</th>
                                                <th>Action Mapping</th>
                                                <th>Last Lead</th>
                                                <th>Status</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($filters as $filter)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $filter->name }}</strong>
                                                    </td>
                                                    <td>
                                                        <div class="small">
                                                            @if($filter->from) <div><strong>From:</strong> {{ $filter->from }}</div> @endif
                                                            @if($filter->subject) <div><strong>Subject:</strong> {{ $filter->subject }}</div> @endif
                                                            @if($filter->has_words) <div><strong>Has:</strong> {{ Str::limit($filter->has_words, 20) }}</div> @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="small">
                                                            <div><strong>Source:</strong> {{ $filter->leadSource->name ?? 'N/A' }}</div>
                                                            <div><strong>Template:</strong> {{ $filter->template->name ?? 'N/A' }}</div>
                                                            @if($filter->assignee) <div><strong>Assign:</strong> {{ $filter->assignee->name }}</div> @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="small text-muted">
                                                            @if($filter->last_lead_at)
                                                                {{ $filter->last_lead_at->format('d-M-Y H:i') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($filter->status === 'Active')
                                                            <span class="badge bg-soft-success text-success">Active</span>
                                                        @else
                                                            <span class="badge bg-soft-danger text-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="dropdown dropdown-action">
                                                            <a href="#" class="btn-action-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ti ti-dots-vertical"></i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a class="dropdown-item" href="javascript:void(0);">
                                                                    <i class="ti ti-edit me-2"></i>Edit
                                                                </a>
                                                                <a class="dropdown-item text-danger" href="javascript:void(0);">
                                                                    <i class="ti ti-trash me-2"></i>Delete
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted">No filters defined for this inbox.</td>
                                                </tr>
                                            @endforelse
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

<!-- Add Mail Filter Offcanvas -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
    <div class="offcanvas-header border-bottom justify-content-between">
        <h4>Configure Mail Filter</h4>
        <button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body p-4">
        <form id="mail_filter_form" action="{{ route('settings.mail_filters.store', ['box_slug' => request()->route('box_slug') ?? ($boxUser->box->slug ?? 'default')]) }}" method="POST">
            @csrf
            <input type="hidden" name="inbox_id" value="{{ $inbox->id }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Name <span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" name="name" id="filter_name" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Template <span class="text-danger">*</span></label>
                                <select class="select form-control" name="template_id" id="template_id" required>
                                    <option value="">--Select--</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label d-flex justify-content-between align-items-center">
                                    <span>Lead Source <span class="text-danger"> *</span></span>
                                    <a href="javascript:void(0);" onclick="quickAddSource()" class="text-primary fw-bold" style="font-size:12px;">
                                        <i class="ti ti-plus"></i> Add
                                    </a>
                                </label>
                                <select class="select form-control" name="lead_source_id" id="lead_source_id" required>
                                    <option value="">--Select--</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">From </label>
                                <input type="text" class="form-control" name="from" id="from">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Subject </label>
                                <input type="text" class="form-control" name="subject" id="subject">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="col-form-label">Lead Status </label>
                                <select class="select form-control" name="lead_status_id" id="lead_status_id">
                                    <option value="">--Select--</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="col-form-label">Round Robin Lead Allocation</label>
                                <select class="select form-control" name="round_robin" id="round_robin">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6" id="assigned_to_div">
                            <div class="mb-3 ">
                                <label class="col-form-label">Assigned To</label>
                                <select class="select form-control" name="assigned_to" id="assigned_to">
                                    <option value="">--Select--</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 d-none" id="round_robin_users_div">
                            <div class="mb-3 ">
                                <label class="col-form-label">Round Robin Users <span class="badge bg-info ms-1 robin-count-badge">0 Selected</span></label>
                                <select class="select form-control" name="round_robin_users[]" id="round_robin_users" multiple="multiple">
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 d-none" id="shifts_config_div">
                            <div class="mb-3">
                                <label class="col-form-label">Enable Round Robin Shifts</label>
                                <select name="enable_shifts" id="enable_shifts" class="form-select select">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                            
                            <div id="shifts_container" class="mb-4" style="display:none;">
                                <h6 class="fw-bold mb-3 border-bottom pb-2">User Shifts Configuration</h6>
                                <div id="shifts_list"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="col-form-label">Status</label>
                                <select class="select form-control" name="status" id="status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="col-form-label">Has Words (Multiple keywords/phrases) </label>
                                <select class="form-control" name="has_words[]" id="has_words" multiple="multiple">
                                </select>
                                <small class="text-muted">Type a word and press Enter to add it as a filter criteria.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>

<!-- Quick Add Source Modal -->
<div class="modal fade" id="quick_add_source_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Lead Source</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Source Name <span class="text-danger">*</span></label>
                    <input type="text" id="quick_source_name" class="form-control" placeholder="e.g. Website, LinkedIn">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save_quick_source">Save Source</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    var isFormDataLoaded = false;
    
    function openFilterModal() {
        $('#mail_filter_form')[0].reset();
        var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvas_add'));
        offcanvas.show();
        loadFormData();
    }
    
    function loadFormData() {
        if(isFormDataLoaded) return;
        $.ajax({
            url: '{{ route('settings.mail_filters.form_data', ['box_slug' => request()->route('box_slug') ?? ($boxUser->box->slug ?? 'default')]) }}',
            type: 'GET',
            success: function(res) {
                if (res.success) {
                    var d = res.data;
                    $('#template_id').html('<option value="">--Select--</option>');
                    d.leadSourceTemplates.forEach(i => $('#template_id').append(new Option(i.name, i.id)));
                    $('#lead_source_id').html('<option value="">--Select--</option>');
                    d.leadSources.forEach(i => $('#lead_source_id').append(new Option(i.name, i.id)));
                    $('#lead_status_id').html('<option value="">--Select--</option>');
                    d.leadsStatus.forEach(i => $('#lead_status_id').append(new Option(i.name, i.id)));
                    $('#assigned_to').html('<option value="">--Select--</option>');
                    $('#round_robin_users').empty();
                    d.boxUsers.forEach(i => {
                        $('#assigned_to').append(new Option(i.name + ' (' + i.email + ')', i.id));
                        $('#round_robin_users').append(new Option(i.name, i.id));
                    });
                    window.globalData = { users: d.boxUsers };
                    isFormDataLoaded = true;
                    
                    // Initialize Select2 after data is loaded
                    if ($.fn.select2) {
                        $('.select').select2({
                            dropdownParent: $('#offcanvas_add'),
                            width: '100%',
                            placeholder: '--Select--'
                        });
                    }
                }
            }
        });
    }

    // Toggle Round Robin Fields
    $('#round_robin').on('change', function() {
        if ($(this).val() == '1') {
            $('#round_robin_users_div').removeClass('d-none');
            $('#shifts_config_div').removeClass('d-none');
            $('#assigned_to_div').addClass('d-none');
        } else {
            $('#round_robin_users_div').addClass('d-none');
            $('#shifts_config_div').addClass('d-none');
            $('#assigned_to_div').removeClass('d-none');
        }
    });

    // Handle enable shifts toggle
    $('#enable_shifts').on('change', function() {
        if ($(this).val() == 'Yes') {
            $('#shifts_container').show();
        } else {
            $('#shifts_container').hide();
        }
    });

    // Dynamic Shifts List when users are selected
    $(document).on('change', '#round_robin_users', function () {
        let selectedIds = $(this).val() || [];
        let listContainer = $('#shifts_list');

        // Update badge count
        $('.robin-count-badge').text(selectedIds.length + ' Selected');

        // Remove rows that are no longer selected
        listContainer.find('.user-shift-row').each(function () {
            let userId = $(this).data('user-id').toString();
            if (!selectedIds.includes(userId)) {
                $(this).remove();
            }
        });

        // Add rows for new selections
        selectedIds.forEach(userId => {
            if (listContainer.find(`.user-shift-row[data-user-id="${userId}"]`).length === 0) {
                let user = globalData.users.find(u => u.id == userId);
                if (user) {
                    let newRow = `
                        <div class="card border mb-2 user-shift-row" data-user-id="${userId}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-medium text-dark">${user.name} (${user.email})</span>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="shifts[${userId}][is_default]" value="1" id="default_${userId}">
                                        <label class="form-check-label small" for="default_${userId}">Default</label>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small mb-1">Start Time</label>
                                        <input type="time" name="shifts[${userId}][start]" class="form-control form-control-sm" value="09:00">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small mb-1">End Time</label>
                                        <input type="time" name="shifts[${userId}][end]" class="form-control form-control-sm" value="18:00">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    listContainer.append(newRow);
                }
            }
        });
    });

    // Also initialize simple selects that don't depend on AJAX
    $(document).ready(function() {
        if ($.fn.select2) {
            $('#round_robin, #status, #round_robin_users, #enable_shifts').select2({
                dropdownParent: $('#offcanvas_add'),
                width: '100%'
            });
            
            // Initialize Has Words as tag input
            $('#has_words').select2({
                tags: true,
                tokenSeparators: [','],
                dropdownParent: $('#offcanvas_add'),
                width: '100%',
                placeholder: 'Add keywords...'
            });
        }
    });

    $('#mail_filter_form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        
        // Prepare data
        var formData = form.serializeArray();
        var hasWords = [];
        
        // Find all selected tags and join them
        $('#has_words option:selected').each(function() {
            hasWords.push($(this).val());
        });
        
        // Remove the original has_words[] from formData if present
        formData = formData.filter(item => item.name !== 'has_words[]');
        
        // Add joined has_words as a string for the backend
        formData.push({ name: 'has_words', value: hasWords.join(',') });

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            success: function(res) {
                if (res.success) {
                    toastr.success(res.message);
                    
                    // Close Offcanvas
                    var offcanvasInstance = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvas_add'));
                    if (offcanvasInstance) offcanvasInstance.hide();
                    
                    // Instant UI Update: Append to table
                    const newFilter = `
                        <tr>
                            <td><strong>${$('#filter_name').val()}</strong></td>
                            <td>
                                <div class="small">
                                    ${$('#from').val() ? `<div><strong>From:</strong> ${$('#from').val()}</div>` : ''}
                                    ${$('#subject').val() ? `<div><strong>Subject:</strong> ${$('#subject').val()}</div>` : ''}
                                    ${hasWords.length ? `<div><strong>Has:</strong> ${hasWords.join(', ')}</div>` : ''}
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <div><strong>Source:</strong> ${$('#lead_source_id option:selected').text()}</div>
                                    <div><strong>Template:</strong> ${$('#template_id option:selected').text()}</div>
                                    ${$('#assigned_to').val() ? `<div><strong>Assign:</strong> ${$('#assigned_to option:selected').text()}</div>` : ''}
                                </div>
                            </td>
                            <td><div class="small text-muted">-</div></td>
                            <td><span class="badge bg-soft-success text-success">Active</span></td>
                            <td class="text-end">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="btn-action-icon" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-edit me-2"></i>Edit</a>
                                        <a class="dropdown-item text-danger" href="javascript:void(0);"><i class="ti ti-trash me-2"></i>Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>`;
                    
                    if ($('table tbody tr:first td').length === 1) $('table tbody').empty(); // Handle empty table
                    $('table tbody').prepend(newFilter);
                    form[0].reset();
                    $('#has_words').val(null).trigger('change');
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(xhr) {
                toastr.error('Internal server error');
            }
        });
    });

    function quickAddSource() {
        $('#quick_source_name').val('');
        $('#quick_add_source_modal').modal('show');
    }

    $('#save_quick_source').on('click', function() {
        const name = $('#quick_source_name').val();
        if (!name) return toastr.warning('Please enter a source name.');
        
        $.ajax({
            url: '{{ box_route('store.source') }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', name: name, type: 'source', status: 1 },
            success: function(res) {
                if (res.success) {
                    $('#lead_source_id').append(new Option(res.data.name, res.data.id, true, true)).trigger('change');
                    $('#quick_add_source_modal').modal('hide');
                    toastr.success('Source added.');
                }
            }
        });
    });
</script>
@endpush
@endsection
