@extends('layouts.main')
@section('content')
@section('title', 'Integration')


<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <div class="row">
            <div class="col-md-12">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">Bulk Uploads</h4>
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

                <div class="card ">
                    <div class="card-header">
                        <!-- Search -->
                        <div class="row align-items-center">
                            <div class="col-sm-4">
                               <!--  <div class="icon-form mb-3 mb-sm-0">
                                    <span class="form-icon"><i class="ti ti-search"></i></span>
                                    <input type="text" class="form-control" placeholder="Search...">
                                </div> -->
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
                                    <button type="button" class="btn btn-danger me-2" id="deleteSelectedBtn" style="display: none;">
                                        <i class="ti ti-trash me-2"></i>Delete Selected
                                    </button>
                                    <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas"
                                        data-bs-target="#offcanvas_add"><i
                                            class="ti ti-square-rounded-plus me-2"></i>Upload</a>
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
                                                <a href="javascript:void(0);" class="dropdown-item">
                                                    <i class="ti ti-circle-chevron-right me-1"></i>Ascending
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">
                                                    <i class="ti ti-circle-chevron-right me-1"></i>Descending
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">
                                                    <i class="ti ti-circle-chevron-right me-1"></i>Recently
                                                    Viewed
                                                </a>
                                            </li>
                                            <li></li>
                                                <a href="javascript:void(0);" class="dropdown-item">
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
                                <!-- <div class="dropdown me-2">
                                    <a href="javascript:void(0);" class="btn bg-soft-purple text-purple"
                                        data-bs-toggle="dropdown" data-bs-auto-close="outside"><i
                                            class="ti ti-columns-3 me-2"></i>Manage Columns</a>
                                    <div class="dropdown-menu  dropdown-menu-md-end dropdown-md p-3">
                                        <h4 class="mb-2 fw-semibold">Want to manage datatables?</h4>
                                        <p class="mb-3">Please drag and drop your column to reorder your table
                                            and enable see option as you want.</p>
                                        <div class="border-top pt-3">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Name</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-name" class="check">
                                                    <label for="col-name" class="checktoggle"></label>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Phone</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-phone" class="check">
                                                    <label for="col-phone" class="checktoggle"></label>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Email</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-email" class="check">
                                                    <label for="col-email" class="checktoggle"></label>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Tags</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-tag" class="check">
                                                    <label for="col-tag" class="checktoggle"></label>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Location</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-loc" class="check">
                                                    <label for="col-loc" class="checktoggle"></label>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Rating</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-rate" class="check">
                                                    <label for="col-rate" class="checktoggle"></label>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Owner</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-owner" class="check">
                                                    <label for="col-owner" class="checktoggle"></label>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Contact</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-contact" class="check"
                                                        checked="">
                                                    <label for="col-contact" class="checktoggle"></label>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Status</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-status" class="check">
                                                    <label for="col-status" class="checktoggle"></label>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Action</p>
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="col-action" class="check">
                                                    <label for="col-action" class="checktoggle"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>  -->
                                <!-- <div class="form-sorts dropdown me-2">
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
                                                        <a href="companies.html" class="btn btn-primary">Filter</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="view-icons">
                                    <a href="" class="active"><i class="ti ti-list-tree"></i></a>
                                    <a href=""><i class="ti ti-grid-dots"></i></a>
                                </div>
                            </div>
                        </div>
                        <!-- /Filter -->

                        <!-- Contact List -->
                        <div class="table-responsive custom-table">
                            <table class="table" id="bulkUploadsTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort">
                                            <label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
                                        </th>
                                        <th>ID</th>
                                        <th>File Name</th>
                                        <th>File Size</th>
                                        <th>File Type</th>
                                        <th>Uploaded By</th>
                                        <th>Uploaded At</th>
                                        <th>Status</th>
                                        <th>Total Rows</th>
                                        <th>Successful</th>
                                        <th>Skipped</th>
                                        <th>Duplicates</th>
                                        <th>Leads Count</th>
                                        <th>Percentage</th>
                                        <th>Processed At</th>
                                        <th>Export</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="16" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2 text-muted">Loading bulk uploads...</p>
                                        </td>
                                    </tr>
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
        <h5 class="fw-semibold">Upload CSV/Excel</h5>
        <button type="button"
            class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
            data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <form id="csvUploadForm" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <div class="profile-upload">
                            <div class="profile-upload-img">
                                <span><i class="ti ti-file"></i></span>
                                <img src="assets/img/profiles/avatar-20.jpg" alt="img" class="preview1"
                                    style="display:none;">
                                <button type="button" class="profile-remove" style="display:none;">
                                    <i class="ti ti-x"></i>
                                </button>
                            </div>
                            <div class="profile-upload-content">
                                <label class="profile-upload-btn">
                                    <i class="ti ti-file-broken"></i> Upload CSV/Excel File
                                    <input type="file" id="csvFileInput" name="file"
                                        accept=".csv,.txt,.xlsx,.xls" class="input-img" required>
                                </label>
                                <p>OR Drag And Drop</p>
                                <div id="uploadStatus" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2 mb-2 flex-wrap">
                            <h5 class="mb-0">Download Sample File:</h5>
                            <a href="{{ route('bulk.download.sample', ['box_slug' => request()->route('box_slug'), 'type' => 'csv']) }}" class="btn btn-sm btn-outline-primary" download>
                                <i class="ti ti-download"></i> CSV
                            </a>
                            <a href="{{ route('bulk.download.sample', ['box_slug' => request()->route('box_slug'), 'type' => 'xlsx']) }}" class="btn btn-sm btn-outline-success" download>
                                <i class="ti ti-download"></i> Excel
                            </a>
                        </div>
                        <h5 style="font-weight:normal;">CSV or Excel files allowed (Max 50MB).</h5>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                <button type="submit" class="btn btn-primary" id="uploadBtn">Upload</button>
            </div>
        </form>
    </div>
</div>
<!-- /Add Company -->

<!-- Full Screen Loader -->
<div id="uploadLoader" class="fullscreen-loader-overlay" style="display: none;">
    <div class="loader-content">
        <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-4 mb-0 fw-semibold fs-5">Processing file...</p>
        <small class="text-muted d-block mt-2" id="loaderMessage">Please wait while we process your file</small>
    </div>
</div>
<!-- /Full Screen Loader -->

<!-- Label Mapping Offcanvas -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="labelMappingOffcanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Map File Fields</h5>
        <button type="button"
            class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
            data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <form id="mappingForm">
            <input type="hidden" id="uploadedFilePath" name="file_path">

            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="mb-3">
                        <h5 class="mb-1">Settings</h5>
                        <label class="col-form-label">Create Duplicate if exists</label>
                        <select class="select" id="createDuplicate" name="create_duplicate">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Conditional Settings - Always show now -->
            <div id="duplicateSettings" class="row mb-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="col-form-label">Assign To</label>
                        <select class="select" id="assignToEnabled" name="assign_to_enabled">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="col-form-label">Status</label>
                        <select class="select" id="statusEnabled" name="status_enabled">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <h5 class="mb-3">Field Mapping</h5>
                    <div id="mappingContainer">
                        <div class="text-center p-4">
                            <i class="ti ti-loader"></i> Loading...
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-end mt-4">
                <button type="button" class="btn btn-light me-2" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary" id="saveMappingBtn">Save Mapping</button>
            </div>
        </form>
    </div>
</div>
<!-- /Label Mapping Offcanvas -->

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
        <form action="companies.html">
            <div class="accordion" id="main_accordion">
                <!-- Basic Info -->
                <div class="accordion-item rounded mb-3">
                    <div class="accordion-header">
                        <a href="#"
                            class="accordion-button accordion-custom-button bg-white rounded fw-medium text-dark"
                            data-bs-toggle="collapse" data-bs-target="#basic">
                            <span class="avatar avatar-md rounded text-dark border me-2"><i
                                    class="ti ti-user-plus fs-20"></i></span>
                            Basic Info
                        </a>
                    </div>
                    <div class="accordion-collapse collapse show" id="basic" data-bs-parent="#main_accordion">
                        <div class="accordion-body border-top">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="profile-upload">
                                            <div class="profile-upload-img">
                                                <span><i class="ti ti-photo"></i></span>
                                                <img src="assets/img/profiles/avatar-20.jpg" alt="img"
                                                    class="preview1">
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
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Company Name</label>
                                        <input type="text" class="form-control" value="NovaWave LLC">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="col-form-label">Email <span
                                                    class="text-danger">*</span></label>
                                            <div class="status-toggle small-toggle-btn d-flex align-items-center">
                                                <span class="me-2 label-text">Email Opt Out</span>
                                                <input type="checkbox" id="user" class="check" checked="">
                                                <label for="user" class="checktoggle"></label>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control" value="robertson@example.com">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Phone 1 <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="1234567890">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Phone 2</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Fax</label>
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
                                    <div class="mb-3">
                                        <label class="col-form-label">Ratings</label>
                                        <div class="icon-form-end">
                                            <span class="form-icon"><i class="ti ti-star"></i></span>
                                            <input type="text" class="form-control" placeholder="4.2">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="fmb-3">
                                        <label class="col-form-label">Owner</label>
                                        <select class="select-img">
                                            <option data-image="assets/img/profiles/avatar-14.jpg" selected="">
                                                Hendry Milner</option>
                                            <option data-image="assets/img/profiles/avatar-15.jpg">Guilory Berggren
                                            </option>
                                            <option data-image="assets/img/profiles/avatar-16.jpg">Jami Carlile
                                            </option>
                                            <option data-image="assets/img/profiles/avatar-13.jpg">Theresa Nelson
                                            </option>
                                            <option data-image="assets/img/profiles/avatar-24.jpg">Smith Cooper
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Tags </label>
                                        <input class="input-tags form-control" type="text" data-role="tagsinput"
                                            name="Label" value="Collab">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label class="col-form-label">Deals</label>
                                            <a href="#" class="label-add" data-bs-toggle="offcanvas"
                                                data-bs-target="#offcanvas_add_2"><i
                                                    class="ti ti-square-rounded-plus"></i>Add New</a>
                                        </div>
                                        <select class="select2">
                                            <option>Choose</option>
                                            <option selected="">Collins</option>
                                            <option>Konopelski</option>
                                            <option>Adams</option>
                                            <option>Schumm</option>
                                            <option>Wisozk</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Source <span
                                                class="text-danger">*</span></label>
                                        <select class="select2">
                                            <option>Choose</option>
                                            <option>Phone Calls</option>
                                            <option>Social Media</option>
                                            <option>Referral Sites</option>
                                            <option>Web Analytics</option>
                                            <option>Previous Purchases</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Industry <span
                                                class="text-danger">*</span></label>
                                        <select class="select">
                                            <option>Choose</option>
                                            <option>Retail Industry</option>
                                            <option>Banking</option>
                                            <option>Hotels</option>
                                            <option>Financial Services</option>
                                            <option>Insurance</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Contacts</label>
                                        <select class="multiple-img" multiple="multiple">
                                            <option data-image="assets/img/profiles/avatar-19.jpg">Darlee
                                                Robertson</option>
                                            <option data-image="assets/img/profiles/avatar-20.jpg" selected="">
                                                Sharon Roy</option>
                                            <option data-image="assets/img/profiles/avatar-21.jpg">Vaughan Lewis
                                            </option>
                                            <option data-image="assets/img/profiles/avatar-23.jpg">Jessica Louise
                                            </option>
                                            <option data-image="assets/img/profiles/avatar-16.jpg">Carol Thomas
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Currency <span
                                                class="text-danger">*</span></label>
                                        <select class="select">
                                            <option>Choose</option>
                                            <option selected="">Dollar</option>
                                            <option>Euro</option>
                                            <option>Pound</option>
                                            <option>Rupee</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Language <span
                                                class="text-danger">*</span></label>
                                        <select class="select">
                                            <option>Choose</option>
                                            <option selected="">English</option>
                                            <option>Arabic</option>
                                            <option>French</option>
                                            <option>German</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-0">
                                        <label class="col-form-label">Description <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Basic Info -->

                <!-- Address Info -->
                <div class="accordion-item border-top rounded mb-3">
                    <div class="accordion-header">
                        <a href="#"
                            class="accordion-button accordion-custom-button rounded bg-white fw-medium text-dark"
                            data-bs-toggle="collapse" data-bs-target="#address">
                            <span class="avatar avatar-md rounded text-dark border me-2"><i
                                    class="ti ti-map-pin-cog fs-20"></i></span>
                            Address Info
                        </a>
                    </div>
                    <div class="accordion-collapse collapse" id="address" data-bs-parent="#main_accordion">
                        <div class="accordion-body border-top">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Street Address </label>
                                        <input type="text" class="form-control" value="87 Griffin Street">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Country</label>
                                        <select class="select">
                                            <option>Choose</option>
                                            <option selected="">USA</option>
                                            <option>Canada</option>
                                            <option>Germany</option>
                                            <option>France</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">State / Province </label>
                                        <select class="select">
                                            <option>Choose</option>
                                            <option selected="">California</option>
                                            <option>New York</option>
                                            <option>Texas</option>
                                            <option>Florida</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 mb-md-0">
                                        <label class="col-form-label">City </label>
                                        <select class="select">
                                            <option>Choose</option>
                                            <option selected="">Los Angeles</option>
                                            <option>San Diego</option>
                                            <option>Fresno</option>
                                            <option>San Francisco</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-0">
                                        <label class="col-form-label">Zipcode </label>
                                        <input type="text" class="form-control" value="90001">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Address Info -->

                <!-- Social Profile -->
                <div class="accordion-item border-top rounded mb-3">
                    <div class="accordion-header">
                        <a href="#"
                            class="accordion-button accordion-custom-button rounded bg-white fw-medium text-dark"
                            data-bs-toggle="collapse" data-bs-target="#social">
                            <span class="avatar avatar-md rounded text-dark border me-2"><i
                                    class="ti ti-social fs-20"></i></span>
                            Social Profile
                        </a>
                    </div>
                    <div class="accordion-collapse collapse" id="social" data-bs-parent="#main_accordion">
                        <div class="accordion-body border-top">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Facebook</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Skype </label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Linkedin </label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Twitter</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 mb-md-0">
                                        <label class="col-form-label">Whatsapp</label>
                                        <input type="text" class="form-control" value="1234567890">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-0">
                                        <label class="col-form-label">Instagram</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Social Profile -->

                <!-- Access -->
                <div class="accordion-item border-top rounded mb-3">
                    <div class="accordion-header">
                        <a href="#"
                            class="accordion-button accordion-custom-button rounded bg-white fw-medium text-dark"
                            data-bs-toggle="collapse" data-bs-target="#access-info">
                            <span class="avatar avatar-md rounded text-dark border me-2"><i
                                    class="ti ti-accessible fs-20"></i></span>
                            Access
                        </a>
                    </div>
                    <div class="accordion-collapse collapse" id="access-info" data-bs-parent="#main_accordion">
                        <div class="accordion-body border-top">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Visibility</label>
                                        <div class="d-flex flex-wrap">
                                            <div class="me-2">
                                                <input type="radio" class="status-radio" id="public"
                                                    name="visible">
                                                <label for="public">Public</label>
                                            </div>
                                            <div class="me-2">
                                                <input type="radio" class="status-radio" id="private"
                                                    name="visible">
                                                <label for="private">Private</label>
                                            </div>
                                            <div data-bs-toggle="modal" data-bs-target="#access_view">
                                                <input type="radio" class="status-radio" id="people"
                                                    name="visible">
                                                <label for="people">Select People</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-0">
                                        <label class="col-form-label">Status</label>
                                        <div class="d-flex flex-wrap">
                                            <div class="me-2">
                                                <input type="radio" class="status-radio" id="active"
                                                    name="status" checked="">
                                                <label for="active">Active</label>
                                            </div>
                                            <div>
                                                <input type="radio" class="status-radio" id="inactive"
                                                    name="status">
                                                <label for="inactive">Inactive</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Access -->
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                <button type="button" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
<!-- /Edit Company -->

<!-- Delete Company -->
<div class="modal fade" id="delete_from_list" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                        <i class="ti ti-trash-x fs-36 text-danger"></i>
                    </div>
                    <h4 class="mb-2">Remove?</h4>
                    <p class="mb-0">Are you sure you want to remove <br> you selected.</p>
                    <div class="d-flex align-items-center justify-content-center mt-4">
                        <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                        <a href="companies.html" class="btn btn-danger">Yes, Delete it</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Company -->

<!-- Create Company -->
<div class="modal custom-modal fade" id="create_success" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 m-0 justify-content-end">
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="success-message text-center">
                    <div class="success-popup-icon bg-light-blue">
                        <i class="ti ti-building"></i>
                    </div>
                    <h3>Company Created Successfully!!!</h3>
                    <p>View the details of company, created</p>
                    <div class="col-lg-12 text-center modal-btn">
                        <a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
                        <a href="company-details.html" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Create Company -->

<!-- Create Deal -->
<div class="modal custom-modal fade" id="create_success_1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 m-0 justify-content-end">
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="success-message text-center">
                    <div class="success-popup-icon bg-light-blue">
                        <i class="ti ti-medal"></i>
                    </div>
                    <h3>Deal Created Successfully!!!</h3>
                    <p>View the details of deal, created</p>
                    <div class="col-lg-12 text-center modal-btn">
                        <a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
                        <a href="deals-details.html" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Create Deal -->

<!-- Add New View -->
<div class="modal custom-modal fade" id="save_view" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New View</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form action="companies.html">
                    <div class="mb-3">
                        <label class="col-form-label">View Name</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="modal-btn text-end">
                        <a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-danger">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add New View -->

<!-- Access -->
<div class="modal fade" id="access_view" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Access For</h5>
                <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form action="companies.html">
                <div class="modal-body">
                    <div class="icon-form mb-3">
                        <span class="form-icon"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search">
                    </div>
                    <div class="access-wrap mb-0">
                        <ul>
                            <li class="select-people-checkbox">
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                    <span class="people-profile">
                                        <img src="assets/img/profiles/avatar-19.jpg" alt="">
                                        <a href="#">Darlee Robertson</a>
                                    </span>
                                </label>
                            </li>
                            <li class="select-people-checkbox">
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                    <span class="people-profile">
                                        <img src="assets/img/profiles/avatar-20.jpg" alt="">
                                        <a href="#">Sharon Roy</a>
                                    </span>
                                </label>
                            </li>
                            <li class="select-people-checkbox">
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                    <span class="people-profile">
                                        <img src="assets/img/profiles/avatar-21.jpg" alt="">
                                        <a href="#">Vaughan Lewis</a>
                                    </span>
                                </label>
                            </li>
                            <li class="select-people-checkbox">
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                    <span class="people-profile">
                                        <img src="assets/img/profiles/avatar-01.jpg" alt="">
                                        <a href="#">Jessica Louise</a>
                                    </span>
                                </label>
                            </li>
                            <li class="select-people-checkbox">
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                    <span class="people-profile">
                                        <img src="assets/img/profiles/avatar-16.jpg" alt="">
                                        <a href="#">Carol Thomas</a>
                                    </span>
                                </label>
                            </li>
                            <li class="select-people-checkbox">
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                    <span class="people-profile">
                                        <img src="assets/img/profiles/avatar-22.jpg" alt="">
                                        <a href="#">Dawn Mercha</a>
                                    </span>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center justify-content-end m-0">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Access -->

<!-- Add New Deals -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add_2">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Add New Deals</h5>
        <button type="button"
            class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
            data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <form action="contacts.html">
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="col-form-label">Deal Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <label class="col-form-label">Pipeine <span class="text-danger">*</span></label>
                        </div>
                        <select class="select2">
                            <option>Choose</option>
                            <option>Sales</option>
                            <option>Marketing</option>
                            <option>Calls</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="col-form-label">Status <span class="text-danger">*</span></label>
                        <select class="select2">
                            <option>Choose</option>
                            <option>Open</option>
                            <option>Lost</option>
                            <option>Won</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="mb-3">
                        <label class="col-form-label">Deal Value<span class="text-danger"> *</span></label>
                        <input class="form-control" type="text">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="mb-3">
                        <label class="col-form-label">Currency <span class="text-danger">*</span></label>
                        <select class="select">
                            <option>Choose</option>
                            <option>Dollar</option>
                            <option>Euro</option>
                            <option>Pound</option>
                            <option>Rupee</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="mb-3">
                        <label class="col-form-label">Period <span class="text-danger">*</span></label>
                        <select class="select">
                            <option>Choose</option>
                            <option>Days</option>
                            <option>Month</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="mb-3">
                        <label class="col-form-label">Period Value <span class="text-danger">*</span></label>
                        <input class="form-control" type="text">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="col-form-label">Contact <span class="text-danger">*</span></label>
                        <select class="multiple-img" multiple="multiple">
                            <option data-image="assets/img/profiles/avatar-19.jpg" selected="">Darlee Robertson
                            </option>
                            <option data-image="assets/img/profiles/avatar-20.jpg">Sharon Roy</option>
                            <option data-image="assets/img/profiles/avatar-21.jpg">Vaughan Lewis</option>
                            <option data-image="assets/img/profiles/avatar-23.jpg">Jessica Louise</option>
                            <option data-image="assets/img/profiles/avatar-16.jpg">Carol Thomas</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">Project <span class="text-danger">*</span></label>
                        <select class="select" multiple="multiple">
                            <option selected="">Devops Design</option>
                            <option selected="">MargrateDesign</option>
                            <option selected="">UI for Chat</option>
                            <option>Web Chat</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="col-form-label">Due Date <span class="text-danger">*</span></label>
                        <div class="icon-form">
                            <span class="form-icon"><i class="ti ti-calendar-check"></i></span>
                            <input type="text" class="form-control datetimepicker" placeholder="">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="col-form-label">Expected Closing Date <span
                                class="text-danger">*</span></label>
                        <div class="icon-form">
                            <span class="form-icon"><i class="ti ti-calendar-check"></i></span>
                            <input type="text" class="form-control datetimepicker" placeholder="">
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="col-form-label">Assignee <span class="text-danger">*</span></label>
                        <select class="multiple-img" multiple="multiple">
                            <option data-image="assets/img/profiles/avatar-19.jpg">Darlee Robertson</option>
                            <option data-image="assets/img/profiles/avatar-20.jpg" selected="">Sharon Roy
                            </option>
                            <option data-image="assets/img/profiles/avatar-21.jpg">Vaughan Lewis</option>
                            <option data-image="assets/img/profiles/avatar-23.jpg">Jessica Louise</option>
                            <option data-image="assets/img/profiles/avatar-16.jpg">Carol Thomas</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="col-form-label">Follow Up Date <span class="text-danger">*</span></label>
                        <div class="icon-form">
                            <span class="form-icon"><i class="ti ti-calendar-check"></i></span>
                            <input type="text" class="form-control datetimepicker" placeholder="">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="col-form-label">Source <span class="text-danger">*</span></label>
                        <select class="select">
                            <option>Select</option>
                            <option>Google</option>
                            <option>Social Media</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="col-form-label">Tags <span class="text-danger">*</span></label>
                        <input class="input-tags form-control" type="text" data-role="tagsinput"
                            name="Label" value="Collab, Rated">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="col-form-label">Priority <span class="text-danger">*</span></label>
                        <select class="select">
                            <option>Select</option>
                            <option>High</option>
                            <option>Low</option>
                            <option>Medium</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="mb-3">
                        <label class="col-form-label">Description <span class="text-danger">*</span></label>
                        <div class="summernote"></div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#create_success_1">Create</button>
            </div>
        </form>
    </div>
</div>
<!-- /Add New Deals -->

<!-- /Delete Invoices -->

<!-- Delete Bulk Upload Confirmation Modal -->
<div class="modal fade" id="deleteBulkUploadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                        <i class="ti ti-trash-x fs-36 text-danger"></i>
                    </div>
                    <h4 class="mb-2">Delete Bulk Upload?</h4>
                    <p class="mb-0">Are you sure you want to delete <strong id="deleteFileName"></strong>?</p>
                    <p class="text-muted small mt-2">This action cannot be undone. The file and all related statistics will be permanently deleted.</p>
                    <div class="d-flex align-items-center justify-content-center mt-4">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                            <i class="ti ti-trash"></i> Yes, Delete it
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Bulk Upload Confirmation Modal -->

<!-- Delete All Bulk Uploads Confirmation Modal -->
<div class="modal fade" id="deleteAllBulkUploadsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                        <i class="ti ti-trash-x fs-36 text-danger"></i>
                    </div>
                    <h4 class="mb-2">Delete Selected Bulk Uploads?</h4>
                    <p class="mb-0">Are you sure you want to delete <strong id="deleteAllCount">0</strong> selected bulk upload(s)?</p>
                    <p class="text-muted small mt-2">This action cannot be undone. All selected files and their statistics will be permanently deleted.</p>
                    <div class="d-flex align-items-center justify-content-center mt-4">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteAllBtn">
                            <i class="ti ti-trash"></i> Yes, Delete All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete All Bulk Uploads Confirmation Modal -->

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#uploadLoader').hide();
            // Handle duplicate settings visibility
            function toggleDuplicateSettings() {
                // Always show the settings so users can update existing leads
                $('#duplicateSettings').show();
            }

            // Initial check on page load
            toggleDuplicateSettings();

            // Listen for changes on duplicate dropdown
            $(document).on('change', '#createDuplicate', function() {
                toggleDuplicateSettings();
            });

            const bulkUploadsTable = $('#bulkUploadsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('bulk.uploads.data', ['box_slug' => request()->route('box_slug')]) }}',
                ordering: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<label class="checkboxs"><input type="checkbox" class="bulk-upload-checkbox" value="${row.id}"><span class="checkmarks"></span></label>`;
                        }
                    },
                    { data: 'id', name: 'id' },
                    {
                        data: 'original_file_name',
                        name: 'original_file_name',
                        render: function(data, type, row) {
                            // Display original filename if available, otherwise show stored filename
                            const displayName = data || row.file_name || '';
                            return '<strong>' + escapeHtml(displayName) + '</strong>';
                        }
                    },
                    { data: 'file_size_formatted', name: 'file_size' },
                    {
                        data: 'file_type',
                        name: 'file_type',
                        render: function(data) {
                            return '<span class="badge bg-secondary">' + escapeHtml(data || '') + '</span>';
                        }
                    },
                    { data: 'uploaded_by_name', name: 'user.name' },
                    { data: 'uploaded_at', name: 'created_at' },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            return getStatusBadge(data);
                        }
                    },
                    { data: 'total_rows', name: 'total_rows' },
                    {
                        data: 'successful_rows',
                        name: 'successful_rows',
                        render: function(data) {
                            return '<span class="text-success fw-bold">' + (data || 0) + '</span>';
                        }
                    },
                    {
                        data: 'skipped_rows',
                        name: 'skipped_rows',
                        render: function(data) {
                            return '<span class="text-warning fw-bold">' + (data || 0) + '</span>';
                        }
                    },
                    {
                        data: 'duplicate_rows',
                        name: 'duplicate_rows',
                        render: function(data) {
                            return '<span class="text-info fw-bold">' + (data || 0) + '</span>';
                        }
                    },
                    {
                        data: 'leads_count',
                        name: 'leads_count',
                        render: function(data) {
                            return '<span class="text-primary fw-bold">' + (data || 0) + '</span>';
                        }
                    },
                    {
                        data: 'percentage',
                        name: 'percentage',
                        render: function(data, type, row) {
                            const pct = row.percentage || 0;
                            return `
                                <div class="progress" style="height: 20px; width: 80px;">
                                    <div class="progress-bar ${getProgressBarClass(row.status)}" role="progressbar"
                                         style="width: ${pct}%"
                                         aria-valuenow="${pct}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        ${pct}%
                                    </div>
                                </div>
                            `;
                        }
                    },
                    { data: 'processed_at_formatted', name: 'processed_at', defaultContent: '-' },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return getReportExportCell(row);
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-end',
                        render: function(data, type, row) {
                            return `
                                <div class="action-icon d-inline-flex" style="gap: 10px;">
                                    <a href="javascript:void(0);" class="view-leads" data-upload-id="${row.id}" title="View Leads">
                                        <i class="ti ti-eye text-info"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="edit-upload" data-upload-id="${row.id}" title="Edit">
                                        <i class="ti ti-edit text-blue"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="map-upload" data-upload-id="${row.id}" title="Map Fields">
                                        <i class="ti ti-columns text-primary"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="download-report" data-upload-id="${row.id}" title="Download Report">
                                        <i class="ti ti-file text-info"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="delete-upload text-danger" data-upload-id="${row.id}" data-file-name="${escapeHtml(row.file_name || '')}" title="Delete">
                                        <i class="ti ti-trash text-danger"></i>
                                    </a>
                                </div>
                            `;
                        }
                    }
                ],
                language: {
                    search: "",
                    searchPlaceholder: "Search",
                    sLengthMenu: "_MENU_",
                    info: "_START_ - _END_ of _TOTAL_ items",
                    paginate: {
                        next: 'Next <i class="fa fa-angle-right"></i>',
                        previous: '<i class="fa fa-angle-left"></i> Prev'
                    }
                },
                drawCallback: function() {
                    attachTableEventHandlers();
                    $('#select-all').prop('checked', false);
                    updateDeleteButtonVisibility();
                }
            });

            window.reloadBulkUploadsTable = function() {
                bulkUploadsTable.ajax.reload(null, false);
            };

            // Function to get status badge HTML
            function getStatusBadge(status) {
                const badges = {
                    'pending': '<span class="badge bg-secondary">Pending</span>',
                    'processing': '<span class="badge bg-primary"><i class="ti ti-loader"></i> Processing</span>',
                    'completed': '<span class="badge bg-success">Completed</span>',
                    'failed': '<span class="badge bg-danger">Failed</span>',
                    'cancelled': '<span class="badge bg-warning">Cancelled</span>'
                };
                return badges[status] || '<span class="badge bg-secondary">' + status + '</span>';
            }

            // Function to get progress bar class based on status
            function getProgressBarClass(status) {
                const classes = {
                    'pending': 'bg-secondary',
                    'processing': 'bg-primary progress-bar-striped progress-bar-animated',
                    'completed': 'bg-success',
                    'failed': 'bg-danger',
                    'cancelled': 'bg-warning'
                };
                return classes[status] || 'bg-secondary';
            }

            function getReportExportCell(upload) {
                const status = upload.report_status || 'pending';
                const progress = upload.report_progress || 0;

                if (status === 'completed' && upload.report_file_path) {
                    return '<span class="badge bg-success"><i class="ti ti-check"></i> Ready</span>';
                }
                if (status === 'processing') {
                    const pct = Math.min(100, Math.max(0, progress)).toFixed(0);
                    return `
                        <small class="text-muted d-block mb-1">Generating... ${pct}%</small>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar"
                                style="width: ${pct}%"
                                aria-valuenow="${pct}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    `;
                }
                if (status === 'failed') {
                    return '<span class="badge bg-danger"><i class="ti ti-alert-triangle"></i> Failed</span>';
                }
                return '<span class="badge bg-light text-muted">Not generated</span>';
            }

            // Function to escape HTML
            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
            }

            // Function to attach event handlers for table actions
            function attachTableEventHandlers() {
                // View Leads
                $('.view-leads').off('click').on('click', function() {
                    const uploadId = $(this).data('upload-id');
                    // Redirect to leads page filtered by bulk_upload_id
                    window.location.href = '{{ route("leads.details", ["box_slug" => request()->route("box_slug")]) }}?bulk_upload_id=' + uploadId;
                });

                // Edit Upload
                $('.edit-upload').off('click').on('click', function(e) {
                    e.preventDefault();
                    const uploadId = $(this).data('upload-id');
                    openMappingFromList(uploadId);
                });

                // Map fields again for an existing upload
                $('.map-upload').off('click').on('click', function(e) {
                    e.preventDefault();
                    const uploadId = $(this).data('upload-id');
                    openMappingFromList(uploadId);
                });

                // Download Report
                $('.download-report').off('click').on('click', function() {
                    const uploadId = $(this).data('upload-id');

                    // Try to get report_file_url from the table row data
                    const table = $('#bulkUploadsTable').DataTable();
                    const rowData = table.row($(this).closest('tr')).data();

                    // If report_file_url exists and report is ready, download directly
                    if (rowData && rowData.report_file_url && rowData.report_status === 'completed') {
                        // Direct download using the stored URL
                        triggerAutoDownload(rowData.report_file_url);
                        return;
                    }

                    // Otherwise, request the report (will generate if needed)
                    requestReportDownload(uploadId);
                });

                // Delete Upload
                $('.delete-upload').off('click').on('click', function() {
                    const uploadId = $(this).data('upload-id');
                    const fileName = $(this).data('file-name');

                    // Store upload ID for deletion
                    $('#deleteBulkUploadModal').data('upload-id', uploadId);
                    $('#deleteFileName').text(fileName);

                    // Show confirmation modal
                    const deleteModal = new bootstrap.Modal(document.getElementById('deleteBulkUploadModal'));
                    deleteModal.show();
                });
            }

            // Refresh table button (if exists)
            $(document).on('click', '#refreshBulkUploads', function() {
                reloadBulkUploadsTable();
            });

            // Handle select all checkbox
            $(document).on('change', '#select-all', function() {
                const isChecked = $(this).is(':checked');
                $('.bulk-upload-checkbox').prop('checked', isChecked);
                updateDeleteButtonVisibility();
            });

            // Handle individual checkbox change
            $(document).on('change', '.bulk-upload-checkbox', function() {
                const totalCheckboxes = $('.bulk-upload-checkbox').length;
                const checkedCheckboxes = $('.bulk-upload-checkbox:checked').length;
                $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
                updateDeleteButtonVisibility();
            });

            // Update delete selected button visibility
            function updateDeleteButtonVisibility() {
                const checkedCount = $('.bulk-upload-checkbox:checked').length;
                if (checkedCount > 0) {
                    $('#deleteSelectedBtn').show().html(`<i class="ti ti-trash me-2"></i>Delete Selected (${checkedCount})`);
                } else {
                    $('#deleteSelectedBtn').hide();
                }
            }

            // Confirm delete single upload
            // Helper function to safely re-enable buttons after actions
            function reEnableButton(btn, originalText, delay) {
                delay = delay || 0;
                setTimeout(function() {
                    if (btn && btn.length) {
                        btn.prop('disabled', false).html(originalText);
                    }
                }, delay);
            }

            $(document).on('click', '#confirmDeleteBtn', function() {
                const uploadId = $('#deleteBulkUploadModal').data('upload-id');
                const btn = $(this);
                const originalText = btn.html();

                btn.prop('disabled', true).html('<i class="ti ti-loader"></i> Deleting...');

                $.ajax({
                    url: '{{ route('bulk.upload.delete', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'DELETE',
                    data: { id: uploadId },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            bootstrap.Modal.getInstance(document.getElementById('deleteBulkUploadModal')).hide();
                            reloadBulkUploadsTable();

                            // Show success message
                            const alert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                '<i class="ti ti-check"></i> ' + response.message +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                                '</div>');
                            $('.page-header').after(alert);
                            setTimeout(() => alert.fadeOut(), 3000);

                            // Re-enable button after modal closes
                            reEnableButton(btn, originalText, 300);
                        } else {
                            alert('Error: ' + (response.message || 'Failed to delete bulk upload'));
                            reEnableButton(btn, originalText);
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to delete bulk upload. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                        reEnableButton(btn, originalText);
                    }
                });
            });

            // Delete selected uploads
            $(document).on('click', '#deleteSelectedBtn', function() {
                const selectedIds = [];
                $('.bulk-upload-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    alert('Please select at least one bulk upload to delete.');
                    return;
                }

                $('#deleteAllCount').text(selectedIds.length);
                $('#deleteAllBulkUploadsModal').data('upload-ids', selectedIds);

                const deleteAllModal = new bootstrap.Modal(document.getElementById('deleteAllBulkUploadsModal'));
                deleteAllModal.show();
            });

            // Confirm delete all selected uploads
            $(document).on('click', '#confirmDeleteAllBtn', function() {
                const uploadIds = $('#deleteAllBulkUploadsModal').data('upload-ids');
                const btn = $(this);
                const originalText = btn.html();

                btn.prop('disabled', true).html('<i class="ti ti-loader"></i> Deleting...');

                $.ajax({
                    url: '{{ route('bulk.upload.delete.all', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'DELETE',
                    data: { ids: uploadIds },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            bootstrap.Modal.getInstance(document.getElementById('deleteAllBulkUploadsModal')).hide();
                            reloadBulkUploadsTable();
                            $('#select-all').prop('checked', false);
                            updateDeleteButtonVisibility();

                            // Show success message
                            const alert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                '<i class="ti ti-check"></i> ' + response.message +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                                '</div>');
                            $('.page-header').after(alert);
                            setTimeout(() => alert.fadeOut(), 3000);

                            // Re-enable button after modal closes
                            reEnableButton(btn, originalText, 300);
                        } else {
                            alert('Error: ' + (response.message || 'Failed to delete bulk uploads'));
                            reEnableButton(btn, originalText);
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to delete bulk uploads. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                        reEnableButton(btn, originalText);
                    }
                });
            });

            // Auto-refresh table every 30 seconds if there are processing uploads
            setInterval(function() {
                const hasProcessing = bulkUploadsTable
                    .rows()
                    .data()
                    .toArray()
                    .some(function(row) { return row.status === 'processing'; });
                if (hasProcessing) {
                    // Show overlay briefly during refresh
                    $('#uploadLoader').fadeIn(150);
                    reloadBulkUploadsTable();
                    setTimeout(() => $('#uploadLoader').fadeOut(150), 400);
                }
            }, 30000); // Refresh every 30 seconds

            let csvHeaders = [];
            let systemFields = [];
            let customFields = [];
            let uploadedFilePath = '';
            let existingMappings = {};
            const reportStatusPollers = {};

            // Handle CSV file upload
            $('#csvUploadForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const uploadBtn = $('#uploadBtn');
                const originalText = uploadBtn.html();

                uploadBtn.prop('disabled', true).html('<i class="ti ti-loader"></i> Uploading...');
                $('#uploadStatus').html(
                    '<div class="alert alert-info"><i class="ti ti-loader"></i> Uploading file...</div>'
                );

                // Show full screen loader
                $('#uploadLoader').fadeIn(300);
                $('#loaderMessage').text('Uploading file...');

                $.ajax({
                    url: '{{ route('bulk.upload.csv', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            csvHeaders = response.headers;
                            uploadedFilePath = response.file_path;
                            existingMappings = {};
                            const fileType = response.file_type || 'file';
                            const fileTypeName = fileType === 'xlsx' || fileType === 'xls' ?
                                'Excel' : 'CSV';

                            $('#uploadStatus').html(
                                '<div class="alert alert-success"><i class="ti ti-check"></i> ' +
                                response.message + '<br><small>Found ' + csvHeaders.length +
                                ' columns in ' + fileTypeName + ' file.</small></div>');

                            // Re-enable button after success
                            reEnableButton(uploadBtn, originalText, 500);

                            // Close upload offcanvas and open mapping
                            setTimeout(function() {
                                // Hide loader
                                $('#uploadLoader').fadeOut(300);

                                const uploadOffcanvas = bootstrap.Offcanvas.getInstance(
                                    $('#offcanvas_add'));
                                if (uploadOffcanvas) {
                                    uploadOffcanvas.hide();
                                }

                                // Load system fields and show mapping
                                loadSystemFields();
                            }, 800);
                        } else {
                            // Hide loader on error
                            $('#uploadLoader').fadeOut(300);
                            $('#uploadStatus').html(
                                '<div class="alert alert-danger"><i class="ti ti-alert-circle"></i> ' +
                                response.message + '</div>');
                            reEnableButton(uploadBtn, originalText);
                        }
                    },
                    error: function(xhr) {
                        // Hide loader on error
                        $('#uploadLoader').fadeOut(300);
                        let errorMsg = 'Upload failed. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        $('#uploadStatus').html(
                            '<div class="alert alert-danger"><i class="ti ti-alert-circle"></i> ' +
                            errorMsg + '</div>');
                        reEnableButton(uploadBtn, originalText);
                    }
                });
            });

            // Open mapping flow from existing bulk upload entry
            function openMappingFromList(uploadId) {
                if (!uploadId) {
                    alert('Invalid bulk upload selected.');
                    return;
                }

                $('#uploadLoader').fadeIn(300);
                $('#loaderMessage').text('Preparing mapping data...');

                $.ajax({
                    url: '{{ route('bulk.upload.headers', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                    data: { id: uploadId },
                    success: function(response) {
                        $('#uploadLoader').fadeOut(300);
                        if (response.status === 'success') {
                            csvHeaders = response.headers || [];
                            uploadedFilePath = response.file_path || '';
                            existingMappings = response.mappings || {};

                            const duplicateValue = response.create_duplicate ? '1' : '0';
                            $('#createDuplicate').val(duplicateValue).trigger('change');
                            $('#assignToEnabled').val(response.assign_to_enabled ? '1' : '0');
                            $('#statusEnabled').val(response.status_enabled ? '1' : '0');

                            if (!uploadedFilePath) {
                                alert('File path not found for the selected upload. Please re-upload the file.');
                                return;
                            }

                            if (!csvHeaders || csvHeaders.length === 0) {
                                alert('No columns detected in the selected file. Please ensure the file has a header row.');
                                return;
                            }

                            loadSystemFields();
                        } else {
                            alert(response.message || 'Failed to fetch file details for mapping.');
                        }
                    },
                    error: function(xhr) {
                        $('#uploadLoader').fadeOut(300);
                        let errorMsg = 'Failed to fetch file details for mapping.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                    }
                });
            }

            function requestReportDownload(uploadId) {
                if (!uploadId) {
                    alert('Invalid bulk upload selected.');
                    return;
                }

                $('#uploadLoader').fadeIn(300);
                $('#loaderMessage').text('Preparing report...');

                $.ajax({
                    url: '{{ route('bulk.report.request', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'POST',
                    data: { id: uploadId },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Use report_file_url if available, otherwise fallback to download_url
                        const downloadUrl = response.report_file_url || response.download_url;

                        if ((response.status === 'ready' || response.status === 'completed') && downloadUrl) {
                            $('#uploadLoader').fadeOut(300);
                            $('#uploadStatus').html(
                                '<div class="alert alert-success"><i class="ti ti-check"></i> Report ready! Downloading automatically...</div>'
                            );
                            // Automatically download using reliable method
                            triggerAutoDownload(downloadUrl);
                            // Reload table to show updated status
                            if (typeof window.reloadBulkUploadsTable === 'function') {
                                setTimeout(function() {
                                    window.reloadBulkUploadsTable();
                                }, 1000);
                            }
                        } else if (response.status === 'processing') {
                            // Keep loader visible and show progress
                            $('#uploadLoader').fadeIn(300);
                            $('#loaderMessage').text(response.message || 'Generating report... ' + (response.progress || 0) + '%');
                            renderReportProgress(response.progress || 0, response.message);
                            startReportPolling(uploadId);
                        } else {
                            $('#uploadLoader').fadeOut(300);
                            alert(response.message || 'Unable to generate report. Please try again.');
                        }
                    },
                    error: function(xhr) {
                        $('#uploadLoader').fadeOut(300);
                        let errorMsg = 'Failed to start report generation.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                    }
                });
            }

            function startReportPolling(uploadId) {
                if (reportStatusPollers[uploadId]) {
                    return;
                }
                // Poll more frequently for faster response (every 2 seconds)
                const poller = setInterval(function() {
                    checkReportStatus(uploadId);
                }, 2000);
                reportStatusPollers[uploadId] = poller;
                // Check immediately
                checkReportStatus(uploadId);
            }

            // Function to trigger automatic download
            function triggerAutoDownload(downloadUrl) {
                // Method 1: Create a temporary link and click it (most reliable)
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.download = ''; // Let browser determine filename
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Method 2: Fallback to window.open if link click doesn't work
                setTimeout(function() {
                    window.open(downloadUrl, '_blank');
                }, 100);
            }

            function checkReportStatus(uploadId) {
                $.ajax({
                    url: '{{ route('bulk.report.status', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                    data: { id: uploadId },
                    success: function(response) {
                        // Use report_file_url if available, otherwise fallback to download_url
                        const downloadUrl = response.report_file_url || response.download_url;

                        if ((response.status === 'ready' || response.status === 'completed') && downloadUrl) {
                            stopReportPolling(uploadId);
                            $('#uploadLoader').fadeOut(300);
                            $('#uploadStatus').html(
                                '<div class="alert alert-success"><i class="ti ti-check"></i> Report ready! Downloading automatically...</div>'
                            );

                            // Store download URL in localStorage for persistence
                            try {
                                const downloadData = {
                                    uploadId: uploadId,
                                    downloadUrl: downloadUrl,
                                    reportFileUrl: response.report_file_url,
                                    timestamp: new Date().toISOString(),
                                    status: 'completed'
                                };
                                localStorage.setItem('bulk_upload_download_' + uploadId, JSON.stringify(downloadData));
                            } catch (e) {
                                console.warn('Could not save to localStorage:', e);
                            }

                            // Automatically download the report using reliable method
                            triggerAutoDownload(downloadUrl);

                            // Reload table to show updated status
                            if (typeof window.reloadBulkUploadsTable === 'function') {
                                setTimeout(function() {
                                    window.reloadBulkUploadsTable();
                                }, 1000);
                            }
                        } else if (response.status === 'failed') {
                            stopReportPolling(uploadId);
                            $('#uploadLoader').fadeOut(300);
                            $('#uploadStatus').html(
                                '<div class="alert alert-danger"><i class="ti ti-alert-circle"></i> ' +
                                (response.message || 'Report generation failed.') + '</div>'
                            );
                        } else if (response.status === 'processing') {
                            // Show loader with progress
                            $('#uploadLoader').fadeIn(300);
                            $('#loaderMessage').text(response.message || 'Generating report... ' + (response.progress || 0) + '%');
                            renderReportProgress(response.progress || 0, response.message);
                        }
                    },
                    error: function() {
                        // ignore transient errors
                    }
                });
            }

            function stopReportPolling(uploadId) {
                if (reportStatusPollers[uploadId]) {
                    clearInterval(reportStatusPollers[uploadId]);
                    delete reportStatusPollers[uploadId];
                }
            }

            // Function to retrieve download URL from localStorage and trigger download
            function retrieveAndDownloadFromStorage(uploadId) {
                try {
                    const storedData = localStorage.getItem('bulk_upload_download_' + uploadId);
                    if (storedData) {
                        const downloadData = JSON.parse(storedData);
                        if (downloadData.downloadUrl && downloadData.status === 'completed') {
                            // Verify the URL is still valid by checking if report is ready
                            $.ajax({
                                url: '{{ route('bulk.report.status', ['box_slug' => request()->route('box_slug')]) }}',
                                type: 'GET',
                                data: { id: uploadId },
                                success: function(response) {
                                    // Use report_file_url if available, otherwise fallback to download_url
                                    const downloadUrl = response.report_file_url || response.download_url;

                                    if ((response.status === 'ready' || response.status === 'completed') && downloadUrl) {
                                        // Use the current download URL from server
                                        triggerAutoDownload(downloadUrl);
                                    } else if (downloadData.reportFileUrl || downloadData.downloadUrl) {
                                        // Fallback to stored URL (prefer report_file_url)
                                        triggerAutoDownload(downloadData.reportFileUrl || downloadData.downloadUrl);
                                    }
                                },
                                error: function() {
                                    // Fallback to stored URL if server check fails
                                    if (downloadData.downloadUrl) {
                                        triggerAutoDownload(downloadData.downloadUrl);
                                    }
                                }
                            });
                            return true;
                        }
                    }
                } catch (e) {
                    console.warn('Could not retrieve from localStorage:', e);
                }
                return false;
            }

            function renderReportProgress(percent, message) {
                // Update loader message with progress
                if (message) {
                    $('#loaderMessage').text(message + ' (' + percent + '%)');
                } else {
                    $('#loaderMessage').text('Generating report... ' + percent + '%');
                }
                const displayPercent = Math.min(100, Math.max(0, percent || 0)).toFixed(0);
                const infoText = message || 'Report is being generated...';
                $('#uploadStatus').html(`
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="ti ti-loader"></i> ${infoText}</span>
                            <span class="fw-semibold">${displayPercent}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: ${displayPercent}%"
                                aria-valuenow="${displayPercent}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                `);
            }

            // Load system fields for mapping
            function loadSystemFields() {
                $('#mappingContainer').html(
                    '<div class="text-center p-4"><i class="ti ti-loader"></i> Loading system fields...</div>');

                $.ajax({
                    url: '{{ route('get.all.lable.uploade', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            systemFields = response.system_fields || [];
                            customFields = response.custom_fields || [];

                            // Show mapping offcanvas
                            const mappingOffcanvas = new bootstrap.Offcanvas($(
                                '#labelMappingOffcanvas'));
                            mappingOffcanvas.show();

                            // Populate mapping form
                            populateMappingForm();
                        } else {
                            $('#mappingContainer').html(
                                '<div class="alert alert-danger">Failed to load system fields: ' +
                                response.message + '</div>');
                        }
                    },
                    error: function(xhr) {
                        $('#mappingContainer').html(
                            '<div class="alert alert-danger">Failed to load system fields. Please try again.</div>'
                        );
                    }
                });
            }

            // Populate mapping form
            function populateMappingForm() {
                $('#uploadedFilePath').val(uploadedFilePath);
                const container = $('#mappingContainer');
                container.empty();

                if (!csvHeaders || csvHeaders.length === 0) {
                    container.html('<div class="alert alert-warning">No columns found in file.</div>');
                    return;
                }

                // Combine system and custom fields - only from API
                const allFields = [];
                const fieldIdMap = {}; // Track field IDs to avoid duplicates
                const savedMappingByField = {};

                if (existingMappings && typeof existingMappings === 'object') {
                    Object.keys(existingMappings).forEach(function(columnName) {
                        const mappedField = existingMappings[columnName];
                        if (mappedField) {
                            savedMappingByField[mappedField] = columnName;
                        }
                    });
                }

                // Process system fields
                systemFields.forEach(function(field) {
                    const hasCustomRef = field.field_refernce_key && field.field_refernce_key !== null && field.field_refernce_key !== '';
                    const fieldRefKey = String(field.field_refernce_key || '').trim();
                    const isNumericRef = hasCustomRef && /^\d+$/.test(fieldRefKey);
                    if ((field.field || field.field_name || '').toLowerCase().includes('source') ||
                        (field.name || '').toLowerCase().includes('source')) {
                        console.log('Processing source field:', {
                            name: field.name,
                            field: field.field,
                            field_name: field.field_name,
                            field_refernce_key: field.field_refernce_key,
                            hasCustomRef: hasCustomRef,
                            isNumericRef: isNumericRef,
                            willBeSystemField: !isNumericRef
                        });
                    }

                    if (hasCustomRef && isNumericRef) {
                        const customField = customFields.find(cf => cf.id == field.field_refernce_key);
                        if (customField) {
                            const fieldId = customField.col_name;
                            if (!fieldIdMap[fieldId]) {
                                allFields.push({
                                    id: fieldId,
                                    name: field.name,
                                    type: 'custom',
                                    field_refernce_key: customField.id
                                });
                                fieldIdMap[fieldId] = true;
                            }
                        }
                    } else {
                        const fieldId = field.field || field.field_name;
                        if (fieldId && !fieldIdMap[fieldId]) {
                            allFields.push({
                                id: fieldId,
                                name: field.name,
                                type: 'system',
                                field_refernce_key: field.field_refernce_key
                            });
                            fieldIdMap[fieldId] = true;

                            if (fieldId.toLowerCase().includes('source') || (field.name || '').toLowerCase().includes('source')) {
                                console.log('Added source field to allFields:', {
                                    id: fieldId,
                                    name: field.name,
                                    type: 'system'
                                });
                            }
                        } else if (fieldId && fieldIdMap[fieldId]) {
                            if (fieldId.toLowerCase().includes('source') || (field.name || '').toLowerCase().includes('source')) {
                                console.log('Source field already in map, skipping:', fieldId);
                            }
                        } else if (!fieldId) {
                            
                            if ((field.name || '').toLowerCase().includes('source')) {
                                console.log('Source field has no field ID:', field);
                            }
                        }
                    }
                });

                customFields.forEach(function(field) {
                    const fieldId = field.col_name;

                    if (!fieldIdMap[fieldId]) {
                        allFields.push({
                            id: fieldId,
                            name: field.name,
                            type: 'custom',
                            field_refernce_key: field.id
                        });
                        fieldIdMap[fieldId] = true;
                    }
                });
                window.allFieldsForMapping = allFields;
                window.csvHeadersForMapping = csvHeaders;
                const mappingSection = $('<div class="mb-3"></div>');
                mappingSection.append('<h5 class="mb-3"><i class="ti ti-database text-primary"></i> Map Fields to Uploaded Columns</h5>');
                mappingSection.append('<p class="text-muted small mb-3"><i class="ti ti-info-circle"></i> ' +
                    'Select which uploaded column maps to each field. Drag fields to reorder. System fields go to leads table, custom fields go to data_' + (systemFields[0]?.box_id || '') + ' table.</p>');
                const mappingContainerDiv = $('<div id="mappingFieldsContainer" class="mapping-fields-container"></div>');
                mappingSection.append(mappingContainerDiv);
                container.append(mappingSection);
                const fieldsContainer = $('#mappingFieldsContainer');
                const systemFieldsList = allFields.filter(f => f.type === 'system');
                const customFieldsList = allFields.filter(f => f.type === 'custom');
                function addConditionalMappingRows(fieldsContainer, csvHeaders) {
                    const assignToEnabled = $('#assignToEnabled').val() === '1' || $('#assignToEnabled').val() === 1;
                    const statusEnabled = $('#statusEnabled').val() === '1' || $('#statusEnabled').val() === 1;
                    if (assignToEnabled) {
                        const ownerCard = $('<div class="mb-3 draggable-field border border-warning p-3 rounded position-relative" id="conditionalMappingRow_owner" data-field-id="owner_mapping" data-field-type="conditional" style="cursor: move;"></div>');
                        const dragHandle = $('<div class="drag-handle position-absolute top-0 start-0 p-2" style="cursor: grab;"><i class="ti ti-grip-vertical text-muted"></i></div>');
                        ownerCard.append(dragHandle);
                        const ownerLabel = $('<label class="col-form-label fw-semibold mb-2 text-warning" style="padding-left: 30px;">' +
                            '<i class="ti ti-user-check text-warning me-1"></i>Owner Email/Name (Assign To)' +
                            ' <span class="badge bg-warning ms-2">Conditional</span></label>');
                        ownerCard.append(ownerLabel);
                        const ownerSelectWrapper = $('<div style="padding-left: 30px;"></div>');
                        const ownerSelect = $('<select class="form-select conditional-mapping-select mapping-select" ' +
                            'id="ownerMapping" name="owner_mapping" data-field-id="owner_mapping" style="width:100%"></select>');
                        ownerSelect.append('<option value="">-- Select CSV Column for Owner --</option>');
                        csvHeaders.forEach(function(header) {
                            if (header && header.trim() !== '') {
                                const option = $('<option value="' + header + '">' + header + '</option>');
                                const headerLower = header.toLowerCase().trim();
                                if (headerLower.includes('owner') || headerLower.includes('assigned') ||
                                    headerLower.includes('assign to') || headerLower.includes('user email')) {
                                    option.prop('selected', true);
                                    ownerCard.addClass('border-success');
                                }
                                ownerSelect.append(option);
                            }
                        });
                        ownerSelectWrapper.append(ownerSelect);
                        ownerSelectWrapper.append('<small class="text-info d-block mt-1"><i class="ti ti-info-circle"></i> Will search by email or name in users table (box scoped)</small>');
                        ownerCard.append(ownerSelectWrapper);
                        fieldsContainer.append(ownerCard);
                    }
                    if (statusEnabled) {
                        const statusCard = $('<div class="mb-3 draggable-field border border-success p-3 rounded position-relative" id="conditionalMappingRow_status" data-field-id="status_mapping" data-field-type="conditional" style="cursor: move;"></div>');
                        const dragHandle = $('<div class="drag-handle position-absolute top-0 start-0 p-2" style="cursor: grab;"><i class="ti ti-grip-vertical text-muted"></i></div>');
                        statusCard.append(dragHandle);
                        const statusLabel = $('<label class="col-form-label fw-semibold mb-2 text-success" style="padding-left: 30px;">' +
                            '<i class="ti ti-flag text-success me-1"></i>Lead Status Name' +
                            ' <span class="badge bg-success ms-2">Conditional</span></label>');
                        statusCard.append(statusLabel);
                        const statusSelectWrapper = $('<div style="padding-left: 30px;"></div>');
                        const statusSelect = $('<select class="form-select conditional-mapping-select mapping-select" ' +
                            'id="statusMapping" name="status_mapping" data-field-id="status_mapping" style="width:100%"></select>');
                        statusSelect.append('<option value="">-- Select CSV Column for Status --</option>');
                        csvHeaders.forEach(function(header) {
                            if (header && header.trim() !== '') {
                                const option = $('<option value="' + header + '">' + header + '</option>');
                                const headerLower = header.toLowerCase().trim();
                                if (headerLower.includes('status') || headerLower.includes('stage') ||
                                    headerLower.includes('lead status')) {
                                    option.prop('selected', true);
                                    statusCard.addClass('border-success');
                                }
                                statusSelect.append(option);
                            }
                        });
                        statusSelectWrapper.append(statusSelect);
                        statusCard.append(statusSelectWrapper);
                        fieldsContainer.append(statusCard);
                    }

                }

                function findBestMatch(field, csvHeaders) {
                    if (!field || !csvHeaders || csvHeaders.length === 0) return null;

                    const fieldName = (field.name || '').trim();
                    const fieldId = (field.id || '').trim();
                    const fieldNameLower = fieldName.toLowerCase();
                    const fieldIdLower = fieldId.toLowerCase();
                    const normalize = function(str) {
                        return str.toLowerCase().replace(/[_\s\-\.]/g, '');
                    };
                    const normalizedFieldName = normalize(fieldName);
                    const normalizedFieldId = normalize(fieldId);
                    let bestMatch = null;
                    let bestScore = 0;
                    const fieldVariations = {
                        'email': ['email', 'e-mail', 'mail', 'email address', 'emailaddress'],
                        'phone': ['phone', 'telephone', 'tel', 'mobile', 'cell', 'contact number'],
                        'mobile': ['mobile', 'cell', 'cellphone', 'mobile phone', 'mobile number'],
                        'name': ['name', 'full name', 'fullname', 'first name', 'firstname', 'fname'],
                        'last name': ['last name', 'lastname', 'lname', 'surname', 'family name'],
                        'address': ['address', 'addr', 'street address', 'streetaddress'],
                        'city': ['city', 'town'],
                        'state': ['state', 'province', 'region'],
                        'country': ['country', 'nation'],
                        'postal code': ['postal code', 'postalcode', 'zip', 'zip code', 'zipcode', 'pincode'],
                        'company': ['company', 'company name', 'companyname', 'organization', 'org', 'business'],
                        'designation': ['designation', 'title', 'job title', 'jobtitle', 'position', 'role'],
                        'source': ['source', 'lead source', 'leadsource', 'crm source', 'crm_source', 'origin'],
                        'status': ['status', 'lead status', 'leadstatus', 'stage', 'lead stage'],
                        'industry': ['industry', 'sector', 'business type'],
                        'owner': ['owner', 'assigned to', 'assignedto', 'assigned', 'user email', 'useremail']
                    };
                    csvHeaders.forEach(function(header) {
                        if (!header || !header.trim()) return;
                        const headerLower = header.toLowerCase().trim();
                        const normalizedHeader = normalize(header);
                        let score = 0;
                        if (headerLower === fieldNameLower || headerLower === fieldIdLower) {
                            score = 100;
                        }
                        else if (normalizedHeader === normalizedFieldName || normalizedHeader === normalizedFieldId) {
                            score = 95;
                        }
                        else if (fieldNameLower.includes(headerLower) || headerLower.includes(fieldNameLower)) {
                            score = 80;
                        }
                        else if (fieldIdLower.includes(headerLower) || headerLower.includes(fieldIdLower)) {
                            score = 75;
                        }
                        else {
                            Object.keys(fieldVariations).forEach(function(key) {
                                if (fieldNameLower.includes(key) || fieldIdLower.includes(key)) {
                                    if (fieldVariations[key].some(function(variation) {
                                        return headerLower.includes(variation) || variation.includes(headerLower);
                                    })) {
                                        score = Math.max(score, 70);
                                    }
                                }
                            });
                        }
                        if (score === 0) {
                            if ((fieldNameLower.includes('email') || fieldIdLower.includes('email')) &&
                                (headerLower.includes('email') || headerLower.includes('mail'))) {
                                score = 85;
                            }
                            else if ((fieldNameLower.includes('phone') || fieldNameLower.includes('mobile') ||
                                     fieldIdLower.includes('phone') || fieldIdLower.includes('mobile')) &&
                                    (headerLower.includes('phone') || headerLower.includes('mobile') ||
                                     headerLower.includes('tel') || headerLower.includes('cell'))) {
                                score = 85;
                            }
                            else if ((fieldNameLower.includes('name') || fieldIdLower.includes('name')) &&
                                    (headerLower.includes('name') || headerLower.includes('fname') ||
                                     headerLower.includes('lname'))) {
                                score = 80;
                            }
                        }

                        if (score > bestScore) {
                            bestScore = score;
                            bestMatch = header;
                        }
                    });
                    return bestScore >= 60 ? bestMatch : null;
                }
                const excludedFieldIds = ['designation', 'amount', 'status', 'lead_status_id'];
                const systemFieldsToMap = systemFieldsList.filter(field => {
                    const fieldId = (field.id || '').toLowerCase();
                    return !excludedFieldIds.includes(fieldId);
                });

                systemFieldsToMap.forEach(function(field, index) {
                    const isRequired = field.is_required === 1 || field.is_required === true || field.required === 1 || field.required === true;
                    const requiredBadge = isRequired ? '<span class="text-danger ms-1">*</span>' : '';
                    
                    // Create draggable field card similar to lead add form
                    const fieldCard = $('<div class="mb-3 draggable-field border p-3 rounded position-relative" data-field-id="' + field.id + '" data-field-type="system" data-field-name="' + field.name + '" data-required="' + isRequired + '" style="cursor: move;"></div>');

                    // Add drag handle
                    const dragHandle = $('<div class="drag-handle position-absolute top-0 start-0 p-2" style="cursor: grab;"><i class="ti ti-grip-vertical text-muted"></i></div>');
                    fieldCard.append(dragHandle);

                    // Check if this is a source field to add special help text
                    const fieldIdLower = (field.id || '').toLowerCase();
                    const isSourceField = fieldIdLower === 'lead_source_id' || fieldIdLower === 'source' || fieldIdLower === 'lead_source';

                    // Field label
                    const fieldLabel = $('<label class="col-form-label fw-semibold mb-2" style="padding-left: 30px;">' +
                        '<i class="ti ti-settings text-primary me-1"></i>' + field.name + requiredBadge +
                        ' <span class="badge bg-primary ms-2">System Field</span></label>');
                    fieldCard.append(fieldLabel);

                    // Mapping select dropdown
                    const selectWrapper = $('<div style="padding-left: 30px;"></div>');
                    const select = $('<select class="form-select mapping-select" name="mappings[' +
                        field.id + ']" data-field-id="' + field.id + '" style="width:100%"></select>');
                    select.append('<option value="">-- Select Uploaded Column --</option>');

                    // Find best matching CSV column for this field
                    const bestMatch = findBestMatch(field, csvHeaders);

                    // Add all uploaded columns as options
                    let autoMatched = false;
                    csvHeaders.forEach(function(header) {
                        if (header && header.trim() !== '') {
                            const option = $('<option value="' + header + '">' + header + '</option>');
                            // Auto-select if this is the best match
                            if (bestMatch && header === bestMatch) {
                                option.prop('selected', true);
                                autoMatched = true;
                                fieldCard.addClass('border-success');
                            }
                            select.append(option);
                        }
                    });

                    if (savedMappingByField[field.id]) {
                        select.val(savedMappingByField[field.id]);
                        select.attr('data-preselected', savedMappingByField[field.id]);
                        fieldCard.removeClass('border-success').addClass('border-info');
                    }

                    selectWrapper.append(select);

                    // Add special help text for source fields
                    if (isSourceField) {
                        selectWrapper.append('<small class="text-info d-block mt-1"><i class="ti ti-info-circle"></i> This column should contain source name. Will search by name in crm_sources table (box scoped)</small>');
                    } else {
                        selectWrapper.append('<small class="text-muted d-block mt-1">Select the CSV column that maps to this field</small>');
                    }

                    fieldCard.append(selectWrapper);
                    fieldsContainer.append(fieldCard);
                });

                // Create mapping fields for each custom field
                customFieldsList.forEach(function(field, index) {
                    const isRequired = field.is_required === 1 || field.is_required === true || field.required === 1 || field.required === true;
                    const requiredBadge = isRequired ? '<span class="text-danger ms-1">*</span>' : '';

                    // Create draggable field card similar to lead add form
                    const fieldCard = $('<div class="mb-3 draggable-field border p-3 rounded position-relative" data-field-id="' + field.id + '" data-field-type="custom" data-field-name="' + field.name + '" data-required="' + isRequired + '" style="cursor: move;"></div>');

                    // Add drag handle
                    const dragHandle = $('<div class="drag-handle position-absolute top-0 start-0 p-2" style="cursor: grab;"><i class="ti ti-grip-vertical text-muted"></i></div>');
                    fieldCard.append(dragHandle);

                    // Field label
                    const fieldLabel = $('<label class="col-form-label fw-semibold mb-2" style="padding-left: 30px;">' +
                        '<i class="ti ti-tag text-info me-1"></i>' + field.name + requiredBadge +
                        ' <span class="badge bg-info ms-2">Custom Field</span></label>');
                    fieldCard.append(fieldLabel);

                    // Mapping select dropdown
                    const selectWrapper = $('<div style="padding-left: 30px;"></div>');
                    const select = $('<select class="form-select mapping-select" name="mappings[' +
                        field.id + ']" data-field-id="' + field.id + '" style="width:100%"></select>');
                    select.append('<option value="">-- Select Uploaded Column --</option>');

                    // Find best matching CSV column for this custom field
                    const bestMatch = findBestMatch(field, csvHeaders);

                    // Add all uploaded columns as options
                    let autoMatched = false;
                    csvHeaders.forEach(function(header) {
                        if (header && header.trim() !== '') {
                            const option = $('<option value="' + header + '">' + header + '</option>');
                            // Auto-select if this is the best match
                            if (bestMatch && header === bestMatch) {
                                option.prop('selected', true);
                                autoMatched = true;
                                fieldCard.addClass('border-success');
                            }
                            select.append(option);
                        }
                    });

                    if (savedMappingByField[field.id]) {
                        select.val(savedMappingByField[field.id]);
                        select.attr('data-preselected', savedMappingByField[field.id]);
                        fieldCard.removeClass('border-success').addClass('border-info');
                    }

                    selectWrapper.append(select);
                    selectWrapper.append('<small class="text-muted d-block mt-1">Select the CSV column that maps to this field</small>');

                    fieldCard.append(selectWrapper);
                    fieldsContainer.append(fieldCard);
                });

                // Add conditional mapping fields for Assign To and Status (Source is now in system fields)
                addConditionalMappingRows(fieldsContainer, csvHeaders);

                // Initialize SortableJS for drag and drop functionality
                if (typeof Sortable !== 'undefined') {
                    new Sortable(fieldsContainer[0], {
                        handle: '.drag-handle',
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',
                        dragClass: 'sortable-drag'
                    });
                }

                // Initialize select2 if available
                if (typeof $('.select').select2 === 'function') {
                    $('.mapping-select').select2({
                        placeholder: 'Select Uploaded Column',
                        allowClear: true
                    }).each(function() {
                        const preselected = $(this).attr('data-preselected');
                        if (preselected) {
                            $(this).val(preselected).trigger('change');
                        }
                    });
                }

                // Re-initialize conditional rows when assign_to changes
                $('#assignToEnabled').on('change', function() {
                    // Remove existing conditional rows (owner and status)
                    $('#conditionalMappingRow_owner, #conditionalMappingRow_status').remove();
                    // Re-add conditional rows
                    addConditionalMappingRows($('#mappingFieldsContainer'), csvHeaders);
                    // Re-initialize select2 for new selects
                    if (typeof $('.select').select2 === 'function') {
                        $('.conditional-mapping-select').select2({
                            placeholder: 'Select Uploaded Column',
                            allowClear: true
                        });
                    }
                });

                // Function to update available options (keep all CSV headers available in all dropdowns)
                function updateAvailableOptions() {
                    const currentSelections = {};

                    // Collect all current selections (for preserving selections, not for exclusion)
                    $('.mapping-select').each(function() {
                        const currentSelect = $(this);
                        const fieldId = currentSelect.data('field-id');
                        // Get value - try Select2 first, then fallback to native
                        let selectedColumn = null;
                        if (currentSelect.hasClass('select2-hidden-accessible')) {
                            selectedColumn = currentSelect.select2('val');
                        } else {
                            selectedColumn = currentSelect.val();
                        }

                        currentSelections[fieldId] = selectedColumn || null;
                    });

                    // Update each dropdown - keep ALL headers available
                    $('.mapping-select').each(function() {
                        const currentSelect = $(this);
                        const fieldId = currentSelect.data('field-id');
                        const currentColumn = currentSelections[fieldId];
                        const isSelect2 = currentSelect.hasClass('select2-hidden-accessible');

                        // Store current selection before clearing
                        const wasSelected = currentColumn;

                        // Destroy select2 first if it exists
                        if (isSelect2) {
                            currentSelect.select2('destroy');
                        }

                        // Clear all options
                        currentSelect.empty();

                        // Add back empty option
                        currentSelect.append('<option value="">-- Select Uploaded Column --</option>');

                        // Add back ALL uploaded columns (no exclusions - allow same column in multiple fields)
                        const headersToUse = window.csvHeadersForMapping || csvHeaders;
                        headersToUse.forEach(function(header) {
                            if (header && header.trim() !== '') {
                                // Always show all headers - allow duplicate selections
                                    const option = $('<option value="' + header + '">' + header + '</option>');
                                    if (header === wasSelected) {
                                        option.prop('selected', true);
                                    }
                                    currentSelect.append(option);
                            }
                        });

                        // Reinitialize select2 if it was initialized before
                        if (typeof $('.select').select2 === 'function') {
                            // Initialize select2
                            currentSelect.select2({
                                placeholder: 'Select Uploaded Column',
                                allowClear: true
                            });

                            // Restore selection if there was one - ensure option exists first
                            if (wasSelected) {
                                // Check if the option exists in the dropdown
                                const optionExists = currentSelect.find('option[value="' + wasSelected + '"]').length > 0;
                                if (optionExists) {
                                    // Use setTimeout to ensure Select2 is fully initialized
                                    setTimeout(function() {
                                        currentSelect.val(wasSelected);
                                        currentSelect.trigger('change.select2');
                                    }, 50);
                                }
                            }
                        } else {
                            // If select2 is not available, just set the value directly
                            if (wasSelected) {
                                const optionExists = currentSelect.find('option[value="' + wasSelected + '"]').length > 0;
                                if (optionExists) {
                                    currentSelect.val(wasSelected);
                                }
                            }
                        }
                    });
                }

                // Store allFields globally for updateAvailableOptions
                window.allFieldsForMapping = allFields;

                // Call on initial load (after a small delay to ensure select2 is initialized)
                setTimeout(function() {
                    updateAvailableOptions();
                }, 200);

                // Update when any dropdown changes - use debounce to prevent rapid updates
                let updateTimeout;
                $(document).off('change', '.mapping-select').on('change', '.mapping-select', function(e) {
                    const currentSelect = $(this);
                    const selectedValue = currentSelect.val();

                    // Clear any pending updates
                    if (updateTimeout) {
                        clearTimeout(updateTimeout);
                    }

                    // Debounce the update - wait 300ms after last change
                    updateTimeout = setTimeout(function() {
                        updateAvailableOptions();
                    }, 300);
                });

                // Add info note
                const infoNote = $('<div class="alert alert-info mt-3 mb-0">' +
                    '<i class="ti ti-info-circle"></i> <strong>Simple Mapping:</strong> ' +
                    'Select uploaded column for each field. Each column can only be used once. Auto-matched fields are highlighted in green.</div>');
                container.append(infoNote);
            }

            // Handle mapping form submission
            $('#mappingForm').on('submit', function(e) {
                e.preventDefault();

                const filePath = $('#uploadedFilePath').val();
                if (!filePath) {
                    alert('Error: File path is missing. Please upload the file again.');
                    return;
                }

                let validationErrors = [];
                $('.draggable-field[data-required="true"]').each(function() {
                    const fieldName = $(this).attr('data-field-name');
                    const select = $(this).find('.mapping-select');
                    let val = null;
                    if (select.hasClass('select2-hidden-accessible')) {
                        val = select.select2('val');
                    } else {
                        val = select.val();
                    }
                    if (!val || val === '') {
                        validationErrors.push(fieldName);
                    }
                });

                if (validationErrors.length > 0) {
                    alert('Please map the following required fields:\n- ' + validationErrors.join('\n- '));
                    return;
                }

                const formData = {
                    file_path: filePath,
                    create_duplicate: $('#createDuplicate').val() || '0',
                    assign_to_enabled: $('#assignToEnabled').val() || '0',
                    status_enabled: $('#statusEnabled').val() || '0',
                    mappings: {}
                };

                // Collect mappings - reverse format: mappings[uploaded_column] = field_id
                // Frontend has: mappings[field_id] = uploaded_column
                // Backend expects: mappings[uploaded_column] = field_id
                let mappingCount = 0;
                $('.mapping-select').each(function() {
                    const fieldId = $(this).data('field-id');
                    let uploadedColumn = null;

                    // Try to get value from Select2 first, then fallback to native
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        uploadedColumn = $(this).select2('val');
                    } else {
                        uploadedColumn = $(this).val();
                    }

                    if (uploadedColumn && uploadedColumn !== '' && uploadedColumn !== null && fieldId) {
                        // Handle special mappings (status, owner) - they map to specific field IDs
                        // Source is now handled as a regular system field, no special handling needed
                        if (fieldId === 'status_mapping') {
                            // Status mapping: map uploaded column to lead_status_id
                            formData.mappings[uploadedColumn] = 'lead_status_id';
                            mappingCount++;
                        } else if (fieldId === 'owner_mapping') {
                            // Owner mapping: map uploaded column to assigned_to
                            formData.mappings[uploadedColumn] = 'assigned_to';
                            mappingCount++;
                        } else {
                            // Regular field mapping (including source fields like lead_source_id)
                        formData.mappings[uploadedColumn] = fieldId;
                        mappingCount++;
                        }
                    }
                });

                // Validate that at least one mapping exists
                if (mappingCount === 0) {
                    alert('Error: Please map at least one field before saving.');
                    return;
                }

                const saveBtn = $('#saveMappingBtn');
                const originalText = saveBtn.html();
                saveBtn.prop('disabled', true).html('<i class="ti ti-loader"></i> Saving...');

                $.ajax({
                    url: '{{ route('bulk.save.mapping', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            // Re-enable button after success (before closing modal)
                            reEnableButton(saveBtn, originalText, 300);

                            // Close mapping offcanvas immediately
                            const mappingOffcanvas = bootstrap.Offcanvas.getInstance($(
                                '#labelMappingOffcanvas'));
                            if (mappingOffcanvas) {
                                mappingOffcanvas.hide();
                            }
                            $('#mappingModal').modal('hide');

                            // Automatically start import process without confirmation
                            processImport();
                        } else {
                            let errorMsg = 'Failed to save mapping: ' + response.message;
                            if (response.invalid_mappings && response.invalid_mappings.length > 0) {
                                errorMsg += '\n\nInvalid mappings:\n' + response.invalid_mappings.slice(0, 5).join('\n');
                                if (response.invalid_mappings.length > 5) {
                                    errorMsg += '\n... and ' + (response.invalid_mappings.length - 5) + ' more';
                                }
                            }
                            alert(errorMsg);
                            reEnableButton(saveBtn, originalText);
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to save mapping. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                        reEnableButton(saveBtn, originalText);
                    }
                });
            });

            // Process import
            function processImport() {
                $.ajax({
                    url: '{{ route('bulk.process.import', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            if (response.queued) {
                                // Store file path for statistics polling
                                const filePath = response.file_path;

                                // Show modal with polling for statistics
                                showImportProgressModal(filePath);

                                // Start polling for statistics
                                pollImportStatistics(filePath);
                            } else {
                                showImportResults({
                                    total: response.total || 0,
                                    successful: response.imported || 0,
                                    skipped: response.skipped || 0,
                                    duplicates: response.duplicates || 0
                                });
                            }
                            // Reset form
                            $('#csvUploadForm')[0].reset();
                            $('#uploadStatus').empty();
                        } else {
                            alert('Import failed: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to process import. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                    }
                });
            }

            // Store import start time for elapsed time calculation
            let importStartTime = null;
            let timeInterval = null;

            // Show import progress modal
            function showImportProgressModal(filePath) {
                importStartTime = new Date();

                const modalHtml = `
                    <div class="modal fade" id="importProgressModal" tabindex="-1" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><i class="ti ti-upload"></i> Import in Progress</h5>
                                </div>
                                <div class="modal-body text-center">
                                    <!-- Status Badge -->
                                    <div class="mb-3">
                                        <span class="badge bg-primary fs-6 px-3 py-2" id="importStatusBadge">
                                            <i class="ti ti-loader"></i> Status: <span id="statusText">Processing</span>
                                        </span>
                                    </div>

                                    <div id="loadingSpinner" class="spinner-border text-primary mb-3" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mb-0">Your file is being processed in the background...</p>
                                    <p class="text-muted small mt-2">This may take a few minutes depending on file size.</p>

                                    <!-- Status Summary -->
                                    <div id="statusSummary" class="mt-4">
                                        <div class="row g-2">
                                            <div class="col-6 col-md-3">
                                                <div class="card border-success">
                                                    <div class="card-body p-2">
                                                        <small class="text-muted d-block">Succeeded</small>
                                                        <strong id="statSuccessful" class="text-success">0</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="card border-info">
                                                    <div class="card-body p-2">
                                                        <small class="text-muted d-block">Duplicates</small>
                                                        <strong id="statDuplicates" class="text-info">0</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="card border-warning">
                                                    <div class="card-body p-2">
                                                        <small class="text-muted d-block">Skipped</small>
                                                        <strong id="statSkipped" class="text-warning">0</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="card border-primary">
                                                    <div class="card-body p-2">
                                                        <small class="text-muted d-block">Total</small>
                                                        <strong id="statTotal">0</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                <i class="ti ti-clock"></i> Time Elapsed: <span id="timeElapsed">0s</span>
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div id="progressBarContainer" class="mt-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted small">Progress</span>
                                            <span class="text-primary fw-bold" id="progressPercentage">0%</span>
                                        </div>
                                        <div class="progress" style="height: 35px; position: relative; background-color: #e9ecef; border-radius: 8px;">
                                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                                 role="progressbar"
                                                 style="width: 0%; transition: width 0.3s ease; border-radius: 8px;"
                                                 aria-valuenow="0"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                            </div>
                                            <span id="progressBarText" class="fw-bold text-dark" style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); white-space: nowrap; z-index: 10; font-size: 14px;">0%</span>
                                        </div>
                                        <div class="mt-3">
                                            <div class="row g-2">
                                                <div class="col-6 col-md-3">
                                                    <div class="text-center p-2 border rounded">
                                                        <small class="text-muted d-block">Processed</small>
                                                        <strong id="processedCount" class="text-primary">0</strong>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <div class="text-center p-2 border rounded bg-success-light">
                                                        <small class="text-muted d-block">Stored in DB</small>
                                                        <strong id="storedInDb" class="text-success">0</strong>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <div class="text-center p-2 border rounded bg-warning-light">
                                                        <small class="text-muted d-block">Pending</small>
                                                        <strong id="pendingCount" class="text-warning">0</strong>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <div class="text-center p-2 border rounded">
                                                        <small class="text-muted d-block">Total</small>
                                                        <strong id="totalCount">0</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="importStatusMessage" class="mt-3" style="display: none;">
                                        <div class="alert alert-info mb-0">
                                            <i class="ti ti-info-circle"></i> <span id="statusMessageText"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" id="cancelImportBtn">
                                        <i class="ti ti-x"></i> Cancel Import
                                    </button>
                                    <button type="button" class="btn btn-info" id="viewDetailsBtn" style="display: none;">
                                        <i class="ti ti-eye"></i> View Details
                                    </button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeImportModalBtn" style="display: none;">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Remove existing modal if any
                $('#importProgressModal').remove();
                $('body').append(modalHtml);

                const modal = new bootstrap.Modal(document.getElementById('importProgressModal'));
                modal.show();
                // make backdrop transparent to avoid page blur
                setTimeout(function() {
                    $('.modal-backdrop').last().addClass('import-progress-backdrop');
                }, 0);

                // Show cancel button
                $('#cancelImportBtn').show();

                // Handle cancel button click
                $('#cancelImportBtn').off('click').on('click', function() {
                    // Show confirmation dialog with option to delete files
                    if (confirm('Do you want to cancel the import and delete the files?\n\nClick OK to cancel and delete files.\nClick Cancel to just cancel the import (files will be kept).')) {
                        // Cancel and delete files
                        cancelImport(filePath, true);
                    } else {
                        // Just cancel import (keep files)
                        if (confirm('Are you sure you want to cancel the import? The process will stop after the current row is processed.')) {
                            cancelImport(filePath, false);
                        }
                    }
                });

                // Start time counter
                startTimeCounter();

                // Time counter is already started above
            }

            // Start time counter
            function startTimeCounter() {
                if (timeInterval) {
                    clearInterval(timeInterval);
                }
                timeInterval = setInterval(function() {
                    if (importStartTime) {
                        const elapsed = Math.floor((new Date() - importStartTime) / 1000);
                        const minutes = Math.floor(elapsed / 60);
                        const seconds = elapsed % 60;
                        const timeText = minutes > 0 ? `${minutes}m ${seconds}s` : `${seconds}s`;
                        $('#timeElapsed').text(timeText);
                    }
                }, 1000);
            }

            // Stop time counter
            function stopTimeCounter() {
                if (timeInterval) {
                    clearInterval(timeInterval);
                    timeInterval = null;
                }
            }

            // Cancel import function
            function cancelImport(filePath, deleteFiles = false) {
                const cancelBtn = $('#cancelImportBtn');
                const originalText = cancelBtn.html();
                cancelBtn.prop('disabled', true).html('<i class="ti ti-loader"></i> Cancelling...');

                $.ajax({
                    url: '{{ route('bulk.stop.import', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'POST',
                    data: {
                        file_path: filePath,
                        delete_files: deleteFiles ? 1 : 0
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            // Update status badge
                            $('#statusText').text(response.deleted ? 'Deleted' : 'Cancelled');
                            $('#importStatusBadge').removeClass('bg-info bg-primary bg-success').addClass(response.deleted ? 'bg-danger' : 'bg-warning');

                            // Update status message
                            $('#importStatusMessage').show();
                            $('#statusMessageText').html('<strong>' + (response.deleted ? 'Import Cancelled & Deleted:' : 'Import Cancelled:') + '</strong> ' + response.message);
                            $('#statusMessageText').parent().removeClass('alert-info').addClass(response.deleted ? 'alert-danger' : 'alert-warning');

                            // Hide cancel button, show close button
                            cancelBtn.hide();
                            $('#closeImportModalBtn').show();

                            // Re-enable button (even though it's hidden, ensure it's enabled if shown again)
                            reEnableButton(cancelBtn, originalText, 300);

                            // Stop polling and time counter
                            if (pollInterval) {
                                clearInterval(pollInterval);
                                pollInterval = null;
                            }
                            stopTimeCounter();

                            // Update progress bar
                            $('#progressBar').removeClass('bg-primary bg-success bg-info').addClass(response.deleted ? 'bg-danger' : 'bg-warning');
                            $('#progressBar').removeClass('progress-bar-animated');
                            $('#progressBarText').text(response.deleted ? 'Deleted' : 'Cancelled');

                            // Reload bulk uploads table if files were deleted
                            if (response.deleted && typeof window.reloadBulkUploadsTable === 'function') {
                                setTimeout(function() {
                                    window.reloadBulkUploadsTable();
                                }, 1000);
                            }

                            alert(response.deleted ? 'Import has been cancelled and files have been deleted successfully.' : 'Import has been cancelled successfully.');
                        } else {
                            alert('Failed to cancel import: ' + response.message);
                            reEnableButton(cancelBtn, originalText);
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to cancel import. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                        reEnableButton(cancelBtn, originalText);
                    }
                });
            }

            // Poll for import statistics
            let pollInterval = null;
            function pollImportStatistics(filePath) {
                let pollCount = 0;
                const maxPolls = 600; // Poll for up to 10 minutes (1 second * 600)

                // First poll immediately
                $.ajax({
                    url: '{{ route('bulk.import.statistics', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                    data: { file_path: filePath },
                    success: function(response) {
                        if (response.status === 'success' && response.stats) {
                            updateStatisticsDisplay(response.stats);
                        } else if (response.status === 'pending') {
                            // Update status to Processing if pending
                            $('#statusText').text('Processing');
                            $('#importStatusBadge').removeClass('bg-info').addClass('bg-primary');
                        }
                    },
                    error: function() {
                        // On error, show processing status
                        $('#statusText').text('Processing');
                        $('#importStatusBadge').removeClass('bg-info').addClass('bg-primary');
                    }
                });

                // Then continue polling every 1 second for real-time updates
                pollInterval = setInterval(function() {
                    pollCount++;

                    $.ajax({
                        url: '{{ route('bulk.import.statistics', ['box_slug' => request()->route('box_slug')]) }}',
                        type: 'GET',
                        data: { file_path: filePath },
                        success: function(response) {
                            if (response.status === 'success' && response.stats) {
                                // Update statistics display
                                updateStatisticsDisplay(response.stats);

                                // Check if import is cancelled/stopped
                                if (response.stats.status === 'cancelled') {
                                    clearInterval(pollInterval);
                                    pollInterval = null;
                                    stopTimeCounter();

                                    // Update status badge
                                    $('#statusText').text('Cancelled');
                                    $('#importStatusBadge').removeClass('bg-info bg-primary bg-success').addClass('bg-warning');

                                    // Update UI to show stopped status
                                    $('#importStatusMessage').show();
                                    $('#statusMessageText').html('<strong>Import Cancelled:</strong> The import process has been cancelled.');
                                    $('#statusMessageText').parent().removeClass('alert-info').addClass('alert-warning');

                                    $('#cancelImportBtn').hide();
                                    $('#viewDetailsBtn').show();
                                    $('#closeImportModalBtn').show();
                                    $('#loadingSpinner').hide();

                                    // Update progress bar
                                    $('#progressBar').removeClass('bg-primary bg-success bg-info').addClass('bg-warning');
                                    $('#progressBar').removeClass('progress-bar-animated');
                                    $('#progressBarText').text('Cancelled');
                                }
                                // Check if import is complete
                                else if (response.stats.status === 'completed' ||
                                    (response.stats.total > 0 && response.stats.percentage >= 100)) {
                                    clearInterval(pollInterval);
                                    pollInterval = null;
                                    stopTimeCounter();
                                    showImportResults(response.stats);
                                    $('#cancelImportBtn').hide();
                                    $('#viewDetailsBtn').show();
                                    $('#closeImportModalBtn').show();
                                    $('#loadingSpinner').hide();

                                    // Reload bulk uploads table
                                    if (typeof window.reloadBulkUploadsTable === 'function') {
                                        setTimeout(function() {
                                            window.reloadBulkUploadsTable();
                                        }, 2000);
                                    }

                                } else if (response.stats.status === 'failed') {
                                    clearInterval(pollInterval);
                                    pollInterval = null;
                                    stopTimeCounter();
                                    alert('Import failed: ' + (response.stats.errors && response.stats.errors.length > 0 ? response.stats.errors[0] : 'Unknown error'));
                                    $('#cancelImportBtn').hide();
                                    $('#viewDetailsBtn').show();
                                    $('#closeImportModalBtn').show();
                                    $('#loadingSpinner').hide();
                                }
                            } else if (response.status === 'pending') {
                                // Still processing, update status and continue polling
                                $('#statusText').text('Processing');
                                $('#importStatusBadge').removeClass('bg-info bg-success bg-warning bg-danger').addClass('bg-primary');

                                if (pollCount >= maxPolls) {
                                    clearInterval(pollInterval);
                                    alert('Import is taking longer than expected. Please check back later.');
                                    $('#closeImportModalBtn').show();
                                }
                            }
                        },
                        error: function() {
                            // Continue polling on error
                            if (pollCount >= maxPolls) {
                                clearInterval(pollInterval);
                                alert('Unable to fetch import statistics. Please check back later.');
                                $('#closeImportModalBtn').show();
                            }
                        }
                    });
                }, 1000); // Poll every 1 second for real-time updates
            }

            // Update statistics display
            function updateStatisticsDisplay(stats) {
                // Progress bar and stats are always visible now
                $('#loadingSpinner').hide();

                // Update status badge
                let statusText = 'Processing';
                let statusClass = 'bg-primary';

                // Check status from stats
                if (stats.status === 'processing' || !stats.status || stats.status === 'pending') {
                    statusText = 'Processing';
                    statusClass = 'bg-primary';
                } else if (stats.status === 'completed') {
                    statusText = 'Completed';
                    statusClass = 'bg-success';
                } else if (stats.status === 'cancelled') {
                    statusText = 'Cancelled';
                    statusClass = 'bg-warning';
                } else if (stats.status === 'failed') {
                    statusText = 'Failed';
                    statusClass = 'bg-danger';
                } else {
                    // Default to processing if status is unknown
                    statusText = 'Processing';
                    statusClass = 'bg-primary';
                }

                $('#statusText').text(statusText);
                $('#importStatusBadge').removeClass('bg-info bg-primary bg-success bg-warning bg-danger').addClass(statusClass);

                // Update progress bar
                const percentage = parseFloat(stats.percentage) || 0;
                const percentageText = percentage.toFixed(1) + '%';

                // Update percentage display
                $('#progressPercentage').text(percentageText);

                // Update progress bar width and attributes
                const percentageValue = Math.min(100, Math.max(0, percentage)); // Ensure between 0-100
                $('#progressBar').css('width', percentageValue + '%')
                                 .attr('aria-valuenow', percentageValue);

                // Update text - change color based on progress bar width for visibility
                $('#progressBarText').text(percentageText);
                if (percentageValue > 50) {
                    // If bar is more than 50%, show white text
                    $('#progressBarText').css('color', '#fff');
                } else {
                    // If bar is less than 50%, show dark text
                    $('#progressBarText').css('color', '#000');
                }

                // Update processed count
                const processed = stats.processed || 0;
                const total = stats.total || 0;
                const successful = stats.successful || 0;
                const pending = total - processed;

                $('#processedCount').text(processed.toLocaleString());
                $('#totalCount').text(total.toLocaleString());
                $('#storedInDb').text(successful.toLocaleString());
                $('#pendingCount').text(pending > 0 ? pending.toLocaleString() : '0');

                // Update statistics cards
                $('#statTotal').text((total || 0).toLocaleString());
                $('#statSuccessful').text((successful || 0).toLocaleString());
                $('#statSkipped').text((stats.skipped || 0).toLocaleString());
                $('#statDuplicates').text((stats.duplicates || 0).toLocaleString());

                // Update percentage in progress bar text
                $('#progressBarText').text(percentageText);

                // Update progress bar color based on completion
                if (percentage >= 100) {
                    $('#progressBar').removeClass('bg-primary bg-info').addClass('bg-success');
                    $('#progressBar').removeClass('progress-bar-animated');
                } else if (percentage >= 75) {
                    $('#progressBar').removeClass('bg-primary bg-success').addClass('bg-info');
                } else {
                    $('#progressBar').removeClass('bg-info bg-success').addClass('bg-primary');
                }

                // Debug log (remove in production)
                console.log('Progress Update:', {
                    percentage: percentage,
                    processed: processed,
                    total: total,
                    stats: stats
                });
            }

            // Handle view details button
            $(document).on('click', '#viewDetailsBtn', function() {
                $('#importProgressModal').modal('hide');
                setTimeout(function() {
                    $('html, body').animate({
                        scrollTop: $('#uploadStatus').offset().top - 100
                    }, 500);
                }, 300);
            });

            $(document).on('hide.bs.modal', '#importProgressModal', function () {
                if (document.activeElement) {
                    document.activeElement.blur();
                }
            });

            $(document).on('hidden.bs.modal', '#importProgressModal', function () {
                if (pollInterval) {
                    clearInterval(pollInterval);
                    pollInterval = null;
                }
                stopTimeCounter();
                $('#uploadLoader').hide();
                if (document.activeElement) {
                    document.activeElement.blur();
                }
                $('body').removeClass('modal-open').css('padding-right', '');
                $('.modal-backdrop').remove();
                reloadBulkUploadsTable();
                $('#importProgressModal').remove();
            });

            // Show final import results
            function showImportResults(stats) {
                // Don't hide modal, just update it with final results
                stopTimeCounter();

                const percentage = stats.percentage || 100;
                const total = stats.total || 0;
                const successful = stats.successful || 0;
                const skipped = stats.skipped || 0;
                const duplicates = stats.duplicates || 0;
                const errors = stats.errors || [];
                const errorCount = errors.length;

                let resultsHtml = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading"><i class="ti ti-check-circle"></i> Import Completed! (${percentage.toFixed(1)}%)</h5>
                        <hr>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6 col-lg-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-file-text text-primary me-2 fs-4"></i>
                                    <div>
                                        <small class="text-muted d-block">Total Rows</small>
                                        <strong class="fs-5">${total.toLocaleString()}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-check text-success me-2 fs-4"></i>
                                    <div>
                                        <small class="text-muted d-block">Successfully Imported</small>
                                        <strong class="fs-5 text-success">${successful.toLocaleString()}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-alert-triangle text-warning me-2 fs-4"></i>
                                    <div>
                                        <small class="text-muted d-block">Skipped</small>
                                        <strong class="fs-5 text-warning">${skipped.toLocaleString()}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-copy text-info me-2 fs-4"></i>
                                    <div>
                                        <small class="text-muted d-block">Duplicates</small>
                                        <strong class="fs-5 text-info">${duplicates.toLocaleString()}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                // Show errors if any
                if (errorCount > 0) {
                    resultsHtml += `
                        <hr>
                        <div class="mt-3">
                            <h6 class="text-danger"><i class="ti ti-alert-circle"></i> Errors (${errorCount})</h6>
                            <div class="mt-2" style="max-height: 200px; overflow-y: auto;">
                                <ul class="list-unstyled mb-0">
                                    ${errors.slice(0, 10).map(error => `<li class="text-danger small mb-1"><i class="ti ti-x"></i> ${error}</li>`).join('')}
                                    ${errorCount > 10 ? `<li class="text-muted small"><em>... and ${errorCount - 10} more errors. Check logs for details.</em></li>` : ''}
                                </ul>
                            </div>
                        </div>`;
                }

                resultsHtml += `
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;

                // Clear any existing alerts and show results
                $('.alert-success, .alert-danger').remove();
                $('#uploadStatus').html(resultsHtml);

                // Show summary alert
                const summaryText = `Import Summary:\n` +
                    `✅ Successfully Imported: ${successful.toLocaleString()}\n` +
                    `⚠️ Skipped: ${skipped.toLocaleString()}\n` +
                    `📋 Duplicates: ${duplicates.toLocaleString()}\n` +
                    (errorCount > 0 ? `❌ Errors: ${errorCount}` : '');

                // Auto-hide after 15 seconds (longer if there are errors)
                setTimeout(function() {
                    $('.alert-success').fadeOut();
                }, errorCount > 0 ? 20000 : 10000);
            }

            // Handle file input change
            $('#csvFileInput').on('change', function() {
                const file = this.files[0];
                if (file) {
                    $('#uploadStatus').html(
                        '<div class="alert alert-info"><i class="ti ti-file"></i> Selected: ' +
                        file.name + ' (' + (file.size / 1024).toFixed(2) + ' KB)</div>');
                }
            });
        });
    </script>

    <style>
        .fullscreen-loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            pointer-events: none;
        }

        .fullscreen-loader-overlay .loader-content {
            pointer-events: auto;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .modal-backdrop.import-progress-backdrop {
            background-color: transparent !important;
            backdrop-filter: none !important;
        }

        .loader-content {
            text-align: center;
            padding: 40px;
        }

        .spinner-border {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            vertical-align: text-bottom;
            border: 0.25em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border 0.75s linear infinite;
        }

        @keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }

        .visually-hidden {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
    </style>
@endpush
@endsection
