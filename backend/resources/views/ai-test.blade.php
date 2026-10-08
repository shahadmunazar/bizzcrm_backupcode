<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Smart Filters Tester</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 10px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .pre-json { background: #2d2d2d; color: #a6e22e; padding: 15px; border-radius: 5px; font-size: 13px; max-height: 400px; overflow-y: auto; }
        .tab-btn { margin-right: 10px; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">🧠 AI Smart Activity Filter & Ask CRM Tester</h2>
    
    <div class="row">
        <!-- Create Smart View -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">1. Create a Smart View</h5>
                    <p class="text-muted small">Translate natural language into a persistent filter.</p>
                    <div class="mb-3">
                        <label>View Name</label>
                        <input type="text" id="sv-name" class="form-control" placeholder="e.g., Hot Leads">
                    </div>
                    <div class="mb-3">
                        <label>Target Module</label>
                        <select id="sv-module" class="form-select">
                            <option value="activity">Activities</option>
                            <option value="lead">Leads</option>
                            <option value="whatsapp">WhatsApp Messages</option>
                            <option value="project">Projects</option>
                            <option value="deal">Deals</option>
                            <option value="all">All Modules</option>
                        </select>
                    </div>
                    
                    <!-- Manual Filters -->
                    <div class="mb-3">
                        <label>Manual Filters (Optional)</label>
                        <div id="manual-filters-container"></div>
                        <div class="row g-2 mt-2">
                            <div class="col-4">
                                <select id="filter-field" class="form-select form-select-sm">
                                    <!-- Populated dynamically -->
                                </select>
                            </div>
                            <div class="col-3">
                                <select id="filter-operator" class="form-select form-select-sm">
                                    <option value="=">=</option>
                                    <option value="!=">!=</option>
                                    <option value=">">&gt;</option>
                                    <option value="<">&lt;</option>
                                    <option value="LIKE">LIKE</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <input type="text" id="filter-value" class="form-control form-control-sm" placeholder="Value">
                            </div>
                            <div class="col-1">
                                <button class="btn btn-sm btn-secondary w-100" onclick="addManualFilter()">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>AI Instruction</label>
                        <textarea id="sv-instruction" class="form-control" rows="2" placeholder="e.g., Show me high intent customers who want a demo"></textarea>
                    </div>
                    <button class="btn btn-primary" onclick="createSmartView()">Generate Rule & Save</button>
                    <button class="btn btn-info d-none ms-2" id="btn-fetch-records" onclick="fetchSmartViewRecords()">Fetch Matching Records</button>
                    <div class="mt-3">
                        <pre class="pre-json" id="sv-result">Output will appear here...</pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ask CRM -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">2. Ask CRM (Global Search)</h5>
                    <p class="text-muted small">Convert natural language into dynamic database queries.</p>
                    <div class="mb-3">
                        <label>Your Query</label>
                        <textarea id="ask-query" class="form-control" rows="2" placeholder="e.g., Find leads from last week that requested a demo"></textarea>
                    </div>
                    <button class="btn btn-success" onclick="askCrm()">Translate to Query</button>
                    <div class="mt-3">
                        <pre class="pre-json" id="ask-result">Output will appear here...</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Saved Smart Views Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Saved Smart Views for this Box</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mt-3">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Module</th>
                                    <th>Instruction</th>
                                    <th>Filters</th>
                                    <th>AI Rules</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($savedViews ?? [] as $view)
                                    <tr>
                                        <td>{{ $view->id }}</td>
                                        <td>{{ $view->name }}</td>
                                        <td><span class="badge bg-primary">{{ ucfirst($view->module) }}</span></td>
                                        <td><small>{{ $view->natural_language_instruction }}</small></td>
                                        <td><pre class="mb-0" style="font-size: 0.75em">{{ json_encode($view->filters, JSON_PRETTY_PRINT) }}</pre></td>
                                        <td><pre class="mb-0" style="font-size: 0.75em">{{ json_encode($view->ai_rules, JSON_PRETTY_PRINT) }}</pre></td>
                                        <td>
                                            <button class="btn btn-sm btn-info w-100" onclick="testSavedView({{ $view->id }}, '{{ $view->module }}')">Test Fetch</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No saved views found for this tenant box.</td>
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

<!-- Modal to show fetched records -->
<div class="modal fade" id="fetchModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Fetched Records</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <pre class="pre-json" id="fetch-modal-result">Fetching...</pre>
      </div>
    </div>
  </div>
</div>

<script>
    let lastSmartViewId = null;
    let lastSmartViewModule = null;
    let manualFilters = [];

    const defaultFields = {
        'activity': ['id'],
        'lead': ['id'],
        'whatsapp': ['id'],
        'project': ['id'],
        'deal': ['id'],
        'all': ['id']
    };
    
    const passedFields = @json($moduleFields ?? null);
    const moduleFields = passedFields || defaultFields;

    function formatFieldName(field) {
        if (!field) return '';
        // Remove trailing _id
        let name = field.replace(/_id$/, '');
        // Replace underscores with spaces
        name = name.replace(/_/g, ' ');
        // Convert to Title Case
        return name.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function updateFilterFields() {
        const module = document.getElementById('sv-module').value;
        const fieldSelect = document.getElementById('filter-field');
        const fields = moduleFields[module] || ['id'];
        
        const hiddenFields = ['box_id', 'id', 'deleted_at', 'updated_at', 'deleted_by', 'added_by', 'last_updated_by', 'password', 'remember_token', 'bulk_upload_id', 'process_uploading'];
        const filteredFields = fields.filter(f => !hiddenFields.includes(f));
        
        fieldSelect.innerHTML = filteredFields.map(f => `<option value="${f}">${formatFieldName(f)}</option>`).join('');
    }

    document.getElementById('sv-module').addEventListener('change', updateFilterFields);
    // Initialize on load
    updateFilterFields();

    function addManualFilter() {
        const field = document.getElementById('filter-field').value;
        const operator = document.getElementById('filter-operator').value;
        const value = document.getElementById('filter-value').value;

        if (!field || !value) return;

        manualFilters.push({ field, operator, value });
        
        document.getElementById('filter-field').selectedIndex = 0;
        document.getElementById('filter-operator').selectedIndex = 0;
        document.getElementById('filter-value').value = '';
        
        renderManualFilters();
    }

    function renderManualFilters() {
        const container = document.getElementById('manual-filters-container');
        container.innerHTML = manualFilters.map((f, i) => `
            <span class="badge bg-secondary me-1 mb-1">
                ${f.field} ${f.operator} ${f.value}
                <button type="button" class="btn-close btn-close-white" style="font-size: 0.5em" onclick="removeManualFilter(${i})"></button>
            </span>
        `).join('');
    }

    function removeManualFilter(index) {
        manualFilters.splice(index, 1);
        renderManualFilters();
    }

    async function createSmartView() {
        // Auto-add any pending manual filter that the user forgot to click '+' on
        const pendingField = document.getElementById('filter-field').value;
        const pendingValue = document.getElementById('filter-value').value;
        if (pendingField && pendingValue) {
            addManualFilter();
        }

        const btn = document.querySelector('button[onclick="createSmartView()"]');
        const resultBox = document.getElementById('sv-result');
        const fetchBtn = document.getElementById('btn-fetch-records');
        btn.innerHTML = 'Processing...';
        btn.disabled = true;
        fetchBtn.classList.add('d-none');

        try {
            const response = await fetch('/{{ request()->route('box_slug') }}/smart-views', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    name: document.getElementById('sv-name').value || 'Test View',
                    module: document.getElementById('sv-module').value,
                    natural_language_instruction: document.getElementById('sv-instruction').value || 'Show me activities where customer intent is high and sentiment is positive',
                    filters: manualFilters
                })
            });
            const data = await response.json();
            resultBox.innerHTML = JSON.stringify(data, null, 2);
            
            if (data.id) {
                lastSmartViewId = data.id;
                lastSmartViewModule = data.module;
                fetchBtn.classList.remove('d-none');
            }
        } catch (e) {
            resultBox.innerHTML = 'Error: ' + e.message;
        } finally {
            btn.innerHTML = 'Generate Rule & Save';
            btn.disabled = false;
        }
    }

    async function fetchSmartViewRecords() {
        if (!lastSmartViewId || !lastSmartViewModule) return;
        
        const fetchBtn = document.getElementById('btn-fetch-records');
        const resultBox = document.getElementById('sv-result');
        const originalHtml = resultBox.innerHTML;
        
        fetchBtn.innerHTML = 'Fetching...';
        fetchBtn.disabled = true;
        resultBox.innerHTML = 'Fetching matching records from database...';

        try {
            const response = await fetch(`/{{ request()->route('box_slug') }}/smart-views/${lastSmartViewId}/records/${lastSmartViewModule}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            resultBox.innerHTML = JSON.stringify(data, null, 2);
        } catch (e) {
            resultBox.innerHTML = 'Error: ' + e.message;
        } finally {
            fetchBtn.innerHTML = 'Fetch Matching Records';
            fetchBtn.disabled = false;
        }
    }

    async function testSavedView(id, module) {
        const modal = new bootstrap.Modal(document.getElementById('fetchModal'));
        const resultBox = document.getElementById('fetch-modal-result');
        resultBox.innerHTML = 'Fetching matching records from database...';
        modal.show();

        try {
            const response = await fetch(`/{{ request()->route('box_slug') }}/smart-views/${id}/records/${module}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            resultBox.innerHTML = JSON.stringify(data, null, 2);
        } catch (e) {
            resultBox.innerHTML = 'Error: ' + e.message;
        }
    }

    async function askCrm() {
        const btn = document.querySelector('button[onclick="askCrm()"]');
        const resultBox = document.getElementById('ask-result');
        btn.innerHTML = 'Processing...';
        btn.disabled = true;

        try {
            const response = await fetch('/{{ request()->route('box_slug') }}/ask-crm', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    query: document.getElementById('ask-query').value
                })
            });
            const data = await response.json();
            resultBox.innerHTML = JSON.stringify(data, null, 2);
        } catch (e) {
            resultBox.innerHTML = 'Error: ' + e.message;
        } finally {
            btn.innerHTML = 'Translate to Query';
            btn.disabled = false;
        }
    }
</script>
</body>
</html>
