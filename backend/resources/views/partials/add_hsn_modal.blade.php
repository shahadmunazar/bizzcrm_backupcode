<!-- Create HSN Modal -->
<div id="createHsnModal" class="modal custom-modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add HSN/SAC</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createHsnForm">
                    @csrf
                    <input type="hidden" name="company_id" value="{{ request('company_id') }}">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>HSN/SAC (Code) <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="code" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Short Name</label>
                            <input class="form-control" type="text" name="short_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>SGST % <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" step="0.01" name="sgst" value="0.00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>CGST % <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" step="0.01" name="cgst" value="0.00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>IGST % <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" step="0.01" name="igst" value="0.00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>TYPE <span class="text-danger">*</span></label>
                            <select class="form-control" name="type" required>
                                <option value="Goods">Goods</option>
                                <option value="Services">Services</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>UQC (Unit)</label>
                            <input class="form-control" type="text" name="uqc">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>CESS % <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" step="0.01" name="cess" value="0.00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Effective Date From</label>
                            <input class="form-control" type="date" name="from_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Effective Date To</label>
                            <input class="form-control" type="date" name="to_date">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" required>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="submit-section text-center mt-3">
                        <button type="submit" class="btn btn-primary submit-btn px-5">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Create HSN Modal -->

@push('scripts')
<script>
$(document).ready(function() {
    // Create HSN logic for generic modal
    $('#createHsnForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        $.ajax({
            url: "{{ box_route('accounts.product_hsns.store') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#createHsnModal').modal('hide');
                    $('#createHsnForm')[0].reset();
                    showToast(response.message || 'HSN created successfully.', 'success');
                    
                    // If we are on the HSNs index page, reload the datatable
                    if (typeof window.hsnTable !== 'undefined') {
                        window.hsnTable.ajax.reload(null, false);
                    }
                    
                    // If we are on the Product Create/Edit page, add the new option to the select
                    if ($('#hsn_select').length) {
                        var newOption = new Option(response.data.code, response.data.code, true, true);
                        $('#hsn_select').append(newOption).trigger('change');
                    }
                } else {
                    showToast('Error creating HSN', 'error');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        var input = $('#createHsnForm [name="' + key + '"]');
                        if (input.length) {
                            input.addClass('is-invalid');
                            input.after('<div class="invalid-feedback d-block">' + value[0] + '</div>');
                        } else {
                            showToast(value[0], 'error');
                        }
                    });
                } else {
                    showToast('Something went wrong', 'error');
                }
            }
        });
    });

    // Auto calculate GST fields
    function calculateCreateIgst() {
        var sgst = parseFloat($('#createHsnForm input[name="sgst"]').val()) || 0;
        var cgst = parseFloat($('#createHsnForm input[name="cgst"]').val()) || 0;
        $('#createHsnForm input[name="igst"]').val((sgst + cgst).toFixed(2));
    }

    $('#createHsnForm input[name="sgst"], #createHsnForm input[name="cgst"]').on('input', calculateCreateIgst);
});
</script>
@endpush
