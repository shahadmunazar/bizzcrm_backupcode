<div class="modal fade" id="globalAddLeadModal" tabindex="-1" aria-labelledby="globalAddLeadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-semibold" id="globalAddLeadModalLabel">Add New Lead</h5>
                <button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="globalLeadsForm" method="POST" novalidate>
                    @csrf
                    <div id="global-sortable-fields-add" class="sortable-container">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3 w-100">Create Lead</button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('frontend.leads.partials.add_lead_form_js')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const globalModal = document.getElementById('globalAddLeadModal');
        if (globalModal) {
            globalModal.addEventListener('show.bs.modal', function () {
                const container = document.getElementById('global-sortable-fields-add');
                if (container.dataset.loaded !== 'true') {
                    // Use fallback box_slug if not available in route (e.g., global pages)
                    var boxSlug = "{{ request()->route('box_slug') ?? session('box_slug') ?? 'default' }}";
                    var url = "{{ route('leads.add-form-fields', ['box_slug' => ':slug']) }}".replace(':slug', boxSlug);
                    
                    fetch(url)
                        .then(response => response.json())
                        .then(res => {
                            if(res.status && res.data) {
                                // renderAddLeadForm is provided by add_lead_form_js.blade.php
                                container.innerHTML = renderAddLeadForm(res.data);
                                container.dataset.loaded = 'true';
                                
                                if (typeof jQuery !== 'undefined' && $.fn.select2) {
                                    $(container).find('.dynamic-select').select2({
                                        dropdownParent: $('#globalAddLeadModal')
                                    });
                                }
                            } else {
                                container.innerHTML = '<div class="alert alert-danger">Failed to load form fields.</div>';
                            }
                        })
                        .catch(err => {
                            console.error('Error loading fields:', err);
                            container.innerHTML = '<div class="alert alert-danger">Error loading form fields.</div>';
                        });
                }
            });

            // Form Submit Logic
            $('#globalLeadsForm').on('submit', function (e) {
                e.preventDefault();
                const $form = $(this);
                
                // Simple validation
                let isValid = true;
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('[data-required="true"], [required]').each(function() {
                    const $field = $(this);
                    let val = $field.val();
                    if (!val || String(val).trim() === '') {
                        isValid = false;
                        $field.addClass('is-invalid');
                    }
                });

                if (!isValid) return false;

                const form = this;
                const formData = new FormData(form);
                
                var boxSlug = "{{ request()->route('box_slug') ?? session('box_slug') ?? 'default' }}";
                var submitUrl = "{{ route('leads.store', ['box_slug' => ':slug']) }}".replace(':slug', boxSlug);

                const submitBtn = $(form).find('button[type="submit"]');
                const originalText = submitBtn.text();
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Creating...');

                $.ajax({
                    url: submitUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        submitBtn.prop('disabled', false).text(originalText);
                        if (response.status) {
                            if (typeof showToast === 'function') showToast(response.message, 'success');
                            $('#globalAddLeadModal').modal('hide');
                            
                            // Redirect to lead details if we get an ID
                            if (response.lead && response.lead.id) {
                                var detailsUrl = "{{ route('LeadsDetailsPage', ['box_slug' => ':slug']) }}".replace(':slug', boxSlug);
                                window.location.href = detailsUrl + '?lead_id=' + response.lead.id;
                            } else if (typeof fetchLeadsList === 'function') {
                                fetchLeadsList(); // Refresh list if on leads page
                            }
                        } else {
                            if (typeof showToast === 'function') showToast(response.message || 'Error creating lead', 'danger');
                        }
                    },
                    error: function (xhr) {
                        submitBtn.prop('disabled', false).text(originalText);
                        if (typeof showToast === 'function') showToast('An error occurred. Please check the form.', 'danger');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            for (let field in errors) {
                                let fieldEl = $form.find(`[name="${field}"]`);
                                fieldEl.addClass('is-invalid');
                            }
                        }
                    }
                });
            });
        }
    });
</script>
@endpush
