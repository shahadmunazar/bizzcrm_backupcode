<style>
/* Clean Tree Styles */
.tree {
    margin: 10px 0;
    padding: 10px;
}
.tree ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}
.tree li {
    margin: 0;
    padding: 0;
    position: relative;
}
.tree .node {
    display: inline-flex;
    align-items: center;
    padding: 12px 16px;
    border: 1px solid #e2e5e8;
    border-radius: 6px;
    background: #fff;
    margin-bottom: 15px;
    min-width: 250px;
}
.tree .toggle {
    cursor: pointer;
    margin-right: 12px;
    color: #6C7A9C;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}
.tree .toggle i {
    border-radius: 2px;
}
.tree input[type="checkbox"] {
    margin-right: 12px;
    width: 18px;
    height: 18px;
    cursor: pointer;
    border: 1px solid #ced4da;
    border-radius: 4px;
}
.tree .node-label {
    font-weight: 500;
    color: #3f4254;
    font-size: 15px;
}
.designation-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #ebeef2;
    padding: 20px 24px;
}
.designation-title {
    color: #2A3B53;
    font-size: 22px;
    font-weight: 700;
    margin: 0;
}
.custom-circle-close {
    background-color: #fff;
    border: 1px solid #e2e5e8;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    padding: 0;
    opacity: 1;
    transition: background-color 0.2s;
}
.custom-circle-close:hover {
    background-color: #f1f3f5;
}

/* Ensure Designation Tree Modal is on top of other modals */
#designation_tree_modal {
    z-index: 1095 !important;
}
.modal-backdrop.designation-backdrop {
    z-index: 1090 !important;
}

.tree .node.selected-node {
    background-color: #fff4f2;
    border-color: #E41F07;
}
</style>

<div class="modal fade" id="designation_tree_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header designation-header">
                <h5 class="modal-title designation-title">Select Designation</h5>
                <button type="button" class="btn-close custom-circle-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="card shadow-none border-0 mb-0">
                    <div class="card-header border-bottom d-none">
                        <!-- Search -->
                        <div class="row align-items-center" style="display:none;">
                            <div class="col-sm-4">
                                <div class="icon-form mb-3 mb-sm-0">
                                    <span class="form-icon"><i class="ti ti-search"></i></span>
                                    <input type="text" class="form-control" placeholder="Search Designations">
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
                                    <div class="dropdown me-2">
                                        <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
                                        <div class="dropdown-menu  dropdown-menu-end">
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-file-type-pdf text-danger me-1"></i>Export
                                                        as PDF</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-file-type-xls text-green me-1"></i>Export
                                                        as Excel </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    
                                    <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_city"><i class="ti ti-square-rounded-plus me-2"></i>Add City</a>
                                </div>
                            </div>
                        </div>
                        <!-- /Search -->
                    </div>
                    <div class="card-body">
                        <div class="tree" id="designation_tree_container">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm_designation_selection">Select Designation</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Store target dropdown to update upon selection
window.activeDesignationTarget = null;

// The main function to populate and open the tree
window.openDesignationTree = function(departmentId, targetSelectId) {
    window.activeDesignationTarget = targetSelectId;
    console.log('Opening Designation Tree for Department:', departmentId);
    
    let teams = [];
    
    // Priority 1: Check window.teamOptions (the preferred way)
    if (typeof window.teamOptions !== 'undefined' && window.teamOptions[departmentId]) {
        teams = window.teamOptions[departmentId];
        console.log('Found teams in window.teamOptions:', teams.length);
    } 
    // Priority 2: Fallback to searching global departments array if it exists
    else if (typeof departments !== 'undefined' && Array.isArray(departments)) {
        const dept = departments.find(d => d.id == departmentId);
        if (dept) {
            teams = dept.all_teams || dept.allTeams || [];
            console.log('Found teams in departments array:', teams.length);
        }
    }

    const container = $('#designation_tree_container');
    container.empty();

    if (!teams || teams.length === 0) {
        console.warn('No teams found for department:', departmentId);
        container.html('<div class="p-3 text-muted">No designations found for this department.</div>');
        $('#designation_tree_modal').modal('show');
        return;
    }

    // Build hierarchy
    const teamMap = {};
    const rootTeams = [];

    // init map
    teams.forEach(t => {
        teamMap[t.id] = { ...t, children: [] };
    });

    // attach children
    teams.forEach(t => {
        if (t.parent_id && teamMap[t.parent_id]) {
            teamMap[t.parent_id].children.push(teamMap[t.id]);
        } else {
            rootTeams.push(teamMap[t.id]);
        }
    });

    function generateHtml(nodes, isRoot = true) {
        if (!nodes || nodes.length === 0) return '';
        let ulStyle = isRoot ? 'padding-left: 0;' : 'padding-left: 40px;';
        let html = `<ul style="${ulStyle}">`;
        
        nodes.forEach(node => {
            const hasChildren = node.children && node.children.length > 0;
            // Start collapsed by default (fa-plus-square) so it looks clean like the mockup
            let toggleIcon = hasChildren 
                ? '<span class="toggle"><i class="fas fa-plus-square text-secondary"></i></span>' 
                : '<span class="toggle" style="visibility:hidden; width:16px;"></span>';
            
            html += `
                <li>
                    <div class="node">
                        ${toggleIcon}
                        <input type="checkbox" class="designation-checkbox form-check-input mt-0" value="${node.id}" data-name="${node.name} (Team)">
                        <span class="node-label" style="cursor:pointer;" onclick="$(this).siblings('.designation-checkbox').click()">${node.name} (Team)</span>
                    </div>
                    <div class="children-container" style="display: none;">
                        ${hasChildren ? generateHtml(node.children, false) : ''}
                    </div>
                </li>
            `;
        });
        html += '</ul>';
        return html;
    }

    container.html(generateHtml(rootTeams));

    // Highlight and expand to show the selected checkbox
    const currentVal = $(targetSelectId).val();
    if (currentVal) {
        const $selectedChk = container.find(`.designation-checkbox[value="${currentVal}"]`);
        if ($selectedChk.length) {
            $selectedChk.prop('checked', true);
            $selectedChk.closest('.node').addClass('selected-node');
            // Expand all parents
            $selectedChk.parents('.children-container').show();
            // Change the icons of expanded parents from plus to minus
            $selectedChk.parents('.children-container').each(function() {
                $(this).siblings('.node').find('.toggle i').removeClass('fa-plus-square').addClass('fa-minus-square');
            });
        }
    }

    // Forcefully remove aria-hidden if the parent modal added it, to prevent focus block error
    var $modal = $('#designation_tree_modal');
    $modal.removeAttr('aria-hidden');
    $modal.removeAttr('inert');

    $modal.modal('show');
    
    // Apply higher z-index to the backdrop specifically for this modal
    setTimeout(function() {
        $('.modal-backdrop').last().addClass('designation-backdrop');
    }, 50);
};

$(function() {
    // Tree Toggle Logic
    $(document).off('click', '.tree .toggle').on('click', '.tree .toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var icon = $(this).find('i');
        var childrenContainer = $(this).closest('li').find('> .children-container');
        
        if (childrenContainer.length > 0) {
            if (childrenContainer.is(':visible')) {
                childrenContainer.slideUp('fast');
                icon.removeClass('fa-minus-square').addClass('fa-plus-square');
            } else {
                childrenContainer.slideDown('fast');
                icon.removeClass('fa-plus-square').addClass('fa-minus-square');
            }
        }
    });

    // Make checkboxes act like radio buttons for designation selection
    $(document).off('change', '.designation-checkbox').on('change', '.designation-checkbox', function() {
        if ($(this).is(':checked')) {
            $('.designation-checkbox').not(this).prop('checked', false);
            $('.node').removeClass('selected-node');
            $(this).closest('.node').addClass('selected-node');
        } else {
            $(this).closest('.node').removeClass('selected-node');
        }
    });

    // Node Selection via Click
    $(document).off('click', '.tree .node').on('click', '.tree .node', function(e) {
        if (!$(e.target).hasClass('designation-checkbox') && !$(e.target).closest('.toggle').length) {
            $(this).find('.designation-checkbox').click();
        }
    });

    // Handle "Select Designation" button with event delegation
    $(document).off('click', '#confirm_designation_selection').on('click', '#confirm_designation_selection', function(e) {
        e.preventDefault();
        
        var selectedCheckbox = $('.designation-checkbox:checked');
        if (selectedCheckbox.length > 0) {
            var selectedDesignationId = selectedCheckbox.val();
            var selectedDesignationName = selectedCheckbox.data('name');
            var targetIdName;
            
            if (window.activeDesignationTarget) {
                // Because activeDesignationTarget is #designation or #designationedit
                targetIdName = window.activeDesignationTarget.replace('#', '');
                
                // Directly set the hidden input with the ID
                $('#' + targetIdName).val(selectedDesignationId).trigger('change');
                
                // Directly set the visual read-only input with the label
                let $visibleInput = $('#' + targetIdName + '_visible');
                if (!$visibleInput.length) {
                    if (targetIdName.includes('_designation_')) {
                        const parts = targetIdName.split('_designation_');
                        const prefix = parts[0];
                        const fieldKey = parts[1];
                        $visibleInput = $('#' + prefix + '_designation_visible_' + fieldKey);
                    }
                }
                $visibleInput.val(selectedDesignationName).trigger('change');
            }
            
            // Shift focus safely to an element outside the modal to prevent ARIA hidden errors
            let $focusEl = $('#' + targetIdName + '_visible');
            if (!$focusEl.length && targetIdName && targetIdName.includes('_designation_')) {
                const parts = targetIdName.split('_designation_');
                const prefix = parts[0];
                const fieldKey = parts[1];
                $focusEl = $('#' + prefix + '_designation_visible_' + fieldKey);
            }
            if ($focusEl.length) {
                $focusEl.focus();
            } else {
                $(this).blur();
            }
            
            // Clean up when hiding
            $('#designation_tree_modal').modal('hide');
            $('.modal-backdrop').not('.show').remove(); // Clean up rogue backdrops just in case

            // Delay alert to allow modal animation to resolve properly
            setTimeout(function() {
                // No intrusive alert is required anymore. Visual change is enough.
            }, 300);
        } else {
            alert('Please select a designation first.');
        }
    });
    $('#designation_tree_modal').on('hidden.bs.modal', function () {
        $('.designation-backdrop').remove();
        // Ensure the body stays scrollable if the bulk modal is still open
        if ($('.modal.show').length > 0) {
            $('body').addClass('modal-open');
        }
    });
});
</script>
@endpush
