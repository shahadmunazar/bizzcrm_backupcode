@extends('layouts.main')
@section('content')
@section('title', 'Facebook Forms')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-8">
                    <h4 class="page-title">Facebook Forms</h4>
                    <p class="text-muted">Manage all your Facebook Lead Generation Forms</p>
                </div>
                <div class="col-4 text-end">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        @if($isIntegrationEnabled && $facebookConnected)
                        <a href="javascript:void(0);" onclick="checkAllWebhooks()" class="btn btn-info" id="check-webhooks-btn">
                            <i class="fa-solid fa-check-circle"></i> Check All Webhooks
                        </a>
                        <a href="javascript:void(0);" onclick="syncFacebookForms()" class="btn btn-warning" id="sync-forms-btn">
                            <i class="fa-solid fa-sync"></i> Sync Forms
                        </a>
                        @endif
                        <a href="javascript:void(0);" onclick="recalculateLeadCounts()" class="btn btn-outline-info" id="recalculate-btn">
                            <i class="fa-solid fa-calculator"></i> Recalculate Counts
                        </a>
                        <a href="javascript:void(0);" onclick="syncFacebookLeads()" class="btn btn-primary" id="sync-leads-btn">
                            <i class="fa-solid fa-download"></i> Sync All Leads
                        </a>
                        <a href="{{ route('facebook.leads', ['box_slug' => $box->slug]) }}" class="btn btn-outline-info">
                            <i class="fa-solid fa-list"></i> View All Leads
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        @if(!$isIntegrationEnabled || !$facebookConnected)
        <div class="alert alert-soft-danger border-danger d-flex align-items-center mb-4 p-3 shadow-sm">
            <div class="avatar avatar-lg bg-soft-danger rounded-circle me-3">
                <i class="fa-brands fa-facebook fa-lg text-danger"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold text-dark mb-1">Facebook Integration Inactive</h6>
                <p class="text-muted small mb-0">Some features may not work until you enable and connect Facebook in your <a href="{{ box_route('settings.general.api_keys') }}" class="fw-bold text-primary">API Settings</a>.</p>
            </div>
            <a href="{{ box_route('settings.general.api_keys') }}" class="btn btn-danger btn-sm px-3">Enable Now</a>
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg bg-primary-light rounded">
                                    <i class="fa-brands fa-facebook-f fs-4 text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0" id="total-forms">0</h5>
                                <p class="text-muted mb-0">Total Forms</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg bg-success-light rounded">
                                    <i class="fa-solid fa-check-circle fs-4 text-success"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0" id="active-forms">0</h5>
                                <p class="text-muted mb-0">Active Forms</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg bg-info-light rounded">
                                    <i class="fa-solid fa-link fs-4 text-info"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0" id="mapped-forms">0</h5>
                                <p class="text-muted mb-0">Mapped Forms</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg bg-warning-light rounded">
                                    <i class="fa-solid fa-users fs-4 text-warning"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0" id="total-leads">0</h5>
                                <p class="text-muted mb-0">Total Leads</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Statistics Cards -->

        <!-- Forms Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fa-brands fa-facebook-f"></i> All Facebook Forms
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table datatable" id="facebook-forms-table">
                        <thead>
                            <tr>
                                <th>Form Name</th>
                                <th>Form ID</th>
                                <th>Page Name</th>
                                <th>Status</th>
                                <th>Mapped</th>
                                <th>Mapped Fields</th>
                                <th>Leads Count</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /Forms Table -->
    </div>
</div>
<!-- /Page Wrapper -->

<!-- View Form Modal -->
<div class="modal fade" id="viewFormModal" tabindex="-1" aria-labelledby="viewFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-info text-white">
                <h5 class="modal-title shadow-sm" id="viewFormModalLabel">
                    <i class="fa-solid fa-file-lines me-2"></i>Form Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="d-flex flex-column gap-2">
                            <label class="text-muted small text-uppercase fw-bold ls-1">Form Name</label>
                            <h4 id="view-form-name" class="fw-bold text-dark m-0"></h4>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="d-flex flex-column gap-1">
                            <label class="text-muted small text-uppercase fw-bold ls-1">Status</label>
                            <div id="view-form-status-container"></div>
                        </div>
                    </div>
                </div>

                <div class="card bg-light border-0 rounded-3 mb-4">
                    <div class="card-body p-3">
                        <div class="row row-cols-1 row-cols-md-4 g-3">
                            <div class="col border-end-md">
                                <span class="text-muted small d-block mb-1">Facebook ID</span>
                                <span id="view-form-id" class="font-monospace fw-bold text-dark"></span>
                            </div>
                            <div class="col border-end-md">
                                <span class="text-muted small d-block mb-1">Source Page</span>
                                <span id="view-page-name" class="fw-bold text-dark"></span>
                            </div>
                            <div class="col border-end-md">
                                <span class="text-muted small d-block mb-1">Webhook Status</span>
                                <div class="d-flex align-items-center">
                                    <span id="view-webhook-status" class="fw-bold"></span>
                                    <button type="button" id="view-check-webhook-btn" class="btn btn-sm btn-link p-0 ms-2 text-primary" title="Check Real-time Status">
                                        <i class="ti ti-refresh"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col text-md-end">
                                <span class="text-muted small d-block mb-1">Mapping</span>
                                <span id="view-mapped-status" class="fw-bold"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-0">
                    <h6 class="text-muted small text-uppercase fw-bold ls-1 mb-3">Questions & Fields</h6>
                    <div id="view-questions-container" class="vstack gap-2">
                        <!-- Questions will load here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <div class="me-auto text-muted small">
                    <i class="ti ti-calendar-event me-1"></i>Last synced: <span id="view-last-updated"></span>
                </div>
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary px-4" id="view-modal-map-btn">
                    <i class="fa-solid fa-link me-1"></i>Modify Mapping
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Mapping Modal -->
<div class="modal fade" id="mapFormModal" tabindex="-1" aria-labelledby="mapFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapFormModalLabel">Map Facebook Form Fields</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 small mb-3">
                    <i class="ti ti-info-circle me-1"></i> Map the questions from your Facebook form to the corresponding fields in ZipZapGo.
                </div>
                <div class="mb-4">
                    <h6 class="fw-bold">Form: <span id="mapping-form-name" class="text-primary"></span></h6>
                </div>
                <form id="field-mapping-form">
                    <input type="hidden" id="mapping-form-id" name="id">
                    <div id="mapping-container">
                        <!-- Mapping fields will be loaded here -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save-mapping-btn" onclick="saveMapping()">Save Mapping</button>
            </div>
        </div>
    </div>
</div>

<!-- Integration Confirmation Modal -->
<div class="modal fade" id="integrationConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-5">
                <div class="avatar avatar-xxl bg-soft-warning rounded-circle mb-4">
                    <i class="fa-brands fa-facebook fa-3x text-warning"></i>
                </div>
                <h4 class="fw-bold mb-3">Integration Not Enabled</h4>
                <p class="text-muted mb-4">Facebook integration is not fully set up or enabled. Would you like to go to the API settings to enable it now?</p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">No, Later</button>
                    <a href="{{ box_route('settings.general.api_keys') }}" class="btn btn-primary px-4">Yes, Enable Now</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let refreshInterval = null;
let refreshCount = 0;
const maxRefreshes = 30; // 5 minutes at 10s intervals

function startAutoRefresh() {
    if (refreshInterval) clearInterval(refreshInterval);
    
    refreshCount = 0;
    toastr.info('Auto-refreshing table to show live sync progress...', null, { timeOut: 3000 });
    
    refreshInterval = setInterval(function() {
        refreshCount++;
        reloadFormsTable();
        
        if (refreshCount >= maxRefreshes) {
            clearInterval(refreshInterval);
            refreshInterval = null;
            toastr.info('Auto-refresh completed.');
        }
    }, 10000); // 10 seconds
}
</script>
<script>
// Sync Facebook Forms Function
// Sync Facebook Forms Function
function syncFacebookForms(silent = false) {
    const syncUrl = '{{ route('facebook.sync-forms', ['box_slug' => $box->slug]) }}';

    const btn = document.getElementById('sync-forms-btn');
    const originalHtml = btn ? btn.innerHTML : '';

    // Disable button and show loading if not silent
    if (btn && !silent) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Syncing...';
    }

    $.ajax({
        url: syncUrl,
        type: 'POST',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(data) {
            if (data.success) {
                if (!silent) {
                    toastr.success('Forms synced successfully! Pages: ' + (data.data?.pages_count || 0) + ', Forms: ' + (data.data?.forms_count || 0));
                }
                // Reload the forms table
                reloadFormsTable();
                
                // Start auto-refresh to see lead counts updating
                startAutoRefresh();
            } else {
                if (!silent) toastr.error('Failed to sync forms: ' + (data.message || 'Unknown error'));
            }
        },
        error: function(xhr, status, error) {
            console.error('Error syncing forms:', error);
            if (!silent) toastr.error('An error occurred while syncing forms: ' + (xhr.responseJSON?.message || error));
        },
        complete: function() {
            // Re-enable button
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }
    });
}

// Sync All Facebook Leads Function
function syncFacebookLeads() {
    const syncUrl = '{{ route('facebook.sync-leads', ['box_slug' => $box->slug]) }}';

    const btn = document.getElementById('sync-leads-btn');
    const originalHtml = btn ? btn.innerHTML : '';

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Syncing Leads...';
    }

    $.ajax({
        url: syncUrl,
        type: 'POST',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(data) {
            if (data.success) {
                toastr.success(data.message || 'Leads sync has been started in the background.');
                reloadFormsTable();
                
                // Start auto-refresh to see lead counts updating
                startAutoRefresh();
            } else {
                if (data.error_type === 'no_mapping') {
                    toastr.warning(data.message || 'Please map your Facebook form fields first to sync leads.');
                } else if (data.message && (data.message.includes('not connected') || data.message.includes('not found') || data.message.includes('not enabled'))) {
                    const confirmModal = new bootstrap.Modal(document.getElementById('integrationConfirmModal'));
                    confirmModal.show();
                } else {
                    toastr.error('Failed to start sync: ' + (data.message || 'Unknown error'));
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error syncing leads:', error);
            const response = xhr.responseJSON;
            if (response && response.error_type === 'no_mapping') {
                 toastr.warning(response.message || 'Please map your Facebook form fields first to sync leads.');
            } else if (xhr.status === 400 || xhr.status === 404) {
                 const confirmModal = new bootstrap.Modal(document.getElementById('integrationConfirmModal'));
                 confirmModal.show();
            } else {
                toastr.error('An error occurred while syncing leads: ' + (xhr.responseJSON?.message || error));
            }
        },
        complete: function() {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }
    });
}


// Load Facebook Forms in DataTable
function loadFacebookForms() {
    const listUrl = '{{ route('facebook.list', ['box_slug' => $box->slug]) }}';

    if ($.fn.DataTable.isDataTable('#facebook-forms-table')) {
        $('#facebook-forms-table').DataTable().destroy();
    }

    $('#facebook-forms-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: listUrl,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'form_id', name: 'form_id' },
            { data: 'page_name', name: 'page_name' },
            {
                data: 'status',
                name: 'status',
                render: function(data) {
                    var badgeClass = 'badge bg-success';
                    if (data === 'ARCHIVED') badgeClass = 'badge bg-secondary';
                    if (data === 'PAUSED') badgeClass = 'badge bg-warning';
                    return '<span class="' + badgeClass + '">' + data + '</span>';
                }
            },
            {
                data: 'is_mapped',
                name: 'is_mapped',
                render: function(data) {
                    return data === 'Yes'
                        ? '<span class="badge bg-success">Yes</span>'
                        : '<span class="badge bg-secondary">No</span>';
                }
            },
            { data: 'mapped_fields', name: 'mapped_fields', orderable: false, searchable: false },
            { data: 'facebookleads_count', name: 'facebookleads_count' },
            {
                data: 'updated_at',
                name: 'updated_at',
                render: function(data) {
                    return data ? new Date(data).toLocaleString() : '-';
                }
            },
            {
                data: 'id',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    const webhookStatus = row.webhook_enabled === 'Yes'
                        ? '<span class="badge bg-success me-1">Active</span>'
                        : '<span class="badge bg-secondary me-1">Inactive</span>';

                    const webhookButton = row.webhook_enabled === 'Yes'
                        ? '<a href="javascript:void(0);" onclick="checkWebhookStatus(' + row.facebook_page_id + ')" class="btn btn-sm btn-info me-1" title="Check Webhook Status">' +
                          '<i class="fa-solid fa-check-circle"></i></a>'
                        : '<a href="javascript:void(0);" onclick="connectWebhook(' + data + ')" class="btn btn-sm btn-warning me-1" title="Connect Webhook">' +
                          '<i class="fa-solid fa-plug"></i></a>';

                    return '<div class="d-flex align-items-center">' +
                        webhookStatus +
                        '<a href="javascript:void(0);" onclick="viewFormDetails(this, ' + row.id + ')" class="btn btn-sm btn-info me-1" title="View Details">' +
                        '<i class="fa-solid fa-eye"></i></a>' +
                        '<a href="javascript:void(0);" onclick="mapForm(this, ' + data + ')" class="btn btn-sm btn-success me-1" title="Map Field">' +
                        '<i class="fa-solid fa-link"></i></a>' +
                        webhookButton +
                        '</div>';
                }
            }
        ],
        order: [[6, 'desc']],
        pageLength: 10,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        },
        drawCallback: function(settings) {
            const json = settings.json;
            if (json && json.stats) {
                document.getElementById('total-forms').textContent = json.stats.total_forms || 0;
                document.getElementById('active-forms').textContent = json.stats.active_forms || 0;
                document.getElementById('mapped-forms').textContent = json.stats.mapped_forms || 0;
                document.getElementById('total-leads').textContent = json.stats.total_leads || 0;
            }
        }
    });
}

// Keep the function for compatibility but make it a no-op as drawCallback handles it now
function updateStatistics() {
    // Stats are now updated automatically via DataTables drawCallback for better performance
}

function viewFormDetails(btn, id) {
    const url = '{{ route('facebook.show', ['box_slug' => $box->slug, 'id' => ':id']) }}'.replace(':id', id);
    const $btn = $(btn);
    const originalHtml = $btn.html();
    $btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);

    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(data) {
            $btn.html(originalHtml).prop('disabled', false);

            if (data.success && data.template) {
                const form = data.template;
                
                // Set text fields
                $('#view-form-name').text(form.name || 'Untitled Form');
                $('#view-form-id').text(form.form_id);
                $('#view-last-updated').text(form.updated_at ? new Date(form.updated_at).toLocaleString() : 'Never');

                // Status Badge
                let statusHtml = '';
                if (form.status === 'ACTIVE') {
                    statusHtml = '<span class="badge bg-success px-3 py-2 rounded-pill"><i class="fa-solid fa-circle-check me-1"></i>Active</span>';
                } else if (form.status === 'PAUSED') {
                    statusHtml = '<span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="fa-solid fa-pause me-1"></i>Paused</span>';
                } else {
                    statusHtml = '<span class="badge bg-secondary px-3 py-2 rounded-pill">' + form.status + '</span>';
                }
                $('#view-form-status-container').html(statusHtml);

                // Mapped Status
                const mappedStatus = $('#view-mapped-status');
                if (form.is_mapped === 'Yes') {
                    mappedStatus.html('<span class="text-success"><i class="ti ti-link me-1"></i>Mapped</span>');
                } else {
                    mappedStatus.html('<span class="text-danger"><i class="ti ti-link-off me-1"></i>Not Mapped</span>');
                }

                // Webhook Status
                const webhookStatus = $('#view-webhook-status');
                const checkBtn = $('#view-check-webhook-btn');
                if (form.page) {
                    $('#view-page-name').text(form.page.name || form.page_name || 'N/A');
                    if (form.page.webhook_enabled === 'Yes') {
                        webhookStatus.html('<span class="text-success"><i class="ti ti-circle-check me-1"></i>Connected</span>');
                    } else {
                        webhookStatus.html('<span class="text-danger"><i class="ti ti-circle-x me-1"></i>Disconnected</span>');
                    }

                    // Set up Webhook Check button behavior
                    checkBtn.show().off('click').on('click', function() {
                        const originalBtnHtml = checkBtn.html();
                        checkBtn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);
                        
                        const checkUrl = '{{ route('facebook.checkWebhookStatus', ['box_slug' => $box->slug, 'id' => ':id']) }}'.replace(':id', form.page.id);
                        
                        $.get(checkUrl, function(res) {
                            checkBtn.html(originalBtnHtml).prop('disabled', false);
                            if (res.success && res.subscribed) {
                                webhookStatus.html('<span class="text-success"><i class="ti ti-circle-check me-1"></i>Connected</span>');
                                toastr.success('Webhook is connected');
                            } else {
                                webhookStatus.html('<span class="text-danger"><i class="ti ti-circle-x me-1"></i>Disconnected</span>');
                                toastr.warning(res.message || 'Webhook is not connected');
                            }
                        }).fail(function() {
                            checkBtn.html(originalBtnHtml).prop('disabled', false);
                            toastr.error('An error occurred while checking webhook status.');
                        });
                    });
                } else {
                    $('#view-page-name').text(form.page_name || 'N/A');
                    webhookStatus.html('<span class="text-muted">Unknown</span>');
                    checkBtn.hide();
                }

                // Process Questions
                let questionsHtml = '';
                const questions = (form.mapping && form.mapping.questions) ? form.mapping.questions : [];
                const internalMapping = (form.mapping && form.mapping.internal) ? form.mapping.internal : {};

                if (questions.length === 0) {
                    questionsHtml = '<div class="alert alert-warning py-2 small border-warning">No questions data found. Sync might be incomplete.</div>';
                } else {
                    questions.forEach(q => {
                        const questionId = q.name || q.id || q.key || q.label;
                        const mappedTo = internalMapping[questionId] || null;
                        
                        questionsHtml += `
                            <div class="p-3 border rounded-3 bg-white d-flex align-items-center justify-content-between shadow-sm border-light-subtle">
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark mb-0">${q.label}</div>
                                    <div class="small text-muted text-uppercase ls-1" style="font-size: 0.65rem;">
                                        Type: ${q.type || 'Text'} • FB KEY: ${q.key || 'N/A'}
                                    </div>
                                </div>
                                <div class="text-end ms-3">
                                    ${mappedTo 
                                        ? `<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">${mappedTo}</span>`
                                        : `<span class="badge bg-light text-muted border px-2 py-1 italic">Ignored</span>`
                                    }
                                </div>
                            </div>
                        `;
                    });
                }
                $('#view-questions-container').html(questionsHtml);

                // Set up Map button in view modal
                $('#view-modal-map-btn').off('click').on('click', function() {
                    $('#viewFormModal').modal('hide');
                    setTimeout(() => mapForm(null, form.id), 200);
                });

                // Show Modal
                const modal = new bootstrap.Modal(document.getElementById('viewFormModal'));
                modal.show();
            } else {
                alert('Failed to load form details: ' + (data.message || 'Unknown error'));
            }
        },
        error: function(xhr) {
            $btn.html(originalHtml).prop('disabled', false);
            console.error('Error loading form details:', xhr);
            const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred while loading form details.';
            toastr.error(errorMsg);
        }
    });
}

function mapForm(btn, formId) {
    const url = '{{ route('facebook.show', ['box_slug' => $box->slug, 'id' => ':id']) }}'.replace(':id', formId);
    
    // Show loading state
    const $btn = $(btn);
    const originalContent = $btn.html();
    $btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);

    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(data) {
            $btn.html(originalContent).prop('disabled', false);
            
            if (data.success && data.template) {
                const form = data.template;
                const mapping = form.mapping || {};
                const internalMapping = mapping.internal || {};
                const questions = mapping.questions || [];

                $('#mapping-form-id').val(form.id);
                $('#mapping-form-name').text(form.name);

                let mappingHtml = '';
                
                if (questions.length === 0) {
                    mappingHtml = '<div class="alert alert-warning">No questions found for this form. Try syncing again.</div>';
                } else {
                    questions.forEach(q => {
                        const questionId = q.name || q.id || q.key || q.label;
                        const selectedField = internalMapping[questionId] || '';
                        
                        mappingHtml += `
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-5">
                                    <label class="form-label mb-0 fw-semibold text-dark">${q.label}</label>
                                    <div class="small text-muted">${q.type || 'Question'}</div>
                                </div>
                                <div class="col-md-2 text-center">
                                    <i class="ti ti-arrow-narrow-right fs-4"></i>
                                </div>
                                <div class="col-md-5">
                                    <select name="mapping[internal][${questionId}]" class="form-select mapping-select">
                                        <option value="">-- Ignore Field --</option>
                                        @foreach(\App\Helpers\LeadFieldsHelper::getFields() as $field)
                                            <option value="{{ $field['field'] }}" ${selectedField === '{{ $field['field'] }}' ? 'selected' : ''}>{{ $field['name'] }}</option>
                                        @endforeach
                                        <optgroup label="Custom Fields">
                                            @php
                                                $customFields = isset($box) && $box ? \App\Models\BoxCustomField::where('box_id', $box->id)->where('object', 'leads')->get() : [];
                                            @endphp
                                            @foreach($customFields as $cf)
                                                <option value="{{ $cf->field_name }}" ${selectedField === '{{ $cf->field_name }}' ? 'selected' : ''}>{{ $cf->field_label }}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        `;
                    });
                }

                $('#mapping-container').html(mappingHtml);
                const modal = new bootstrap.Modal(document.getElementById('mapFormModal'));
                modal.show();
            } else {
                toastr.error('Failed to load form details: ' + (data.message || 'Unknown error'));
            }
        },
        error: function(xhr) {
            $btn.html(originalContent).prop('disabled', false);
            console.error('Error loading form details:', xhr);
            const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred while loading form details.';
            toastr.error(errorMsg);
        }
    });
}


function saveMapping() {
    const form = document.getElementById('field-mapping-form');
    const formData = new FormData(form);
    const formId = document.getElementById('mapping-form-id').value;
    const saveUrl = '{{ route('facebook.update-form', ['box_slug' => $box->slug]) }}';

    const btn = document.getElementById('save-mapping-btn');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

    // Convert FormData to JSON for mapping[internal]
    const jsonData = {
        id: formId,
        mapping: {
            internal: {}
        }
    };

    formData.forEach((value, key) => {
        if (key.startsWith('mapping[internal][')) {
            const match = key.match(/\[internal\]\[(.*?)\]/);
            if (match && match[1]) {
                const internalKey = match[1];
                if (value) {
                    jsonData.mapping.internal[internalKey] = value;
                }
            }
        }
    });

    $.ajax({
        url: saveUrl,
        type: 'POST',
        data: JSON.stringify(jsonData),
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(data) {
            if (data.success) {
                toastr.success('Mapping saved successfully!');
                bootstrap.Modal.getInstance(document.getElementById('mapFormModal')).hide();
                reloadFormsTable();
            } else {
                toastr.error('Failed to save mapping: ' + (data.message || 'Unknown error'));
            }
        },
        error: function(xhr) {
            console.error('Error saving mapping:', xhr);
            toastr.error('An error occurred while saving mapping.');
        },
        complete: function() {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    });
}

function connectWebhook(formId) {
    const url = '{{ route('facebook.connectWebhook', ['box_slug' => $box->slug, 'id' => ':id']) }}'.replace(':id', formId);

    if (!confirm('This will connect the webhook for this form. Continue?')) {
        return;
    }

    $.ajax({
        url: url,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(data) {
            if (data.success) {
                toastr.success('Webhook connected successfully!');
                reloadFormsTable();
            } else {
                toastr.error('Failed to connect webhook: ' + (data.message || 'Unknown error'));
            }
        },
        error: function(xhr) {
            console.error('Error connecting webhook:', xhr);
            toastr.error('An error occurred while connecting webhook.');
        }
    });
}

function checkWebhookStatus(pageId) {
    const url = '{{ route('facebook.checkWebhookStatus', ['box_slug' => $box->slug, 'id' => ':id']) }}'.replace(':id', pageId);

    $.ajax({
        url: url,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(data) {
            if (data.success) {
                const status = data.subscribed ? 'connected' : 'not connected';
                toastr.info('Webhook is ' + status);
                reloadFormsTable();
            } else {
                toastr.error('Failed to check webhook status: ' + (data.message || 'Unknown error'));
            }
        },
        error: function(xhr) {
            console.error('Error checking webhook status:', xhr);
            toastr.error('An error occurred while checking webhook status.');
        }
    });
}

function checkAllWebhooks() {
    const url = '{{ route('facebook.checkAllWebhooks', ['box_slug' => $box->slug]) }}';

    if (!confirm('This will check webhook status for all pages. Continue?')) {
        return;
    }

    $.ajax({
        url: url,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(data) {
            if (data.success) {
                toastr.success('Webhook Status Check Complete');
                reloadFormsTable();
            } else {
                toastr.error('Failed to check webhooks: ' + (data.message || 'Unknown error'));
            }
        },
        error: function(xhr) {
            console.error('Error checking webhooks:', xhr);
            toastr.error('An error occurred while checking webhooks.');
        }
    });
}

function reloadFormsTable() {
    if ($.fn.DataTable.isDataTable('#facebook-forms-table')) {
        $('#facebook-forms-table').DataTable().ajax.reload(null, false);
    }
}

// Load forms on page load
$(document).ready(function() {
    loadFacebookForms();
    // Auto sync forms from Facebook on page load
    setTimeout(function() {
        syncFacebookForms(true);
    }, 1000);
});

// Recalculate Lead Counts Function
function recalculateLeadCounts() {
    const btn = $('#recalculate-btn');
    const originalHtml = btn.html();
    btn.html('<span class="spinner-border spinner-border-sm" role="status"></span> Recalculating...').prop('disabled', true);

    $.ajax({
        url: "{{ route('facebook.recalculate-counts', ['box_slug' => $box->slug]) }}",
        type: 'POST',
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: function(data) {
            if (data.success) {
                toastr.success(data.message || 'Lead counts recalculated successfully.');
                reloadFormsTable();
            } else {
                toastr.error('Failed to recalculate counts: ' + (data.message || 'Unknown error'));
            }
        },
        error: function() {
            toastr.error('An error occurred while recalculating counts.');
        },
        complete: function() {
            btn.html(originalHtml).prop('disabled', false);
        }
    });
}
</script>
@endpush

