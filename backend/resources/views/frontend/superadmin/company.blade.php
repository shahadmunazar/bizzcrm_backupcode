@extends('layouts.main')
@section('content')
@section('title', 'Companies')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <div class="row">
            <div class="col-md-12">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">Companies<span class="count-title">123</span></h4>
                        </div>
                        <div class="col-4 text-end">
                            <div class="head-icons">
                                <a href="companies.html" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Collapse" id="collapse-header"><i
                                        class="ti ti-chevrons-up"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Summary Cards -->
                <div class="row">
                    <!-- Total Companies -->
                    <div class="col-xl-4 col-sm-6 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="avatar avatar-md rounded bg-dark mb-3">
                                        <i class="ti ti-building fs-16"></i>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h2 class="mb-1">{{ $total_companies }}</h2>
                                        <p class="fs-13">Total Companies</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Active Companies -->
                    <div class="col-xl-4 col-sm-6 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="avatar avatar-md rounded bg-dark mb-3">
                                        <i class="ti ti-carousel-vertical fs-16"></i>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h2 class="mb-1">{{ $active_companies }}</h2>
                                        <p class="fs-13">Active Companies</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inactive Companies -->
                    <div class="col-xl-4 col-sm-6 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="avatar avatar-md rounded bg-dark mb-3">
                                        <i class="ti ti-carousel-horizontal fs-16"></i>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h2 class="mb-1">{{ $inactive_companies }}</h2>
                                        <p class="fs-13">Inactive Companies</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Summary Cards -->
                <div class="card ">
                    <div class="card-header">
                        <!-- Search -->
                        <div class="row align-items-center">
                            <div class="col-sm-4">
                                <div class="icon-form mb-3 mb-sm-0">
                                    <span class="form-icon"><i class="ti ti-search"></i></span>
                                    <input type="text" class="form-control" id="company-search"
                                        placeholder="Search Companies">
                                </div>
                            </div>

                            <div class="col-sm-8">
                                <div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
                                    <div class="dropdown me-2">
                                        <a href="javascript:void(0);" class="dropdown-toggle"
                                            data-bs-toggle="dropdown"><i
                                                class="ti ti-package-export me-2"></i>Export</a>
                                        <div class="dropdown-menu  dropdown-menu-end">
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0);" class="dropdown-item"><i
                                                            class="ti ti-file-type-pdf text-danger me-1"></i>Export
                                                        as PDF</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);" class="dropdown-item"><i
                                                            class="ti ti-file-type-xls text-green me-1"></i>Export
                                                        as Excel </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas"
                                        data-bs-target="#offcanvas_add"><i
                                            class="ti ti-square-rounded-plus me-2"></i>Add Company</a>
                                </div>
                            </div>
                        </div>
                        <!-- /Search -->
                    </div>
                    <div class="card-body">
                        <!-- Filter -->
                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
                            <div class="d-flex align-items-center flex-wrap row-gap-2">
                                <div class="dropdown me-2">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i
                                            class="ti ti-sort-ascending-2 me-2"></i>Sort </a>
                                    <div class="dropdown-menu  dropdown-menu-start">
                                        <ul>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="asc">
                                                    <i class="ti ti-circle-chevron-right me-1"></i>Ascending
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="desc">
                                                    <i class="ti ti-circle-chevron-right me-1"></i>Descending
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="recently_viewed">
                                                    <i class="ti ti-circle-chevron-right me-1"></i>Recently
                                                    Viewed
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="recently_added">
                                                    <i class="ti ti-circle-chevron-right me-1"></i>Recently
                                                    Added
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="icon-form">
                                    <span class="form-icon"><i class="ti ti-calendar"></i></span>
                                    <input type="text" class="form-control bookingrange" placeholder="">
                                </div>
                            </div>
                            <div class="d-flex align-items-center flex-wrap row-gap-2">
                                <div class="dropdown me-2">
                                    <a href="javascript:void(0);" class="btn bg-soft-purple text-purple"
                                        data-bs-toggle="dropdown" data-bs-auto-close="outside"><i
                                            class="ti ti-columns-3 me-2"></i>Manage Columns</a>
                                    <div class="dropdown-menu  dropdown-menu-md-end dropdown-md p-3">
                                        <h4 class="mb-2 fw-semibold">Want to manage datatables?</h4>
                                        <p class="mb-3">Please drag and drop your column to reorder your table
                                            and enable see option as you want.</p>
                                        <div class="border-top pt-3">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Company</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-company" data-col="2" class="check toggle-column" checked>
                                                    <label for="col-company" class="checktoggle"></label>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Email</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-email" data-col="3" class="check toggle-column" checked>
                                                    <label for="col-email" class="checktoggle"></label>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Domain</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-domain" data-col="4" class="check toggle-column" checked>
                                                    <label for="col-domain" class="checktoggle"></label>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Industry</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-industry" data-col="5" class="check toggle-column" checked>
                                                    <label for="col-industry" class="checktoggle"></label>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Created Date</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-created" data-col="6" class="check toggle-column" checked>
                                                    <label for="col-created" class="checktoggle"></label>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Email Status</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-email-status" data-col="7" class="check toggle-column" checked>
                                                    <label for="col-email-status" class="checktoggle"></label>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Status</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-status" data-col="8" class="check toggle-column" checked>
                                                    <label for="col-status" class="checktoggle"></label>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Action</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-action" data-col="9" class="check toggle-column" checked>
                                                    <label for="col-action" class="checktoggle"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-sorts dropdown">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown"
                                        data-bs-auto-close="outside"><i class="ti ti-filter-share"></i>Filter</a>
                                    <div class="filter-dropdown-menu dropdown-menu  dropdown-menu-md-end p-3">
                                        <div class="filter-set-view">
                                            <div class="filter-set-head">
                                                <h4><i class="ti ti-filter-share"></i>Filter</h4>
                                            </div>
                                            <div class="accordion" id="accordionExample">
                                                <div class="filter-set-content">
                                                    <div class="filter-set-content-head">
                                                        <a href="#" data-bs-toggle="collapse"
                                                            data-bs-target="#collapseTwo" aria-expanded="true"
                                                            aria-controls="collapseTwo">Country</a>
                                                    </div>
                                                    <div class="filter-set-contents accordion-collapse collapse show"
                                                        id="collapseTwo" data-bs-parent="#accordionExample">
                                                        <div class="filter-content-list">
                                                            <div class="mb-2 icon-form">
                                                                <span class="form-icon"><i
                                                                        class="ti ti-search"></i></span>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Search Country">
                                                            </div>
                                                            <ul>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            India
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            USA
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            France
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            United Kingdom
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            UAE
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            Italy
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            Martin Lewis
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            Germany
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="filter-set-content">
                                                    <div class="filter-set-content-head">
                                                        <a href="#" class="collapsed" data-bs-toggle="collapse"
                                                            data-bs-target="#owner" aria-expanded="false"
                                                            aria-controls="owner">Owner</a>
                                                    </div>
                                                    <div class="filter-set-contents accordion-collapse collapse"
                                                        id="owner" data-bs-parent="#accordionExample">
                                                        <div class="filter-content-list">
                                                            <div class="mb-2 icon-form">
                                                                <span class="form-icon"><i
                                                                        class="ti ti-search"></i></span>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Search Owner">
                                                            </div>
                                                            <ul>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox" checked="">
                                                                            <span class="checkmarks"></span>
                                                                            Hendry Milner
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            Guilory Berggren
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            Jami Carlile
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            Theresa Nelson
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            Smith Cooper
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="filter-set-content">
                                                    <div class="filter-set-content-head">
                                                        <a href="#" class="collapsed" data-bs-toggle="collapse"
                                                            data-bs-target="#Status" aria-expanded="false"
                                                            aria-controls="Status">Status</a>
                                                    </div>
                                                    <div class="filter-set-contents accordion-collapse collapse"
                                                        id="Status" data-bs-parent="#accordionExample">
                                                        <div class="filter-content-list">
                                                            <ul>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox" checked="">
                                                                            <span class="checkmarks"></span>
                                                                            Active
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            Inactive
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="filter-set-content">
                                                    <div class="filter-set-content-head">
                                                        <a href="#" class="collapsed" data-bs-toggle="collapse"
                                                            data-bs-target="#collapseOne" aria-expanded="false"
                                                            aria-controls="collapseOne">Rating</a>
                                                    </div>
                                                    <div class="filter-set-contents accordion-collapse collapse"
                                                        id="collapseOne" data-bs-parent="#accordionExample">
                                                        <div class="filter-content-list">
                                                            <ul>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox" checked="">
                                                                            <span class="checkmarks"></span>
                                                                            <span class="rating">
                                                                                <i class="fa fa-star filled"></i>
                                                                                <i class="fa fa-star filled"></i>
                                                                                <i class="fa fa-star filled"></i>
                                                                                <i class="fa fa-star filled"></i>
                                                                                <i class="fa fa-star filled"></i>
                                                                                <span>5.0</span>
                                                                            </span>
                                                                        </label>
                                                                    </div>

                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            <span class="rating">
                                                                                <i class="fa fa-star filled"></i>
                                                                                <i class="fa fa-star filled"></i>
                                                                                <i class="fa fa-star filled"></i>
                                                                                <i class="fa fa-star filled"></i>
                                                                                <i class="fa fa-star"></i>
                                                                                <span>4.0</span>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            <span class="rating">
                                                                                <i class="fa fa-star filled"></i>
                                                                                <i class="fa fa-star filled"></i>
                                                                                <i class="fa fa-star filled"></i>
                                                                                <i class="fa fa-star"></i>
                                                                                <i class="fa fa-star"></i>
                                                                                <span>3.0</span>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            <span class="rating">
                                                                                <i class="fa fa-star filled"></i>
                                                                                <i class="fa fa-star filled"></i>
                                                                                <i class="fa fa-star"></i>
                                                                                <i class="fa fa-star"></i>
                                                                                <i class="fa fa-star"></i>
                                                                                <span>2.0</span>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            <span class="rating">
                                                                                <i class="fa fa-star filled"></i>
                                                                                <i class="fa fa-star"></i>
                                                                                <i class="fa fa-star"></i>
                                                                                <i class="fa fa-star"></i>
                                                                                <i class="fa fa-star"></i>
                                                                                <span>1.0</span>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="filter-set-content">
                                                    <div class="filter-set-content-head">
                                                        <a href="#" class="collapsed" data-bs-toggle="collapse"
                                                            data-bs-target="#collapseThree" aria-expanded="false"
                                                            aria-controls="collapseThree">Tags</a>
                                                    </div>
                                                    <div class="filter-set-contents accordion-collapse collapse"
                                                        id="collapseThree" data-bs-parent="#accordionExample">
                                                        <div class="filter-content-list">
                                                            <ul>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox" checked="">
                                                                            <span class="checkmarks"></span>
                                                                            Promotion
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            Rated
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            Rejected
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            Collab
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="filter-checks">
                                                                        <label class="checkboxs">
                                                                            <input type="checkbox">
                                                                            <span class="checkmarks"></span>
                                                                            Calls
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="filter-reset-btns">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <a href="#" class="btn btn-light">Reset</a>
                                                    </div>
                                                    <div class="col-6">
                                                        <a href="company.html" class="btn btn-primary">Filter</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Filter -->

                        <!-- Contact List -->
                        <div class="table-responsive custom-table">
                            <table id="boxes-lists">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort">
                                            <label class="checkboxs">
                                                <input type="checkbox" id="select-all">
                                                <span class="checkmarks"></span>
                                            </label>
                                        </th>
                                        <th class="no-sort"></th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Account URL</th>
                                        <th>Industry</th>
                                        <th>Created Date</th>
                                        <th>Verification</th>
                                        <th>Status</th>
                                        <th class="text-end no-sort">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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
                        <!-- /Contact List -->

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
<!-- /Page Wrapper -->

<!-- Add Company -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Add New Company</h5>
        <button type="button"
            class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
            data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <form action="company.html">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="profile-upload">
                                    <div class="profile-upload-img">
                                        <span><i class="ti ti-photo"></i></span>
                                        <img src="assets/img/profiles/avatar-20.jpg" alt="img" class="preview1">
                                        <button type="button" class="profile-remove">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                    <div class="profile-upload-content">
                                        <label class="profile-upload-btn">
                                            <i class="ti ti-file-broken"></i> Upload File
                                            <input type="file" class="input-img">
                                        </label>
                                        <p>JPG, GIF or PNG. Max size of 800K</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Company Name <span class="text-danger"> *</span></label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Email Address</label>
                                <input type="email" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="col-form-label">Account URL</label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Phone Number <span class="text-danger"> *</span></label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Website</label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="col-form-label">Password <span class="text-danger"> *</span></label>
                                <div class="pass-group">
                                    <input type="password" class="pass-input form-control">
                                    <span class="ti toggle-password ti-eye-off"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="col-form-label">Confirm Password <span class="text-danger">
                                        *</span></label>
                                <div class="pass-group">
                                    <input type="password" class="pass-inputs form-control">
                                    <span class="ti toggle-passwords ti-eye-off"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="col-form-label">Address</label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="col-form-label">Plan Name <span class="text-danger"> *</span></label>
                                <select class="select">
                                    <option>Select</option>
                                    <option>Advanced</option>
                                    <option>Basic</option>
                                    <option>Enterprise</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="col-form-label">Plan Type <span class="text-danger"> *</span></label>
                                <select class="select">
                                    <option>Select</option>
                                    <option>Monthly</option>
                                    <option>Yearly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="col-form-label">Currency <span class="text-danger"> *</span></label>
                                <select class="select">
                                    <option>Choose</option>
                                    <option>Dollar</option>
                                    <option>Euro</option>
                                    <option>Pound</option>
                                    <option>Rupee</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="col-form-label">Language <span class="text-danger"> *</span></label>
                                <select class="select">
                                    <option>Choose</option>
                                    <option>English</option>
                                    <option>Arabic</option>
                                    <option>French</option>
                                    <option>German</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-0">
                                <div class="radio-wrap">
                                    <label class="col-form-label">Status</label>
                                    <div class="d-flex flex-wrap">
                                        <div class="me-2">
                                            <input type="radio" class="status-radio" id="person" name="leave"
                                                checked="">
                                            <label for="person">Active</label>
                                        </div>
                                        <div>
                                            <input type="radio" class="status-radio" id="Organization"
                                                name="leave">
                                            <label for="Organization">Inactive</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>
<!-- /Add Company -->

<!-- Edit Company -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Edit Company</h5>
        <button type="button"
            class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
            data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <form id="edit_company_form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="company_id" id="edit_company_id">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="profile-upload">
                                    <div class="profile-upload-img">
                                        <span><i class="ti ti-photo"></i></span>
                                        <img src="{{ asset('assets/img/profiles/avatar-20.jpg') }}" id="edit_logo_preview" alt="img" class="preview1">
                                        <button type="button" class="profile-remove" onclick="$('#edit_company_logo').val(''); $('#edit_logo_preview').attr('src', '{{ asset('assets/img/profiles/avatar-20.jpg') }}');">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                    <div class="profile-upload-content">
                                        <label class="profile-upload-btn">
                                            <i class="ti ti-file-broken"></i> Upload File
                                            <input type="file" id="edit_company_logo" name="company_logo" class="input-img">
                                        </label>
                                        <p>JPG, GIF or PNG. Max size of 2MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Company Name <span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" name="name" id="edit_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Email Address</label>
                                <input type="email" class="form-control" name="company_email" id="edit_company_email">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="col-form-label">Domain</label>
                                <input type="text" class="form-control" name="domain" id="edit_domain">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Phone Number</label>
                                <input type="text" class="form-control" name="phone" id="edit_phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Address</label>
                                <input type="text" class="form-control" name="address" id="edit_address">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="col-form-label">Update Superadmin Password</label>
                                <div class="pass-group">
                                    <input type="password" class="pass-input form-control" name="password" id="edit_password">
                                    <span class="ti toggle-password ti-eye-off"></span>
                                </div>
                                <small class="text-muted">Leave blank to keep current password</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 ">
                                <label class="col-form-label">Confirm Password</label>
                                <div class="pass-group">
                                    <input type="password" class="pass-inputs form-control" name="password_confirmation" id="edit_password_confirmation">
                                    <span class="ti toggle-passwords ti-eye-off"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="col-md-6">
                            <div class="mb-3 ">
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btn_edit_company">Save Changes</button>
            </div>
        </form>
    </div>
</div>
<!-- /Edit Company -->

<!-- Upgrade Plane -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="upgrade_plan">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Upgrade Package</h5>
        <button type="button"
            class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
            data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <form action="company.html">
            <div class="card">
                <div class="card-body pb-0">
                    <div class="mb-1">
                        <div class="">
                            <h5 class="mb-3">Current Plan Details</h5>
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Company Name</p>
                                        <p class="text-gray-9">NovaWave LLC</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Plan Name</p>
                                        <p class="text-gray-9">Advanced</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Plan Type</p>
                                        <p class="text-gray-9">Monthly</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Price</p>
                                        <p class="text-gray-9">200</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Register Date</p>
                                        <p class="text-gray-9">12 Sep 2024</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Expiring On</p>
                                        <p class="text-gray-9">11 Oct 2024</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h5 class="mb-3">Change Plan</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Plan Name <span class="text-danger">*</span></label>
                                    <select class="select">
                                        <option>Select</option>
                                        <option>Advanced</option>
                                        <option>Basic</option>
                                        <option>Enterprise</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 ">
                                    <label class="col-form-label">Plan Type <span class="text-danger">*</span></label>
                                    <select class="select">
                                        <option>Select</option>
                                        <option>Monthly</option>
                                        <option>Yearly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Ammount<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Payment Date <span
                                            class="text-danger">*</span></label>
                                    <div class="input-icon-end position-relative">
                                        <input type="text" class="form-control datetimepicker"
                                            placeholder="dd/mm/yyyy">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar text-gray-7"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Next Payment Date <span
                                            class="text-danger">*</span></label>
                                    <div class="input-icon-end position-relative">
                                        <input type="text" class="form-control datetimepicker"
                                            placeholder="dd/mm/yyyy">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar text-gray-7"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Expiring On <span
                                            class="text-danger">*</span></label>
                                    <div class="input-icon-end position-relative">
                                        <input type="text" class="form-control datetimepicker"
                                            placeholder="dd/mm/yyyy">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar text-gray-7"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>
<!-- /Upgrade Plane -->

<!-- Delete Company -->
<div class="modal fade" id="delete_company" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                        <i class="ti ti-trash-x fs-36 text-danger"></i>
                    </div>
                    <h4 class="mb-2">Remove Company?</h4>
                    <p class="mb-0">Are you sure you want to remove <br> company you selected.</p>
                    <div class="d-flex align-items-center justify-content-center mt-4">
                        <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                        <a href="company.html" class="btn btn-danger">Yes, Delete it</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Company -->

<!-- Company Detail -->
{{-- <div class="modal fade custom-modal" id="company_detail">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Company Detail</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="modal-body pb-0">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center rounded bg-light-400 p-3">
                            <div class="file-name-icon d-flex align-items-center">
                                <a href="#" class="avatar avatar-sm border rounded p-1 me-2 flex-shrink-0">
                                    <img src="assets/img/icons/company-icon-01.svg" class="w-auto h-auto" alt="img">
                                </a>
                                <div>
                                    <p class="text-gray-9 fw-medium mb-0">NovaWave LLC</p>
                                </div>
                            </div>
                            <span class="badge badge-success">Active</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-9 fw-medium">Basic Info</p>
                        <div class="border-bottom mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Account URL</p>
                                        <p class="text-gray-9">nw.example.com</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Phone Number</p>
                                        <p class="text-gray-9">+1 875455453</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Website</p>
                                        <p class="text-gray-9">www.exmple.com</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Currency</p>
                                        <p class="text-gray-9">United Stated Dollar (USD)</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Language</p>
                                        <p class="text-gray-9">English</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Addresss</p>
                                        <p class="text-gray-9">3705 Lynn Avenue, Phelps, WI 54554</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-9 fw-medium">Plan Details</p>
                        <div>
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Plan Name</p>
                                        <p class="text-gray-9">Advanced</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Plan Type</p>
                                        <p class="text-gray-9">Monthly</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Price</p>
                                        <p class="text-gray-9">$200</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Register Date</p>
                                        <p class="text-gray-9">12 Sep 2024</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Expiring On</p>
                                        <p class="text-gray-9">11 Oct 2024</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
        </div>
    </div> --}}
<div class="modal fade custom-modal" id="company_detail" tabindex="-1" aria-labelledby="companyDetailLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="companyDetailLabel">Company Detail</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="modal-body pb-0">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center rounded bg-light-400 p-3">
                        <div class="file-name-icon d-flex align-items-center">
                            <a href="#" class="avatar avatar-sm border rounded p-1 me-2 flex-shrink-0">
                                <!-- Optional: You can update this img src dynamically if needed -->
                                <img src="assets/img/icons/company-icon-01.svg" class="w-auto h-auto"
                                    alt="Company Logo" id="company_logo_modal">
                            </a>
                            <div>
                                <p class="text-gray-9 fw-medium mb-0" id="company_name">-</p>
                            </div>
                        </div>
                        <span class="badge badge-success" id="company_status">Active</span>
                    </div>
                </div>
                <div>
                    <p class="text-gray-9 fw-medium">Basic Info</p>
                    <div class="border-bottom mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <p class="fs-12 mb-0">Account URL</p>
                                    <p class="text-gray-9" id="account_url">-</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <p class="fs-12 mb-0">Phone Number</p>
                                    <p class="text-gray-9" id="phone_number">-</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <p class="fs-12 mb-0">Website</p>
                                    <p class="text-gray-9" id="website">-</p>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <p class="fs-12 mb-0">Currency</p>
                                    <p class="text-gray-9" id="currency">-</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <p class="fs-12 mb-0">Language</p>
                                    <p class="text-gray-9" id="language">-</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <p class="fs-12 mb-0">Addresss</p>
                                    <p class="text-gray-9" id="address">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-9 fw-medium">Plan Details</p>
                    <div>
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <p class="fs-12 mb-0">Plan Name</p>
                                    <p class="text-gray-9" id="plan_name">-</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <p class="fs-12 mb-0">Plan Type</p>
                                    <p class="text-gray-9" id="plan_type">-</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <p class="fs-12 mb-0">Price</p>
                                    <p class="text-gray-9" id="price">-</p>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <p class="fs-12 mb-0">Register Date</p>
                                    <p class="text-gray-9" id="register_date">-</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <p class="fs-12 mb-0">Expiring On</p>
                                    <p class="text-gray-9" id="expire_date">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Company Detail -->

@push('scripts')
<script>
    const defaultImgLink = "{{ asset('assets/img/profiles/avatar-14.jpg') }}";
</script>

<script>
    function initCompanyDetailsTable() {
        const tableId = '#boxes-lists';
        const table = $(tableId).DataTable({
            order: [],
            ajax: {
                url: '{{ route('get.company', ['box_slug' => request()->route('box_slug')]) }}',
                data: function(d) {
                    d.sort_by = window.currentSortBy || 'recently_added';
                    if (window.currentStartDate) d.start_date = window.currentStartDate;
                    if (window.currentEndDate) d.end_date = window.currentEndDate;
                },
                dataSrc: function(json) {
                    return json.success ? json.data : [];
                }
            },
            columns: [{
                    data: null,
                    render: () => `
                    <label class="checkboxs">
                        <input type="checkbox">
                        <span class="checkmarks"></span>
                    </label>`,
                    orderable: false
                },
                {
                    data: null,
                    render: () => `
                    <div class="set-star rating-select">
                        <i class="fa fa-star"></i>
                    </div>`,
                    orderable: false
                },
                {
                    data: 'company_logo',
                    title: 'Company',
                    render: (logo, type, row) => {
                        const name = row.name || '-';
                        const initial = name[0]?.toUpperCase() || '-';
                        const defaultImg = defaultImgLink;
                        const imageSrc = logo ? `/storage/${logo}` : defaultImg;
                        return `
            <div class="d-flex align-items-center">
                <div class="position-relative me-2">
                    <img
                        src="${imageSrc}"
                        alt="${initial}"
                        class="rounded-circle"
                        style="width: 32px; height: 32px; object-fit: cover;"
                    />
                    ${!logo ? `
                        <span class="position-absolute top-50 start-50 translate-middle text-white fw-bold"
                              style="font-size: 14px;">${initial}</span>
                    ` : ''}
                </div>
                <span>${name}</span>
            </div>
        `;
                    },
                    orderable: false
                },

                {
                    data: 'superadmin',
                    render: (data) => data && data.email ? data.email : '-'
                },
                {
                    data: 'domain',
                    render: (data) => data ?? '-'
                },
                {
                    data: 'industry',
                    render: (data) => data ?? '-'
                },
                {
                    data: 'created_at',
                    render: (data) => {
                        if (!data) return '-';
                        const date = new Date(data);
                        return date.toLocaleDateString('en-IN', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                    }
                },
                {
                    data: 'superadmin',
                    render: (data) => {
                        const verified = data && data.email_verified_at;
                        const badgeClass = verified ? 'badge-success' : 'badge-warning';
                        const label = verified ? 'Verified' : 'Unverified';
                        return `<span class="badge ${badgeClass}">${label}</span>`;
                    },
                    title: 'Email Status'
                },
                {
                    data: 'status',
                    render: (data) => {
                        const cls = (data?.toLowerCase() === 'enabled') ? 'bg-success' : 'bg-danger';
                        return `<span class="badge badge-pill badge-status ${cls}">${data}</span>`;
                    }
                },
                {
                    data: null,
                    className: 'text-end',
                    render: (data, type, row, meta) => `
                    <div class="dropdown table-action">
                        <a href="#" class="action-icon" data-bs-toggle="dropdown">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="/{{ request()->route('box_slug') }}/superadmin/company/impersonate/${row.id}"
                               target="_blank"
                               class="dropdown-item text-primary">
                                <i class="ti ti-login"></i> Login As Admin
                            </a>
                            <a href="#"
                               class="dropdown-item view-company-btn"
                               data-bs-toggle="modal"
                               data-bs-target="#company_detail"
                               data-row-index="${meta.row}">
                                <i class="ti ti-eye text-info"></i> View
                            </a>
                            <a class="dropdown-item edit-company-btn" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit" data-id="${row.id}" data-row-index="${meta.row}">
                                <i class="ti ti-edit text-blue"></i> Edit
                            </a>
                            <a class="dropdown-item delete-company-btn" href="#" data-bs-toggle="modal" data-bs-target="#delete_company" data-id="${row.id}">
                                <i class="ti ti-trash text-danger"></i> Delete
                            </a>
                        </div>
                    </div>`
                }
            ],
            drawCallback: function(settings) {
                const count = this.api().page.info().recordsDisplay;
                $('.count-title').text(count);
            }
        });

        // Filter: Date Range
        if ($('.bookingrange').length > 0) {
            $('.bookingrange').on('apply.daterangepicker', function(ev, picker) {
                window.currentStartDate = picker.startDate.format('DD/MM/YYYY');
                window.currentEndDate = picker.endDate.format('DD/MM/YYYY');
                table.ajax.reload();
            });
            $('.bookingrange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                window.currentStartDate = null;
                window.currentEndDate = null;
                table.ajax.reload();
            });
        }

        // Filter: Sort By
        $('.sort-item').on('click', function(e) {
            e.preventDefault();
            window.currentSortBy = $(this).data('sort');
            table.ajax.reload();
        });

        // Toggle Columns
        $('.toggle-column').on('change', function() {
            var columnIdx = $(this).data('col');
            var column = table.column(columnIdx);
            column.visible($(this).is(':checked'));
        });

        // Select-all functionality
        $('#select-all').off('click').on('click', function() {
            const isChecked = this.checked;
            $(tableId).find('tbody input[type="checkbox"]').prop('checked', isChecked);
        });
        
        // Search functionality
        $('#company-search').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Click handler for View button
        $(tableId + ' tbody').off('click', 'a.view-company-btn').on('click', 'a.view-company-btn', function(e) {
            e.preventDefault();

            const rowIndex = $(this).data('row-index');
            const rowData = table.row(rowIndex).data();

            if (!rowData) return;

            // Update company logo in modal (optional)
            if (rowData.company_logo) {
                $('#company_logo_modal').attr('src', `/storage/${rowData.company_logo}`).show();
            } else {
                $('#company_logo_modal').hide();
            }

            // Populate modal fields
            $('#company_name').text(rowData.name || '-');

            const statusBadge = $('#company_status');
            if (rowData.status?.toLowerCase() === 'enabled') {
                statusBadge.removeClass('badge-danger').addClass('badge-success').text('Active');
            } else {
                statusBadge.removeClass('badge-success').addClass('badge-danger').text('Inactive');
            }

            // Basic Info
            $('#account_url').text(rowData.domain || '-');
            $('#phone_number').text(rowData.phone || '-');
            $('#website').text(rowData.slug || '-');
            $('#currency').text(rowData.currency || '-');
            $('#language').text(rowData.language || '-');
            $('#address').text(rowData.address || '-');

            // Plan Details
            $('#plan_name').text(rowData.plan_name || '-');
            $('#plan_type').text(rowData.plan_type || '-');
            $('#price').text(rowData.price || '-');
            $('#register_date').text(rowData.register_date || '-');
            $('#expire_date').text(rowData.expire_date || '-');
        });

        // Click handler for Edit button
        $(tableId + ' tbody').off('click', 'a.edit-company-btn').on('click', 'a.edit-company-btn', function(e) {
            e.preventDefault();
            const rowIndex = $(this).data('row-index');
            const rowData = table.row(rowIndex).data();
            if (!rowData) return;

            $('#edit_company_id').val(rowData.id);
            $('#edit_name').val(rowData.name);
            $('#edit_company_email').val(rowData.company_email);
            $('#edit_domain').val(rowData.domain);
            $('#edit_phone').val(rowData.phone);
            $('#edit_address').val(rowData.address);
            
            $('#edit_password').val('');
            $('#edit_password_confirmation').val('');
            $('#edit_company_logo').val('');

            if (rowData.company_logo) {
                $('#edit_logo_preview').attr('src', `/storage/${rowData.company_logo}`);
            } else {
                $('#edit_logo_preview').attr('src', '{{ asset('assets/img/profiles/avatar-20.jpg') }}');
            }
        });

        // Form Submit for Edit Company
        $('#edit_company_form').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_company_id').val();
            const formData = new FormData(this);
            const submitBtn = $('#btn_edit_company');
            const originalText = submitBtn.text();
            
            submitBtn.prop('disabled', true).text('Saving...');
            
            $.ajax({
                url: `/{{ request()->route('box_slug') }}/superadmin/company/update/${id}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        $('#offcanvas_edit').offcanvas('hide');
                        table.ajax.reload(null, false);
                    } else {
                        alert(res.message || 'Error updating company');
                    }
                },
                error: function(xhr) {
                    const err = xhr.responseJSON;
                    alert(err?.message || 'Failed to update company');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });
    }

    $(document).ready(function() {
        initCompanyDetailsTable();
    });
</script>
@endpush
@endsection
