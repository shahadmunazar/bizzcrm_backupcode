@extends('layouts.main')
@section('content')
@section('title', 'Email Inboxes')
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">Email Inboxes</h4>
                        </div>
                        <div class="col-4 text-end">
                            <div class="head-icons">
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Collapse" id="collapse-header"><i
                                        class="ti ti-chevrons-up"></i></a>
                            </div>
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
                                <h5 class="card-title mb-0">Email Inboxes List</h5>
                                <a href="{{ box_route('google.connect.box', ['box_slug' => $box->slug, 'current_url' => url()->current()]) }}" class="btn btn-primary">
                                    <i class="ti ti-square-rounded-plus me-2"></i>Add New
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Email</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Last Sync</th>
                                                <th>Active Filters</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($inboxes as $inbox)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar avatar-sm bg-light rounded-circle me-2">
                                                                <i class="fa-brands fa-google text-danger"></i>
                                                            </div>
                                                            <div>
                                                                <div class="fw-bold text-dark">
                                                                    <a href="{{ route('settings.mail_filters.index', ['box_slug' => request()->route('box_slug') ?? ($box->slug ?? 'default'), 'inbox_id' => $inbox->id]) }}" class="text-primary hover-underline">
                                                                        {{ $inbox->email }}
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-soft-info text-info">Gmail API</span></td>
                                                    <td>
                                                        @if($inbox->status === 'Enabled')
                                                            <span class="badge bg-soft-success text-success">Active</span>
                                                        @else
                                                            <span class="badge bg-soft-danger text-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $inbox->last_sync_at ? \Carbon\Carbon::parse($inbox->last_sync_at)->diffForHumans() : 'Never' }}</td>
                                                    <td>
                                                        <div class="d-flex flex-column gap-2">
                                                            @forelse($inbox->filters as $filter)
                                                                <div class="border rounded p-2 bg-light" style="cursor: pointer;" onclick="viewFilterDetails({{ json_encode([
                                                                    'name' => $filter->name,
                                                                    'status' => $filter->status,
                                                                    'from' => $filter->from,
                                                                    'subject' => $filter->subject,
                                                                    'has_words' => $filter->has_words,
                                                                    'template' => $filter->template ? $filter->template->name : 'None',
                                                                    'lead_source' => $filter->leadSource ? $filter->leadSource->name : 'None',
                                                                    'lead_status' => $filter->leadStatus ? $filter->leadStatus->name : 'None',
                                                                    'assigned_to' => $filter->assignee ? $filter->assignee->name : 'None',
                                                                    'round_robin' => $filter->round_robin ? 'Yes' : 'No'
                                                                ]) }})">
                                                                    <div class="fw-bold text-dark d-flex justify-content-between align-items-center mb-1">
                                                                        <span>{{ $filter->name }}</span>
                                                                        @if($filter->status === 'Inactive') 
                                                                            <span class="badge bg-soft-danger text-danger" style="font-size: 10px;">Off</span> 
                                                                        @else
                                                                            <span class="badge bg-soft-success text-success" style="font-size: 10px;">Active</span> 
                                                                        @endif
                                                                    </div>
                                                                    <div class="small text-muted" style="line-height: 1.2;">
                                                                        @if($filter->template) <div><strong>Template:</strong> <span class="text-primary">{{ $filter->template->name }}</span></div> @endif
                                                                        @if($filter->from) <div><strong>From:</strong> {{ $filter->from }}</div> @endif
                                                                        @if($filter->subject) <div><strong>Subject:</strong> {{ $filter->subject }}</div> @endif
                                                                        @if($filter->has_words) <div><strong>Words:</strong> {{ Str::limit($filter->has_words, 40) }}</div> @endif
                                                                        @if(!$filter->template && !$filter->from && !$filter->subject && !$filter->has_words)
                                                                            <div class="fst-italic">Matches all emails</div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <span class="text-muted small">No filters</span>
                                                            @endforelse
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="dropdown dropdown-action">
                                                            <a href="#" class="btn-action-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ti ti-dots-vertical"></i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a class="dropdown-item" href="javascript:void(0);" onclick="openFilterModal('{{ $inbox->id }}', '{{ $inbox->email }}')">
                                                                    <i class="ti ti-plus me-2"></i>Add Filter
                                                                </a>
                                                                <a class="dropdown-item" href="javascript:void(0);" onclick="syncGmail('{{ $inbox->id }}')">
                                                                    <i class="ti ti-refresh me-2"></i>Sync Now
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
                                                    <td colspan="6" class="text-center py-4 text-muted">
                                                        No email inboxes connected. 
                                                        <br>
                                                        <a href="{{ box_route('settings.general.api_keys') }}" class="btn btn-link">Connect Gmail here</a>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
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
                            <form id="mail_filter_form" action="{{ route('settings.mail_filters.store', ['box_slug' => request()->route('box_slug') ?? ($box->slug ?? 'default')]) }}" method="POST">
                                @csrf
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
                                                    <label class="col-form-label d-flex justify-content-between align-items-center">
                                                        <span>Connected Email (Inbox) <span class="text-danger"> *</span></span>
                                                        <a href="{{ route('google.connect.box', ['box_slug' => request()->route('box_slug') ?? ($box->slug ?? 'default')]) }}" class="text-primary fw-bold" style="font-size:12px;">
                                                            <i class="ti ti-plus"></i> Connect Gmail
                                                        </a>
                                                    </label>
                                                    <select class="select form-control" name="inbox_id" id="inbox_id" required>
                                                        <option value="">--Select--</option>
                                                    </select>
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
                                                <div class="mb-3 ">
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
                                                    <label class="col-form-label">Lead Status  </label>
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

                                            <div class="col-md-6">
                                                <div class="mb-3 ">
                                                    <label class="col-form-label">Assigned To</label>
                                                    <select class="select form-control" name="assigned_to" id="assigned_to">
                                                        <option value="">--Select--</option>
                                                    </select>
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
                                                    <label class="col-form-label">Has Words </label>
                                                    <textarea class="form-control" name="has_words" id="has_words"></textarea>
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
                </div>

                    <!-- View Filter Details Modal -->
                    <div class="modal fade" id="view_filter_modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Mail Filter Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                                        <h6 class="mb-0 fs-16 fw-bold" id="detail_filter_name"></h6>
                                        <span id="detail_filter_status" class="badge"></span>
                                    </div>
                                    <div class="row gy-3">
                                        <div class="col-md-6">
                                            <p class="mb-1 text-muted small">Template</p>
                                            <p class="mb-0 fw-semibold text-primary" id="detail_filter_template"></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1 text-muted small">Lead Source</p>
                                            <p class="mb-0 fw-semibold" id="detail_filter_source"></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1 text-muted small">Assigned To</p>
                                            <p class="mb-0 fw-semibold" id="detail_filter_assignee"></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1 text-muted small">Lead Status</p>
                                            <p class="mb-0 fw-semibold" id="detail_filter_lead_status"></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1 text-muted small">Round Robin</p>
                                            <p class="mb-0 fw-semibold" id="detail_filter_round_robin"></p>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-light p-3 rounded mt-4">
                                        <h6 class="mb-3 fs-14 fw-bold">Filter Criteria</h6>
                                        <div class="mb-2">
                                            <span class="text-muted small d-block">From</span>
                                            <span class="fw-semibold" id="detail_filter_from"></span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-muted small d-block">Subject</span>
                                            <span class="fw-semibold" id="detail_filter_subject"></span>
                                        </div>
                                        <div>
                                            <span class="text-muted small d-block">Has Words</span>
                                            <span class="fw-semibold" id="detail_filter_words"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function viewFilterDetails(filter) {
        $('#detail_filter_name').text(filter.name || '-');
        
        var statusBadge = $('#detail_filter_status');
        if (filter.status === 'Active') {
            statusBadge.removeClass('bg-soft-danger text-danger').addClass('bg-soft-success text-success').text('Active');
        } else {
            statusBadge.removeClass('bg-soft-success text-success').addClass('bg-soft-danger text-danger').text('Inactive');
        }

        $('#detail_filter_template').text(filter.template || '-');
        $('#detail_filter_source').text(filter.lead_source || '-');
        $('#detail_filter_assignee').text(filter.assigned_to || '-');
        $('#detail_filter_lead_status').text(filter.lead_status || '-');
        $('#detail_filter_round_robin').text(filter.round_robin || 'No');
        
        $('#detail_filter_from').text(filter.from || 'Any');
        $('#detail_filter_subject').text(filter.subject || 'Any');
        $('#detail_filter_words').text(filter.has_words || 'None');

        var modal = new bootstrap.Modal(document.getElementById('view_filter_modal'));
        modal.show();
    }

    var isFormDataLoaded = false;
    
    function openFilterModal(inboxId, inboxEmail) {
        // Reset form
        $('#mail_filter_form')[0].reset();
        
        // Use a hidden input or just set the value and trigger change for select2 if used
        if ($('#inbox_id').hasClass('select2-hidden-accessible')) {
            $('#inbox_id').val(inboxId).trigger('change');
        } else {
            $('#inbox_id').val(inboxId);
        }
        
        $('#filter_name').val('Filter for ' + inboxEmail);
        
        // Show Offcanvas
        var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvas_add'));
        offcanvas.show();
        
        // Load data if not already loaded
        loadFormData(inboxId);
    }
    
    function loadFormData(selectedInboxId = null) {
        if(isFormDataLoaded) {
            if (selectedInboxId) {
                $('#inbox_id').val(selectedInboxId).trigger('change');
            }
            return;
        }
        
        $.ajax({
            url: '{{ route('settings.mail_filters.form_data', ['box_slug' => request()->route('box_slug') ?? ($box->slug ?? 'default')]) }}',
            type: 'GET',
            success: function(res) {
                if (res.success) {
                    var d = res.data;
                    
                    // Clear and Populate Gmail Inboxes
                    $('#inbox_id').html('<option value="">--Select--</option>');
                    d.gmailInboxes.forEach(function(i) {
                        $('#inbox_id').append(new Option(i.email, i.id));
                    });
                    
                    // Populate Templates
                    $('#template_id').html('<option value="">--Select--</option>');
                    d.leadSourceTemplates.forEach(function(i) {
                        $('#template_id').append(new Option(i.name, i.id));
                    });
                    
                    // Populate Sources
                    $('#lead_source_id').html('<option value="">--Select--</option>');
                    d.leadSources.forEach(function(i) {
                        $('#lead_source_id').append(new Option(i.name, i.id));
                    });
                    
                    // Populate Status
                    $('#lead_status_id').html('<option value="">--Select--</option>');
                    d.leadsStatus.forEach(function(i) {
                        $('#lead_status_id').append(new Option(i.name, i.id));
                    });
                    
                    // Populate Users
                    $('#assigned_to').html('<option value="">--Select--</option>');
                    d.boxUsers.forEach(function(i) {
                        $('#assigned_to').append(new Option(i.name, i.id));
                    });
                    
                    isFormDataLoaded = true;
                    
                    // Set the inbox if it was passed
                    if (selectedInboxId) {
                        $('#inbox_id').val(selectedInboxId).trigger('change');
                    }
                }
            }
        });
    }

    $('#mail_filter_form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serialize();
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            success: function(res) {
                if (res.success) {
                    toastr.success(res.message || 'Filter saved successfully');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    toastr.error(res.message || 'Error saving filter');
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    var msg = '';
                    Object.keys(errors).forEach(key => msg += errors[key][0] + '\n');
                    toastr.error(msg);
                } else {
                    toastr.error('Internal server error');
                }
            }
        });
    });

    function quickAddSource() {
        const name = prompt('Enter new Lead Source name:');
        if (!name) return;

        $.ajax({
            url: '{{ box_route('store.source') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                name: name,
                type: 'source',
                status: 1
            },
            success: function(res) {
                if (res.success) {
                    const newSource = res.data;
                    $('#lead_source_id').append(new Option(newSource.name, newSource.id, true, true)).trigger('change');
                    toastr.success('Source added and selected.');
                } else {
                    toastr.error(res.message || 'Failed to add source.');
                }
            },
            error: function() {
                toastr.error('Error creating source');
            }
        });
    }

    function syncGmail(id) {
        toastr.info('Syncing emails for this inbox...');
        $.ajax({
            url: "{{ box_route('google.sync_emails') }}",
            type: "GET",
            data: { inbox_id: id },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Failed to sync emails. Please check your connection.');
            }
        });
    }
</script>
@endpush

@endsection
