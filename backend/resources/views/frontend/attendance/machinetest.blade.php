@extends('layouts.main')
@section('content')
@section('title', 'Biometric Machine')



<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Biometric Machine Setup </h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="index.html"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            Attendence
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Biometric Machine Setup </li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                <div class="me-2 mb-2">
                    <div class="dropdown">
                        <a href="javascript:void(0);"
                            class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                            data-bs-toggle="dropdown">
                            <i class="ti ti-file-export me-1"></i>Export
                        </a>
                        <ul class="dropdown-menu  dropdown-menu-end p-3">
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1"><i
                                        class="ti ti-file-type-pdf me-1"></i>Export as PDF </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1"><i
                                        class="ti ti-file-type-xls me-1"></i>Export as Excel </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="mb-2 me-2">
                    <button type="button" id="processBiometricBtn" 
                        class="btn btn-success d-flex align-items-center">
                        <i class="ti ti-refresh me-2"></i>Process Biometric Attendance
                    </button>
                </div>
                <div class="mb-2">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#add_machine"
                        class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add
                        Biometric Machine </a>
                </div>
                <div class="head-icons ms-2">
                    <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-original-title="Collapse" id="collapse-header">
                        <i class="ti ti-chevrons-up"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- /Breadcrumb -->



        <!-- Leaves list -->
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                <h5>List Of Machine </h5>

            </div>
            <div class="card-body">
                <div class="custom-datatable-filter table-responsive">
                    <table class="table" id="biometric-machines-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="no-sort">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox" id="select-all" />
                                    </div>
                                </th>
                                <th>Machine Number</th>
                                <th>Machine Model</th>
                                <th>Machine Serial Number</th>
                                <th>Last Sync</th>
                                <th>Machine Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>

                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="datatable-length"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate"></div>
                    </div>
                </div>


            </div>
        </div>
        <!-- /Leaves list -->

        <!-- Process Biometric Attendance Output -->
        <div class="card mt-3" id="processOutputCard" style="display: none;">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5>Process Biometric Attendance - Output</h5>
                <button type="button" class="btn-close" id="closeOutputCard"></button>
            </div>
            <div class="card-body">
                <div id="processStatus" class="alert" style="display: none;"></div>
                <div id="processOutput" style="max-height: 500px; overflow-y: auto; background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; font-size: 12px;">
                    <div id="outputContent"></div>
                </div>
            </div>
        </div>
        <!-- /Process Biometric Attendance Output -->

    </div>

</div>
<!-- /Page Wrapper -->


<!-- Add Machine -->
<div class="modal fade" id="add_machine">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Punch Machine for User</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <form id="addPunchForm" method="POST">
                @csrf

                <div class="modal-body pb-0">
                    <div class="row">

                        <!-- Select Machine -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Select Machine</label>
                                <select class="form-select" name="machine_id" id="machine_list">
                                    <option disabled selected>Loading machines...</option>
                                </select>
                            </div>
                        </div>

                        <!-- Enter User ID -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Enter User ID</label>
                                <input type="text" class="form-control" name="user_id" id="punch_user_id"
                                    placeholder="Enter User ID" required />
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary me-2">Submit Punch</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                </div>

            </form>

        </div>
    </div>
</div>


<!-- /Add Machine -->


<div class="modal fade" id="edit_machine">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Biometric Machine</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="editBiometric" method="POST">
                @csrf
                <input type="hidden" name="id" id="edit_id" />
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Machine No.</label>
                                <input type="text" class="form-control" name="machine_no" id="edit_machine_no" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Machine Type</label>
                                <select class="select" name="machine_type" id="edit_machine_type">
                                    <option disabled selected>Select Machine Type</option>
                                    <option>T52</option>
                                    <option>T16</option>
                                    <option>T28-OLD</option>
                                    <option>T62</option>
                                    <option>T61</option>
                                    <option>RS20-OLD</option>
                                    <option>RS70-OLD</option>
                                    <option>RS10-OLD</option>
                                    <option>T61N</option>
                                    <option>C121</option>
                                    <option>C101W</option>
                                    <option>T60</option>
                                    <option>S02 CONTROLER(T)</option>
                                    <option>S04 CONTROLER(T)</option>
                                    <option>T302</option>
                                    <option>T304</option>
                                    <option>T304F MINI</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Serial No.</label>
                                <input type="text" class="form-control" name="serial_no" id="edit_serial_no" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Server Port No.</label>
                                <input type="text" class="form-control" name="server_port_no"
                                    id="edit_server_port_no" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Machine Name</label>
                                <input type="text" class="form-control" name="machine_name"
                                    id="edit_machine_name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Select Branch</label>
                                <select class="form-control" name="branch_id" id="edit_branch_id_select">
                                    <option disabled selected>Select Branch</option>
                                    <!-- Options will be loaded dynamically -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <select id="edit_country_id" class="form-select" name="country_id">
                                    <option disabled selected>Loading countries...</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">State</label>
                                <select id="edit_state_id" class="form-select" name="state_id" disabled>
                                    <option disabled selected>Select Country First</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">City / Branch</label>
                                <select id="edit_city_id" class="form-select" name="city_id" disabled>
                                    <option disabled selected>Select State First</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Time Zone</label>
                                <select class="select" name="timezone" id="edit_timezone">
                                    <option disabled selected>Select Time Zone</option>

                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Access Control</label>
                                <select class="select" name="access_control" id="edit_access_control">
                                    <option disabled selected>Select Access Control</option>
                                    <option value="In">In</option>
                                    <option value="Out">Out</option>
                                    <option value="In/Out">In/Out</option>
                                </select>
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status </label>
                                <select class="select" name="status" id="status">
                                    <option disabled selected>Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary me-2">Save</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- /Edit Leaves -->

<!-- Delete Modal -->
<div class="modal fade" id="delete_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1">Confirm Delete</h4>
                <p class="mb-3">Are you sure you want to delete this item? This action cannot be undone.</p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div id="dynamicToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="dynamicToastMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>

<style>
    /* green blinking indicator */
    .green-blink {
        height: 12px;
        width: 12px;
        background-color: #28a745;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
        animation: greenBlink 1s infinite;
    }

    @keyframes greenBlink {
        0% {
            opacity: 0.2;
        }

        50% {
            opacity: 1;
        }

        100% {
            opacity: 0.2;
        }
    }

    /* solid red alert indicator */
    .red-alert {
        height: 12px;
        width: 12px;
        background-color: #dc3545;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
        box-shadow: 0px 0px 6px rgba(255, 0, 0, 0.6);
    }
</style>



@push('scripts')
<script>
$(document).ready(function () {

    /* Toast */
    const showToast = (msg, type = "success") => {
        const $toast = $("#dynamicToast");
        $("#dynamicToastMessage").text(msg);

        $toast.removeClass("bg-success bg-danger")
              .addClass(type === "success" ? "bg-success" : "bg-danger");

        new bootstrap.Toast($toast[0]).show();
    };

    /* Load Active Machines in Select */
    const loadMachines = () => {
    $.ajax({
        url: "{{ route('get.all.machine', ['box_slug' => request()->route('box_slug')]) }}",
        type: "GET",
        success: res => {
            const select = $("#machine_list");
            select.empty().append(`<option disabled selected>Select Machine</option>`);
            console.log('res',res.data);
            res.data.forEach(m => {
                select.append(`
                    <option value="${m.serial_no}">
                        ${m.machine_name} - ${m.serial_no}
                    </option>
                `);
            });
        },
        error: () => showToast("Failed to load machines", "error")
    });
};


    /* When punch modal opens — load machines */
    $("#add_machine").on("show.bs.modal", function () {
        loadMachines();
    });
    $("#addPunchForm").submit(function (e) {
        e.preventDefault();
        let form = $(this);

        $.ajax({
            url: "{{ route('machine.punch.user', ['box_slug' => request()->route('box_slug')]) }}",
            type: "POST",
            data: form.serialize(),
            beforeSend: () => form.find("button[type=submit]").prop("disabled", true),
            success: res => {
                $("#add_machine").modal("hide");
                form[0].reset();
                showToast("Punch submitted successfully");
            },
            error: xhr => {
                showToast("Punch submission failed", "error");
            },

            complete: () => form.find("button[type=submit]").prop("disabled", false)
        });
    });

    // Process Biometric Attendance
    $("#processBiometricBtn").click(function() {
        const btn = $(this);
        const outputCard = $("#processOutputCard");
        const outputContent = $("#outputContent");
        const processStatus = $("#processStatus");
        
        // Show output card
        outputCard.show();
        outputContent.html('<div class="text-center"><i class="ti ti-loader spin"></i> Processing...</div>');
        processStatus.hide();
        btn.prop("disabled", true).html('<i class="ti ti-loader spin me-2"></i>Processing...');

        $.ajax({
            url: "{{ route('process.biometric.attendance', ['box_slug' => request()->route('box_slug')]) }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                btn.prop("disabled", false).html('<i class="ti ti-refresh me-2"></i>Process Biometric Attendance');
                
                if (response.success) {
                    processStatus.removeClass("alert-danger").addClass("alert-success")
                        .html('<strong>Success!</strong> ' + response.message).show();
                    
                    let outputHtml = '';
                    
                    // Summary Section
                    if (response.summary) {
                        outputHtml += '<div class="mb-4 p-3 bg-light rounded">';
                        outputHtml += '<h6 class="mb-3"><strong>📊 Processing Summary</strong></h6>';
                        outputHtml += '<div class="row">';
                        if (response.summary.machines_processed > 0) {
                            outputHtml += '<div class="col-md-6 mb-2"><strong>Machines Processed:</strong> ' + response.summary.machines_processed + '</div>';
                        }
                        if (response.summary.logs_found > 0) {
                            outputHtml += '<div class="col-md-6 mb-2"><strong>Logs Found:</strong> ' + response.summary.logs_found + '</div>';
                        }
                        if (response.summary.user_combinations > 0) {
                            outputHtml += '<div class="col-md-6 mb-2"><strong>User-Date Combinations:</strong> ' + response.summary.user_combinations + '</div>';
                        }
                        if (response.summary.combinations_processed > 0) {
                            outputHtml += '<div class="col-md-6 mb-2"><strong>Combinations Processed:</strong> ' + response.summary.combinations_processed + '</div>';
                        }
                        if (response.summary.machines_updated > 0) {
                            outputHtml += '<div class="col-md-6 mb-2"><strong>Machines Updated:</strong> ' + response.summary.machines_updated + '</div>';
                        }
                        outputHtml += '</div>';
                        outputHtml += '</div>';
                    }
                    
                    outputHtml += '<div class="mb-2"><strong>Exit Code:</strong> ' + response.exit_code + '</div>';
                    outputHtml += '<div class="mb-2"><strong>Output Lines:</strong> ' + (response.output_count || 0) + '</div>';
                    
                    // Console Output Section
                    if (response.output_lines && response.output_lines.length > 0) {
                        outputHtml += '<div class="mb-3 mt-3"><strong>Console Output:</strong></div>';
                        outputHtml += '<div class="console-output bg-dark text-light p-3 rounded" style="font-family: monospace; font-size: 12px; max-height: 400px; overflow-y: auto;">';
                        response.output_lines.forEach(function(line) {
                            let lineClass = '';
                            if (line.includes('Error') || line.includes('❌') || line.includes('⚠️')) {
                                lineClass = 'text-danger';
                            } else if (line.includes('Success') || line.includes('✅') || line.includes('✔')) {
                                lineClass = 'text-success';
                            } else if (line.includes('Warning') || line.includes('⚠')) {
                                lineClass = 'text-warning';
                            } else if (line.includes('📊') || line.includes('📱') || line.includes('👥')) {
                                lineClass = 'text-info';
                            } else if (line.includes('📌') || line.includes('🔄') || line.includes('⚙️')) {
                                lineClass = 'text-primary';
                            }
                            outputHtml += '<div class="' + lineClass + ' mb-1">' + escapeHtml(line) + '</div>';
                        });
                        outputHtml += '</div>';
                    } else if (response.output) {
                        outputHtml += '<div class="mb-3 mt-3"><strong>Console Output:</strong></div>';
                        outputHtml += '<pre class="bg-dark text-light p-3 rounded" style="font-family: monospace; font-size: 12px; max-height: 400px; overflow-y: auto;">' + escapeHtml(response.output) + '</pre>';
                    } else {
                        outputHtml += '<div class="text-muted">No output generated.</div>';
                    }
                    
                    outputContent.html(outputHtml);
                } else {
                    processStatus.removeClass("alert-success").addClass("alert-danger")
                        .html('<strong>Error!</strong> ' + response.message).show();
                    outputContent.html('<div class="text-danger">' + escapeHtml(response.error || 'Unknown error') + '</div>');
                }
            },
            error: function(xhr) {
                btn.prop("disabled", false).html('<i class="ti ti-refresh me-2"></i>Process Biometric Attendance');
                processStatus.removeClass("alert-success").addClass("alert-danger")
                    .html('<strong>Error!</strong> Failed to process biometric attendance.').show();
                
                let errorMsg = 'Unknown error occurred';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                outputContent.html('<div class="text-danger">' + escapeHtml(errorMsg) + '</div>');
            }
        });
    });

    // Close output card
    $("#closeOutputCard").click(function() {
        $("#processOutputCard").hide();
    });

    // Helper function to escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

});
</script>
@endpush


@endsection
