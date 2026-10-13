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
                        <i class="ti ti-arrow-left me-2"></i>Employee Details </a>
                </h6>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                <div class="mb-2">
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
                        <span class="avatar avatar-xl avatar-rounded border border-2 border-white m-auto d-flex mb-2 position-relative overflow-hidden">
    <!-- Profile Image -->
    <img id="profile_img"
         class="w-100 h-100 object-fit-cover"
         alt="Profile Image"
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
                                    <p class="text-dark" id="emp-id">CLT-0024 </p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ti ti-id me-2"></i>
                                        Department </span>
                                    <p class="text-dark" id="department_new">.. </p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ti ti-id me-2"></i>
                                        Designation </span>
                                    <p class="text-dark" id="designation_new">... </p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ti ti-star me-2"></i>
                                        Team
                                    </span>
                                    <button type="button" class="btn btn-light text-dark" id="emp-designation"
                                        data-bs-toggle="modal" data-bs-target="#TeamsModel"
                                        data-designation-id="department_id">
                                        UI/UX Design
                                    </button>




                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ti ti-calendar-check me-2"></i>
                                        Date Of Join
                                    </span>
                                    <p class="text-dark" id="emp-joining">1st Jan 2023 </p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ti ti-calendar-check me-2"></i>
                                        Report Office
                                    </span>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm avatar-rounded me-2">
                                            <img src="{{ asset('assets/img/profiles/avatar-12.jpg') }}"
                                                alt="Img" />
                                        </span>
                                        <p class="text-gray-9 mb-0" id="report_office_new">Doglas Martini </p>
                                    </div>
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
                                    Employment of spouse
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
                                            {{-- <div class="row">
                                                    <div class="col-md-3">
                                                        <span class="d-inline-flex align-items-center">
                                                            ____ Name
                                                        </span>
                                                        <h6 class="d-flex align-items-center fw-medium mt-1"
                                                            id="bank-name">Swiz
                                                            Intenational Bank </h6>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <span class="d-inline-flex align-items-center">
                                                            ____ account no
                                                        </span>
                                                        <h6 class="d-flex align-items-center fw-medium mt-1"
                                                            id="bank-acc">159843014641
                                                        </h6>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <span class="d-inline-flex align-items-center">
                                                            ____ Code
                                                        </span>
                                                        <h6 class="d-flex align-items-center fw-medium mt-1"
                                                            id="bank-ifsc">ICI24504 </h6>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <span class="d-inline-flex align-items-center">
                                                            ______
                                                        </span>
                                                        <h6 class="d-flex align-items-center fw-medium mt-1"
                                                            id="bank-branch">Alabama USA
                                                        </h6>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <button class="btn btn-sm btn-primary" id="edit-bank-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#edit_bank">Edit</button>
                                                        <button class="btn btn-sm btn-danger"
                                                            id="delete-bank-btn">Delete</button>
                                                    </div>

                                                </div> --}}
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
                                                        <a href="#" class="btn btn-sm btn-icon ms-auto"
                                                            data-bs-toggle="modal" data-bs-target="#edit_employee"><i
                                                                class="ti ti-edit"></i></a>
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
                                                    <iframe
                                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13716.887069922568!2d76.76316355521546!3d30.740271587698007!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390fed0afe5003d3%3A0x8f47abe9f2044934!2sSector%2017%2C%20Chandigarh!5e0!3m2!1sen!2sin!4v1747668020908!5m2!1sen!2sin"
                                                        width="100%" height="200" style="border:0; width:100%"
                                                        allowfullscreen="" loading="lazy"
                                                        referrerpolicy="no-referrer-when-downgrade"></iframe>

                                                    <h4 class="mt-3">Shitender Kumar</h4>
                                                    <div class="table-responsive mt-3">
                                                        <table class="table table-bordered mb-0">
                                                            <thead>
                                                                <tr>

                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td><strong>Date</strong></td>
                                                                    <td>08-05-2025</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Check-in Time :</strong></td>
                                                                    <td>09:30 Am</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Check-out Time :</strong></td>
                                                                    <td>06:30 Pm</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Total Time :</strong></td>
                                                                    <td>9 Hour</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Rest :</strong></td>
                                                                    <td>1 Hour</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Mobility Time :</strong></td>
                                                                    <td>04 hours 28 Minutes</td>
                                                                </tr>

                                                            </tbody>
                                                        </table>
                                                    </div>




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
                                                <div class="row">
                                                    <div class="col-md-12 d-flex">
                                                        <div class="card flex-fill">
                                                            <div class="card-body">
                                                                <div class="row align-items-center">
                                                                    <div class="col-md-8">
                                                                        <div class="d-flex align-items-center">
                                                                            <a href="project-details.html"
                                                                                class="flex-shrink-0 me-2">
                                                                                <img src="{{ asset('assets/img/products/product-05.jpg') }}"
                                                                                    class="img-fluid rounded-circle"
                                                                                    alt="img" />
                                                                            </a>
                                                                            <div>
                                                                                <h6 class="mb-1"><a
                                                                                        href="project-details.html">Dell
                                                                                        Laptop - #343556656 </a></h6>
                                                                                <div class="d-flex align-items-center">
                                                                                    <p><span class="text-primary">AST -
                                                                                            001 <i
                                                                                                class="ti ti-point-filled text-primary mx-1"></i></span>Assigned
                                                                                        on 22 Nov, 2022 10:32AM </p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div>
                                                                            <span class="mb-1 d-block">Assigned by
                                                                            </span>
                                                                            <a href="#"
                                                                                class="fw-normal d-flex align-items-center">
                                                                                <img class="avatar avatar-sm rounded-circle me-2"
                                                                                    src="{{ asset('assets/img/profiles/avatar-01.jpg') }}"
                                                                                    alt="Img" />
                                                                                Andrew Symon
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <div class="dropdown ms-2">
                                                                            <a href="javascript:void(0);"
                                                                                class="d-inline-flex align-items-center"
                                                                                data-bs-toggle="dropdown"
                                                                                aria-expanded="false">
                                                                                <i class="ti ti-dots-vertical"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-end p-3">
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#asset_info">View
                                                                                        Info </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#refuse_msg">Raise
                                                                                        Issue </a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 d-flex">
                                                        <div class="card flex-fill mb-0">
                                                            <div class="card-body">
                                                                <div class="row align-items-center">
                                                                    <div class="col-md-8">
                                                                        <div class="d-flex align-items-center">
                                                                            <a href="project-details.html"
                                                                                class="flex-shrink-0 me-2">
                                                                                <img src="{{ asset('assets/img/products/product-06.jpg') }}"
                                                                                    class="img-fluid rounded-circle"
                                                                                    alt="img" />
                                                                            </a>
                                                                            <div>
                                                                                <h6 class="mb-1"><a
                                                                                        href="project-details.html">Bluetooth
                                                                                        Mouse - #478878 </a></h6>
                                                                                <div class="d-flex align-items-center">
                                                                                    <p><span class="text-primary">AST -
                                                                                            001 <i
                                                                                                class="ti ti-point-filled text-primary mx-1"></i></span>Assigned
                                                                                        on 22 Nov, 2022 10:32AM </p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div>
                                                                            <span class="mb-1 d-block">Assigned by
                                                                            </span>
                                                                            <a href="#"
                                                                                class="fw-normal d-flex align-items-center">
                                                                                <img class="avatar avatar-sm rounded-circle me-2"
                                                                                    src="{{ asset('assets/img/profiles/avatar-01.jpg') }}"
                                                                                    alt="Img" />
                                                                                Andrew Symon
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <div class="dropdown ms-2">
                                                                            <a href="javascript:void(0);"
                                                                                class="d-inline-flex align-items-center"
                                                                                data-bs-toggle="dropdown"
                                                                                aria-expanded="false">
                                                                                <i class="ti ti-dots-vertical"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-end p-3">
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#asset_info">View
                                                                                        Info </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#refuse_msg">Raise
                                                                                        Issue </a>
                                                                                </li>
                                                                            </ul>
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

<!-- Edit Employee -->
<div class="modal fade" id="edit_employee">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <h4 class="modal-title me-2">Edit Employee </h4><span>Employee ID : ___ -0024 </span>
                </div>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="employee_edit_form">
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
                                            <img src="{{ asset('assets/img/users/user-13.jpg') }}" alt="img"
                                                class="rounded-circle" id="editprofile_img" />
                                        </div>
                                        <div class="profile-upload">
                                            <div class="mb-2">
                                                <h6 class="mb-1">Upload Profile Image </h6>
                                                <p class="fs-12">Image should be below 4 mb </p>
                                            </div>
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn btn btn-sm btn-primary me-2">
                                                    Upload
                                                    <input type="file" class="form-control image-sign"
                                                        multiple="" />
                                                </div>
                                                <a href="javascript:void(0);" class="btn btn-light btn-sm">Cancel </a>
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
                                        <select class="form-select" name="gender" id="emp-gender-info">
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
                                                class="form-control datetimepicker" />
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
                                                class="form-control datetimepicker" />
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
                                        <select name="report_office" id="report_office-info" class="form-select">
                                            <option value="">Select</option>
                                            <option value="Finance">Finance</option>
                                            <option value="Developer">Developer</option>
                                            <option value="Executive">Executive</option>
                                            <option value="IT Support">IT Support</option>
                                            <option value="HR">HR</option>
                                        </select>
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
                                        <label class="form-label">Designation</label>
                                        <select class="form-select" name="designation" id="designation-info">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3" id="reporting_manager_container">
                                        <label class="form-label">Reporting Manager</label>
                                        <select id="reporting_manager-info" name="reporting_manager_id" class="form-select select2">
                                            <option value="">Select Reporting Manager</option>
                                        </select>
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
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Passport No <span class="text-danger"> * </span></label>
                                <input type="text" name="passport_no" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Passport Expiry Date <span class="text-danger"> *
                                    </span></label>
                                <div class="input-icon-end position-relative">
                                    <input type="date" class="form-control datetimepicker"
                                        name="passport_exp_date" placeholder="dd/mm/yyyy" />
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
                                    <option />Select
                                    <option value="married" />Yes
                                    <option value="single" />Nos
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Employee Spouse </label>
                                <input type="text" name="Employment_of_spouse" class="form-control" />
                            </div>
                        </div>
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
                                <label class="form-label">Relationship </label>
                                <input type="text" name="relationship" class="form-control" />
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
                                    <input type="date" name="dob" class="form-control datetimepicker"
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
                                    <input type="date" class="form-control datetimepicker" name="start_year"
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
                                    <input type="date" class="form-control datetimepicker" name="end_year"
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
                                    <input type="date" name="start_date" class="form-control datetimepicker"
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
                                    <input type="date" name="end_date" class="form-control datetimepicker"
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

@push('scripts')
<script>
    $(document).ready(function() {
        const empId = new URLSearchParams(window.location.search).get('emp_id');
        if (!empId) {
            alert('Employee ID not found.');
            return;
        }
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function() {
                const input = document.querySelector(this.getAttribute('data-target'));
                if (!input) return;
                if (input.type === 'password') {
                    input.type = 'text';
                    this.classList.remove('ti-eye-off');
                    this.classList.add('ti-eye');
                } else {
                    input.type = 'password';
                    this.classList.remove('ti-eye');
                    this.classList.add('ti-eye-off');
                }
            });
        });

        fetchEmployeeDetails(empId);

        function handleFormSubmit(formSelector, route, modalSelector, extraData = {}) {
            $(document).off('submit', formSelector).on('submit', formSelector, function(e) {
                e.preventDefault();
                const formElement = this;
                const formData = new FormData(formElement);
                formData.append('_method', 'PUT');
                formData.append('_token', '{{ csrf_token() }}');
                $.each(extraData, function(key, value) {
                    formData.append(key, value);
                });
                const $form = $(formElement);
                console.log("formData", formData);
                const submitBtn = $form.find('button[type="submit"]');
                submitBtn.prop('disabled', true).blur();
                document.activeElement.blur();
                $.ajax({
                    url: route,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        showToast(response.message || 'Saved successfully.', true);
                        if (modalSelector) {
                            $(modalSelector).modal('hide');
                            setTimeout(() => {
                                $('.modal-backdrop').remove();
                                $('body').removeClass('modal-open').css(
                                    'padding-right', '');
                            }, 500);
                            fetchEmployeeDetails(empId);
                        }

                        submitBtn.prop('disabled', false);
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false);
                        let msg = 'Something went wrong. Please try again.';
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            msg = "Validation Error:\n";
                            $.each(xhr.responseJSON.errors, function(key, val) {
                                msg += `- ${val[0]}\n`;
                            });
                        }
                        showToast(msg, false);
                    }
                });
            });
        }
        handleFormSubmit('#family-info-form',
            '{{ route('employee.family', ['box_slug' => request()->route('box_slug')]) }}',
            '#edit_familyinformation', {
                employee_id: empId
            });
        $('.modal').on('hidden.bs.modal', function() {
            const form = $(this).find('form')[0];
            if (form) {
                form.reset();
                $(form).find('input[type="hidden"]').val('');
            }
        });
        handleFormSubmit('#education-employee-info',
            '{{ route('employee.education', ['box_slug' => request()->route('box_slug')]) }}',
            '#edit_education', {
                employee_id: empId
            });
        handleFormSubmit('#employee_details_exp',
            '{{ route('employee.experience', ['box_slug' => request()->route('box_slug')]) }}',
            '#edit_experience', {
                employee_id: empId
            });
        handleFormSubmit('#employee_bank_statutory',
            '{{ route('employee.bank_safety', ['box_slug' => request()->route('box_slug')]) }}',
            '#add_bank_satutory', {
                employee_id: empId
            });
        handleFormSubmit('#employee_change_password',
            '{{ route('employee.change-password', ['box_slug' => request()->route('box_slug')]) }}',
            '#change_password', {
                employee_id: empId
            }
        );
        handleFormSubmit('#employee_emergency_c_dtails',
            '{{ route('employee.emergency', ['box_slug' => request()->route('box_slug')]) }}',
            '#edit_emergency', {
                employee_id: empId
            });
        handleFormSubmit('#employee_personal_info',
            '{{ route('employee.update', ['box_slug' => request()->route('box_slug')]) }}',
            '#edit_personal', {
                id: empId
            });
        handleFormSubmit('#employee_edit_form',
            '{{ route('employee.update', ['box_slug' => request()->route('box_slug')]) }}',
            '#edit_employee', {
                id: empId
            });
        handleFormSubmit(
            '#employee_edit_form_details',
            '{{ route('employee.bank.update', ['box_slug' => request()->route('box_slug')]) }}',
            '#edit_bank', {
                employee_id: empId
            }
        );

        function showToast(message, isSuccess = true) {
            const toastEl = $('#toastMessage');
            const toastBody = $('#toastBody');
            toastEl.removeClass('bg-success bg-danger')
                .addClass(isSuccess ? 'bg-success' : 'bg-danger');
            toastBody.text(message);
            const toast = new bootstrap.Toast(toastEl[0]);
            toast.show();
        }
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
                emergency: 'Delted Emergency Records'
            };
            $('#confirm_delete_btn')
                .data('id', itemId)
                .data('type', itemType)
                .text('Yes, Delete')
                .prop('disabled', false);

            $('#delete_title').text(titles[itemType] || 'Delete Record');
            $('#delete_modal').modal('show');
        });

        // fetch team model

        $('#emp-designation').on('click', function() {
            const designationId = this.getAttribute('data-designation-id');
            if (!designationId) {
                console.warn('No designation ID found.');
                return;
            }

            // Set the hidden input
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

                        // Use your modal's ID here
                        let $info = $('#TeamsModel .modal-body .team-info');
                        if (!$info.length) {
                            $info = $('<p class="team-info text-primary mt-2"></p>');
                            $('#TeamsModel .modal-body').append($info);
                        }
                        $info.text('Team Name: ' + team.name);
                    } else {
                        console.error('Team not found.');
                    }
                },
                error: function(xhr) {
                    console.error('AJAX error:', xhr.responseText);
                }
            });
        });

        // Confirm Delete
        $(document).on('click', '#confirm_delete_btn', function() {
            const $btn = $(this);
            const itemId = $btn.data('id');
            const itemType = $btn.data('type');
            const deleteRoutes = {
                bank: '{{ route('delete.bank', ['box_slug' => request()->route('box_slug')]) }}',
                family: '{{ route('delete.family', ['box_slug' => request()->route('box_slug')]) }}',
                education: '{{ route('delete.education', ['box_slug' => request()->route('box_slug')]) }}',
                experience: '{{ route('delete.experience', ['box_slug' => request()->route('box_slug')]) }}',
                emergency: '{{ route('delete.emergency', ['box_slug' => request()->route('box_slug')]) }}'
            };
            const url = deleteRoutes[itemType];
            if (!url) {
                showToast('Unknown delete type', false);
                return;
            }
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
                }
            };
            const requestData = requestDataMap[itemType];
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');
            $.ajax({
                url: url,
                type: 'DELETE',
                data: requestData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#delete_modal').modal('hide');
                    showToast(response.message || 'Deleted successfully.', true);
                    fetchEmployeeDetails(empId); // Ensure empId is globally scoped
                },
                error: function(xhr) {
                    $('#delete_modal').modal('hide');
                    showToast(xhr.responseJSON?.message || 'Delete failed.', false);
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Yes, Delete');
                }
            });
        });

        function populateEmployeeData(data) {
            function setValue(selector, value) {
                const $el = $(selector);
                if ($el.is('input, select, textarea')) {
                    $el.val(value ?? '');
                } else {
                    $el.html(value ?? '');
                }
            }
            function showLetterAvatar(name) {
    const $img = $('#profile_img');
    const $letterAvatar = $('#letter_avatar');
    const $avatarLetter = $('#avatar_letter');

    const displayName = (name || 'U').trim();
    const firstLetter = displayName.charAt(0).toUpperCase();
    const color = getColorFromName(firstLetter);

    $img.addClass('d-none');
    $avatarLetter.text(firstLetter);
    $letterAvatar
        .removeClass('d-none')
        .addClass('d-flex')
        .css('background-color', color);
}

function getColorFromName(letter) {
    const colors = [
        "#f44336", "#e91e63", "#9c27b0", "#673ab7", "#3f51b5",
        "#2196f3", "#03a9f4", "#00bcd4", "#009688", "#4caf50",
        "#8bc34a", "#cddc39", "#ffc107", "#ff9800", "#ff5722"
    ];
    const code = letter.toUpperCase().charCodeAt(0);
    const index = code % colors.length;
    return colors[index];
}

            setValue('#emp-department', data.designation_name)
            setValue('#department_id', data.department_id);
            setValue('#emp-designation', data.department_name)
            $('#emp-designation').attr('data-designation-id', data.department_id);
            setValue('#emp-name', data.user.name);
            setValue('#first_name', data.name);
            setValue('#last_name', data.last_name);
            setValue('#emp-gender', data.gender);
            setValue('#emp-birthday', data.birthday);
            setValue('#emp-id', data.employee_id);
            setValue('#emp-joining', data.joining_date);
            setValue('#username', data.username);
            setValue('#emp-email', data.email);
            setValue('#emp-phone', data.phone);
            setValue('#company', data.company);
            setValue('#department', data.department);
            setValue('#department_new', data.department_name);
            setValue('#designation_new', data.designation_name);
            setValue('#report_office_new', data.report_office);
            setValue('#designation', data.designation);
            setValue('#emp-address', data.address);
            setValue('#emp-about', data.about);
            setValue('#emp-passport', data.passport_no);
            setValue('#emp-passport-exp', data.passport_exp_date);
            setValue('#emp-nationality', data.nationality);
            setValue('#emp-religion', data.religion);
            setValue('#emp-marital', data.marital_status);
            setValue('#emp-spouse', data.Employment_of_spouse);
            setValue('#emp-children', data.no_of_children);

            // Populate Reporting Manager for edit modal
            if (data.department_id) {
                populateReportingManagerSelect($('#reporting_manager-info'), data.department_id, data.designation, data.reporting_manager_id);
            }
         const userName = data.user?.name || data.name || 'User';
const imgSrc = data.profile_image_url;

if (imgSrc) {
    const $img = $('#profile_img');
    const $letterAvatar = $('#letter_avatar');
    const $avatarLetter = $('#avatar_letter');

    $img
        .attr('src', imgSrc)
        .removeClass('d-none')
        .on('error', function () {
            showLetterAvatar(userName); // fallback on image load error
        });

    $letterAvatar.addClass('d-none');
} else {
    showLetterAvatar(userName); // no image URL provided
}

            //for emergency listing
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
                            ${ec.name || ''}
                            <span><i class="ti ti-point-filled text-danger"></i></span>
                            ${ec.relation || ''}
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
            $(document).on('click', '.edit-bank-btn', function() {
                const bankData = $(this).data('bank');
                const form = $('#employee_edit_form_details')[0];
                form.elements['bank_name'].value = bankData.bank_name || '';
                form.elements['account_number'].value = bankData.account_number || '';
                form.elements['ifsc_code'].value = bankData.ifsc_code || '';
                form.elements['branch'].value = bankData.branch || '';
                form.elements['bank_type'].value = bankData.bank_type || '';
                $('#bank-id-hidden').val(bankData.id || ''); // set bank_id here
            });
            // 2. When modal opens without clicking Edit (if applicable) — clear form & bank_id
            $('#edit_bank').on('show.bs.modal', function() {
                const form = $('#employee_edit_form_details')[0];
                form.reset();
                $('#bank-id-hidden').val('');
            });
            //  Bank Details
            const allowedBankTypes = ['primary', 'secondary'];
            const $accordionContainer = $('#bank-accordion-container');
            $accordionContainer.empty();
            if (Array.isArray(data.bank_details) && data.bank_details.length > 0) {
                data.bank_details.forEach((bd, index) => {
                    const accordionId = `bankDetail-${index}`;
                    const headingId = `heading-${index}`;
                    const collapseId = `collapse-${index}`;
                    const bankType = allowedBankTypes.includes(bd.bank_type) ? bd.bank_type : 'Other';
                    $accordionContainer.append(`
            <div class="accordion-item border">
                <h2 class="accordion-header" id="${headingId}">
                    <button class="accordion-button ${index !== 0 ? 'collapsed' : ''}" type="button" data-bs-toggle="collapse"
                        data-bs-target="#${collapseId}" aria-expanded="${index === 0}" aria-controls="${collapseId}">
                        ${bankType.toUpperCase()} BANK ACCOUNT
                    </button>
                </h2>
                <div id="${collapseId}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" aria-labelledby="${headingId}" data-bs-parent="#bank-accordion-container">
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
                                        data-bs-toggle="modal" data-bs-target="#edit_bank">Edit</button>
                                <button class="btn btn-sm btn-danger delete-bank-btn" data-type="bank" data-id="${bd.id}">Delete</button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);
                });
            }
            if (data.bank_statutory) {
                const bs = data.bank_statutory;
                $('select[name="salary_basis"]').val(bs.salary_basis ?? '');
                $('input[name="salary_amount"]').val(bs.salary_amount ?? '');
                $('select[name="payment_type"]').val(bs.payment_type ?? '');
                $('select[name="pf_contribution"]').val(bs.pf_contribution ?? '');
                $('input[name="pf_no"]').val(bs.pf_no ?? '');
                $('input[name="pf_rate"]').val(bs.pf_rate ?? '');
                $('select[name="pf_additional"]').val(bs.pf_additional ?? '');
                $('input[name="pf_total"]').val(bs.pf_total ?? '');
                $('select[name="esi_contribution"]').val(bs.esi_contribution ?? '');
                $('input[name="esi_no"]').val(bs.esi_no ?? '');
                $('input[name="esi_rate"]').val(bs.esi_rate ?? '');
                $('select[name="esi_additional"]').val(bs.esi_additional ?? '');
                $('input[name="esi_total"]').val(bs.esi_total ?? '');
            }
            const departments = data?.departments;
            // On modal show
            $('#edit_employee').off('show.bs.modal').on('show.bs.modal', function () {
    const form = document.getElementById('employee_edit_form');
    if (!form) return;

    //  Profile Image or Initial Avatar Setup
    const $avatarContainer = $('.frames'); // The avatar div
    $avatarContainer.empty(); // Clear previous content

    const profileImage = data.profile_image_url;
    const userName = data.user?.name || data.first_name || 'U';
    const userInitial = userName.charAt(0).toUpperCase();

    if (profileImage) {
        $avatarContainer.append(`
            <img src="${profileImage}" alt="img"
                class="rounded-circle w-100 h-100 object-fit-cover"
                id="editprofile_img" />
        `);
    } else {
        $avatarContainer.append(`
            <span class="fw-bold fs-2 text-uppercase text-primary d-flex align-items-center justify-content-center w-100 h-100">
                ${userInitial}
            </span>
        `);
    }

    //  Department and Designation Setup
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
        if (dept && Array.isArray(dept.all_teams)) {
            dept.all_teams.forEach(team => {
                const option = document.createElement('option');
                option.value = team.id;
                option.textContent = team.name + ' (Team)';
                designationSelect.appendChild(option);
            });
        }
    }

    deptSelect.onchange = function () {
        populateDesignations(this.value);
    };

    function setValueById(id, value) {
        const el = document.getElementById(id);
        if (!el) return;

        const tag = el.tagName.toLowerCase();

        if (tag === 'select') {
            const val = (value ?? '').toString().trim().toLowerCase();
            const options = Array.from(el.options);
            const match = options.find(opt => (opt.value ?? '').toString().trim().toLowerCase() === val);
            el.value = match ? match.value : '';
            if (id === 'department') {
                populateDesignations(el.value); // Only if needed
            }
        } else if (['input', 'textarea'].includes(tag)) {
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

    setValueById('first_name-info', data.first_name);
    setValueById('last_name-info', data.last_name);
    setValueById('emp-gender-info', data.gender);
    setValueById('emp-birthday-info', formatDateToISO(data.birthday));
    setValueById('employee_code-info', data.employee_id);
    setValueById('emp-joining-info', formatDateToISO(data.joining_date));
    setValueById('usernames-info', data.username);
    setValueById('emp-email-info', data.email);
    setValueById('emp-phone-info', data.phone);
    setValueById('company-info', data.company);
    setValueById('department-info', data.department);
    setValueById('report_office-info', data.report_office);
    setValueById('designation-info', data.designation);
    setValueById('emp-address-info', data.address);
    setValueById('emp-about-info', data.about);
});

            //  Personal Info Modal
            $('#edit_personal').off('show.bs.modal').on('show.bs.modal', function() {
                const form = document.getElementById('employee_personal_info');
                if (!form) return;
                form.elements['passport_no'].value = data.passport_no ?? '';
                form.elements['passport_exp_date'].value = data.passport_exp_date ?? '';
                form.elements['nationality'].value = data.nationality ?? '';
                form.elements['religion'].value = data.religion ?? '';
                form.elements['marital_status'].value = data.marital_status ?? '';
                form.elements['Employment_of_spouse'].value = data.Employment_of_spouse ?? '';
                form.elements['no_of_children'].value = data.no_of_children ?? '';
            });
            //  Experience List
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
                            data-company="${exp.company_name || ''}"
                            data-position="${exp.position || ''}"
                            data-start="${exp.start_date || ''}"
                            data-end="${exp.end_date || ''}"
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
            $(document).on('click', '.edit-experience', function() {
                const $btn = $(this);
                const id = $btn.data('id');
                const company = $btn.data('company');
                const position = $btn.data('position');
                const start = $btn.data('start');
                const end = $btn.data('end');
                const current = $btn.data('current');
                $('#experience-id').val(id);
                const form = document.getElementById('employee_details_exp');
                if (!form) return;
                form.elements['company_name'].value = company;
                form.elements['position'].value = position;
                form.elements['start_date'].value = start;
                form.elements['end_date'].value = end;
                form.elements['current_working'].checked = current == 1;
                const modalEl = document.getElementById('edit_experience');
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
                // Fix lingering modal issues
                setTimeout(() => {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('padding-right', '');
                }, 300);
            });
            //  Education List
            let educationData = []; // Declare globally
            $('#education-list').empty();
            if (Array.isArray(data.educations)) {
                educationData = data.educations; // Assign here
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
            //for eduction list edit modal
            $(document).on('click', '.edit-education', function() {
                const id = $(this).data('id');
                const edu = educationData.find(item => item.id === id);
                if (edu) {
                    $('#education-id').val(edu.id);
                    $('input[name="institute"]').val(edu.institute || '');
                    $('input[name="degree"]').val(edu.degree || '');
                    $('input[name="start_year"]').val(edu.start_year || '');
                    $('input[name="end_year"]').val(edu.end_year || '');
                    const modal = new bootstrap.Modal(document.getElementById('edit_education'));
                    modal.show();
                } else {
                    alert('Education record not found.');
                }
            });
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
            // Edit-family click handler (this must go *after* rendering the buttons)
            $(document).on('click', '.edit-family', function() {
                const id = $(this).data('id');
                const member = familyData.find(item => item.id === id);
                if (member) {
                    $('#family-id').val(member.id);
                    const form = document.getElementById('family-info-form');
                    if (!form) return;
                    form.elements['name'].value = member.name || '';
                    form.elements['relationship'].value = member.relationship || '';
                    form.elements['dob'].value = member.dob || '';
                    form.elements['contact'].value = member.contact || '';

                    const modal = new bootstrap.Modal(document.getElementById(
                        'edit_familyinformation'));
                    modal.show();
                } else {
                    alert('Family record not found.');
                }
            });
            $(document).on('click', '.edit-emergency', function() {
                const id = $(this).data('id');
                const member = emergencyData.find(item => item.id === id);
                if (member) {
                    $('#emergency-id').val(member.id);
                    const form = document.getElementById('employee_emergency_c_dtails');
                    if (!form) return;
                    const index = member.type === 'secondary' ? 1 : 0;
                    form.elements[`contacts[${index}][name]`].value = member.name || '';
                    form.elements[`contacts[${index}][relation]`].value = member.relation || '';
                    form.elements[`contacts[${index}][phone]`].value = member.phone || '';
                    form.elements[`contacts[${index}][phone_alt]`].value = member.phone_alt || '';
                    const modal = new bootstrap.Modal(document.getElementById('edit_emergency'));
                    modal.show();
                } else {

                }
            });

        }

        function fetchEmployeeDetails(empId) {
            $.ajax({
                url: "{{ route('employee.details.admin', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'GET',
                data: {
                    req_emp_id: empId
                },
                success: function(response) {
                    if (response && response.data) {
                        populateEmployeeData(response.data);
                    } else {
                        alert('Employee data not found.');
                    }
                },
                error: function() {
                    alert('Failed to fetch employee details.');
                }
            });
        }

        // Hierarchical Reporting Manager Logic
        function populateReportingManagerSelect($select, filterDept = null, filterDesignation = null, selectedId = null) {
            $select.prop('disabled', true).empty().append('<option value="">Loading managers...</option>');

            const currentEditingEmpId = new URLSearchParams(window.location.search).get('emp_id');

            $.ajax({
                url: '{{ route('employee.get.box.users', ['box_slug' => request()->route('box_slug')]) }}',
                method: 'GET',
                data: {
                    department: filterDept,
                    superior_to_designation: filterDesignation,
                    exclude_id: currentEditingEmpId || null,
                    include_id: selectedId || null
                },
                success: function(response) {
                    $select.prop('disabled', false);
                    if (response.status && Array.isArray(response.data)) {
                        let selectedDepth = null;
                        if (filterDesignation && teamOptions[filterDept]) {
                            const found = teamOptions[filterDept].find(t => String(t.id) === String(filterDesignation));
                            if (found) {
                                selectedDepth = (found.depth !== undefined && found.depth !== null) ? parseInt(found.depth) : null;
                            }
                        }
                        
                        renderReportingManagerOptions($select, response.data, selectedId, filterDesignation, selectedDepth);
                    } else {
                        $select.empty().append('<option value="">No superiors found</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching superior users:', error);
                    $select.prop('disabled', false).empty().append('<option value="">Error loading managers</option>');
                }
            });
        }

        function renderReportingManagerOptions($select, users, selectedId = null, activeFilterDesignation = null, selectedDepth = null) {
            $select.empty().append('<option value="">Select Reporting Manager</option>');
            
            let hierarchyRec = [];
            let others = [];
            const currentEditingEmpId = new URLSearchParams(window.location.search).get('emp_id');

            // Sort logic
            users.sort((a, b) => {
                const aAdmin = (a.staff_role || '').toLowerCase() === 'admin' ? 0 : 1;
                const bAdmin = (b.staff_role || '').toLowerCase() === 'admin' ? 0 : 1;
                if (aAdmin !== bAdmin) return aAdmin - bAdmin;
                
                if (selectedDepth !== null) {
                    const aDist = Math.abs((a.designation_depth || 0) - (selectedDepth - 1));
                    const bDist = Math.abs((b.designation_depth || 0) - (selectedDepth - 1));
                    return aDist - bDist;
                }
                return (a.designation_depth || 0) - (b.designation_depth || 0);
            });

            users.forEach(emp => {
                if (currentEditingEmpId && String(emp.id) === String(currentEditingEmpId)) return;
                
                let reason = "";
                let highlightType = ""; 

                if (activeFilterDesignation) {
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
                    hierarchyRec.push({ ...emp, recommendationReason: reason, isHierarchy: true, highlightType: highlightType });
                } else {
                    others.push(emp);
                }
            });

            const createOption = (emp) => {
                const suffix = emp.recommendationReason || "";
                const designation = emp.designation_name || 'No Designation';
                return $('<option></option>')
                    .val(emp.id)
                    .text(`${emp.name}${emp.employee_id ? ' (' + emp.employee_id + ')' : ''}${suffix} [${designation}]`)
                    .attr('selected', selectedId && String(emp.id) === String(selectedId))
                    .data('is-hierarchy', !!emp.isHierarchy)
                    .data('highlight-type', emp.highlightType || "")
                    .data('designation-name', designation);
            };

            if (hierarchyRec.length > 0) {
                const $hGroup = $('<optgroup label="Direct Hierarchy"></optgroup>');
                hierarchyRec.forEach(emp => $hGroup.append(createOption(emp)));
                $select.append($hGroup);
            }
            if (others.length > 0) {
                const label = hierarchyRec.length > 0 ? "Other Employees" : "All Employees";
                const $othersGroup = $('<optgroup label="' + label + '"></optgroup>');
                others.forEach(emp => $othersGroup.append(createOption(emp)));
                $select.append($othersGroup);
            }

            if ($.fn.select2) {
                if ($select.data('select2')) { $select.select2('destroy'); }
                $select.select2({
                    placeholder: 'Select Reporting Manager',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $select.closest('.modal'),
                    templateResult: reportingManagerTemplateResult,
                    templateSelection: reportingManagerTemplateSelection
                });
            }
        }

        function reportingManagerTemplateResult(data) {
            if (!data.id) return data.text;
            var $el = $(data.element);
            var isHierarchy = $el.data('is-hierarchy') === true || $el.data('is-hierarchy') === "true";
            var highlightType = $el.data('highlight-type') || "";
            
            if (isHierarchy) {
                let bgColor = "#fff3e0"; 
                let borderColor = "#ff9800";
                let badgeText = "Hierarchy Parent";
                let badgeClass = "bg-warning";
                
                if (highlightType === "immediate") {
                    bgColor = "#e8f5e9"; 
                    borderColor = "#4caf50";
                    badgeText = "Immediate Superior";
                    badgeClass = "bg-success";
                } else if (highlightType === "admin") {
                    bgColor = "#f3e5f5"; 
                    borderColor = "#9c27b0";
                    badgeText = "Administrator";
                    badgeClass = "bg-secondary";
                }

                return $('<div style="display: flex; justify-content: space-between; align-items: center; background-color: ' + bgColor + '; padding: 4px 8px; border-radius: 4px; border-left: 4px solid ' + borderColor + '; margin: 1px 0;">' +
                            '<div>' +
                                '<div style="font-weight: 600; color: #333;">' + data.text.split(' [')[0] + '</div>' +
                                '<div style="font-size: 0.75rem; color: #666;">' + ($el.data('designation-name') || '') + '</div>' +
                            '</div>' +
                            '<span class="badge ' + badgeClass + '" style="font-size: 0.65rem;">' + badgeText + '</span>' +
                        '</div>');
            }
            return data.text;
        }

        function reportingManagerTemplateSelection(data) {
            if (!data.id) return data.text;
            return data.text.split(' (')[0]; 
        }
    });
</script>
@endpush
@endsection
