@extends('layouts.main')

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/leads-custom.css') }}">
<style>
    /* Custom CSS for Leads can go here */
</style>
@endpush

@section('title', 'Leads List')
@section('content')
@php
    $fields = $fields ?? collect();
    $customFields = $customFields ?? collect();
    $leadSources = $leadSources ?? collect();
    $industries = $industries ?? collect();
    $leadsStatus = $leadsStatus ?? collect();
    $countries = $countries ?? collect();
    $states = $states ?? collect();
    $cities = $cities ?? collect();
    $owners = $owners ?? collect();
    $leadsCount = $leadsCount ?? 0;
    $activeReport = $activeReport ?? null;
@endphp

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
 
        <div class="row">
            <div class="col-md-12">

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/leads-custom.css') }}">
@endpush



 



                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <h4 class="page-title">Leads<span class="count-title">{{ $leadsCount ?? 0 }}</span></h4>
                        </div>
                        <div class="col-8 text-end">
                            <div class="head-icons">
                                <a href="leads.html" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Refresh">
                                    <i class="ti ti-refresh-dot"></i>
                                </a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Collapse" id="collapse-header">
                                    <i class="ti ti-chevrons-up"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->





                <div class="card">
                    <div class="card-header">
                        <!-- Search -->
                        <div class="row align-items-center">
                            
                            <div class="col-sm-8">
                                <div class="d-flex align-items-center flex-wrap row-gap-2">
                                    {{-- <a href="javascript:void(0);" class="btn btn-white me-2" data-bs-toggle="modal"
                                        data-bs-target="#manage_columns"><i class="ti ti-columns me-2"></i>Manage
                                        Columns</a> --}}
                                    <div class="dropdown me-2">
                                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white"
                                            data-bs-toggle="dropdown">
                                            <i class="ti ti-users me-2"></i> <span id="scope-display">All Leads</span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <ul>
                                                <li><a href="javascript:void(0);" class="dropdown-item scope-filter"
                                                        data-scope="all">All Leads</a></li>
                                                <li><a href="javascript:void(0);" class="dropdown-item scope-filter"
                                                        data-scope="team">My Team Leads</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    

                                    
                                    <button id="toggle-starred-view" class="btn btn-light me-2"><i
                                            class="fa fa-star text-warning me-2"></i>Starred</button>

                                    @if(\App\Helpers\PermissionHelper::hasModulePermission('leads', 'read', 'listtrash'))
                                        <button id="toggle-trash-view" class="btn btn-light me-2"><i
                                                class="ti ti-trash me-2"></i>Trash</button>
                                    @endif
                                    @if(\App\Helpers\PermissionHelper::hasModulePermission('leads', 'read', 'listduplicate'))
                                        <button id="toggle-duplicate-view" class="btn btn-light me-2"><i
                                                class="ti ti-copy me-2"></i>Duplicate</button>
                                    @endif
                                    @if(\App\Helpers\PermissionHelper::hasModulePermission('leads', 'read', 'listall'))
                                        <button id="toggle-all-view" class="btn btn-light me-2"><i
                                                class="ti ti-list me-2"></i>All</button>
                                    @endif
                                    
                                    
                                </div>
                            </div>


                            <div class="col-sm-4">
                                <div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
                                    <div class="dropdown me-2">
                                        <a href="javascript:void(0);" class="dropdown-toggle"
                                            data-bs-toggle="dropdown"><i
                                                class="ti ti-package-export me-2"></i>Export</a>
                                        <div class="dropdown-menu  dropdown-menu-end">
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0);" class="dropdown-item btn-export-leads"
                                                        data-type="pdf"><i
                                                            class="ti ti-file-type-pdf text-danger me-1"></i>Export
                                                        as PDF</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);" class="dropdown-item btn-export-leads"
                                                        data-type="excel"><i
                                                            class="ti ti-file-type-xls text-green me-1"></i>Export
                                                        as Excel </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                
                                    @if (\App\Helpers\PermissionHelper::canAccessRoute('leads.store'))
                                        <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas"
                                            data-bs-target="#offcanvas_add"><i
                                                class="ti ti-square-rounded-plus me-2"></i>Add Leads</a>
                                    @endif
                                </div>
                            </div>




                        </div>
                        <!-- /Search -->
                    </div>
                    <div class="card-body">
                        <!-- Filter -->
                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
                            <div class="d-flex align-items-center flex-wrap row-gap-2">
                                <div class="dropdown me-2" id="bulk-action-container" style="display:none;">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown">
                                        Action
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-start">
                                        <ul>
                                            <li>
                                                <div class="dropdown-item">
                                                    <div class="form-check">
                                                        <input class="form-check-input bulk-action-check"
                                                            type="checkbox" value="status" id="chk_status">
                                                        <label class="form-check-label" for="chk_status">Lead
                                                            Status</label>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="dropdown-item">
                                                    <div class="form-check">
                                                        <input class="form-check-input bulk-action-check"
                                                            type="checkbox" value="source" id="chk_source">
                                                        <label class="form-check-label" for="chk_source">Lead
                                                            Source</label>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="dropdown-item">
                                                    <div class="form-check">
                                                        <input class="form-check-input bulk-action-check"
                                                            type="checkbox" value="assign" id="chk_assign">
                                                        <label class="form-check-label" for="chk_assign">Assigned
                                                            To</label>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="dropdown-item">
                                                    <div class="form-check">
                                                        <input class="form-check-input bulk-action-check"
                                                            type="checkbox" value="trash" id="chk_trash">
                                                        <label class="form-check-label" for="chk_trash">Trash</label>
                                                    </div>
                                                </div>
                                            </li>
                                            <li id="li_merge" style="display:none;">
                                                <div class="dropdown-item">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="merge"
                                                            id="chk_merge_manual">
                                                        <label class="form-check-label" for="chk_merge_manual">Merge
                                                            Lead</label>
                                                    </div>
                                                </div>
                                            </li>
                                            <li id="li_restore" style="display:none;">
                                                <div class="dropdown-item">
                                                    <div class="form-check">
                                                        <input class="form-check-input bulk-action-check"
                                                            type="checkbox" value="restore" id="chk_restore">
                                                        <label class="form-check-label" for="chk_restore">Restore
                                                            Selected</label>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="dropdown-item">
                                                    <div class="form-check">
                                                        <input class="form-check-input bulk-action-check"
                                                            type="checkbox" value="whatsapp" id="chk_whatsapp">
                                                        <label class="form-check-label" for="chk_whatsapp">WhatsApp Campaign</label>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="dropdown me-2">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i
                                            class="ti ti-sort-ascending-2 me-2"></i>Sort
                                    </a>
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
                                            <li>
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
                                    <input type="text" class="form-control" id="leadsDateFilter" placeholder="Select Date Range" style="min-width: 220px;">
                                </div>
                            </div>
                            <div class="d-flex align-items-center flex-wrap row-gap-2">
                                <div class="dropdown me-2">
                                    <a href="javascript:void(0);" class="btn btn-white me-2" data-bs-toggle="modal"
                                        data-bs-target="#manage_columns"><i class="ti ti-columns me-2"></i>Manage
                                        Columns</a>

                                </div>
                                <!-- <div class="dropdown me-2">
                                        <a href="javascript:void(0);" class="btn bg-soft-purple text-purple"
                                            data-bs-toggle="dropdown" data-bs-auto-close="outside"><i
                                                class="ti ti-columns-3 me-2"></i>Manage Form</a>
                                        <div class="dropdown-menu  dropdown-menu-md-end dropdown-md p-3">
                                            <h4 class="mb-2 fw-semibold">Want to manage datatables?</h4>
                                            <p class="mb-3">Please drag and drop your column to reorder your table
                                                and enable see option as you want.</p>

                                            <ul id="field-list" class="list-unstyled border-top pt-3">

                                            </ul>
                                        </div>
                                    </div> -->
                               

                                

                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                        data-bs-target="#filter_leads_modal"><i class="ti ti-filter me-2"></i>Add
                                        Filter</button>

                                <div class="new-box-here me-2">
                                    <a href="javascript:void(0);" data-bs-toggle="modal"
                                        data-bs-target="#settings_modal" id="settings_modal_trigger" class=""><i
                                            class="ti ti-settings-cog" style="font-size: 22px;"></i></a>
                                </div>


                                <div class="view-icons">
                                    <a href="leads.html" class="active"><i class="ti ti-list-tree"></i></a>
                                    <a href="leads-kanban.html"><i class="ti ti-grid-dots"></i></a>
                                </div>
                            </div>
                        </div>
                        <!-- /Filter -->

                        <!-- Active Filters Section -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h6 class="text-dark mb-2" id="current_filters_label" style="display:none;">Current Filters
                                </h6>
                                <div class="align-items-start gap-2">
                                    <div id="active_filters_list" class="row">
                                        <!-- Active filters will be shown here -->
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-soft-primary"
                                        data-bs-toggle="modal" data-bs-target="#filter_leads_modal" onclick="$('#filter_leads_modal').find('.modal-title').text('Settings');">
                                        <i class="ti ti-plus"></i> Add Filter
                                    </a>
                                </div>
                            </div>
                        </div>


                        <!-- Contact List -->
                        <div class="table-responsive custom-table" tabindex="0" style="outline: none;">
                            <table class="table" id="leads_lists">
                                <thead>
                                    <tr id="leads_head_row"></tr>
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
<!-- Settings Modal -->
<div class="modal fade" id="settings_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #E41F07;">
                <h5 class="modal-title text-white">Settings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex justify-content-between align-items-center" style="min-height: 100px;">
                <a href="#" class="h6 mb-0 text-decoration-none text-primary" data-bs-toggle="modal"
                    data-bs-target="#saved_lists_modal" data-bs-dismiss="modal">All Dashboards</a>

                <div class="dropdown">
                    <a href="#" class="dropdown-toggle text-decoration-none text-muted" data-bs-toggle="dropdown"
                        aria-expanded="false">Options</a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#create_view_modal"
                                data-bs-dismiss="modal">Clone</a></li>
                        <li class="active-report-action" style="display:none;"><a class="dropdown-item" href="#"
                                data-bs-toggle="modal" data-bs-target="#update_view_modal"
                                data-bs-dismiss="modal">Update</a></li>
                        <li class="active-report-action" style="display:none;"><a class="dropdown-item text-danger"
                                href="javascript:void(0);" id="btn_delete_current_view">Delete</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0);" id="btn_export_current_view">Export</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Saved Lists Modal -->
<div class="modal fade" id="saved_lists_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #E41F07;">
                <h5 class="modal-title text-white">All saved List</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-6 border-end p-3">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                            <h6 class="mb-0">My Saved Lists</h6>
                        </div>
                        <div id="my_saved_lists_container" style="max-height: 400px; overflow-y: auto;">
                            <!-- Dynamic List -->
                            <p class="text-muted text-center mt-3">Loading...</p>
                        </div>
                    </div>
                    <div class="col-md-6 p-3">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                            <h6 class="mb-0">Others Shared Lists</h6>
                        </div>
                        <div id="shared_saved_lists_container" style="max-height: 400px; overflow-y: auto;">
                            <!-- Dynamic List -->
                            <p class="text-muted text-center mt-3">No shared lists found.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create View Modal -->
<div class="modal fade" id="create_view_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #E41F07;">
                <h5 class="modal-title text-white">Create New List</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createViewForm">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required placeholder="List Name">
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="view_is_public" name="is_public">
                            <label class="form-check-label" for="view_is_public">Visible for all</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="view_show_reports"
                                name="show_in_report">
                            <label class="form-check-label" for="view_show_reports">Show in Reports</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="view_is_default" name="is_default">
                            <label class="form-check-label" for="view_is_default">Set a default</label>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">CLOSE</button>
                        <button type="submit" class="btn text-white"
                            style="background-color: #E41F07; border-color: #E41F07;">SUBMIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Update View Modal -->
<div class="modal fade" id="update_view_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #E41F07;">
                <h5 class="modal-title text-white">update List</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateViewForm">
                    <input type="hidden" name="id" id="update_view_id">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="update_view_name" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="update_view_is_public" name="is_public">
                            <label class="form-check-label" for="update_view_is_public">Visible for all</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="update_view_show_report"
                                name="show_in_report">
                            <label class="form-check-label" for="update_view_show_report">Show in Reports</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="update_view_is_default"
                                name="is_default">
                            <label class="form-check-label" for="update_view_is_default">Set a default</label>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">CLOSE</button>
                        <button type="submit" class="btn btn-info text-white">SUBMIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Working Location View Modal -->
<div class="modal fade" id="working_location_view_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #E41F07;">
                <h5 class="modal-title text-white">Working Locations</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <h6>Division Areas List</h6>
                        <div id="division_areas_list" class="border p-3 rounded bg-light">
                            <!-- List of unique division names -->
                        </div>
                    </div>
                    <div class="col-md-12">
                        <h6>Working Locations Table</h6>
                        <div class="table-responsive">
                            <table class="table table-striped" id="working_locations_display_table">
                                <thead>
                                    <tr>
                                        <th>State</th>
                                        <th>City</th>
                                        <th>Division</th>
                                        <th>Company</th>
                                    </tr>
                                </thead>
                                <tbody id="working_locations_display_body">
                                    <!-- Dynamic rows -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- /Page Wrapper -->

<!-- Add Lead -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Add New Lead</h5>
        <button type="button"
            class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
            data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <form id="leadsForm" method="POST" novalidate>
            @csrf
            <div id="sortable-fields-add" class="sortable-container">
                <!-- Fields will be dynamically loaded here via AJAX -->
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Create</button>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const offcanvasAdd = document.getElementById('offcanvas_add');
                if (offcanvasAdd) {
                    offcanvasAdd.addEventListener('show.bs.offcanvas', function () {
                        const container = document.getElementById('sortable-fields-add');
                        // Only load if not already loaded
                        if (container.dataset.loaded !== 'true') {
                            fetch("{{ route('leads.add-form-fields', ['box_slug' => request()->route('box_slug')]) }}")
                                .then(response => response.json())
                                .then(res => {
                                    if(res.status && res.data) {
                                        container.innerHTML = renderAddLeadForm(res.data);
                                        container.dataset.loaded = 'true';
                                        
                                        // Re-initialize select2 if dynamic-select is present
                                        if (typeof jQuery !== 'undefined' && $.fn.select2) {
                                            $(container).find('.dynamic-select').select2({
                                                dropdownParent: $('#offcanvas_add')
                                            });
                                        }
                                    } else {
                                        console.error("Failed to load form fields via API");
                                    }
                                })
                                .catch(error => {
                                    console.error('Error fetching form fields:', error);
                                    container.innerHTML = '<div class="alert alert-danger">Failed to load form fields.</div>';
                                });
                        }
                    });
                }
            });
        </script>
    </div>

</div>
<!-- /Add Lead -->

<!-- Add Company -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add_2">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Add New Company</h5>
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
                                                <img src="" alt="img" class="preview1">
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
                                        <input type="text" class="form-control">
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
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Phone 1 <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control">
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
                                            <option data-image="" selected="">
                                                Hendry Milner</option>
                                            <option data-image="">Guilory Berggren
                                            </option>
                                            <option data-image="">Jami Carlile
                                            </option>
                                            <option data-image="">Theresa Nelson
                                            </option>
                                            <option data-image="">Smith Cooper
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

                                        </div>
                                        <select class="select2">
                                            <option>Choose</option>
                                            <option>Collins</option>
                                            <option>Konopelski</option>
                                            <option>Adams</option>
                                            <option>Schumm</option>
                                            <option>Wisozk</option>
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
                                            <option data-image="">Darlee
                                                Robertson</option>
                                            <option data-image="" selected="">
                                                Sharon Roy</option>
                                            <option data-image="">Vaughan Lewis
                                            </option>
                                            <option data-image="">Jessica Louise
                                            </option>
                                            <option data-image="">Carol Thomas
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Currency <span
                                                class="text-danger">*</span></label>
                                        <select name="currency" class="form-select select"></select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Language <span
                                                class="text-danger">*</span></label>
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
                                        <label class="col-form-label">Query <span
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
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Country</label>
                                        <select class="select">
                                            <option>Choose</option>
                                            <option>USA</option>
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
                                            <option>California</option>
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
                                            <option>Los Angeles</option>
                                            <option>San Diego</option>
                                            <option>Fresno</option>
                                            <option>San Francisco</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-0">
                                        <label class="col-form-label">Zipcode </label>
                                        <input type="text" class="form-control">
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
                                        <input type="text" class="form-control">
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
                                                <input type="radio" class="status-radio" id="public" name="visible">
                                                <label for="public">Public</label>
                                            </div>
                                            <div class="me-2">
                                                <input type="radio" class="status-radio" id="private" name="visible">
                                                <label for="private">Private</label>
                                            </div>
                                            <div data-bs-toggle="modal" data-bs-target="#access_view">
                                                <input type="radio" class="status-radio" id="people" name="visible">
                                                <label for="people">Select People</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-0">
                                        <label class="col-form-label">Status</label>
                                        <div class="d-flex flex-wrap">
                                            <div class="me-2">
                                                <input type="radio" class="status-radio" id="active" name="status"
                                                    checked="">
                                                <label for="active">Active</label>
                                            </div>
                                            <div>
                                                <input type="radio" class="status-radio" id="inactive" name="status">
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#create_success_2">Create</button>
            </div>
        </form>
    </div>
</div>
<!-- /Add Company -->

<!-- Edit Lead -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Edit Lead</h5>
        <button type="button"
            class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
            data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        
            @php
                // Merge System and Custom Fields for unified Drag and Drop
                $mergedFields = collect();

                // Process System Fields
                foreach ($fields as $field) {
                    $field->is_custom = false;

                    // Skip hidden fields (Owner/Created On) or if not visible
                    if (($field->field !== 'assigned_to' && (strtolower($field->name) === 'owner' || strtolower($field->name) === 'lead owner')) || strtolower($field->name) === 'created on')
                        continue;

                    // Check visibility logic
                    // Ensure we check is_visible property if it exists
                    if (isset($field->is_visible) && $field->is_visible == 0)
                        continue;

                    // Hide System Field if it exists in Custom Fields (check duplicate name)
                    // Custom Field col_name vs System Field field
                    if (isset($customFields) && $customFields->contains('col_name', $field->field))
                        continue;

                    $mergedFields->push($field);
                }

                if (isset($customFields)) {
                    // Process Custom Fields
                    foreach ($customFields as $customField) {
                        // Check visibility for custom fields
                        if (isset($customField->status) && $customField->status === 'Disabled')
                            continue;

                        $customField->is_custom = true;
                        // Map ordering to sequence to allow unified sorting
                        // SystemField uses 'sequence', BoxCustomField uses 'ordering'
                        $customField->sequence = $customField->ordering;
                        $mergedFields->push($customField);
                    }
                }

                // Sort by unified sequence
                $mergedFields = $mergedFields->sortBy('sequence');
            @endphp
            <form id="editForm" method="POST">
            @csrf

            <div id="sortable-fields-edit" class="sortable-container">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>

</div>
<!-- /Edit Lead -->

<!-- Move to Trash Confirmation Modal -->
<div class="modal fade" id="delete_lead_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Move to Trash</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to move this lead to trash?</p>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="lead_id_to_delete">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-trash">Confirm</button>
            </div>
        </div>
    </div>
</div>
<!-- /Move to Trash Confirmation Modal -->

<!-- Manage Columns Modal -->
<div class="modal fade" id="manage_columns" tabindex="-1" aria-labelledby="manageColumnsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="manageColumnsLabel">Manage Columns</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Drag and drop to reorder columns. Toggle visibility using the switches.</p>
                <div class="mb-3 icon-form">
                    <span class="form-icon"><i class="ti ti-search"></i></span>
                    <input type="text" class="form-control" id="manage-columns-search" placeholder="Search Columns">
                </div>
                <div id="columns_list" class="list-group">
                    <!-- Columns will be populated here -->
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save_column_preferences">Save Changes</button>
            </div>
        </div>
    </div>
</div>
<!-- /Manage Columns Modal -->



<!-- Clone Modal -->
<div class="modal fade" id="clone_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Clone </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form action="#">
                <div class="modal-body pb-0">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Name </label>
                                <input type="text" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Make it default </label>
                            <div class="mb-3">
                                <div class="d-flex flex-wrap">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" name="Radio" id="Radio-sm">
                                        <label class="form-check-label" for="Radio-sm">
                                            Yes
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="Radio" id="Radio-sm">
                                        <label class="form-check-label" for="Radio-sm">
                                            No
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Share with others </label>
                            <div class="mb-3">
                                <div class="d-flex flex-wrap">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" name="Radio" id="Radio-sm">
                                        <label class="form-check-label" for="Radio-sm">
                                            Yes
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="Radio" id="Radio-sm">
                                        <label class="form-check-label" for="Radio-sm">
                                            No
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>




                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel </button>
                    <button type="submit" class="btn btn-primary">Submit </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Clone Modal -->


<!-- Duplicate Lead Modal -->
<div class="modal fade" id="duplicate_lead_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #E41F07;">
                <h4 class="modal-title text-white">Duplicate</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="duplicateLeadForm">
                <input type="hidden" name="lead_id" id="duplicate_lead_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="duplicate_department" class="form-label text-danger">Department</label>
                        <select class="form-select select" id="duplicate_department">
                            <option value="">Select Department</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="duplicate_designation_visible" class="form-label text-danger">Designation</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="duplicate_designation_visible"
                                placeholder="Select Designation" readonly
                                style="background-color: #f8f9fa; cursor: not-allowed;">
                            <input type="hidden" id="duplicate_designation" name="duplicate_designation">
                            <button class="btn btn-outline-secondary" type="button"
                                onclick="if($('#duplicate_department').val()) { window.openDesignationTree($('#duplicate_department').val(), '#duplicate_designation'); } else { alert('Please select a department first'); }"><i
                                    class="ti ti-sitemap"></i></button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-danger">Assigned To</label>
                        <select class="form-select" name="assigned_to" id="duplicate_assigned_to" required>
                            <option value="">Please select</option>
                            <!-- Options populated via JS from #bulk_assigned_to -->
                        </select>
                        <div class="invalid-feedback">Please select.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lead Source</label>
                        <select class="form-select" name="lead_source_id" id="duplicate_lead_source">
                            <option value="">Select Lead Source</option>
                            <!-- Options populated via JS from #bulk_lead_source -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn text-white"
                        style="background-color: #E41F07; border-color: #E41F07;">SUBMIT</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Duplicate Lead Modal -->










<!-- Create Lead -->
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
                    <h3>Lead Created Successfully!!!</h3>
                    <p>View the details of lead, created</p>
                    <div class="col-lg-12 text-center modal-btn">
                        <a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
                        <a href="leads-details.html" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Create Lead -->

<!-- Create Company -->
<div class="modal custom-modal fade" id="create_success_2" role="dialog">
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
                <form action="leads.html">
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
<div class="modal custom-modal fade" id="access_view" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Access For</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form action="leads.html">
                    <div class="mb-2 icon-form">
                        <span class="form-icon"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search">
                    </div>
                    <div class="access-wrap">
                        <ul>
                            <li class="select-people-checkbox">
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                    <span class="people-profile">
                                        <img src="" alt="">
                                        <a href="#">Darlee Robertson</a>
                                    </span>
                                </label>
                            </li>
                            <li class="select-people-checkbox">
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                    <span class="people-profile">
                                        <img src="" alt="">
                                        <a href="#">Sharon Roy</a>
                                    </span>
                                </label>
                            </li>
                            <li class="select-people-checkbox">
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                    <span class="people-profile">
                                        <img src="" alt="">
                                        <a href="#">Vaughan Lewis</a>
                                    </span>
                                </label>
                            </li>
                            <li class="select-people-checkbox">
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                    <span class="people-profile">
                                        <img src="" alt="">
                                        <a href="#">Jessica Louise</a>
                                    </span>
                                </label>
                            </li>
                            <li class="select-people-checkbox">
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                    <span class="people-profile">
                                        <img src="" alt="">
                                        <a href="#">Carol Thomas</a>
                                    </span>
                                </label>
                            </li>
                            <li class="select-people-checkbox">
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                    <span class="people-profile">
                                        <img src="" alt="">
                                        <a href="#">Dawn Mercha</a>
                                    </span>
                                </label>
                            </li>
                        </ul>
                    </div>
                    <div class="modal-btn text-end">
                        <a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Bulk Assign Lead Modal -->
<div class="modal fade" id="assign_to_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Leads</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bulkAssignForm">
                    <div class="mb-3">
                        <label for="assign_department" class="form-label">Department</label>
                        <select class="form-select select" id="assign_department">
                            <option value="">Select Department</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assign_designation_visible" class="form-label">Designation</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="assign_designation_visible"
                                placeholder="Select Designation" readonly
                                style="background-color: #f8f9fa; cursor: not-allowed;">
                            <input type="hidden" id="assign_designation">
                            <button class="btn btn-outline-secondary" type="button"
                                onclick="if($('#assign_department').val()) { window.openDesignationTree($('#assign_department').val(), '#assign_designation'); } else { alert('Please select a department first'); }"><i
                                    class="ti ti-sitemap"></i> Select</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign To <span class="text-danger">*</span></label>
                        <select class="select form-control" name="bulk_assigned_to" id="bulk_assigned_to" required>
                            <option value="">Select User</option>
                            @foreach ($owners as $owner)
                                <option value="{{ $owner->id }}" data-department-id='{{ $owner->department_id }}'
                                    data-designation-id='{{ $owner->designation_id }}'>{{ $owner->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('frontend.employess.components.designation_tree_modal')
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.departmentOptions = {};
            window.teamOptions = window.teamOptions || {};

            // Backup original options for all filtering
            window.allAssignOwners = $('#bulk_assigned_to option').clone();

            function fetchAndPopulateUsers($select, deptId, desigId, callback) {
                if (!$select || $select.length === 0) return;

                const url = "{{ route('get.users.list', ['box_slug' => request()->route('box_slug')]) }}";
                const data = {
                    department_id: deptId || '',
                    designation_id: desigId || ''
                };

                // Don't show "Loading..." if no filters are selected (maybe fetch all)
                // But actually fetching all is fine.

                $.ajax({
                    url: url,
                    method: 'GET',
                    data: data,
                    success: function (response) {
                        $select.empty().append('<option value="">Select User</option>');
                        if (response.status && response.data) {
                            response.data.forEach(function (user) {
                                $select.append(`<option value="${user.id}">${user.name}</option>`);
                            });
                        }
                        if ($select.hasClass('select-multiple') || $select.attr('multiple')) {
                            // Keep multiple if needed, but here it seems they are single selects
                        }
                        $select.trigger('change.select2');
                        if (callback) callback();
                    },
                    error: function () {
                        console.error('Failed to fetch filtered users');
                    }
                });
            }

            window.loadDepartments = function () {
                var boxSlug = "{{ request()->route('box_slug') }}";
                if (!boxSlug) return;
                $.ajax({
                    url: '/' + boxSlug + '/manage-department',
                    method: 'GET',
                    success: function (response) {
                        if (response.status) {
                            const departments = response.data;
                            window.departmentOptions = {};
                            window.teamOptions = {};
                            $('#assign_department').empty().append('<option value="">Select Department</option>');
                            $('.add-department-select, .edit-department-select').empty().append('<option value="">Select Department</option>');
                            $('#multi_assign_department').empty().append('<option value="">Select Department</option>');
                            departments.forEach(dept => {
                                window.departmentOptions[dept.id] = dept.name;
                                window.teamOptions[dept.id] = dept.all_teams || dept.allTeams || [];
                                $('#assign_department').append(`<option value="${dept.id}">${dept.name}</option>`);
                                $('.add-department-select, .edit-department-select').append(`<option value="${dept.id}">${dept.name}</option>`);
                                $('#multi_assign_department').append(`<option value="${dept.id}">${dept.name}</option>`);
                            });
                            $('#assign_department, #multi_assign_department').trigger('change.select2');
                            $('.add-department-select, .edit-department-select').trigger('change.select2');
                        }
                    }
                });
            }

            $('#assign_to_modal').on('show.bs.modal', function () {
                if (Object.keys(window.departmentOptions || {}).length === 0) {
                    loadDepartments();
                }
            });

            $('#assign_department').on('change', function () {
                $('#assign_designation').val('').trigger('change');
                $('#assign_designation_visible').val('');
                filterOwners();

                var val = $(this).val();
                if (val && typeof window.openDesignationTree === 'function') {
                    window.openDesignationTree(val, '#assign_designation');
                }
            });

            $('#assign_designation').on('change', function () {
                filterOwners();
            });

            $(document).on('change', '#multi_assign_department', function () {
                $('#multi_assign_designation').val('').trigger('change');
                $('#multi_assign_designation_visible').val('');
                filterMultiOwners($(this).closest('.action-group'));

                var val = $(this).val();
                if (val && typeof window.openDesignationTree === 'function') {
                    window.openDesignationTree(val, '#multi_assign_designation');
                }
            });

            $(document).on('change', '#multi_assign_designation', function () {
                filterMultiOwners($(this).closest('.action-group'));
            });

            function filterMultiOwners($group) {
                const assignedToSelect = $('#multi_bulk_assigned_to');
                if (assignedToSelect.length === 0) return;

                const deptId = $('#multi_assign_department').val();
                const desigId = $('#multi_assign_designation').val();

                fetchAndPopulateUsers(assignedToSelect, deptId, desigId);
            }

            function filterOwners() {
                const deptId = $('#assign_department').val();
                const desigId = $('#assign_designation').val();
                const $select = $('#bulk_assigned_to');

                fetchAndPopulateUsers($select, deptId, desigId);
            }

            $(document).on('change', '.add-department-select, .edit-department-select', function () {
                var targetField = $(this).data('target');
                var prefix = $(this).hasClass('add-department-select') ? 'add_' : 'edit_';
                $('#' + prefix + 'designation_' + targetField).val('').trigger('change');
                $('#' + prefix + 'designation_visible_' + targetField).val('');
                filterFormOwners($(this).closest('form'), targetField);

                var val = $(this).val();
                if (val && typeof window.openDesignationTree === 'function') {
                    window.openDesignationTree(val, '#' + prefix + 'designation_' + targetField);
                }
            });

            $(document).on('change', '.add-designation-select, .edit-designation-select', function () {
                var targetField = $(this).data('target');
                filterFormOwners($(this).closest('form'), targetField);
            });

            function filterFormOwners($form, targetField) {
                var prefix = $form.attr('id') === 'leadsForm' ? 'add_' : 'edit_';
                // Sanitize targetField for ID selector (remove [] if present)
                const idTarget = targetField.replace('[]', '');
                const deptId = $('#' + prefix + 'department_' + idTarget).val();
                const desigId = $('#' + prefix + 'designation_' + idTarget).val();
                const $select = $form.find('select[name="' + targetField + '"], select[name="' + targetField + '[]"]');

                fetchAndPopulateUsers($select, deptId, desigId);
            }

            $(document).on('change', '#duplicate_department', function () {
                $('#duplicate_designation').val('').trigger('change');
                $('#duplicate_designation_visible').val('');
                filterDuplicateOwners();

                var val = $(this).val();
                if (val && typeof window.openDesignationTree === 'function') {
                    window.openDesignationTree(val, '#duplicate_designation');
                }
            });

            $(document).on('change', '#duplicate_designation', function () {
                filterDuplicateOwners();
            });

            function filterDuplicateOwners() {
                const deptId = $('#duplicate_department').val();
                const desigId = $('#duplicate_designation').val();
                const $select = $('#duplicate_assigned_to');

                fetchAndPopulateUsers($select, deptId, desigId);
            }

            $('#offcanvas_add, #offcanvas_edit').on('show.bs.offcanvas', function () {
                if (Object.keys(window.departmentOptions || {}).length === 0) {
                    loadDepartments();
                }
                const $form = $(this).find('form');
                const prefix = $form.attr('id') === 'leadsForm' ? 'add_' : 'edit_';
                $form.find('select.add-owner-select, select.edit-owner-select').each(function () {
                    let targetField = $(this).attr('name');
                    if (targetField) targetField = targetField.replace('[]', '');
                    if (!window.allFormOwners) window.allFormOwners = {};
                    if (!window.allFormOwners[prefix + targetField]) {
                        window.allFormOwners[prefix + targetField] = $(this).find('option').clone();
                    }
                });
            });
        });
    </script>
@endpush
<!-- /Bulk Assign Lead Modal -->

<!-- Bulk Update Status Modal -->
<div class="modal fade" id="update_status_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Lead Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bulkUpdateStatusForm">
                    <div class="mb-3">
                        <label class="form-label">Lead Status <span class="text-danger">*</span></label>
                        <select class="select form-control" name="bulk_lead_status" id="bulk_lead_status" required>
                            <option value="">Select Status</option>
                            @foreach ($leadsStatus as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Bulk Update Status Modal -->

<!-- Bulk Update Source Modal -->
<div class="modal fade" id="update_source_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Lead Source</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bulkUpdateSourceForm">
                    <div class="mb-3">
                        <label class="form-label">Lead Source <span class="text-danger">*</span></label>
                        <select class="select form-control" name="bulk_lead_source" id="bulk_lead_source" required>
                            <option value="">Select Source</option>
                            @foreach ($leadSources as $source)
                                <option value="{{ $source->id }}">{{ $source->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Bulk Update Source Modal -->

<!-- Filter Leads Modal -->
<div class="modal fade" id="filter_leads_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Filter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filterLeadsForm">
                    <!-- Step 1: Choose Field -->
                    <div id="filter-step-1">
                        <div class="mb-3">
                            <label class="form-label">Choose Field <span class="text-danger">*</span></label>
                            <select class="select form-control" id="filter_field" required>
                                <option value="">Choose Field</option>
                                <!-- Options populated by JS -->
                            </select>
                        </div>
                    </div>

                    <!-- Step 2: Configure Filter (Hidden initially) -->
                    <div id="filter-step-2" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label" id="filter_field_label">Selected Field</label>
                            <input type="hidden" id="filter_field_value">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Condition</label>
                            <div class="filter-radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="filter_operator" value="contains"
                                        id="op_contains" checked>
                                    <label class="form-check-label" for="op_contains">Contains</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="filter_operator"
                                        value="not_contains" id="op_not_contains">
                                    <label class="form-check-label" for="op_not_contains">Does not Contain</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="filter_operator" value="empty"
                                        id="op_empty">
                                    <label class="form-check-label" for="op_empty">Empty</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="filter_operator"
                                        value="not_empty" id="op_not_empty">
                                    <label class="form-check-label" for="op_not_empty">Not Empty</label>
                                </div>
                                <div class="form-check" id="op_equals_div">
                                    <input class="form-check-input" type="radio" name="filter_operator" value="equals"
                                        id="op_equals">
                                    <label class="form-check-label" for="op_equals">Equals</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="filter_value_container">
                            <label class="form-label">Value</label>
                            <input type="text" class="form-control" id="filter_value" placeholder="Enter value">
                            <!-- Dynamic input type based on field? -->
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary me-2" id="btn-filter-back">Back</button>
                            <button type="submit" class="btn btn-primary">Apply Filter</button>
                        </div>
                    </div>

                    <!-- Step 1 Footer -->
                    <div class="text-end" id="filter-step-1-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Filter Leads Modal -->

<!-- Bulk Trash/Delete Confirm Modal -->
<div class="modal fade" id="delete_confirm_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Move to Trash</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to move selected leads to trash?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-trash">Confirm</button>
            </div>
        </div>
    </div>
</div>
<!-- /Bulk Trash/Delete Confirm Modal -->

<!-- WhatsApp Campaign & Quick Modal -->
@include('whatsapp.partials.leads_whatsapp_modal')
<!-- Multi Action Modal -->
<div class="modal fade" id="multi_action_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="multiActionForm">
                    <input type="hidden" id="multi_lead_ids" name="lead_ids">
                    <div id="multi_action_inputs"></div>
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirmed</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Multi Action Modal -->

<!-- Manual Merge Modal -->
<div class="modal fade" id="manual_merge_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white bg-primary">
                <h5 class="modal-title text-white">Merge Lead</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="manualMergeForm">
                    <input type="hidden" id="merge_source_ids" name="lead_ids">
                    <div class="mb-3">
                        <p class="text-primary fw-bold mb-1">Merge selected lead(s) INTO a Target Lead:</p>
                        <label class="form-label small">Enter Target Lead ID <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="target_lead_id" id="modal_merge_target_id"
                            placeholder="e.g. 12345" required>
                        <div class="form-text mt-2 text-danger small">
                            <i class="ti ti-info-circle me-1"></i> Warning: This action will move all activities,
                            messages, and history to the Target Lead and then permanently delete the source lead(s).
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Merge Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Manual Merge Modal -->


<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">
    <div id="leadToast" class="toast align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Success!</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>


<!-- Custom Pagination Modal -->
<div class="modal fade" id="custom_pagination_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Custom Records Per Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="customPaginationForm">
                    <div class="mb-3">
                        <label class="form-label">Enter number of records to view:</label>
                        <input type="number" class="form-control" id="custom_page_length" min="1"
                            placeholder="e.g. 2000" required>
                        <div class="form-text">For amounts overlapping 500, a batch process with progress bar will be
                            initiated.</div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Load Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Batch Progress Modal -->
<div class="modal fade" id="batch_progress_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Loading Heavy Data</h5>
            </div>
            <div class="modal-body text-center">
                <p id="batch_status_text" class="mb-2">Initializing...</p>
                <div class="progress mb-3" style="height: 20px;">
                    <div id="batch_progress_bar" class="progress-bar progress-bar-striped progress-bar-animated"
                        role="progressbar" style="width: 0%">0%</div>
                </div>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Please wait...</span>
                    <span>Elapsed: <span id="batch_timer">00:00</span></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Team Members Modal -->
<div class="modal fade" id="team_members_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Team Members</h5>
                <div class="ms-3">
                    <label class="form-check-label small">
                        <input type="checkbox" id="select_all_team_members"> Select All
                    </label>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="team_members_hierarchy" class="mt-2">
                    <!-- Hierarchical tree populated by JS -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="apply_team_member_filter">Filter Leads</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<style>
    .sortable-container {
        min-height: 100px;
    }

    /* Team Member Hierarchy Styles */
    .team-tree-node {
        margin-left: 25px;
        border-left: 1px dashed #ccc;
        padding-left: 15px;
    }

    .team-tree-root {
        margin-left: 0;
    }

    .team-member-item {
        display: flex;
        align-items: center;
        padding: 8px 10px;
        border-radius: 4px;
        transition: background 0.2s;
    }

    .team-member-item:hover {
        background: #f0f4f9;
    }

    .member-info {
        margin-left: 12px;
    }

    .member-name {
        font-weight: 500;
        display: block;
    }

    .member-email {
        font-size: 11px;
        color: #777;
    }

    .toggle-subordinates {
        cursor: pointer;
        margin-right: 5px;
        width: 16px;
        display: inline-block;
    }

    .draggable-field {
        transition: all 0.3s ease;
        background-color: #fff;
    }

    .draggable-field:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .drag-handle {
        opacity: 0.3;
        transition: opacity 0.2s ease;
        z-index: 10;
    }

    .draggable-field:hover .drag-handle {
        opacity: 1;
    }

    .drag-handle:hover {
        opacity: 1 !important;
        cursor: grab !important;
    }

    .drag-handle:active {
        cursor: grabbing !important;
    }

    /* Ghost element (the element being dragged) */
    .sortable-ghost {
        opacity: 0.4;
        background-color: #f0f0f0;
        border: 2px dashed #6c757d !important;
    }

    /* Chosen element (when drag starts) */
    .sortable-chosen {
        background-color: #f8f9fa;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Drag element (the element in its original position) */
    .sortable-drag {
        opacity: 1;
        cursor: grabbing !important;
    }

    /* Fallback element (for touch devices) */
    .sortable-fallback {
        display: block !important;
        opacity: 0.8;
        background-color: #fff;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        border: 2px solid #007bff;
        cursor: grabbing !important;
    }

    /* Smooth animation during reordering */
    .draggable-field {
        transition: transform 0.15s ease;
    }

    /* Visual feedback for drop zone */
    .sortable-container .draggable-field.sortable-ghost+.draggable-field {
        margin-top: 10px;
    }

    /* Make sure drag handle icon is visible */
    .drag-handle i {
        font-size: 18px;
        line-height: 1;
    }

    /* Prevent text selection during drag */
    .draggable-field.sortable-chosen,
    .draggable-field.sortable-drag {
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }

    /* Add a subtle pulse animation to the drag handle on hover */
    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }
    }

    .drag-handle:hover i {
        animation: pulse 1s ease-in-out infinite;
    }

    /* Success feedback after drop */
    .draggable-field.drop-success {
        animation: dropSuccess 0.5s ease;
    }

    @keyframes dropSuccess {
        0% {
            background-color: #d4edda;
            border-color: #28a745;
        }

        100% {
            background-color: #fff;
            border-color: #dee2e6;
        }
    }
</style>

@endpush

@push('scripts')
    <script>
        // Global vars
        var allColumnDefinitions = [];
        var columnPreferences = {};
        var leadsTableInstance = null;
        var columnsSortableInstance = null;
        var isTrashView = false;
        var isStarredView = false;
        var isDuplicateView = false;
        var isAllView = false;
        var currentLeadScope = 'all';
        window.activeLeadFilter = [];
        window.selectedLeadIds = new Set();
        window.optimisticRemovedIds = new Set();

        // Handle URL-based filters (from dashboard)
        function handleUrlFilters() {
            const urlParams = new URLSearchParams(window.location.search);
            const filterParam = urlParams.get('filter');
            const statusParam = urlParams.get('lead_status_id');
            const userId = {{ Auth::id() }};

            if (filterParam === 'unassigned') {
                window.activeLeadFilter.push({
                    field: 'assigned_to',
                    operator: 'empty',
                    value: '',
                    label: 'Owner: Unassigned'
                });
            } else if (filterParam === 'new') {
                window.activeLeadFilter.push({
                    field: 'created_at',
                    operator: 'equals',
                    value: 'this_week',
                    label: 'Created: This Week'
                });
            } else if (filterParam === 'lost') {
                window.activeLeadFilter.push({
                    field: 'lead_status',
                    operator: 'contains',
                    value: 'Lost',
                    label: 'Status: Lost'
                });
            } else if (statusParam) {
                const statusName = urlParams.get('status_name') || statusParam;
                // Handle status box redirection
                window.activeLeadFilter.push({
                    field: 'lead_status_id',
                    operator: 'equals',
                    value: statusParam,
                    label: 'Status: ' + statusName
                });
            }

            // Re-render active filters UI
            if (window.activeLeadFilter.length > 0 && typeof renderActiveFilters === 'function') {
                renderActiveFilters();
            }
        }


        function initOwnerSelects() {
            $('.add-owner-select, .edit-owner-select').each(function () {
                $(this).select2({
                    placeholder: $(this).attr('data-placeholder') || "Select Assignees",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $(this).closest('.offcanvas') || $(this).closest('.modal')
                });
            });
        }

        $(document).ready(function () {
            initOwnerSelects();
            handleUrlFilters();
            // Helper function to escape HTML
            function escapeHtml(text) {
                if (!text) return '';
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, function (m) { return map[m]; });
            }

            // Load Sources dynamically into filter dropdown
            function loadSourcesFilter() {
                const boxSlug = "{{ request()->route('box_slug') }}";
                const url = "{{ url('/') }}/" + boxSlug + "/get-sources-list";
                console.log('Loading sources from:', url);

                $.ajax({
                    url: url,
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        console.log('Sources response:', response);
                        if (response && response.status && response.data) {
                            const sourceList = $('#source-filter-list');
                            if (sourceList.length === 0) {
                                console.error('Source filter list element not found!');
                                return;
                            }

                            sourceList.empty();

                            if (response.data.length === 0) {
                                sourceList.html('<li class="text-muted p-2">No sources found</li>');
                                return;
                            }

                            response.data.forEach(function (source) {
                                const listItem = $('<li></li>');
                                const checkbox = $('<div class="filter-checks"></div>');
                                checkbox.html(
                                    '<label class="checkboxs">' +
                                    '<input type="checkbox" class="source-filter-checkbox" value="' + source.id + '" data-source-name="' + escapeHtml(source.name) + '">' +
                                    '<span class="checkmarks"></span>' +
                                    escapeHtml(source.name) +
                                    '</label>'
                                );
                                listItem.append(checkbox);
                                sourceList.append(listItem);
                            });

                            console.log('Sources loaded successfully. Count:', response.data.length);
                        } else {
                            console.error('Invalid response format:', response);
                            $('#source-filter-list').html('<li class="text-danger p-2">Invalid response format</li>');
                        }
                    },

                    
                    error: function (xhr, status, error) {
                        console.error('Failed to load sources:', {
                            status: status,
                            error: error,
                            response: xhr.responseText,
                            xhr: xhr
                        });
                        $('#source-filter-list').html('<li class="text-danger p-2">Failed to load sources: ' + error + '</li>');
                    }
                });
            }

            // Load Lead Status dynamically into filter dropdown
            function loadLeadStatusFilter() {
                const boxSlug = "{{ request()->route('box_slug') }}";
                const url = "{{ url('/') }}/" + boxSlug + "/get-lead-status-list";
                console.log('Loading lead statuses from:', url);

                $.ajax({
                    url: url,
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        console.log('Lead statuses response:', response);
                        if (response && response.status && response.data) {
                            const statusList = $('#lead-status-filter-list');
                            if (statusList.length === 0) {
                                console.error('Lead status filter list element not found!');
                                return;
                            }

                            statusList.empty();

                            if (response.data.length === 0) {
                                statusList.html('<li class="text-muted p-2">No statuses found</li>');
                                return;
                            }

                            response.data.forEach(function (status) {
                                const listItem = $('<li></li>');
                                const checkbox = $('<div class="filter-checks"></div>');
                                checkbox.html(
                                    '<label class="checkboxs">' +
                                    '<input type="checkbox" class="status-filter-checkbox" value="' + status.id + '" data-status-name="' + escapeHtml(status.name) + '">' +
                                    '<span class="checkmarks"></span>' +
                                    escapeHtml(status.name) +
                                    '</label>'
                                );
                                listItem.append(checkbox);
                                statusList.append(listItem);
                            });

                            console.log('Lead statuses loaded successfully. Count:', response.data.length);
                        } else {
                            console.error('Invalid response format:', response);
                            $('#lead-status-filter-list').html('<li class="text-danger p-2">Invalid response format</li>');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Failed to load lead statuses:', {
                            status: status,
                            error: error,
                            response: xhr.responseText,
                            xhr: xhr
                        });
                        $('#lead-status-filter-list').html('<li class="text-danger p-2">Failed to load statuses: ' + error + '</li>');
                    }
                });
            }

            // Source search functionality
            $(document).on('keyup', '#source-search', function () {
                const searchTerm = $(this).val().toLowerCase();
                $('#source-filter-list li').each(function () {
                    const sourceName = $(this).find('label').text().toLowerCase();
                    if (sourceName.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Manage Columns search functionality
            $(document).on('keyup', '#manage-columns-search', function () {
                const searchTerm = $(this).val().toLowerCase();
                $('#columns_list .list-group-item').each(function () {
                    const colName = $(this).find('span.fw-medium').text().toLowerCase();
                    if (colName.includes(searchTerm)) {
                        $(this).removeClass('d-none').addClass('d-flex');
                    } else {
                        $(this).removeClass('d-flex').addClass('d-none');
                    }
                });
            });

            // Load filters on page load - with a small delay to ensure DOM is ready
            setTimeout(function () {
                if ($('#source-filter-list').length > 0) {
                    loadSourcesFilter();
                } else {
                    console.error('Source filter list element not found in DOM');
                }
                if ($('#lead-status-filter-list').length > 0) {
                    loadLeadStatusFilter();
                } else {
                    console.error('Lead status filter list element not found in DOM');
                }
            }, 100);

            $('#leadsDateFilter').daterangepicker({
                autoUpdateInput: false, // Important: keep this false so it doesn't default to "This Month"
                locale: {
                    format: 'MM/DD/YYYY',
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            // Reload DataTables when Date Range changes
            $('#leadsDateFilter').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                if (leadsTableInstance) {
                    leadsTableInstance.ajax.reload(null, false);
                } else {
                    fetchLeadsList(); // For client-side mode or initial load
                }
            });

            // Optional: Clear filter on cancel
            $('#leadsDateFilter').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                if (leadsTableInstance) {
                    leadsTableInstance.ajax.reload(null, false);
                } else {
                    fetchLeadsList();
                }
            });

                // Validation Function
                const validateAnyForm = function($form) {
                    let isValid = true;
                    $form.find('.is-invalid').removeClass('is-invalid');
                    
                    $form.find('[data-required="true"], [required]').each(function() {
                        const $field = $(this);
                        let val = $field.val();
                        const $container = $field.closest('.draggable-field, .mb-3, .form-group');
                        let $errorMsg = $container.find('.error-msg');
                        
                        if ($errorMsg.length === 0) {
                            const labelText = $container.find('label').first().text().replace('*', '').trim() || 'This field';
                            $errorMsg = $(`
                                <div class="error-msg text-danger fw-semibold mb-1" style="display:none; font-size: 13px; padding-left: ${$container.hasClass('draggable-field') ? '30px' : '0'};">
                                    <i class="ti ti-alert-circle me-1"></i> <span class="error-text">${labelText} is required</span>
                                </div>
                            `);
                            $container.prepend($errorMsg);
                        }
                        
                        const fieldName = $container.find('label').first().text().replace('*', '').trim() || 'This field';
                        
                        let isFieldValid = true;
                        if ($field.attr('type') === 'radio') {
                            const name = $field.attr('name');
                            if (!$form.find(`input[name="${name}"]:checked`).length) isFieldValid = false;
                        } else if ($field.prop('tagName') === 'SELECT' || $field.hasClass('select')) {
                            if (!val || (Array.isArray(val) && val.length === 0)) isFieldValid = false;
                        } else if (!val || String(val).trim() === '') {
                            isFieldValid = false;
                        }
                        
                        if (!isFieldValid) {
                            isValid = false;
                            $field.addClass('is-invalid');
                            $errorMsg.find('.error-text').text(`${fieldName} is required`);
                            $errorMsg.fadeIn();
                        } else {
                            $errorMsg.hide();
                        }
                    });
                    
                    if (!isValid) {
                        const $firstError = $form.find('.error-msg:visible').first();
                        if ($firstError.length) $firstError[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return isValid;
                };

                // Form submission
                $('#leadsForm, #editForm').on('submit', function (e) {
                    e.preventDefault();
                    const $form = $(this);
                    if (!validateAnyForm($form)) return false;

                    const form = this;
                const formData = new FormData(form);
                const leadId = formData.get('id');
                const isEdit = !!leadId;
                const url = isEdit
                    ? "{{ route('leads.update', ['box_slug' => request()->route('box_slug')]) }}"
                    : "{{ route('leads.store', ['box_slug' => request()->route('box_slug')]) }}";

                if (formData.has('_method')) {
                    formData.delete('_method');
                }

                // Sanitize numeric fields - remove empty strings for numeric fields
                const numericFields = ['value', 'amount', 'assigned_to', 'country_id', 'state_id', 'city_id'];
                numericFields.forEach(field => {
                    if (formData.has(field) && formData.get(field) === '') {
                        formData.delete(field);
                    }
                });

                // Sanitize date fields
                if (formData.has('matured_date') && formData.get('matured_date') === '') {
                    formData.delete('matured_date');
                }

                // Debug: Log all form data including assigned_to
                const formDataObj = {};
                for (let [key, value] of formData.entries()) {
                    formDataObj[key] = value;
                }
                console.log('📤 Submitting form data:', formDataObj);
                console.log('🎯 assigned_to value:', formData.get('assigned_to'));
                console.log('Submitting to:', { url: url, method: 'POST', isEdit: isEdit, leadId: leadId });

                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.is_duplicate && response.duplicate_info) {
                            let info = response.duplicate_info;
                            let modalHtml = `
                                                    <div class="modal fade" id="duplicateAlertModal" tabindex="-1" aria-hidden="true">
                                                      <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content border-0 shadow">
                                                          <div class="modal-header bg-warning text-dark">
                                                            <h5 class="modal-title"><i class="ti ti-alert-triangle me-2"></i>Duplicate Lead Detected</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                          </div>
                                                          <div class="modal-body p-4">
                                                            <div class="alert alert-warning mb-4">
                                                                A new submission was saved, but this lead appears to be a duplicate. The new information has been appended to the existing lead's timeline.
                                                            </div>
                                                            <h6 class="fw-bold mb-3">Original Lead Details:</h6>
                                                            <div class="card bg-light border-0">
                                                                <div class="card-body">
                                                                    <div class="row mb-2">
                                                                        <div class="col-4 text-muted">Name</div>
                                                                        <div class="col-8 fw-semibold">${info.name}</div>
                                                                    </div>
                                                                    <div class="row mb-2">
                                                                        <div class="col-4 text-muted">Assigned To</div>
                                                                        <div class="col-8 fw-semibold">${info.assigned_to}</div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-4 text-muted">Lead Status</div>
                                                                        <div class="col-8"><span class="badge bg-primary">${info.lead_status}</span></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                          </div>
                                                          <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Acknowledge</button>
                                                          </div>
                                                        </div>
                                                      </div>
                                                    </div>`;

                            $('#duplicateAlertModal').remove();
                            $('body').append(modalHtml);
                            var duplicateModal = new bootstrap.Modal(document.getElementById('duplicateAlertModal'));
                            duplicateModal.show();
                        } else {
                            const message = 'Lead ' + (isEdit ? 'updated' : 'created') + ' successfully!';
                            showToast(message, 'success');
                        }
                        fetchLeadsList();

                        // Close the modal (Offcanvas)
                        const offcanvasElement = $(form).closest('.offcanvas')[0];
                        if (offcanvasElement) {
                            const canvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                            if (canvas) canvas.hide();
                        } else {
                            // Fallback
                            const canvasAdd = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvas_add'));
                            if (canvasAdd) canvasAdd.hide();
                            const canvasEdit = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvas_edit'));
                            if (canvasEdit) canvasEdit.hide();
                        }

                        form.reset();
                        const submitButton = form.querySelector('button[type="submit"]');
                        if (submitButton) {
                            submitButton.textContent = isEdit ? 'Update' : 'Create';
                        }
                        const idField = form.querySelector('[name="id"]');
                        if (idField) idField.remove();
                    },
                    error: function (xhr) {
                        let msg = 'Unknown error occurred.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        } else if (xhr.responseText) {
                            msg = xhr.responseText;
                        }
                        showToast('Error:\n' + msg, 'danger');
                    }
                });
            });

            // Dependent dropdown
            $(document).on('change', '.dynamic-select', function () {
                const selectedId = $(this).val();
                const dependentField = $(this).data('dependent');
                const token = $('meta[name="csrf-token"]').attr('content');
                const $form = $(this).closest('form');

                if (dependentField && selectedId) {
                    $.ajax({
                        url: "{{ route('gets.depenedent', ['box_slug' => request()->route('box_slug')]) }}",
                        method: "GET",
                        data: {
                            parent_field: $(this).attr('name').replace('[]', ''),
                            parent_id: selectedId,
                            _token: token
                        },
                        success: function (response) {
                            const $dependentSelect = $form.find('[name="' + dependentField + '"]');
                            $dependentSelect.empty().append('<option value="">Choose</option>');
                            if (response.data) {
                                $.each(response.data, function (key, value) {
                                    $dependentSelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                                });
                            }
                        }
                    });
                }
            });

            @if(isset($activeReport))
                var activeReport = @json($activeReport);
                if (activeReport) {
                    window.activeLeadFilter = activeReport.filters || [];
                    if (activeReport.columns && Object.keys(activeReport.columns).length > 0) {
                        columnPreferences = activeReport.columns;
                    }
                }
            @endif

                            // Fetch leads on page load
                            if ($("#leads_lists").length > 0) {
                fetchLeadsList();
            }

            // Show page after brief delay to avoid FOUC
            setTimeout(() => {
                $('.page-wrapper').addClass('loaded');
            }, 100);

            // Reset form when opening for new lead
            $(document).on('click', '[data-bs-target="#offcanvas_add"]', function () {
                const form = document.getElementById('leadsForm');
                if (form) {
                    form.reset();
                    const $form = $(form);
                    $form.find('.error-msg').hide();
                    $form.find('.is-invalid').removeClass('is-invalid');
                    
                    const submitButton = form.querySelector('button[type="submit"]');
                    if (submitButton) submitButton.textContent = 'Create';
                    const idField = form.querySelector('[name="id"]');
                    if (idField) idField.remove();
                }
            });

            // Clear validation errors on interaction
            $(document).on('input change', 'form [data-required="true"], form [required]', function() {
                const $field = $(this);
                let val = $field.val();
                
                if ($field.attr('type') === 'radio') {
                    const name = $field.attr('name');
                    if ($('input[name="' + name + '"]:checked').length > 0) {
                        $field.closest('.draggable-field, .mb-3, .form-group').find('.error-msg').fadeOut();
                    }
                } else if (val && (Array.isArray(val) ? val.length > 0 : String(val).trim() !== '')) {
                    $field.removeClass('is-invalid');
                    $field.closest('.draggable-field, .mb-3, .form-group').find('.error-msg').fadeOut();
                }
            });

            // Edit lead
            $(document).on('click', '.btn-edit-lead', function (e) {
                e.preventDefault();
                const leadId = $(this).data('lead-id') || $(this).attr('data-lead-id');

                if (!leadId) {
                    console.error('Lead ID not found');
                    showToast('Invalid lead data - ID not found', 'danger');
                    return;
                }

                ensureEditFormLoaded(function() {
                    $.ajax({
                        url: "{{ route('get.leads.details', ['box_slug' => request()->route('box_slug')]) }}",
                        method: 'GET',
                        data: { lead_id: leadId },
                        success: function (res) {
                            if (res.status && res.data && res.data.lead) {
                                populateEditForm(res.data);
                                const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('offcanvas_edit'));
                                offcanvas.show();
                            } else {
                                showToast(res.message || 'Failed to fetch lead details', 'danger');
                            }
                        },
                        error: function (xhr) {
                            let errorMsg = 'Failed to fetch lead details';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMsg = xhr.responseJSON.error;
                            }
                            showToast(errorMsg, 'danger');
                            console.error("Failed to fetch lead details:", xhr.responseText);
                        }
                    });
                });
            });

            // Populate edit form
            function populateEditForm(data) {
                const lead = data.lead || {};
                const custom = data.custom_data || {};
                const form = document.getElementById('editForm');
                if (!form) return;

                // Debug: Log the lead data to see assigned_to value
                console.log('📝 Populating edit form with lead data:', {
                    lead_id: lead.id,
                    assigned_to: lead.assigned_to,
                    lead_full: lead
                });

                // Field mappings: form field name => database column name
                // Complete mapping for all 28 system fields
                const fieldMappings = {
                    // Core identification fields
                    'name': 'name',
                    'email': 'email',
                    'sec_email': 'sec_email',
                    'phone': 'phone',
                    'sec_phone': 'sec_phone',
                    'phone_type': 'phone_type',

                    // Type and company fields
                    'lead_type': 'lead_type',
                    'company_name': 'company_name',
                    'designation': 'designation',

                    // Value fields
                    'amount': 'amount',
                    'currency': 'currency',

                    // Relationship fields (IDs)
                    'lead_status_id': 'lead_status_id',
                    'lead_sub_status_id': 'lead_sub_status_id',
                    'lead_source_id': 'lead_source_id',
                    'industry': 'industry',
                    'assigned_to': 'assigned_to',

                    // Location fields
                    'country_id': 'country_id',
                    'state_id': 'state_id',
                    'city_id': 'city_id',
                    'address': 'address',
                    'postal_code': 'postal_code',
                    'location': 'location',

                    // Additional info fields
                    'tags': 'tags',
                    'query': 'query',
                    'matured_date': 'matured_date',
                    'visibility': 'visibility',
                    'status': 'status',
                    'last_name': 'last_name'
                };

                // Reverse mappings: database column => form field name
                const reverseMappings = {};
                Object.keys(fieldMappings).forEach(function (k) {
                    reverseMappings[fieldMappings[k]] = k;
                });

                // Hidden ID field
                if (lead.id) {
                    let idField = form.querySelector('[name="id"]');
                    if (!idField) {
                        idField = document.createElement('input');
                        idField.type = 'hidden';
                        idField.name = 'id';
                        form.appendChild(idField);
                    }
                    idField.value = lead.id;
                }

                // Helper function to set field value based on field type
                function setFieldValue(field, value, fieldName = '') {
                    // Allow 0 as a valid value
                    if (!field || value === null || value === undefined || value === '') return;

                    const tag = field.tagName ? field.tagName.toLowerCase() : '';
                    const type = field.type ? field.type.toLowerCase() : '';
                    const name = field.name || field.getAttribute('name');

                    if (tag === 'select') {
                        // Special logging for assigned_to select
                        if (fieldName === 'assigned_to' || fieldName === 'owner_id' || field.multiple) {
                            console.log('🔧 Setting select field:', {
                                fieldName: fieldName,
                                value: value,
                                isMultiple: field.multiple,
                                fieldOptions: Array.from(field.options).map(opt => ({ value: opt.value, text: opt.text })),
                                currentValue: $(field).val()
                            });
                        }

                        if (field.multiple && !Array.isArray(value)) {
                            value = [value];
                        }

                        $(field).val(value).trigger('change');

                        // Verify it was set
                        if (fieldName === 'assigned_to') {
                            console.log('✅ After setting:', {
                                newValue: field.value,
                                selectedText: field.options[field.selectedIndex]?.text
                            });
                        }
                    } else if (tag === 'textarea') {
                        field.value = value || '';
                    } else if (type === 'radio') {
                        const radio = form.querySelector('input[name="' + name + '"][value="' + value + '"]');
                        if (radio) {
                            radio.checked = true;
                        }
                    } else if (type === 'checkbox') {
                        if (name && name.endsWith('[]')) {
                            const values = Array.isArray(value) ? value : (typeof value === 'string' ? value.split(',') : [value]);
                            form.querySelectorAll('input[name="' + name + '"]').forEach(cb => {
                                cb.checked = values.includes(cb.value);
                            });
                        } else {
                            field.checked = (value == 1 || value === true || value === '1' || value === 'true');
                        }
                    } else {
                        field.value = value || '';
                    }
                }

                // Populate standard mapped fields
                Object.keys(fieldMappings).forEach(function (formField) {
                    const dbField = fieldMappings[formField];
                    let value = lead[dbField];

                    // Special handling for relationship fields that might return objects
                    // assigned_to might be {id: 11, name: "John"} or just 11
                    if (formField === 'assigned_to' && value && typeof value === 'object' && value.id) {
                        value = value.id; // Extract the ID from the object
                    }

                    // Skip if value is null, undefined, or empty string
                    // But allow 0 as valid value
                    if (value === null || value === undefined || value === '') {
                        return;
                    }

                    const field = form.querySelector('[name="' + formField + '"]');
                    if (field) {
                        // Special logging for assigned_to
                        if (formField === 'assigned_to') {
                            console.log('⚙️ Preparing to set assigned_to:', {
                                formField: formField,
                                dbField: dbField,
                                originalValue: lead[dbField],
                                extractedValue: value,
                                assigneesRelation: lead.assignees || [],
                                valueType: typeof value,
                                fieldElement: field,
                                fieldType: field.tagName,
                                fieldName: field.name
                            });
                        }
                        setFieldValue(field, value, formField);
                    }
                });

                // 🆕 Special handling for Multiple Assignees (using the relationship data)
                if (lead.assignees && Array.isArray(lead.assignees) && lead.assignees.length > 0) {
                    const assigneeIds = lead.assignees.map(a => a.id);
                    const assignedField = form.querySelector('[name="assigned_to[]"]') || form.querySelector('[name="assigned_to"]');
                    if (assignedField) {
                        console.log('👥 Setting Multiple Assignees:', assigneeIds);
                        setFieldValue(assignedField, assigneeIds, 'assigned_to');
                    }

                    const ownerField = form.querySelector('[name="owner_id[]"]') || form.querySelector('[name="owner_id"]');
                    if (ownerField) {
                        setFieldValue(ownerField, assigneeIds, 'owner_id');
                    }
                } else if (lead.assigned_to) {
                    // Fallback for single assignee if relation is missing or empty
                    const assignedField = form.querySelector('[name="assigned_to[]"]') || form.querySelector('[name="assigned_to"]');
                    if (assignedField) {
                        setFieldValue(assignedField, [lead.assigned_to], 'assigned_to');
                    }
                    const ownerField = form.querySelector('[name="owner_id[]"]') || form.querySelector('[name="owner_id"]');
                    if (ownerField) {
                        setFieldValue(ownerField, [lead.assigned_to], 'owner_id');
                    }
                }

                // Note: All system fields are now handled in fieldMappings above
                // No need for separate systemFields loop anymore

                // Handle dependent dropdowns (country → state → city)
                const loadDependentData = function (parentField, parentId, dependentFieldName, callback) {
                    if (!parentId || !dependentFieldName) {
                        if (callback) callback();
                        return;
                    }
                    $.ajax({
                        url: "{{ route('gets.depenedent', ['box_slug' => request()->route('box_slug')]) }}",
                        method: "GET",
                        data: {
                            parent_field: parentField,
                            parent_id: parentId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            const depField = form.querySelector('[name="' + dependentFieldName + '"]');
                            if (depField && res.data) {
                                $(depField).empty().append('<option value="">Choose</option>');
                                $.each(res.data, function (k, v) {
                                    $(depField).append('<option value="' + v.id + '">' + v.name + '</option>');
                                });
                                // Set the value after populating options
                                if (dependentFieldName === 'state_id' && lead.state_id) {
                                    $(depField).val(lead.state_id).trigger('change');
                                } else if (dependentFieldName === 'city_id' && lead.city_id) {
                                    $(depField).val(lead.city_id).trigger('change');
                                }
                            }
                            if (callback) callback();
                        },
                        error: function () {
                            if (callback) callback();
                        }
                    });
                };

                // Load dependent dropdowns in sequence
                if (lead.country_id) {
                    const countryField = form.querySelector('[name="country_id"]');
                    if (countryField) {
                        setFieldValue(countryField, lead.country_id);
                        loadDependentData('country_id', lead.country_id, 'state_id', function () {
                            if (lead.state_id) {
                                const stateField = form.querySelector('[name="state_id"]');
                                if (stateField) {
                                    setFieldValue(stateField, lead.state_id);
                                    loadDependentData('state_id', lead.state_id, 'city_id', function () {
                                        if (lead.city_id) {
                                            const cityField = form.querySelector('[name="city_id"]');
                                            if (cityField) {
                                                setFieldValue(cityField, lead.city_id);
                                            }
                                        }
                                    });
                                }
                            }
                        });
                    }
                } else {
                    // If no country_id, still try to set state and city if they exist
                    if (lead.state_id) {
                        const stateField = form.querySelector('[name="state_id"]');
                        if (stateField) {
                            setFieldValue(stateField, lead.state_id);
                        }
                    }
                    if (lead.city_id) {
                        const cityField = form.querySelector('[name="city_id"]');
                        if (cityField) {
                            setFieldValue(cityField, lead.city_id);
                        }
                    }
                }

                // Load Lead Sub Status if Lead Status exists
                const leadStatusId = lead.lead_status_id;
                if (leadStatusId) {
                    const statusField = form.querySelector('[name="lead_status"]') || form.querySelector('[name="lead_status_id"]');
                    if (statusField) {
                        setFieldValue(statusField, leadStatusId);

                        const $subStatusSelect = $(form).find('#lead_sub_status_id');
                        const subStatusId = lead.lead_sub_status_id;

                        if ($subStatusSelect.length) {
                            $subStatusSelect.empty().append('<option value="">-- Select Sub Status --</option>');
                            $.ajax({
                                url: "{{ route('get.lead.substatus.list', ['box_slug' => request()->route('box_slug')]) }}",
                                type: 'GET',
                                data: { lead_status_id: leadStatusId },
                                success: function (response) {
                                    if (response.status && response.data && response.data.length > 0) {
                                        $.each(response.data, function (key, val) {
                                            $subStatusSelect.append('<option value="' + val.id + '">' + val.name + '</option>');
                                        });
                                        if (subStatusId) {
                                            $subStatusSelect.val(subStatusId);
                                        }
                                    }
                                }
                            });
                        }
                    }
                }

                // Populate custom fields from custom_data
                Object.keys(custom).forEach(function (key) {
                    if (custom[key] === null || custom[key] === undefined) return;

                    // Try to find field by exact name first
                    let field = form.querySelector('[name="' + key + '"]');

                    // If not found, try with array notation
                    if (!field) {
                        field = form.querySelector('[name="' + key + '[]"]');
                    }

                    if (field) {
                        setFieldValue(field, custom[key]);
                    }
                });

                // Populate any remaining lead fields that might not be mapped
                Object.keys(lead).forEach(function (key) {
                    // Skip already handled fields (check if key exists in fieldMappings values)
                    const isMappedField = Object.values(fieldMappings).includes(key);
                    if (fieldMappings[key] || isMappedField) return;

                    // Skip relationship objects and formatted dates
                    if (typeof lead[key] === 'object' && lead[key] !== null) return;
                    if (key.includes('_at') && key.startsWith('formatted_')) return;
                    if (key === 'id' || key === 'box_id' || key === 'created_at' || key === 'updated_at') return;

                    // Try to find and populate the field
                    const field = form.querySelector('[name="' + key + '"]');
                    if (field) {
                        setFieldValue(field, lead[key]);
                    }
                });

                // Update button text
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn && lead.id) {
                    submitBtn.textContent = 'Update';
                }
            }


            // Load preferences
            function loadColumnPreferences(callback) {
                $.get("{{ route('leads.column.preferences', ['box_slug' => request()->route('box_slug')]) }}", function (res) {
                    if (res.status && res.data) {
                        const data = Array.isArray(res.data) ? res.data : Object.values(res.data);
                        data.forEach(function (p) {
                            columnPreferences[p.column_name] = {
                                visible: p.visible == true || p.visible == 1 || p.visible === '1' || p.visible === 'true',
                                order: parseInt(p.order) || 0
                            };
                        });
                    }
                    if (callback) callback();
                }).fail(function () {
                    if (callback) callback();
                });
            }


            
            $(document).on('click', '#toggle-starred-view', function () {
                isStarredView = !isStarredView;
                isDuplicateView = false;
                isTrashView = false;
                isAllView = false;

                // Reset other buttons
                var trashBtn = $('#toggle-trash-view');
                trashBtn.removeClass('btn-danger').addClass('btn-light');
                trashBtn.html('<i class="ti ti-trash me-2"></i>Trash');

                var dupBtn = $('#toggle-duplicate-view');
                dupBtn.removeClass('btn-danger').addClass('btn-light');
                dupBtn.html('<i class="ti ti-copy me-2"></i>Duplicate');

                var allBtn = $('#toggle-all-view');
                allBtn.removeClass('btn-danger').addClass('btn-light');
                allBtn.html('<i class="ti ti-list me-2"></i>All');

                var btn = $(this);
                if (isStarredView) {
                    btn.removeClass('btn-light').addClass('btn-warning text-white');
                    btn.html('<i class="ti ti-arrow-back-up me-2"></i>Exit Starred');
                } else {
                    btn.removeClass('btn-warning text-white').addClass('btn-light');
                    btn.html('<i class="fa fa-star text-warning me-2"></i>Starred');
                }

                if (typeof fetchLeadsList === 'function') {
                    fetchLeadsList();
                }
            });

            $(document).on('click', '#toggle-trash-view', function () {
                isTrashView = !isTrashView;
                isDuplicateView = false; // Reset duplicate view
                isAllView = false; // Reset all view
                isStarredView = false;

                // Update Duplicate Button State
                var dupBtn = $('#toggle-duplicate-view');
                dupBtn.removeClass('btn-danger').addClass('btn-light');
                dupBtn.html('<i class="ti ti-copy me-2"></i>Duplicate');

                // Update Starred Button State
                var starBtn = $('#toggle-starred-view');
                starBtn.removeClass('btn-warning text-white').addClass('btn-light');
                starBtn.html('<i class="fa fa-star text-warning me-2"></i>Starred');

                // Update All Button State
                var allBtn = $('#toggle-all-view');
                allBtn.removeClass('btn-danger').addClass('btn-light');
                allBtn.html('<i class="ti ti-list me-2"></i>All');

                var btn = $(this);
                if (isTrashView) {
                    btn.removeClass('btn-light').addClass('btn-danger');
                    btn.html('<i class="ti ti-arrow-back-up me-2"></i>Exit Trash');
                } else {
                    btn.removeClass('btn-danger').addClass('btn-light');
                    btn.html('<i class="ti ti-trash me-2"></i>Trash');
                }

                if (typeof fetchLeadsList === 'function') {
                    fetchLeadsList();
                }
            });

            $(document).on('click', '#toggle-duplicate-view', function () {
                isDuplicateView = !isDuplicateView;
                isTrashView = false; // Reset trash view
                isAllView = false; // Reset all view
                isStarredView = false;

                // Update Trash Button State
                var trashBtn = $('#toggle-trash-view');
                trashBtn.removeClass('btn-danger').addClass('btn-light');
                trashBtn.html('<i class="ti ti-trash me-2"></i>Trash');

                // Update Starred Button State
                var starBtn = $('#toggle-starred-view');
                starBtn.removeClass('btn-warning text-white').addClass('btn-light');
                starBtn.html('<i class="fa fa-star text-warning me-2"></i>Starred');

                // Update All Button State
                var allBtn = $('#toggle-all-view');
                allBtn.removeClass('btn-danger').addClass('btn-light');
                allBtn.html('<i class="ti ti-list me-2"></i>All');

                var btn = $(this);
                if (isDuplicateView) {
                    btn.removeClass('btn-light').addClass('btn-danger');
                    btn.html('<i class="ti ti-arrow-back-up me-2"></i>Exit Duplicate');
                } else {
                    btn.removeClass('btn-danger').addClass('btn-light');
                    btn.html('<i class="ti ti-copy me-2"></i>Duplicate');
                }

                if (typeof fetchLeadsList === 'function') {
                    fetchLeadsList();
                }
            });

            $(document).on('click', '#toggle-all-view', function () {
                isAllView = !isAllView;
                isDuplicateView = false; // Reset duplicate view
                isTrashView = false; // Reset trash view
                isStarredView = false;

                // Reset other buttons
                var trashBtn = $('#toggle-trash-view');
                trashBtn.removeClass('btn-danger').addClass('btn-light');
                trashBtn.html('<i class="ti ti-trash me-2"></i>Trash');

                var dupBtn = $('#toggle-duplicate-view');
                dupBtn.removeClass('btn-danger').addClass('btn-light');
                dupBtn.html('<i class="ti ti-copy me-2"></i>Duplicate');

                var btn = $(this);
                if (isAllView) {
                    btn.removeClass('btn-light').addClass('btn-danger');
                    btn.html('<i class="ti ti-arrow-back-up me-2"></i>Exit All');
                } else {
                    btn.removeClass('btn-danger').addClass('btn-light');
                    btn.html('<i class="ti ti-list me-2"></i>All');
                }

                if (typeof fetchLeadsList === 'function') {
                    fetchLeadsList();
                }
            });

            var selectedTeamMemberIds = [];

            $(document).on('click', '.scope-filter', function (e) {
                e.preventDefault();
                var scope = $(this).data('scope');
                currentLeadScope = scope;

                // Update display text
                var displayText = 'All Leads';
                if (scope === 'all') {
                    selectedTeamMemberIds = [];
                }
                if (scope === 'team') {
                    displayText = 'My Team Leads';
                    fetchTeamMembers();
                }

                $('#scope-display').text(displayText);

                if (typeof fetchLeadsList === 'function') {
                    fetchLeadsList();
                }
            });

            function fetchTeamMembers() {
                const url = "{{ route('get.team.members', ['box_slug' => request()->route('box_slug')]) }}";
                $.get(url, function (response) {
                    if (response.status === 'success' && response.data) {
                        const container = $('#team_members_hierarchy');
                        container.empty();

                        if (response.data.length === 0) {
                            container.append('<div class="text-center p-3">No team members found</div>');
                        } else {
                            // 1. Build the tree
                            const rootBoxUserId = response.root_box_user_id;
                            const members = response.data;
                            const tree = [];
                            const mappedArr = {};

                            members.forEach(item => {
                                mappedArr[item.box_user_id] = { ...item, children: [] };
                            });

                            for (const id in mappedArr) {
                                if (mappedArr.hasOwnProperty(id)) {
                                    const mappedElem = mappedArr[id];
                                    if (mappedElem.parent_box_user_id && mappedArr[mappedElem.parent_box_user_id] && mappedElem.parent_box_user_id != rootBoxUserId) {
                                        mappedArr[mappedElem.parent_box_user_id].children.push(mappedElem);
                                    } else {
                                        tree.push(mappedElem);
                                    }
                                }
                            }

                            // 2. Render the tree
                            container.append(renderTeamTree(tree));
                        }
                        $('#team_members_modal').modal('show');
                    }
                });
            }

            function renderTeamTree(nodes) {
                let html = '<div class="team-tree-container">';
                nodes.forEach(node => {
                    var isChecked = selectedTeamMemberIds.includes(String(node.id)) ? 'checked' : '';
                    html += '<div class="team-tree-branch">';
                    html += '<div class="team-member-item" data-id="' + node.id + '" data-box-user-id="' + node.box_user_id + '">';
                    html += '<input type="checkbox" class="team-member-checkbox" value="' + node.id + '" ' + isChecked + '>';
                    html += '<div class="member-info">';
                    html += '<span class="member-name">' + node.name + '</span>';
                    html += '<span class="member-email">' + node.email + '</span>';
                    html += '</div>';
                    html += '</div>';

                    if (node.children && node.children.length > 0) {
                        html += '<div class="team-tree-node">';
                        html += renderTeamTree(node.children);
                        html += '</div>';
                    }
                    html += '</div>';
                });
                html += '</div>';
                return html;
            }

            // Optional: Select children when parent is selected
            $(document).on('change', '.team-member-checkbox', function () {
                const isChecked = $(this).prop('checked');
                $(this).closest('.team-tree-branch').find('.team-tree-node .team-member-checkbox').prop('checked', isChecked);
            });


            $(document).on('click', '#apply_team_member_filter', function () {
                selectedTeamMemberIds = $('.team-member-checkbox:checked').map(function () {
                    return $(this).val();
                }).get();
                if (typeof fetchLeadsList === 'function') {
                    fetchLeadsList();
                }
                $('#team_members_modal').modal('hide');
            });

            $(document).on('change', '#select_all_team_members', function () {
                $('.team-member-checkbox').prop('checked', $(this).prop('checked'));
            });


                        // Row Click Handler to make entire row clickable
            $(document).on('click', '#leads_lists tbody tr', function (e) {
                // 1. Prevent redirection if user is selecting text (copying)
                if (window.getSelection().toString().length > 0) {
                    return;
                }

                // 2. Prevent redirection if clicking on checkbox, buttons, or existing links (DIRECT click on them)
                if ($(e.target).closest('input[type="checkbox"], .checkboxs, .checkmarks, button, a, .dropdown-toggle, .set-star').length > 0) {
                    return;
                }

                // 3. User Restriction: "redirect when click on highlighted"
                // Only redirect if the clicked element is within our clickable span
                var $clickable = $(e.target).closest('.lead-clickable-content');

                if ($clickable.length > 0) {
                    // Fetch data from DataTable to avoid issues when columns are hidden
                    const table = $('#leads_lists').DataTable();
                    const rowData = table.row(this).data();
                    
                    if (rowData) {
                        const leadId = rowData.id || rowData.lead_id;
                        if (leadId) {
                            let detailsUrl = "{{ route('LeadsDetailsPage', ['box_slug' => request()->route('box_slug')]) }}?lead_id=" + leadId;
                            const urlParams = new URLSearchParams(window.location.search);
                            if (urlParams.has('bulk_upload_id')) {
                                detailsUrl += "&bulk_upload_id=" + urlParams.get('bulk_upload_id');
                            }
                            window.location.href = detailsUrl;
                        }
                    }
                }
            });


            // Fetch & render table
            function fetchLeadsList() {
                var canDeleteLead = @json(\App\Helpers\PermissionHelper::canAccessRoute('leads.bulk.delete'));

                // Clear selection on refresh
                if (window.selectedLeadIds) window.selectedLeadIds.clear();
                toggleBulkActions();

                const url = "{{ route('get.leads', ['box_slug' => request()->route('box_slug')]) }}";
                loadColumnPreferences(function () {
                    $.post(url, { _token: '{{ csrf_token() }}', draw: 1, start: 0, length: 1 }, function (res) {
                        const customFieldObjs = @json($customFields->map(function ($f) {
                            return ['key' => $f->col_name, 'label' => $f->name];
                        }));
                        const customKeys = customFieldObjs.map(function (o) { return o.key; });

                        const baseHeaders = [
                            { title: '<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>', columnName: "checkbox", data: null, defaultContent: "", render: function () { return '<label class="checkboxs"><input type="checkbox" class="lead-checkbox"><span class="checkmarks"></span></label>'; }, orderable: false, visible: true },
                            { title: "", columnName: "star", data: null, defaultContent: "", render: function (d, t, r) { 
                                const isStarred = r && r.is_starred ? 'filled text-warning' : '';
                                return '<div class="set-star rating-select"><i class="fa fa-star ' + isStarred + '"></i></div>'; 
                            }, orderable: false, visible: true },
                            {
                                title: "Name", columnName: "name", data: "name", render: function (d, t, r) {
                                    const leadId = (r && r.id) ? r.id : ((r && r.lead_id) ? r.lead_id : '');
                                    let detailsUrl = leadId ? "{{ route('LeadsDetailsPage', ['box_slug' => request()->route('box_slug')]) }}?lead_id=" + leadId : "#";
                                    const urlParams = new URLSearchParams(window.location.search);
                                    if (urlParams.has('bulk_upload_id')) {
                                        detailsUrl += "&bulk_upload_id=" + urlParams.get('bulk_upload_id');
                                    }
                                    let badges = '';
                                    if (r.deleted_at) {
                                        badges += '<span class="badge bg-danger ms-2">Trashed</span>';
                                    }
                                    if (r.duplicate_leads == 1) {
                                        badges += '<span class="badge bg-warning ms-2">Duplicate</span>';
                                    }
                                    return '<a href="' + detailsUrl + '" class="title-name">' + (r.name || d || '--') + '</a>' + badges;
                                }, visible: true
                            },
                            { title: "Email", columnName: "email", data: "email", render: function (d, t, r) { return r.email || d || '--'; }, visible: true },
                            { title: "Sec Email", columnName: "sec_email", data: "sec_email", render: function (d, t, r) { return r.sec_email || d || '--'; }, visible: false },
                            {
                                title: "Phone", columnName: "phone", data: "phone", render: function (d, t, r) {
                                    var phone = r.phone || d || '--';
                                    if (phone !== '--') {
                                        var cleanPhone = phone.replace(/[^\d+]/g, '');
                                        var waPhone = cleanPhone.replace('+', '');
                                        return '<span>' + phone + '</span> ' +
                                            '<a href="https://wa.me/' + waPhone + '" target="_blank" aria-label="WhatsApp" class="ms-2 text-success" title="WhatsApp">' +
                                            '<i class="fa-brands fa-whatsapp fs-16"></i></a> ' +
                                            '<a href="tel:' + cleanPhone + '" aria-label="Call" class="ms-1 text-primary" title="Call">' +
                                            '<i class="fa-solid fa-phone fs-16"></i></a>';
                                    }
                                    return phone;
                                }, visible: true
                            },
                            { title: "Secondary Phone", columnName: "sec_phone", data: "sec_phone", render: function (d, t, r) { return r.sec_phone || d || '--'; }, visible: false },
                            { title: "Phone Type", columnName: "phone_type", data: "phone_type", render: function (d, t, r) { return r.phone_type || d || '--'; }, visible: false },
                            { title: "Lead Type", columnName: "lead_type", data: "lead_type", render: function (d, t, r) { return r.lead_type || d || '--'; }, visible: false },
                            { title: "Company Name", columnName: "company_name", data: "company_name", render: function (d, t, r) { return r.company_name || d || '--'; }, visible: true },
                            { title: "Designation", columnName: "designation", data: "designation", render: function (d, t, r) { return r.designation || d || '--'; }, visible: false },
                            { title: "Currency", columnName: "currency", data: "currency", render: function (d, t, r) { return r.currency || d || '--'; }, visible: false },
                            { title: "Amount", columnName: "amount", data: "amount", render: function (d, t, r) { var val = (r.amount !== undefined && r.amount !== null) ? r.amount : d; return (val !== null && val !== undefined && val !== '') ? new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(val) : '--'; }, visible: true },
                            { title: "Last Received", columnName: "last_receipt_amount", data: "last_receipt_amount", render: function (d, t, r) { var val = (r.last_receipt_amount !== undefined && r.last_receipt_amount !== null) ? r.last_receipt_amount : d; return (val !== null && val !== undefined && val !== '') ? new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(val) : '--'; }, visible: true },
                            { title: "Total Amount", columnName: "receipts_sum_amount", data: "receipts_sum_amount", render: function (d, t, r) { var val = (r.receipts_sum_amount !== undefined && r.receipts_sum_amount !== null) ? r.receipts_sum_amount : d; return (val !== null && val !== undefined && val !== '') ? new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(val) : '--'; }, visible: true },
                            { title: "Last Receipt Date", columnName: "last_receipt_date", data: "last_receipt_date", render: function (d, t, r) { var val = r.last_receipt_date || d; if (!val || val === '--') return '--'; try { var date = new Date(val); return date.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' }); } catch(e) { return val; } }, visible: true },
                                {
                                title: "Lead Status", columnName: "lead_status_id", data: "lead_status", render: function (d, t, r) {
                                    const statusName = r.lead_status || d || '--';
                                    if (statusName !== '--') {
                                        console.log('Rendering status for lead ID:', r.id, 'Data received:', r);
                                        const bgColor = r.lead_status_color || '#6c757d'; // default secondary
                                        return '<span class="badge" style="background-color: ' + bgColor + '; color: #fff;">' + escapeHtml(statusName) + '</span>';
                                    }
                                    return statusName;
                                }, visible: true
                            },
                            {
                                title: "Lead Sub Status", columnName: "lead_sub_status_id", data: "lead_sub_status", render: function (d, t, r) {
                                    return r.lead_sub_status || d || '--';
                                }, visible: true
                            },
                            {
                                title: "Lead Source", columnName: "lead_source_id", data: "source", render: function (d, t, r) {
                                    return r.source || d || '--';
                                }, visible: false
                            },
                            {
                                title: "Industry", columnName: "industry", data: "industry", render: function (d, t, r) {
                                    if (r.industry_relation && r.industry_relation.name) {
                                        return r.industry_relation.name;
                                    }
                                    if (r.industryRelation && r.industryRelation.name) {
                                        return r.industryRelation.name;
                                    }
                                    return r.industry || d || '--';
                                }, visible: false
                            },
                            {
                                title: "Assignment Type", columnName: "assignment_type", data: "assignment_type", render: function (d) { return d ? d.charAt(0).toUpperCase() + d.slice(1) : "--"; }, visible: false },
                            { title: "Assigned To", columnName: "assigned_to", data: "assigned_to", render: function (d, t, r) {
                                    var assigneesList = [];
                                    if (r.assignees && Array.isArray(r.assignees)) {
                                        assigneesList = r.assignees;
                                    } else if (r.assignees && typeof r.assignees === 'object') {
                                        assigneesList = Object.values(r.assignees);
                                    }

                                    if (assigneesList.length > 0) {
                                        let badges = assigneesList.map(function (user) {
                                            return '<span class="badge bg-soft-primary text-primary me-1">' + escapeHtml(user.name) + '</span>';
                                        }).join('');
                                        return badges;
                                    }

                                    let assignedName = null;
                                    if (r.assigned_to && typeof r.assigned_to === 'object' && r.assigned_to.name) {
                                        assignedName = r.assigned_to.name;
                                    } else if (r.assignedTo && typeof r.assignedTo === 'object' && r.assignedTo.name) {
                                        assignedName = r.assignedTo.name;
                                    } else if (r.assigned_to_name) {
                                        assignedName = r.assigned_to_name;
                                    } else if (r.assigned_to && typeof r.assigned_to === 'string' && isNaN(Number(r.assigned_to))) {
                                        assignedName = r.assigned_to;
                                    }

                                    if (assignedName) {
                                        return '<span class="badge bg-soft-primary text-primary me-1">' + escapeHtml(assignedName) + '</span>';
                                    }

                                    if (r.assigned_to && !isNaN(Number(r.assigned_to))) {
                                        return '<span class="badge bg-soft-secondary text-secondary me-1">User ID: ' + escapeHtml(String(r.assigned_to)) + '</span>';
                                    }

                                    return '<span class="badge bg-soft-danger text-danger me-1">Unassigned</span>';
                                }, visible: true
                            },
                            {
                                title: "Assigned History", columnName: "assigned_history", data: "assigned_history", render: function (d, t, r) {
                                    if (r.assigned_history && Array.isArray(r.assigned_history) && r.assigned_history.length > 0) {
                                        return r.assigned_history.map(function (name) {
                                            return '<span class="badge bg-soft-info text-info me-1">' + escapeHtml(name) + '</span>';
                                        }).join('');
                                    }
                                    return '--';
                                }, visible: true
                            },
                            {
                                title: "Last Assigned To", columnName: "last_assigned_to", data: "last_assigned_to", render: function (d, t, r) {
                                    return r.last_assigned_to || d || '--';
                                }, visible: true
                            },
                            {
                                title: "Last Lead Status", columnName: "last_lead_status", data: "last_lead_status", render: function (d, t, r) {
                                    const statusName = r.last_lead_status || d || '--';
                                    if (statusName !== '--') {
                                        const bgColor = r.last_lead_status_color || '#6c757d'; // default secondary
                                        return '<span class="badge" style="background-color: ' + bgColor + '; color: #fff;">' + escapeHtml(statusName) + '</span>';
                                    }
                                    return statusName;
                                }, visible: true
                            },
                            { title: "Lead Status Color", columnName: "lead_status_color", data: "lead_status_color", visible: false, searchable: false },
                            { title: "Last Lead Status Color", columnName: "last_lead_status_color", data: "last_lead_status_color", visible: false, searchable: false },
                            {
                                title: "Country", columnName: "country_id", data: "country_id", render: function (d, t, r) {
                                    if (r.country && r.country.name) {
                                        return r.country.name;
                                    }
                                    return r.country_id || d || '--';
                                }, visible: false
                            },
                            {
                                title: "State", columnName: "state_id", data: "state_id", render: function (d, t, r) {
                                    if (r.state && r.state.name) {
                                        return r.state.name;
                                    }
                                    return r.state_id || d || '--';
                                }, visible: false
                            },
                            {
                                title: "City", columnName: "city_id", data: "city_id", render: function (d, t, r) {
                                    if (r.city && r.city.name) {
                                        return r.city.name;
                                    }
                                    return r.city_id || d || '--';
                                }, visible: false
                            },
                            { title: "Address", columnName: "address", data: "address", render: function (d, t, r) { return r.address || d || '--'; }, visible: false },
                            { title: "Postal Code", columnName: "postal_code", data: "postal_code", render: function (d, t, r) { return r.postal_code || d || '--'; }, visible: false },
                            { title: "Location", columnName: "location", data: "location", render: function (d, t, r) { return r.location || d || '--'; }, visible: false },
                            {
                                title: "Working Location", columnName: "working_location", data: "division_areas_count", render: function (d, t, r) {
                                    const leadId = (r && r.id) ? r.id : ((r && r.lead_id) ? r.lead_id : '');
                                    const count = r.division_areas_count || 0;
                                    const badgeClass = count > 0 ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary';
                                    return '<a href="javascript:void(0);" class="btn btn-sm btn-working-location ' + badgeClass + '" data-lead-id="' + leadId + '"><i class="ti ti-map-pin me-1"></i>Working Location (' + count + ')</a>';
                                }, visible: true
                            },
                            { title: "Tags", columnName: "tags", data: "tags", render: function (d, t, r) { return r.tags || d || '--'; }, visible: false },
                            {
                                title: "Query", columnName: "query", data: "query", render: function (d, t, r) {
                                    const desc = r.query || d || '';
                                    return desc.length > 50 ? desc.substring(0, 50) + '...' : (desc || '--');
                                }, visible: false
                            },
                            { title: "Matured Date", columnName: "matured_date", data: "matured_date", render: function (d, t, r) { return r.matured_date || d || '--'; }, visible: false },
                            { title: "Visibility", columnName: "visibility", data: "visibility", render: function (d, t, r) { return r.visibility || d || '--'; }, visible: false },

                            { title: "GSTIN", columnName: "gstin_number", data: "gstin_number", render: function (d, t, r) { return r.gstin_number || d || '--'; }, visible: false },
                            { title: "PAN", columnName: "pan_number", data: "pan_number", render: function (d, t, r) { return r.pan_number || d || '--'; }, visible: false },
                            { title: "Query", columnName: "query", data: "query", render: function (d, t, r) { 
                                var text = r.query || d || ''; 
                                if (!text) return '--';
                                var words = text.split(/\s+/);
                                if (words.length > 10) {
                                    var truncated = words.slice(0, 10).join(' ') + '...';
                                    var escapedTruncated = $('<div>').text(truncated).html().replace(/\n/g, '<br>');
                                    var escapedFull = $('<div>').text(text).html().replace(/\n/g, '<br>');
                                    return '<div style=""><span class="query-truncated">' + escapedTruncated + ' <a href="javascript:void(0);" class="text-primary fw-bold ms-1" onclick="showFullQuery(this)" data-full-query="' + encodeURIComponent(escapedFull) + '">More</a></span></div>';
                                }
                                return '<div style="white-space: pre-wrap; word-break: break-word;">' + $('<div>').text(text).html().replace(/\n/g, '<br>') + '</div>';
                            }, visible: false },
                            { title: "Created From", columnName: "created_from", data: "created_from", render: function (d, t, r) { return r.created_from || d || '--'; }, visible: false },
                            { title: "Duplicate", columnName: "duplicate_leads", data: "duplicate_leads", render: function (d, t, r) { return r.duplicate_leads == 1 ? "Yes" : "No"; }, visible: false },
                            {
                                title: "ID / Action", columnName: "action", data: "id", render: function (d, t, r) {
                                    var id = r.id || d || '';
                                    if (!id) return '--';
                                    return '<div>' + id + '</div>' +
                                        '<div class="d-flex gap-1 mt-1">' +
                                        '<a href="javascript:void(0);" class="btn btn-sm btn-success text-white btn-quick-whatsapp p-1" data-lead-id="' + id + '" data-lead-name="' + escapeHtml(r.name || '') + '" data-lead-phone="' + escapeHtml(r.phone || '') + '" title="Send WhatsApp"><i class="ti ti-brand-whatsapp"></i></a>' +
                                        '<a href="#" class="btn btn-sm btn-info text-white btn-duplicate-lead p-1" data-lead-id="' + id + '" title="Duplicate"><i class="ti ti-copy"></i></a>' +
                                        '<a href="#" class="btn btn-sm btn-warning text-white btn-edit-lead p-1" data-lead-id="' + id + '" title="Edit"><i class="ti ti-edit"></i></a>' +
                                        (canDeleteLead ? '<a href="#" class="btn btn-sm btn-danger text-white btn-delete-lead p-1" data-bs-toggle="modal" data-bs-target="#delete_lead_modal" data-lead-id="' + id + '" title="Delete"><i class="ti ti-trash"></i></a>' : '') +
                                        '</div>';
                                }, visible: true
                            }
                        ];

                        const customHeaders = customFieldObjs.map(function (obj) {
                            return {
                                title: obj.label,
                                columnName: 'custom_' + obj.key,
                                data: "custom_data",
                                render: function (d) { return d && d[obj.key] ? d[obj.key] : "N/A"; },
                                visible: false
                            };
                        });

                        const actionHeaders = [
                            { title: "Created On", columnName: "created_at", data: "created_at", render: function (d, type) { if (!d) return '--'; if (type === 'sort' || type === 'type') return d; const dateObj = new Date(d); const pad = n => n < 10 ? '0' + n : n; return pad(dateObj.getDate()) + '-' + pad(dateObj.getMonth() + 1) + '-' + dateObj.getFullYear() + ' ' + dateObj.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }); }, visible: false },
                            { title: "Updated On", columnName: "updated_at", data: "updated_at", render: function (d, type) { if (!d) return '--'; if (type === 'sort' || type === 'type') return d; const dateObj = new Date(d); const pad = n => n < 10 ? '0' + n : n; return pad(dateObj.getDate()) + '-' + pad(dateObj.getMonth() + 1) + '-' + dateObj.getFullYear() + ' ' + dateObj.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }); }, visible: false },
                            { title: "Last Updated By", columnName: "last_updated_by", data: "last_updated_by", render: function (d, t, r) { return r.last_updated_by || d || '--'; }, visible: false },
                            {
                                title: "Assigned Time", columnName: "change_assigned_to_time", data: "change_assigned_to_time", render: function (d) {
                                    if (!d) return '--';
                                    const dateObj = new Date(d);
                                    function pad(n) { return n < 10 ? '0' + n : n; }
                                    // manual formatting to ensure DD-MM-YYYY
                                    const day = pad(dateObj.getDate());
                                    const month = pad(dateObj.getMonth() + 1);
                                    const year = dateObj.getFullYear();
                                    return day + '-' + month + '-' + year + ' ' + dateObj.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                                }, visible: true
                            },
                            {
                                title: "Status Time", columnName: "change_lead_status_time", data: "change_lead_status_time", render: function (d) {
                                    if (!d) return '--';
                                    const dateObj = new Date(d);
                                    function pad(n) { return n < 10 ? '0' + n : n; }
                                    const day = pad(dateObj.getDate());
                                    const month = pad(dateObj.getMonth() + 1);
                                    const year = dateObj.getFullYear();
                                    return day + '-' + month + '-' + year + ' ' + dateObj.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                                }, visible: true
                            },
                            /*
                            { title: "Owner", columnName: "owner", data: "assigned_to", render: function(d, t, r) {
                                if (r.assigned_to && r.assigned_to.name) {
                                    return r.assigned_to.name;
                                }
                                if (r.assignedTo && r.assignedTo.name) {
                                    return r.assignedTo.name;
                                }
                                return r.assigned_to_name || (r.assigned_to ? String(r.assigned_to) : "Unassigned");
                            }, visible: true }
                            */
                        ];

                        // Use a Map to prevent duplicates - key by columnName
                        const columnMap = new Map();

                        // First, add all base, custom, and action headers to the map
                        baseHeaders.forEach(function (col) {
                            columnMap.set(col.columnName, col);
                        });
                        customHeaders.forEach(function (col) {
                            columnMap.set(col.columnName, col);
                        });
                        actionHeaders.forEach(function (col) {
                            columnMap.set(col.columnName, col);
                        });

                        // Apply preferences - update existing columns or add missing ones
                        Object.keys(columnPreferences).forEach(function (name) {
                            if (columnMap.has(name)) {
                                // Column exists - update it with preferences
                                const existingCol = columnMap.get(name);
                                existingCol.visible = !!(columnPreferences[name] && columnPreferences[name].visible);
                                existingCol.savedOrder = (columnPreferences[name] && columnPreferences[name].order !== undefined) ? columnPreferences[name].order : (existingCol.savedOrder !== undefined ? existingCol.savedOrder : 999);
                            } else {
                                // Column doesn't exist - create new one only if it's a custom field
                                if (name.startsWith('custom_')) {
                                    var fieldName = name.replace('custom_', '');
                                    var title = fieldName.replace(/_/g, ' ').replace(/\b\w/g, function (l) { return l.toUpperCase(); });
                                    columnMap.set(name, {
                                        title: title,
                                        columnName: name,
                                        data: 'custom_data',
                                        render: function (d, t, r) {
                                            var customData = r.custom_data || {};
                                            return customData[fieldName] || '--';
                                        },
                                        visible: !!(columnPreferences[name] && columnPreferences[name].visible),
                                        savedOrder: (columnPreferences[name] && columnPreferences[name].order !== undefined) ? columnPreferences[name].order : 999
                                    });
                                }
                            }
                        });

                        // Convert map back to array and apply default visibility/order for columns without preferences
                        allColumnDefinitions = Array.from(columnMap.values());
                        allColumnDefinitions.forEach(function (col, i) {
                            if (col.columnName === 'checkbox') {
                                col.savedOrder = -3;
                                col.visible = true; // Always visible
                            } else if (col.columnName === 'star') {
                                col.savedOrder = -2;
                                col.visible = true; // Always visible
                            } else if (col.columnName === 'action') {
                                col.savedOrder = -1;
                                col.visible = true; // Always visible
                            } else if (col.columnName === 'lead_id') {
                                col.savedOrder = 0;
                                col.visible = true;
                            } else if (columnPreferences[col.columnName]) {
                                // Already updated above
                            } else {
                                col.savedOrder = (col.savedOrder !== undefined) ? col.savedOrder : i;
                                col.visible = col.visible !== undefined ? col.visible : (['checkbox', 'star', 'name', 'email', 'phone', 'company_name'].indexOf(col.columnName) !== -1);
                            }
                        });

                        allColumnDefinitions.sort(function (a, b) {
                            let orderA = 999;
                            if (a.savedOrder !== undefined && a.savedOrder !== null && a.savedOrder !== '') {
                                const parsed = parseInt(a.savedOrder, 10);
                                if (!isNaN(parsed)) orderA = parsed;
                            }
                            let orderB = 999;
                            if (b.savedOrder !== undefined && b.savedOrder !== null && b.savedOrder !== '') {
                                const parsed = parseInt(b.savedOrder, 10);
                                if (!isNaN(parsed)) orderB = parsed;
                            }
                            return orderA - orderB;
                        });

                        // Expose columns for filter modal
                        window.availableLeadColumns = allColumnDefinitions;
                        const visible = allColumnDefinitions.filter(function (c) {
                            return c.visible || c.columnName === 'lead_status_color' || c.columnName === 'last_lead_status_color';
                        });

                        const tableCols = visible.map(function (c) {
                            var result = {};
                            for (var key in c) {
                                if (key !== 'columnName' && key !== 'savedOrder') {
                                    result[key] = c[key];
                                }
                            }
                            
                            // Explicitly set 'name' to the columnName so backend DataTables can use it for sorting
                            if (c.columnName) {
                                // Map 'action' column to 'id' for correct sorting in the database
                                result.name = c.columnName === 'action' ? 'id' : c.columnName;
                            }
                            
                            // Ensure orderable is explicitly true if not defined as false
                            if (result.orderable !== false) {
                                result.orderable = true;
                            }

                            return result;
                        });

                        // Validate columns before proceeding
                        if (!tableCols || tableCols.length === 0) {
                            console.error('No columns to display');
                            return;
                        }

                        // Ensure table structure exists
                        const $table = $("#leads_lists");
                        const $thead = $table.find('thead');
                        const $tbody = $table.find('tbody');

                        if ($thead.length === 0 || $tbody.length === 0) {
                            console.error('Table structure is invalid');
                            return;
                        }

                        $("#leads_head_row").empty();
                        visible.forEach(function (c) {
                            $("#leads_head_row").append("<th>" + (c.title || "") + "</th>");
                        });
                            // Function to toggle truncated text inline
                            window.toggleText = function(btn, e) {
                                if (e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                }
                                var $btn = $(btn);
                                var $wrapper = $btn.closest('.text-truncate-wrapper');
                                var $truncated = $wrapper.find('.truncated-text');
                                var $full = $wrapper.find('.full-text');
                                
                                if ($full.hasClass('d-none')) {
                                    $full.removeClass('d-none');
                                    $truncated.addClass('d-none');
                                    $btn.text('Show Less');
                                } else {
                                    $full.addClass('d-none');
                                    $truncated.removeClass('d-none');
                                    $btn.text('Load More');
                                }
                            };

                            // Function to initialize DataTable
                            // data: optional array of data for client-side mode
                            function initDataTable(data) {
                            // Properly destroy existing DataTable instance
                            if ($.fn.DataTable.isDataTable("#leads_lists")) {
                                $("#leads_lists").DataTable().clear().destroy();
                                $table.empty(); // Clear content
                                $table.append('<thead><tr id="leads_head_row"></tr></thead><tbody></tbody>');
                                $("#leads_head_row").empty();
                                visible.forEach(function (c) {
                                    $("#leads_head_row").append("<th>" + (c.title || "") + "</th>");
                                });
                            }

                            // Determine mode
                            const isClientSide = Array.isArray(data);

                            // Improved error handling for DataTables
                            $.fn.dataTable.ext.errMode = 'none'; // Prevent alert box
                            $("#leads_lists").on('error.dt', function (e, settings, techNote, message) {
                                console.error('DataTables Warning:', message);
                                console.error('Server Response:', settings.jqXHR ? settings.jqXHR.responseText : 'No response from server');

                                // Check if unauthorized (401)
                                if (settings.jqXHR && settings.jqXHR.status === 401) {
                                    window.location.reload();
                                }
                            });

                            // Intercept DataTables AJAX requests
                            $("#leads_lists").off('preXhr.dt').on('preXhr.dt', function (e, settings, data) {
                                // If user selected "Custom" (value -1), DataTables tries to fetch ALL records.
                                // We intercept this and force it to fetch only 10 to prevent server crash/hang
                                // while the Custom Pagination Modal opens.
                                if (data.length === -1) {
                                    console.log('🛑 Intercepted "All Records" request. Forcing length=10 to save server.');
                                    data.length = 10;
                                }
                            });

                            leadsTableInstance = $("#leads_lists").DataTable({
                                processing: true,
                                serverSide: !isClientSide,
                                data: isClientSide ? data : null,
                                ajax: isClientSide ? null : {
                                    url: url,
                                    type: "POST",
                                    data: function (d) {
                                        d._token = '{{ csrf_token() }}';
                                        d.trashed = typeof isTrashView !== 'undefined' ? isTrashView : false;
                                        d.starred = typeof isStarredView !== 'undefined' ? isStarredView : false;
                                        d.duplicate = typeof isDuplicateView !== 'undefined' ? isDuplicateView : false;
                                        d.all = typeof isAllView !== 'undefined' ? isAllView : false;
                                        d.lead_scope = currentLeadScope;
                                        d.team_member_ids = selectedTeamMemberIds;
                                        d.advanced_filter = window.activeLeadFilter || [];
                                        d.date_range = $('#leadsDateFilter').val() || '';

                                        // Extract bulk_upload_id if present in URL
                                        const urlParams = new URLSearchParams(window.location.search);
                                        if (urlParams.has('bulk_upload_id')) {
                                            d.bulk_upload_id = urlParams.get('bulk_upload_id');
                                        }
                                    },
                                    dataSrc: function (json) {
                                        if (json.data && window.optimisticRemovedIds && window.optimisticRemovedIds.size > 0) {
                                            return json.data.filter(function (item) {
                                                var id = item.id || item.lead_id;
                                                return !window.optimisticRemovedIds.has(String(id));
                                            });
                                        }
                                        return json.data;
                                    }
                                },
                                                                columns: tableCols,
                                drawCallback: function(settings) {
                                    var api = this.api();
                                    $("#leads_total_amount_display").remove();
                                },
                                autoWidth: true,
                                scrollX: true, // Enable horizontal scrolling natively in DataTables
                                ordering: true,
                                pageLength: isClientSide ? 10 : 10, // Default page length
                                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "Custom"]],
                                rowCallback: function (row, data) {
                                    var id = data.id || data.lead_id;
                                    if (id && (window.selectedLeadIds.has(id) || window.selectedLeadIds.has(String(id)) || window.selectedLeadIds.has(parseInt(id)))) {
                                        $(row).find('.lead-checkbox').prop('checked', true);
                                    }
                                    $('td', row).each(function () {
                                        var $cell = $(this);
                                        
                                        // Skip checkbox column entirely
                                        if ($cell.find('input[type="checkbox"]').length > 0) return;

                                        var text = $cell.text().trim();
                                        var emptyValues = ['', '--', '-', '—', 'N/A', 'n/a'];
                                        if (emptyValues.includes(text)) return;

                                        if ($cell.find('input, button, a, .dropdown').length > 0) {
                                            // Cell has interactive elements. We only wrap the raw text, plain spans, or plain divs.
                                            
                                            // Wrap raw text nodes (if any exist directly inside the td)
                                            $cell.contents().filter(function() {
                                                return this.nodeType === 3 && this.textContent.trim().length > 0; 
                                            }).wrap('<span class="lead-clickable-content"></span>');
                                            
                                            // Target Phone column (plain span)
                                            $cell.children('span:not([class])').addClass('lead-clickable-content');
                                            
                                            // Target ID column (plain div)
                                            $cell.children('div:not([class])').addClass('lead-clickable-content');
                                        } else {
                                            // Standard text cell (e.g. Email)
                                            $cell.wrapInner('<span class="lead-clickable-content"></span>');
                                        }

                                        // Apply truncation to content exceeding 10 words
                                        $cell.find('.lead-clickable-content').each(function() {
                                            var $content = $(this);
                                            
                                            // Skip if it contains complex HTML tags to avoid breaking structure
                                            if ($content.find('input, button, a, select').length > 0) return;
                                            
                                            var fullText = $content.text().trim();
                                            var words = fullText.split(/\s+/);
                                            
                                            if (words.length > 10) {
                                                var truncatedText = words.slice(0, 10).join(' ') + '...';
                                                var escapedFull = $('<div>').text(fullText).html().replace(/\n/g, '<br>');
                                                var escapedTruncated = $('<div>').text(truncatedText).html().replace(/\n/g, ' ');

                                                var newHtml = '<div class="text-truncate-wrapper text-wrap" style="max-width: 300px;">' +
                                                              '<span class="truncated-text">' + escapedTruncated + '</span>' +
                                                              '<span class="full-text d-none">' + escapedFull + '</span>' +
                                                              '<a href="javascript:void(0);" class="text-primary fw-bold ms-1 toggle-more-text" onclick="window.toggleText(this, event)">Load More</a>' +
                                                              '</div>';
                                                $content.html(newHtml);
                                            }
                                        });
                                    });

                                        // Special case: Lead Name usually has an <a> tag. We might want to apply the class to that <a> or wrap it?
                                        // Existing render returns <a href...>. So wrapping it in span is fine, or adding class to A.
                                        // But Lead name A tag already has a link.
                                        // Previous render: <a href="..." class="title-name">...</a> via 'render' function.
                                        // Clicking that A tag works natively.
                                        // Highlighting: CSS targets .lead-clickable-content:hover
                                        // If we wrap the A tag in span, hovering span underlines the A tag text.
                                        // But A tags have their own styles.
                                        // User said "make like this all".
                                },
                                language: {
                                    search: "", sLengthMenu: "_MENU_", searchPlaceholder: "Search",
                                    info: "_START_ - _END_ of _TOTAL_ items",
                                    paginate: { next: 'Next <i class="fa fa-angle-right"></i>', previous: '<i class="fa fa-angle-left"></i> Prev' }
                                },
                                drawCallback: function (settings) {
                                    if (settings.json && settings.json.recordsTotal !== undefined) {
                                        $('.count-title').text(settings.json.recordsTotal);
                                    }
                                    if ($('.lead-checkbox').length > 0) {
                                        var allChecked = $('.lead-checkbox').length === $('.lead-checkbox:checked').length;
                                        $('#select-all').prop('checked', allChecked);
                                    } else {
                                        $('#select-all').prop('checked', false);
                                    }
                                    toggleBulkActions();
                                },
                                initComplete: function (settings, json) {
                                    $('.page-wrapper').addClass('loaded');
                                }
                            });

                            // Fallback to ensure the page unhides if Datatables fails
                            setTimeout(function () {
                                $('.page-wrapper').addClass('loaded');
                            }, 1000);

                            // Handle Custom Length Selection
                            $('#leads_lists_length select').off('change.custom').on('change.custom', function () {
                                var val = $(this).val();
                                if (val == -1) {
                                    // Show Custom Modal
                                    $('#custom_pagination_modal').modal('show');
                                    // Reset select to 10 temporarily so it doesn't stay on "Custom" visually if cancelled
                                    // But DataTables re-renders.
                                }
                            });
                        }

                        // Expose init function globally
                        window.initLeadsDataTable = initDataTable;

                        // Initial Load (Server Side)
                        initDataTable(null);


                        // Make table rows clickable to redirect to leads details page
                        /*
                        // Remove duplicate/loose row click handler to enforce strict highlighting click only.
                        // The strict handler is defined earlier in the file.
                        */

                        // Add cursor pointer style ONLY to clickable elements (handled via CSS now)
                        $('#leads_lists tbody').css('cursor', 'pointer');

                    });
                });
            }


            // --- Custom Pagination Logic ---

            // Handle Custom Pagination Submit
            // Handle Custom Pagination Submit
            $(document).on('submit', '#customPaginationForm', function (e) {
                e.preventDefault();

                // Use relative find to avoid duplicate ID collision if valid
                var $input = $(this).find('#custom_page_length');
                if ($input.length === 0) $input = $(this).find('input[type="number"]'); // Fallback

                var valStr = $input.val();
                var limit = parseInt(valStr);

                console.log('Custom Pagination Submit:', { val: valStr, parsed: limit });

                if (isNaN(limit) || limit <= 0) {
                    alert('Please enter a valid number.');
                    return;
                }

                $('#custom_pagination_modal').modal('hide');

                if (limit > 500) {
                    console.log('Limit > 500, starting Batch Fetch');
                    startBatchFetch(limit);
                } else {
                    // Just update standard DataTable length
                    console.log('Limit <= 500, updating DataTable directly');
                    if (leadsTableInstance) {
                        leadsTableInstance.page.len(limit).draw();
                    }
                }
            });

            var batchTimerInterval;
            var batchStartTime;

            function startBatchFetch(totalLimit) {
                $('#batch_progress_modal .modal-title').text('Loading Heavy Data');
                $('#batch_progress_modal').modal('show');
                $('#batch_progress_bar').css('width', '1%').text('0%');
                $('#batch_status_text').text('Initializing fetch...');

                // Timer
                var batchStartTime = Date.now();
                if (typeof batchTimerInterval !== 'undefined') clearInterval(batchTimerInterval);
                batchTimerInterval = setInterval(function () {
                    var now = Date.now();
                    var diff = Math.floor((now - batchStartTime) / 1000);
                    var m = Math.floor(diff / 60);
                    var s = diff % 60;
                    $('#batch_timer').text((m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s);
                }, 1000);

                // Recursive Fetch Configuration
                var chunkSize = 100; // Reduced to 100 as requested for lighter load
                var totalFetched = 0;
                var allLeads = [];

                function fetchChunk(startInfo) {
                    var currentStart = startInfo;

                    // Update progress
                    var pct = Math.round((totalFetched / totalLimit) * 100);
                    if (pct > 99) pct = 99; // Hold at 99 until done
                    $('#batch_progress_bar').css('width', pct + '%').text(pct + '%');
                    $('#batch_status_text').text('Fetching records ' + (currentStart + 1) + ' to ' + Math.min(currentStart + chunkSize, totalLimit) + '...');

                    var requestData = {
                        _token: '{{ csrf_token() }}',
                        batch_mode: 1, // Trigger backend optimization
                        start: currentStart,
                        length: chunkSize,
                        trashed: typeof isTrashView !== 'undefined' ? isTrashView : false,
                        starred: typeof isStarredView !== 'undefined' ? isStarredView : false,
                        duplicate: typeof isDuplicateView !== 'undefined' ? isDuplicateView : false,
                        all: typeof isAllView !== 'undefined' ? isAllView : false,
                        lead_scope: currentLeadScope,
                        team_member_ids: selectedTeamMemberIds,
                        advanced_filter: window.activeLeadFilter || []
                    };
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('bulk_upload_id')) {
                        requestData.bulk_upload_id = urlParams.get('bulk_upload_id');
                    }

                    $.ajax({
                        url: "{{ route('get.leads', ['box_slug' => request()->route('box_slug')]) }}",
                        type: "POST",
                        data: requestData,
                        success: function (response) {
                            if (response.status === 'success' && response.data) {
                                var chunkData = response.data;
                                allLeads = allLeads.concat(chunkData);
                                totalFetched += chunkData.length;

                                // Check if we need more
                                if (totalFetched < totalLimit && chunkData.length > 0) {
                                    // Small delay to let UI breathe
                                    setTimeout(function () {
                                        fetchChunk(totalFetched);
                                    }, 50);
                                } else {
                                    // Done
                                    clearInterval(batchTimerInterval);
                                    $('#batch_progress_bar').css('width', '100%').text('100%');
                                    $('#batch_status_text').text('Download complete. Rendering ' + allLeads.length + ' records...');
                                    finishFetchJob(allLeads);
                                }
                            } else {
                                handleFetchError('Invalid response from server during batch.');
                            }
                        },
                        error: function (xhr) {
                            handleFetchError('Chunk fetch failed: ' + (xhr.responseJSON?.message || xhr.statusText));
                        }
                    });
                }

                // Start first chunk
                fetchChunk(0);
            }

            function pollJobStatus(jobId) {
                // Construct URL
                var url = "{{ route('get.leads.job.status', ['box_slug' => request()->route('box_slug'), 'jobId' => 'JOB_ID']) }}";
                url = url.replace('JOB_ID', jobId);

                var pollInterval = setInterval(function () {
                    $.ajax({
                        url: url,
                        type: "GET",
                        success: function (response) {
                            if (response.status === 'processing') {
                                // Fake progress up to 90%
                                var currentWidth = parseFloat($('#batch_progress_bar').css('width')) / $('#batch_progress_bar').parent().width() * 100;
                                if (currentWidth < 90) {
                                    var newWidth = currentWidth + 2;
                                    $('#batch_progress_bar').css('width', newWidth + '%').text(Math.round(newWidth) + '%');
                                }
                            } else if (response.status === 'error') {
                                clearInterval(pollInterval);
                                handleFetchError(response.message);
                            } else if (response.data) {
                                // Done! data is available
                                clearInterval(pollInterval);
                                $('#batch_progress_bar').css('width', '100%').text('100%');
                                $('#batch_status_text').text('Download complete. Rendering...');
                                finishFetchJob(response.data);
                            } else {
                                // Logic fallback for data array
                                if (Array.isArray(response)) {
                                    clearInterval(pollInterval);
                                    $('#batch_progress_bar').css('width', '100%').text('100%');
                                    finishFetchJob(response);
                                }
                            }
                        },
                        error: function (xhr) {
                            // Check if 500 error with message
                            var msg = 'Network error while polling';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            } else if (xhr.status === 500) {
                                msg = 'Server Error (500). Job may have failed.';
                            }

                            // Specific check for job failure from cache
                            if (xhr.responseJSON && xhr.responseJSON.status === 'error') {
                                msg = xhr.responseJSON.message;
                            }

                            clearInterval(pollInterval);
                            handleFetchError(msg);
                        }
                    });
                }, 2000); // Poll every 2 seconds
            }

            function handleFetchError(msg) {
                clearInterval(batchTimerInterval);
                $('#batch_status_text').addClass('text-danger').text('Error: ' + msg);
                alert('Data Fetch Error: ' + msg);
                $('#batch_progress_modal').modal('hide');
                $('#batch_status_text').removeClass('text-danger'); // reset
            }

            function finishFetchJob(data) {
                clearInterval(batchTimerInterval);

                // Small delay to let user see 100%
                setTimeout(function () {
                    $('#batch_progress_modal').modal('hide');

                    // Render asynchronously to allow modal to close first
                    setTimeout(function () {
                        if (typeof window.initLeadsDataTable === 'function') {
                            // Pass data for Client-Side mode
                            window.initLeadsDataTable(data);
                        } else {
                            console.error('initLeadsDataTable is not defined');
                        }

                        if (data && data.length) {
                            showToast('Successfully loaded ' + data.length + ' records.', 'success');
                        } else {
                            showToast('Loaded records.', 'success');
                        }
                    }, 300);

                }, 500);
            }

            // Populate Manage Columns Modal
            function populateColumnsModal() {
                const columnsList = $('#columns_list');

                // Destroy existing Sortable instance if it exists
                if (columnsSortableInstance) {
                    try {
                        columnsSortableInstance.destroy();
                    } catch (e) {
                        console.warn('Error destroying Sortable instance:', e);
                    }
                    columnsSortableInstance = null;
                }

                // Remove all event handlers to prevent duplicates
                columnsList.off('mousedown click');

                columnsList.empty();

                // Fetch all columns from preferences API
                $.ajax({
                    url: "{{ route('leads.column.preferences', ['box_slug' => request()->route('box_slug')]) }}",
                    method: 'GET',
                    cache: false, // Prevent caching
                    data: { _t: new Date().getTime() }, // Cache-busting parameter
                    success: function (response) {
                        if (response.status && response.data) {
                            let preferences = Array.isArray(response.data) ? response.data : Object.values(response.data);

                            // Merge with in-memory preferences if they exist (prioritize in-memory for latest state)
                            if (Object.keys(columnPreferences).length > 0) {
                                preferences = preferences.map(function (pref) {
                                    // If in-memory has this column, use in-memory data
                                    if (columnPreferences[pref.column_name]) {
                                        return {
                                            column_name: pref.column_name,
                                            display_name: pref.display_name,
                                            visible: columnPreferences[pref.column_name].visible,
                                            order: columnPreferences[pref.column_name].order
                                        };
                                    }
                                    return pref;
                                });
                            }

                            // Sort by order
                            preferences.sort(function (a, b) {
                                return (parseInt(a.order) || 0) - (parseInt(b.order) || 0);
                            });

                            // Filter out Owner, Value, and Status columns as per request
                            preferences = preferences.filter(function (pref) {
                                const hiddenColumns = ['value', 'owner', 'owner_id', 'status', 'checkbox', 'star', 'action', 'description'];
                                return !hiddenColumns.includes(pref.column_name);
                            });

                            preferences.forEach(function (pref) {
                                // Use in-memory preference if available, otherwise use server data
                                const memPref = columnPreferences[pref.column_name];
                                const isVisible = memPref ? memPref.visible : (pref.visible === true || pref.visible === 1 || pref.visible === '1' || pref.visible === 'true');

                                let displayTitle = pref.display_name || pref.column_name.replace(/_/g, ' ').replace(/\b\w/g, function (l) { return l.toUpperCase(); });

                                // Rename specific columns to remove "Id" suffix
                                if (pref.column_name === 'lead_source_id') displayTitle = 'Lead Source';
                                else if (pref.column_name === 'country_id') displayTitle = 'Country';
                                else if (pref.column_name === 'state_id') displayTitle = 'State';
                                else if (pref.column_name === 'city_id') displayTitle = 'City';
                                else if (pref.column_name === 'lead_status_id') displayTitle = 'Lead Status';
                                // Generic fallback for any other Id columns if needed
                                else if (displayTitle.endsWith(' Id')) {
                                    displayTitle = displayTitle.substring(0, displayTitle.length - 3);
                                }

                                const checkboxId = 'toggle_' + pref.column_name.replace(/[^a-zA-Z0-9]/g, '_');
                                const columnItem = $('<div class="list-group-item d-flex align-items-center justify-content-between" data-column-name="' + pref.column_name + '" data-order="' + (parseInt(pref.order) || 0) + '">' +
                                    '<div class="d-flex align-items-center flex-grow-1" style="cursor: move;">' +
                                    '<i class="ti ti-grip-vertical me-2 text-muted" style="cursor: move;"></i>' +
                                    '<span class="fw-medium">' + displayTitle + '</span>' +
                                    '</div>' +
                                    '<div class="status-toggle" style="position: relative; z-index: 1000; pointer-events: auto; cursor: pointer;">' +
                                    '<input type="checkbox" class="check column-visibility-toggle" id="' + checkboxId + '" ' + (isVisible ? 'checked' : '') + ' data-column="' + pref.column_name + '" style="pointer-events: auto; cursor: pointer; position: relative; z-index: 1001;">' +
                                    '<label class="checktoggle" for="' + checkboxId + '" style="cursor: pointer; pointer-events: auto; position: relative; z-index: 1001;"></label>' +
                                    '</div>' +
                                    '</div>');
                                columnsList.append(columnItem);
                            });

                            // Make list sortable (drag and drop) using SortableJS - exclude toggle area completely
                            if (typeof Sortable !== 'undefined') {
                                columnsSortableInstance = new Sortable(columnsList[0], {
                                    handle: '.ti-grip-vertical',
                                    animation: 150,
                                    filter: '.status-toggle, .status-toggle *, .column-visibility-toggle, .checktoggle',
                                    preventOnFilter: true,
                                    onEnd: function (evt) {
                                        // Update order attributes
                                        columnsList.find('.list-group-item').each(function (index) {
                                            $(this).attr('data-order', index);
                                        });
                                    }
                                });
                            }

                            // Use event delegation on the container (works even after DOM changes)
                            // Remove old handlers first to prevent duplicates
                            columnsList.off('mousedown.click-handler click.click-handler');

                            // Use mousedown to catch clicks before sortable interferes
                            columnsList.on('mousedown.click-handler', '.status-toggle, .column-visibility-toggle, .checktoggle', function (e) {
                                e.stopPropagation();
                                e.preventDefault();
                                return false;
                            });

                            // Handle actual toggle on click - use event delegation
                            columnsList.on('click.click-handler', '.status-toggle', function (e) {
                                e.stopPropagation();
                                e.preventDefault();
                                const checkbox = $(this).find('.column-visibility-toggle');
                                if (checkbox.length) {
                                    const currentState = checkbox.prop('checked');
                                    checkbox.prop('checked', !currentState);
                                    checkbox.trigger('change');
                                }
                                return false;
                            });

                            columnsList.on('click.click-handler', '.column-visibility-toggle', function (e) {
                                e.stopPropagation();
                                // Let browser handle the checkbox toggle naturally
                            });

                            columnsList.on('click.click-handler', '.checktoggle', function (e) {
                                e.stopPropagation();
                                e.preventDefault();
                                const checkbox = $(this).siblings('.column-visibility-toggle').first();
                                if (checkbox.length) {
                                    const currentState = checkbox.prop('checked');
                                    checkbox.prop('checked', !currentState);
                                    checkbox.trigger('change');
                                }
                                return false;
                            });
                        } else {
                            columnsList.html('<div class="text-center text-muted p-3">No columns found</div>');
                        }
                    },
                    error: function (xhr) {
                        columnsList.html('<div class="text-center text-danger p-3">Failed to load columns</div>');
                        console.error('Failed to load column preferences:', xhr.responseText);
                    }
                });
            }

            // Open Manage Columns Modal
            $('#manage_columns').on('show.bs.modal', function () {
                populateColumnsModal();
            });

            // Fix aria-hidden warning by blurring focused element before modal hides
            $('#manage_columns').on('hide.bs.modal', function () {
                const activeElement = document.activeElement;
                if (activeElement && $(activeElement).closest('#manage_columns').length) {
                    activeElement.blur();
                }
            });

            // Save Column Preferences
            $('#save_column_preferences').on('click', function () {
                const columns = [];
                $('#columns_list .list-group-item').each(function (index) {
                    const columnName = $(this).data('column-name');
                    const isVisible = $(this).find('.column-visibility-toggle').is(':checked');
                    columns.push({
                        column_name: columnName,
                        visible: isVisible === true || isVisible === 'true' || isVisible === 1,
                        order: parseInt(index)
                    });
                });

                // Update preferences in memory immediately for faster response
                columnPreferences = {};
                columns.forEach(function (col) {
                    columnPreferences[col.column_name] = {
                        visible: col.visible,
                        order: col.order
                    };
                });

                $.ajax({
                    url: "{{ route('leads.column.preferences.save', ['box_slug' => request()->route('box_slug')]) }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify({
                        columns: columns
                    }),
                    success: function (response) {
                        if (response.status) {
                            // Blur the save button before hiding modal to fix aria-hidden warning
                            $('#save_column_preferences').blur();

                            showToast('Column preferences saved successfully!', 'success');
                            $('#manage_columns').modal('hide');

                            // Reload preferences from server in background to keep in sync
                            loadColumnPreferences(function () {
                                // Reload table with updated preferences
                                fetchLeadsList();
                            });
                        } else {
                            // Revert preferences on error
                            loadColumnPreferences(function () { });
                            showToast(response.message || 'Failed to save column preferences.', 'danger');
                        }
                    },
                    error: function (xhr) {
                        // Revert preferences on error
                        loadColumnPreferences(function () { });
                        let errorMsg = 'Failed to save column preferences.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }
                        showToast(errorMsg, 'danger');
                    }
                });
            });

            // Toast
            function showToast(message, type) {
                type = type || 'success';
                const toastEl = document.getElementById('leadToast');
                const toastBody = document.getElementById('toastMessage');
                toastBody.textContent = message;
                toastEl.className = 'toast align-items-center text-bg-' + type + ' border-0';
                new bootstrap.Toast(toastEl).show();
            }

            // ====================================================================
            // Drag and Drop Functionality for Add and Edit Forms
            // ====================================================================

            let addFormSortable = null;
            let editFormSortable = null;

            /**
             * Initialize SortableJS for Add Lead Form
             */
            function initAddFormSortable() {
                const addContainer = document.getElementById('sortable-fields-add');

                if (addContainer && !addFormSortable) {
                    addFormSortable = new Sortable(addContainer, {
                        animation: 150,
                        handle: '.drag-handle',
                        ghostClass: 'sortable-ghost',
                        dragClass: 'sortable-drag',
                        chosenClass: 'sortable-chosen',
                        forceFallback: true,
                        fallbackClass: 'sortable-fallback',
                        fallbackOnBody: true,
                        swapThreshold: 0.65,

                        onStart: function (evt) {
                            evt.item.style.opacity = '0.5';
                        },

                        onEnd: function (evt) {
                            evt.item.style.opacity = '1';
                            evt.item.classList.add('drop-success');
                            setTimeout(() => {
                                evt.item.classList.remove('drop-success');
                            }, 500);

                            const newOrder = Array.from(addContainer.children).map((el, index) => ({
                                id: el.getAttribute('data-id'),
                                type: el.getAttribute('data-type'),
                                position: index + 1
                            }));

                            console.log('Add Form - New field order:', newOrder);
                            saveFieldOrder('add', newOrder);
                        }
                    });
                    console.log('✅ Add Form Sortable initialized');
                }
            }

            function initEditFormSortable() {
                const editContainer = document.getElementById('sortable-fields-edit');

                if (editContainer && !editFormSortable) {
                    editFormSortable = new Sortable(editContainer, {
                        animation: 150,
                        handle: '.drag-handle',
                        ghostClass: 'sortable-ghost',
                        dragClass: 'sortable-drag',
                        chosenClass: 'sortable-chosen',
                        forceFallback: true,
                        fallbackClass: 'sortable-fallback',
                        fallbackOnBody: true,
                        swapThreshold: 0.65,

                        onStart: function (evt) {
                            evt.item.style.opacity = '0.5';
                        },

                        onEnd: function (evt) {
                            evt.item.style.opacity = '1';
                            evt.item.classList.add('drop-success');
                            setTimeout(() => {
                                evt.item.classList.remove('drop-success');
                            }, 500);

                            const newOrder = Array.from(editContainer.children).map((el, index) => ({
                                id: el.getAttribute('data-id'),
                                type: el.getAttribute('data-type'),
                                position: index + 1
                            }));

                            console.log('Edit Form - New field order:', newOrder);
                            saveFieldOrder('edit', newOrder);
                        }
                    });
                    console.log('✅ Edit Form Sortable initialized');
                }
            }

            function saveFieldOrder(formType, order) {
                const fields = order.map(item => ({
                    id: parseInt(item.id),
                    sequence: item.position,
                    type: item.type
                }));

                $.ajax({
                    url: "{{ route('admin.system-fields.reorder', ['box_slug' => request()->route('box_slug')]) }}",
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        fields: fields
                    },
                    success: function (response) {
                        console.log('✅ Field order saved successfully:', response);
                        if (response.status) {
                            showToast('Field order saved successfully', 'success');
                        } else {
                            showToast(response.message || 'Failed to save field order', 'warning');
                        }
                    },
                    error: function (xhr) {
                        console.error('❌ Failed to save field order:', xhr);
                        let errorMsg = 'Failed to save field order';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        showToast(errorMsg, 'danger');
                    }
                });
            }

            // Initialize sortable when Add Lead offcanvas is shown
            $('#offcanvas_add').on('shown.bs.offcanvas', function () {
                setTimeout(function () {
                    initAddFormSortable();
                    initOwnerSelects();
                }, 100);
            });

            // Initialize sortable when Edit Lead offcanvas is shown
            $('#offcanvas_edit').on('shown.bs.offcanvas', function () {
                setTimeout(function () {
                    initEditFormSortable();
                    initOwnerSelects();
                }, 100);
            });

            // Destroy instances when offcanvas is hidden to prevent memory leaks
            $('#offcanvas_add').on('hidden.bs.offcanvas', function () {
                if (addFormSortable) {
                    addFormSortable.destroy();
                    addFormSortable = null;
                    console.log('🗑️ Add Form Sortable destroyed');
                }
            });

            $('#offcanvas_edit').on('hidden.bs.offcanvas', function () {
                if (editFormSortable) {
                    editFormSortable.destroy();
                    editFormSortable = null;
                    console.log('🗑️ Edit Form Sortable destroyed');
                }
            });

            // Initialize on page load if offcanvas is already open
            $(document).ready(function () {
                if ($('#offcanvas_add').hasClass('show')) {
                    initAddFormSortable();
                }
                if ($('#offcanvas_edit').hasClass('show')) {
                    initEditFormSortable();
                }
            });
            // Select All Checkbox Logic
            // Select All Checkbox Logic
            // Manual Merge Handler (Checkbox Triggered)
            $(document).on('change', '#chk_merge_manual', function () {
                if ($(this).is(':checked')) {
                    var selectedIds = Array.from(window.selectedLeadIds || []);
                    if (selectedIds.length === 0) {
                        showToast('Please select at least one lead to merge.', 'warning');
                        $(this).prop('checked', false);
                        return;
                    }

                    // Populate hidden input and open modal
                    $('#merge_source_ids').val(selectedIds.join(','));
                    $('#modal_merge_target_id').val('');
                    $('#manual_merge_modal').modal('show');

                    // Uncheck after opening modal to allow re-triggering if needed
                    $(this).prop('checked', false);
                }
            });

            // Handle Manual Merge Form Submission
            $('#manualMergeForm').on('submit', function (e) {
                e.preventDefault();
                var targetId = $('#modal_merge_target_id').val();
                var sourceIds = $('#merge_source_ids').val();

                if (!confirm('Are you sure you want to merge selected lead(s) into Target Lead ID ' + targetId + '? This action is permanent.')) {
                    return;
                }

                var $btn = $(this).find('button[type="submit"]');
                var oldText = $btn.text();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Merging...');

                $.ajax({
                    url: "{{ route('leads.merge_duplicates', ['box_slug' => request()->route('box_slug')]) }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        target_lead_id: targetId,
                        lead_ids: sourceIds
                    },
                    success: function (res) {
                        if (res.status) {
                            showToast(res.message, 'success');
                            $('#manual_merge_modal').modal('hide');
                            if (window.selectedLeadIds) window.selectedLeadIds.clear();
                            $('.lead-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            toggleBulkActions();
                            fetchLeadsList();
                        } else {
                            showToast(res.message || 'Merge failed.', 'danger');
                        }
                    },
                    error: function (xhr) {
                        showToast(xhr.responseJSON?.message || 'Error occurred during merge.', 'danger');
                    },
                    complete: function () {
                        $btn.prop('disabled', false).text(oldText);
                    }
                });
            });

            function toggleBulkActions() {
                var count = window.selectedLeadIds ? window.selectedLeadIds.size : 0;
                // Adjust for double storage (string+number) if we do that, but let's stick to handling one type or Set handles uniqueness of primitives.
                // Actually Set treats "1" and 1 as different. I will normalize to String everywhere to be safe.
                // But for now, let's just use the set size.

                // Improve count accuracy: Filter the set for unique values if we store mixed types, 
                // or just ensure we only store Strings. I will enforce String storage.

                if (count > 0) {
                    $('#bulk-action-container').show();
                    // Show Restore option only if in Trash View
                    if (typeof isTrashView !== 'undefined' && isTrashView) {
                        $('#li_restore').show();
                    } else {
                        $('#li_restore').hide();
                    }

                    // Show Merge option if at least ONE lead is selected
                    if (count >= 1 && (!isTrashView)) {
                        $('#li_merge').show();
                    } else {
                        $('#li_merge').hide();
                    }
                } else {
                    $('#bulk-action-container').hide();
                    $('#li_merge').hide();
                }
            }

            $(document).on('change', '#select-all', function () {
                var isChecked = $(this).prop('checked');

                if (leadsTableInstance) {
                    var rows = leadsTableInstance.rows({ filter: 'applied' }).data();
                    rows.each(function (data) {
                        var id = data.id || data.lead_id;
                        if (id) {
                            // Normalize to String to avoid duplicates (1 vs "1")
                            var strId = String(id);
                            if (isChecked) {
                                window.selectedLeadIds.add(strId);
                            } else {
                                window.selectedLeadIds.delete(strId);
                            }
                        }
                    });
                }

                $('.lead-checkbox').prop('checked', isChecked);
                toggleBulkActions();
            });

            // Reset optimistic blacklist when switching views to ensure we see fresh data eventually
            $(document).on('click', '#toggle-starred-view, #toggle-trash-view, #toggle-duplicate-view, #toggle-all-view', function () {
                if (window.optimisticRemovedIds) {
                    window.optimisticRemovedIds.clear();
                }
            });

            $(document).on('change', '.lead-checkbox', function () {
                var tr = $(this).closest('tr');
                if (leadsTableInstance) {
                    var row = leadsTableInstance.row(tr);
                    var data = row.data();
                    if (data) {
                        var id = data.id || data.lead_id;
                        if (id) {
                            var strId = String(id);
                            if ($(this).prop('checked')) {
                                window.selectedLeadIds.add(strId);
                            } else {
                                window.selectedLeadIds.delete(strId);
                            }
                        }
                    }
                }

                if ($('.lead-checkbox').length > 0) {
                    var allChecked = $('.lead-checkbox').length === $('.lead-checkbox:checked').length;
                    $('#select-all').prop('checked', allChecked);
                }
                toggleBulkActions();
            });



            // Duplicate Lead Logic
            $(document).on('click', '.btn-duplicate-lead', function (e) {
                e.preventDefault();

                // Get row data
                var tr = $(this).closest('tr');
                var row = leadsTableInstance.row(tr);
                var rowData = row.data();

                if (!rowData) {
                    console.warn('No row data found for duplicate');
                    return;
                }

                var leadId = rowData.id || rowData.lead_id;
                $('#duplicate_lead_id').val(leadId);

                // Populate Options from Bulk Selects (robustness against undefined Blade variables)
                if (window.allAssignOwners) {
                    $('#duplicate_assigned_to').empty().append(window.allAssignOwners.clone());
                } else if ($('#bulk_assigned_to').length) {
                    $('#duplicate_assigned_to').html($('#bulk_assigned_to').html());
                }

                // Reset/Populate filtering selects
                $('#duplicate_department').val('').trigger('change.select2');
                $('#duplicate_designation').val('');
                $('#duplicate_designation_visible').val('');

                if (Object.keys(window.departmentOptions || {}).length > 0) {
                    var $deptSelect = $('#duplicate_department');
                    $deptSelect.empty().append('<option value="">Select Department</option>');
                    for (var id in window.departmentOptions) {
                        $deptSelect.append('<option value="' + id + '">' + window.departmentOptions[id] + '</option>');
                    }
                    $deptSelect.trigger('change.select2');
                } else if (typeof window.loadDepartments === 'function') {
                    window.loadDepartments();
                }
                if ($('#bulk_lead_source').length) {
                    $('#duplicate_lead_source').html($('#bulk_lead_source').html());
                    if (!$('#duplicate_lead_source option[value=""]').length) {
                        $('#duplicate_lead_source').prepend('<option value="">Select Lead Source</option>');
                    }
                }

                // Reset form fields
                $('#duplicate_assigned_to').val('');
                $('#duplicate_lead_source').val('');

                // Open Modal
                $('#duplicate_lead_modal').modal('show');
            });

            // Handle Duplicate Form Submission
            $('#duplicateLeadForm').on('submit', function (e) {
                e.preventDefault();

                var form = $(this);
                var submitBtn = form.find('button[type="submit"]');
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('leads.duplicate', ['box_slug' => request()->route('box_slug')]) }}",
                    method: 'POST',
                    data: form.serialize() + "&_token={{ csrf_token() }}",
                    success: function (response) {
                        if (response.status) {
                            showToast(response.message, 'success');
                            $('#duplicate_lead_modal').modal('hide');
                            if (typeof fetchLeadsList === 'function') {
                                fetchLeadsList(); // Reload table data via AJAX
                            } else {
                                location.reload(); // Fallback
                            }
                        } else {
                            showToast(response.message || 'Failed to duplicate lead.', 'danger');
                        }
                    },
                    error: function (xhr) {
                        var msg = 'An error occurred.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showToast(msg, 'danger');
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false);
                    }
                });
            });

            // Bulk Assign Logic
            $(document).on('click', '.btn-bulk-assign', function () {
                $('#assign_to_modal').modal('show');
            });

            $('#bulkAssignForm').on('submit', function (e) {
                e.preventDefault();
                const $form = $(this);
                if (!validateAnyForm($form)) return false;

                var selectedIds = [];
                if (window.selectedLeadIds && window.selectedLeadIds.size > 0) {
                    window.selectedLeadIds.forEach(function (id) {
                        selectedIds.push(String(id));
                    });
                    // Deduplicate
                    selectedIds = [...new Set(selectedIds)];
                } else {
                    // Fallback
                    $('.lead-checkbox:checked').each(function () {
                        var tr = $(this).closest('tr');
                        if (leadsTableInstance) {
                            var row = leadsTableInstance.row(tr);
                            var data = row.data();
                            if (data && (data.id || data.lead_id)) {
                                selectedIds.push(data.id || data.lead_id);
                            }
                        }
                    });
                }

                if (selectedIds.length === 0) {
                    showToast('No leads selected.', 'warning');
                    return;
                }

                var assignedTo = $('#bulk_assigned_to').val();
                var url = "{{ route('leads.bulk.assign', ['box_slug' => request()->route('box_slug')]) }}";
                var data = {
                    _token: '{{ csrf_token() }}',
                    assigned_to: assignedTo
                };

                executeBulkAction(url, data, selectedIds, function (response) {
                    showToast(response.message, 'success');
                    $('#assign_to_modal').modal('hide');
                    $('#bulkAssignForm')[0].reset();

                    // Refresh table to show updated assignment
                    if (typeof fetchLeadsList === 'function') {
                        fetchLeadsList();
                    } else if (leadsTableInstance) {
                        leadsTableInstance.ajax.reload(null, false);
                    }

                    $('#bulk-action-container').hide();
                    $('#select-all').prop('checked', false);
                });
            });


            // Handler for Duplicate Lead Button
            $(document).on('click', '.btn-duplicate-lead', function (e) {
                e.preventDefault();
                var leadId = $(this).data('lead-id');

                // Show loader/spinner?
                // Reuse the edit logic to fetch data
                $.get("{{ route('get.leads.details', ['box_slug' => request()->route('box_slug')]) }}", { lead_id: leadId }, function (response) {
                    if (response.status && response.data) {
                        var lead = response.data;
                        var form = document.querySelector('#add_lead form');
                        if (!form) return;

                        // Reset form first
                        form.reset();
                        $(form).find('input[type="hidden"]').val(''); // Clear all hidden including IDs

                        // Populate form fields using existing helper or manual
                        // Reuse the populateLeadForm if available or similar logic from btn-edit-lead
                        // We'll manually call the logic used in 'btn-edit-lead' but adapted

                        // We need to access the populate logic. It was inside the edit handler.
                        // Let's copy/paste the populate logic or ideally refactor it.
                        // For now, I'll invoke the 'populateLeadForm' if I extracted it, but I implemented it inline in previous steps or steps I viewed.
                        // Wait, I saw 'setFieldValue' helper earlier.

                        // Re-implement basic population for duplicate
                        Object.keys(lead).forEach(function (key) {
                            if (key === 'id' || key === 'lead_id') return; // Skip ID to ensure new creation
                            if (key === 'created_at' || key === 'updated_at') return;

                            let val = lead[key];
                            if (key === 'name' || key === 'lead_name') val = val + ' (Copy)';

                            let input = form.querySelector('[name="' + key + '"]');
                            if (!input && key === 'name') input = form.querySelector('[name="lead_name"]'); // mapping

                            if (input) {
                                if (input.type === 'checkbox' || input.type === 'radio') {
                                    if (input.value == val) input.checked = true;
                                } else {
                                    input.value = val || '';
                                }
                            }
                        });

                        // Handle Custom Data
                        if (lead.custom_data) {
                            Object.keys(lead.custom_data).forEach(function (k) {
                                let input = form.querySelector('[name="custom_fields[' + k + ']"]'); // Assuming structure
                                if (!input) input = form.querySelector('[name="' + k + '"]'); // Fallback
                                if (input) input.value = lead.custom_data[k];
                            });
                        }

                        // Set Modal Title and Button
                        $('#add_status_label').text('Duplicate Lead');
                        $(form).find('button[type="submit"]').text('Create Copy');

                        // Open Modal
                        $('#add_lead').modal('show');
                    } else {
                        showToast('Failed to fetch lead details for duplication.', 'danger');
                    }
                });
            });

            // Handler for Delete Button (Action Column & ID Column)
            // Ensure the modal knows which ID to delete
            // Handler for Delete Button (Action Column & ID Column)
            // Uses the Bulk Trash/Delete Modal logic by simulating a single selection
            $(document).on('click', '.btn-delete-lead', function (e) {
                e.preventDefault();
                var leadId = $(this).data('lead-id');
                if (!leadId) return;

                // Clear previous selections to treat this as a single action
                if (window.selectedLeadIds) {
                    window.selectedLeadIds.clear();
                    window.selectedLeadIds.add(String(leadId));
                }
                $('.lead-checkbox').prop('checked', false);

                // Determine action based on view
                if (typeof isTrashView !== 'undefined' && isTrashView) {
                    trashActionUrl = "{{ route('leads.bulk.delete', ['box_slug' => request()->route('box_slug')]) }}";
                    $('#delete_confirm_modal .modal-body p').text('Are you sure you want to permanently delete this lead?');
                } else {
                    trashActionUrl = "{{ route('leads.bulk.trash', ['box_slug' => request()->route('box_slug')]) }}";
                    $('#delete_confirm_modal .modal-body p').text('Are you sure you want to move this lead to trash?');
                }

                $('#delete_confirm_modal').modal('show');
            });

            // Working Location Click Handler
            $(document).on('click', '.btn-working-location', function (e) {
                e.preventDefault();
                e.stopPropagation(); // Prevent row click redirect
                const leadId = $(this).data('lead-id');
                showWorkingLocations(leadId);
            });

            function showWorkingLocations(leadId) {
                const boxSlug = "{{ request()->route('box_slug') }}";
                const url = "{{ url('/') }}/" + boxSlug + "/activity-get-locations?lead_id=" + leadId;

                $('#working_locations_display_body').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
                $('#division_areas_list').html('<p class="text-center">Loading...</p>');
                $('#working_location_view_modal').modal('show');

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function (res) {
                        if (res.success && res.data && res.data.length > 0) {
                            let tableRows = '';
                            let divisions = new Set();

                            res.data.forEach(function (loc) {
                                const state = loc.state ? loc.state.name : (loc.state_id || '--');
                                const city = loc.city ? loc.city.name : (loc.city_id || '--');
                                const division = loc.division ? loc.division.name : (loc.division_id || '--');
                                const company = (loc.division && loc.division.company) ? loc.division.company.bill_name : '--';

                                tableRows += `<tr>
                                                    <td>${state}</td>
                                                    <td>${city}</td>
                                                    <td>${division}</td>
                                                    <td>${company}</td>
                                                </tr>`;

                                if (division && division !== '--') {
                                    divisions.add(division);
                                }
                            });

                            $('#working_locations_display_body').html(tableRows);

                            if (divisions.size > 0) {
                                let listHtml = '<ul class="mb-0">';
                                divisions.forEach(function (div) {
                                    listHtml += `<li>${div}</li>`;
                                });
                                listHtml += '</ul>';
                                $('#division_areas_list').html(listHtml);
                            } else {
                                $('#division_areas_list').html('<p class="text-muted mb-0">No divisions assigned.</p>');
                            }
                        } else {
                            $('#working_locations_display_body').html('<tr><td colspan="4" class="text-center">No working locations found.</td></tr>');
                            $('#division_areas_list').html('<p class="text-muted mb-0">No working locations found.</p>');
                        }
                    },
                    error: function () {
                        $('#working_locations_display_body').html('<tr><td colspan="4" class="text-center text-danger">Error fetching data.</td></tr>');
                        $('#division_areas_list').html('<p class="text-danger mb-0">Error fetching data.</p>');
                    }
                });
            }

            // Clean up modal text on hide
            $('#delete_confirm_modal').on('hidden.bs.modal', function () {
                // Reset text to default (bulk)
                $('#delete_confirm_modal .modal-body p').text('Are you sure you want to delete selected leads?');
                // Clear selection if it was a single action? 
                // Maybe better to leave it, as resetBulkChecks handles it on success.
            });
            // --- Bulk Action Multi-Select Logic ---

            // Prevent dropdown from closing on click
            $('#bulk-action-dropdown').on('click', function (e) {
                e.stopPropagation();
            });

            // Handle Checkbox Change
            $(document).on('change', '.bulk-action-check', function () {
                var $this = $(this);
                var isChecked = $this.is(':checked');
                var type = $this.val();

                if (type === 'whatsapp' && isChecked) {
                    if (window.selectedLeadIds.size === 0) {
                        showToast('Please select at least one lead.', 'warning');
                        $this.prop('checked', false);
                        return;
                    }
                    openWhatsAppCampaignModal();
                    $this.prop('checked', false);
                    return;
                }

                var checkedCount = $('.bulk-action-check:checked').length;

                // Logic for Merge Duplicate Leads Action
                if (type === 'merge' && isChecked) {
                    // Check if exactly one lead is selected
                    if (window.selectedLeadIds.size !== 1) {
                        showToast('Please select exactly one lead to merge duplicate leads into.', 'warning');
                        $this.prop('checked', false);
                        return;
                    }
                }

                if (checkedCount > 2) {
                    $this.prop('checked', false);
                    showToast('You can only select up to 2 actions.', 'warning');
                    return;
                }

                if (window.selectedLeadIds.size === 0) {
                    $this.prop('checked', false);
                    showToast('No leads selected.', 'warning');
                    return;
                }

                openMultiActionModal();
            });

            function openMultiActionModal() {
                var checked = $('.bulk-action-check:checked');
                if (checked.length === 0) {
                    $('#multi_action_modal').modal('hide'); // Close if no actions selected
                    return;
                }

                var hasAssign = false;
                checked.each(function () { if ($(this).val() === 'assign') hasAssign = true; });
                if (hasAssign && typeof window.loadDepartments === 'function' && Object.keys(window.departmentOptions || {}).length === 0) {
                    window.loadDepartments();
                } else if (hasAssign && Object.keys(window.departmentOptions || {}).length > 0) {
                    setTimeout(function () {
                        var select = $('#multi_assign_department');
                        if (select.length > 0) {
                            select.empty().append('<option value="">Select Department</option>');
                            for (var id in window.departmentOptions) {
                                select.append('<option value="' + id + '">' + window.departmentOptions[id] + '</option>');
                            }
                            select.trigger('change.select2');
                        }
                    }, 100);
                }

                // Snapshot IDs
                var leadIds = Array.from(window.selectedLeadIds).join(',');
                $('#multi_lead_ids').val(leadIds);

                var container = $('#multi_action_inputs');
                container.empty();

                checked.each(function () {
                    var type = $(this).val();
                    var html = '';
                    var selectId = '';

                    if (type === 'status') {
                        var source = $('#bulkUpdateStatusForm .mb-3');
                        if (source.length > 0) {
                            html = source.first().clone();
                            selectId = 'multi_bulk_status';
                            var select = html.find('select');

                            // Clean up Select2 artifacts
                            html.find('.select2-container').remove();
                            select.removeClass('select2-hidden-accessible').removeAttr('data-select2-id').removeAttr('aria-hidden').removeAttr('tabindex');
                            select.show(); // Ensure it's visible for re-init (though select2 hides it again)

                            select.attr('id', selectId).attr('name', 'multi_status');
                            html.find('label').text('Lead Status');

                            container.append('<div class="mb-3 action-group" data-type="status"></div>');
                            container.find('.action-group:last').append(html);
                        }

                    } else if (type === 'source') {
                        var source = $('#bulkUpdateSourceForm .mb-3');
                        if (source.length > 0) {
                            html = source.first().clone();
                            selectId = 'multi_bulk_source';
                            var select = html.find('select');

                            // Clean up Select2 artifacts
                            html.find('.select2-container').remove();
                            select.removeClass('select2-hidden-accessible').removeAttr('data-select2-id').removeAttr('aria-hidden').removeAttr('tabindex');
                            select.show();

                            select.attr('id', selectId).attr('name', 'multi_source');
                            html.find('label').text('Lead Source');
                            container.append('<div class="mb-3 action-group" data-type="source"></div>');
                            container.find('.action-group:last').append(html);
                        }

                    } else if (type === 'assign') {
                        var source = $('#bulkAssignForm').children('.mb-3').clone();
                        if (source.length > 0) {
                            container.append('<div class="mb-3 action-group border-bottom pb-2" data-type="assign"><h5>Lead Assignment</h5></div>');
                            var target = container.find('.action-group:last');

                            source.each(function () {
                                var field = $(this);
                                field.find('.select2-container').remove();
                                var select = field.find('select');
                                var input = field.find('input');
                                var label = field.find('label');
                                var button = field.find('button');

                                if (select.length) {
                                    select.removeClass('select2-hidden-accessible').removeAttr('data-select2-id').removeAttr('aria-hidden').removeAttr('tabindex').show();
                                    if (select.attr('id') === 'assign_department') {
                                        select.attr('id', 'multi_assign_department').attr('name', 'multi_department');
                                        label.attr('for', 'multi_assign_department');
                                    } else if (select.attr('id') === 'bulk_assigned_to') {
                                        select.attr('id', 'multi_bulk_assigned_to').attr('name', 'multi_assign');
                                        label.attr('for', 'multi_bulk_assigned_to');
                                    }
                                }
                                if (input.length) {
                                    if (input.attr('id') === 'assign_designation_visible') {
                                        input.attr('id', 'multi_assign_designation_visible');
                                        label.attr('for', 'multi_assign_designation_visible');
                                    } else if (input.attr('id') === 'assign_designation') {
                                        input.attr('id', 'multi_assign_designation');
                                    }
                                }
                                if (button.length) {
                                    var onclick = button.attr('onclick');
                                    if (onclick) {
                                        onclick = onclick.replace('#assign_designation', '#multi_assign_designation');
                                        onclick = onclick.replace('#assign_department', '#multi_assign_department');
                                        button.attr('onclick', onclick);
                                    }
                                }
                                target.append(field);
                            });
                        }

                    } else if (type === 'trash') {
                        container.append('<div class="mb-3 action-group" data-type="trash"><p class="text-danger fw-bold">Move selected leads to Trash?</p></div>');
                    } else if (type === 'restore') {
                        container.append('<div class="mb-3 action-group" data-type="restore"><p class="text-secondary fw-bold">Restore selected leads?</p></div>');
                    } else if (type === 'delete') {
                        container.append('<div class="mb-3 action-group" data-type="delete"><p class="text-danger fw-bold">Permanently Delete selected leads?</p></div>');
                    }
                });

                // Add class for non-blocking
                $('#multi_action_modal').addClass('non-blocking-modal');
                var modal = new bootstrap.Modal(document.getElementById('multi_action_modal'), {
                    backdrop: false
                });
                modal.show();

                // Re-initialize Select2 for any selects added
                container.find('select').each(function () {
                    $(this).select2({
                        dropdownParent: $('#multi_action_modal'),
                        width: '100%'
                    });
                });
            }

            // When modal is cancelled/closed, we should probably uncheck the boxes?
            // Or keep them? User said "when checkbox checked... open modal".
            // If I cancel, I probably want to uncheck everything?
            $('#multi_action_modal').on('hidden.bs.modal', function () {
                // Optional: Uncheck boxes if the user cancelled?
                // But if they just wanted to close and come back?
                // Let's safe side: Do NOT uncheck everything automatically unless submitted.
                // Wait, if I uncheck manually in the dropdown, the code above runs.
                // But if I close modal, the checkboxes remain checked.
            });

            // Uncheck all on success/reset
            function resetBulkChecks() {
                $('.bulk-action-check').prop('checked', false);
            }

            // --- Batch Processing Utility for Bulk Actions ---
            function executeBulkAction(url, data, leadIds, successCallback, finallyCallback) {
                var idsArray = [];
                if (Array.isArray(leadIds)) {
                    idsArray = leadIds;
                } else if (typeof leadIds === 'string') {
                    idsArray = leadIds.split(',').filter(Boolean);
                }

                var total = idsArray.length;
                var batchSize = 500;

                if (total <= batchSize) {
                    // Standard single request
                    data.lead_ids = idsArray.join(',');
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: data,
                        success: function (res) {
                            if (res.status) {
                                if (successCallback) successCallback(res);
                            } else {
                                showToast(res.message || 'Action failed', 'danger');
                            }
                        },
                        error: function (xhr) {
                            showToast('Error: ' + (xhr.responseJSON?.message || 'Action failed'), 'danger');
                        },
                        complete: function () {
                            if (finallyCallback) finallyCallback();
                        }
                    });
                    return;
                }

                // Batch Request
                $('#batch_progress_modal .modal-title').text('Processing Bulk Actions');
                $('#batch_progress_modal').modal('show');
                $('#batch_progress_bar').css('width', '1%').text('0%');
                $('#batch_status_text').text('Starting batch process...');

                var processed = 0;
                var chunks = [];
                for (var i = 0; i < total; i += batchSize) {
                    chunks.push(idsArray.slice(i, i + batchSize));
                }

                // Timer
                var batchStartTime = Date.now();
                if (typeof batchTimerInterval !== 'undefined') clearInterval(batchTimerInterval);
                batchTimerInterval = setInterval(function () {
                    var now = Date.now();
                    var diff = Math.floor((now - batchStartTime) / 1000);
                    var m = Math.floor(diff / 60);
                    var s = diff % 60;
                    $('#batch_timer').text((m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s);
                }, 1000);

                function runNextBatch(index) {
                    if (index >= chunks.length) {
                        // Done
                        clearInterval(batchTimerInterval);
                        $('#batch_progress_bar').css('width', '100%').text('100%');
                        $('#batch_status_text').text('Batch process completed.');

                        $('#batch_progress_modal').modal('hide');

                        setTimeout(function () {
                            if (successCallback) successCallback({ status: true, message: 'All ' + total + ' leads processed successfully.' });
                            if (finallyCallback) finallyCallback();
                        }, 100);
                        return;
                    }

                    var currentChunk = chunks[index];
                    data.lead_ids = currentChunk.join(',');

                    var pct = Math.round((processed / total) * 100);
                    $('#batch_progress_bar').css('width', pct + '%').text(pct + '%');
                    $('#batch_status_text').text('Processing leads ' + (processed + 1) + ' to ' + Math.min(processed + currentChunk.length, total) + ' of ' + total + '...');

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: data,
                        success: function (res) {
                            if (res.status) {
                                processed += currentChunk.length;
                                runNextBatch(index + 1);
                            } else {
                                handleBatchError(res.message);
                            }
                        },
                        error: function (xhr) {
                            handleBatchError(xhr.responseJSON?.message || 'Batch request failed');
                        }
                    });
                }

                function handleBatchError(msg) {
                    clearInterval(batchTimerInterval);
                    $('#batch_status_text').addClass('text-danger').text('Error: ' + msg);
                    setTimeout(function () {
                        $('#batch_progress_modal').modal('hide');
                        $('#batch_status_text').removeClass('text-danger');
                        showToast('Batch process stopped: ' + msg, 'danger');
                        if (finallyCallback) finallyCallback();
                    }, 3000);
                }

                runNextBatch(0);
            }

            // Handle Multi Action Submit
            $('#multiActionForm').on('submit', function (e) {
                e.preventDefault();
                const $form = $(this);
                if (!validateAnyForm($form)) return false;

                var actions = [];
                var isValid = true;

                // Collect Data
                $('#multi_action_inputs .action-group').each(function () {
                    var type = $(this).data('type');
                    if (type === 'merge') {
                        var mergeFromId = $(this).find('input[name="merge_from_lead_id"]').val();
                        actions.push({ type: 'merge', from_lead_id: mergeFromId });
                    } else if (type === 'status') {
                        var val = $(this).find('select').val();
                        if (!val) isValid = false;
                        actions.push({ type: 'status', value: val });
                    } else if (type === 'source') {
                        var val = $(this).find('select').val();
                        if (!val) isValid = false;
                        actions.push({ type: 'source', value: val });
                    } else if (type === 'assign') {
                        var val = $(this).find('select').val();
                        if (!val) isValid = false;
                        actions.push({ type: 'assign', value: val });
                    } else if (type === 'trash') {
                        actions.push({ type: 'trash' });
                    } else if (type === 'restore') {
                        actions.push({ type: 'restore' });
                    } else if (type === 'delete') {
                        actions.push({ type: 'delete' });
                    }
                });

                if (!isValid) {
                    showToast('Please fill in all required fields.', 'warning');
                    return;
                }

                // Execute Sequentially
                // Read IDs from hidden field
                var leadIds = $('#multi_lead_ids').val();
                if (!leadIds) {
                    // Fallback
                    leadIds = Array.from(window.selectedLeadIds).join(',');
                }

                var boxSlug = "{{ request()->route('box_slug') }}";

                // Disable button
                var btn = $(this).find('button[type="submit"]');
                var originalText = btn.text();
                btn.prop('disabled', true).text('Processing...');

                // Helper for sequential execution
                var promiseChain = Promise.resolve();

                actions.forEach(function (action) {
                    promiseChain = promiseChain.then(function () {
                        return new Promise(function (resolve, reject) {
                            var url = '';
                            var data = { _token: '{{ csrf_token() }}' };

                            if (action.type === 'merge') {
                                url = "{{ route('leads.merge_duplicates', ['box_slug' => request()->route('box_slug')]) }}";
                                data.primary_lead_id = leadIds.split(',')[0];
                                if (action.from_lead_id) {
                                    data.merge_from_lead_id = action.from_lead_id;
                                }
                            } else if (action.type === 'status') {
                                url = "{{ route('leads.bulk.update.status', ['box_slug' => request()->route('box_slug')]) }}";
                                data.lead_status_id = action.value;
                            } else if (action.type === 'source') {
                                url = "{{ route('leads.bulk.update.source', ['box_slug' => request()->route('box_slug')]) }}";
                                data.lead_source_id = action.value;
                            } else if (action.type === 'assign') {
                                url = "{{ route('leads.bulk.assign', ['box_slug' => request()->route('box_slug')]) }}";
                                data.assigned_to = action.value;
                            } else if (action.type === 'trash') {
                                url = "{{ route('leads.bulk.trash', ['box_slug' => request()->route('box_slug')]) }}";
                            } else if (action.type === 'restore') {
                                url = "{{ route('leads.bulk.restore', ['box_slug' => request()->route('box_slug')]) }}";
                            } else if (action.type === 'delete') {
                                url = "{{ route('leads.bulk.delete', ['box_slug' => request()->route('box_slug')]) }}";
                            }

                            console.log('Running action with batch support:', action.type, url);

                            executeBulkAction(url, data, leadIds, function (res) {
                                resolve(res);
                            }, null);
                            // Note: executeBulkAction handles its own errors and toasts
                        });
                    });
                });

                promiseChain.then(function () {
                    showToast('All actions completed successfully!', 'success');
                    $('#multi_action_modal').modal('hide');

                    // Reload the table data to reflect changes (removed rows for delete/trash type, or updated rows for status/assign)
                    if (typeof fetchLeadsList === 'function') {
                        fetchLeadsList();
                    } else if (leadsTableInstance) {
                        leadsTableInstance.ajax.reload(null, false);
                    }

                    $('#bulk-action-container').hide();
                    $('#select-all').prop('checked', false);
                    resetBulkChecks(); // Uncheck boxes on success
                    $('#multi_lead_ids').val(''); // Clear IDs
                }).catch(function (err) {
                    console.error(err);
                    showToast('Error: ' + (err.message || 'One or more actions failed.'), 'danger');
                }).finally(function () {
                    btn.prop('disabled', false).text(originalText);
                });
            });

            $('#bulkUpdateStatusForm').on('submit', function (e) {
                e.preventDefault();
                const $form = $(this);
                if (!validateAnyForm($form)) return false;

                var selectedIds = [];
                if (window.selectedLeadIds && window.selectedLeadIds.size > 0) {
                    window.selectedLeadIds.forEach(function (id) {
                        selectedIds.push(String(id));
                    });
                    selectedIds = [...new Set(selectedIds)];
                } else {
                    $('.lead-checkbox:checked').each(function () {
                        var tr = $(this).closest('tr');
                        if (leadsTableInstance) {
                            var row = leadsTableInstance.row(tr);
                            var data = row.data();
                            if (data && (data.id || data.lead_id)) {
                                selectedIds.push(data.id || data.lead_id);
                            }
                        }
                    });
                }

                if (selectedIds.length === 0) {
                    showToast('No leads selected.', 'warning');
                    return;
                }

                var statusId = $('#bulk_lead_status').val();
                var url = "{{ route('leads.bulk.update.status', ['box_slug' => request()->route('box_slug')]) }}";
                var data = {
                    _token: '{{ csrf_token() }}',
                    lead_status_id: statusId
                };

                executeBulkAction(url, data, selectedIds, function (response) {
                    showToast(response.message, 'success');
                    $('#update_status_modal').modal('hide');
                    $('#bulkUpdateStatusForm')[0].reset();

                    // Refresh table to show updated status
                    if (typeof fetchLeadsList === 'function') {
                        fetchLeadsList();
                    } else if (leadsTableInstance) {
                        leadsTableInstance.ajax.reload(null, false);
                    }

                    $('#bulk-action-container').hide();
                    $('#select-all').prop('checked', false);
                });
            });



            // Bulk Update Source Logic
            $(document).on('click', '.btn-bulk-source', function () {
                $('#update_source_modal').modal('show');
            });

            $('#bulkUpdateSourceForm').on('submit', function (e) {
                e.preventDefault();
                const $form = $(this);
                if (!validateAnyForm($form)) return false;

                var selectedIds = [];
                if (window.selectedLeadIds && window.selectedLeadIds.size > 0) {
                    window.selectedLeadIds.forEach(function (id) {
                        selectedIds.push(String(id));
                    });
                    selectedIds = [...new Set(selectedIds)];
                } else {
                    $('.lead-checkbox:checked').each(function () {
                        var tr = $(this).closest('tr');
                        if (leadsTableInstance) {
                            var row = leadsTableInstance.row(tr);
                            var data = row.data();
                            if (data && (data.id || data.lead_id)) {
                                selectedIds.push(data.id || data.lead_id);
                            }
                        }
                    });
                }

                if (selectedIds.length === 0) {
                    showToast('No leads selected.', 'warning');
                    return;
                }

                var sourceId = $('#bulk_lead_source').val();
                var url = "{{ route('leads.bulk.update.source', ['box_slug' => request()->route('box_slug')]) }}";
                var data = {
                    _token: '{{ csrf_token() }}',
                    lead_source_id: sourceId
                };

                executeBulkAction(url, data, selectedIds, function (response) {
                    showToast(response.message, 'success');
                    $('#update_source_modal').modal('hide');
                    $('#bulkUpdateSourceForm')[0].reset();

                    // Refresh table to show updated source
                    if (typeof fetchLeadsList === 'function') {
                        fetchLeadsList();
                    } else if (leadsTableInstance) {
                        leadsTableInstance.ajax.reload(null, false);
                    }

                    $('#bulk-action-container').hide();
                    $('#select-all').prop('checked', false);
                });
            });

            var trashActionUrl = '';

            // Bulk Trash Logic
            $(document).on('click', '.btn-bulk-trash', function () {
                var selectedIds = [];
                if (window.selectedLeadIds && window.selectedLeadIds.size > 0) {
                    window.selectedLeadIds.forEach(function (id) {
                        selectedIds.push(String(id));
                    });
                    selectedIds = [...new Set(selectedIds)];
                } else {
                    $('.lead-checkbox:checked').each(function () {
                        var tr = $(this).closest('tr');
                        if (leadsTableInstance) {
                            var row = leadsTableInstance.row(tr);
                            var data = row.data();
                            if (data && (data.id || data.lead_id)) {
                                selectedIds.push(data.id || data.lead_id);
                            }
                        }
                    });
                }

                if (selectedIds.length === 0) {
                    showToast('No leads selected.', 'warning');
                    return;
                }

                trashActionUrl = "{{ route('leads.bulk.trash', ['box_slug' => request()->route('box_slug')]) }}";
                $('#delete_confirm_modal').modal('show');
            });

            // Bulk Delete Logic
            $(document).on('click', '.btn-bulk-delete', function () {
                var selectedIds = [];
                if (window.selectedLeadIds && window.selectedLeadIds.size > 0) {
                    window.selectedLeadIds.forEach(function (id) {
                        selectedIds.push(String(id));
                    });
                    selectedIds = [...new Set(selectedIds)];
                } else {
                    $('.lead-checkbox:checked').each(function () {
                        var tr = $(this).closest('tr');
                        if (leadsTableInstance) {
                            var row = leadsTableInstance.row(tr);
                            var data = row.data();
                            if (data && (data.id || data.lead_id)) {
                                selectedIds.push(data.id || data.lead_id);
                            }
                        }
                    });
                }

                if (selectedIds.length === 0) {
                    showToast('No leads selected.', 'warning');
                    return;
                }

                trashActionUrl = "{{ route('leads.bulk.delete', ['box_slug' => request()->route('box_slug')]) }}";
                $('#delete_confirm_modal').modal('show');
            });

            // Confirm Trash/Delete Action
            $('#btn-confirm-trash').on('click', function () {
                var selectedIds = [];
                if (window.selectedLeadIds && window.selectedLeadIds.size > 0) {
                    window.selectedLeadIds.forEach(function (id) {
                        selectedIds.push(String(id));
                    });
                    selectedIds = [...new Set(selectedIds)];
                } else {
                    $('.lead-checkbox:checked').each(function () {
                        var tr = $(this).closest('tr');
                        if (leadsTableInstance) {
                            var row = leadsTableInstance.row(tr);
                            var data = row.data();
                            if (data && (data.id || data.lead_id)) {
                                selectedIds.push(data.id || data.lead_id);
                            }
                        }
                    });
                }

                var data = {
                    _token: '{{ csrf_token() }}'
                };

                executeBulkAction(trashActionUrl, data, selectedIds, function (response) {
                    showToast(response.message, 'success');
                    $('#delete_confirm_modal').modal('hide');

                    // Immediately remove rows from view
                    removeLeadsFromTable(selectedIds);

                    // Also trigger a background refresh to ensure counts and server state are synced
                    if (typeof fetchLeadsList === 'function') {
                        fetchLeadsList();
                    } else if (leadsTableInstance) {
                        leadsTableInstance.ajax.reload(null, false);
                    }

                    $('#bulk-action-container').hide();
                    $('#select-all').prop('checked', false);
                });
            });

            // --- Filter Modal Logic ---
            $('#filter_leads_modal').on('show.bs.modal', function () {
                // Reset state
                $('#filter-step-1').show();
                $('#filter-step-2').hide();
                $('#filter-step-1-footer').show();
                $('#filterLeadsForm')[0].reset();

                // Populate fields
                var $select = $('#filter_field');
                $select.empty();
                $select.append('<option value="">Choose Field</option>');

                if (window.availableLeadColumns) {
                    window.availableLeadColumns.forEach(function (col) {
                        // Exclude system columns that shouldn't be filtered this way or complex renderers
                        if (['checkbox', 'star', 'action'].includes(col.columnName)) return;

                        $select.append('<option value="' + col.columnName + '">' + (col.title || col.columnName) + '</option>');
                    });
                }

                // Initialize Select2 for the field selection with typeahead search
                $('#filter_field').select2({
                    dropdownParent: $('#filter_leads_modal'),
                    width: '100%'
                });
            });

            // Step 1 -> Step 2
            $('#filter_field').on('change', function () {
                var val = $(this).val();
                var text = $(this).find('option:selected').text();

                if (val) {
                    $('#filter_field_value').val(val);
                    $('#filter_field_label').text(text);

                    // Check if field requires a dropdown (Source, Status, Owner)
                    // Fields: lead_source_id (Source), lead_status_id (Lead Status), assigned_to (Owner/Assigned To)
                    var isSelectField = false;
                    var fetchUrl = '';
                    var valueContainerP = $('#filter_value_container');
                    valueContainerP.empty(); // Clear previous input

                    // Reset operators to default full set, then restrict if necessary
                    $('.filter-radio-group .form-check').show();
                    // Default select "Contains" or "Equals" depending on type?

                    if (val === 'lead_source_id') {
                        isSelectField = true;
                        fetchUrl = "{{ route('get.sources.list', ['box_slug' => request()->route('box_slug')]) }}";
                    } else if (val === 'lead_status_id' || val === 'lead_status') {
                        isSelectField = true;
                        fetchUrl = "{{ route('get.lead.status.list', ['box_slug' => request()->route('box_slug')]) }}";
                    } else if (val === 'assigned_to' || val === 'owner' || val === 'last_assigned_to' || val === 'assigned_history') {
                        isSelectField = true;
                        fetchUrl = "{{ route('get.users.list', ['box_slug' => request()->route('box_slug')]) }}";
                    } else if (val === 'last_lead_status') {
                        isSelectField = true;
                        fetchUrl = "{{ route('get.lead.status.list', ['box_slug' => request()->route('box_slug')]) }}";
                    }

                    if (isSelectField) {
                        // Show loader
                        valueContainerP.html('<p>Loading options...</p>');

                        // User REQUEST: Allow Contains/Does Not Contain for these fields too.
                        // We show all operators.
                        $('#op_contains').parent().show();
                        $('#op_not_contains').parent().show();

                        // Set default to Equals as it is most common for dropdowns.
                        $('#op_equals').prop('checked', true);

                        $.get(fetchUrl, function (res) {
                            if (res.status && res.data) {
                                var selectHtml = '<div id="filter_value_select_container"><label class="form-label">Value</label>';
                                selectHtml += '<select class="form-control select" id="filter_value_select">';
                                selectHtml += '<option value="">Select Option</option>';
                                res.data.forEach(function (opt) {
                                    selectHtml += '<option value="' + opt.id + '">' + opt.name + '</option>';
                                });
                                selectHtml += '</select></div>';

                                var textHtml = '<div id="filter_value_text_container" style="display:none;"><label class="form-label">Value</label><input type="text" class="form-control" id="filter_value_text" placeholder="Enter text to search"></div>';

                                valueContainerP.html(selectHtml + textHtml);

                                // Initialize Select2 for the new dynamic select
                                $('#filter_value_select').select2({
                                    dropdownParent: $('#filter_leads_modal'),
                                    width: '100%'
                                });

                                // Store that we are in select mode for this field
                                valueContainerP.attr('data-mode', 'select-field');

                            } else {
                                valueContainerP.html('<p class="text-danger">Failed to load options.</p>');
                            }
                        });
                    } else if (val === 'created_at' || val === 'updated_at' || val.includes('_date') || text.toLowerCase().includes(' date') || text.toLowerCase().startsWith('date') || text.toLowerCase().includes('created on') || text.toLowerCase().includes('updated on')) {
                        // Date Fields
                        var dateOpsHtml = `
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="filter_operator" value="date_is" id="op_date_is" checked>
                                                <label class="form-check-label" for="op_date_is">Date is</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="filter_operator" value="equals" id="op_date_equals">
                                                <label class="form-check-label" for="op_date_equals">Equal to</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="filter_operator" value="greater_than" id="op_greater_than">
                                                <label class="form-check-label" for="op_greater_than">Greater than</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="filter_operator" value="less_than" id="op_less_than">
                                                <label class="form-check-label" for="op_less_than">Less than</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="filter_operator" value="between" id="op_between">
                                                <label class="form-check-label" for="op_between">Between</label>
                                            </div>
                                        `;
                        $('.filter-radio-group').html(dateOpsHtml);

                        // Value Inputs: Preset Dropdown + Calendar Inputs
                        var inputsHtml = `
                                            <div id="filter_date_inputs_container">
                                                <div id="preset_container" style="display:none;">
                                                    <label class="form-label">Select Preset</label>
                                                    <select class="form-control select" id="filter_value_preset">
                                                        <option value="">Choose option</option>
                                                        <option value="today">Today</option>
                                                        <option value="yesterday">Yesterday</option>
                                                        <option value="greater_than_today">Greater Than Today</option>
                                                        <option value="less_than_today">Less Than Today</option>
                                                        <option value="last_7_days">Last 7 Days</option>
                                                        <option value="last_1_month">Last 1 Month</option>
                                                        <option value="this_month">This Month</option>
                                                        <option value="custom">Custom Date</option>
                                                    </select>
                                                </div>
                                                <div id="custom_date_container">
                                                    <label class="form-label">Select Date</label>
                                                    <input type="date" class="form-control" id="filter_value_custom">
                                                </div>
                                                <div id="custom_range_container" style="display:none;">
                                                    <label class="form-label">Select Date Range</label>
                                                    <input type="date" class="form-control mb-2" id="filter_value_start" placeholder="Start Date">
                                                    <input type="date" class="form-control" id="filter_value_end" placeholder="End Date">
                                                </div>
                                            </div>
                                        `;
                        valueContainerP.html(inputsHtml);

                        // Handle Operator Change to toggle inputs
                        $('input[name="filter_operator"]').on('change', function () {
                            var op = $(this).val();
                            var inputs = $('#filter_date_inputs_container').children();
                            inputs.hide();

                            if (op === 'date_is') {
                                $('#preset_container').show();
                                if ($('#filter_value_preset').val() === 'custom') {
                                    $('#custom_date_container').show();
                                }
                            } else if (op === 'between') {
                                $('#custom_range_container').show();
                            } else if (op === 'empty' || op === 'not_empty') {
                                // All hidden
                            } else {
                                // equals, greater_than, less_than
                                $('#custom_date_container').show();
                            }
                        });

                    } else {
                        // Text Field - Restore default operators
                        // ... (existing text operators) ...
                        var defaultOpsHtml = `
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="filter_operator" value="contains" id="op_contains" checked>
                                                <label class="form-check-label" for="op_contains">Contains</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="filter_operator" value="not_contains" id="op_not_contains">
                                                <label class="form-check-label" for="op_not_contains">Does not Contain</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="filter_operator" value="empty" id="op_empty">
                                                <label class="form-check-label" for="op_empty">Empty</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="filter_operator" value="not_empty" id="op_not_empty">
                                                <label class="form-check-label" for="op_not_empty">Not Empty</label>
                                            </div>
                                            <div class="form-check" id="op_equals_div">
                                                <input class="form-check-input" type="radio" name="filter_operator" value="equals" id="op_equals">
                                                <label class="form-check-label" for="op_equals">Equals</label>
                                            </div>
                                        `;
                        $('.filter-radio-group').html(defaultOpsHtml);

                        valueContainerP.html('<label class="form-label">Value</label><input type="text" class="form-control" id="filter_value" placeholder="Enter value">');

                        // Show all operators
                        $('.filter-radio-group .form-check').show();
                        $('#op_contains').prop('checked', true);
                    }

                    $('#filter-step-1').hide();
                    $('#filter-step-1-footer').hide();
                    $('#filter-step-2').fadeIn();
                }
            });

            // CSS for Non-blocking modal
            $('<style>').text(`
                                #multi_action_modal.non-blocking-modal {
                                    pointer-events: none;
                                    background: rgba(0,0,0,0);
                                }
                                #multi_action_modal.non-blocking-modal .modal-dialog {
                                    pointer-events: auto;
                                    margin-top: 10%; 
                                }
                                #multi_action_modal.non-blocking-modal .modal-backdrop {
                                    display: none;
                                }
                                .bulk-action-check:checked { 
                                    background-color: #dc3545; 
                                    border-color: #dc3545; 
                                }
                            `).appendTo('head');

            // Step 2 -> Step 1
            $('#btn-filter-back').on('click', function () {
                $('#filter-step-2').hide();
                $('#filter-step-1').fadeIn();
                $('#filter-step-1-footer').show();
                $('#filter_field').val(''); // Reset selection
            });

            // Handle Operator Change (Hide value input for Empty/Not Empty) - Global Handler
            $(document).on('change', 'input[name="filter_operator"]', function () {
                var op = $(this).val();
                var valueContainerP = $('#filter_value_container');
                var mode = valueContainerP.attr('data-mode');

                if (mode === 'select-field') {
                    // Logic for Source/Status/Assignee
                    if (op === 'contains' || op === 'not_contains' || op === 'equals') {
                        $('#filter_value_select_container').show();
                        $('#filter_value_text_container').hide();
                    } else if (op === 'empty' || op === 'not_empty') {
                        $('#filter_value_select_container').hide();
                        $('#filter_value_text_container').hide();
                        $('#filter_value_select').prop('required', false);
                        $('#filter_value_text').prop('required', false);
                    }
                } else {
                    // Default logic
                    if (op === 'empty' || op === 'not_empty') {
                        $('#filter_value_container').children().hide();
                        if ($('#filter_date_inputs_container').length === 0) {
                            $('#filter_value_container').hide();
                            $('#filter_value').prop('required', false);
                        }
                    } else {
                        if ($('#filter_date_inputs_container').length === 0) {
                            $('#filter_value_container').show();
                            $('#filter_value').prop('required', false); // Text input logic handled elsewhere?
                        } else {
                            // Date logic handles itself
                        }
                    }
                }
            });

            // Initialize active filter as array only if not already set
            window.activeLeadFilter = window.activeLeadFilter || [];

            // Render on load (Delayed to allow columns to load logic if needed, but safe to call)
            $(document).ready(function () {
                setTimeout(renderActiveFilters, 500); // Small delay to ensure columns loaded for titles
            });

            function renderActiveFilters() {
                var container = $('#active_filters_list');
                container.empty();

                if (window.activeLeadFilter.length > 0) {
                    $('#current_filters_label').show();
                } else {
                    $('#current_filters_label').hide();
                }

                window.activeLeadFilter.forEach(function (filter, index) {
                    var fieldName = filter.field;
                    // Try to find readable title from available columns
                    if (window.availableLeadColumns) {
                        var col = window.availableLeadColumns.find(function (c) {
                            return c.columnName === filter.field || c.columnName === 'custom_' + filter.field;
                        });
                        if (col) fieldName = col.title;
                    }

                    if (filter.field === 'last_activity_description') {
                        fieldName = 'Last Activity';
                    }
                    if (filter.field === 'last_activity_date') {
                        fieldName = 'Last Activity Date';
                    }
                    if (filter.field === 'completed_date' || filter.field === 'upcoming_activity_date') {
                        fieldName = 'Upcoming Activity Date';
                    }
                    if (filter.field === 'activity_type_name') {
                        fieldName = 'Activity Type';
                    }
                    if (filter.field === 'activity_setting_name') {
                        fieldName = 'Activity';
                    }

                    var opLabel = filter.operator.replace(/_/g, ' ');
                    if (opLabel === 'date is') opLabel = 'is';
                    if (opLabel === 'date is not') opLabel = 'is not';
                    if (opLabel === 'date between') opLabel = 'is between';
                    if (opLabel === 'date before') opLabel = 'is before';
                    if (opLabel === 'date after') opLabel = 'is after';
                    var valLabel = filter.label || filter.value;

                    if (filter.field === 'activity_type_name' && String(filter.value).toLowerCase() === 'all') {
                        valLabel = 'All Activity Types';
                    }

                    if (typeof valLabel === 'object') {
                        valLabel = valLabel.start + ' to ' + valLabel.end;
                    } else if (!valLabel && (filter.operator === 'empty' || filter.operator === 'not_empty')) {
                        valLabel = '';
                    }

                    // Title case the value label if it's a string
                    if (typeof valLabel === 'string') {
                        valLabel = valLabel.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                    }

                    var html = '<div class="align-items-center mb-3 col-md-3" style="position:relative;">' +
                        '<div class="p-2 rounded text-dark fs-12" style="background-color: #ffdec7; height: 100%;">' +
                        '<span class="text-primary" style="font-size:12px;">' + (index === 0 ? 'Where' : 'And') + '</span><br>' +
                        '<strong>' + fieldName + '</strong> ' + opLabel + ' <em>' + valLabel + '</em>' +
                        '</div>' +
                        '<div class="ms-2 d-flex align-items-center text-primary fs-3 btn-ed-dl">' +
                        '<i class="ti ti-edit cursor-pointer" onclick="editFilter(' + index + ')" title="Edit"></i>' +
                        '<span class="mx-1" style="color: #ffdec7;">|</span>' +
                        '<i class="ti ti-circle-minus cursor-pointer" onclick="removeFilter(' + index + ')" title="Remove"></i>' +
                        '</div>' +
                        '</div>';
                    container.append(html);
                });
            }

            window.removeFilter = function (index) {
                window.activeLeadFilter.splice(index, 1);
                renderActiveFilters();
                if (typeof leadsTableInstance !== 'undefined' && leadsTableInstance) {
                    leadsTableInstance.draw();
                } else if (typeof fetchLeadsList === 'function') {
                    fetchLeadsList();
                }
            };

            window.editFilter = function (index) {
                var filter = window.activeLeadFilter[index];
                // Remove from active filters so "Apply" adds the updated one
                window.activeLeadFilter.splice(index, 1);
                renderActiveFilters();

                // Open Modal
                $('#filter_leads_modal').modal('show');

                // Populate Fields (Small delay to ensure modal is ready)
                setTimeout(function () {
                    $('#filter_field').val(filter.field).trigger('change');

                    // Wait for Field Change Logic (operators population)
                    setTimeout(function () {
                        $('input[name="filter_operator"][value="' + filter.operator + '"]').prop('checked', true).trigger('change');

                        // Set Values
                        setTimeout(function () {
                            if (typeof filter.value === 'object') {
                                $('#filter_value_start').val(filter.value.start);
                                $('#filter_value_end').val(filter.value.end);
                            } else {
                                $('#filter_value').val(filter.value);
                                $('#filter_value_text').val(filter.value);
                                $('#filter_value_select').val(filter.value);
                                $('#filter_value_custom').val(filter.value);

                                // Handle Date Preset if needed
                                if ($('#filter_value_preset').length > 0) {
                                    if (filter.operator === 'date_is' && filter.value !== 'custom' && !filter.value.match(/^\d{4}-\d{2}-\d{2}$/)) {
                                        $('#filter_value_preset').val(filter.value).trigger('change');
                                    } else {
                                        $('#filter_value_preset').val('custom').trigger('change');
                                        $('#filter_value_custom').val(filter.value);
                                    }
                                }
                            }
                        }, 100);
                    }, 100);
                }, 200);
            };

            // Handle Filter Submit
            $('#filterLeadsForm').off('submit').on('submit', function (e) {
                e.preventDefault();

                var field = $('#filter_field_value').val();
                var operator = $('input[name="filter_operator"]:checked').val();
                var value = $('#filter_value').val();

                // Handle Date Values
                if ($('#filter_date_inputs_container').length > 0) {

                    if (operator === 'date_is') {
                        value = $('#filter_value_preset').val();
                        if (value === 'custom') {
                            value = $('#filter_value_custom').val(); // Use the custom date
                            if (!value) {
                                showToast('Please select a date.', 'warning');
                                return;
                            }
                        } else if (!value) {
                            showToast('Please select a preset.', 'warning');
                            return;
                        }
                    } else if (operator === 'between') {
                        var start = $('#filter_value_start').val();
                        var end = $('#filter_value_end').val();
                        if (!start || !end) {
                            showToast('Please select both start and end dates.', 'warning');
                            return;
                        }
                        value = { start: start, end: end };
                    } else if (operator === 'empty' || operator === 'not_empty') {
                        value = '';
                    } else {
                        // equals, greater_than, less_than -> Single Date
                        value = $('#filter_value_custom').val();
                        if (!value) {
                            showToast('Please select a date.', 'warning');
                            return;
                        }
                    }
                }
                // Handle Select Field Mode (Source/Status/Assignee)
                else if ($('#filter_value_container').attr('data-mode') === 'select-field') {
                    if (operator === 'equals') {
                        value = $('#filter_value_select').val();
                        if (!value) {
                            showToast('Please select an option.', 'warning');
                            return;
                        }
                    } else if (operator === 'contains' || operator === 'not_contains') {
                        value = $('#filter_value_text').val();
                        if (!value) {
                            showToast('Please enter a value.', 'warning');
                            return;
                        }
                    } else if (operator === 'empty' || operator === 'not_empty') {
                        value = '';
                    }
                }

                if (!field) {
                    showToast('Please select a field first.', 'warning');
                    return;
                }

                var newFilter = {
                    field: field,
                    operator: operator,
                    value: value
                };

                window.activeLeadFilter.push(newFilter);
                renderActiveFilters();

                $('#filter_leads_modal').modal('hide');
                showToast('Filter Applied', 'success');

                if (typeof leadsTableInstance !== 'undefined' && leadsTableInstance) {
                    leadsTableInstance.draw();
                } else if (typeof fetchLeadsList === 'function') {
                    fetchLeadsList();
                }
            });


            // Export Handler
            $(document).on('click', '.btn-export-leads, #btn_export_current_view', function (e) {
                e.preventDefault();

                var exportUrl = "{{ route('leads.export', request()->route('box_slug')) }}";

                var token = $('meta[name="csrf-token"]').attr('content');
                if (!token) console.warn('CSRF Token missing in meta tag');

                // Show loading state
                var $btn = $(this);
                var originalText = $btn.text();
                $btn.text('Exporting...').addClass('disabled');

                $.ajax({
                    url: exportUrl,
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    data: {
                        filters: window.activeLeadFilter || [],
                        type: $btn.data('type') || 'excel'
                    },
                    success: function (response) {
                        if (response.status) {
                            showToast(response.message, 'success');
                            if (response.url) {
                                // For PDF, download directly
                                var link = document.createElement('a');
                                link.href = response.url;
                                link.download = response.url.split('/').pop();
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                            }
                        } else {
                            showToast(response.message || 'Export failed.', 'error');
                        }
                    },
                    error: function (xhr) {
                        console.error('Export Error:', xhr);
                        if (xhr.status === 419) {
                            showToast('Session Expired. Please refresh.', 'error');
                        } else {
                            showToast('Export failed. Please try again.', 'error');
                        }
                    },
                    complete: function () {
                        $btn.text(originalText).removeClass('disabled');
                    }
                });
            });

            // --- My Reports Logic ---
            function loadReports() {
                const url = "{{ route('reports.index', ['box_slug' => request()->route('box_slug')]) }}";
                $.get(url, function (res) {
                    if (res.status) {
                        renderReports(res.my_reports, '#my_saved_lists_container', true);
                        renderReports(res.shared_reports, '#shared_saved_lists_container', false);
                    }
                });
            }

            $(document).on('show.bs.modal', '#saved_lists_modal', function () {
                loadReports();
            });

            function renderReports(reports, containerId, isOwner) {
                const container = $(containerId);
                container.empty();
                if (reports.length === 0) {
                    container.html('<p class="text-muted text-center mt-3">No reports found.</p>');
                    return;
                }

                reports.forEach(function (report) {
                    let actions = `<button class="btn btn-sm btn-icon btn-light btn-apply-report" data-id="${report.id}" title="Apply"><i class="ti ti-check text-success"></i></button>`;
                    if (isOwner) {
                        actions += `<button class="btn btn-sm btn-icon btn-light btn-edit-report-modal" data-id="${report.id}" title="Edit"><i class="ti ti-edit text-warning"></i></button>`;
                        actions += `<button class="btn btn-sm btn-icon btn-light btn-delete-report" data-id="${report.id}" title="Delete"><i class="ti ti-trash text-danger"></i></button>`;
                    }

                    const html = `
                                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 border-bottom">
                                            <div class="flex-grow-1 cursor-pointer btn-apply-report" data-id="${report.id}">
                                                <h6 class="mb-0 fs-14">${report.name} ${report.is_default ? '<span class="badge bg-success-transparent ms-1">Default</span>' : ''}</h6>
                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                    <span class="fs-12 text-muted"><i class="ti ti-lock me-1"></i>${report.is_public ? 'Shared' : 'Private'}</span>
                                                    <span class="fs-12 text-muted ml-2"><i class="ti ti-chart-bar me-1"></i>${report.show_in_report ? 'Report-Enabled' : 'List-Only'}</span>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-1">
                                                ${actions}
                                            </div>
                                        </div>
                                    `;
                    container.append(html);
                });

                // Store reports data for easy access
                if (isOwner) window.myReportsData = reports;
                else window.sharedReportsData = reports;
            }

            $('#createViewForm').on('submit', function (e) {
                e.preventDefault();
                const formData = $(this).serializeArray();
                const data = {};
                formData.forEach(item => data[item.name] = item.value);

                // Add current filters and columns
                data.filters = window.activeLeadFilter || [];
                data.columns = columnPreferences || {};

                const url = "{{ route('reports.store', ['box_slug' => request()->route('box_slug')]) }}";
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: data,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function (res) {
                        if (res.status) {
                            showToast('Report saved successfully!', 'success');
                            $('#create_view_modal').modal('hide');
                            $('#createViewForm')[0].reset();
                            loadReports();
                            // Open the Saved Lists modal after a short delay to allow re-rendering
                            setTimeout(() => {
                                $('#saved_lists_modal').modal('show');
                            }, 500);
                        } else {
                            showToast(res.message, 'danger');
                        }
                    }
                });
            });

            $(document).on('click', '.btn-edit-report-modal', function () {
                const id = $(this).data('id');
                const report = window.myReportsData.find(r => r.id == id);
                if (report) {
                    $('#update_view_id').val(report.id);
                    $('#update_view_name').val(report.name);
                    $('#update_view_is_public').prop('checked', report.is_public);
                    $('#update_view_show_report').prop('checked', report.show_in_report);
                    $('#update_view_is_default').prop('checked', report.is_default);
                    $('#update_view_modal').modal('show');
                }
            });

            $('#updateViewForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#update_view_id').val();
                const formData = $(this).serializeArray();
                const data = {};
                formData.forEach(item => data[item.name] = item.value);

                // Optional: User might want to update filters/columns of existing view too
                // For now let's just update the metadata as per the modal fields
                // If they want to update filters, they'd use the "Update" option in Settings dropdown (to be implemented)

                const url = "{{ url('/') }}/{{ request()->route('box_slug') }}/my-reports/" + id;
                $.ajax({
                    url: url,
                    method: 'POST', // Route is POST but we can use _method PUT if needed, our route is POST /{id}
                    data: data,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function (res) {
                        if (res.status) {
                            showToast('Report updated successfully!', 'success');
                            $('#update_view_modal').modal('hide');
                            loadReports();
                        } else {
                            showToast(res.message, 'danger');
                        }
                    }
                });
            });

            $(document).on('click', '.btn-delete-report', function () {
                if (!confirm('Are you sure you want to delete this report?')) return;
                const id = $(this).data('id');
                const url = "{{ url('/') }}/{{ request()->route('box_slug') }}/my-reports/" + id;
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function (res) {
                        if (res.status) {
                            showToast('Report deleted successfully!', 'success');
                            loadReports();
                        } else {
                            showToast(res.message, 'danger');
                        }
                    }
                });
            });

            $(document).on('click', '.btn-apply-report', function () {
                const id = $(this).data('id');
                let report = (window.myReportsData || []).find(r => r.id == id);
                if (!report) report = (window.sharedReportsData || []).find(r => r.id == id);

                if (report) {
                    window.currentlyActiveReportId = report.id;
                    // Apply Filters
                    window.activeLeadFilter = report.filters || [];
                    renderActiveFilters();

                    // Apply Columns
                    if (report.columns && Object.keys(report.columns).length > 0) {
                        columnPreferences = report.columns;
                        // We need to refresh the table with new columns
                        fetchLeadsList();
                    } else {
                        if (typeof leadsTableInstance !== 'undefined' && leadsTableInstance) {
                            leadsTableInstance.draw();
                        }
                    }

                    // Update URL without refreshing
                    const newUrl = window.location.pathname + '?report_id=' + report.id;
                    window.history.pushState({ path: newUrl }, '', newUrl);

                    $('#saved_lists_modal').modal('hide');
                    showToast(`Applied report: ${report.name}`, 'success');

                    // Show update/delete buttons in settings
                    $('.active-report-action').show();
                }
            });

            $(document).on('click', '#settings_modal_trigger', function () {
                if (window.currentlyActiveReportId) {
                    $('.active-report-action').show();
                } else {
                    $('.active-report-action').hide();
                }
            });

            $(document).on('click', '#btn_delete_current_view', function () {
                if (!window.currentlyActiveReportId) return;
                if (!confirm('Are you sure you want to delete the current view?')) return;

                const id = window.currentlyActiveReportId;
                const url = "{{ url('/') }}/{{ request()->route('box_slug') }}/my-reports/" + id;
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function (res) {
                        if (res.status) {
                            showToast('Report deleted successfully!', 'success');
                            window.currentlyActiveReportId = null;
                            window.location.href = "{{ route('leads.details', ['box_slug' => request()->route('box_slug')]) }}";
                        } else {
                            showToast(res.message, 'danger');
                        }
                    }
                });
            });

            // Populate Update Modal from Settings
            $(document).on('click', '[data-bs-target="#update_view_modal"]', function () {
                if (!window.currentlyActiveReportId) return;
                const report = (window.myReportsData || []).find(r => r.id == window.currentlyActiveReportId);
                if (report) {
                    $('#update_view_id').val(report.id);
                    $('#update_view_name').val(report.name);
                    $('#update_view_is_public').prop('checked', report.is_public);
                    $('#update_view_show_report').prop('checked', report.show_in_report);
                    $('#update_view_is_default').prop('checked', report.is_default);
                }
            });

            // Initial Load
            loadReports();

            // --- Delete Lead Logic ---
            var deleteModal = document.getElementById('delete_lead_modal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function (event) {
                    // Button that triggered the modal
                    var button = event.relatedTarget;
                    // Extract info from data-bs-* attributes
                    var leadId = button.getAttribute('data-lead-id');
                    // Update the modal's input field.
                    var modalInput = deleteModal.querySelector('#lead_id_to_delete');
                    if (modalInput) modalInput.value = leadId;
                });

                // Confirm Trash Handler
                $(document).on('click', '#btn-confirm-trash', function () {
                    var input = deleteModal.querySelector('#lead_id_to_delete');
                    var leadId = input ? input.value : null;
                    if (!leadId) return;

                    var $btn = $(this);
                    $btn.prop('disabled', true).text('Processing...');

                    $.ajax({
                        url: "{{ route('leads.bulk.trash', ['box_slug' => request()->route('box_slug')]) }}",
                        type: "POST",
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        data: { lead_ids: [leadId] },
                        success: function (response) {
                            $('#delete_lead_modal').modal('hide');
                            showToast(response.message || 'Lead moved to trash', 'success');
                            if (typeof removeLeadsFromTable === 'function') {
                                removeLeadsFromTable([leadId]);
                            } else {
                                fetchLeadsList();
                            }
                        },
                        error: function (xhr) {
                            $btn.prop('disabled', false).text('Confirm');
                            var msg = 'Failed to trash lead';
                            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                            showToast('Error: ' + msg, 'danger');
                        },
                        complete: function () {
                            $btn.prop('disabled', false).text('Confirm');
                        }
                    });
                });
            }

        });

        // Single click to toggle star
        $(document).on('click', '#leads_lists tbody tr .set-star', function(e) {
            e.stopPropagation();

            var $row = $(this).closest('tr');
            var rowData = leadsTableInstance.row($row).data();
            if (!rowData) return;

            var leadId = rowData.id || rowData.lead_id;
            if (!leadId) return;

            var $starIcon = $row.find('.fa-star');

            $.ajax({
                url: "{{ route('leads.toggle_star', ['box_slug' => request()->route('box_slug')]) }}",
                type: "POST",
                data: { lead_id: leadId },
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.status) {
                        if (res.is_starred) {
                            $starIcon.addClass('filled text-warning');
                            if (typeof showToast === 'function') showToast('Lead starred successfully!', 'success');
                        } else {
                            $starIcon.removeClass('filled text-warning');
                            if (typeof showToast === 'function') showToast('Lead star removed successfully!', 'success');
                        }
                        rowData.is_starred = res.is_starred;
                        // Avoid full redraw to prevent jumping, just update data object implicitly used by render
                    } else {
                        if (typeof showToast === 'function') showToast(res.message || 'Failed to toggle star', 'danger');
                    }
                },
                error: function() {
                    if (typeof showToast === 'function') showToast('Failed to toggle star', 'danger');
                }
            });
        });

        // Dynamic Lead Sub Status Logic
        $(document).on('change', 'select[name="lead_status"], select[name="lead_status_id"]', function () {
            var statusId = $(this).val();
            var $subStatusSelect = $('#lead_sub_status_id');
            var $wrapper = $('#lead_sub_status_wrapper');

            $subStatusSelect.empty().append('<option value="">-- Select Sub Status --</option>');

            if (statusId) {
                $.ajax({
                    url: "{{ route('get.lead.substatus.list', ['box_slug' => request()->route('box_slug')]) }}",
                    type: 'GET',
                    data: { lead_status_id: statusId },
                    success: function (response) {
                        if (response.status && response.data && response.data.length > 0) {
                            $.each(response.data, function (key, val) {
                                $subStatusSelect.append('<option value="' + val.id + '">' + val.name + '</option>');
                            });
                        }
                    },
                    error: function () {
                        // console.error('Failed to fetch sub-statuses');
                    }
                });
            }
        });

        /**
         * Helper to remove leads from the DataTable client-side
         * This provides immediate feedback while background jobs process the data
         */
        function removeLeadsFromTable(idsArray) {
            if (!idsArray || idsArray.length === 0) return;

            // Ensure IDs are strings
            var idsSet = new Set(idsArray.map(String));

            // Add to persistent optimistic blacklist to prevent reappearance during queue processing
            if (window.optimisticRemovedIds) {
                idsArray.forEach(function (id) {
                    window.optimisticRemovedIds.add(String(id));
                });
            }

            if (leadsTableInstance) {
                // 1. Remove rows locally for immediate feedback
                // This works for both Client-Side and Server-Side (optimistic UI)
                leadsTableInstance.rows(function (idx, data, node) {
                    var id = data.id || data.lead_id;
                    return id && idsSet.has(String(id));
                }).remove().draw(false);

                // 2. Only attempt AJAX reload if the table is actually Server-Side
                // Use internal settings to verify mode reliably
                var settings = leadsTableInstance.settings()[0];
                var isServerSide = settings.oFeatures.bServerSide;

                if (isServerSide) {
                    // Server-side mode reload (optional, for syncing)
                    leadsTableInstance.ajax.reload(null, false);

                    // 3. Optional: schedule a full refresh to catch background job completion
                    setTimeout(function () {
                        if (leadsTableInstance && leadsTableInstance.settings()[0].oFeatures.bServerSide) {
                            leadsTableInstance.ajax.reload(null, false);
                        }
                    }, 3000);
                } else {
                    console.log('Client-side mode detected. Skipping AJAX reload.');
                    // For client-side, we must manually update the info/counts because removing rows doesn't always auto-update everything if we are bypassing draw()
                    // But we called .draw(false) above, so pagination should update.
                    // We might want to refresh the "Showing X to Y of Z" text manually if needed, but DataTables handles it on draw.
                }
            }

            // Clear selection state
            if (window.selectedLeadIds) {
                idsArray.forEach(function (id) {
                    window.selectedLeadIds.delete(String(id));
                });
            }

            // Uncheck all if selection is empty
            if (!window.selectedLeadIds || window.selectedLeadIds.size === 0) {
                $('.lead-checkbox').prop('checked', false);
                $('#select-all').prop('checked', false);
                $('#bulk-action-container').hide();
            } else {
                // Re-sync UI for remaining selection
                toggleBulkActions();
            }

            // Update counts if possible (optional, DataTables info updates automatically)
        }

        // --- Fetch and Populate Currencies ---
        function fetchAndPopulateCurrencies() {
            fetch("{{ asset('assets/json/countries.json') }}")
                .then(res => res.json())
                .then(data => {
                    const currencyMap = new Map();

                    data.forEach(country => {
                        if (country.currencies && Object.keys(country.currencies).length > 0) {
                            const code = Object.keys(country.currencies)[0];
                            const currency = country.currencies[code];

                            if (!currencyMap.has(code)) {
                                currencyMap.set(code, {
                                    code: code,
                                    name: currency.name,
                                    symbol: currency.symbol || code
                                });
                            }
                        }
                    });

                    const allCurrencies = Array.from(currencyMap.values());
                    allCurrencies.sort((a, b) => a.name.localeCompare(b.name));

                    const currencyDropdowns = document.querySelectorAll('select[name="currency"]');
                    currencyDropdowns.forEach(dropdown => {
                        const firstOption = dropdown.querySelector('option[value=""]');
                        const placeholder = firstOption ? firstOption.textContent : 'Select currency';
                        
                        const currentValue = dropdown.value || '{{ $defaultCurrency ?? "USD" }}';

                        dropdown.innerHTML = `<option value="">${placeholder}</option>`;
                        
                        allCurrencies.forEach(currency => {
                            const isSelected = (currentValue === currency.code) ? 'selected' : '';
                            dropdown.insertAdjacentHTML('beforeend', `<option value="${currency.code}" ${isSelected}>${currency.code} (${currency.symbol}) - ${currency.name}</option>`);
                        });
                        
                        if($(dropdown).hasClass('select2') || $(dropdown).hasClass('select')) {
                            $(dropdown).trigger('change');
                        }
                    });
                })
                .catch(err => {
                    console.error("Failed to load currencies from API:", err);
                });
        }

        $(document).ready(function() {
            fetchAndPopulateCurrencies();
        });

        // Show full query in modal
        function showFullQuery(el) {
            var fullQuery = decodeURIComponent($(el).data('full-query'));
            $('#full_query_content').html(fullQuery);
            $('#full_query_modal').modal('show');
        }
    </script>

    <!-- Full Query Modal -->
    <div class="modal fade" id="full_query_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">Full Query</h5>
                    <button type="button" class="btn-close-custom bg-transparent border-0" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-3"></i>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div id="full_query_content" style="white-space: pre-wrap; font-size: 14px; line-height: 1.6;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@include('frontend.leads.partials.add_lead_form_js')
@include('whatsapp.partials.leads_whatsapp_js')
@endpush
@endsection

