@extends('layouts.main')
@section('content')
@section('title', 'Deal Settings')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-sm-8">
                            <h4 class="page-title">Deal Settings</h4>
                        </div>
                        <div class="col-sm-4 text-sm-end">
                            <div class="head-icons">
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh" onclick="window.location.reload()"><i class="ti ti-refresh-dot"></i></a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-bottom mb-4">
                            <li class="nav-item"><a class="nav-link active" href="#status_tab" data-bs-toggle="tab">Status</a></li>
                            <li class="nav-item"><a class="nav-link" href="#period_tab" data-bs-toggle="tab">Period</a></li>
                            <li class="nav-item"><a class="nav-link" href="#source_tab" data-bs-toggle="tab">Source</a></li>
                            <li class="nav-item"><a class="nav-link" href="#priority_tab" data-bs-toggle="tab">Priority</a></li>
                            <li class="nav-item"><a class="nav-link" href="#tag_tab" data-bs-toggle="tab">Tags</a></li>
                        </ul>

                        <div class="tab-content">
                            <!-- Status Tab -->
                            <div class="tab-pane show active" id="status_tab">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="fw-semibold">Deal Statuses</h5>
                                    <a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="openAddModal('status')"><i class="ti ti-plus me-1"></i>Add Status</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="thead-light"><tr><th>Name</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                                        <tbody id="table_status"></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Period Tab -->
                            <div class="tab-pane" id="period_tab">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="fw-semibold">Deal Periods</h5>
                                    <a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="openAddModal('period')"><i class="ti ti-plus me-1"></i>Add Period</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="thead-light"><tr><th>Name</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                                        <tbody id="table_period"></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Source Tab -->
                            <div class="tab-pane" id="source_tab">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="fw-semibold">Deal Sources</h5>
                                    <a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="openAddModal('source')"><i class="ti ti-plus me-1"></i>Add Source</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="thead-light"><tr><th>Name</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                                        <tbody id="table_source"></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Priority Tab -->
                            <div class="tab-pane" id="priority_tab">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="fw-semibold">Deal Priorities</h5>
                                    <a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="openAddModal('priority')"><i class="ti ti-plus me-1"></i>Add Priority</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="thead-light"><tr><th>Name</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                                        <tbody id="table_priority"></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tags Tab -->
                            <div class="tab-pane" id="tag_tab">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="fw-semibold">Deal Tags</h5>
                                    <a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="openAddModal('tag')"><i class="ti ti-plus me-1"></i>Add Tag</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="thead-light"><tr><th>Name</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                                        <tbody id="table_tag"></tbody>
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

<!-- Universal Add/Edit Modal -->
<div class="modal fade" id="crud_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crud_modal_title">Add Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="crud_form">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="crud_type">
                    <input type="hidden" id="crud_id">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="crud_name" required>
                        <span class="text-danger error-text name_error" style="font-size: 12px; margin-top: 5px; display: block;"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" id="crud_status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <span class="text-danger error-text status_error" style="font-size: 12px; margin-top: 5px; display: block;"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function loadData() {
        $.ajax({
            url: "{{ route('manage_users.deal_settings.json', ['box_slug' => request()->route('box_slug')]) }}",
            type: 'GET',
            success: function(res) {
                if(res) {
                    populateTable('status', res.statuses);
                    populateTable('period', res.periods);
                    populateTable('source', res.sources);
                    populateTable('priority', res.priorities);
                    populateTable('tag', res.tags);
                }
            }
        });
    }

    function populateTable(type, data) {
        let html = '';
        if(data && data.length > 0) {
            data.forEach(item => {
                let nameStr = item.name.replace(/'/g, "\\'");
                let badgeClass = item.status === 'active' ? 'bg-success' : 'bg-danger';
                let statusLabel = item.status === 'active' ? 'Active' : 'Inactive';
                html += `<tr>
                    <td>${item.name}</td>
                    <td><span class="badge ${badgeClass}">${statusLabel}</span></td>
                    <td class="text-end">
                        <a href="javascript:void(0);" class="btn btn-sm btn-info" onclick="openEditModal('${type}', ${item.id}, '${nameStr}', '${item.status}')"><i class="ti ti-edit"></i></a>
                        <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="deleteItem('${type}', ${item.id})"><i class="ti ti-trash"></i></a>
                    </td>
                </tr>`;
            });
        } else {
            html = `<tr><td colspan="3" class="text-center text-muted">No records found.</td></tr>`;
        }
        $('#table_' + type).html(html);
    }

    // Load data initially
    $(document).ready(function() {
        loadData();
    });

    function getEndpoint(type, id = null) {
        let base = `{{ url(request()->route('box_slug').'/manage-users/deal-settings') }}/${type}`;
        if (id) base += `/${id}`;
        return base;
    }

    function openAddModal(type) {
        $('#crud_modal_title').text('Add ' + type.charAt(0).toUpperCase() + type.slice(1));
        $('#crud_type').val(type);
        $('#crud_id').val('');
        $('#crud_name').val('');
        $('span.error-text').text('');
        $('#crud_status').val('active');
        $('#crud_modal').modal('show');
    }

    function openEditModal(type, id, name, status) {
        $('#crud_modal_title').text('Edit ' + type.charAt(0).toUpperCase() + type.slice(1));
        $('#crud_type').val(type);
        $('#crud_id').val(id);
        $('#crud_name').val(name);
        $('span.error-text').text('');
        $('#crud_status').val(status);
        $('#crud_modal').modal('show');
    }

    $('#crud_form').on('submit', function(e) {
        e.preventDefault();
        let type = $('#crud_type').val();
        let id = $('#crud_id').val();
        let method = id ? 'PUT' : 'POST';
        let url = getEndpoint(type, id);

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            beforeSend: function() {
                $(document).find('span.error-text').text('');
            },
            success: function(res) {
                if(res.success) {
                    showToast(res.message, 'success');
                    $('#crud_modal').modal('hide');
                    loadData(); // Reload dynamically
                } else {
                    showToast(res.message || 'Error occurred', 'error');
                }
            },
            error: function(xhr) {
                if(xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(prefix, val) {
                        $('span.'+prefix+'_error').text(val[0]);
                    });
                } else {
                    let msg = 'An error occurred while saving.';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    showToast(msg, 'error');
                }
            }
        });
    });

    function deleteItem(type, id) {
        if(confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: getEndpoint(type, id),
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if(res.success) {
                        showToast(res.message, 'success');
                        loadData(); // Reload dynamically
                    }
                }
            });
        }
    }
</script>
@endsection
@endsection
