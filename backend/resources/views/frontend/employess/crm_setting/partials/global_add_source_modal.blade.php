<!-- Add New Source -->
    <div class="modal fade" id="global_add_source" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Source</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form action="sources.html">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="col-form-label">Source Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="mb-0">
                            <label class="col-form-label">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <input type="radio" class="status-radio" id="global-add-source-active" name="status"
                                        checked="">
                                    <label for="global-add-source-active">Active</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="global-add-source-inactive" name="status">
                                    <label for="global-add-source-inactive">Inactive</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex align-items-center justify-content-end m-0">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add New Source -->

@push('scripts')
<script>
$(document).ready(function() {
    $(document).on('submit', '#global_add_source form', function(e) {
        e.preventDefault();

        const $form = $(this);
        const url = "{{ route('store.source', ['box_slug' => request()->route('box_slug') ?? session('box_slug') ?? 'default']) }}";

        const data = {
            name: $form.find('input[type="text"]').val().trim(),
            status: $('#global-add-source-active').is(':checked') ? 1 : 0,
            type: 'source'
        };

        const submitBtn = $form.find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                submitBtn.prop('disabled', false).text(originalText);
                if (response.success) {
                    $('#global_add_source').modal('hide');
                    $form[0].reset();
                    if(typeof showToast === 'function') showToast(response.message || 'Source added successfully!', 'success');
                    
                    if (typeof reloadSourceTable === 'function') {
                        try { reloadSourceTable(); } catch(e) {}
                    } else if ($('#source-list').length) {
                        try { $('#source-list').DataTable().ajax.reload(); } catch(e) {}
                    } else {
                        setTimeout(function(){window.location.reload();}, 1500);
                    }
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).text(originalText);
                const res = xhr.responseJSON;
                if (xhr.status === 422 && res?.errors) {
                    const messages = Object.values(res.errors).flat().join(' ');
                    if(typeof showToast === 'function') showToast(messages, 'error');
                } else {
                    if(typeof showToast === 'function') showToast(res?.message || 'Failed to add source.', 'error');
                }
            }
        });
    });
});
</script>
@endpush
