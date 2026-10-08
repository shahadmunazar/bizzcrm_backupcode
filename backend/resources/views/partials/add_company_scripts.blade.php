@push('scripts')
<script>
$(document).ready(function() {
    // Clear modal on open
    $('#offcanvas_add').on('show.bs.offcanvas', function () {
        // Init Select2 inside offcanvas to handle z-index and focus
        $('#bill_state_id, #bill_city_id').select2({
            dropdownParent: $('#offcanvas_add'),
            theme: 'bootstrap-5',
            width: '100%'
        });

        $('#add-company-form')[0].reset();
        $('#bill_state_id, #bill_city_id').trigger('change.select2');
        $('#btn-save-company').text('Submit');
        $('#offcanvas_add .fw-semibold').text('Add New Company');
        $('#add-company-form').removeData('id');

        console.log("Modal opened, calling loadStates");
        loadStates();
    });

    $("#addCompanyModal").on("show.bs.modal", function () {
        // Init Select2 inside modal to handle z-index and focus
        $("#bill_state_id, #bill_city_id").select2({
            dropdownParent: $("#addCompanyModal"),
            theme: "bootstrap-5",
            width: "100%"
        });

        $("#add-company-form")[0].reset();
        $("#bill_state_id, #bill_city_id").trigger("change.select2");
        $("#btn-save-company").text("Submit");
        $("#addCompanyModal .modal-title").text("Add New Company");
        $("#add-company-form").removeData("id");
        
        console.log("Modal opened, calling loadStates");
        loadStates();
    });

    // Load States
    function loadStates() {
        console.log("Executing $.ajax for loadStates");
        $.ajax({
            url: "{{ box_route('activities.states') }}",
            type: "GET",
            data: { country_id: 101, type: 'state' }, // Fetching for India
            success: function(response) {
                let stateSelect = $('#bill_state_id');
                stateSelect.empty().append('<option value="">--Select--</option>');
                if (response.success && response.data) {
                    $.each(response.data, function(index, item) {
                        stateSelect.append(new Option(item.name, item.id));
                    });
                }
                // Trigger change to update Select2 if used
                stateSelect.trigger('change'); 
            },
            error: function(xhr, status, error) {
                console.error("Failed to load states:", error);
                if (typeof toastr !== 'undefined') {
                    toastr.error("Failed to load states.");
                }
            }
        });
    }

    // Load Cities
    function loadCities(stateId, selectedCityId = null) {
        let citySelect = $('#bill_city_id');
        if(!stateId) {
            citySelect.empty().append('<option value="">--Select--</option>');
             // Trigger change to update Select2 if used
            citySelect.trigger('change');
            return;
        }

        $.ajax({
            url: "{{ box_route('activities.cities') }}",
            type: "GET",
            data: { state_id: stateId },
            success: function(response) {
                citySelect.empty().append('<option value="">--Select--</option>');
                if (response.success && response.data) {
                    $.each(response.data, function(index, item) {
                        citySelect.append(new Option(item.name, item.id));
                    });
                }
                if(selectedCityId) {
                    citySelect.val(selectedCityId);
                }
                // Trigger change to update Select2 if used
                citySelect.trigger('change');
            }
        });
    }

    // On State Change
    $('#bill_state_id').on('change', function() {
        let stateId = $(this).val();
        loadCities(stateId);
    });

    // Save Company
    $('#add-company-form').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let id = $(this).data('id');
        let url = id 
            ? "{{ box_route('settings.general.companies.update') }}"
            : "{{ box_route('settings.general.companies.store') }}";
        
        if (id) {
            formData.append('_method', 'PUT');
            formData.append('id', id); // explicit ID for backend fallback
        }

        $.ajax({
            url: url,
            type: 'POST', // Use POST with _method=PUT for file uploads
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#offcanvas_add').offcanvas('hide');
                $('#addCompanyModal').modal('hide');
                
                if (typeof loadCompanies === 'function') {
                    loadCompanies();
                }
                $(document).trigger('company:added', [response.company]);
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseText);
            }
        });
    });

    loadStates(); // Load states on page load (India - 101)
});
</script>
@endpush
