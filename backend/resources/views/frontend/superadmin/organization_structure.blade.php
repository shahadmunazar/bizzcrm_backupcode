@extends('layouts.main')
@section('content')
@section('title','Organization Structure')
<style>
    .org-chart {
        background: linear-gradient(135deg, #e6f0fa 0%, #f5f7fa 100%);
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        font-family: 'Inter', sans-serif;
        position: relative;
        min-height: 600px;
        overflow-x: auto;
        display: flex;
        justify-content: center;
    }

    .node {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        margin: 20px 0;
        position: relative;
        transition: all 0.3s ease;
        min-width: 150px;
        width: 200px;
    }

    .node:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }

    .node-label {
        font-weight: 600;
        color: #fff;
        padding: 10px 20px;
        border-radius: 20px;
        font-size: 0.9em;
        white-space: nowrap;
        position: relative;
        transition: all 0.3s ease;
    }

    .node-actions {
        display: none;
        position: absolute;
        top: -12px;
        right: -12px;
        background: rgba(0, 0, 0, 0.75);
        border-radius: 50%;
        padding: 6px;
        z-index: 10;
    }

    .node:hover .node-actions {
        display: flex;
        gap: 8px;
    }

    .node-actions a {
        color: #fff;
        font-size: 0.9em;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .node-actions a:hover {
        color: #4a90e2;
    }

    .department .node-label {
        background: linear-gradient(45deg, #2a5298 0%, #4a90e2 100%);
        font-size: 1.1em;
    }

    .head .node-label {
        background: linear-gradient(45deg, #b31217 0%, #e52d27 100%);
        font-size: 1em;
    }

    .manager .node-label {
        background: linear-gradient(45deg, #3c8dbc 0%, #6ab0de 100%);
        font-size: 0.95em;
    }

    .lead .node-label {
        background: linear-gradient(45deg, #f7971e 0%, #ffd200 100%);
        font-size: 0.9em;
    }

    .employee .node-label {
        background: linear-gradient(45deg, #27ae60 0%, #2ecc71 100%);
        font-size: 0.85em;
    }

    .level {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        position: relative;
        margin-top: 20px;
    }

    .node:before {
        content: '';
        position: absolute;
        top: -20px;
        left: 50%;
        width: 2px;
        height: 20px;
        background: #555;
        transform: translateX(-50%);
        z-index: 0;
    }

    .node .left, .node .right {
        position: relative;
        margin: 0 20px;
    }

    .node .left:before, .node .right:before {
        content: '';
        position: absolute;
        top: 0;
        width: 20px;
        height: 2px;
        background: #555;
    }

    .node .left:before {
        left: 50%;
        transform: translateX(-100%);
    }

    .node .right:before {
        right: 50%;
        transform: translateX(100%);
    }

    .level .node:first-child:before {
        display: none;
    }

    .nav-tabs {
        border-bottom: 2px solid #4a90e2;
        margin-bottom: 30px;
    }

    .nav-tabs .nav-link {
        color: #333;
        background: #f5f7fa;
        border: 1px solid #e0e0e0;
        border-radius: 8px 8px 0 0;
        margin-right: 6px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link.active {
        background: linear-gradient(45deg, #4a90e2 0%, #63b8ff 100%);
        color: #fff;
        border-bottom: none;
    }

    .nav-tabs .nav-link:hover {
        background: #d1e0f0;
        color: #333;
    }

    .modal-content {
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        background: #f5f7fa;
        border-bottom: none;
    }

    .modal-footer {
        border-top: none;
        padding: 20px;
    }

    .btn-primary {
        background: linear-gradient(45deg, #4a90e2 0%, #63b8ff 100%);
        border: none;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(45deg, #2a5298 0%, #4a90e2 100%);
    }

    .btn-secondary {
        background: #f5f5f5;
        color: #333;
        border: 1px solid #e0e0e0;
    }

    .team-input-group {
        margin-bottom: 15px;
    }

    .team-input-group .input-group-text {
        background: #f5f7fa;
    }

    @media (max-width: 768px) {
        .org-chart {
            padding: 20px;
            min-height: 400px;
        }

        .node {
            margin: 10px 0;
            width: 100%;
            max-width: 180px;
        }

        .node-label {
            padding: 8px 15px;
            font-size: 0.85em;
        }

        .node-actions {
            top: -8px;
            right: -8px;
            padding: 4px;
        }

        .nav-tabs .nav-link {
            padding: 8px 12px;
            margin-bottom: 6px;
            font-size: 0.9em;
        }
    }
</style>



<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">Organization Structure (BST)</h4>
                        </div>
                        <div class="col-4 text-end">
                            <div class="head-icons">
                                <a href="javascript:void(0);" id="addDepartment" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-original-title="Add Department"><i
                                        class="ti ti-plus"></i></a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Refresh" onclick="loadDepartmentTeams()"><i
                                        class="ti ti-refresh-dot"></i></a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Collapse" id="collapse-header"><i
                                        class="ti ti-chevrons-up"></i></a>
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
                                    <input type="text" class="form-control" placeholder="Search Organization">
                                </div>
                            </div>
                            <div class="col-sm-8"></div>
                        </div>
                        <!-- /Search -->
                    </div>
                    <div class="card-body">
                        <!-- Tab Bar -->
                        <ul class="nav nav-tabs" id="departmentTabs" role="tablist">
                            <!-- Populated dynamically -->
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="departmentTabContent">
                            <!-- Populated dynamically -->
                        </div>
                        <!-- /Organization Structure -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<!-- Create Department Modal -->
<div class="modal fade custom-modal" id="createDepartmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form method="POST" action="{{ route('departments.store', ['box_slug' => request()->route('box_slug')]) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Department</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="modal-body pb-0">
                    <div class="mb-3">
                        <label class="form-label">Department Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter department name" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign to Box</label>
                        <select name="box_id" class="form-select" required>
                            <option value="">Select Box</option>
                            <!-- Populated dynamically via AJAX or server-side -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teams (Optional)</label>
                        <div id="team-inputs">
                            <div class="team-input-group input-group mb-2">
                                <input type="text" name="teams[][name]" class="form-control team-input" placeholder="Enter team name (e.g., Head, Manager, Lead, Employee)">
                                <select name="teams[][type]" class="form-select" style="max-width: 150px;">
                                    <option value="head">Head</option>
                                    <option value="manager">Manager</option>
                                    <option value="lead">Lead</option>
                                    <option value="employee">Employee</option>
                                </select>
                                <select name="teams[][parent_id]" class="form-select" style="max-width: 200px;">
                                    <option value="">No Parent (Head)</option>
                                </select>
                                <button type="button" class="btn btn-secondary remove-team-input"><i class="ti ti-minus"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-link" id="add-team-input">Add Another Team</button>
                    </div>
                    <input type="hidden" name="type" value="department">
                    <input type="hidden" name="id" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Department</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Create Node Modal (for Head, Manager, Lead, Employee) -->
<div class="modal fade custom-modal" id="createNodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('departments.store', ['box_slug' => request()->route('box_slug')]) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Node</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="modal-body pb-0">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter name" required>
                    </div>
                    <input type="hidden" name="parent_id" value="">
                    <input type="hidden" name="team_id" value="">
                    <input type="hidden" name="type" value="">
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="department_id" value="">
                    <input type="hidden" name="box_id" value="{{ request()->route('box_id') }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Node Modal -->
<div class="modal fade custom-modal" id="editNodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('departments.store', ['box_slug' => request()->route('box_slug')]) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Node</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="modal-body pb-0">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter name" required>
                    </div>
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="type" value="">
                    <input type="hidden" name="department_id" value="">
                    <input type="hidden" name="box_id" value="{{ request()->route('box_id') }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        let departmentsData = [];

    function getDepartmentName(nodeLabel) {
        for (let i = 0; i < nodeLabel.childNodes.length; i++) {
            const node = nodeLabel.childNodes[i];
            if (node.nodeType === Node.TEXT_NODE && node.nodeValue.trim() !== '') {
                return node.nodeValue.trim();
            }
        }
        return '';
    }

    function flattenTeams(teams, departmentId, parentId = null, depth = 0) {
        let allTeams = [];
        teams.forEach(team => {
            let type = depth === 0 ? 'head' : (depth === 1 ? 'manager' : depth === 2 ? 'lead' : 'employee');
            allTeams.push({
                id: team.id,
                department_id: departmentId,
                box_id: team.box_id,
                name: team.name,
                user_id: team.user_id,
                created_by: team.created_by,
                updated_by: team.updated_by,
                deleted_by: team.deleted_by,
                _lft: team._lft,
                _rgt: team._rgt,
                parent_id: parentId,
                depth: depth,
                type: type,
                status: team.status,
                created_at: team.created_at,
                updated_at: team.updated_at,
                deleted_at: team.deleted_at,
                children: team.children || []
            });
            if (team.children && team.children.length > 0) {
                allTeams = allTeams.concat(flattenTeams(team.children, departmentId, team.id, depth + 1));
            }
        });
        return allTeams;
    }

    // Function to build tree from flattened teams
    function buildTreeFromFlat(flatTeams, parentId = null) {
        return flatTeams.filter(team => team.parent_id === parentId).map(team => {
            return {
                ...team,
                children: buildTreeFromFlat(flatTeams, team.id)
            };
        });
    }

    // Fetch department teams on page load
    function loadDepartmentTeams() {
        $.ajax({
            url: '{{ route('get.department', ['box_slug' => request()->route('box_slug')]) }}',
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    departmentsData = response.data.map(dept => {
                        return {
                            ...dept,
                            all_teams: flattenTeams(dept.teams || [], dept.id),
                            tree_teams: buildTreeFromFlat(flattenTeams(dept.teams || [], dept.id))
                        };
                    });
                    console.log("departmentsData", departmentsData);

                    const tabContent = $('#departmentTabContent');
                    const tabList = $('#departmentTabs');
                    tabContent.empty();
                    tabList.empty();


                    function renderNode(node) {


    const type = node.type || 'head'; // default if undefined
    let html = `<div class="node ${type}" data-id="${node.id}" data-type="${type}" data-department-id="${node.department_id || ''}" data-parent_id="${node.parent_id || ''}">
        <div class="node-label">${node.name}<span class="node-actions">
            <a href="javascript:void(0);" data-action="edit" data-type="${type}" data-id="${node.id}">
                <i class="ti ti-edit"></i>
            </a>
            <a href="javascript:void(0);" data-action="delete" data-type="${type}" data-id="${node.id}">
                <i class="ti ti-trash"></i>
            </a>
            <a href="javascript:void(0);" data-action="add-${type}" data-type="${type}" data-id="${node.id}" data-name="${node.name}" data-parent_id="${node.parent_id}">
                <i class="ti ti-user-plus"></i>
            </a>
        </span></div>`;

    let sortedChildren = [...(node.children || [])].sort((a, b) => a.id - b.id);
    if (sortedChildren.length > 0) {
        html += `<div class="level">`;
        sortedChildren.forEach(child => {
            html += `<div class="child-node">${renderNode(child)}</div>`;
        });
        html += `</div>`;
    }

    html += `</div>`;
    return html;
}


                    departmentsData.forEach((dept, index) => {
                        const tabId = `dept${dept.id}`;
                        const tab = `<li class="nav-item" role="presentation">
                            <button class="nav-link ${index === 0 ? 'active' : ''}" id="${tabId}-tab" data-bs-toggle="tab" data-bs-target="#${tabId}" type="button" role="tab" aria-controls="${tabId}" aria-selected="${index === 0 ? 'true' : 'false'}">${dept.name}</button>
                        </li>`;
                        tabList.append(tab);

                        let orgChartHtml = `<div class="tab-pane fade ${index === 0 ? 'show active' : ''}" id="${tabId}" role="tabpanel" aria-labelledby="${tabId}-tab">
    <div class="org-chart">
        <div class="level">
            <div class="node department" data-id="${dept.id}" data-box-id="${dept.box_id}">
                ${renderNode({ ...dept, children: dept.tree_teams })}
            </div>
        </div>
    </div>
</div>`;


                        tabContent.append(orgChartHtml);
                    });
                } else {
                    alert('Failed to load departments: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('An error occurred while fetching department teams.');
            }
        });
    }

    loadDepartmentTeams();

        // Add Department (Direct Creation)
        $('#addDepartment').on('click', function(e) {
            e.preventDefault();
            const maxDeptId = departmentsData.reduce((max, dept) => Math.max(max, dept.id), 0);
            const departmentName = `Department ${maxDeptId + 1}`;
            const boxId = '{{ request()->route('box_id') }}';

            $.ajax({
                url: '{{ route('departments.store', ['box_slug' => request()->route('box_slug')]) }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType: 'application/json',
                data: JSON.stringify({
                    action: 'create',
                    department: { id: maxDeptId + 1, name: departmentName, box_id: boxId },
                    teams: []
                }),
                success: function(response) {
                    if (response.success) {
                        loadDepartmentTeams();
                        alert(response.message);
                    } else {
                        alert('Failed to create department: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error creating department:', error);
                    alert('An error occurred while creating the department.');
                }
            });
        });
$(document).on('click', '[data-action^="add-"]', function (e) {
    e.preventDefault();

    const action = $(this).data('action'); // like add-head, add-lead, etc.
    const teamId = $(this).data('id');
    const parentName = $(this).data('name');
    const parentId = $(this).data('parent_id');
console.log("Parent_id",parentId);
    const departmentNode = $(this).parents('.node.department').first();
    if (!departmentNode.length) {
        console.error('Department node not found for clicked element:', this);
    alert('Department node not found. Please check your DOM structure.');
    return;
    }

    const departmentId = departmentNode.data('id');
    const boxId = departmentNode.data('box-id') || '{{ request()->route('box_id') }}';

    // Get type from action → remove "add-"
    const type = action.replace('add-', '');
    const baseName = parentName;

    const payload = {
        action: 'create',
        department: {
            id: departmentId,
            box_id: boxId
        },
        teams: [{
            name: baseName,
            type: type,
            parent_id: parentId,
            department_id: departmentId,
            team_id: teamId
        }]
    };
    console.log('Sending payload:', payload);
    $.ajax({
        url: '{{ route('departments.store', ['box_slug' => request()->route('box_slug')]) }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        contentType: 'application/json',
        data: JSON.stringify(payload),
        success: function (response) {
            if (response.success) {
                loadDepartmentTeams();
                alert(response.message);
            } else {
                alert('Failed to add team: ' + response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error adding team:', error);
            alert('An error occurred while adding the team.');
        }
    });
});

        $(document).on('click', '[data-action="edit"]', function(e) {
            e.preventDefault();
            const nodeId = $(this).data('id');
            const nodeType = $(this).data('type');
            const node = $(this).closest('.node');
            const nodeName = node.find('.node-label').contents().filter(function() {
                return this.nodeType === 3;
            }).text().trim();
            const departmentId = node.data('department-id') || node.closest('.org-chart').find('.department').data('id');

            const dept = departmentsData.find(d => d.id === parseInt(departmentId));
            if (!dept) {
                alert('Department not found.');
                return;
            }
            const targetNode = dept.all_teams.find(t => t.id === parseInt(nodeId) && t.type === nodeType);
            if (!targetNode) {
                alert('Node not found.');
                return;
            }

            const modal = $('#editNodeModal');
            const form = modal.find('form');

            modal.find('.modal-title').text(`Edit ${nodeType.charAt(0).toUpperCase() + nodeType.slice(1)}`);
            form.find('input[name="name"]').val(targetNode.name);
            form.find('input[name="id"]').val(targetNode.id);
            form.find('input[name="type"]').val(targetNode.type);
            form.find('input[name="department_id"]').val(targetNode.department_id);
            modal.modal('show');
        });

        // Handle create/edit department form submission
        $('#createDepartmentModal form, #editNodeModal form').on('submit', function(e) {
            e.preventDefault();
            console.log('Form submission triggered'); // Debug log
            const form = $(this);
            const action = form.find('input[name="id"]').val() ? 'update' : 'create'; // Use 'create' for new departments, 'update' for editing
            const departmentId = form.find('input[name="department_id"]').val() || form.find('input[name="id"]').val() || null;
            const departmentName = form.find('input[name="name"]').val().trim();
            const boxId = form.find('select[name="box_id"]').val() || '{{ request()->route('box_id') }}';
            const teamId = form.find('input[name="id"]').val(); // Team ID for editing

            const dept = departmentsData.find(d => d.id === parseInt(departmentId));
            if (!dept && action === 'update' && !teamId) {
                alert('Department or team not found.');
                return;
            }

            let payload = {
                action: action,
                department: { id: departmentId, name: dept ? dept.name : departmentName, box_id: boxId }
            };

            if (teamId) {
                // Editing a team
                const teams = dept.all_teams.map(team => ({
                    id: team.id,
                    name: team.id === parseInt(teamId) ? departmentName : team.name,
                    parent_id: team.parent_id,
                    type: team.type,
                    department_id: team.department_id
                }));
                payload.teams = teams;
            } else if (!empty(form.find('input[name="teams[][name]"]').length)) {
                // Creating teams for a new department
                const teams = form.find('input[name="teams[][name]"]').map(function() {
                    return {
                        name: $(this).val(),
                        type: $(this).closest('.team-input-group').find('select[name="teams[][type]"]').val(),
                        parent_id: $(this).closest('.team-input-group').find('select[name="teams[][parent_id]"]').val() || null,
                        department_id: departmentId
                    };
                }).get();
                payload.teams = teams;
            }

            if (!departmentName || departmentName.length > 255) {
                alert('Department name is required and must not exceed 255 characters.');
                return;
            }

            $.ajax({
                url: '{{ route('departments.store', ['box_slug' => request()->route('box_slug')]) }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType: 'application/json',
                data: JSON.stringify(payload),
                success: function(response) {
                    if (response.success) {
                        loadDepartmentTeams();
                        form.closest('.modal').modal('hide');
                        alert(response.message);
                    } else {
                        alert('Failed to save: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saving department:', xhr.responseText);
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        alert('Validation error: ' + (errors ? Object.values(errors).join(', ') : 'Unprocessable Content'));
                    } else {
                        alert('An error occurred while saving the department: ' + error);
                    }
                }
            });
        });

        // Delete node with confirmation
        $(document).on('click', '[data-action="delete"]', function (e) {
    e.preventDefault();
    const nodeId = $(this).data('id');
    const nodeType = $(this).data('type');

    if (confirm(`Are you sure you want to delete this ${nodeType}? This will also delete all child teams.`)) {
        const departmentNode = $(this).closest('.org-chart').find('.department');
        const departmentId = departmentNode.data('id');
        const boxId = departmentNode.data('box-id');
        const departmentName = departmentNode.find('.node-label').contents().filter(function () {
            return this.nodeType === 3;
        }).text().trim();

        const payload = {
            action: 'delete'
        };

        if (nodeType === 'department') {
            payload.department = { id: nodeId };
        } else {
            const dept = departmentsData.find(d => d.id === departmentId);
            const deletedTeamIds = getAllChildTeamIds(nodeId, dept.all_teams);
            const teams = dept.all_teams.filter(team => !deletedTeamIds.includes(team.id)).map(team => ({
                id: team.id,
                name: team.name,
                parent_id: team.parent_id,
                type: team.type,
                department_id: departmentId
            }));

            payload.department = {
                id: departmentId,
                name: departmentName,
                box_id: boxId
            };
            payload.teams = teams;
            payload.deleted_team_id = nodeId;
        }

        $.ajax({
            url: '{{ route('departments.store', ['box_slug' => request()->route('box_slug')]) }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: function (response) {
                console.log('Success Response:', response);
                if (response.success) {
                    loadDepartmentTeams();
                    alert(response.message);
                } else {
                    // Server responded but action not successful
                    alert('Failed to delete: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                console.log('Full Error Response:', xhr.responseJSON);
                const errorMsg = xhr.responseJSON?.message || 'An unknown error occurred while deleting.';
                alert('Error: ' + errorMsg);
            }
        });
    }
});


        // Helper to get all child team IDs using Baum's descendants
        function getAllChildTeamIds(teamId, teams) {
            const childIds = [parseInt(teamId)];
            const team = teams.find(t => t.id === parseInt(teamId));
            if (team) {
                const children = teams.filter(t => t._lft > team._lft && t._rgt < team._rgt);
                children.forEach(child => {
                    childIds.push(child.id);
                });
            }
            return childIds;
        }

        // Search functionality
        $('input[placeholder="Search Organization"]').on('input', function() {
            const query = $(this).val().toLowerCase();
            $('.node-label').each(function() {
                const text = $(this).text().toLowerCase();
                $(this).closest('.node').toggle(text.includes(query));
            });
        });
    });

</script>
@endpush
@endsection
