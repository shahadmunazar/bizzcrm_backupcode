
    // Working Location Script
    $(document).ready(function() {
        const WORKING_COUNTRY_ID = 101; // India ID

        // Populate States and Divisions when modal is opened
        $('#working_location').on('shown.bs.modal', function () {
            // Check if states are already populated to avoid re-fetching
            if ($('#working_state_id').children('option').length <= 1) {
                
                $.ajax({
                    url: "{{ route('company.states.select', ['box_slug' => request()->route('box_slug')]) }}",
                    type: 'POST',
                    data: {
                        country_id: WORKING_COUNTRY_ID,
                         _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                         const stateSelect = $('#working_state_id');
                         stateSelect.empty().append('<option value="">-- Select State --</option>');
                         if (res) {
                             $.each(res, function(id, name) {
                                  stateSelect.append('<option value="'+id+'">'+name+'</option>');
                             });
                         }
                    },
                    error: function(err) {
                        console.error('Error fetching states:', err);
                        showToast('Failed to load states.', 'danger');
                    }
                });
            }

            // Fetch Divisions (Independent of State)
            if ($('#working_division_id').children('option').length <= 1) {
                 $.ajax({
                    url: "{{ route('activities.divisions', ['box_slug' => request()->route('box_slug')]) }}",
                    type: 'GET',
                    success: function(res) {
                         const divisionSelect = $('#working_division_id');
                         divisionSelect.empty().append('<option value="">-- Select Division --</option>');
                         if (res.success && res.data) {
                             $.each(res.data, function(index, item) {
                                  divisionSelect.append('<option value="'+item.id+'">'+item.name+'</option>');
                             });
                         }
                    },
                    error: function(err) {
                        console.error('Error fetching divisions:', err);
                    }
                });
            }
        });

        // Handle State Change -> Fetch Cities
        $('#working_state_id').on('change', function() {
            const stateId = $(this).val();
            const citySelect = $('#working_city_id');
            // Division is now independent, do not reset
            
            citySelect.empty().append('<option value="">-- Select City --</option>');

            if (stateId) {
                $.ajax({
                    url: "{{ route('company.city.select', ['box_slug' => request()->route('box_slug')]) }}",
                    type: 'POST',
                    data: {
                        state_id: stateId,
                         _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                         if (res) {
                             $.each(res, function(id, name) {
                                  citySelect.append('<option value="'+id+'">'+name+'</option>');
                             });
                         }
                    },
                     error: function(err) {
                        console.error('Error fetching cities:', err);
                    }
                });
            }
        });

         // Optional: Handle City Change -> Fetch Divisions? (No API provided for divisions, maybe static or unused)
         // Assuming Division might be just a text field or loaded from elsewhere. 
         // For now, leaving it as is, or I could clear it on State change which I did.
    });
