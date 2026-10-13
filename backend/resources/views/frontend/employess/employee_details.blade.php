@extends('layouts.main')
@section('content')
@section('title', 'Employee Details')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h6 class="fw-medium d-inline-flex align-items-center mb-3 mb-sm-0"><a
                        href="{{ box_route('manage_users.employee_list') }}">
                        <i class="ti ti-arrow-left me-2"></i>Employee Details </a> <span class="ms-2">Employee ID : <span id="details_header_employee_id"></span></span>
                </h6>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                <div class="mb-2 me-2">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#add_bank_satutory"
                        class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Bank &
                        Statutory </a>
                </div>
                <div class="mb-2">
                    <a href="{{ route('forget.password') }}" class="btn btn-primary d-flex align-items-center"><i
                            class="ti ti-circle-plus me-2"></i>Change
                        Password </a>
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

        <div class="row">
            <div class="col-xl-4 theiaStickySidebar">
                <div class="card card-bg-1">
                    <div class="card-body p-0">
                        <span
                            class="avatar avatar-xl avatar-rounded border border-2 border-white m-auto d-flex mb-2 position-relative overflow-hidden">
                            <!-- Profile Image -->
                            <img id="profile_img" class="w-100 h-100 object-fit-cover" alt="Profile Image"
                                onerror="this.classList.add('d-none'); document.getElementById('letter_avatar').classList.remove('d-none');" />

                            <!-- Fallback Letter Avatar -->
                            <div id="letter_avatar"
                                class="position-absolute top-0 start-0 w-100 h-100 rounded-circle d-none justify-content-center align-items-center text-white fw-bold"
                                style="font-size: 24px;">
                                <span id="avatar_letter"></span>
                            </div>
                        </span>


                        <div class="text-center px-3 pb-3 border-bottom">
                            <div class="mb-3">
                                <h5 class="d-flex align-items-center justify-content-center mb-1" id="emp-name"> <i
                                        class="ti ti-discount-check-filled text-success ms-1"></i></h5>
                                <span class="badge badge-soft-dark fw-medium me-2" id="emp-department">
                                    <i class="ti ti-point-filled me-1"></i>Software Developer
                                </span>
                                <span class="badge badge-soft-secondary fw-medium" id="emp-exp">10+ years of
                                    Experience </span>
                            </div>
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ti ti-id me-2"></i>
                                        Employee ID </span>
                                    <p class="text-dark" id="emp-id"></p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2" id="department_display_section">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ti ti-id me-2"></i>
                                        Department </span>
                                    <p class="text-dark" id="department_new">.. </p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2" id="designation_display_section">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ti ti-id me-2"></i>
                                        Designation </span>
                                    <p class="text-dark" id="designation_new">... </p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ti ti-user-check me-2"></i>
                                        Employee Type </span>
                                    <p class="text-dark" id="emp-type-display">-</p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2" id="reporting_manager_section">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ti ti-id me-2"></i>
                                        Reporting Manager </span>
                                    <p class="text-dark" id="reporting_manager_display">-</p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-inline-flex align-items-center">

                                        <i class="ti ti-star me-2"></i>
                                        Team
                                    </span>
                                    <button type="button" class="btn btn-light text-dark" id="emp-designation"
                                        data-bs-toggle="modal" data-bs-target="#TeamsModel"
                                        data-designation-id="department_id">

                                    </button>




                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ti ti-calendar-check me-2"></i>
                                        Date Of Join
                                    </span>
                                    <p class="text-dark" id="emp-joining"></p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ti ti-calendar-check me-2"></i>
                                        Report Office
                                    </span>
                                    <div class="d-flex align-items-center">
                                        <p class="text-gray-9 mb-0" id="report_office_new"> </p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ti ti-calendar-check me-2"></i>
                                        Working Mode
                                    </span>
                                    <p class="text-dark" id="workignMode_emp"> </p>
                                </div>


                                <div class="row gx-2 mt-3">
                                    <div class="col-6">
                                        <div>
                                            <a href="#" class="btn btn-dark w-100" data-bs-toggle="modal"
                                                data-bs-target="#edit_employee"><i class="ti ti-edit me-1"></i>Edit Info
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div>
                                            <a href="chat.html" class="btn btn-primary w-100"><i
                                                    class="ti ti-message-heart me-1"></i>Message </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 border-bottom">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6>Basic information </h6>
                                <a href="javascript:void(0);" class="btn btn-icon btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#edit_employee"><i class="ti ti-edit"></i></a>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="d-inline-flex align-items-center">
                                    <i class="ti ti-phone me-2"></i>
                                    Phone
                                </span>
                                <p class="text-dark" id="emp-phone">(163) 2459 315 </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="d-inline-flex align-items-center">
                                    <i class="ti ti-mail-check me-2"></i>
                                    Email
                                </span>
                                <a href="javascript:void(0);" class="text-info d-inline-flex align-items-center"><span
                                        class="__cf_email__"
                                        data-cfemail="d2a2b7a0a0b3bea6e3e092b7aab3bfa2beb7fcb1bdbf" id="emp-email">
                                    </span><i class="ti ti-copy text-dark ms-2"></i></a>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="d-inline-flex align-items-center">
                                    <i class="ti ti-gender-male me-2"></i>
                                    Gender
                                </span>
                                <p class="text-dark text-end" id="emp-gender">Male </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="d-inline-flex align-items-center">
                                    <i class="ti ti-cake me-2"></i>
                                    Birdthday
                                </span>
                                <p class="text-dark text-end" id="emp-birthday">24th July 2000 </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="d-inline-flex align-items-center">
                                    <i class="ti ti-map-pin-check me-2"></i>
                                    Address
                                </span>
                                <p class="text-dark text-end" id="emp-address">1861 Bayonne Ave, <br /> Manchester,
                                    NJ, 08759 </p>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="d-inline-flex align-items-center">
                                    <i class="ti ti-calendar-check me-2"></i>
                                    Date of Resignation
                                </span>
                                <p class="text-dark text-end" id="emp-resignation"> </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="d-inline-flex align-items-center">
                                    <i class="ti ti-id me-2"></i>
                                    ESI No.
                                </span>
                                <p class="text-dark text-end" id="emp-esi-data"> </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="d-inline-flex align-items-center">
                                    <i class="ti ti-id me-2"></i>
                                    PF No.
                                </span>
                                <p class="text-dark text-end" id="emp-pf-data"> </p>
                            </div>





                        </div>
                        <div class="p-3 border-bottom">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6>Personal Information </h6>
                                <a href="javascript:void(0);" class="btn btn-icon btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#edit_personal"><i class="ti ti-edit"></i></a>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="d-inline-flex align-items-center">
                                    <i class="ti ti-e-passport me-2"></i>
                                    Passport No
                                </span>
                                <p class="text-dark" id="emp-passport">QRET4566FGRT</p>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="d-inline-flex align-items-center">
                                    <i class="ti ti-calendar-x me-2"></i>
                                    Passport Exp Date
                                </span>
                                <p class="text-dark text-end" id="emp-passport-exp">15 May 2029</p>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="d-inline-flex align-items-center">
                                    <i class="ti ti-gender-male me-2"></i>
                                    Nationality
                                </span>
                                <p class="text-dark text-end" id="emp-nationality">Indian</p>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="d-inline-flex align-items-center">
                                    <i class="ti ti-bookmark-plus me-2"></i>
                                    Religion
                                </span>
                                <p class="text-dark text-end" id="emp-religion">Christianity</p>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="d-inline-flex align-items-center">
                                    <i class="ti ti-hotel-service me-2"></i>
                                    Marital status
                                </span>
                                <p class="text-dark text-end" id="emp-marital">Yes</p>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="d-inline-flex align-items-center">
                                    <i class="ti ti-briefcase-2 me-2"></i>
                                    Anniversary Date
                                </span>
                                <p class="text-dark text-end" id="emp-spouse"></p>
                            </div>

                            <div class="d-flex align-items-center justify-content-between">
                                <span class="d-inline-flex align-items-center">
                                    <i class="ti ti-baby-bottle me-2"></i>
                                    No. of children
                                </span>
                                <p class="text-dark text-end" id="emp-children"></p>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6>Emergency Contact Number </h6>
                    <a href="javascript:void(0);" class="btn btn-icon btn-sm" data-bs-toggle="modal"
                        data-bs-target="#edit_emergency"><i class="ti ti-edit"></i></a>
                </div>
                <div class="card">
                    <div class="card-body p-0" id="emergency-list">

                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div>
                    <div class="tab-content custom-accordion-items">
                        <div class="tab-pane active show" id="bottom-justified-tab1" role="tabpanel">
                            <div class="accordion accordions-items-seperate" id="accordionExample">
                                <div class="accordion-item">
                                    <div class="accordion-header" id="headingOne">
                                        <div class="accordion-button">
                                            <div class="d-flex align-items-center flex-fill">
                                                <h5>About Employee </h5>
                                                <a href="#" class="btn btn-sm btn-icon ms-auto"
                                                    data-bs-toggle="modal" data-bs-target="#edit_employee"><i
                                                        class="ti ti-edit"></i></a>
                                                <a href="#"
                                                    class="d-flex align-items-center collapsed collapse-arrow"
                                                    data-bs-toggle="collapse" data-bs-target="#primaryBorderOne"
                                                    aria-expanded="false" aria-controls="primaryBorderOne">
                                                    <i class="ti ti-chevron-down fs-18"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="primaryBorderOne" class="accordion-collapse collapse show border-top"
                                        aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body mt-2" id="emp-about">

                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-header" id="headingOne1">
                                        <div class="accordion-button">
                                            <div class="d-flex align-items-center flex-fill">
                                                <h5>Assign Employee Shift </h5>
                                                <a href="#" class="btn btn-sm btn-icon ms-auto"
                                                    data-bs-toggle="modal" data-bs-target="#assign_shift_employee"><i
                                                        class="ti ti-edit"></i></a>
                                                <a href="#"
                                                    class="d-flex align-items-center collapsed collapse-arrow"
                                                    data-bs-toggle="collapse" data-bs-target="#primaryBorderOne1"
                                                    aria-expanded="false" aria-controls="primaryBorderOne1">
                                                    <i class="ti ti-chevron-down fs-18"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="primaryBorderOne1" class="accordion-collapse collapse show border-top"
                                        aria-labelledby="headingOne1" data-bs-parent="#accordionExample12">
                                        <div class="accordion-body mt-2" id="emp-assign">
                                            <input type="hidden" id="emp-assign-id" value="" name="id">
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-header" id="headingTwo">
                                        <div class="accordion-button">
                                            <div class="d-flex align-items-center flex-fill">
                                                <h5>Bank Information </h5>
                                                <a href="#" class="btn btn-sm btn-icon ms-auto"
                                                    data-bs-toggle="modal" data-bs-target="#edit_bank"><i
                                                        class="ti ti-edit"></i></a>
                                                <a href="#"
                                                    class="d-flex align-items-center collapsed collapse-arrow"
                                                    data-bs-toggle="collapse" data-bs-target="#primaryBorderTwo"
                                                    aria-expanded="false" aria-controls="primaryBorderTwo">
                                                    <i class="ti ti-chevron-down fs-18"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="primaryBorderTwo" class="accordion-collapse collapse border-top"
                                        aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                        <div class="accordion-body" id="bank-accordion-container">
                                            {{-- Dynamically Added here --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-header" id="headingThree">
                                        <div class="accordion-button">
                                            <div class="d-flex align-items-center justify-content-between flex-fill">
                                                <h5>Family Information </h5>
                                                <div class="d-flex">
                                                    <a href="#" class="btn btn-icon btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#edit_familyinformation"><i
                                                            class="ti ti-edit"></i></a>
                                                    <a href="#"
                                                        class="d-flex align-items-center collapsed collapse-arrow"
                                                        data-bs-toggle="collapse" data-bs-target="#primaryBorderThree"
                                                        aria-expanded="false" aria-controls="primaryBorderThree">
                                                        <i class="ti ti-chevron-down fs-18"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="primaryBorderThree" class="accordion-collapse collapse border-top"
                                        aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                        <div class="accordion-body" id="family-list">

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="accordion-item">
                                            <div class="row">
                                                <div class="accordion-header" id="headingFour">
                                                    <div class="accordion-button">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-fill">
                                                            <h5>Education Details </h5>
                                                            <div class="d-flex">
                                                                <a href="#" class="btn btn-icon btn-sm"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#edit_education"><i
                                                                        class="ti ti-edit"></i></a>
                                                                <a href="#"
                                                                    class="d-flex align-items-center collapsed collapse-arrow"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#primaryBorderFour"
                                                                    aria-expanded="false"
                                                                    aria-controls="primaryBorderFour">
                                                                    <i class="ti ti-chevron-down fs-18"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="primaryBorderFour"
                                                    class="accordion-collapse collapse border-top"
                                                    aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body" id="education-list">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="accordion-item">
                                            <div class="row">
                                                <div class="accordion-header" id="headingFive">
                                                    <div class="accordion-button collapsed">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-fill">
                                                            <h5>Experience </h5>
                                                            <div class="d-flex">
                                                                <a href="#" class="btn btn-icon btn-sm"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#edit_experience"><i
                                                                        class="ti ti-edit"></i></a>
                                                                <a href="#"
                                                                    class="d-flex align-items-center collapsed collapse-arrow"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#primaryBorderFive"
                                                                    aria-expanded="false"
                                                                    aria-controls="primaryBorderFive">
                                                                    <i class="ti ti-chevron-down fs-18"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="primaryBorderFive"
                                                    class="accordion-collapse collapse border-top"
                                                    aria-labelledby="headingFive" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body" id="experience-list">
                                                        <!-- multiple hard-coded experience blocks -->
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-6">

                                        <div class="accordion-item">
                                            <div class="accordion-header" id="headingSeven">
                                                <div class="accordion-button">
                                                    <div class="d-flex align-items-center flex-fill">
                                                        <h5>Location Tracking </h5>
                                                        <div class="form-check form-switch ms-auto me-3 mt-1">
                                                            <input class="form-check-input" type="checkbox" id="locationTrackingToggle" onchange="toggleLocationTracking(this)">
                                                            <label class="form-check-label mb-0" for="locationTrackingToggle" style="font-size: 14px; font-weight: normal;">Enable Tracking</label>
                                                        </div>
                                                        <a href="#"
                                                            class="d-flex align-items-center collapsed collapse-arrow"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#primaryBorderSeven" aria-expanded="false"
                                                            aria-controls="primaryBorderSeven">
                                                            <i class="ti ti-chevron-down fs-18"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="primaryBorderSeven"
                                                class="accordion-collapse collapse border-top"
                                                aria-labelledby="headingSeven" data-bs-parent="#accordionExample">
                                                <div class="accordion-body mt-2">
                                                    <div id="employeeTrackingMap" style="width: 100%; height: 300px; border-radius: 4px; background-color: #f8f9fa;" class="d-flex align-items-center justify-content-center border">
                                                        <span class="text-muted">Loading map...</span>
                                                    </div>

                                                    <h4 class="mt-3" id="tracking-emp-name"></h4>
                                                    <div class="table-responsive mt-3">
                                                        <table class="table table-bordered mb-0">
                                                            <thead>
                                                                <tr>

                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td><strong>Date</strong></td>
                                                                    <td id="tracking-metric-date">-</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Check-in Time :</strong></td>
                                                                    <td id="tracking-metric-check-in">-</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Check-out Time :</strong></td>
                                                                    <td id="tracking-metric-check-out">-</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Total Time :</strong></td>
                                                                    <td id="tracking-metric-total-time">-</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Rest :</strong></td>
                                                                    <td id="tracking-metric-rest">-</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Mobility Time :</strong></td>
                                                                    <td id="tracking-metric-mobility">-</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    {{-- <div class="col-md-6">
                                        <div class="accordion-item">
                                            <div class="accordion-header" id="headingEight">
                                                <div class="accordion-button">
                                                    <div class="d-flex align-items-center flex-fill">
                                                        <h5>Biometric Maping </h5>
                                                        <a href="#" class="btn btn-sm btn-icon ms-auto edit-bio-metric"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#biometric_maping"><i
                                                                class="ti ti-edit"></i></a>
                                                        <a href="#"
                                                            class="d-flex align-items-center collapsed collapse-arrow"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#primaryBorderEight" aria-expanded="false"
                                                            aria-controls="primaryBorderSeven">
                                                            <i class="ti ti-chevron-down fs-18"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="primaryBorderEight"
                                                class="accordion-collapse collapse border-top"
                                                aria-labelledby="headingEight" data-bs-parent="#accordionExample">
                                                <div class="accordion-body mt-2 p-0">
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="col-md-6">
                                        <div class="accordion-item">
                                            <div class="accordion-header" id="headingEight">
                                                <div class="accordion-button">
                                                    <div class="d-flex align-items-center flex-fill">
                                                        <h5>Biometric Mapping </h5>
                                                        <a href="#"
                                                            class="btn btn-sm btn-icon ms-auto edit-bio-metric"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#biometric_maping"><i
                                                                class="ti ti-edit"></i></a>
                                                        <a href="#"
                                                            class="d-flex align-items-center collapsed collapse-arrow"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#primaryBorderEight" aria-expanded="false"
                                                            aria-controls="primaryBorderSeven">
                                                            <i class="ti ti-chevron-down fs-18"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="primaryBorderEight"
                                                class="accordion-collapse collapse border-top"
                                                aria-labelledby="headingEight" data-bs-parent="#accordionExample">
                                                <div class="accordion-body mt-2 p-0">
                                                    <div id="biometric-list"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="accordion-item">
                                            <div class="accordion-header" id="headingNine">
                                                <div class="accordion-button">
                                                    <div class="d-flex align-items-center flex-fill">
                                                        <h5>Working Mode </h5>
                                                        <a href="#"
                                                            class="btn btn-sm btn-icon ms-auto edit-working-mode"
                                                            data-id="123" data-bs-toggle="modal"
                                                            data-bs-target="#working_mode">
                                                            <i class="ti ti-edit"></i>
                                                        </a>

                                                        <a href="#"
                                                            class="d-flex align-items-center collapsed collapse-arrow"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#primaryBorderNine" aria-expanded="false"
                                                            aria-controls="primaryBorderNine">
                                                            <i class="ti ti-chevron-down fs-18"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="primaryBorderNine" class="accordion-collapse collapse border-top"
                                                aria-labelledby="headingNine" data-bs-parent="#accordionExample">
                                                <div class="accordion-body mt-2 p-3">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-3 col-sm-6 mb-2">
                                                            <p class="mb-1 fw-bold">Work Mode:</p>
                                                            <p id="workingMode" class="mb-0"></p>
                                                        </div>
                                                        <div class="col-md-9 col-sm-6 mb-2">
                                                            <p class="mb-1 fw-bold">Web Working Days Selected:</p>
                                                            <div id="working_web_days" class="d-flex flex-wrap gap-2">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>




                                <div class="row">
                                    <div class="col-md-6">

                                        <div class="accordion-item">
                                            <div class="accordion-header" id="headingTen">
                                                <div class="accordion-button">
                                                    <div class="d-flex align-items-center flex-fill">
                                                        <h5>Assigned Assets </h5>
                                                        <a href="javascript:void(0);"
                                                            class="btn btn-sm btn-icon ms-auto btn-open-assign-modal"
                                                             data-bs-toggle="modal"
                                                            data-bs-target="#modal_assign_asset">
                                                            <i class="ti ti-plus"></i>
                                                        </a>

                                                        <a href="#"
                                                            class="d-flex align-items-center collapsed collapse-arrow"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#primaryBorderTen" aria-expanded="false"
                                                            aria-controls="primaryBorderTen">
                                                            <i class="ti ti-chevron-down fs-18"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="primaryBorderTen" class="accordion-collapse collapse border-top"
                                                aria-labelledby="headingTen" data-bs-parent="#accordionExample">
                                                <div class="accordion-body mt-2 p-3">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-bordered text-nowrap mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Asset Name</th>
                                                                    <th>Assigned Date</th>
                                                                    <th>Returned Date</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="accordion-asset-list">
                                                                <!-- Dynamically populated -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">

                                        <div class="accordion-item">
                                            <div class="accordion-header" id="headingEleven">
                                                <div class="accordion-button">
                                                    <div class="d-flex align-items-center flex-fill">
                                                        <h5>Loan Availed </h5>
                                                        <a href="#"
                                                            class="btn btn-sm btn-icon ms-auto"
                                                             data-bs-toggle="modal"
                                                            data-bs-target="#">
                                                            <i class="ti ti-edit"></i>
                                                        </a>

                                                        <a href="#"
                                                            class="d-flex align-items-center collapsed collapse-arrow"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#primaryBorderEleven" aria-expanded="false"
                                                            aria-controls="primaryBorderEleven">
                                                            <i class="ti ti-chevron-down fs-18"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="primaryBorderEleven" class="accordion-collapse collapse border-top"
                                                aria-labelledby="headingEleven" data-bs-parent="#accordionExample">
                                                <div class="accordion-body mt-2 p-3">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>






                                <div class="card">
                                    <div class="card-body">
                                        <div class="contact-grids-tab p-0 mb-3">
                                            <ul class="nav nav-underline" id="myTab" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="info-tab2"
                                                        data-bs-toggle="tab" data-bs-target="#basic-info2"
                                                        type="button" role="tab" aria-selected="true">Projects
                                                    </button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="address-tab2" data-bs-toggle="tab"
                                                        data-bs-target="#address2" type="button" role="tab"
                                                        aria-selected="false">Assets </button>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="tab-content" id="myTabContent3">
                                            <div class="tab-pane fade show active" id="basic-info2" role="tabpanel"
                                                aria-labelledby="info-tab2" tabindex="0">
                                                <div class="row">
                                                    <div class="col-md-6 d-flex">
                                                        <div class="card flex-fill mb-4 mb-md-0">
                                                            <div class="card-body">
                                                                <div
                                                                    class="d-flex align-items-center pb-3 mb-3 border-bottom">
                                                                    <a href="project-details.html"
                                                                        class="flex-shrink-0 me-2">
                                                                        <img src="{{ asset('assets/img/social/project-03.svg') }}"
                                                                            alt="Img" />
                                                                    </a>
                                                                    <div>
                                                                        <h6 class="mb-1"><a
                                                                                href="project-details.html">World
                                                                                Health </a></h6>
                                                                        <div class="d-flex align-items-center">
                                                                            <p class="mb-0 fs-13">8 tasks </p>
                                                                            <p class="fs-13"><span class="mx-1"><i
                                                                                        class="ti ti-point-filled text-primary"></i></span>15
                                                                                Completed </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div>
                                                                            <span class="mb-1 d-block">Deadline </span>
                                                                            <p class="text-dark">31 July 2025 </p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div>
                                                                            <span class="mb-1 d-block">Project Lead
                                                                            </span>
                                                                            <a href="#"
                                                                                class="fw-normal d-flex align-items-center">
                                                                                <img class="avatar avatar-sm rounded-circle me-2"
                                                                                    src="{{ asset('assets/img/profiles/avatar-01.jpg') }}"
                                                                                    alt="Img" />
                                                                                Leona
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 d-flex">
                                                        <div class="card flex-fill mb-0">
                                                            <div class="card-body">
                                                                <div
                                                                    class="d-flex align-items-center pb-3 mb-3 border-bottom">
                                                                    <a href="project-details.html"
                                                                        class="flex-shrink-0 me-2">
                                                                        <img src="{{ asset('assets/img/social/project-01.svg') }}"
                                                                            alt="Img" />
                                                                    </a>
                                                                    <div>
                                                                        <h6 class="mb-1 text-truncate"><a
                                                                                href="project-details.html">Hospital
                                                                                Administration </a></h6>
                                                                        <div class="d-flex align-items-center">
                                                                            <p class="mb-0 fs-13">8 tasks </p>
                                                                            <p class="fs-13"><span class="mx-1"><i
                                                                                        class="ti ti-point-filled text-primary"></i></span>15
                                                                                Completed </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div>
                                                                            <span class="mb-1 d-block">Deadline </span>
                                                                            <p class="text-dark">31 July 2025 </p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div>
                                                                            <span class="mb-1 d-block">Project Lead
                                                                            </span>
                                                                            <a href="#"
                                                                                class="fw-normal d-flex align-items-center">
                                                                                <img class="avatar avatar-sm rounded-circle me-2"
                                                                                    src="{{ asset('assets/img/profiles/avatar-01.jpg') }}"
                                                                                    alt="Img" />
                                                                                Leona
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="address2" role="tabpanel"
                                                aria-labelledby="address-tab2" tabindex="0">
                                                <div id="tab-asset-list">
                                                    <!-- Dynamically populated -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
<!-- /Page Wrapper -->
{{-- Team Department --}}

<div class="modal fade" id="TeamsModel" tabindex="-1" aria-labelledby="yourModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="yourModalLabel">Edit Designation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <input type="hidden" id="department_id" class="form-control">

                <!-- Modal content like form inputs here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>



{{--
<div class="modal fade" id="biometric_maping">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Biometric Maping </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="biometric_maping_add" method="POST">
                @csrf
                <div class="modal-body pb-0">
                    <input type="hidden" name="bank_id" id="bank-id-hidden">

                    <div class="row">
                        <div class="d-flex justify-content-between mb-2">
                            <label class="form-label mb-0"></label>
                            <a href="#" id="toggleSalarySettingBtn" class="text-primary"
                                data-bs-toggle="modal" data-bs-target="#add_fields">
                                <i class="ti ti-plus me-1"></i>Add New
                            </a>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <input type="text" class="form-control" id="machine-user-id"
                                    name="machine_user_id" placeholder="Machine User ID" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <select class="select" id="biometric_id" name="biometric_machine_id">
                                    <option disabled selected>-Machine Serial No.-</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2" data-bs-dismiss="modal">Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">Save </button>
                </div>
            </form>
        </div>
    </div>
</div> --}}
<div class="modal fade" id="biometric_maping">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Biometric Maping </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="biometric_maping_add" method="POST">
                @csrf
                <div class="modal-body pb-0">
                    <input type="hidden" name="id" id="biometric-id-hidden">

                    <div class="row">
                        <div class="d-flex justify-content-between mb-2">
                            <label class="form-label mb-0"></label>
                            <a href="#" id="toggleSalarySettingBtn" class="text-primary"
                                data-bs-toggle="modal" data-bs-target="#add_fields">
                                <i class="ti ti-plus me-1"></i>Add New
                            </a>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <input type="text" class="form-control" id="machine-user-id"
                                    name="machine_user_id" placeholder="Machine User ID" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <select class="select" id="biometric_id" name="biometric_machine_id">
                                    <option disabled selected>-Machine Serial No.-</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2" data-bs-dismiss="modal">Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">Save </button>
                </div>
            </form>
        </div>
    </div>
</div>



<div class="modal fade" id="working_mode">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Working Mode </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="working_mode_submit" method="POST">
                @csrf
                <input type="hidden" name="bank_id" id="bank-id-hidden">
                <div class="modal-body pb-0">
                    <div class="row">

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Working Mode </label>
                                <select class="select" name="work_mode_id" id="working-mode-select">
                                    <option disabled selected>-Working Mode-</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Web Punch In Allowed </label>
                            <div class="mb-3">
                                <div class="d-flex flex-wrap">
                                    <div class="me-4">
                                        <input class="form-check-input" type="checkbox" name="working_web_days[]"
                                            value="Monday" id="mon">
                                        <label for="mon"> Mon</label>
                                    </div>
                                    <div class="me-4">
                                        <input class="form-check-input" type="checkbox" name="working_web_days[]"
                                            value="Tuesday" id="tue">
                                        <label for="tue"> Tue</label>
                                    </div>
                                    <div class="me-4">
                                        <input class="form-check-input" type="checkbox" name="working_web_days[]"
                                            value="Wednesday" id="wed">
                                        <label for="wed"> Wed</label>
                                    </div>
                                    <div class="me-4">
                                        <input class="form-check-input" type="checkbox" name="working_web_days[]"
                                            value="Thursday" id="thu">
                                        <label for="thu"> Thu</label>
                                    </div>
                                    <div class="me-4">
                                        <input class="form-check-input" type="checkbox" name="working_web_days[]"
                                            value="Friday" id="fri">
                                        <label for="fri"> Fri</label>
                                    </div>
                                    <div class="me-4">
                                        <input class="form-check-input" type="checkbox" name="working_web_days[]"
                                            value="Saturday" id="sat">
                                        <label for="sat"> Sat</label>
                                    </div>
                                    <div class="me-4">
                                        <input class="form-check-input" type="checkbox" name="working_web_days[]"
                                            value="Sunday" id="sun">
                                        <label for="sun"> Sun</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Edit Employee -->
<div class="modal fade" id="edit_employee">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <h4 class="modal-title me-2">Edit Employee </h4><span>Employee ID : <span id="edit_employee_details_header_id"></span> </span>
                </div>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="employee_edit_form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="id" id="edit_employee_details_id">
                <input type="hidden" name="cropped_image" id="edit_details_cropped_image" value="">
                <div class="contact-grids-tab">
                    <ul class="nav nav-underline" id="myTab2" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="info-tab3" data-bs-toggle="tab"
                                data-bs-target="#basic-info3" type="button" role="tab"
                                aria-selected="true">Basic Information </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="address-tab3" data-bs-toggle="tab"
                                data-bs-target="#address3" type="button" role="tab"
                                aria-selected="false">Permissions </button>
                        </li>
                    </ul>
                </div>
                <div class="tab-content" id="myTabContent2">
                    <div class="tab-pane fade show active" id="basic-info3" role="tabpanel"
                        aria-labelledby="info-tab3" tabindex="0">
                        <div class="modal-body pb-0 ">
                            <div class="row">
                                <div class="col-md-12">
                                    <div
                                        class="d-flex align-items-center flex-wrap row-gap-3 bg-light w-100 rounded p-3 mb-4">
                                        <div
                                            class="d-flex align-items-center justify-content-center avatar avatar-xxl rounded-circle border border-dashed me-2 flex-shrink-0 text-dark frames">
                                            <div class="uploaded_image"><i class="ti ti-photo text-gray-2 fs-16"></i></div>
                                        </div>
                                        <div class="profile-upload my-crop">
                                            <div class="mb-2">
                                                <h6 class="mb-1">Upload Profile Image </h6>
                                                <p class="fs-12">Image should be below 4 mb </p>
                                            </div>
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn btn btn-sm btn-primary me-2">
                                                    Upload
                                                    <input type="file" name="profile_image"
                                                        class="form-control image-sign" multiple="" />
                                                </div>
                                                <a href="javascript:void(0);" class="btn btn-light btn-sm">Cancel </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal uploadimageModal" id="add_uploadimageModal">
                                        <div class="modal-dialog">
                                            <div class="modal-content text-center">
                                            <div class="modal-body" style="max-height:inherit;">
                                                <div class="image_demo"></div>
                                                <button type="button" class="btn w-100 btn-success crop_image">
                                                Upload
                                                </button>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">First Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="first_name"
                                            id="first_name-info" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="last_name"
                                            id="last_name-info" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Gender</label>
                                        <select class="form-select select" name="gender" id="emp-gender-info">
                                            <option value="" selected disabled>Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Date Of Birth <span
                                                class="text-danger">*</span></label>
                                        <div class="input-icon-end position-relative">
                                            <input type="date" id="emp-birthday-info" name="birthday"
                                                class="form-control" />
                                            <span class="input-icon-addon"><i
                                                    class="ti ti-calendar text-gray-7"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Employee ID <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="employee_code"
                                            id="employee_code-info" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Joining Date <span
                                                class="text-danger">*</span></label>
                                        <div class="input-icon-end position-relative">
                                            <input type="date" id="emp-joining-info" name="joining_date"
                                                class="form-control" />
                                            <span class="input-icon-addon"><i
                                                    class="ti ti-calendar text-gray-7"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Username <span class="text-danger">*</span></label>
                                        <input type="text" id="usernames-info" name="username"
                                            class="form-control" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" id="emp-email-info" name="email"
                                            class="form-control" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Phone Number <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="emp-phone-info" name="phone"
                                            class="form-control" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Company <span class="text-danger">*</span></label>
                                        <input type="text" id="company-info" name="company"
                                            class="form-control" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Report Office</label>
                                        <select name="report_office[]" id="report_office-info"
                                            class="form-select select2" multiple>
                                            <!-- Options will be loaded dynamically -->
                                        </select>
                                        <!-- Display selected offices as removable badges -->
                                        <div id="selected-offices-container" class="mt-2 d-flex flex-wrap gap-2"></div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">PF No <span class="text-danger">*</span></label>
                                        <input type="text" id="emp-pf-no" name="pf_no" class="form-control"
                                            placeholder="Enter PF Number" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ESI No <span class="text-danger">*</span></label>
                                        <input type="text" id="emp-esi-no" name="esi_no" class="form-control"
                                            placeholder="Enter ESI Number" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Resignation Date</label>
                                        <input type="date" id="emp-resignation-date" name="resignation_date"
                                            class="form-control" placeholder="Select Resignation Date" />
                                    </div>
                                </div>



                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Department</label>
                                        <select class="form-select" id="department-info" name="department">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Designation <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="designation-info_visible" placeholder="Select Designation" readonly>
                                            <button type="button" class="btn btn-primary" onclick="if($('#department-info').val()) { window.openDesignationTree($('#department-info').val(), '#designation-info'); } else { alert('Please select a department first'); }">
                                                <i class="ti ti-hierarchy-2"></i>
                                            </button>
                                        </div>
                                        <input type="hidden" name="designation" id="designation-info">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Employee Type</label>
                                        <select class="form-select" name="emp_type" id="emp-type-info">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6" id="reporting_manager_edit_section">
                                    <div class="mb-3">
                                        <label class="form-label">Reporting Manager</label>
                                        <select class="form-select select2" name="reporting_manager_id" id="reporting_manager-info">
                                            <option value="">Select Reporting Manager</option>
                                        </select>
                                        <small class="text-muted">Search by name, employee ID, designation, department, or report office</small>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Address <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="emp-address-info" name="address" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">About <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="emp-about-info" name="about" rows="3"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-light border me-2"
                                data-bs-dismiss="modal">Cancel </button>
                            <button type="submit" class="btn btn-primary">Save </button>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="address3" role="tabpanel" aria-labelledby="address-tab3"
                        tabindex="0">
                        <div class="modal-body">
                            <div class="card bg-light-500 shadow-none">
                                <div
                                    class="card-body d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                    <h6>Enable Options </h6>
                                    <div class="d-flex align-items-center justify-content-end">
                                        <div class="form-check form-switch me-2">
                                            <label class="form-check-label mt-0">
                                                <input class="form-check-input me-2" type="checkbox"
                                                    role="switch" />
                                                Enable all Module
                                            </label>
                                        </div>
                                        <div class="form-check d-flex align-items-center">
                                            <label class="form-check-label mt-0">
                                                <input class="form-check-input" type="checkbox" checked="" />
                                                Select All
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive border rounded">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="form-check form-switch me-2">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            role="switch" checked="" />
                                                        Holidays
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox"
                                                            checked="" />
                                                        Read
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Write
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Create
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox"
                                                            checked="" />
                                                        Delete
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Import
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Export
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check form-switch me-2">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            role="switch" />
                                                        Leaves
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Read
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Write
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Create
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Delete
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Import
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Export
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check form-switch me-2">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            role="switch" />
                                                        Clients
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Read
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Write
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Create
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Delete
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Import
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Export
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check form-switch me-2">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            role="switch" />
                                                        Projects
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Read
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Write
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Create
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Delete
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Import
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Export
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check form-switch me-2">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            role="switch" />
                                                        Tasks
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Read
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Write
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Create
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Delete
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Import
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Export
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check form-switch me-2">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            role="switch" />
                                                        Chats
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Read
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Write
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Create
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Delete
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Import
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Export
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check form-switch me-2">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            role="switch" checked="" />
                                                        Assets
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Read
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Write
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox"
                                                            checked="" />
                                                        Create
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Delete
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox"
                                                            checked="" />
                                                        Import
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Export
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check form-switch me-2">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            role="switch" />
                                                        Timing Sheets
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Read
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Write
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Create
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Delete
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Import
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Export
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-light border me-2"
                                data-bs-dismiss="modal">Cancel </button>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#success_modal">Save </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Edit Employee -->
<!-- Edit Personal -->
<div class="modal fade" id="edit_personal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Personal Info </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="employee_personal_info" method="POST">
                @csrf
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Passport No</label>
                                <input type="text" name="passport_no" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Passport Expiry Date</label>
                                <div class="input-icon-end position-relative">
                                    <input type="date" class="form-control" name="passport_exp_date" />
                                    <span class="input-icon-addon">
                                        <i class="ti ti-calendar text-gray-7"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nationality <span class="text-danger"> * </span></label>
                                <input type="text" name="nationality" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Religion </label>
                                <input type="text" name="religion" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Marital status <span class="text-danger"> *
                                    </span></label>
                                <select class="select" name="marital_status">
                                    <option value="">Select</option>
                                    <option value="single">Single</option>
                                    <option value="married">Married</option>
                                    <option value="divorced">Divorced</option>
                                </select>

                            </div>
                        </div>

                        <div class="col-md-6 d-none" id="marriage_date_field">
                            <div class="mb-3">
                                <label class="form-label">Anniversary Date <span
                                        class="text-danger">*</span></label>
                                <div class="input-icon-end position-relative">
                                    <input type="date" class="form-control" name="marriage_date" />
                                    <span class="input-icon-addon">
                                        <i class="ti ti-calendar text-gray-7"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Spouse Name</label>
                                <input type="text" name="Employment_of_spouse" class="form-control" />
                            </div>
                        </div> --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">No. of children </label>
                                <input type="text" name="no_of_children" class="form-control" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2" data-bs-dismiss="modal">Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">Save </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Edit Personal -->
<!-- Edit Emergency Contact -->
<div class="modal fade" id="edit_emergency">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Emergency Contact Details </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="employee_emergency_c_dtails" method="POST">
                <div class="modal-body pb-0">
                    <input type="hidden" id="emergency-id" name="id">
                    <!-- Primary Contact -->
                    <div class="border-bottom mb-3">
                        <div class="row">
                            <h5 class="mb-3">Primary Contact Details</h5>
                            <input type="hidden" name="contacts[0][type]" value="primary" />
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="contacts[0][name]" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Relationship</label>
                                    <input type="text" name="contacts[0][relation]" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone No 1 <span class="text-danger">*</span></label>
                                    <input type="text" name="contacts[0][phone]" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone No 2</label>
                                    <input type="text" name="contacts[0][phone_alt]" class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Secondary Contact -->
                    <div class="row">
                        <h5 class="mb-3">Secondary Contact Details</h5>
                        <input type="hidden" name="contacts[1][type]" value="secondary" />
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="contacts[1][name]" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Relationship</label>
                                <input type="text" name="contacts[1][relation]" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone No 1 <span class="text-danger">*</span></label>
                                <input type="text" name="contacts[1][phone]" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone No 2</label>
                                <input type="text" name="contacts[1][phone_alt]" class="form-control" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>

        </div>
    </div>
</div>
<!-- /Edit Emergency Contact -->
<!-- Edit Bank -->
<div class="modal fade" id="edit_bank">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Bank Details </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="employee_edit_form_details" method="POST">
                <div class="modal-body pb-0">
                    <input type="hidden" name="bank_id" id="bank-id-hidden">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Bank Details <span class="text-danger"> * </span></label>
                                <input type="text" name="bank_name" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Bank account No </label>
                                <input type="text" name="account_number" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">IFSC Code </label>
                                <input type="text" name="ifsc_code" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Branch Address </label>
                                <input type="text" name="branch" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Bank Type <span class="text-danger">*</span></label>
                                <select name="bank_type" class="form-control" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="primary">Primary</option>
                                    <option value="secondary">Secondary</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2" data-bs-dismiss="modal">Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">Save </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Edit Bank -->
<!-- Add Family -->
<div class="modal fade" id="edit_familyinformation">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Family Information </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="family-info-form" method="POST">
                <div class="modal-body pb-0">
                    <div class="row">
                        <input type="hidden" name="id" id="family-id">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger"> * </span></label>
                                <input type="text" name="name" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Relationship <span class="text-danger">*</span></label>
                                <select name="relationship" class="form-select" required>
                                    <option value="" disabled selected>Select Relationship</option>
                                    <option value="Father">Father</option>
                                    <option value="Mother">Mother</option>
                                    <option value="Brother">Brother</option>
                                    <option value="Sister">Sister</option>
                                    <option value="Spouse">Spouse</option>
                                    <option value="Son">Son</option>
                                    <option value="Daughter">Daughter</option>
                                    <option value="Uncle">Uncle</option>
                                    <option value="Aunt">Aunt</option>
                                    <option value="Cousin">Cousin</option>
                                    <option value="Grandfather">Grandfather</option>
                                    <option value="Grandmother">Grandmother</option>
                                    <option value="Nephew">Nephew</option>
                                    <option value="Niece">Niece</option>
                                    <option value="Friend">Friend</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Phone </label>
                                <input type="text" name="contact" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Date of Birth <span class="text-danger"> *
                                    </span></label>
                                <div class="input-icon-end position-relative">
                                    <input type="date" name="dob" class="form-control"
                                        placeholder="dd/mm/yyyy" />
                                    <span class="input-icon-addon">
                                        <i class="ti ti-calendar text-gray-7"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2" data-bs-dismiss="modal">Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">Save </button>
                </div>
            </form>

        </div>
    </div>
</div>
<!-- /Add Family -->
<!-- Add Education -->
<div class="modal fade" id="edit_education">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Education Information </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="education-employee-info" method="POST">
                <div class="modal-body pb-0">
                    <div class="row">
                        <input type="hidden" name="id" id="education-id">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Institution Name <span class="text-danger"> *
                                    </span></label>
                                <input type="text" name="institute" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Course <span class="text-danger"> * </span></label>
                                <input type="text" name="degree" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start Date <span class="text-danger"> * </span></label>
                                <div class="input-icon-end position-relative">
                                    <input type="date" class="form-control" name="start_year"
                                        placeholder="dd/mm/yyyy" />
                                    <span class="input-icon-addon">
                                        <i class="ti ti-calendar text-gray-7"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">End Date <span class="text-danger"> * </span></label>
                                <div class="input-icon-end position-relative">
                                    <input type="date" class="form-control" name="end_year"
                                        placeholder="dd/mm/yyyy" />
                                    <span class="input-icon-addon">
                                        <i class="ti ti-calendar text-gray-7"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2" data-bs-dismiss="modal">Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">Save </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Education -->
<!-- Add Experience -->
<div class="modal fade" id="edit_experience">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Company Information </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="employee_details_exp" method="POST">
                <div class="modal-body pb-0">
                    <div class="row">
                        <input type="hidden" name="id" id="experience-id">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Previous Company Name <span class="text-danger"> *
                                    </span></label>
                                <input type="text" name="company_name" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Designation <span class="text-danger"> * </span></label>
                                <input type="text" name="position" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start Date <span class="text-danger"> * </span></label>
                                <div class="input-icon-end position-relative">
                                    <input type="date" name="start_date" class="form-control"
                                        placeholder="dd/mm/yyyy" />
                                    <span class="input-icon-addon">
                                        <i class="ti ti-calendar text-gray-7"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">End Date <span class="text-danger"> * </span></label>
                                <div class="input-icon-end position-relative">
                                    <input type="date" name="end_date" class="form-control"
                                        placeholder="dd/mm/yyyy" />
                                    <span class="input-icon-addon">
                                        <i class="ti ti-calendar text-gray-7"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-check-label d-flex align-items-center mt-0">
                                    <input class="form-check-input mt-0 me-2" name="current_working"
                                        type="checkbox" checked="" />
                                    <span class="text-dark">Check if you working _______ </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2" data-bs-dismiss="modal">Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">Save </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Experience -->
<!-- Add Employee Success -->
<div class="modal fade" id="success_modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center p-3">
                    <span class="avatar avatar-lg avatar-rounded bg-success mb-3"><i
                            class="ti ti-check fs-24"></i></span>
                    <h5 class="mb-2">Employee Added Successfully </h5>
                    <p class="mb-3">Stephan Peralt has been _____ with Client ID : <span class="text-primary">#EMP
                            - 0001 </span>
                    </p>
                    <div>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="employees.html" class="btn btn-dark w-100">Back to List </a>
                            </div>
                            <div class="col-6">
                                <a href="employee-details.html" class="btn btn-primary w-100">Detail Page </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Add Client Success -->

<!-- Add Statuorty -->
<div class="modal fade" id="add_bank_satutory">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Bank & Statutory </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="employee_bank_statutory" method="POST">
                <div class="modal-body pb-0">
                    <div class="border-bottom mb-4">
                        <h5 class="mb-3">Basic Salary Information </h5>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Salary basis <span class="text-danger"> *
                                        </span></label>
                                    <select class="select" name="salary_basis">
                                        <option />Select
                                        <option value="Weekly" />Weekly
                                        <option value="Monthly" />Monthly
                                        <option value="Annualy" />Annualy
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Salary basis </label>
                                    <input type="text" name="salary_amount" class="form-control"
                                        value="$" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Payment type </label>
                                    <select class="select" name="payment_type">
                                        <option />Select
                                        <option value="Cash" />Cash
                                        <option value="Debit Card" />Debit Card
                                        <option value="Mobile Payment" />Mobile Payment
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-bottom mb-4">
                        <h5 class="mb-3">PF Information </h5>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">PF contribution <span class="text-danger"> *
                                        </span></label>
                                    <select class="select" name="pf_contribution">
                                        <option />Select
                                        <option value="Employee Contribution" />Employee Contribution
                                        <option value="Employer Contribution" />Employer Contribution
                                        <option value="Provident Fund Interest" />Provident Fund Interest
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">PF No </label>
                                    <input type="text" name="pf_no" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Employee PF rate </label>
                                    <input type="text" name="pf_rate" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Additional rate </label>
                                    <select class="select" name="pf_additional">
                                        <option />Select
                                        <option value="ESI" />ESI
                                        <option value="EPS" />EPS
                                        <option value="EPF" />EPF
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Total rate </label>
                                    <input type="text" name="pf_total" class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <h5 class="mb-3">ESI Information </h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">ESI contribution <span class="text-danger"> *
                                    </span></label>
                                <select class="select" name="esi_contribution">
                                    <option />Select
                                    <option value="Employee Contribution" />Employee Contribution
                                    <option value="Employer Contribution" />Employer Contribution
                                    <option value="Maternity Benefit" />Maternity Benefit
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">ESI Number </label>
                                <input type="text" name="esi_no" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Employee ESI rate <span class="text-danger"> *
                                    </span></label>
                                <input type="text" name="esi_rate" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Additional rate </label>
                                <select class="select" name="esi_additional">
                                    <option />Select
                                    <option value="ESI" />ESI
                                    <option value="EPS" />EPS
                                    <option value="EPF" />EPF
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total rate </label>
                                <input type="text" name="esi_total" class="form-control" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2" data-bs-dismiss="modal">Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">Save </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="change_password">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Change Password</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="employee_change_password" method="POST">
                @csrf
                <div class="modal-body pb-0">
                    <div class="mb-3 position-relative">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password"
                            required>
                        <span class="ti toggle-password ti-eye-off" data-target="#new_password" role="button"
                            aria-label="Toggle password visibility"
                            style="cursor:pointer; position:absolute; right:15px; top:38px;"></span>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="new_password_confirmation"
                            name="new_password_confirmation" required>
                        <span class="ti toggle-password ti-eye-off" data-target="#new_password_confirmation"
                            role="button" aria-label="Toggle password visibility"
                            style="cursor:pointer; position:absolute; right:15px; top:38px;"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="change_password">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Change Password</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="employee_change_password" method="POST">
                @csrf
                <div class="modal-body pb-0">
                    <div class="mb-3 position-relative">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password"
                            required>
                        <span class="ti toggle-password ti-eye-off" data-target="#new_password" role="button"
                            aria-label="Toggle password visibility"
                            style="cursor:pointer; position:absolute; right:15px; top:38px;"></span>
                    </div>

                    <div class="mb-3 position-relative">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="new_password_confirmation"
                            name="new_password_confirmation" required>
                        <span class="ti toggle-password ti-eye-off" data-target="#new_password_confirmation"
                            role="button" aria-label="Toggle password visibility"
                            style="cursor:pointer; position:absolute; right:15px; top:38px;"></span>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Statuorty -->
<!-- Asset Information -->
<div class="modal fade" id="asset_info">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Asset Information </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="bg-light p-3 rounded d-flex align-items-center mb-3">
                    <span class="avatar avatar-lg flex-shrink-0 me-2">
                        <img src="" alt="img" class="ig-fluid rounded-circle" />
                    </span>
                    <div>
                        <h6>Dell Laptop - #343556656 </h6>
                        <p class="fs-13"><span class="text-primary">AST - 001 </span><i
                                class="ti ti-point-filled text-primary"></i> Assigned on 22 ___, 2022 10:32AM </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p class="fs-13 mb-0">Type </p>
                            <p class="text-gray-9">Laptop </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p class="fs-13 mb-0">Brand </p>
                            <p class="text-gray-9">Dell </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p class="fs-13 mb-0">Category </p>
                            <p class="text-gray-9">Computer </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p class="fs-13 mb-0">Serial No </p>
                            <p class="text-gray-9">3647952145678 </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p class="fs-13 mb-0">Cost </p>
                            <p class="text-gray-9">$800 </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p class="fs-13 mb-0">Vendor </p>
                            <p class="text-gray-9">Compusoft Systems Ltd., </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p class="fs-13 mb-0">Warranty </p>
                            <p class="text-gray-9">12 Jan 2022 - 12 Jan 2026 </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p class="fs-13 mb-0">Location </p>
                            <p class="text-gray-9">46 Laurel Lane, TX 79701 </p>
                        </div>
                    </div>
                </div>
                <div>
                    <p class="fs-13 mb-2">Asset Images </p>
                    <div class="d-flex align-items-center">
                        <img src="" alt="img" class="img-fluid rounded me-2" />
                        <img src="" alt="img" class="img-fluid rounded me-2" />
                        <img src="" alt="img" class="img-fluid rounded" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Asset Information -->

<!-- Refuse -->
<div class="modal fade" id="refuse_msg">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Raise Issue </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form action="employee-details.html">
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Description <span class="text-danger"> * </span></label>
                                <textarea class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2" data-bs-dismiss="modal">Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">Submit </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Refuse -->
<!-- Delete Modal -->
<div class="modal fade" id="delete_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1" id="delete_title">Confirm Delete</h4>
                <p class="mb-3">
                    You want to delete the marked item. This action <strong>cannot</strong> be undone once you proceed.
                </p>

                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete_btn" data-id=""
                        data-type="">
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toastMessage" class="toast align-items-center text-white bg-danger border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastBody"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<!-- /Delete Modal -->

<!-- Simple Empty Modal -->
<div class="modal fade" id="assign_shift_employee" tabindex="-1" aria-labelledby="assignShiftModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="assignShiftModalLabel">Assign Shift to Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assignShiftForm">
                    <div class="mb-3">
                        <label for="shiftSelect" class="form-label">Select Shift</label>
                        <select class="form-select" id="shiftSelect" name="shift_id" required>
                            <option value="">-- Select Shift --</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="assignShiftForm" class="btn btn-primary">Save</button>
            </div>

        </div>
    </div>
</div>

@push('scripts')
    <!-- Croppie CSS and Scripts for Image Cropping -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.js"></script>

    <style>
        .croppie-container .cr-slider-wrap {
            width: 95%;
            margin: 0 6px;
            text-align: center;
            padding-top: 5px;
        }
        .cr-viewport {
            outline: none;
            box-shadow: 0;
        }
        .cr-boundary {
            width: 100%;
        }
    </style>

    <!-- Image Crop Script - External File -->
    <script src="{{ asset('assets/js/image-cropper.js') }}"></script>

<!-- Asset Assign Modal -->
<div class="modal fade" id="modal_assign_asset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Quick Asset Assignment</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quick_assign_asset_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="col-form-label">Available Asset <span class="text-danger">*</span></label>
                                <select class="select form-control" name="asset_id" id="available_asset_id_details" required>
                                    <option value="">Loading available assets...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="col-form-label">Assigned Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="assigned_date" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>
                        <!-- Hidden current employee ID -->
                        <input type="hidden" name="box_user_id" id="assign_box_user_id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-light me-2">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btn_save_assign_details">Assign Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Asset Assign Modal -->

<script data-cfasync="false" src="{{ asset('assets/js/email-decode.min.js') }}"></script>
 <script>
    $(document).ready(function() {
        const empId = new URLSearchParams(window.location.search).get('emp_id');
        if (!empId) {
            alert('Employee ID not found.');
            return;
        }
        // Store employee branch assignment status globally
        let employeeHasBranches = false;

        // Prevent modal from opening if no branches assigned
        $(document).off('click', '.edit-bio-metric').on('click', '.edit-bio-metric', function(e) {
            console.log('Edit button clicked, employeeHasBranches:', employeeHasBranches);
            if (!employeeHasBranches) {
                e.preventDefault();
                e.stopPropagation();
                showToast('Machine cannot be assigned. Employee is not assigned to any branch.', false);
                return false;
            }
            // If branches are assigned, allow modal to open normally
        });

        $('#biometric_maping').off('show.bs.modal').on('show.bs.modal', function (event) {
            const modal = $(this);

            console.log('Modal opening, employeeHasBranches:', employeeHasBranches);

            // Check if employee has assigned branches - only show modal if branches are assigned
            if (!employeeHasBranches) {
                event.preventDefault();
                event.stopPropagation();
                modal.modal('hide');
                showToast('Machine cannot be assigned. Employee is not assigned to any branch.', false);
                return false;
            }

            // If branches are assigned, proceed with modal opening and load machines

            const machineSelect = modal.find('select.select');
            machineSelect.empty();
            machineSelect.append('<option disabled selected>Loading...</option>');
            $.ajax({
                url: '{{ route('get.all.machines', ['box_slug' => request()->route('box_slug')]) }}',
                method: 'GET',
                success: function (response) {
                    machineSelect.empty();
                    if(response.status && response.machines.length > 0){
                        machineSelect.append('<option disabled selected>-Machine Serial No.-</option>');
                        response.machines.forEach(machine => {
                            machineSelect.append(`<option value="${machine.id}">${machine.serial_no} - ${machine.machine_name}</option>`);
                        });
                        // Handle edit mode: Select the pre-set machine ID after population
                        const editMachineId = modal.data('edit-machine-id');
                        if (editMachineId) {
                            machineSelect.val(editMachineId);
                            // Clear edit data to avoid interference on next show
                            modal.removeData('edit-machine-id');
                        }
                    } else {
                        machineSelect.append('<option disabled selected>No Machines Found</option>');
                    }
                },
                error: function () {
                    machineSelect.empty();
                    machineSelect.append('<option disabled selected>Failed to load machines</option>');
                }
            });
        });
   $('#biometric_maping_add').submit(function(e) {
    e.preventDefault();
    if (!empId) {
        showToast('Employee ID not found.', false);
        return;
    }

    // Validate form fields before submission
    const machineUserId = $('#machine-user-id').val().trim();
    const biometricMachineId = $('#biometric_id').val();

    if (!machineUserId) {
        showToast('Machine User ID is required.', false);
        return;
    }

    if (!biometricMachineId || biometricMachineId === '') {
        showToast('Please select a biometric machine.', false);
        return;
    }

    // Disable submit button to prevent double submission
    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true).text('Saving...');

    // Get fresh CSRF token
    const csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';

    // Use FormData to ensure proper encoding and fresh token
    const formData = new FormData(this);
    formData.append('emp_id', empId);
    formData.set('_token', csrfToken); // Ensure fresh token

    $.ajax({
        url: '{{ route('map.to.assing', ['box_slug' => request()->route('box_slug')]) }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {
            console.log('API Success Response:', response); // Log for debugging
            if (response.status === true) {
                $('#biometric_maping').modal('hide');
                cleanupModalBackdrop(); // remove modal backdrop
                fetchEmployeeDetails(empId, true); // refresh employee info
                showToast('Biometric mapping assigned successfully!', true);
            } else {
                console.log('API Error Response:', response); // Log for debugging
                // Show full JSON in alert for detailed error
                // alert(JSON.stringify(response, null, 2)); // Pretty-print JSON
                showToast(response.message || 'Failed to map biometric machine', false);
            }
        },
        error: function(xhr) {
            console.log('AJAX Error:', xhr); // Log full error
            let errorMsg = 'Something went wrong. Please try again.';

            // Handle CSRF token expiration (419 status code)
            if (xhr.status === 419) {
                errorMsg = 'Session expired. Please refresh the page and try again.';
                showToast(errorMsg, false);
                setTimeout(function() {
                    location.reload();
                }, 2000);
                return;
            }

            // Handle validation errors (422)
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                errorMsg = 'Validation Error:\n' + Object.values(xhr.responseJSON.errors)
                    .map(val => `- ${val[0] || val.join(', ')}`).join('\n');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }

            // Show full response in alert
            const fullResponse = xhr.responseJSON ? JSON.stringify(xhr.responseJSON, null, 2) :
                                JSON.stringify({ error: 'Network or server error', status: xhr.status });
            // alert(fullResponse);
            showToast(errorMsg, false);
        },
        complete: function() {
            // Re-enable submit button
            submitBtn.prop('disabled', false).text('Save');
        }
    });
});
        $('.edit-working-mode').on('click', function(e) {
            e.preventDefault();
            const employeeId = $(this).data('id');
            const form = $('#employee_edit_form_details');
            const workModeSelect = $('#working-mode-select');
            form.trigger('reset');
            form.find('.form-check-input').prop('checked', false);
            function getEmployeeDataFromLocalStorage(empId) {
                const localKey = `employee_${empId}`;
                const stored = localStorage.getItem(localKey);
                if (!stored) return null;
                try {
                    return JSON.parse(stored);
                } catch (err) {
                    console.error('Error parsing employee data from localStorage:', err);
                    return null;
                }
            }
            function populateModalWithData(data) {
                let workingDays = [];
                if (data?.working_web_days) {
                    if (typeof data.working_web_days === 'string') {
                        try {
                            workingDays = JSON.parse(data.working_web_days);
                            if (!Array.isArray(workingDays)) workingDays = [];
                        } catch (err) {
                            console.warn(
                                'working_web_days is not valid JSON, defaulting to empty array.');
                            workingDays = [];
                        }
                    } else if (Array.isArray(data.working_web_days)) {
                        workingDays = data.working_web_days;
                    }
                }
                $('#working_mode input[name="working_web_days[]"]').prop('checked', false);
                workingDays.forEach(day => {
                    $(`#working_mode input[name="working_web_days[]"][value="${day}"]`).prop(
                        'checked', true);
                });
                $('#working_mode').modal('show');
            }
            const localData = getEmployeeDataFromLocalStorage(empId);
            $.ajax({
                url: '{{ route('get.all.workmode', ['box_slug' => request()->route('box_slug')]) }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    workModeSelect.find('option:not(:first)').remove();
                    response.data.forEach(mode => {
                        workModeSelect.append($('<option>', {
                            value: mode.id,
                            text: mode.name
                        }));
                    });
                    if (localData) {
                        if (localData.work_mode?.id) {
                            workModeSelect.val(localData.work_mode.id.toString()).trigger(
                                'change');
                        }
                        populateModalWithData(localData);
                    } else {
                        $('#working_mode').modal('show');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching working modes:', error);
                    alert('Failed to load working modes.');
                }
            });
        });
        $('#working_mode_submit').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const submitBtn = $form.find('button[type="submit"]');
            submitBtn.prop('disabled', true);

            // Get fresh CSRF token from meta tag
            const csrfToken = $('meta[name="csrf-token"]').attr('content') || $form.find('input[name="_token"]').val() || '{{ csrf_token() }}';

            // Use FormData to ensure proper encoding
            const formData = new FormData(this);
            formData.append('employee_id', empId);
            // Ensure CSRF token is included (override if already exists)
            formData.set('_token', csrfToken);

            $.ajax({
                url: '{{ route('update.work.mode', ['box_slug' => request()->route('box_slug')]) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(res) {
                    $('#working_mode').modal('hide');
                    cleanupModalBackdrop();
                    fetchEmployeeDetails(empId, true);
                    showToast('Work Mode assigned successfully!', true);
                },
                error: function(xhr) {
                    let msg = 'Failed to save working mode.';

                    // Handle CSRF token expiration (419 status code)
                    if (xhr.status === 419) {
                        msg = 'Session expired. Please refresh the page and try again.';
                        showToast(msg, false);
                        // Reload the page after a delay to get fresh token
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                        return;
                    }

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }

                    showToast(msg, false);
                    console.error('Error saving working mode:', xhr);
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                }
            });
        });
        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function() {
                const input = document.querySelector(this.getAttribute('data-target'));
                if (!input) return;
                input.type = input.type === 'password' ? 'text' : 'password';
                this.classList.toggle('ti-eye', input.type === 'text');
                this.classList.toggle('ti-eye-off', input.type === 'password');
            });
        });
        // Handle marital status change
        $('select[name="marital_status"]').on('change', function() {
            const isMarried = $(this).val() === 'married';
            $('#marriage_date_field').toggleClass('d-none', !isMarried);
            if (!isMarried) $('input[name="marriage_date"]').val('');
        });
        // Centralized modal backdrop cleanup
        function cleanupModalBackdrop() {
            // Remove all backdrops to prevent stacking
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('padding-right', '');
        }
        // Generic form submit handler
        function handleFormSubmit(formSelector, route, modalSelector, extraData = {}) {
            $(document).off('submit', formSelector).on('submit', formSelector, function(e) {
                e.preventDefault();

                const $form = $(this);

                // Prevent duplicate submissions - check if form is already submitting
                if ($form.data('is-submitting')) {
                    console.log('Form submission already in progress, ignoring duplicate submit');
                    return false;
                }

                const submitBtn = $form.find('button[type="submit"]');
                submitBtn.prop('disabled', true).blur();
                document.activeElement.blur();

                // Set submitting flag on form element
                $form.data('is-submitting', true);
                // Set contact types
                $form.find('[name="contacts[0][type]"]').val('primary');
                $form.find('[name="contacts[1][type]"]').val('secondary');

                    // Check if cropped_image exists before creating FormData
                    var hasCroppedImage = false;
                    if (formSelector === '#employee_edit_form') {
                        var croppedImageVal = $('#edit_details_cropped_image').val();
                        if (!croppedImageVal) {
                            croppedImageVal = $('#employee_edit_form').data('cropped-image');
                        }
                        hasCroppedImage = croppedImageVal && croppedImageVal.trim() !== '';
                    }

                    const formData = new FormData(this);

                    // If cropped_image exists, remove profile_image file from FormData
                    if (hasCroppedImage && formData.has('profile_image')) {
                        formData.delete('profile_image');
                        console.log('Removed profile_image file because cropped_image exists');
                    }

                    formData.append('_method', 'PUT');
                    // Use dynamic CSRF token from meta tag (refreshes on each request)
                    const csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
                    formData.append('_token', csrfToken);

                    // For employee_edit_form, handle report_office specially
                    if (formSelector === '#employee_edit_form') {
                        const reportOfficeSelect = $('#report_office-info');
                        const selectedOffices = reportOfficeSelect.val() || [];

                        // Remove ALL existing report_office entries from formData (both array and single)
                        // FormData can have multiple entries with same key, so we need to remove all
                        const keysToRemove = [];
                        for (let pair of formData.entries()) {
                            if (pair[0] === 'report_office' || pair[0] === 'report_office[]') {
                                keysToRemove.push(pair[0]);
                            }
                        }
                        keysToRemove.forEach(key => {
                            formData.delete(key);
                        });

                        // Always send report_office - send null if nothing selected, otherwise send the array
                        if (selectedOffices.length === 0) {
                            // Send as string "null" - backend will convert to actual null
                            // This ensures report_office is explicitly sent as null in the payload
                            formData.set('report_office', 'null');
                        } else {
                            // Clear any single report_office entry first
                            formData.delete('report_office');
                            // Add each selected office as array
                            selectedOffices.forEach(officeId => {
                                formData.append('report_office[]', officeId);
                            });
                        }
                    }

                    // Add extra data (like id, employee_id, etc.)
                    Object.entries(extraData).forEach(([key, value]) => {
                        // Don't override existing form fields, only add if not present
                        if (!formData.has(key)) {
                            formData.append(key, value);
                        }
                    });

                    // For employee_edit_form, ensure id is always included and cropped_image is sent
                    if (formSelector === '#employee_edit_form') {
                        if (extraData.id) {
                            formData.set('id', extraData.id);
                        }
                        // Include cropped_image if it exists - check both hidden input and form data
                        var croppedImage = $('#edit_details_cropped_image').val();
                        console.log('Cropped image from hidden input:', croppedImage ? 'Found (' + croppedImage.substring(0, 50) + '...)' : 'Not found');

                        if (!croppedImage || croppedImage.trim() === '') {
                            // Try to get from form data attribute (backup)
                            croppedImage = $('#employee_edit_form').data('cropped-image');
                            console.log('Cropped image from form data:', croppedImage ? 'Found (' + croppedImage.substring(0, 50) + '...)' : 'Not found');
                        }

                        if (croppedImage && croppedImage.trim() !== '') {
                            formData.set('cropped_image', croppedImage);
                            // Remove profile_image file if cropped_image exists (cropped_image has priority)
                            if (formData.has('profile_image')) {
                                formData.delete('profile_image');
                                console.log('Cropped image included in form submission, profile_image file removed');
                            } else {
                                console.log('Cropped image included in form submission');
                            }
                        } else {
                            console.log('No cropped image found, using profile_image file if present');
                        }
                    }
                // Log form submission for debugging
                console.log('Submitting form:', formSelector, 'Route:', route);
                if (formSelector === '#employee_edit_form') {
                    console.log('FormData entries:', Array.from(formData.entries()).map(([key, val]) => {
                        if (key === 'cropped_image') {
                            return [key, val ? val.substring(0, 50) + '...' : 'null'];
                        } else if (val instanceof File) {
                            return [key, 'File: ' + val.name];
                        }
                        return [key, val];
                    }));
                }

                $.ajax({
                    url: route,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        console.log('Form submission successful:', formSelector);
                        // Reset submitting flag
                        $form.data('is-submitting', false);

                        showToast(response.message || 'Saved successfully.', true);
                        if (modalSelector) {
                            $(modalSelector).modal('hide');
                            cleanupModalBackdrop();

                            // Update images immediately if cropped image was sent
                            if (formSelector === '#employee_edit_form') {
                                var croppedImage = $('#edit_details_cropped_image').val();
                                if (croppedImage && response.data && response.data.profile_image_url) {
                                    var imageUrl = response.data.profile_image_url;
                                    var cacheBuster = '?t=' + new Date().getTime();
                                    var finalUrl = imageUrl + (imageUrl.indexOf('?') === -1 ? cacheBuster : '&t=' + new Date().getTime());

                                    // Update the main profile image
                                    $('#profile_img').attr('src', finalUrl).removeClass('d-none');
                                    $('#letter_avatar').addClass('d-none');

                                    // Update the edit form preview image
                                    // Handle both ID and class based on what is present
                                    if ($('.uploaded_image').length > 0) {
                                         $('.uploaded_image').html('<img src="' + finalUrl + '" alt="img" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" id="editprofile_img" />');
                                    }
                                }
                            }

                            fetchEmployeeDetails(empId, true);
                        }
                    },
                    error: function(xhr) {
                        // Reset submitting flag on error
                        $form.data('is-submitting', false);

                        let msg = 'Something went wrong. Please try again.';
                        // Handle CSRF token expiration (419 status code)
                        if (xhr.status === 419) {
                            msg = 'Session expired. Please refresh the page and try again.';
                            showToast(msg, false);
                            // Optionally reload the page after a delay
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                            return;
                        }
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            msg = 'Validation Error:\n' + Object.values(xhr.responseJSON
                                    .errors)
                                .map(val => `- ${val[0]}`).join('\n');
                        }
                        showToast(msg, false);
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false);
                    }
                });
            });
        }
        // Initialize form handlers
        const formConfigs = [{
                selector: '#family-info-form',
                route: '{{ route('employee.family', ['box_slug' => request()->route('box_slug')]) }}',
                modal: '#edit_familyinformation',
                data: {
                    employee_id: empId
                }
            },
            {
                selector: '#education-employee-info',
                route: '{{ route('employee.education', ['box_slug' => request()->route('box_slug')]) }}',
                modal: '#edit_education',
                data: {
                    employee_id: empId
                }
            },
            {
                selector: '#employee_details_exp',
                route: '{{ route('employee.experience', ['box_slug' => request()->route('box_slug')]) }}',
                modal: '#edit_experience',
                data: {
                    employee_id: empId
                }
            },
            {
                selector: '#employee_bank_statutory',
                route: '{{ route('employee.bank_safety', ['box_slug' => request()->route('box_slug')]) }}',
                modal: '#add_bank_satutory',
                data: {
                    employee_id: empId
                }
            },
            {
                selector: '#employee_change_password',
                route: '{{ route('employee.change-password', ['box_slug' => request()->route('box_slug')]) }}',
                modal: '#change_password',
                data: {
                    employee_id: empId
                }
            },
            {
                selector: '#employee_emergency_c_dtails',
                route: '{{ route('employee.emergency', ['box_slug' => request()->route('box_slug')]) }}',
                modal: '#edit_emergency',
                data: {
                    employee_id: empId
                }
            },
            {
                selector: '#employee_personal_info',
                route: '{{ route('employee.update', ['box_slug' => request()->route('box_slug')]) }}',
                modal: '#edit_personal',
                data: {
                    id: empId
                }
            },
            {
                selector: '#employee_edit_form',
                route: '{{ route('employee.update', ['box_slug' => request()->route('box_slug')]) }}',
                modal: '#edit_employee',
                data: {
                    id: empId
                }
            },
            {
                selector: '#employee_edit_form_details',
                route: '{{ route('employee.bank.update', ['box_slug' => request()->route('box_slug')]) }}',
                modal: '#edit_bank',
                data: {
                    employee_id: empId
                }
            }
        ];
        formConfigs.forEach(config => handleFormSubmit(config.selector, config.route, config.modal, config
            .data));
        // Reset forms and clean up backdrops on modal hide
        $('.modal').on('hidden.bs.modal', function() {
            const form = this.querySelector('form');
            if (form) {
                form.reset();
                $(form).find('input[type="hidden"]').val('');
            }
            // Specific for biometric mapping modal
            if (this.id === 'biometric_maping') {
                $('#biometric_maping_add input[name="id"]').remove();
            }
            // Clear employee ID in edit modal header when modal closes
            // if (this.id === 'edit_employee') {
            //     const employeeIdHeader = document.getElementById('edit_employee_details_header_id');
            //     if (employeeIdHeader) {
            //         employeeIdHeader.textContent = '';
            //     }
            // }
            cleanupModalBackdrop();
        });
        // Toast notification
        function showToast(message, isSuccess = true) {
            const $toast = $('#toastMessage').removeClass('bg-success bg-danger')
                .addClass(isSuccess ? 'bg-success' : 'bg-danger');
            $('#toastBody').text(message);
            new bootstrap.Toast($toast[0]).show();
        }
        // Delete button handler
        $(document).on('click', '.delete-bank-btn', function() {
            const itemId = $(this).data('id');
            const itemType = $(this).data('type');
            if (!itemId || !itemType) {
                showToast('Invalid item ID or type', false);
                return;
            }
            const titles = {
                bank: 'Delete Bank Detail',
                family: 'Delete Family Member',
                education: 'Delete Education Record',
                experience: 'Delete Experience Record',
                emergency: 'Delete Emergency Records',
                shift: 'Delete Shift Records',
                biometric: 'Delete Biometric Mapping'
            };
            $('#confirm_delete_btn')
                .data({
                    id: itemId,
                    type: itemType
                })
                .text('Yes, Delete')
                .prop('disabled', false);
            $('#delete_title').text(titles[itemType] || 'Delete Record');
            $('#delete_modal').modal('show');
        });
        // Confirm delete
        $(document).on('click', '#confirm_delete_btn', function() {
            const $btn = $(this);
            const {
                id: itemId,
                type: itemType
            } = $btn.data();
            const deleteRoutes = {
                bank: '{{ route('delete.bank', ['box_slug' => request()->route('box_slug')]) }}',
                family: '{{ route('delete.family', ['box_slug' => request()->route('box_slug')]) }}',
                education: '{{ route('delete.education', ['box_slug' => request()->route('box_slug')]) }}',
                experience: '{{ route('delete.experience', ['box_slug' => request()->route('box_slug')]) }}',
                emergency: '{{ route('delete.emergency', ['box_slug' => request()->route('box_slug')]) }}',
                shift: '{{ route('delete.shift.emp', ['box_slug' => request()->route('box_slug')]) }}',
                biometric: '{{ route("delete.biometric.mapping", ["box_slug" => request()->route("box_slug")]) }}'
            };
            const requestDataMap = {
                bank: {
                    bank_id: itemId
                },
                family: {
                    family_id: itemId
                },
                education: {
                    education_id: itemId
                },
                experience: {
                    experience_id: itemId
                },
                emergency: {
                    emergency_id: itemId
                },
                shift: {
                    shift_id: itemId,
                    emp_id: empId
                },
                biometric: {
                    id: itemId
                }
            };
            const url = deleteRoutes[itemType];
            if (!url) {
                showToast('Unknown delete type', false);
                return;
            }
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');
            $.ajax({
                url,
                type: 'DELETE',
                data: requestDataMap[itemType],
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#delete_modal').modal('hide');
                    cleanupModalBackdrop();
                    showToast(response.message || 'Deleted successfully.', true);
                    fetchEmployeeDetails(empId, true);
                },
                error: function(xhr) {
                    $('#delete_modal').modal('hide');
                    cleanupModalBackdrop();
                    showToast(xhr.responseJSON?.message || 'Delete failed.', false);
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Yes, Delete');
                }
            });
        });
        // Fetch team details
        $('#emp-designation').on('click', function() {
            const designationId = this.getAttribute('data-designation-id');
            if (!designationId) {
                console.warn('No designation ID found.');
                return;
            }
            $('#department_id').val(designationId);
            $.ajax({
                url: '{{ route('get.teams', ['box_slug' => request()->route('box_slug')]) }}',
                method: 'GET',
                data: {
                    team_id: designationId
                },
                success: function(response) {
                    if (response.status === 200 && response.data) {
                        const team = response.data;
                        let $info = $('#TeamsModel .modal-body .team-info');
                        if (!$info.length) {
                            $info = $('<p class="team-info text-primary mt-2"></p>')
                                .appendTo('#TeamsModel .modal-body');
                        }
                        $info.text(`Team Name: ${team.name}`);
                    } else {
                        console.error('Team not found.');
                    }
                },
                error: function(xhr) {
                    console.error('AJAX error:', xhr.responseText);
                }
            });
        });
        // Biometric edit handler
        $(document).off('click', '.edit-biometric').on('click', '.edit-biometric', function() {
            const id = $(this).data('id');
            const biometricMachineId = $(this).data('biometric-machine-id');
            const machineUserId = $(this).data('machine-user-id');
            const modal = $('#biometric_maping');
            // Reset form
            $('#biometric_maping_add')[0].reset();
            // Set values (machine-user-id can be set early as it's not affected by load)
            $('#machine-user-id').val(machineUserId);
            // Store edit machine ID on modal data for post-load selection
            modal.data('edit-machine-id', biometricMachineId);
            // Add hidden id for update
            let hiddenId = $('#biometric_maping_add input[name="id"]');
            if (hiddenId.length) {
                hiddenId.val(id);
            } else {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'id',
                    value: id
                }).appendTo('#biometric_maping_add');
            }
            modal.modal('show');
            cleanupModalBackdrop();
        });
        // Populate employee data
        function populateEmployeeData(data) {
            const daysContainer = $('#working_web_days');
            function setValue(selector, value) {
                const $el = $(selector);
                if ($el.is('input, select, textarea')) {
                    $el.val(value ?? '');
                } else {
                    $el.html(value ?? '');
                }
            }

            // Set Name above map
            $('#tracking-emp-name').text((data.first_name || '') + ' ' + (data.last_name || ''));
            
            // Load map data
            if (data.is_location_tracking == 1) {
                loadTrackingMap(data.user_id);
            } else {
                $('#employeeTrackingMap').html('<span class="text-muted">Tracking is disabled for this user.</span>');
            }
            console.log('data', data);
            console.log('has_assigned_branches from response:', data.has_assigned_branches);

            // Store branch assignment status for machine mapping validation
            // Directly set the variable - it's in the same scope
            employeeHasBranches = data.has_assigned_branches === true || data.has_assigned_branches === 1 || data.has_assigned_branches === '1';
            console.log('employeeHasBranches set to:', employeeHasBranches);

            $('#locationTrackingToggle').prop('checked', data.is_location_tracking == 1);

            // Keep biometric section visible - don't hide it
            // Only show error message when trying to open modal if no branch assigned

            function showLetterAvatar(name) {
                const $img = $('#profile_img');
                const $letterAvatar = $('#letter_avatar');
                const $avatarLetter = $('#avatar_letter');
                const firstLetter = (name || 'U').trim().charAt(0).toUpperCase();
                const color = getColorFromName(firstLetter);
                $img.addClass('d-none');
                $avatarLetter.text(firstLetter);
                $letterAvatar.removeClass('d-none').addClass('d-flex').css('background-color', color);
            }
            function getColorFromName(letter) {
                const colors = [
                    '#f44336', '#e91e63', '#9c27b0', '#673ab7', '#3f51b5',
                    '#2196f3', '#03a9f4', '#00bcd4', '#009688', '#4caf50',
                    '#8bc34a', '#cddc39', '#ffc107', '#ff9800', '#ff5722'
                ];
                return colors[letter.toUpperCase().charCodeAt(0) % colors.length];
            }
            const is_admin = String(data.staff_role).toLowerCase() === 'admin';
            // Set basic employee data
            setValue('#emp-department', is_admin ? '' : (data.department_name || '-'));
            setValue('#department_id', data.department_id);
            setValue('#emp-designation', is_admin ? '' : (data.designation_name || '-'));
            $('#emp-designation').attr('data-designation-id', data.designation_id);
            setValue('#emp-name', data.user?.name);
            setValue('#first_name', data.name);
            setValue('#last_name', data.last_name);
            setValue('#emp-gender', data.gender);
            setValue('#emp-birthday', data.birthday);
            setValue('#emp-id', data.employee_id);
            setValue('#details_header_employee_id', data.employee_id);
            setValue('#edit_employee_details_header_id',data.employee_id);
            // Update employee ID in header

            setValue('#emp-joining', data.joining_date);
            setValue('#username', data.username);
            setValue('#emp-email', data.email);
            setValue('#emp-phone', data.phone);
            setValue('#company', data.company);
            setValue('#department', data.department);
            setValue('#department_new', is_admin ? '' : (data.department_name || '-'));
            setValue('#designation_new', is_admin ? '' : (data.designation_name || '-'));
            setValue('#designation-info_visible', is_admin ? '' : (data.designation_name || '-'));
            setValue('#designation-info', is_admin ? '' : (data.designation_name || '-'));
            setValue('#emp-type-display', is_admin ? 'NA' : (data.employee_type_name || '-'));

            // Set Reporting Manager display
            if (is_admin) {
                $('#reporting_manager_section').hide();
                $('#reporting_manager_edit_section').hide();
                $('#department_display_section').hide();
                $('#designation_display_section').hide();
            } else {
                $('#reporting_manager_section').show();
                $('#reporting_manager_edit_section').show();
                $('#department_display_section').show();
                $('#designation_display_section').show();

                if (data.reporting_manager && data.reporting_manager.user) {
                    setValue('#reporting_manager_display', data.reporting_manager.user.name || '-');
                } else if (data.reporting_manager_id && data.all_employees) {
                    const reportingManager = data.all_employees.find(emp => emp.id == data.reporting_manager_id);
                    if (reportingManager) {
                        setValue('#reporting_manager_display', reportingManager.name || '-');
                    } else {
                        setValue('#reporting_manager_display', '-');
                    }
                } else {
                    setValue('#reporting_manager_display', '-');
                }
            }
            if (data.assigned_offices && Array.isArray(data.assigned_offices)) {
                const branchNames = data.assigned_offices.map(office => office.branch?.name);
                const formattedNames = branchNames.join(', ');
                setValue('#report_office_new', formattedNames);
            }
            if (data.work_mode) {
                // If work_mode is an array, map names, otherwise use the single object's name
                let formattedNames = '';
                if (Array.isArray(data.work_mode)) {
                    formattedNames = data.work_mode.map(mode => mode.name).join(', ');
                } else {
                    formattedNames = data.work_mode.name || '';
                }
                setValue('#workignMode_emp', formattedNames);
            }
            setValue('#designation', data.designation);
            setValue('#emp-address', data.address);
            setValue('#emp-resignation', data.resignation_date);
            setValue('#emp-esi-data',data.esi_no);
            setValue('#emp-pf-data',data.pf_no);
            setValue('#workingMode', data?.work_mode?.name)
            daysContainer.empty();

            // Set hidden field for assign asset
            $('#assign_box_user_id').val(data.id);

            // Helper for date formatting
            const formatDate = (dateStr) => {
                if (!dateStr) return 'N/A';
                const d = new Date(dateStr);
                if (isNaN(d)) return dateStr;
                return d.toLocaleDateString('en-GB') + ' ' + d.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            };

            // Render Assigned Assets
            const assetBody = $('#accordion-asset-list');
            assetBody.empty();
            if (data.assigned_assets && data.assigned_assets.length > 0) {
                data.assigned_assets.forEach(assignment => {
                    const asset = assignment.asset || { asset_name: 'Unknown', serial_number: 'N/A' };
                    const assignedDate = formatDate(assignment.assigned_date);
                    const returnedDate = assignment.returned_date ? formatDate(assignment.returned_date) : '<span class="badge bg-warning">Currently Assigned</span>';
                    
                    assetBody.append(`
                        <tr>
                            <td>${asset.asset_name} (${asset.serial_number})</td>
                            <td>${assignedDate}</td>
                            <td>${returnedDate}</td>
                        </tr>
                    `);
                });
            } else {
                assetBody.append('<tr><td colspan="3" class="text-center">No assets assigned yet.</td></tr>');
            }

            // Render Assigned Assets (Tab)
            const assetTabBody = $('#tab-asset-list');
            assetTabBody.empty();
            if (data.assigned_assets && data.assigned_assets.length > 0) {
                let tabHtml = '<div class="row">';
                data.assigned_assets.forEach(assignment => {
                    const asset = assignment.asset || { asset_name: 'Unknown', serial_number: 'N/A' };
                    const assignedDate = formatDate(assignment.assigned_date);
                    const returnedDateHtml = assignment.returned_date ? `<span class="text-danger">Returned: ${formatDate(assignment.returned_date)}</span>` : `<span class="badge bg-success">Active</span>`;
                    
                    tabHtml += `
                        <div class="col-md-12 d-flex">
                            <div class="card flex-fill mb-2 shadow-none border">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-md bg-primary-light me-2 rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-device-laptop fs-20 text-primary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">${asset.asset_name} - #${asset.serial_number}</h6>
                                                    <div class="d-flex align-items-center">
                                                        <p class="mb-0 text-muted small">Assigned: ${assignedDate} | ${returnedDateHtml}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                tabHtml += '</div>';
                assetTabBody.append(tabHtml);
            } else {
                assetTabBody.append('<div class="text-center p-4"><p>No assets assigned to this employee.</p></div>');
            }
            let days = [];
            try {
                days = JSON.parse(data.working_web_days);
            } catch (e) {
                console.error('Error parsing working_web_days:', e);
            }
            if (Array.isArray(days)) {
                days.forEach(day => {
                    const badge = $('<span>')
                        .addClass('badge bg-success me-1 mb-1')
                        .text(day);
                    daysContainer.append(badge);
                });
            }
            setValue('#emp-about', data.about);
            setValue('#emp-passport', data.passport_no);
            setValue('#emp-passport-exp', data.passport_exp_date);
            setValue('#emp-nationality', data.nationality);
            setValue('#emp-religion', data.religion);
            setValue('#emp-marital', data.marital_status);
            setValue('#emp-spouse', data.marriage_date);
            setValue('#emp-children', data.no_of_children);
            // Profile image or avatar
            const userName = data.user?.name || data.name || 'User';
            const imgSrc = data.profile_image_url;
            if (imgSrc) {
                $('#profile_img').attr('src', imgSrc).removeClass('d-none').on('error', () => showLetterAvatar(
                    userName));
                $('#letter_avatar').addClass('d-none');
            } else {
                showLetterAvatar(userName);
            }
            // Shift details
            const shiftCode = data?.shift?.code ?? 'Not Assigned';
            const shiftId = data?.shift?.id ?? 'Not Found ID';
            $('#emp-assign').html(`
                <div class="emp-card border p-3 rounded mb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Shift:</strong> ${shiftCode}
                        <input type="hidden" id="emp-assign-id" value="${shiftId}" name="id" />
                    </div>
                    <div>
                        <button class="btn btn-sm btn-warning me-2"
                                data-bs-toggle="modal"
                                data-bs-target="#assign_shift_employee"
                                data-shift-id="${shiftId}"
                                onclick="setCurrentEmpId(${empId})">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-bank-btn"
                                data-id="${shiftId}"
                                data-type="shift"
                                type="button">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>
            `);
            // Emergency contacts
            let emergencyData = [];
            $('#emergency-list').empty();
            if (Array.isArray(data.emergency_contacts)) {
                emergencyData = data.emergency_contacts;
                emergencyData.forEach(ec => {
                    $('#emergency-list').append(`
                        <div class="p-3 border-bottom emergency-item" data-id="${ec.id}">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span>${ec.type ? ec.type.charAt(0).toUpperCase() + ec.type.slice(1) : ''}</span>
                                    <h6>
                                        ${ec.name || ''} <i class="ti ti-point-filled text-danger"></i> ${ec.relation || ''}
                                    </h6>
                                </div>
                                <div class="text-end">
                                    <p class="text-dark mb-1">${ec.phone || ''}</p>
                                    <button class="btn btn-sm btn-primary edit-emergency" data-id="${ec.id}">Edit</button>
                                    <button class="btn btn-sm btn-danger delete-bank-btn" data-type="emergency" data-id="${ec.id}">Delete</button>
                                </div>
                            </div>
                        </div>
                    `);
                });
            }
            // Bank details edit
            $(document).off('click', '.edit-bank-btn').on('click', '.edit-bank-btn', function() {
                const bankData = $(this).data('bank');
                const form = $('#employee_edit_form_details')[0];
                form.elements['bank_name'].value = bankData.bank_name || '';
                form.elements['account_number'].value = bankData.account_number || '';
                form.elements['ifsc_code'].value = bankData.ifsc_code || '';
                form.elements['branch'].value = bankData.branch || '';
                form.elements['bank_type'].value = bankData.bank_type || '';
                $('#bank-id-hidden').val(bankData.id || '');
                $('#edit_bank').modal('show');
                cleanupModalBackdrop();
            });
            $('#edit_bank').on('show.bs.modal', function() {
                $('#employee_edit_form_details')[0].reset();
                $('#bank-id-hidden').val('');
                cleanupModalBackdrop();
            });
            // Bank accordion
            const allowedBankTypes = ['primary', 'secondary'];
            const $accordionContainer = $('#bank-accordion-container').empty();
            if (Array.isArray(data.bank_details) && data.bank_details.length) {
                data.bank_details.forEach((bd, index) => {
                    const bankType = allowedBankTypes.includes(bd.bank_type) ? bd.bank_type : 'Other';
                    $accordionContainer.append(`
                        <div class="accordion-item border">
                            <h2 class="accordion-header" id="heading-${index}">
                                <button class="accordion-button ${index !== 0 ? 'collapsed' : ''}" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse-${index}"
                                        aria-expanded="${index === 0}" aria-controls="collapse-${index}">
                                    ${bankType.toUpperCase()} BANK ACCOUNT
                                </button>
                            </h2>
                            <div id="collapse-${index}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}"
                                 aria-labelledby="heading-${index}" data-bs-parent="#bank-accordion-container">
                                <div class="accordion-body">
                                    <div class="row align-items-center mb-2">
                                        <div class="col-md-3">
                                            <span>Bank Name</span>
                                            <h6 class="fw-medium mt-1">${bd.bank_name ?? ''}</h6>
                                        </div>
                                        <div class="col-md-3">
                                            <span>Account No</span>
                                            <h6 class="fw-medium mt-1">${bd.account_number ?? ''}</h6>
                                        </div>
                                        <div class="col-md-3">
                                            <span>IFSC Code</span>
                                            <h6 class="fw-medium mt-1">${bd.ifsc_code ?? ''}</h6>
                                        </div>
                                        <div class="col-md-3">
                                            <span>Branch</span>
                                            <h6 class="fw-medium mt-1">${bd.branch ?? ''}</h6>
                                        </div>
                                        <div class="col-md-3 mt-3">
                                            <button class="btn btn-sm btn-primary edit-bank-btn"
                                                    data-bank='${JSON.stringify(bd)}'
                                                    >Edit</button>
                                            <button class="btn btn-sm btn-danger delete-bank-btn"
                                                    data-type="bank" data-id="${bd.id}">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                });
            }

            $(document).on('click', '.edit-bank-btn', function() {
                const bankData = $(this).data('bank');
                console.log('Editing bank:', bankData); // Verify data

                // Populate the modal fields
                const $modal = $('#edit_bank');
                $modal.find('[name="bank_id"]').val(bankData.id); // Hidden input for ID
                $modal.find('[name="bank_name"]').val(bankData.bank_name);
                $modal.find('[name="account_number"]').val(bankData.account_number);
                $modal.find('[name="ifsc_code"]').val(bankData.ifsc_code);
                $modal.find('[name="branch"]').val(bankData.branch);
                $modal.find('[name="bank_type"]').val(bankData.bank_type);

                // Show the modal
                $modal.modal('show');
            });
            // Bank statutory
            if (data.bank_statutory) {
                const bs = data.bank_statutory;
                ['salary_basis', 'salary_amount', 'payment_type', 'pf_contribution', 'pf_no', 'pf_rate',
                    'pf_additional', 'pf_total', 'esi_contribution', 'esi_no', 'esi_rate', 'esi_additional',
                    'esi_total'
                ]
                .forEach(field => $(`[name="${field}"]`).val(bs[field] ?? ''));
            }
            // Employee edit modal
            const departments = data?.departments || [];
            window.teamOptions = {};
            departments.forEach(dept => {
                window.teamOptions[dept.id] = dept.all_teams || dept.allTeams || [];
            });

            // Store all box users globally for filtering
            let allBoxUsers = [];

            // Initialize Select2 for Reporting Manager (defined globally for access)
            // Initialize Select2 for Reporting Manager with rich templates
            function initializeReportingManagerSelect2($select) {
                if (!$select || !$select.length || !$.fn.select2) return;

                $select.select2({
                    placeholder: 'Search by name, ID, designation, department, or report office',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $select.closest('.modal'),
                    matcher: function(params, data) {
                        if ($.trim(params.term) === '') return data;
                        const term = params.term.toLowerCase();
                        const $element = $(data.element);
                        const name = ($element.data('name') || '').toLowerCase();
                        const employeeId = ($element.data('employee-id') || '').toLowerCase();
                        const designation = ($element.data('designation-name') || '').toLowerCase();
                        const department = ($element.data('department-name') || '').toLowerCase();
                        const reportOffice = ($element.data('report-office') || '').toLowerCase();
                        const text = data.text.toLowerCase();
                        return (name.includes(term) || employeeId.includes(term) || designation.includes(term) ||
                                department.includes(term) || reportOffice.includes(term) || text.includes(term)) ? data : null;
                    },
                    templateResult: reportingManagerTemplateResult,
                    templateSelection: function(data) {
                        return data.text || $(data.element).data('name') || 'Select Reporting Manager';
                    }
                });
            }

            // Custom template for Reporting Manager dropdown results with badges and icons
            function reportingManagerTemplateResult(data) {
                if (!data.id) return data.text;
                const $element = $(data.element);
                const name = $element.data('name') || data.text;
                const employeeId = $element.data('employee-id') || '';
                const designation = $element.data('designation-name') || '';
                const department = $element.data('department-name') || '';
                const highlightType = $element.data('highlight-type') || '';
                
                let icon = '<i class="ti ti-user me-1 text-muted"></i>';
                let badgeClass = 'badge-soft-secondary';
                
                if (highlightType === 'immediate') {
                    icon = '<i class="ti ti-star-filled me-1 text-success"></i>';
                    badgeClass = 'bg-success text-white';
                } else if (highlightType === 'admin') {
                    icon = '<i class="ti ti-crown me-1 text-warning"></i>';
                    badgeClass = 'bg-primary text-white';
                } else if (highlightType === 'higher') {
                    icon = '<i class="ti ti-arrow-up-circle me-1 text-info"></i>';
                    badgeClass = 'bg-info text-white';
                }

                let html = `
                    <div class="d-flex align-items-center p-1 ${highlightType === 'immediate' ? 'border-start border-3 border-success ps-2' : ''}">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center">
                                ${icon}
                                <span class="fw-bold text-dark">${name}</span>
                                ${employeeId ? `<span class="ms-2 badge badge-soft-dark fs-10">#${employeeId}</span>` : ''}
                            </div>
                            <div class="mt-1 d-flex gap-1 flex-wrap">
                                ${designation ? `<span class="badge ${badgeClass} fs-9">${designation}</span>` : ''}
                                ${department ? `<span class="badge badge-soft-dark fs-9">${department}</span>` : ''}
                            </div>
                        </div>
                    </div>`;

                return $(html);
            }

            // Render helper for Reporting Manager options
            function renderReportingManagerOptions($select, employees, selectedId, filterDesignation, selectedDepth) {
                $select.empty().append('<option value="">Select Reporting Manager</option>');
                
                if (employees.length > 0) {
                    employees.forEach(emp => {
                        let reason = "";
                        let highlightType = "";
                        const isHierarchy = filterDesignation && selectedDepth !== null;

                        if (isHierarchy) {
                            const isAdmin = emp.staff_role && emp.staff_role.toLowerCase() === 'admin';
                            const empDepth = parseInt(emp.designation_depth || 0);

                            if (isAdmin) {
                                reason = " (Administrator)";
                                highlightType = "admin";
                            } else if (selectedDepth !== null && empDepth === (selectedDepth - 1)) {
                                reason = " (Immediate Superior)";
                                highlightType = "immediate";
                            } else {
                                reason = " (Higher Hierarchy)";
                                highlightType = "higher";
                            }
                        }

                        const option = $('<option></option>')
                            .attr('value', emp.id)
                            .text(emp.name + reason)
                            .attr('data-name', emp.name)
                            .attr('data-employee-id', emp.employee_id)
                            .attr('data-designation-name', emp.designation_name)
                            .attr('data-department-name', emp.department_name)
                            .attr('data-report-office', emp.report_office_names)
                            .attr('data-highlight-type', highlightType);

                        if (String(emp.id) === String(selectedId)) {
                            option.prop('selected', true);
                        }
                        $select.append(option);
                    });
                } else {
                    $select.append('<option value="">No valid superiors found</option>');
                }

                if ($.fn.select2) {
                    if ($select.data('select2')) $select.select2('destroy');
                    initializeReportingManagerSelect2($select);
                }
            }

            // Centralized populate function to sync with the server-side hierarchy logic
            function populateReportingManagerSelect($select, filterDept = null, filterDesignation = null, selectedId = null) {
                if (!$select || !$select.length) return;

                if (filterDesignation) {
                    const currentEditingEmpId = $('#edit_employee_details_id').val();
                    $select.empty().append('<option value="">Loading superiors...</option>').prop('disabled', true);
                    
                    $.ajax({
                        url: '{{ route('employee.get.box.users', ['box_slug' => request()->route('box_slug')]) }}',
                        type: 'GET',
                        data: { 
                            superior_to_designation: filterDesignation, 
                            exclude_id: currentEditingEmpId || null,
                            include_id: selectedId || null
                        },
                        success: function(response) {
                            $select.prop('disabled', false);
                            if (response.status && Array.isArray(response.data)) {
                                let selectedDepth = null;
                                if (window.teamOptions) {
                                    Object.values(window.teamOptions).forEach(deptTeams => {
                                        if (selectedDepth === null) {
                                            const found = deptTeams.find(t => String(t.id) === String(filterDesignation));
                                            if (found) {
                                                selectedDepth = (found.depth !== undefined && found.depth !== null) ? parseInt(found.depth) : null;
                                            }
                                        }
                                    });
                                }
                                renderReportingManagerOptions($select, response.data, selectedId, filterDesignation, selectedDepth);

                                // Auto-select logic for hierarchy optimization
                                if (!selectedId && response.data.length > 0 && selectedDepth !== null) {
                                    const targetDepth = selectedDepth - 1;
                                    const firstImmediate = response.data.find(u => parseInt(u.designation_depth || 0) === targetDepth);
                                    if (firstImmediate) {
                                        $select.val(firstImmediate.id).trigger('change');
                                    }
                                }
                            } else {
                                $select.empty().append('<option value="">No superiors found</option>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching superior users:', error);
                            $select.prop('disabled', false).empty().append('<option value="">Error loading superiors</option>');
                        }
                    });
                } else {
                    // Fallback to basic list if no designation selected
                    $select.empty().append('<option value="">Select Designation First</option>');
                }
            }
            
            // Flag to prevent designation tree from opening during initial form population
            window.isPopulatingEditForm = false;

            $('#edit_employee').off('show.bs.modal').on('show.bs.modal', function() {
                window.isPopulatingEditForm = true;
                const form = document.getElementById('employee_edit_form');
                if (!form) return;

                const is_admin = String(data.staff_role).toLowerCase() === 'admin';
                if (is_admin) {
                    if ($('#department-info').find('option[value="Owner"]').length === 0) {
                        $('#department-info').append(new Option('Owner', 'Owner'));
                    }
                    $('#department-info').val('Owner').trigger('change').prop('disabled', true);
                    $('#designation-info_visible').val('Owner');
                    $('#designation-info').val('Owner');
                    $('#designation-info_visible').closest('.input-group').find('button').prop('disabled', true);

                    // Hide containers
                    $('#department-info').closest('.col-md-6').hide();
                    $('#designation-info_visible').closest('.col-md-6').hide();
                    $('#reporting_manager_edit_section').hide();
                    $('#emp-joining-info').closest('.col-md-6').hide();
                    
                    // Employee Type should be NA for admin
                    $('#emp-type-info').val('NA').prop('disabled', true);
                    $('#emp-joining-info').prop('disabled', true);
                } else {
                    $('#department-info').prop('disabled', false);
                    $('#designation-info_visible').closest('.input-group').find('button').prop('disabled', false);
                    
                    // Show containers
                    $('#department-info').closest('.col-md-6').show();
                    $('#designation-info_visible').closest('.col-md-6').show();
                    $('#reporting_manager_edit_section').show();
                    $('#emp-joining-info').closest('.col-md-6').show();
                    
                    $('#emp-type-info').prop('disabled', false);
                    $('#emp-joining-info').prop('disabled', false);
                }

                // Store update route in form data for image cropper
                const updateRoute = '{{ route('employee.update', ['box_slug' => request()->route('box_slug')]) }}';
                $('#employee_edit_form').data('update-url', updateRoute);

                // Set employee ID in hidden input
                $('#edit_employee_details_id').val(data.id);

                // Don't clear cropped_image when modal opens - preserve it if user already cropped
                // It will only be cleared when a new file is selected
                // $('#edit_details_cropped_image').val('');

                // Fetch Employee Types
                const $empTypeSelect = $('#emp-type-info');
                // Only fetch if not already populated or has only default option
                if ($empTypeSelect.children().length <= 1) {
                    $empTypeSelect.empty().append('<option value="">Loading...</option>');
                    $.ajax({
                        url: '{{ route("employee.get.types", ["box_slug" => request()->route("box_slug")]) }}',
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                             $empTypeSelect.empty().append('<option value="">Select</option>');
                             if(response.status === 200 && response.data) {
                                 response.data.forEach(type => {
                                     $empTypeSelect.append(`<option value="${type.id}">${type.name}</option>`);
                                 });
                                 if (data.emp_type) {
                                     $empTypeSelect.val(data.emp_type);
                                 }
                             }
                        },
                        error: function() {
                            $empTypeSelect.empty().append('<option value="">Error loading types</option>');
                        }
                    });
                } else {
                     if (data.emp_type) {
                         $empTypeSelect.val(data.emp_type);
                     }
                }

                // Initialize Reporting Manager dropdown with server-side hierarchy filtering
                const $reportingManagerSelect = $('#reporting_manager-info');
                populateReportingManagerSelect($reportingManagerSelect, data.department, data.designation, data.reporting_manager_id);

                const $avatarContainer = $('#employee_edit_form').find('.frames .uploaded_image');
                const profileImage = data.profile_image_url;
                const userName = data.user?.name || data.first_name || 'U';
                if ($avatarContainer.length > 0) {
                    if (profileImage) {
                        $avatarContainer.html(`<img src="${profileImage}" alt="img" class="rounded-circle w-100 h-100 object-fit-cover" id="editprofile_img" />`);
                    } else {
                        $avatarContainer.html(`<i class="ti ti-photo text-gray-2 fs-16"></i>`);
                    }
                }
                const deptSelect = form.elements['department'];
                deptSelect.innerHTML = '<option value="">Select</option>';
                departments.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept.id;
                    option.textContent = dept.name + ' (Dept)';
                    deptSelect.appendChild(option);
                });
                
                const designationSelect = form.elements['designation'];

                function populateDesignations(departmentId) {
                    designationSelect.innerHTML = '<option value="">Select</option>';
                    const dept = departments.find(d => d.id == departmentId);
                    if (dept?.all_teams) {
                        dept.all_teams.forEach(team => {
                            const option = document.createElement('option');
                            option.value = team.id;
                            option.textContent = team.name + ' (Team)';
                            designationSelect.appendChild(option);
                        });
                    }

                    // Filter Reporting Manager dropdown when department changes
                    filterReportingManagersBySelection();
                }

                // Filter Reporting Manager dropdown based on department and designation using server-side logic
                function filterReportingManagersBySelection() {
                    const selectedDept = deptSelect.value;
                    const selectedDesignation = designationSelect.value;
                    const currentSelected = $('#reporting_manager-info').val();
                    const $reportingManagerSelect = $('#reporting_manager-info');

                    populateReportingManagerSelect($reportingManagerSelect, selectedDept || null, selectedDesignation || null, currentSelected);
                }

                // Note: initializeReportingManagerSelect2 is now defined globally above (outside this modal handler)

                deptSelect.onchange = () => {
                    if (window.isPopulatingEditForm) return;

                    const val = deptSelect.value;
                    if (val && typeof window.openDesignationTree === 'function') {
                        window.openDesignationTree(val, '#designation-info');
                    }
                    // Clear designation when department changes
                    $('#designation-info').val('').trigger('change');
                    $('#designation-info_visible').val('');
                };

                // Filter Reporting Manager when designation changes
                $(document).on('change', '#designation-info', function() {
                    filterReportingManagersBySelection();
                });
                function setValueById(id, value) {
                    const el = document.getElementById(id);
                    if (!el) return;
                    
                    const $el = $(el);
                    if (el.tagName.toLowerCase() === 'select') {
                        $el.val(value ?? '').trigger('change');
                        // Special handling for department to refresh designations
                        if (id === 'department-info') {
                            // Department set, designation logic handled by deptSelect.onchange if needed
                            // But here we might be populating for initial load
                        }
                    } else {
                        el.value = value ?? '';
                    }
                }
                function formatDateToISO(dateStr) {
                    if (!dateStr) return '';
                    const parts = dateStr.includes('-') ? dateStr.split('-') : dateStr.split('/');
                    if (parts.length !== 3) return '';
                    const [dd, mm, yyyy] = parts;
                    return `${yyyy}-${mm.padStart(2, '0')}-${dd.padStart(2, '0')}`;
                }
                const reportOfficeSelect = $('#report_office-info');
                reportOfficeSelect.empty().append('<option value="">Loading...</option>');
                $.ajax({
                    url: '{{ route('get.all.branchOffice', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        reportOfficeSelect.empty();

                        // Handle previously selected offices first
                        let selectedOffices = [];
                        if (data.report_office) {
                            // Support both array & string (comma-separated)
                            if (typeof data.report_office === 'string') {
                                try {
                                    selectedOffices = JSON.parse(data.report_office);
                                } catch {
                                    selectedOffices = data.report_office.split(',').map(s =>
                                        s.trim());
                                }
                            } else if (Array.isArray(data.report_office)) {
                                selectedOffices = data.report_office;
                            }
                        }

                        // Also get from assigned_offices if available
                        if (data.assigned_offices && Array.isArray(data.assigned_offices)) {
                            const officeIds = data.assigned_offices.map(office => office.branch_office_id || office.id);
                            selectedOffices = [...new Set([...selectedOffices, ...officeIds])];
                        }

                            // Populate ALL offices in the dropdown (not just selected ones)
                            if (response.status && Array.isArray(response.data) && response.data.length > 0) {
                                // Add all branches to the dropdown
                                response.data.forEach(branch => {
                                    reportOfficeSelect.append(
                                        `<option value="${branch.id}">${branch.name}</option>`
                                    );
                                });
                            } else {
                                reportOfficeSelect.append(
                                    '<option value="">No branches found</option>');
                            }

                            // Apply selection to pre-select the ones that are already assigned
                            if (selectedOffices.length > 0) {
                                reportOfficeSelect.val(selectedOffices).trigger('change');
                            }

                        // Display selected offices as removable badges
                        renderSelectedOffices(selectedOffices, response.data || []);

                        // Initialize Select2 for better multiple selection UX
                        if ($.fn.select2) {
                            reportOfficeSelect.select2({
                                placeholder: 'Select Report Office(s)',
                                width: '100%',
                                allowClear: true
                            });

                            // Update badges when selection changes
                            reportOfficeSelect.on('change', function() {
                                const selected = $(this).val() || [];
                                renderSelectedOffices(selected, response.data || []);
                            });
                        }
                    },
                    error: function(err) {
                        console.error('Failed to load branch offices:', err);
                        reportOfficeSelect.empty().append(
                            '<option value="">Error loading branches</option>');
                    }
                });

                // Function to render selected offices as removable badges (second instance)
                function renderSelectedOffices(selectedIds, allBranches) {
                    const container = $('#selected-offices-container');
                    container.empty();

                    if (!selectedIds || selectedIds.length === 0) {
                        return;
                    }

                    // Get employee ID (box_user.id) - this is the database ID, not employee_id code
                    const employeeDbId = data.id || data.box_user_id || null;

                    if (!employeeDbId) {
                        console.error('Employee database ID not found in data:', data);
                        return;
                    }

                    selectedIds.forEach(officeId => {
                        const branch = allBranches.find(b => b.id == officeId);
                        if (branch) {
                            const badge = $(`
                                <span class="badge bg-primary d-inline-flex align-items-center gap-1" data-office-id="${officeId}" data-employee-id="${employeeDbId}">
                                    ${branch.name}
                                    <button type="button" class="btn-close btn-close-white" style="font-size: 0.7em;" aria-label="Remove"></button>
                                </span>
                            `);

                            // Handle remove click
                            badge.find('.btn-close').on('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();

                                const badgeElement = $(this).closest('.badge');
                                const officeIdToRemove = badgeElement.data('office-id');
                                const employeeIdToUse = badgeElement.data('employee-id');

                                if (!employeeIdToUse) {
                                    console.error('Employee ID not found in badge data');
                                    showToast('Employee ID not found.', false);
                                    return;
                                }

                                if (!officeIdToRemove) {
                                    console.error('Office ID not found in badge data');
                                    showToast('Office ID not found.', false);
                                    return;
                                }

                                if (confirm(`Are you sure you want to remove "${branch.name}" from Report Office?`)) {
                                    if (typeof window.removeOfficeAssignment === 'function') {
                                        window.removeOfficeAssignment(employeeIdToUse, officeIdToRemove, branch.name);
                                    } else {
                                        showToast('Remove function not available.', false);
                                    }
                                }
                            });

                            container.append(badge);
                        }
                    });
                }

                // Set all form field values
                setValueById('first_name-info', data.first_name);
                setValueById('last_name-info', data.last_name);
                setValueById('emp-gender-info', data.gender);
                setValueById('emp-birthday-info', formatDateToISO(data.birthday));
                setValueById('employee_code-info', data.employee_id);
                setValueById('emp-joining-info', formatDateToISO(data.joining_date));
                setValueById('usernames-info', data.username);
                setValueById('emp-pf-no', data.pf_no);
                setValueById('emp-esi-no', data.esi_no);
                setValueById('emp-resignation-date',data.resignation_date);
                setValueById('emp-email-info', data.email);
                setValueById('emp-phone-info', data.phone);
                setValueById('company-info', data.company);
                
                // Populate Department and refresh Designations
                const deptId = data.department_id || data.department;
                setValueById('department-info', deptId);

                // Set designation after department has populated its options
                const designationId = data.designation_id || data.designation;
                setValueById('designation-info', designationId);

                // Filter Reporting Manager based on selected department and designation
                // This will filter the dropdown to show only users from the same department/team
                setTimeout(() => {
                    filterReportingManagersBySelection();

                    // Set selected reporting manager after filtering
                    if (data.reporting_manager_id) {
                        const $reportingManagerSelect = $('#reporting_manager-info');
                        if ($reportingManagerSelect.length && $reportingManagerSelect.find(`option[value="${data.reporting_manager_id}"]`).length > 0) {
                            $reportingManagerSelect.val(data.reporting_manager_id);
                            if ($.fn.select2 && $reportingManagerSelect.data('select2')) {
                                $reportingManagerSelect.trigger('change.select2');
                            }
                        }
                    }
                }, 200);
                setValueById('emp-address-info', data.address);
                setValueById('emp-about-info', data.about);

                // Reset population flag after everything is set
                setTimeout(() => {
                    window.isPopulatingEditForm = false;
                }, 500);
                cleanupModalBackdrop();
            });
            // Personal info modal - Fixed to properly format date fields
            $('#edit_personal').off('show.bs.modal').on('show.bs.modal', function() {
                const form = document.getElementById('employee_personal_info');
                if (!form) return;

                // Format date for HTML date inputs - handles multiple formats
                function formatDateForInput(dateStr) {
                    if (!dateStr) return '';

                    // If already in YYYY-MM-DD format, return as is
                    if (dateStr.match(/^\d{4}-\d{2}-\d{2}$/)) {
                        return dateStr;
                    }

                    // If in YYYY-MM-DD HH:MM:SS format, extract date part
                    if (dateStr.match(/^\d{4}-\d{2}-\d{2}\s/)) {
                        return dateStr.split(' ')[0];
                    }

                    // Try to parse as Date object
                    const date = new Date(dateStr);
                    if (!isNaN(date.getTime())) {
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    }

                    // Fallback: try DD-MM-YYYY or DD/MM/YYYY format
                    const parts = dateStr.includes('-') ? dateStr.split('-') : dateStr.split('/');
                    if (parts.length === 3) {
                        const [dd, mm, yyyy] = parts;
                        return `${yyyy}-${mm.padStart(2, '0')}-${dd.padStart(2, '0')}`;
                    }

                    return '';
                }

                // Populate text fields
                if (form.elements['passport_no']) {
                    form.elements['passport_no'].value = data.passport_no ?? '';
                }
                if (form.elements['nationality']) {
                    form.elements['nationality'].value = data.nationality ?? '';
                }
                if (form.elements['religion']) {
                    form.elements['religion'].value = data.religion ?? '';
                }
                if (form.elements['no_of_children']) {
                    form.elements['no_of_children'].value = data.no_of_children ?? '';
                }

                // Populate date fields with proper formatting
                if (form.elements['passport_exp_date']) {
                    form.elements['passport_exp_date'].value = formatDateForInput(data.passport_exp_date);
                }

                // Populate marital status and handle anniversary date
                if (form.elements['marital_status']) {
                    form.elements['marital_status'].value = data.marital_status ?? '';
                    // Trigger change event for select if using a custom select library
                    if (typeof $(form.elements['marital_status']).select !== 'undefined') {
                        $(form.elements['marital_status']).trigger('change');
                    }
                }

                // Show/hide anniversary date field based on marital status
                if (data.marital_status === 'married') {
                    $('#marriage_date_field').removeClass('d-none');
                    if (form.elements['marriage_date']) {
                        form.elements['marriage_date'].value = formatDateForInput(data.marriage_date);
                    }
                } else {
                    $('#marriage_date_field').addClass('d-none');
                    if (form.elements['marriage_date']) {
                        form.elements['marriage_date'].value = '';
                    }
                }

                cleanupModalBackdrop();
            });
            function renderExperienceList(experiences) {
                $('#experience-list').empty();
                if (Array.isArray(experiences)) {
                    experiences.forEach(exp => {
                        $('#experience-list').append(`
                            <div class="mb-3 border rounded p-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="d-inline-flex align-items-center fw-medium">${exp.company_name || ''}</h6>
                                        <span class="d-flex align-items-center badge bg-secondary-transparent mt-1">
                                            <i class="ti ti-point-filled me-1"></i>${exp.position || ''}
                                        </span>
                                    </div>
                                    <p class="text-dark mb-0">${exp.start_date || ''} - ${exp.end_date || ''}</p>
                                </div>
                                <div class="text-end mt-2">
                                    <button class="btn btn-sm btn-primary edit-experience"
                                            data-id="${exp.id}"
                                            data-company_name="${exp.company_name}"
                                            data-position="${exp.position}"
                                            data-start_date="${exp.start_date}"
                                            data-end_date="${exp.end_date}"
                                            data-current="${exp.current_working ? 1 : 0}">
                                        Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-bank-btn" data-type="experience" data-id="${exp.id}">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        `);
                    });
                }
            }
            renderExperienceList(data.experiences);
            $(document).off('click', '.edit-experience').on('click', '.edit-experience', function() {
                const $btn = $(this);
                const form = document.getElementById('employee_details_exp');
                if (!form) return;
                form.elements['id'].value = $btn.data('id') || '';
                form.elements['company_name'].value = $btn.data('company_name') || '';
                form.elements['position'].value = $btn.data('position') || '';
                form.elements['start_date'].value = $btn.data('start_date') || '';
                form.elements['end_date'].value = $btn.data('end_date') || '';
                const currentWorking = $btn.data('current');
                form.elements['current_working'].checked = currentWorking === 1 || currentWorking ===
                    "on";
                new bootstrap.Modal(document.getElementById('edit_experience')).show();
                cleanupModalBackdrop?.();
            });
            // Education list
            let educationData = [];
            $('#education-list').empty();
            if (Array.isArray(data.educations)) {
                educationData = data.educations;
                data.educations.forEach(edu => {
                    $('#education-list').append(`
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fw-normal">${edu.institute || ''}</span>
                                    <h6 class="mt-1">${edu.degree || ''}</h6>
                                </div>
                                <p class="text-dark">${edu.start_year || ''} - ${edu.end_year || ''}</p>
                            </div>
                            <div class="text-end mt-2">
                                <button class="btn btn-sm btn-primary edit-education" data-id="${edu.id}">Edit</button>
                                <button class="btn btn-sm btn-danger delete-bank-btn" data-type="education" data-id="${edu.id}">Delete</button>
                            </div>
                        </div>
                    `);
                });
            }
            $(document).off('click', '.edit-education').on('click', '.edit-education', function() {
                const edu = educationData.find(item => item.id === $(this).data('id'));
                if (edu) {
                    $('#education-id').val(edu.id);
                    ['institute', 'degree', 'start_year', 'end_year'].forEach(field => {
                        $(`input[name="${field}"]`).val(edu[field] || '');
                    });
                    new bootstrap.Modal(document.getElementById('edit_education')).show();
                    cleanupModalBackdrop();
                } else {
                    showToast('Education record not found.', false);
                }
            });
            // Family list
            let familyData = [];
            $('#family-list').empty();
            if (Array.isArray(data.families)) {
                familyData = data.families;
                data.families.forEach(member => {
                    $('#family-list').append(`
                        <div class="row mb-3 align-items-center border rounded p-2">
                            <div class="col-md-2"><span>Name</span><h6>${member.name || ''}</h6></div>
                            <div class="col-md-2"><span>Relationship</span><h6>${member.relationship || ''}</h6></div>
                            <div class="col-md-2"><span>Date of Birth</span><h6>${member.dob || ''}</h6></div>
                            <div class="col-md-2"><span>Contact</span><h6>${member.contact || ''}</h6></div>
                            <div class="col-md-2">
                                <button class="btn btn-sm btn-primary edit-family" data-id="${member.id}">Edit</button>
                                <button class="btn btn-sm btn-danger delete-bank-btn" data-type="family" data-id="${member.id}">Delete</button>
                            </div>
                        </div>
                    `);
                });
            }
            $(document).off('click', '.edit-family').on('click', '.edit-family', function() {
                const member = familyData.find(item => item.id == $(this).data('id'));
                if (member) {
                    $('#family-id').val(member.id);
                    const form = document.getElementById('family-info-form');
                    if (!form) return;
                    ['name', 'relationship', 'dob', 'contact'].forEach(field => {
                        form.elements[field].value = member[field] || '';
                    });
                    new bootstrap.Modal(document.getElementById('edit_familyinformation')).show();
                    cleanupModalBackdrop();
                } else {
                    showToast('Family record not found.', false);
                }
            });
            $(document).off('click', '.edit-emergency').on('click', '.edit-emergency', function() {
                const member = emergencyData.find(item => item.id === $(this).data('id'));
                if (member) {
                    $('#emergency-id').val(member.id);
                    const form = document.getElementById('employee_emergency_c_dtails');
                    if (!form) return;
                    const index = member.type === 'secondary' ? 1 : 0;
                    ['name', 'relation', 'phone', 'phone_alt'].forEach(field => {
                        form.elements[`contacts[${index}][${field}]`].value = member[field] ||
                            '';
                    });
                    new bootstrap.Modal(document.getElementById('edit_emergency')).show();
                    cleanupModalBackdrop();
                }
            });
            // Biometric Mappings List
            const biometricBody = $('#primaryBorderEight .accordion-body');
            biometricBody.empty();
            if (Array.isArray(data.machine_mappings) && data.machine_mappings.length > 0) {
                let listHtml = '';
                data.machine_mappings.forEach(mapping => {
                    const machine = mapping.machine || { serial_no: '', machine_name: '' };
                    listHtml += `
                        <div class="p-3 border-bottom biometric-item" data-id="${mapping.id}">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6>${machine.serial_no} - ${machine.machine_name}</h6>
                                    <p class="text-muted mb-0">Machine User ID: ${mapping.machine_user_id}</p>
                                </div>
                                <div class="text-end">
                                    <button class="btn btn-sm btn-primary edit-biometric me-1"
                                            data-id="${mapping.id}"
                                            data-biometric-machine-id="${mapping.biometric_machine_id}"
                                            data-machine-user-id="${mapping.machine_user_id}">Edit</button>
                                    <button class="btn btn-sm btn-danger delete-bank-btn" data-type="biometric" data-id="${mapping.id}">Delete</button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                biometricBody.html(listHtml);
            } else {
                biometricBody.html('<p class="text-muted p-3">No biometric mappings found.</p>');
            }
        }
        // Reset emergency form
        $('#edit_emergency').on('hidden.bs.modal', function() {
            const form = this.querySelector('form');
            form.reset();
            form.querySelector('[name="contacts[0][type]"]').value = 'primary';
            form.querySelector('[name="contacts[1][type]"]').value = 'secondary';
            cleanupModalBackdrop();
        });
        // Assign shift modal
        $(document).on('click', '[data-bs-target="#assign_shift_employee"]', function(e) {
            e.stopPropagation();
            const selectedShiftId = $(this).data('shift-id') ?? '';
            $('#shiftSelect').html('<option value="">Loading...</option>');
            $.ajax({
                url: '{{ route('get.assign.shift', ['box_slug' => request()->route('box_slug')]) }}',
                type: 'GET',
                success: function(response) {
                    const options = response.data?.length ? [
                            '<option value="">-- Select Shift --</option>'
                        ].concat(
                            response.data.map(shift =>
                                `<option value="${shift.id}">${shift.code}</option>`)
                        ).join('') :
                        '<option value="">No shifts found</option>';
                    $('#shiftSelect').html(options).val(selectedShiftId || '');
                },
                error: function() {
                    $('#shiftSelect').html(
                        '<option value="">Error loading shifts</option>');
                }
            });
        });
        // Assign shift form submit
        $('#assignShiftForm').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const submitBtn = $form.find('button[type="submit"]');
            submitBtn.prop('disabled', true);

            // Get fresh CSRF token
            const csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';

            // Use FormData to ensure proper encoding
            const formData = new FormData(this);
            formData.append('employee_id', empId);
            formData.set('_token', csrfToken); // Ensure fresh token

            $.ajax({
                url: '{{ route('assign.employee.shift', ['box_slug' => request()->route('box_slug')]) }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    $('#assign_shift_employee').modal('hide');
                    cleanupModalBackdrop();
                    fetchEmployeeDetails(empId, true);
                    showToast(response.message || 'Shift assigned successfully!', true);
                },
                error: function(xhr) {
                    let msg = 'Failed to assign shift.';

                    // Handle CSRF token expiration (419 status code)
                    if (xhr.status === 419) {
                        msg = 'Session expired. Please refresh the page and try again.';
                        showToast(msg, false);
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                        return;
                    }

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }

                    showToast(msg, false);
                    console.error(xhr.responseJSON);
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                }
            });
        });
        function storeEmployeeData(empId, data) {
            const localKey = `employee_${empId}`;
            try {
                localStorage.setItem(localKey, JSON.stringify(data));
            } catch (e) {
                console.error('Error storing employee data in localStorage:', e);
            }
        }
        function getEmployeeData(empId) {
            const localKey = `employee_${empId}`;
            const data = localStorage.getItem(localKey);
            if (data) {
                try {
                    return JSON.parse(data);
                } catch (e) {
                    console.error('Error parsing employee data from localStorage:', e);
                    return null;
                }
            }
            return null;
        }
        // Remove specific employee data from localStorage
        function removeEmployeeData(empId) {
            const localKey = `employee_${empId}`;
            localStorage.removeItem(localKey);
        }
        // Fetch employee details
        function fetchEmployeeDetails(empId, forceRefresh = false) {
            const localKey = `employee_${empId}`;
            if (forceRefresh) {
                // Force refresh: Clear cache and fetch from API
                localStorage.removeItem(localKey);
            }
            const cachedData = getEmployeeData(empId);
            if (cachedData && !forceRefresh) {
                // Use cached data if available and not forcing refresh
                populateEmployeeData(cachedData);
                return;
            }
            $.ajax({
                url: "{{ route('employee.details.admin', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'GET',
                data: {
                    req_emp_id: empId
                },
                success: function(response) {
                    if (response?.data) {
                        populateEmployeeData(response.data);
                        storeEmployeeData(empId, response
                            .data); // Store all fetched data in localStorage
                    } else {
                        showToast('Employee data not found.', false);
                    }
                },
                error: function() {
                    showToast('Failed to fetch employee details.', false);
                }
            });
        }
        cleanupModalBackdrop();
        fetchEmployeeDetails(empId);

        // Load available assets when modal is opened
        $(document).on('click', '.btn-open-assign-modal', function() {
            var boxSlug = '{{ request()->route('box_slug') }}';
            var select = $('#available_asset_id_details');
            select.html('<option value="">Loading...</option>');
            $.ajax({
                url: "/" + boxSlug + "/assets/available",
                type: "GET",
                success: function(response) {
                    if(response.success) {
                        var options = '<option value="">Choose Asset</option>';
                        $.each(response.data, function(i, asset) {
                            options += '<option value="' + asset.id + '">' + asset.asset_name + ' (' + asset.serial_number + ')</option>';
                        });
                        select.html(options);
                    }
                }
            });
        });

        // Quick Assign Form Submit
        $('#quick_assign_asset_form').on('submit', function(e) {
            e.preventDefault();
            var boxSlug = '{{ request()->route('box_slug') }}';
            var formData = $(this).serialize();
            $.ajax({
                url: "/" + boxSlug + "/assets/assign",
                type: "POST",
                data: formData,
                success: function(response) {
                    if(response.success) {
                        $('#modal_assign_asset').modal('hide');
                        showToast(response.message, true);
                        fetchEmployeeDetails(empId, true);
                    } else {
                        showToast(response.message, false);
                    }
                },
                error: function(xhr) {
                    showToast('Failed to assign asset.', false);
                }
            });
        });

        window.addEventListener('beforeunload', function() {
            removeEmployeeData(empId);
        });
    });

    function toggleLocationTracking(checkbox) {
        const isEnabled = $(checkbox).is(':checked');
        const empId = new URLSearchParams(window.location.search).get('emp_id');
        
        $.ajax({
            url: "{{ route('employee.toggle-location-tracking', ['box_slug' => request()->route('box_slug')]) }}",
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                emp_id: empId,
                status: isEnabled ? 1 : 0
            },
            success: function(res) {
                if(res.success) {
                    showToast(res.message, true);
                    if (isEnabled) {
                        $('#employeeTrackingMap').html('<span class="text-muted">Loading map...</span>');
                        // Needs user_id, which we can get from the cached employee data
                        const cachedData = getEmployeeData(empId);
                        if(cachedData && cachedData.user_id) {
                            loadTrackingMap(cachedData.user_id);
                        }
                    } else {
                        $('#employeeTrackingMap').html('<span class="text-muted">Tracking is disabled for this user.</span>');
                    }
                } else {
                    $(checkbox).prop('checked', !isEnabled);
                    showToast(res.message, false);
                }
            },
            error: function() {
                $(checkbox).prop('checked', !isEnabled);
                showToast('An error occurred. Please try again.', false);
            }
        });
    }

    let empTrackingMap = null;
    let empTrackingPolyline = null;
    let empTrackingMarkers = [];

    function loadTrackingMap(userId) {
        if (typeof google === 'object' && typeof google.maps === 'object') {
            initEmpTrackingMap(userId);
        } else {
            var apiKey = 'AIzaSyCix326VJYzGXyMzF7_yTD_vbn5DBMHZL4';
            var script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key=' + apiKey + '&callback=initEmpTrackingMapCb';
            script.async = true;
            script.defer = true;
            window.initEmpTrackingMapCb = function() {
                initEmpTrackingMap(userId);
            };
            document.head.appendChild(script);
        }
    }

    function initEmpTrackingMap(userId) {
        empTrackingMap = new google.maps.Map(document.getElementById('employeeTrackingMap'), {
            zoom: 12,
            center: { lat: 20.5937, lng: 78.9629 }, // Default to India
            mapTypeId: 'terrain'
        });

        // Get current date (YYYY-MM-DD)
        const date = new Date().toLocaleDateString('en-CA');

        $.ajax({
            url: "{{ route('fieldstracking.location.route', ['box_slug' => request()->route('box_slug')]) }}",
            type: 'GET',
            data: { user_id: userId, date: date },
            success: function(res) {
                if (res.success && res.data.points.length > 0) {
                    let points = res.data.points;
                    let routeCoords = [];
                    let bounds = new google.maps.LatLngBounds();

                    points.forEach((point, index) => {
                        let position = { lat: parseFloat(point.latitude), lng: parseFloat(point.longitude) };
                        routeCoords.push(position);
                        bounds.extend(position);

                        if (index === 0 || index === points.length - 1) {
                            let label = index === 0 ? 'Start' : 'End';
                            let iconColor = index === 0 ? 'green-dot.png' : 'red-dot.png';
                            
                            let marker = new google.maps.Marker({
                                position: position,
                                map: empTrackingMap,
                                title: label,
                                icon: `http://maps.google.com/mapfiles/ms/icons/${iconColor}`
                            });
                            empTrackingMarkers.push(marker);
                        }
                    });

                    empTrackingPolyline = new google.maps.Polyline({
                        path: routeCoords,
                        geodesic: true,
                        strokeColor: '#007bff',
                        strokeOpacity: 1.0,
                        strokeWeight: 4
                    });
                    empTrackingPolyline.setMap(empTrackingMap);

                    empTrackingMap.fitBounds(bounds);

                    // Update metrics table
                    if (res.data.metrics) {
                        $('#tracking-metric-date').text(res.data.metrics.date || '-');
                        $('#tracking-metric-check-in').text(res.data.metrics.check_in || '-');
                        $('#tracking-metric-check-out').text(res.data.metrics.check_out || '-');
                        $('#tracking-metric-total-time').text(res.data.metrics.total_time || '-');
                        $('#tracking-metric-rest').text(res.data.metrics.rest_time || '-');
                        $('#tracking-metric-mobility').text(res.data.metrics.mobility_time || '-');
                    }

                } else {
                    $('#employeeTrackingMap').html('<span class="text-muted">No tracking data available for today.</span>');
                    $('#tracking-metric-date, #tracking-metric-check-in, #tracking-metric-check-out, #tracking-metric-total-time, #tracking-metric-rest, #tracking-metric-mobility').text('-');
                }
            },
            error: function() {
                $('#employeeTrackingMap').html('<span class="text-danger">Failed to load map data.</span>');
            }
        });
    }
</script>
<style>
        .modal-backdrop {
            opacity: 0.5 !important;
            background-color: #000 !important;
        }

        .modal-open .modal-backdrop.show {
            z-index: 1040 !important;
        }
        .modal {
            z-index: 1050 !important;
        }
    </style>
    @include('frontend.employess.components.designation_tree_modal')
@endpush
@endsection
