@extends('layouts.main')

@section('content')
@section('title', 'Organization Structure')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">Organization Structure</h4>
                        </div>
                        <div class="col-4 text-end">
                            <div class="head-icons">
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Refresh" onclick="loadOrgChart()">
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
                    <div class="card-header"></div>
                    <div class="card-body">
                        <div id="orgChartContainer">
                            <div id="orgChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<!-- Node Form Modal -->
<div class="modal fade" id="nodeFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nodeFormModalTitle">Add Organization Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="nodeForm">
                    <input type="hidden" id="nodeId" name="node_id">
                    <input type="hidden" id="nodeParent" name="parent_id">
                    <div class="mb-3">
                        <label for="nodeName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="nodeName" name="name"
                            placeholder="Enter organization unit name" required autofocus>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="showIntoLeads" name="show_into_leads">
                            <label class="form-check-label" for="showIntoLeads">
                                Show into Leads
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="showIntoAccounts" name="show_into_accounts">
                            <label class="form-check-label" for="showIntoAccounts">
                                Show into Accounts
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveNodeBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteNodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remove Organization Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deleteNodeId" name="delete_node_id">
                <div id="deleteNodeMessage">
                    <p>Are you sure you want to remove <strong id="deleteNodeName"></strong>?</p>
                    <p class="text-danger mb-0"><small>This action cannot be undone.</small></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Remove</button>
            </div>
        </div>
    </div>
</div>

<!-- Permission Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Permissions: <span id="permissionNodeName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="select_all_global_modal">
                        <label class="form-check-label fw-bold" for="select_all_global_modal">Enable All Permissions</label>
                    </div>
                </div>
                <div id="modalPermissionsContainer" style="max-height: 60vh; overflow-y: auto;">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading permissions template...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary shadow-none" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="savePermissionsBtn">
                    <i class="ti ti-device-floppy me-1"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Personnel Modal -->
<div class="modal fade" id="assignPersonnelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Employees to: <span id="assignNodeName" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignPersonnelForm">
                <div class="modal-body">
                    <input type="hidden" id="assignNodeId" name="node_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Employee</label>
                        <select class="form-select select2" id="assignEmployeeId" name="employee_id" required>
                            <option value="">Search employee...</option>
                        </select>
                        <small class="text-muted">Search by name, email, or phone</small>
                    </div>

                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body p-3">
                            <h6 class="mb-2 fs-14 fw-bold"><i class="ti ti-info-circle me-1"></i> Assignment info</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="small text-muted mb-0">Target Department</label>
                                    <p id="targetDeptName" class="mb-0 fw-semibold text-dark">-</p>
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted mb-0">Target Team</label>
                                    <p id="targetTeamName" class="mb-0 fw-semibold text-dark">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Reporting Manager</label>
                        <select class="form-select select2" id="assignReportingManagerId" name="reporting_manager_id">
                            <option value="">Select Manager</option>
                        </select>
                        <small id="rmSuggestionText" class="text-success small d-block mt-1"></small>
                    </div>

                    <div id="assignedPersonnelSection" class="mt-4" style="display: none;">
                        <h6 class="fw-bold fs-14 mb-2"><i class="ti ti-users text-primary me-1"></i> Already Assigned Users</h6>
                        <div class="table-responsive border rounded">
                            <table class="table table-sm table-nowrap align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-2">Name</th>
                                        <th>Reporting To</th>
                                        <th class="text-end pe-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="assignedPersonnelList"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary shadow-none" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveAssignmentBtn">
                        <i class="ti ti-user-check me-1"></i>Assign Personnel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toastMessage" class="toast align-items-center text-white bg-danger border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastBody"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<style>
    #orgChart {
        width: auto;
        height: auto;
    }

    #orgChartContainer {
        width: 100%;
        height: auto;
        min-height: 500px;
        overflow: auto;
        background: #eeeeee;
        padding: 20px;
    }

    div.orgChart div.node {
        position: relative;
        transition: all 0.2s ease;
    }

    div.orgChart div.node[node-id]:not([node-id="0"]) {
        cursor: move;
    }

    div.orgChart div.node[node-id="0"] {
        cursor: default;
    }

    div.orgChart div.node:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    div.orgChart div.node.ui-draggable-dragging {
        opacity: 0.8;
        z-index: 1000;
        transform: scale(1.05);
    }

    div.orgChart div.node.ui-droppable-hover,
    div.orgChart div.node.drop-zone-highlight {
        border: 2px dashed #007bff !important;
        background-color: #e7f3ff !important;
    }

    div.orgChart div.node.dragging {
        opacity: 0.7;
        z-index: 1000;
    }

    div.orgChart div.node {
        position: relative;
        transition: all 0.2s ease;
        width: auto;
        min-width: 90px;
    }

    div.orgChart div.node h2 {
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 2px 4px;
        border-radius: 3px;
        white-space: nowrap;
        font-size: 12px;
    }

    div.orgChart div.node h2:hover {
        background: #f0f0f0;
        color: #007bff;
    }

    .org-del-button {
        background: #dc3545 !important;
        border-radius: 50% !important;
        width: 18px !important;
        height: 18px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
        border: none !important;
        padding: 0 !important;
        bottom: 5px !important;
        right: 5px !important;
        opacity: 0.9;
        position: absolute !important;
        z-index: 10;
    }

    .org-del-button:hover {
        background: #c82333 !important;
        opacity: 1 !important;
        transform: scale(1.1);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3) !important;
    }

    .org-del-button i {
        color: #fff;
        font-size: 12px;
    }

    .org-add-button {
        background: #28a745 !important;
        border-radius: 50% !important;
        width: 18px !important;
        height: 18px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
        border: none !important;
        padding: 0 !important;
        bottom: 5px !important;
        left: 5px !important;
        opacity: 0.9;
        position: absolute !important;
        z-index: 10;
        font-size: 0 !important;
    }

    .org-add-button:hover {
        background: #218838 !important;
        opacity: 1 !important;
        transform: scale(1.1);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3) !important;
    }

    .org-add-button::before {
        content: "+";
        color: #fff;
        font-weight: bold;
        line-height: 1;
    }

    .org-permission-button {
        background: #007bff !important;
        border-radius: 50% !important;
        width: 18px !important;
        height: 18px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
        border: none !important;
        padding: 0 !important;
        bottom: 5px !important;
        left: 28px !important;
        opacity: 0.9;
        position: absolute !important;
        z-index: 10;
    }

    .org-permission-button:hover {
        background: #0056b3 !important;
        opacity: 1 !important;
        transform: scale(1.1);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3) !important;
    }

    .org-assign-button {
        background: #ffc107 !important;
        border-radius: 50% !important;
        width: 18px !important;
        height: 18px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
        border: none !important;
        padding: 0 !important;
        bottom: 5px !important;
        left: 51px !important;
        opacity: 0.9;
        position: absolute !important;
        z-index: 10;
    }

    .org-assign-button:hover {
        background: #e0a800 !important;
        opacity: 1 !important;
        transform: scale(1.1);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3) !important;
    }

    .org-assign-button i {
        color: #000;
        font-size: 11px;
    }

    div.orgChart div.node:hover .org-del-button,
    div.orgChart div.node:hover .org-add-button,
    div.orgChart div.node:hover .org-permission-button,
    div.orgChart div.node:hover .org-assign-button {
        opacity: 1;
    }


    /* User node specific styling */
    .user-node {
        background-color: #fff !important;
        border: 2px solid #007bff !important;
        min-width: 180px !important;
        padding: 10px !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
    }
    .user-node h2 {
        color: #007bff !important;
        font-size: 14px !important;
        margin-bottom: 2px !important;
    }
    .user-title {
        font-weight: 600;
        font-size: 11px;
        color: #333;
        margin-bottom: 2px;
    }
    .user-email {
        font-size: 10px;
        color: #666;
    }
    .user-node .org-add-button,
    .user-node .org-del-button,
    .user-node .org-assign-button,
    .user-node .org-permission-button {
        display: none !important;
    }
</style>

@push('scripts')
<script>
    (function() {
        'use strict';

        const TEAM_ID_OFFSET = 1000000;
        const DRAG_DEBOUNCE_MS = 300;
        const BUTTON_SETUP_DELAY = 400;
        const DRAG_DROP_DELAY = 500;
        const MODAL_FOCUS_DELAY = 300;

        let org_chart = null;
        let dragTimeout = null;
        let currentNodeData = null;
        let nodeFormModal = null;
        let deleteNodeModal = null;
        let currentChartData = [];

        function loadOrgChart() {
            $('#orgChart').html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');

            $.ajax({
                url: "{{ route('organization.get-structure', ['box_slug' => request()->route('box_slug')]) }}",
                method: 'GET',
                success: function(response) {
                    if (org_chart) {
                        $('#orgChart').empty();
                    }

                    currentChartData = response.data || [];

                    if (currentChartData.length === 0) {
                        $('#orgChart').html(
                            '<div class="text-center p-4 text-muted">No organization structure found. Click the box to add the first department.</div>'
                        );
                        return;
                    }

                    try {
                        org_chart = $("#orgChart").orgChart({
                            data: currentChartData,
                            showControls: true,
                            allowEdit: true,
                            onAddNode: function(node) {
                                currentNodeData = {
                                    id: null,
                                    parent: node.data.id,
                                    name: '',
                                    isEdit: false
                                };
                                openNodeForm();
                            },
                            onClickNode: function(node) {
                                // Handled by h2 click handler
                            },
                            onDeleteNode: function(node) {
                                openDeleteModal(node);
                            }
                        });

                        setTimeout(function() {
                            initializeDragAndDrop(currentChartData);
                        }, DRAG_DROP_DELAY);

                        setTimeout(function() {
                            setupNodeButtons(currentChartData);
                        }, BUTTON_SETUP_DELAY);
                    } catch (error) {
                        $('#orgChart').html(
                            '<div class="text-center p-4 text-danger">Error initializing organization chart. Please refresh the page.</div>'
                        );
                        showError('Failed to initialize organization chart');
                    }
                },
                error: function(xhr) {
                    $('#orgChart').html(
                        '<div class="text-center p-4 text-danger">Error loading organization structure. Please refresh the page.</div>'
                    );
                    showError('Failed to load organization structure');
                }
            });
        }

        function showToast(message, isSuccess = true) {
            const $toast = $('#toastMessage').removeClass('bg-success bg-danger')
                .addClass(isSuccess ? 'bg-success' : 'bg-danger');
            $('#toastBody').text(message);
            new bootstrap.Toast($toast[0]).show();
        }

        function showSuccess(message) {
            showToast(message, true);
        }

        function showError(message) {
            showToast(message, false);
        }

        function openNodeForm() {
            if (!nodeFormModal) {
                nodeFormModal = new bootstrap.Modal(document.getElementById('nodeFormModal'));
            }

            const isEdit = currentNodeData.isEdit;
            $('#nodeFormModalTitle').text(isEdit ? 'Edit Organization Unit' : 'Add Organization Unit');
            $('#nodeId').val(currentNodeData.id || '');

            const parentValue = currentNodeData.parent !== null && currentNodeData.parent !== undefined ?
                parseInt(currentNodeData.parent) : 0;
            $('#nodeParent').val(parentValue);

            $('#nodeName').val(isEdit ? currentNodeData.name : '');
            
            if (isEdit) {
                $('#showIntoLeads').prop('checked', currentNodeData.show_into_leads == 1);
                $('#showIntoAccounts').prop('checked', currentNodeData.show_into_accounts == 1);
            } else {
                $('#showIntoLeads').prop('checked', false);
                $('#showIntoAccounts').prop('checked', false);
            }

            $('#saveNodeBtn').text(isEdit ? 'Update' : 'Save');
            nodeFormModal.show();

            setTimeout(function() {
                $('#nodeName').focus();
            }, MODAL_FOCUS_DELAY);
        }

        function openDeleteModal(node) {
            if (!deleteNodeModal) {
                deleteNodeModal = new bootstrap.Modal(document.getElementById('deleteNodeModal'));
            }

            const nodeId = node.data.id;
            $('#deleteNodeId').val(nodeId);
            $('#deleteNodeName').text(node.data.name);

            checkNodeHasChildren(nodeId, function(hasChildren, childrenCount) {
                if (hasChildren) {
                    $('#deleteNodeMessage').html(
                        '<p class="text-danger"><strong>Cannot remove this organization unit!</strong></p>' +
                        '<p>This unit has <strong>' + childrenCount +
                        '</strong> child unit(s). Please remove all child units first before removing this unit.</p>'
                    );
                    $('#confirmDeleteBtn').prop('disabled', true).addClass('disabled');
                } else {
                    $('#deleteNodeMessage').html(
                        '<p>Are you sure you want to remove <strong>' + node.data.name +
                        '</strong>?</p>' +
                        '<p class="text-danger mb-0"><small>This action cannot be undone.</small></p>'
                    );
                    $('#confirmDeleteBtn').prop('disabled', false).removeClass('disabled');
                }
                deleteNodeModal.show();
            });
        }

        function checkNodeHasChildren(nodeId, callback) {
            let childrenCount = 0;
            for (let i = 0; i < currentChartData.length; i++) {
                if (currentChartData[i].parent == nodeId) {
                    childrenCount++;
                }
            }
            callback(childrenCount > 0, childrenCount);
        }

        function findNodeInData(data, nodeId) {
            for (let i = 0; i < data.length; i++) {
                if (data[i].id == nodeId) {
                    return data[i];
                }
            }
            return null;
        }

        function initializeDragAndDrop(data) {
            setTimeout(function() {
                if (typeof $.fn.draggable === 'undefined') {
                    showError('Drag and drop requires jQuery UI. Please refresh the page.');
                    return;
                }

                $('#orgChart .node').each(function() {
                    const $node = $(this);
                    const nodeIdStr = $node.attr('node-id');
                    const nodeId = nodeIdStr ? parseInt(nodeIdStr) : null;

                    if (nodeId !== null && nodeId > 0) {
                        if ($node.hasClass('ui-draggable')) {
                            $node.draggable('destroy');
                        }

                        try {
                            $node.draggable({
                                cursor: 'move',
                                cursorAt: { top: 20, left: 50 },
                                helper: 'clone',
                                opacity: 0.7,
                                revert: 'invalid',
                                zIndex: 1000,
                                disabled: false,
                                start: function() {
                                    $(this).addClass('dragging');
                                },
                                stop: function() {
                                    $(this).removeClass('dragging');
                                }
                            });
                        } catch (error) {
                            // Silently fail for individual nodes
                        }
                    }
                });

                $('#orgChart .node').each(function() {
                    const $dropNode = $(this);
                    const dropNodeId = $dropNode.attr('node-id');
                    const dropId = parseInt(dropNodeId);

                    if ($dropNode.hasClass('ui-droppable')) {
                        $dropNode.droppable('destroy');
                    }

                    $dropNode.droppable({
                        accept: function(draggable) {
                            const dragNodeId = parseInt(draggable.attr('node-id'));
                            return dragNodeId !== dropId && dropId !== 0;
                        },
                        hoverClass: 'ui-droppable-hover',
                        tolerance: 'pointer',
                        over: function() {
                            $(this).addClass('drop-zone-highlight');
                        },
                        out: function() {
                            $(this).removeClass('drop-zone-highlight');
                        },
                        drop: function(event, ui) {
                            const dragNodeId = parseInt(ui.draggable.attr('node-id'));
                            const newParentId = parseInt(dropNodeId);

                            if (dragTimeout) {
                                clearTimeout(dragTimeout);
                            }

                            const nodeElement = ui.draggable;
                            nodeElement.css('opacity', '0.7');

                            dragTimeout = setTimeout(function() {
                                $.ajax({
                                    url: "{{ route('organization.update-structure', ['box_slug' => request()->route('box_slug')]) }}",
                                    method: 'POST',
                                    data: {
                                        id: dragNodeId,
                                        parent: newParentId,
                                        _token: "{{ csrf_token() }}"
                                    },
                                    success: function(res) {
                                        if (res.success) {
                                            loadOrgChart();
                                            showSuccess('Organization structure updated successfully');
                                        } else {
                                            loadOrgChart();
                                            showError(res.error || 'Failed to reorganize structure');
                                        }
                                    },
                                    error: function(xhr) {
                                        loadOrgChart();
                                        const errorMsg = xhr.responseJSON?.error ||
                                            xhr.responseJSON?.message ||
                                            'Failed to reorganize structure';
                                        showError('Error: ' + errorMsg);
                                    },
                                    complete: function() {
                                        nodeElement.css('opacity', '1');
                                    }
                                });
                            }, DRAG_DEBOUNCE_MS);
                        }
                    });
                });
            }, DRAG_DROP_DELAY);
        }

        function setupNodeButtons(data) {
            $('#orgChart .node h2').off('click').on('click', function(e) {
                e.stopPropagation();
                const $node = $(this).closest('.node');
                const nodeId = parseInt($node.attr('node-id'));

                if (nodeId === 0) {
                    showError('Owner/Box node cannot be edited');
                    return;
                }

                const nodeName = $(this).text();
                const nodeData = findNodeInData(data, nodeId);

                currentNodeData = {
                    id: nodeId,
                    parent: nodeData ? nodeData.parent : 0,
                    name: nodeName,
                    show_into_leads: nodeData ? nodeData.show_into_leads : 0,
                    show_into_accounts: nodeData ? nodeData.show_into_accounts : 0,
                    isEdit: true
                };
                openNodeForm();
            });

            $('.org-del-button').each(function() {
                const $node = $(this).closest('.node');
                const nodeId = parseInt($node.attr('node-id'));
                
                // Hide controls for user nodes
                if (nodeId >= 2000000) {
                    $(this).hide();
                    $node.find('.org-add-button').hide();
                    $node.find('.org-permission-button').hide();
                    $node.find('.org-assign-button').hide();
                    return;
                }

                $(this).css('background-image', 'none');
                if ($(this).find('i').length === 0 && !$(this).attr('data-icon-added')) {
                    $(this).html('<i class="ti ti-trash" style="color: #fff; font-size: 12px;"></i>');
                    $(this).attr('data-icon-added', 'true');
                }
            });

            $('.org-add-button').each(function() {
                $(this).css('background-image', 'none');
                if ($(this).find('i').length === 0 && !$(this).attr('data-icon-added')) {
                    $(this).html('<i class="ti ti-plus" style="color: #fff; font-size: 14px;"></i>');
                    $(this).attr('data-icon-added', 'true');
                }
            });

            // Add additional action buttons individually
            $('#orgChart .node').each(function() {
                const $node = $(this);
                const nodeId = parseInt($node.attr('node-id'));
                if (nodeId !== 0) {
                    if ($node.find('.org-permission-button').length === 0) {
                        $node.append('<div class="org-permission-button" title="Manage Permissions"><i class="ti ti-shield-lock" style="color: #fff; font-size: 12px;"></i></div>');
                    }
                    if ($node.find('.org-assign-button').length === 0) {
                        $node.append('<div class="org-assign-button" title="Assign Employees"><i class="ti ti-user-plus" style="color: #000; font-size: 11px;"></i></div>');
                    }
                }
            });

            $('.org-assign-button').off('click').on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                const $node = $(this).closest('.node');
                const nodeId = parseInt($node.attr('node-id'));
                const nodeName = $node.find('h2').text();
                openAssignPersonnelModal(nodeId, nodeName, data);
            });

            $('.org-permission-button').off('click').on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                const $node = $(this).closest('.node');
                const nodeId = parseInt($node.attr('node-id'));
                const nodeName = $node.find('h2').text();
                openPermissionModal({ data: { id: nodeId, name: nodeName } });
            });

            $('.org-del-button').off('click').on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                const $node = $(this).closest('.node');
                const nodeId = parseInt($node.attr('node-id'));
                if (nodeId === 0) return;

                const nodeName = $node.find('h2').text() || 'Unknown';
                const nodeData = findNodeInData(data, nodeId);
                openDeleteModal({ data: { id: nodeId, name: nodeName, parent: nodeData ? nodeData.parent : 0 } });
            });

            $('#orgChart .node[node-id="0"] .org-del-button').hide();
        }

        function openPermissionModal(node) {
            const nodeId = parseInt(node.data.id);
            const nodeName = node.data.name;
            
            $('#permissionNodeName').text(nodeName);
            $('#modalPermissionsContainer').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Loading permissions for ${nodeName}...</p>
                </div>
            `);
            
            const permissionModal = new bootstrap.Modal(document.getElementById('permissionModal'));
            permissionModal.show();

            const TEAM_ID_OFFSET = 1000000;
            let reqDeptId = '';
            let reqTeamId = '';

            if (nodeId >= TEAM_ID_OFFSET) {
                reqTeamId = nodeId - TEAM_ID_OFFSET;
            } else {
                reqDeptId = nodeId;
            }

            $.ajax({
                url: "{{ route('manage_users.permissions', ['box_slug' => request()->route('box_slug')]) }}",
                method: "GET",
                data: {
                    department_id: reqDeptId,
                    team_id: reqTeamId,
                    ajax_load: true
                },
                success: function(response) {
                    if (response.html) {
                        $('#modalPermissionsContainer').html(response.html);
                        // Hide internal save button as we use modal footer
                        $('#modalPermissionsContainer #save_btn_container').hide();
                        
                        // Initialize Listeners
                        initializeModalCheckboxListeners();
                    } else {
                        showError('Failed to load permissions template');
                    }
                },
                error: function() {
                    showError('Error connecting to permissions service');
                }
            });
        }

        function initializeModalCheckboxListeners() {
            // Module level propagation
            $(document).off('change', '#modalPermissionsContainer .module-checkbox').on('change', '#modalPermissionsContainer .module-checkbox', function() {
                const module = $(this).data('module');
                const isChecked = $(this).is(':checked');
                $(`#modalPermissionsContainer .module-${module}`).prop('checked', isChecked);
                $(`#modalPermissionsContainer input[data-module="${module}"].submodule-checkbox`).prop('checked', isChecked);
            });

            // Submodule level propagation
            $(document).off('change', '#modalPermissionsContainer .submodule-checkbox').on('change', '#modalPermissionsContainer .submodule-checkbox', function() {
                const module = $(this).data('module');
                const submodule = $(this).data('submodule');
                const isChecked = $(this).is(':checked');
                $(`#modalPermissionsContainer .submodule-${module}-${submodule}`).prop('checked', isChecked);
                checkModalModuleState(module);
            });

            // Global Select All
            $('#select_all_global_modal').off('change').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('#modalPermissionsContainer .permission-item-checkbox, #modalPermissionsContainer .module-checkbox, #modalPermissionsContainer .submodule-checkbox').prop('checked', isChecked);
            });

            // Individual Checkbox changes
            $(document).off('change', '#modalPermissionsContainer .permission-item-checkbox').on('change', '#modalPermissionsContainer .permission-item-checkbox', function() {
                const module = $(this).data('module');
                const submodule = $(this).data('submodule');
                checkModalSubmoduleState(module, submodule);
                checkModalModuleState(module);
            });
            
            // Initial Sync
            $('#modalPermissionsContainer .module-checkbox').each(function() {
                checkModalModuleState($(this).data('module'));
            });
            $('#modalPermissionsContainer .submodule-checkbox').each(function() {
                checkModalSubmoduleState($(this).data('module'), $(this).data('submodule'));
            });
        }

        function checkModalSubmoduleState(module, submodule) {
            const total = $(`#modalPermissionsContainer .submodule-${module}-${submodule}`).length;
            const checked = $(`#modalPermissionsContainer .submodule-${module}-${submodule}:checked`).length;
            $(`#modalPermissionsContainer #sub_${module}_${submodule}`).prop('checked', total > 0 && total === checked);
        }

        function checkModalModuleState(module) {
            const total = $(`#modalPermissionsContainer .module-${module}`).length;
            const checked = $(`#modalPermissionsContainer .module-${module}:checked`).length;
            $(`#modalPermissionsContainer #mod_${module}`).prop('checked', total > 0 && total === checked);
        }

        $('#savePermissionsBtn').on('click', function() {
            const $btn = $(this);
            const form = $('#modalPermissionsContainer #permissions_form');
            
            if (form.length === 0) {
                showError('Permission form not found');
                return;
            }

            const originalHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>Saving...');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(res) {
                    if (res.status || res.success) {
                        showSuccess(res.message || 'Permissions updated successfully');
                        bootstrap.Modal.getInstance(document.getElementById('permissionModal')).hide();
                    } else {
                        showError(res.message || 'Failed to update permissions');
                    }
                },
                error: function(xhr) {
                    showError('Error saving permissions');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        });

        $('#saveNodeBtn').on('click', function() {
            const name = $('#nodeName').val().trim();
            if (!name) {
                showError('Please enter a name');
                return;
            }

            const nodeId = $('#nodeId').val();
            let parentId = $('#nodeParent').val();
            parentId = parentId === '' || parentId === null || parentId === undefined || isNaN(parentId) ?
                0 : parseInt(parentId);

            const isEdit = currentNodeData.isEdit;

            nodeFormModal.hide();
            $('#orgChart').html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin"></i> ' +
                (isEdit ? 'Updating' : 'Creating') + ' organization unit...</div>');

            const ajaxData = {
                _token: "{{ csrf_token() }}",
                show_into_leads: $('#showIntoLeads').is(':checked') ? 1 : 0,
                show_into_accounts: $('#showIntoAccounts').is(':checked') ? 1 : 0
            };

            if (isEdit) {
                ajaxData.id = nodeId;
                ajaxData.name = name;

                $.ajax({
                    url: "{{ route('organization.update-node-name', ['box_slug' => request()->route('box_slug')]) }}",
                    method: 'POST',
                    data: ajaxData,
                    success: function(res) {
                        if (res.success) {
                            loadOrgChart();
                            showSuccess('Organization unit updated successfully');
                        } else {
                            showError('Failed to update organization unit');
                            loadOrgChart();
                        }
                    },
                    error: function(xhr) {
                        showError('Error: ' + (xhr.responseJSON?.error || 'Failed to update organization unit'));
                        loadOrgChart();
                    }
                });
            } else {
                ajaxData.parent = parseInt(parentId);
                ajaxData.name = name;

                $.ajax({
                    url: "{{ route('organization.create-node', ['box_slug' => request()->route('box_slug')]) }}",
                    method: 'POST',
                    data: ajaxData,
                    success: function(res) {
                        if (res.id) {
                            loadOrgChart();
                            showSuccess('Organization unit created successfully');
                        } else {
                            showError('Failed to create organization unit');
                            loadOrgChart();
                        }
                    },
                    error: function(xhr) {
                        showError('Error: ' + (xhr.responseJSON?.error || 'Failed to create organization unit'));
                        loadOrgChart();
                    }
                });
            }
        });

        $('#confirmDeleteBtn').on('click', function() {
            if ($(this).prop('disabled')) {
                return;
            }

            const nodeId = $('#deleteNodeId').val();
            if (deleteNodeModal) {
                deleteNodeModal.hide();
            }

            $('#orgChart').html(
                '<div class="text-center p-4"><i class="fa fa-spinner fa-spin"></i> Removing organization unit...</div>'
            );

            $.ajax({
                url: "{{ route('organization.delete-node', ['box_slug' => request()->route('box_slug')]) }}",
                method: 'POST',
                data: {
                    id: nodeId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.success) {
                        loadOrgChart();
                        showSuccess('Organization unit removed successfully');
                    } else {
                        showError(res.error || res.message || 'Failed to remove organization unit');
                        loadOrgChart();
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.error || xhr.responseJSON?.message ||
                        'Failed to remove organization unit';
                    showError('Error: ' + errorMsg);
                    loadOrgChart();
                }
            });
        });

        $('#nodeFormModal').on('hidden.bs.modal', function() {
            $('#nodeForm')[0].reset();
            currentNodeData = null;
        });

        $('#deleteNodeModal').on('hidden.bs.modal', function() {
            $('#deleteNodeId').val('');
            $('#deleteNodeName').text('');
            $('#deleteNodeMessage').html('');
            $('#confirmDeleteBtn').prop('disabled', false).removeClass('disabled');
        });

        $('#nodeName').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#saveNodeBtn').click();
            }
        });

        function waitForOrgChart(callback, retries = 20) {
            if (typeof $.fn.orgChart !== 'undefined') {
                callback();
            } else if (retries > 0) {
                setTimeout(function() {
                    waitForOrgChart(callback, retries - 1);
                }, 100);
            } else {
                $('#orgChart').html(
                    '<div class="text-center p-4 text-danger">Organization chart library not loaded. Please refresh the page.</div>'
                );
                showError('Organization chart library not available. Please refresh the page.');
            }
        }

        $(function() {
            if (typeof $.fn.draggable === 'undefined') {
                $('<link>').attr({
                    rel: 'stylesheet',
                    href: 'https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css'
                }).appendTo('head');

                $.getScript('https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', function() {
                    waitForOrgChart(function() {
                        loadOrgChart();
                    });
                }).fail(function() {
                    showError('Failed to load required libraries. Please refresh the page.');
                });
            } else {
                waitForOrgChart(function() {
                    loadOrgChart();
                });
            }
        });
        // Employee Assignment Feature
        let boxEmployees = [];

        function fetchBoxEmployees(callback) {
            if (boxEmployees.length > 0) {
                callback(boxEmployees);
                return;
            }
            
            $.ajax({
                url: '{{ route('employee.get.box.users', ['box_slug' => request()->route('box_slug')]) }}',
                type: 'GET',
                success: function(response) {
                    if (response.status && Array.isArray(response.data)) {
                        boxEmployees = response.data;
                        callback(boxEmployees);
                    } else {
                        showError('Failed to load employees list.');
                        callback([]);
                    }
                },
                error: function() {
                    showError('Error connecting to the server while fetching employees.');
                    callback([]);
                }
            });
        }

            // Custom template for Reporting Manager with hierarchy badges and email/phone
            function reportingManagerTemplateResult(data) {
                if (!data.id) return data.text;
                const $element = $(data.element);
                const name = $element.data('name') || data.text;
                const email = $element.data('email') || '';
                const phone = $element.data('phone') || '';
                const designation = $element.data('designation-name') || '';
                const department = $element.data('department-name') || '';
                const highlightType = $element.data('highlight-type') || '';
                
                let icon = '<i class="ti ti-user me-1 text-muted"></i>';
                let badgeClass = 'badge-soft-secondary';
                
                if (highlightType === 'immediate') {
                    icon = '<i class="ti ti-star-filled me-1 text-success"></i>';
                    badgeClass = 'bg-success text-white';
                } else if (highlightType === 'admin') {
                    icon = '<i class="ti ti-crown me-1 text-warning"></i>';
                    badgeClass = 'bg-primary text-white';
                } else if (highlightType === 'higher') {
                    icon = '<i class="ti ti-arrow-up-circle me-1 text-info"></i>';
                    badgeClass = 'bg-info text-white';
                }

                return $(`
                    <div class="d-flex align-items-center py-1 ${highlightType === 'immediate' ? 'border-start border-3 border-success ps-2' : ''}">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center">
                                ${icon}
                                <span class="fw-bold text-dark">${name}</span>
                            </div>
                            <div class="small text-muted mb-1" style="font-size: 10px;">
                                <i class="ti ti-mail me-1"></i>${email} | <i class="ti ti-phone me-1"></i>${phone}
                            </div>
                            <div class="d-flex gap-1 flex-wrap">
                                ${designation ? `<span class="badge ${badgeClass} fs-9">${designation}</span>` : ''}
                                ${department ? `<span class="badge badge-soft-dark fs-9">${department}</span>` : ''}
                            </div>
                        </div>
                    </div>
                `);
            }
            
            // Custom template for the selected Reporting Manager
            function reportingManagerTemplateSelection(data) {
                if (!data.id) return data.text;
                const $element = $(data.element);
                const name = $element.data('name') || data.text;
                const designation = $element.data('designation-name') || '';
                const department = $element.data('department-name') || '';
                
                let details = '';
                if (designation || department) {
                    details = ` (${designation}${designation && department ? ' - ' : ''}${department})`;
                }
                
                return $('<span>' + name + '<small class="text-muted">' + details + '</small></span>');
            }

            // Render helper for Reporting Manager options in Org Chart
            function renderReportingManagerOptions($select, employees, selectedId, filterDesignation, selectedDepth) {
                $select.empty().append('<option value="">Select Reporting Manager</option>');
                
                if (employees.length > 0) {
                    employees.forEach(emp => {
                        let reason = "";
                        let highlightType = "";
                        const isHierarchy = filterDesignation && selectedDepth !== null;

                        if (isHierarchy) {
                            const isAdmin = emp.staff_role && emp.staff_role.toLowerCase() === 'admin';
                            const empDepth = parseInt(emp.designation_depth || 0);

                            if (isAdmin) {
                                reason = " (Administrator)";
                                highlightType = "admin";
                            } else if (selectedDepth !== null && empDepth === (selectedDepth - 1)) {
                                reason = " (Immediate Superior)";
                                highlightType = "immediate";
                            } else {
                                reason = " (Higher Hierarchy)";
                                highlightType = "higher";
                            }
                        }

                        const option = $('<option></option>')
                            .attr('value', emp.id)
                            .text(emp.name + reason)
                            .attr('data-name', emp.name)
                            .attr('data-email', emp.email || '')
                            .attr('data-phone', emp.phone || '')
                            .attr('data-designation-name', emp.designation_name)
                            .attr('data-department-name', emp.department_name)
                            .attr('data-highlight-type', highlightType);

                        if (String(emp.id) === String(selectedId)) {
                            option.prop('selected', true);
                        }
                        $select.append(option);
                    });
                } else {
                    $select.append('<option value="">No valid superiors found</option>');
                }

                if ($.fn.select2) {
                    if ($select.data('select2')) $select.select2('destroy');
                    $select.select2({
                        dropdownParent: $select.closest('.modal'),
                        width: '100%',
                        templateResult: reportingManagerTemplateResult,
                        templateSelection: reportingManagerTemplateSelection,
                        allowClear: true,
                        placeholder: "Select Manager"
                    });
                }
            }

            // Centralized populate function for Org Chart personnel assignment
            function populateReportingManagerSelect($select, filterDesignation = null, selectedId = null) {
                if (!$select || !$select.length) return;

                // 1. Initial state: Show all known employees from cache if available
                fetchBoxEmployees(function(allEmployees) {
                    // Start with all employees
                    renderReportingManagerOptions($select, allEmployees, selectedId, null, null);

                    // 2. If we have a designation, refine with hierarchy highlighting
                    if (filterDesignation) {
                        $.ajax({
                            url: '{{ route('employee.get.box.users', ['box_slug' => request()->route('box_slug')]) }}',
                            type: 'GET',
                            data: { superior_to_designation: filterDesignation },
                            success: function(response) {
                                if (response.status && Array.isArray(response.data)) {
                                    // Merge or Re-render with highlighting
                                    // We'll re-render but use a special flag to highlight superiors
                                    const superiors = response.data;
                                    const superiorIds = superiors.map(s => s.id);
                                    
                                    // Re-render all employees but pass superiors for highlighting
                                    $select.empty().append('<option value="">Select Reporting Manager</option>');
                                    
                                    // Sort: Superiors first
                                    const sortedEmployees = [...allEmployees].sort((a, b) => {
                                        const aSup = superiorIds.includes(a.id);
                                        const bSup = superiorIds.includes(b.id);
                                        if (aSup && !bSup) return -1;
                                        if (!aSup && bSup) return 1;
                                        return 0;
                                    });

                                    sortedEmployees.forEach(emp => {
                                        const isSuperior = superiorIds.includes(emp.id);
                                        const isSelected = emp.id == selectedId;
                                        const label = isSuperior ? '⭐ ' + emp.name + ' (Recommended)' : emp.name;
                                        
                                        const $opt = $('<option>', {
                                            value: emp.id,
                                            text: label
                                        });

                                        // Crucial: Set data attributes for the Select2 template
                                        $opt.attr('data-name', emp.name);
                                        $opt.attr('data-email', emp.email || '');
                                        $opt.attr('data-phone', emp.phone || '');
                                        $opt.attr('data-designation-name', emp.designation_name || '');
                                        $opt.attr('data-department-name', emp.department_name || '');
                                        $opt.attr('data-highlight-type', isSuperior ? 'immediate' : '');

                                        if (isSelected) $opt.prop('selected', true);
                                        if (isSuperior) $opt.addClass('text-success font-weight-bold');
                                        $select.append($opt);
                                    });
                                }
                            }
                        });
                    }
                });
            }

        function openAssignPersonnelModal(nodeId, nodeName, chartData) {
            const $modal = $('#assignPersonnelModal');
            $('#assignNodeId').val(nodeId);
            $('#assignNodeName').text(nodeName);
            
            const nodeData = findNodeInData(chartData, nodeId);
            $('#targetDeptName').text('-');
            $('#targetTeamName').text('-');

            if (nodeData) {
                if (nodeData.type === 'dept') {
                    $('#targetDeptName').text(nodeData.name + ' (Dept)');
                    $('#targetTeamName').text('None (Department Level)');
                } else if (nodeData.type === 'team') {
                    const deptNode = findNodeInData(chartData, nodeData.department_id);
                    $('#targetDeptName').text(deptNode ? deptNode.name + ' (Dept)' : 'Unknown');
                    $('#targetTeamName').text(nodeData.name + ' (Team)');
                }
            }

            // Fetch currently assigned personnel
            $.ajax({
                url: '{{ route('organization.get-node-personnel', ['box_slug' => request()->route('box_slug')]) }}',
                method: 'GET',
                data: { id: nodeId },
                success: function(res) {
                    const $section = $('#assignedPersonnelSection');
                    const $list = $('#assignedPersonnelList');
                    $list.empty();
                    
                    if (res.success && res.data && res.data.length > 0) {
                        res.data.forEach(p => {
                            $list.append(`
                                <tr>
                                    <td class="ps-2">
                                        <div class="fw-bold text-dark">${p.name}</div>
                                        <div class="small text-muted">${p.email}</div>
                                    </td>
                                    <td class="small">${p.reporting_manager || '-'}</td>
                                    <td class="text-end pe-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger border-0 unassign-btn" 
                                                data-id="${p.id}" title="Remove from this node">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                        $section.show();

                        $('.unassign-btn').off('click').on('click', function() {
                            const employeeId = $(this).data('id');
                            const $row = $(this).closest('tr');
                            if (confirm('Are you sure you want to remove this employee from this unit?')) {
                                $.ajax({
                                    url: '{{ route('organization.unassign-personnel', ['box_slug' => request()->route('box_slug')]) }}',
                                    method: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        id: nodeId,
                                        employee_id: employeeId
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            $row.fadeOut(300, function() {
                                                $(this).remove();
                                                if ($list.find('tr').length === 0) $section.fadeOut();
                                            });
                                            showSuccess(response.message);
                                            loadOrgChart();
                                        } else {
                                            showError(response.message || 'Failed to unassign employee');
                                        }
                                    }
                                });
                            }
                        });
                    } else {
                        $section.hide();
                    }
                }
            });

            // Populate Employee Selects
            fetchBoxEmployees(function(employees) {
                const $empSelect = $('#assignEmployeeId');
                const $rmSelect = $('#assignReportingManagerId');
                
                $empSelect.empty().append('<option value="">Search employee...</option>');
                
                employees.forEach(emp => {
                    $empSelect.append(`<option value="${emp.id}" data-email="${emp.email}" data-phone="${emp.phone}">${emp.name}</option>`);
                });

                // Populate RM Select with hierarchical logic
                // If it's a team node, nodeId is the designation ID (subtract offset if present)
                const TEAM_ID_OFFSET = 1000000;
                const filterDesig = (nodeData && nodeData.type === 'team') ? (nodeId >= TEAM_ID_OFFSET ? nodeId - TEAM_ID_OFFSET : nodeId) : null;
                const recommendedId = nodeData ? (nodeData.user_id || (nodeData.parent !== 0 ? findNodeInData(chartData, nodeData.parent)?.user_id : null)) : null;
                
                populateReportingManagerSelect($rmSelect, filterDesig, recommendedId);

                if ($.fn.select2) {
                    $empSelect.select2({
                        dropdownParent: $modal,
                        width: '100%',
                        templateResult: function(state) {
                            if (!state.id) return state.text;
                            const email = $(state.element).data('email') || '';
                            const phone = $(state.element).data('phone') || '';
                            return $(`<div class="d-flex flex-column py-1"><span class="fw-bold text-dark">${state.text}</span><span class="small text-muted" style="font-size: 11px;"><i class="ti ti-mail me-1"></i>${email} | <i class="ti ti-phone me-1"></i>${phone}</span></div>`);
                        },
                        allowClear: true,
                        placeholder: "Search employee..."
                    });
                }
                $modal.modal('show');
            });
        }

        $('#assignPersonnelForm').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#saveAssignmentBtn');
            const originalText = $btn.html();
            
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

            $.ajax({
                url: '{{ route('organization.assign-personnel', ['box_slug' => request()->route('box_slug')]) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: $('#assignNodeId').val(),
                    employee_id: $('#assignEmployeeId').val(),
                    reporting_manager_id: $('#assignReportingManagerId').val()
                },
                success: function(res) {
                    if (res.success) {
                        showSuccess(res.message);
                        $('#assignPersonnelModal').modal('hide');
                        // Refresh chart if needed (optional since we're adding people to metadata, but icons might not change)
                        loadOrgChart();
                    } else {
                        showError(res.message || 'Failed to assign employee');
                    }
                },
                error: function(xhr) {
                    showError(xhr.responseJSON?.message || 'Something went wrong');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });
    })();
</script>
@endpush

@endsection
