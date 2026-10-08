@extends('layouts.main')
@section('title', 'WhatsApp Campaign')
@section('content')

    <!-- Page Wrapper -->
    <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content">

                <div class="row">
                    <div class="col-md-12">

                        <!-- Page Header -->
                        <div class="page-header">
                            <div class="row align-items-center">
                                <div class="col-4">
                                    <h4 class="page-title">Campaign<span class="count-title" id="count-header">0</span></h4>
                                </div>
                                <div class="col-8 text-end">
                                    <div class="head-icons">
                                        <a href="{{ box_route('whatsapp.create_template') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="WhatsApp Templates">
                                            <i class="ti ti-layout-list"></i>
                                        </a>
                                        <a href="{{ box_route('whatsapp.campaign') }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
                                            <i class="ti ti-refresh-dot"></i>
                                        </a>
                                        <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
                                            <i class="ti ti-chevrons-up"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Page Header -->

                        <!-- Campaign Status -->
                        <div class="row">
                            <div class="col-xl-3 col-lg-6">
                                <div class="campaign-box bg-danger-light">
                                    <div class="campaign-img">
                                        <span>
                                            <i class="ti ti-brand-campaignmonitor"></i>
                                        </span>
                                        <p>Campaign</p>
                                    </div>
                                    <h2 id="count-total">0</h2>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6">
                                <div class="campaign-box bg-warning-light">
                                    <div class="campaign-img">
                                        <span>
                                            <i class="ti ti-clock"></i>
                                        </span>
                                        <p>Pending</p>
                                    </div>
                                    <h2 id="count-pending">0</h2>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6">
                                <div class="campaign-box bg-purple-light">
                                    <div class="campaign-img">
                                        <span>
                                            <i class="ti ti-brand-feedly"></i>
                                        </span>
                                        <p>Active</p>
                                    </div>
                                    <h2 id="count-active">0</h2>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6">
                                <div class="campaign-box bg-success-light">
                                    <div class="campaign-img">
                                        <span>
                                            <i class="ti ti-brand-pocket"></i>
                                        </span>
                                        <p>Completed</p>
                                    </div>
                                    <h2 id="count-completed">0</h2>
                                </div>
                            </div>
                        </div>
                        <!-- /Campaign Status -->

                        <!-- Campaign Tab -->
                        <div class="campaign-tab">
                            <ul class="nav">
                                <li>
                                    <a href="{{ box_route('whatsapp.campaign') }}" class="active">Active Campaign<span id="count-sidebar-active">0</span></a>
                                </li>
                                <li>
                                    <a href="{{ box_route('whatsapp.campaign_complete') }}">Completed Campaign</a>
                                </li>
                                <li>
                                    <a href="{{ box_route('whatsapp.campaign_archieve') }}">Archived Campaign</a>
                                </li>
                            </ul>
                        </div>
                        <!-- Campaign Tab -->

                        <div class="card">
                            <div class="card-header">
                                <!-- Search -->
                                <div class="row align-items-center">
                                    <div class="col-sm-4">
                                        <div class="icon-form mb-3 mb-sm-0">
                                            <span class="form-icon"><i class="ti ti-search"></i></span>
                                            <input type="text" class="form-control" placeholder="Search Campaign">
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
                                            <div class="dropdown me-2">
                                                <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
                                                <div class="dropdown-menu  dropdown-menu-end">
                                                    <ul>
                                                        <li>
                                                            <a href="javascript:void(0);" data-url="{{ route('whatsapp.campaign.export.pdf', $box->slug) }}" class="dropdown-item btn-export-pdf"><i class="ti ti-file-type-pdf text-danger me-1"></i>Export
                                                                as PDF</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);" data-url="{{ route('whatsapp.campaign.export.excel', $box->slug) }}" class="dropdown-item btn-export-excel"><i class="ti ti-file-type-xls text-green me-1"></i>Export
                                                                as Excel </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div id="bulk-actions" style="display: none;" class="me-2">
                                                <button id="btn-bulk-delete" class="btn btn-soft-danger px-3 rounded-pill">
                                                    <i class="ti ti-trash me-1"></i>Delete Selected
                                                </button>
                                            </div>
                                            <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#upload-campaign-modal"><i class="ti ti-square-rounded-plus me-2"></i>Add New Campaign</a>
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
                                            <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-sort-ascending-2 me-2"></i>Sort </a>
                                            <div class="dropdown-menu  dropdown-menu-start">
                                                <ul>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="ascending">
                                                            <i class="ti ti-circle-chevron-right me-1"></i>Ascending
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="descending">
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
                                        <div class="dropdown me-2">
                                            <select class="form-select" id="project_filter" aria-label="Filter by Project">
                                                <option value="">All Projects</option>
                                                @foreach($projects as $project)
                                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="icon-form">
                                            <span class="form-icon"><i class="ti ti-calendar"></i></span>
                                            <input type="text" class="form-control bookingrange" placeholder="">
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center flex-wrap row-gap-2">
                                        <div class="dropdown me-2">
                                            <button type="button" class="btn bg-soft-purple text-purple d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                                                <i class="ti ti-columns-3 me-2"></i>Manage Columns
                                            </button>
                                        </div>
                                        <div class="form-sorts dropdown">
                                            <a href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-filter-share"></i>Filter</a>
                                            <div class="filter-dropdown-menu dropdown-menu  dropdown-menu-md-end p-3">
                                                <div class="filter-set-view">
                                                    <div class="filter-set-head">
                                                        <h4><i class="ti ti-filter-share"></i>Filter</h4>
                                                    </div>
                                                    <div class="accordion" id="accordionExample">
                                                        <div class="filter-set-content">
                                                            <div class="filter-set-content-head">
                                                                <a href="#" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">Name</a>
                                                            </div>
                                                            <div class="filter-set-contents accordion-collapse collapse show" id="collapseTwo" data-bs-parent="#accordionExample">
                                                                <div class="filter-content-list">
                                                                    <div class="mb-2 icon-form">
                                                                        <span class="form-icon"><i class="ti ti-search"></i></span>
                                                                        <input type="text" class="form-control" placeholder="Search Name">
                                                                    </div>
                                                                    <ul>
                                                                        <li>
                                                                            <div class="filter-checks">
                                                                                <label class="checkboxs">
                                                                                    <input type="checkbox">
                                                                                    <span class="checkmarks"></span>
                                                                                    Distribution
                                                                                </label>
                                                                            </div>
                                                                        </li>
                                                                        <li>
                                                                            <div class="filter-checks">
                                                                                <label class="checkboxs">
                                                                                    <input type="checkbox">
                                                                                    <span class="checkmarks"></span>
                                                                                    Merchandising
                                                                                </label>
                                                                            </div>
                                                                        </li>
                                                                        <li>
                                                                            <div class="filter-checks">
                                                                                <label class="checkboxs">
                                                                                    <input type="checkbox">
                                                                                    <span class="checkmarks"></span>
                                                                                    Pricing
                                                                                </label>
                                                                            </div>
                                                                        </li>
                                                                        <li>
                                                                            <div class="filter-checks">
                                                                                <label class="checkboxs">
                                                                                    <input type="checkbox">
                                                                                    <span class="checkmarks"></span>
                                                                                    Increased sales
                                                                                </label>
                                                                            </div>
                                                                        </li>
                                                                        <li>
                                                                            <div class="filter-checks">
                                                                                <label class="checkboxs">
                                                                                    <input type="checkbox">
                                                                                    <span class="checkmarks"></span>
                                                                                    Brand recognition
                                                                                </label>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="filter-set-content">
                                                            <div class="filter-set-content-head">
                                                                <a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#owner" aria-expanded="false" aria-controls="owner">Type</a>
                                                            </div>
                                                            <div class="filter-set-contents accordion-collapse collapse" id="owner" data-bs-parent="#accordionExample">
                                                                <div class="filter-content-list">
                                                                    <ul>
                                                                        <li>
                                                                            <div class="filter-checks">
                                                                                <label class="checkboxs">
                                                                                    <input type="checkbox" checked="">
                                                                                    <span class="checkmarks"></span>
                                                                                    Public Relations
                                                                                </label>
                                                                            </div>
                                                                        </li>
                                                                        <li>
                                                                            <div class="filter-checks">
                                                                                <label class="checkboxs">
                                                                                    <input type="checkbox">
                                                                                    <span class="checkmarks"></span>
                                                                                    Content Marketing
                                                                                </label>
                                                                            </div>
                                                                        </li>
                                                                        <li>
                                                                            <div class="filter-checks">
                                                                                <label class="checkboxs">
                                                                                    <input type="checkbox">
                                                                                    <span class="checkmarks"></span>
                                                                                    Brand
                                                                                </label>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="filter-set-content">
                                                            <div class="filter-set-content-head">
                                                                <a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#Status" aria-expanded="false" aria-controls="Status">Start Date</a>
                                                            </div>
                                                            <div class="filter-set-contents accordion-collapse collapse" id="Status" data-bs-parent="#accordionExample">
                                                                <div class="filter-content-list">
                                                                    <ul>
                                                                        <li>
                                                                            <div class="filter-checks">
                                                                                <label class="checkboxs">
                                                                                    <input type="checkbox" checked="">
                                                                                    <span class="checkmarks"></span>
                                                                                    25 Sep 2023
                                                                                </label>
                                                                            </div>
                                                                        </li>
                                                                        <li>
                                                                            <div class="filter-checks">
                                                                                <label class="checkboxs">
                                                                                    <input type="checkbox">
                                                                                    <span class="checkmarks"></span>
                                                                                    29 Sep 2023
                                                                                </label>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="filter-set-content">
                                                            <div class="filter-set-content-head">
                                                                <a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">End Date</a>
                                                            </div>
                                                            <div class="filter-set-contents accordion-collapse collapse" id="collapseOne" data-bs-parent="#accordionExample">
                                                                <div class="filter-content-list">
                                                                    <ul>
                                                                        <li>
                                                                            <div class="filter-checks">
                                                                                <label class="checkboxs">
                                                                                    <input type="checkbox" checked="">
                                                                                    <span class="checkmarks"></span>
                                                                                    19 Oct 2023
                                                                                </label>
                                                                            </div>

                                                                        </li>
                                                                        <li>
                                                                            <div class="filter-checks">
                                                                                <label class="checkboxs">
                                                                                    <input type="checkbox">
                                                                                    <span class="checkmarks"></span>
                                                                                    23 Nov 2023
                                                                                </label>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="filter-set-content">
                                                            <div class="filter-set-content-head">
                                                                <a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">Status</a>
                                                            </div>
                                                            <div class="filter-set-contents accordion-collapse collapse" id="collapseThree" data-bs-parent="#accordionExample">
                                                                <div class="filter-content-list">
                                                                    <ul>
                                                                        <li>
                                                                            <div class="filter-checks">
                                                                                <label class="checkboxs">
                                                                                    <input type="checkbox" checked="">
                                                                                    <span class="checkmarks"></span>
                                                                                    Success
                                                                                </label>
                                                                            </div>
                                                                        </li>
                                                                        <li>
                                                                            <div class="filter-checks">
                                                                                <label class="checkboxs">
                                                                                    <input type="checkbox">
                                                                                    <span class="checkmarks"></span>
                                                                                    Pending
                                                                                </label>
                                                                            </div>
                                                                        </li>
                                                                        <li>
                                                                            <div class="filter-checks">
                                                                                <label class="checkboxs">
                                                                                    <input type="checkbox">
                                                                                    <span class="checkmarks"></span>
                                                                                    Bounced
                                                                                </label>
                                                                            </div>
                                                                        </li>
                                                                        <li>
                                                                            <div class="filter-checks">
                                                                                <label class="checkboxs">
                                                                                    <input type="checkbox">
                                                                                    <span class="checkmarks"></span>
                                                                                    Paused
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
                                                                <a href="campaign-archieve.html" class="btn btn-primary">Filter</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Filter -->

                                <!-- Campaign List -->
                                <div class="table-responsive custom-table">
                                    <table class="table" id="whatsapp-campaign-list">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="no-sort">
                                                    <label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
                                                </th>
                                                <th></th>
                                                <th>Name</th>
                                                <th>Template</th>
                                                <th>Sent</th>
                                                <th>Schedule At</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Chatbot</th>
                                                <th class="text-end">Action</th>
                                        </tr></thead>
                                        <tbody id="campaigns-tbody">
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
                                <!-- /Campaign List -->

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <!-- /Page Wrapper -->

        <!-- Add New Campaign -->
        <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
            <div class="offcanvas-header border-bottom">
                <h4>Add New Campaign</h4>
                <button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="offcanvas-body">
                <form action="campaign-archieve.html">
                    <div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="col-form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="col-form-label">Campaign Type <span class="text-danger">*</span></label>
                                    <select class="select2">
                                        <option>Choose</option>
                                        <option>Public Relations</option>
                                        <option>Brand</option>
                                        <option>Media</option>
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
                                    <input class="form-control" type="text">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Period Value <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="col-form-label">Target Audience <span class="text-danger">*</span></label>
                                    <select class="select" multiple="multiple">
                                        <option selected="">Small Business</option>
                                        <option selected="">Corporate Companies</option>
                                        <option selected="">Urban Apartment</option>
                                        <option>Business</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="col-form-label">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" rows="4"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="col-form-label">Attachment <span class="text-danger">*</span></label>
                                    <div class="drag-attach">
                                        <input type="file">
                                        <div class="img-upload">
                                            <i class="ti ti-file-broken"></i>Upload File
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="col-form-label">Uploaded Files</label>
                                    <div class="upload-file upload-list">
                                        <div>
                                            <h6>tes.txt</h6>
                                            <p>4.25 MB</p>
                                        </div>
                                        <a href="javascript:void(0);" class="text-danger"><i class="ti ti-trash-x"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-end">
                        <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                        <button type="button" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>

        </div>
        <!-- /Add New Campaign -->

        <!-- Edit Campaign Modal -->
        <div class="modal fade" id="edit_campaign_modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Campaign</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="edit-campaign-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_campaign_id" name="campaign_id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Campaign Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_campaign_name" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Template <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_template_id" name="whats_app_template_id" required>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Attach Chatbot <span class="text-muted fw-normal">(Optional)</span></label>
                                <select class="form-select" id="edit_chatbot_id" name="chatbot_id">
                                    <option value="">-- No Chatbot --</option>
                                    @foreach($chatbots as $chatbot)
                                        <option value="{{ $chatbot->id }}">{{ $chatbot->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block">Schedule Send <span class="text-danger">*</span></label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="send_type" id="edit_send_now" value="now" required>
                                    <label class="form-check-label" for="edit_send_now">Send Now</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="send_type" id="edit_send_later" value="scheduled" required>
                                    <label class="form-check-label" for="edit_send_later">Schedule for Later</label>
                                </div>
                            </div>

                            <div class="mb-3" id="edit_schedule_time_container" style="display: none;">
                                <label class="form-label">Schedule Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="edit_schedule_at" name="schedule_at">
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="edit-campaign-submit-btn">Update Campaign</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Edit Campaign Modal -->

        <!-- Delete Campaign -->
        <div class="modal fade" id="delete_campaign" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="text-center">
                            <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                                <i class="ti ti-trash-x fs-36 text-danger"></i>
                            </div>
                            <h4 class="mb-2">Remove campaign?</h4>
                            <p class="mb-0">Are you sure you want to remove <br> campaign you selected.</p>
                            <div class="d-flex align-items-center justify-content-center mt-4">
                                <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" id="confirm-delete-btn" class="btn btn-danger">Yes, Delete it</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Delete Campaign -->

        <!-- Status Campaign Modal -->
        <div class="modal fade" id="status_campaign_modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="text-center">
                            <div class="avatar avatar-xl bg-warning-light rounded-circle mb-3" id="status-modal-icon-container">
                                <i class="ti ti-info-circle fs-36 text-warning" id="status-modal-icon"></i>
                            </div>
                            <h4 class="mb-2" id="status-modal-title">Change Status?</h4>
                            <p class="mb-0" id="status-modal-text">Are you sure you want to change the status of this campaign?</p>
                            <div class="d-flex align-items-center justify-content-center mt-4">
                                <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" id="confirm-status-btn" class="btn btn-warning">Yes, confirm</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Status Campaign Modal -->

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
                        <form action="campaign.html">
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
    <!-- /Page Wrapper -->

    <!-- Template Preview Modal -->
    <div class="modal fade" id="preview_template_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title text-white"><i class="ti ti-eye me-2"></i> Template Preview</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="template_preview_content" class="whatsapp-preview-card">
                        <!-- Content will be injected here -->
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .whatsapp-preview-card {
            background-color: #e5ddd5;
            background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
            padding: 20px;
            border-radius: 10px;
            min-height: 150px;
        }
        .whatsapp-bubble {
            background: #fff;
            padding: 10px 15px;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            position: relative;
            max-width: 85%;
        }
        .cursor-pointer { cursor: pointer; }
        .campaign-row.selected { background-color: rgba(67, 97, 238, 0.05) !important; }
    </style>

    <!-- Sent Numbers Modal -->
    <div class="modal fade" id="sent_numbers_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title text-white"><i class="ti ti-users me-2"></i> Campaign Recipients</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="max-height: 400px; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="ps-4">Recipient</th>
                                    <th>Phone Number</th>
                                    <th>Status</th>
                                    <th class="pe-4">Sent At</th>
                                </tr>
                            </thead>
                            <tbody id="sent_numbers_tbody">
                                <!-- Content will be injected here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
$(document).ready(function() {
    let selectedIds = new Set();
    let currentSort = null;
    let startDate = null;
    let endDate = null;

    // Column Definitions for Manage Columns feature
    const columnDefinitions = [
        { index: 0, name: 'Checkbox', visible: true },
        { index: 1, name: 'Icon', visible: true },
        { index: 2, name: 'Name', visible: true },
        { index: 3, name: 'Template Name', visible: true },
        { index: 4, name: 'Messages Count', visible: true },
        { index: 5, name: 'Schedule At', visible: true },
        { index: 6, name: 'Status', visible: true },
        { index: 7, name: 'Created At', visible: true },
        { index: 8, name: 'Chatbot', visible: true },
        { index: 9, name: 'Action', visible: true }
    ];

    const pageIdentifier = 'whatsapp-campaign';

    function loadColumnPreferences(callback) {
        $.ajax({
            url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug")]) }}',
            type: 'GET',
            data: { page_identifier: pageIdentifier },
            async: false,
            success: function(response) {
                if (response.success && response.data && Array.isArray(response.data) && response.data.length > 0) {
                    try {
                        const preferences = response.data;
                        preferences.forEach(pref => {
                            const prefIndex = parseInt(pref.index);
                            const col = columnDefinitions.find(c => c.index === prefIndex);
                            if (col) {
                                if (typeof pref.visible === 'string') col.visible = pref.visible === 'true' || pref.visible === '1';
                                else col.visible = pref.visible !== undefined ? Boolean(pref.visible) : true;
                            }
                        });
                        if (callback) callback();
                    } catch (e) {
                        console.error('Error loading column preferences:', e);
                        if (callback) callback();
                    }
                } else {
                    if (callback) callback();
                }
            },
            error: function() {
                if (callback) callback();
            }
        });
    }

    function saveColumnPreferences() {
        $.ajax({
            url: '{{ route("settings.column.preferences.save", ["box_slug" => request()->route("box_slug")]) }}',
            type: 'POST',
            data: {
                page_identifier: pageIdentifier,
                column_preferences: columnDefinitions,
                _token: '{{ csrf_token() }}'
            }
        });
    }

    function populateColumnsModal() {
        const columnsList = $('#columnsList');
        columnsList.empty();
        columnDefinitions.forEach(col => {
            if (col.index === 0 || col.index === 1 || col.index === 9) return; // Skip Checkbox, Icon, Action
            const listItem = $(`
                <div class="list-group-item">
                    <div class="form-check">
                        <input class="form-check-input column-toggle" type="checkbox"
                               data-column-index="${col.index}"
                               id="col_${col.index}"
                               ${col.visible ? 'checked' : ''}>
                        <label class="form-check-label" for="col_${col.index}">
                            ${col.name}
                        </label>
                    </div>
                </div>
            `);
            columnsList.append(listItem);
        });
    }

    // Load preferences before initializing Datatables
    loadColumnPreferences(function() {
        populateColumnsModal();
    });
    
    let table = $('#whatsapp-campaign-list').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ box_route('whatsapp.campaign') }}",
            type: "GET",
            data: function(d) {
                if (currentSort) d.custom_sort = currentSort;
                if (startDate && endDate) {
                    d.start_date = startDate;
                    d.end_date = endDate;
                }
                
                var projectId = $('#project_filter').val();
                if (projectId) {
                    d.project_id = projectId;
                }
            },
            dataSrc: function(json) {
                // Update stats
                $('#count-total').text(json.total || 0);
                $('#count-header').text(json.total || 0);
                $('#count-active').text(json.active || 0);
                $('#count-pending').text(json.pending || 0);
                $('#count-completed').text(json.completed || 0);
                return json.data;
            }
        },
        columns: [
            { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, visible: columnDefinitions[0].visible },
            { data: 'icon', name: 'icon', orderable: false, searchable: false, visible: columnDefinitions[1].visible },
            { data: 'name', name: 'name', visible: columnDefinitions[2].visible },
            { data: 'template_name', name: 'template.name', searchable: false, visible: columnDefinitions[3].visible },
            { data: 'messages_count', name: 'messages_count', searchable: false, visible: columnDefinitions[4].visible },
            { data: 'schedule_at', name: 'schedule_at', visible: columnDefinitions[5].visible },
            { data: 'status', name: 'status', visible: columnDefinitions[6].visible },
            { data: 'created_at', name: 'created_at', visible: columnDefinitions[7].visible },
            { data: 'chatbot', name: 'chatbot', orderable: false, searchable: false, visible: columnDefinitions[8].visible },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end', visible: columnDefinitions[9].visible }
        ],
        order: [[7, 'desc']], // Default order by created_at desc
        drawCallback: function() {
            // Restore selected checkbox states
            $('.campaign-checkbox').each(function() {
                if (selectedIds.has($(this).val())) {
                    $(this).prop('checked', true).closest('tr').addClass('selected');
                }
            });
            if (selectedIds.size > 0) $('#bulk-actions').fadeIn(); else $('#bulk-actions').fadeOut();
        }
    });

    // Date Range Picker Initialization
    if ($('.bookingrange').length > 0) {
        $('.bookingrange').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                format: 'DD/MM/YYYY'
            }
        });
        $('.bookingrange').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            startDate = picker.startDate.format('YYYY-MM-DD');
            endDate = picker.endDate.format('YYYY-MM-DD');
            table.ajax.reload();
        });
        $('.bookingrange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            startDate = null;
            endDate = null;
            table.ajax.reload();
        });
    }

    // Handle project filter change
    $('#project_filter').on('change', function() {
        table.ajax.reload();
    });

    // Sort Handler
    $('.sort-item').on('click', function(e) {
        e.preventDefault();
        currentSort = $(this).data('sort');
        table.ajax.reload();
    });

    // Manage Columns Handler
    $(document).on('change', '.column-toggle', function() {
        const index = parseInt($(this).data('column-index'));
        const isVisible = $(this).is(':checked');
        
        // Update datatable
        const column = table.column(index);
        column.visible(isVisible);
        
        // Update definition
        const colDef = columnDefinitions.find(c => c.index === index);
        if (colDef) colDef.visible = isVisible;
        
        // Save preference
        saveColumnPreferences();
    });

    // Delegated Status Change Handler
    $(document).on('click', '.btn-status-campaign', function() {
                        const id = $(this).data('id');
                        const status = $(this).data('status');
                        
                        let actionWord = status === 'PAUSED' ? 'pause' : (status === 'STOPPED' ? 'stop' : 'resume');
                        
                        $('#status-modal-title').text(actionWord.charAt(0).toUpperCase() + actionWord.slice(1) + ' Campaign?');
                        $('#status-modal-text').html(`Are you sure you want to <strong>${actionWord}</strong> this campaign?`);
                        
                        let iconClass = 'ti-info-circle';
                        let colorClass = 'warning';
                        if (status === 'PAUSED') {
                            iconClass = 'ti-player-pause';
                        } else if (status === 'STOPPED') {
                            iconClass = 'ti-square';
                            colorClass = 'danger';
                        } else if (status === 'SENDING') {
                            iconClass = 'ti-player-play';
                            colorClass = 'success';
                        }
                        
                        $('#status-modal-icon-container').removeClass('bg-warning-light bg-danger-light bg-success-light').addClass(`bg-${colorClass}-light`);
                        $('#status-modal-icon').removeClass().addClass(`ti ${iconClass} fs-36 text-${colorClass}`);
                        $('#confirm-status-btn').removeClass('btn-warning btn-danger btn-success').addClass(`btn-${colorClass}`);
                        
                        $('#confirm-status-btn').data('id', id);
                        $('#confirm-status-btn').data('status', status);
                        $('#status_campaign_modal').modal('show');
                    });

                    // Delete Handler
                    $('.btn-delete-campaign').on('click', function() {
                        const id = $(this).data('id');
                        $('#confirm-delete-btn').removeData('bulk');
                        $('#confirm-delete-btn').data('id', id);
                        $('#delete_campaign').modal('show');
                    });

                    // Delegated Edit Handler
                    $(document).on('click', '.btn-edit-campaign', function() {
                        const id = $(this).data('id');
                        let editUrl = "{{ route('whatsapp.campaign.show', ['box_slug' => $box->slug, 'id' => ':id']) }}".replace(':id', id);
                        
                        // Fetch campaign data
                        $.get(editUrl, function(response) {
                            if(response.success && response.campaign) {
                                let campaign = response.campaign;
                                if (campaign.status !== 'PENDING' && campaign.status !== 'SCHEDULED') {
                                    showToast('Only scheduled or pending campaigns can be edited.', 'danger');
                                    return;
                                }

                                $('#edit_campaign_id').val(campaign.id);
                                $('#edit_campaign_name').val(campaign.name);
                                $('#edit_template_id').val(campaign.whats_app_template_id);
                                $('#edit_chatbot_id').val(campaign.whatsapp_chatbot_id || '');
                                
                                if (campaign.schedule_at) {
                                    $('#edit_send_later').prop('checked', true);
                                    $('#edit_schedule_time_container').show();
                                    
                                    // Format date for datetime-local: YYYY-MM-DDThh:mm
                                    const date = new Date(campaign.schedule_at);
                                    const pad = (n) => n.toString().padStart(2, '0');
                                    const localISOTime = date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()) + 'T' + pad(date.getHours()) + ':' + pad(date.getMinutes());
                                    $('#edit_schedule_at').val(localISOTime);
                                } else {
                                    $('#edit_send_now').prop('checked', true);
                                    $('#edit_schedule_time_container').hide();
                                    $('#edit_schedule_at').val('');
                                }
                                
                                $('#edit_campaign_modal').modal('show');
                            }
                        });
                    });

                    // View Numbers Pagination
                    $(document).on('click', '#numbers-pagination .page-link', function(e) {
                        e.preventDefault();
                        const url = $(this).attr('href');
                        if (url && url !== '#') {
                            fetchCampaignNumbers(url);
                        }
                    });

                    // Edit Campaign JS Logic
                    $('input[name="send_type"]').on('change', function() {
                        if ($('#edit_send_later').is(':checked')) {
                            $('#edit_schedule_time_container').show();
                            $('#edit_schedule_at').attr('required', true);
                        } else {
                            $('#edit_schedule_time_container').hide();
                            $('#edit_schedule_at').removeAttr('required');
                        }
                    });

                    $('#edit-campaign-form').on('submit', function(e) {
                        e.preventDefault();
                        
                        const id = $('#edit_campaign_id').val();
                        const url = "{{ route('whatsapp.campaign.update', ['box_slug' => $box->slug, 'id' => ':id']) }}".replace(':id', id);
                        
                        const submitBtn = $('#edit-campaign-submit-btn');
                        submitBtn.prop('disabled', true).html('<i class="ti ti-loader ti-spin me-2"></i>Updating...');

                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: $(this).serialize(),
                            success: function(response) {
                                if(response.success) {
                                    $('#edit_campaign_modal').modal('hide');
                                    fetchCampaigns(); // Refresh the table
                                    showToast("Campaign updated successfully", 'success');
                                } else {
                                    showToast(response.message, 'danger');
                                }
                            },
                            error: function(xhr) {
                                showToast(xhr.responseJSON?.message || "Failed to update campaign.", 'danger');
                            },
                            complete: function() {
                                submitBtn.prop('disabled', false).html('Update Campaign');
                            }
                        });
                    });

    // Add delegated click handler for viewing numbers
    $(document).on('click', '.view-numbers', function() {
                        const cid = $(this).data('id');
                        $('#sent_numbers_tbody').html('<tr><td colspan="4" class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
                        $('#sent_numbers_modal').modal('show');

                        let messagesUrl = "{{ route('whatsapp.campaign.messages', ['box_slug' => $box->slug, 'id' => ':id']) }}".replace(':id', cid);
                        
                        $.get(messagesUrl, function(response) {
                            if (response.success && response.messages) {
                                let rows = '';
                                response.messages.forEach(msg => {
                                    const leadName = msg.lead ? (msg.lead.name || 'Unnamed') : 'Deleted Lead';
                                    const leadPhone = msg.lead ? (msg.lead.phone || '-') : '-';
                                    const status = msg.status || 'Sent';
                                    const sentAt = msg.created_at ? new Date(msg.created_at).toLocaleString('en-GB', {day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'}) : '-';
                                    
                                    let statusClass = 'bg-soft-success text-success';
                                    if (status.toLowerCase() === 'failed') statusClass = 'bg-soft-danger text-danger';
                                    else if (status.toLowerCase() === 'pending') statusClass = 'bg-soft-warning text-warning';

                                    rows += `
                                        <tr>
                                            <td class="ps-4 fw-bold">${leadName}</td>
                                            <td>${leadPhone}</td>
                                            <td><span class="badge ${statusClass}">${status}</span></td>
                                            <td class="pe-4 text-muted small">${sentAt}</td>
                                        </tr>
                                    `;
                                });
                                
                                $('#sent_numbers_tbody').html(rows || '<tr><td colspan="4" class="text-center p-4">No recipient data found.</td></tr>');
                            }
                        }).fail(function() {
                            $('#sent_numbers_tbody').html('<tr><td colspan="4" class="text-center p-4 text-danger">Failed to load recipient data.</td></tr>');
                        });
                    });

                    // Add delegated click handler for preview
                    $(document).on('click', '.preview-template', function() {
                        const cid = $(this).data('id');
                        let editUrl = "{{ route('whatsapp.campaign.show', ['box_slug' => $box->slug, 'id' => ':id']) }}".replace(':id', cid);
                        $.get(editUrl, function(response) {
                            if(response.success && response.campaign && response.campaign.template) {
                                let campaign = response.campaign;
                                let content = '';
                                if (campaign.template.components) {
                                    campaign.template.components.forEach(comp => {
                                        if (comp.type === 'HEADER' && comp.format === 'TEXT') {
                                            content += `<h6 class="fw-bold mb-1">${comp.text}</h6>`;
                                        } else if (comp.type === 'BODY') {
                                            content += `<div style="white-space: pre-wrap;">${comp.text}</div>`;
                                        } else if (comp.type === 'FOOTER') {
                                            content += `<div class="text-muted small mt-1">${comp.text}</div>`;
                                        }
                                    });
                                }
                                
                                $('#template_preview_content').html(`<div class="whatsapp-bubble">${content || 'No content available'}</div>`);
                                $('#preview_template_modal').modal('show');
                            }
                        });
                    });

    // Selection Logic
    $(document).on('click', '.campaign-row', function(e) {
        if ($(e.target).closest('.btn, a, input, i').length) return;
        const cb = $(this).find('.campaign-checkbox');
        if (!cb.length) return;
        const id = cb.val();
        const checked = !cb.prop('checked');
        cb.prop('checked', checked);
        if (checked) { selectedIds.add(id); $(this).addClass('selected'); } 
        else { selectedIds.delete(id); $(this).removeClass('selected'); $('#select-all').prop('checked', false); }
        if (selectedIds.size > 0) $('#bulk-actions').fadeIn(); else $('#bulk-actions').fadeOut();
    });

    $(document).on('change', '.campaign-checkbox', function() {
        const id = $(this).val();
        const checked = $(this).prop('checked');
        if (checked) { selectedIds.add(id); $(this).closest('tr').addClass('selected'); } 
        else { selectedIds.delete(id); $(this).closest('tr').removeClass('selected'); $('#select-all').prop('checked', false); }
        if (selectedIds.size > 0) $('#bulk-actions').fadeIn(); else $('#bulk-actions').fadeOut();
    });

    $('#select-all').on('change', function() {
        const checked = $(this).prop('checked');
        $('.campaign-checkbox').each(function() {
            const id = $(this).val();
            $(this).prop('checked', checked);
            if (checked) { selectedIds.add(id); $(this).closest('tr').addClass('selected'); } 
            else { selectedIds.delete(id); $(this).closest('tr').removeClass('selected'); }
        });
        if (selectedIds.size > 0) $('#bulk-actions').fadeIn(); else $('#bulk-actions').fadeOut();
    });

    // Bulk Delete Action Trigger
    $('#btn-bulk-delete').on('click', function() {
        $('#confirm-delete-btn').data('bulk', true);
        $('#delete_campaign').modal('show');
    });

    $('#confirm-delete-btn').on('click', function() {
        const isBulk = $(this).data('bulk');
        if (isBulk) {
            if (selectedIds.size === 0) return;
            $.ajax({
                url: "{{ route('whatsapp.campaign.bulk-delete', $box->slug) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: Array.from(selectedIds)
                },
                success: function(response) {
                    $('#delete_campaign').modal('hide');
                    selectedIds.clear();
                    $('#select-all').prop('checked', false);
                    $('#bulk-actions').fadeOut();
                    fetchCampaigns();
                    showToast(response.message || "Campaigns deleted successfully", 'success');
                },
                error: function(xhr) {
                    $('#delete_campaign').modal('hide');
                    showToast(xhr.responseJSON?.message || "Failed to bulk delete campaigns.", 'danger');
                }
            });
        } else {
            const id = $(this).data('id');
            let deleteUrl = "{{ route('whatsapp.campaign.destroy', ['box_slug' => $box->slug, 'id' => ':id']) }}".replace(':id', id);
            
            $.ajax({
                url: deleteUrl,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    $('#delete_campaign').modal('hide');
                    if(response.success) {
                        fetchCampaigns();
                        showToast(response.message || "Campaign deleted successfully", 'success');
                    } else {
                        showToast(response.message || "Failed to delete campaign", 'danger');
                    }
                },
                error: function() {
                    $('#delete_campaign').modal('hide');
                    showToast("An error occurred while deleting the campaign.", 'danger');
                }
            });
        }
    });

    $('#confirm-status-btn').on('click', function() {
        const id = $(this).data('id');
        const status = $(this).data('status');
        const url = "{{ route('whatsapp.campaign.status', ['box_slug' => $box->slug, 'id' => ':id']) }}".replace(':id', id);
        
        const btn = $(this);
        const originalText = btn.text();
        btn.prop('disabled', true).html('<i class="ti ti-loader ti-spin me-2"></i>Processing...');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                status: status
            },
            success: function(response) {
                $('#status_campaign_modal').modal('hide');
                btn.prop('disabled', false).html(originalText);
                if(response.success) {
                    showToast(response.message, 'success');
                    fetchCampaigns();
                } else {
                    showToast(response.message, 'danger');
                }
            },
            error: function(xhr) {
                $('#status_campaign_modal').modal('hide');
                btn.prop('disabled', false).html(originalText);
                showToast(xhr.responseJSON?.message || "Failed to change campaign status.", 'danger');
            }
        });
    });

    $('.btn-export-pdf, .btn-export-excel').on('click', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        
        showToast('Download started...', 'info');
        window.location.href = url;
    });

    // Assign Chatbot Handlers
    $(document).on('click', '.btn-assign-chatbot', function() {
        const campaignId = $(this).data('id');
        const chatbotId = $(this).data('chatbot-id');
        $('#assign_chatbot_campaign_id').val(campaignId);
        $('#assign_chatbot_select').val(chatbotId).trigger('change');
        $('#assign_chatbot_modal').modal('show');
    });

    $('#btn-save-chatbot').on('click', function() {
        const campaignId = $('#assign_chatbot_campaign_id').val();
        const chatbotId = $('#assign_chatbot_select').val();
        const btn = $(this);
        const originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="ti ti-loader ti-spin me-2"></i>Saving...');

        let url = "{{ route('whatsapp.campaign.assign-chatbot', ['box_slug' => $box->slug, 'id' => ':id']) }}".replace(':id', campaignId);
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                chatbot_id: chatbotId
            },
            success: function(response) {
                $('#assign_chatbot_modal').modal('hide');
                btn.prop('disabled', false).html(originalText);
                if(response.success) {
                    showToast(response.message, 'success');
                    fetchCampaigns();
                } else {
                    showToast(response.message, 'danger');
                }
            },
            error: function(xhr) {
                $('#assign_chatbot_modal').modal('hide');
                btn.prop('disabled', false).html(originalText);
                showToast(xhr.responseJSON?.message || "Failed to assign chatbot.", 'danger');
            }
        });
    });

    function fetchCampaigns() {
        if (typeof table !== 'undefined') {
            table.ajax.reload(null, false);
        }
    }
    
    fetchCampaigns();
});
</script>
@endpush

<!-- Assign Chatbot Modal -->
<div class="modal fade" id="assign_chatbot_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Assign Chatbot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="assign_chatbot_campaign_id">
                <div class="mb-3">
                    <label class="form-label">Select Chatbot</label>
                    <select class="form-select" id="assign_chatbot_select">
                        <option value="">-- No Chatbot --</option>
                        @foreach($chatbots as $chatbot)
                            <option value="{{ $chatbot->id }}">{{ $chatbot->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted mt-2 d-block">Linking a chatbot will allow users to respond to the campaign messages to trigger automated conversational flows.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-save-chatbot">Save Assignment</button>
            </div>
        </div>
    </div>
</div>
<!-- Manage Columns Modal -->
<div class="modal fade" id="manageColumnsModal" tabindex="-1" aria-labelledby="manageColumnsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="manageColumnsModalLabel">Manage Columns</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-soft-primary border-bottom">
                    <p class="mb-0 text-muted"><i class="ti ti-info-circle me-1"></i> Check the columns you want to show in the table.</p>
                </div>
                <div class="list-group list-group-flush" id="columnsList">
                    <!-- Columns will be populated here by JS -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
