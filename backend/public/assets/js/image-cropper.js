/**
 * Image Cropper for Employee List Page
 * Handles image cropping for both Add and Edit employee forms using Croppie.js
 * Uses class-based selectors to work with multiple forms
 */

$(document).ready(function() {
    // Store croppie instances for each modal
    var croppieInstances = {};

    // Store form values to prevent clearing when image is uploaded
    var formValuesStore = {};

    // Track which forms are in image upload mode
    var formsInImageUpload = {};

    /**
     * Store all form field values
     */
    function storeFormValues($form) {
        var formId = $form.attr('id');
        if (!formId) return;

        formValuesStore[formId] = {};
        $form.find('input, select, textarea').each(function() {
            var $field = $(this);
            var name = $field.attr('name');
            var type = $field.attr('type');
            var id = $field.attr('id');

            if (name && type !== 'file' && type !== 'hidden' && name !== 'cropped_image') {
                if (type === 'checkbox' || type === 'radio') {
                    formValuesStore[formId][name] = $field.is(':checked');
                } else {
                    formValuesStore[formId][name] = $field.val();
                }
            } else if (id && type !== 'file' && type !== 'hidden') {
                if (type === 'checkbox' || type === 'radio') {
                    formValuesStore[formId]['_id_' + id] = $field.is(':checked');
                } else {
                    formValuesStore[formId]['_id_' + id] = $field.val();
                }
            }
        });

        // Mark form as in image upload mode
        formsInImageUpload[formId] = true;
        $form.data('image-upload-active', true);
    }

    /**
     * Restore all form field values
     */
    function restoreFormValues($form) {
        var formId = $form.attr('id');
        if (!formId || !formValuesStore[formId]) return;

        $.each(formValuesStore[formId], function(key, value) {
            var $field = null;
            if (key.startsWith('_id_')) {
                var fieldId = key.replace('_id_', '');
                $field = $form.find('#' + fieldId);
            } else {
                $field = $form.find('[name="' + key + '"]');
            }

            if ($field.length > 0) {
                var type = $field.attr('type');
                if (type === 'checkbox' || type === 'radio') {
                    $field.prop('checked', value);
                } else if ($field.is('select')) {
                    $field.val(value).trigger('change');
                } else {
                    $field.val(value);
                }
            }
        });
    }

    /**
     * Prevent form reset when image upload is active
     */
    $(document).on('reset', 'form', function(e) {
        var $form = $(this);
        var formId = $form.attr('id');

        // If form is in image upload mode, prevent reset and restore values
        if (formsInImageUpload[formId] || $form.data('image-upload-active')) {
            e.preventDefault();
            e.stopPropagation();

            // Restore values immediately
            restoreFormValues($form);

            // Also restore after a short delay to catch any async resets
            setTimeout(function() {
                restoreFormValues($form);
            }, 50);

            setTimeout(function() {
                restoreFormValues($form);
            }, 200);

            return false;
        }
    });

    /**
     * Initialize croppie for a specific modal
     * @param {string} modalId - The ID of the modal containing the image_demo container
     * @returns {jQuery} Croppie instance or null
     */
    function initCroppie(modalId) {
        if (croppieInstances[modalId]) {
            return croppieInstances[modalId];
        }

        var $modal = $('#' + modalId);
        console.log('initCroppie - Modal ID:', modalId, 'Modal found:', $modal.length > 0);

        if ($modal.length === 0) {
            console.error('Modal not found for initCroppie:', modalId);
            return null;
        }

        var $imageDemo = $modal.find('.image_demo');
        // Also check for ID selector as fallback
        if ($imageDemo.length === 0) {
            $imageDemo = $modal.find('#image_demo');
        }

        if ($imageDemo.length === 0) {
            console.error('image_demo container not found in modal:', modalId);
            return null;
        }

        console.log('image_demo found:', $imageDemo.length > 0);

        var croppie = $imageDemo.croppie({
            enableExif: true,
            viewport: {
                width: 200,
                height: 200,
                type: 'circle'
            },
            boundary: {
                height: 300
            }
        });

        croppieInstances[modalId] = croppie;
        return croppie;
    }

    /**
     * Handle image file selection - works for both add and edit forms
     * Uses event delegation to handle dynamically added file inputs
     */
    $(document).on('change', '.image-sign', function() {
        console.log('Image file input changed');
        var $input = $(this);
        var $form = $input.closest('form');
        var formId = $form.attr('id');
        console.log('Form ID:', formId);

        var isEditForm = formId === 'UpdateFormEmPloyee' || formId === 'employee_edit_form';
        var modalId;
        if (formId === 'UpdateFormEmPloyee') {
            modalId = 'edit_uploadimageModal';
        } else if (formId === 'employee_edit_form') {
            modalId = 'add_uploadimageModal'; // Details page uses this ID from snippet
        } else {
            modalId = 'add_uploadimageModal';
        }
        console.log('Modal ID to use:', modalId);
        var $modal = $('#' + modalId);
        console.log('Modal found:', $modal.length > 0, 'Modal element:', $modal[0]);

        if (this.files && this.files[0]) {
            // Clear any existing cropped_image when a new file is selected
            var $croppedInput = $form.find('#edit_details_cropped_image');
            if ($croppedInput.length === 0) {
                $croppedInput = $form.find('#edit_cropped_image');
            }
            if ($croppedInput.length === 0) {
                $croppedInput = $form.find('input[name="cropped_image"]');
            }
            if ($croppedInput.length > 0) {
                $croppedInput.val('');
                $form.data('cropped-image', '');
            }

            // Store form values BEFORE anything else happens
            storeFormValues($form);

            var reader = new FileReader();
            reader.onload = function(event) {
                // Initialize croppie for this modal if not already done
                var croppie = initCroppie(modalId);
                if (croppie) {
                    croppie.croppie('bind', {
                        url: event.target.result
                    }).then(function() {
                        console.log('Croppie bind complete for ' + modalId);
                    });
                }
            };
            reader.readAsDataURL(this.files[0]);

            // Move modal to body if it's nested (for proper z-index handling)
            if ($modal.parent().is('.modal-body, .modal-content, .modal')) {
                console.log('Moving modal to body (was nested)');
                $modal.appendTo('body');
            }

            // Show modal
            if ($modal.length === 0) {
                console.error('Modal not found with ID:', modalId);
                console.log('Available modals:', $('.uploadimageModal').map(function() { return this.id; }).get());
                alert('Image cropper modal not found. Please check the page structure.');
                return;
            }
            console.log('Showing modal:', modalId, 'Modal element:', $modal[0]);

            // Temporarily disable other modal show events to prevent interference
            var $editEmployeeModal = $('#edit_employee');
            if ($editEmployeeModal.length > 0) {
                $editEmployeeModal.off('show.bs.modal');
            }

            // Use Bootstrap 5 Modal API if available, otherwise use jQuery/Bootstrap 4
            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    console.log('Using Bootstrap 5 Modal API');
                    // Check if modal instance already exists
                    var existingModal = bootstrap.Modal.getInstance($modal[0]);
                    if (existingModal) {
                        console.log('Using existing modal instance');
                        existingModal.show();
                    } else {
                        console.log('Creating new modal instance');
                        var modalInstance = new bootstrap.Modal($modal[0], {
                            backdrop: true,
                            keyboard: true
                        });
                        modalInstance.show();
                    }
                } else {
                    console.log('Using jQuery/Bootstrap 4 modal');
                    // Fallback to jQuery/Bootstrap 4
                    $modal.modal({
                        backdrop: true,
                        keyboard: true,
                        show: true
                    });
                }
            } catch (e) {
                console.error('Error showing modal:', e);
                // Fallback to jQuery
                $modal.modal('show');
            }

            // Re-enable edit employee modal events after a short delay
            setTimeout(function() {
                if ($editEmployeeModal.length > 0) {
                    // The event handler will be re-attached when needed
                    // This is just to prevent the event from firing during crop modal opening
                }
            }, 100);

            // Aggressively restore form values at multiple intervals
            setTimeout(function() {
                restoreFormValues($form);
            }, 50);

            setTimeout(function() {
                restoreFormValues($form);
            }, 150);

            setTimeout(function() {
                restoreFormValues($form);
            }, 300);

            // Also restore when modal is shown
            $modal.on('shown.bs.modal', function() {
                restoreFormValues($form);
                setTimeout(function() {
                    restoreFormValues($form);
                }, 100);
            });
        }
    });

    /**
     * Handle crop and upload button click
     * Gets the cropped image and updates the form preview
     */
    $(document).on('click', '.crop_image', function() {
        var $button = $(this);
        var $modal = $button.closest('.uploadimageModal');
        var modalId = $modal.attr('id');
        var croppie = croppieInstances[modalId];

        if (!croppie) {
            console.error('Croppie instance not found for ' + modalId);
            return;
        }

        croppie.croppie('result', {
            type: 'canvas',
            size: 'viewport'
        }).then(function(response) {
            // Find the corresponding uploaded_image container
            var $form = null;
            if (modalId === 'edit_uploadimageModal') {
                $form = $('#UpdateFormEmPloyee');
            } else if (modalId === 'add_uploadimageModal') {
                 // Check if it's the details page form
                 // We can distinguish by checking which form is currently active/visible
                 // Or by checking if employee_edit_form exists and the modal was triggered from it
                 if ($('#employee_edit_form').length > 0 && $('#employee_edit_form').is(':visible')) {
                     $form = $('#employee_edit_form');
                 } else if ($('#employee_edit_form').length > 0 && $('#edit_employee').hasClass('show')) {
                     // The edit_employee modal (which contains employee_edit_form) is open
                     $form = $('#employee_edit_form');
                 } else {
                     $form = $('#employeeForm');
                 }
            } else {
                $form = $('#employeeForm');
            }

            if ($form && $form.length > 0) {
                // Store employee ID BEFORE doing anything else (in case form gets reset)
                var empId = null;
                if (modalId === 'edit_uploadimageModal' || $form.attr('id') === 'employee_edit_form') {
                    var $empIdInput = $form.find('#edit_employee_id');
                    if ($empIdInput.length === 0) {
                        $empIdInput = $form.find('#edit_employee_details_id');
                    }
                    if ($empIdInput.length > 0) {
                        empId = $empIdInput.val();
                        // Store employee ID in form data to preserve it
                        $form.data('employee-id', empId);
                    }
                }

                // Display the cropped image in the form preview IMMEDIATELY
                var $uploadedImage = $form.find('.uploaded_image');
                if ($uploadedImage.length > 0) {
                    // Display the cropped image in the form preview
                    var html = '<img src="' + response + '" class="img-fluid rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" alt="Cropped Image" />';
                    $uploadedImage.html(html);
                }

                // Store the cropped image as base64 in a hidden input for form submission
                // Try to find by ID first (for edit form), then by name
                var $hiddenInput = $form.find('#edit_cropped_image');
                if ($hiddenInput.length === 0) {
                    $hiddenInput = $form.find('#edit_details_cropped_image');
                }
                if ($hiddenInput.length === 0) {
                    $hiddenInput = $form.find('input[name="cropped_image"]');
                }
                if ($hiddenInput.length === 0) {
                    // Create the hidden input if it doesn't exist
                    var hiddenId = ($form.attr('id') === 'employee_edit_form') ? 'edit_details_cropped_image' : 'edit_cropped_image';
                    $form.append('<input type="hidden" name="cropped_image" id="' + hiddenId + '" value="">');
                    $hiddenInput = $form.find('input[name="cropped_image"]');
                }
                // Set the cropped image value
                $hiddenInput.val(response);
                console.log('Cropped image stored in hidden input:', $hiddenInput.attr('id'), 'Length:', response.length);

                // Also store in form data for extra safety
                $form.data('cropped-image', response);
                console.log('Cropped image also stored in form data attribute');

                // Restore employee ID if it was stored
                if ((modalId === 'edit_uploadimageModal' || $form.attr('id') === 'employee_edit_form') && empId) {
                    var $empIdInput = $form.find('#edit_employee_id');
                    if ($empIdInput.length === 0) {
                        $empIdInput = $form.find('#edit_employee_details_id');
                    }
                    if ($empIdInput.length > 0) {
                        $empIdInput.val(empId);
                    }
                }

                // Note: We don't save immediately here to avoid duplicate API calls
                // The cropped image will be saved when the form is submitted
                // The image preview is already updated above, so the user can see the cropped result
            }

            // Hide modal
            $modal.modal('hide');

            // Wait for modal to hide, then restore form values aggressively
            $modal.off('hidden.bs.modal').on('hidden.bs.modal', function() {
                if ($form && $form.length > 0) {
                    // Restore employee ID FIRST (most important)
                    if (modalId === 'edit_uploadimageModal') {
                        var storedEmpId = $form.data('employee-id');
                        if (storedEmpId) {
                            var $empIdInput = $form.find('#edit_employee_id');
                            if ($empIdInput.length > 0) {
                                $empIdInput.val(storedEmpId);
                            }
                        }

                        // Restore cropped image
                        var storedCroppedImage = $form.data('cropped-image');
                        if (storedCroppedImage) {
                            var $croppedInput = $form.find('#edit_cropped_image');
                            if ($croppedInput.length > 0) {
                                $croppedInput.val(storedCroppedImage);
                            }

                            // Ensure image is still displayed
                            var $uploadedImage = $form.find('.uploaded_image');
                            if ($uploadedImage.length > 0 && !$uploadedImage.find('img').length) {
                                var html = '<img src="' + storedCroppedImage + '" class="img-fluid rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" alt="Cropped Image" />';
                                $uploadedImage.html(html);
                            }
                        }
                    }

                    // Restore form values multiple times to ensure they stick
                    restoreFormValues($form);

                    setTimeout(function() {
                        // Restore employee ID again
                        if (modalId === 'edit_uploadimageModal') {
                            var storedEmpId = $form.data('employee-id');
                            if (storedEmpId) {
                                var $empIdInput = $form.find('#edit_employee_id');
                                if ($empIdInput.length > 0) {
                                    $empIdInput.val(storedEmpId);
                                }
                            }

                            // Restore cropped image again
                            var storedCroppedImage = $form.data('cropped-image');
                            if (storedCroppedImage) {
                                var $croppedInput = $form.find('#edit_cropped_image');
                                if ($croppedInput.length > 0) {
                                    $croppedInput.val(storedCroppedImage);
                                }
                            }
                        }
                        restoreFormValues($form);
                    }, 50);
                    setTimeout(function() {
                        restoreFormValues($form);
                    }, 150);
                    setTimeout(function() {
                        restoreFormValues($form);
                    }, 300);

                    // Clear the image upload flag after a delay
                    setTimeout(function() {
                        var formId = $form.attr('id');
                        formsInImageUpload[formId] = false;
                        $form.data('image-upload-active', false);
                    }, 500);
                }
            });
        });
    });

    /**
     * Save cropped image to database immediately (for details page)
     * @param {number} employeeId - The employee ID
     * @param {string} croppedImageBase64 - The cropped image as base64
     * @param {function} callback - Callback function(success, imageUrl)
     */
    function saveCroppedImageToDatabase(employeeId, croppedImageBase64, callback) {
        if (!employeeId || !croppedImageBase64) {
            if (callback) callback(false, null);
            return;
        }

        // Get the update route from the form or use a default
        var $form = $('#employee_edit_form');
        var updateRoute = $form.data('update-url') || $form.attr('action');

        // If no route found, try to construct it
        if (!updateRoute) {
            // Try to get from form's data attribute or construct from current URL
            var currentPath = window.location.pathname;
            var boxSlug = currentPath.split('/')[1]; // Assuming structure: /box_slug/...
            updateRoute = '/' + boxSlug + '/employee/update';
        }

        // Get CSRF token
        var csrfToken = $('meta[name="csrf-token"]').attr('content') || $form.find('input[name="_token"]').val();

        var formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('_token', csrfToken);
        formData.append('id', employeeId);
        formData.append('cropped_image', croppedImageBase64);

        $.ajax({
            url: updateRoute,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                var imageUrl = null;
                if (response && response.data && response.data.profile_image_url) {
                    imageUrl = response.data.profile_image_url;
                } else if (response && response.profile_image_url) {
                    imageUrl = response.profile_image_url;
                }
                if (callback) callback(true, imageUrl);
            },
            error: function(xhr) {
                console.error('Error saving cropped image:', xhr);
                if (callback) callback(false, null);
            }
        });
    }

    /**
     * Update the profile image on the details page
     * @param {string} imageUrl - The new image URL
     */
    function updateDetailsPageImage(imageUrl) {
        if (!imageUrl) return;

        // Add cache-busting parameter
        var cacheBuster = '?t=' + new Date().getTime();
        var finalUrl = imageUrl + (imageUrl.indexOf('?') === -1 ? cacheBuster : '&t=' + new Date().getTime());

        // Update the main profile image
        var $profileImg = $('#profile_img');
        if ($profileImg.length > 0) {
            $profileImg.attr('src', finalUrl).removeClass('d-none');
            $('#letter_avatar').addClass('d-none');
        }

        // Update the edit form preview image
        // Check for both ID and class selectors
        var $editProfileImg = $('#editprofile_img');
        if ($editProfileImg.length > 0) {
            $editProfileImg.attr('src', finalUrl);
        }

        // Update the uploaded_image container in the form
        var $uploadedImage = $('#uploaded_image');
        if ($uploadedImage.length > 0) {
            // Check if it has an image or icon
            if ($uploadedImage.find('img').length > 0) {
                 $uploadedImage.find('img').attr('src', finalUrl);
            } else {
                 $uploadedImage.html('<img src="' + finalUrl + '" alt="img" class="rounded-circle" id="editprofile_img" />');
            }
        }

        // Also check for class based uploaded_image (from user snippet)
        var $uploadedImageClass = $('.uploaded_image');
        if ($uploadedImageClass.length > 0) {
             $uploadedImageClass.html('<img src="' + finalUrl + '" alt="img" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" id="editprofile_img" />');
        }
    }

    /**
     * Clean up croppie instances when modals are hidden
     * Keep instances for reuse (don't destroy)
     */
    $(document).on('hidden.bs.modal', '.uploadimageModal', function() {
        // Don't destroy, just keep for reuse
        // Instances are stored in croppieInstances object for future use
    });
});

