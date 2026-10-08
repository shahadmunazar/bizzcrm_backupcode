<!-- Selection Modal -->
<div class="modal fade" id="selectionModal" tabindex="-1" aria-labelledby="selectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="selectionModalLabel">Select Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 border-bottom position-sticky top-0 bg-white" style="z-index: 10;">
                    <input type="text" id="selectionModalSearch" class="form-control" placeholder="Search..." autocomplete="off">
                </div>
                <div class="list-group list-group-flush" id="selectionModalList">
                    <!-- Items will be appended here -->
                </div>
                <div id="selectionModalEmptyState" class="p-3 text-center" style="display: none;">
                    <p class="text-muted mb-2">No matching records found.</p>
                    <button type="button" id="selectionModalAddNewBtn" class="btn btn-sm btn-primary">
                        + Add New <small>(F2)</small>
                    </button>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">Press <kbd>F2</kbd> to add new</small>
                <div>
                    <button type="button" class="btn btn-primary me-2" id="selectionModalFooterAddBtn">
                        + Add New
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentSelectionConfig = null;
    let selectionData = [];
    const selectionModalEl = document.getElementById('selectionModal');
    let selectionModal;
    if (selectionModalEl) {
        selectionModal = new bootstrap.Modal(selectionModalEl);
    }
    
    const searchInput = document.getElementById('selectionModalSearch');
    const listContainer = document.getElementById('selectionModalList');
    const emptyState = document.getElementById('selectionModalEmptyState');
    const addNewBtn = document.getElementById('selectionModalAddNewBtn');
    const footerAddBtn = document.getElementById('selectionModalFooterAddBtn');
    const modalTitle = document.getElementById('selectionModalLabel');

    window.openSelectionModal = function(config) {
        currentSelectionConfig = config;
        modalTitle.textContent = config.title;
        searchInput.value = '';
        listContainer.innerHTML = '<div class="p-3 text-center"><span class="spinner-border spinner-border-sm"></span> Loading...</div>';
        emptyState.style.display = 'none';
        
        selectionModal.show();

        // Fetch data
        const url = '{{ box_route("accounts.products.attributes.list") }}?type=' + config.type;
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                selectionData = res.data;
                renderSelectionList(selectionData);
            }
        })
        .catch(err => {
            console.error(err);
            listContainer.innerHTML = '<div class="p-3 text-danger text-center">Failed to load data.</div>';
        });
    };

    function renderSelectionList(data) {
        listContainer.innerHTML = '';
        if (data.length === 0) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
            data.forEach(item => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'list-group-item list-group-item-action';
                btn.textContent = item.name;
                btn.dataset.id = item.id;
                btn.addEventListener('click', function() {
                    selectItem(item.id);
                });
                listContainer.appendChild(btn);
            });
        }
    }

    function selectItem(id) {
        if (currentSelectionConfig) {
            const mainSelect = document.getElementById(currentSelectionConfig.selectId);
            if (mainSelect) {
                // Ensure option exists or add it if not
                let optionExists = Array.from(mainSelect.options).some(opt => opt.value == id);
                if (!optionExists) {
                    mainSelect.add(new Option(id, id, true, true));
                } else {
                    mainSelect.value = id;
                }
                
                // Trigger change event for Select2 or regular select
                const event = new Event('change', { bubbles: true });
                mainSelect.dispatchEvent(event);
                
                if (typeof jQuery !== 'undefined') {
                    jQuery(mainSelect).trigger('change.select2');
                }
            }

            if (currentSelectionConfig.statusName) {
                const statusSelect = document.querySelector(`select[name="${currentSelectionConfig.statusName}"]`);
                if (statusSelect) {
                    statusSelect.value = 'Yes';
                    const event = new Event('change', { bubbles: true });
                    statusSelect.dispatchEvent(event);
                }
            }
        }
        selectionModal.hide();
    }

    function triggerAddNew() {
        selectionModal.hide();
        if (currentSelectionConfig && currentSelectionConfig.createModalId) {
            setTimeout(() => {
                const createModalEl = document.getElementById(currentSelectionConfig.createModalId);
                if (createModalEl) {
                    const createModal = bootstrap.Modal.getOrCreateInstance(createModalEl);
                    createModal.show();
                }
            }, 400);
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const val = this.value.toLowerCase();
            const filtered = selectionData.filter(item => item.name.toLowerCase().includes(val));
            renderSelectionList(filtered);
        });
    }

    if (addNewBtn) {
        addNewBtn.addEventListener('click', triggerAddNew);
    }
    
    if (footerAddBtn) {
        footerAddBtn.addEventListener('click', triggerAddNew);
    }

    if (selectionModalEl) {
        selectionModalEl.addEventListener('shown.bs.modal', function() {
            searchInput.focus();
        });

        // F2 listener when modal is open
        selectionModalEl.addEventListener('keydown', function(e) {
            if (e.key === 'F2') {
                e.preventDefault();
                triggerAddNew();
            }
        });
    }
});
</script>
