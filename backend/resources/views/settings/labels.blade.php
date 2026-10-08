@extends('layouts.main')
@section('content')
@section('title', 'Labels')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <div class="row">
            <div class="col-md-12">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">Upload</h4>
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
                                <div class="icon-form mb-3 mb-sm-0">
                                    <h3 class="page-title">Label Maping</h3>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
                                    <h5>Total Number of Rows : 0 </h5>
                                </div>
                            </div>
                        </div>
                        <!-- /Search -->
                    </div>
                    <div class="card-body">
                        <div class="col-md-6 offset-3">
                            <form action="#">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <h5 class="mb-1">Setting</h5>
                                        <label class="col-form-label">Create Duplicate if exists</label>
                                        <select class="select">
                                            <option selected disabled>Create Duplicate if exists</option>
                                            <option>No</option>
                                            <option>Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <h5 class="mb-1">Leads</h5>
                                        <label class="col-form-label">Name</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Email</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Email</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Sec Email</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Phone</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Lead Status</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Lead Source</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Query</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">IP Address</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Address</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Postal Code</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Country</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">State</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">City</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Assigned To</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Assigned To Email</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Assigned To Phone</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Is Matured</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Matured Date</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Amount</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Last Activity Type</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Last Activity Status</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Location</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">GST No.</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Lead Type</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Company Name</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Advertising In</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Product Pitched</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Industry</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Created Date</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Production</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Promotion</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">GSTIN No.</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Number of Users</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">PAN Number</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Alternate Number</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Content</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Name</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <h5 class="mb-1">Activities</h5>
                                        <label class="col-form-label">Activity</label>
                                        <select class="select">
                                            <option>-- Select --</option>
                                            <option>Name</option>
                                            <option>Email</option>
                                            <option>Phone</option>
                                            <option>Query</option>
                                        </select>
                                    </div>
                                </div>



                                <div class="d-flex align-items-center justify-content-end">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#">Save Maping</button>
                                </div>



                            </form>
                        </div>
                    </div>

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
        <h5 class="fw-semibold">Upload</h5>
        <button type="button"
            class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
            data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <div class="profile-upload">
                        <div class="profile-upload-img">
                            <span><i class="ti ti-file"></i></span>
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
                            <p>OR Drag And Drop</p>
                        </div>

                    </div>
                    <h5 class="mt-2 mb-2"><a style="color:#E41F07" href="#"><u>Download a Sample csv
                                file</u></a></h5>
                    <h5 style="font-weight:normal;">Only zip or csv allowed.</h5>
                </div>
            </div>


        </div>
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
<div class="modal fade" id="delete_contact" role="dialog">
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
                        <label class="col-form-label">Expected Closing Date <span class="text-danger">*</span></label>
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
                            <option data-image="assets/img/profiles/avatar-20.jpg" selected="">Sharon Roy</option>
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
                        <input class="input-tags form-control" type="text" data-role="tagsinput" name="Label"
                            value="Collab, Rated">
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
@endsection
