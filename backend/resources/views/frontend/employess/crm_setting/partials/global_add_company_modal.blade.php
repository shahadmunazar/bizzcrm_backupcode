		<!-- Add Company -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="global_add_company">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Add New Company</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form id="global_add_company_form" onsubmit="return false;" enctype="multipart/form-data">
                    <div class="tab-content">
                        <!-- Company Tab -->
                        <div class="tab-pane fade show active" id="company_tab" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Company Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="bill_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Contact Number</label>
                                        <input type="text" class="form-control" name="bill_phone">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">GSTIN</label>
                                        <input type="text" class="form-control" name="gstin">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">PAN Number</label>
                                        <input type="text" class="form-control" name="pan">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Pin Code</label>
                                        <input type="text" class="form-control" name="bill_pin_code">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">State <span class="text-danger">*</span></label>
                                        <select class="select2 form-control" name="global_bill_state_id" id="global_bill_state_id">
                                            <option value="">--Select--</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Address</label>
                                        <textarea class="form-control" name="bill_address"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">City <span class="text-danger">*</span></label>
                                        <select class="select2 form-control" name="global_bill_city_id" id="global_bill_city_id">
                                            <option value="">--Select--</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Status <span class="text-danger">*</span></label>
                                        <select class="select" name="status" id="status_select">
                                            <option value="Enable">Enable</option>
                                            <option value="Disabled">Disable</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Setting Tab Removed -->
                    </div>

                    <div class="d-flex align-items-center justify-content-start mt-3">
                        <button type="submit" class="btn btn-primary me-2" id="global_btn_save_company">Submit</button>
                        <button type="button" class="btn btn-outline-light border text-secondary" data-bs-dismiss="offcanvas">Reset</button>
                    </div>
                </form>
			</div>
		</div>
		<!-- /Add Company -->

@push('scripts')
<script>
    $(document).ready(function() {
        let globalCompanyRoutes = {
            states: "{{ box_route('activities.states') }}",
            cities: "{{ box_route('activities.cities') }}",
            store: "{{ box_route('settings.general.companies.store') }}"
        };

        // Load states on page load (India - 101) for the global modal
        loadGlobalCompanyStates();

        // Clear modal on open
        $('#global_add_company').on('show.bs.offcanvas', function () {
            $('#global_bill_state_id, #global_bill_city_id').select2({
                dropdownParent: $('#global_add_company'),
                placeholder: "--Select--",
                allowClear: true,
                width: '100%'
            });

            $('#global_add_company_form')[0].reset();
            $('#global_btn_save_company').text('Create');
            $('#global_add_company .fw-semibold').text('Add New Company');
            $('#global_add_company_form').removeData('id');
            
            // Reset selects
            $('#global_bill_state_id').val('').trigger('change');
            $('#global_bill_city_id').empty().append('<option value="">--Select--</option>').trigger('change');
        });

        // Load States
        function loadGlobalCompanyStates() {
            let stateSelect = $('#global_bill_state_id');
            $.ajax({
                url: globalCompanyRoutes.states,
                type: "GET",
                success: function(response) {
                    stateSelect.empty().append('<option value="">--Select--</option>');
                    if (response.success && response.data) {
                        $.each(response.data, function(index, item) {
                            stateSelect.append(new Option(item.name, item.id));
                        });
                    }
                    stateSelect.trigger('change'); 
                }
            });
        }

        // Load Cities
        function loadGlobalCompanyCities(stateId) {
            let citySelect = $('#global_bill_city_id');
            if(!stateId) {
                citySelect.empty().append('<option value="">--Select--</option>');
                citySelect.trigger('change');
                return;
            }

            $.ajax({
                url: globalCompanyRoutes.cities,
                type: "GET",
                data: { state_id: stateId },
                success: function(response) {
                    citySelect.empty().append('<option value="">--Select--</option>');
                    if (response.success && response.data) {
                        $.each(response.data, function(index, item) {
                            citySelect.append(new Option(item.name, item.id));
                        });
                    }
                    citySelect.trigger('change');
                }
            });
        }

        // On State Change
        $('#global_bill_state_id').on('change', function() {
            let stateId = $(this).val();
            loadGlobalCompanyCities(stateId);
        });

        // Save Company
        $('#global_add_company_form').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            
            $.ajax({
                url: globalCompanyRoutes.store,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#global_add_company').offcanvas('hide');
                    if (typeof loadCompanies === 'function') {
                        loadCompanies();
                    } else {
                        setTimeout(() => window.location.reload(), 1500);
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to create company.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert('Error: ' + errorMsg);
                }
            });
        });
    });
</script>
@endpush
