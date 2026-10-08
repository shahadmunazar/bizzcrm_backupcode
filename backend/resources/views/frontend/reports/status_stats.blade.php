@extends('layouts.main')
@section('title', 'Status Stats')
@section('content')

		<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-4">
									<h4 class="page-title">Status Wise Stats<span class="count-title"></span></h4>
								</div>
								<div class="col-8 text-end">
									<div class="head-icons">
										<a href="#" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
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

						

						

						<div class="card">
							
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<div class="stats-users">
										    <select class="form-control select2" id="userFilter">
											  <option value="all" selected>All Users</option>
										    </select>
										</div>
									</div>
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<div class="icon-form">
											<span class="form-icon"><i class="ti ti-calendar"></i></span>
											<input type="text" class="form-control bookingrange" placeholder="" id="dateFilter">
										</div>
									</div>
								</div>
								<!-- Campaign List -->
								<div class="table-responsive" style="border:1px solid #E8E8E8; border-radius:6px;">
								
<table class="table dataTable text-nowrap" id="">
  <thead class="thead-light">
    <tr id="tableHeaderRow">
      <!-- Headers loaded via AJAX -->
    </tr>
  </thead>
  <tbody id="statsTableBody">
    <tr>
        <td colspan="100" class="text-center">Loading...</td>
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

		<!-- Edit Campaign -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom">
				<h4>Edit Campaign</h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="campaign-archieve.html">
					<div class="pro-create">
						<div class="row">
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Name <span class="text-danger">*</span></label>
									<input type="text" class="form-control" value="Enhanced brand">
								</div>
								<div class="mb-3">
									<label class="col-form-label">Campaign Type <span class="text-danger">*</span></label>
									<select class="select2">
										<option>Choose</option>
										<option>Public Relations</option>
										<option>Brand</option>
										<option>Media</option>
										<option selected="">Sales</option>
									</select>
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Deal Value<span class="text-danger"> *</span></label>
									<input class="form-control" type="text" value="$12,989">
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
		<!-- /Edit Campaign -->

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
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="campaign.html" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Campaign -->

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



@push('scripts')
<script>
    $(document).ready(function() {
        function loadCommonFilters() {
            $.ajax({
                url: "{{ box_route('reports.common_filters_ajax') }}",
                type: 'GET',
                success: function(res) {
                    if (res.status) {
                        var userHtml = '<option value="all" selected>All Users</option>';
                        $.each(res.users, function(i, bUser) {
                            if (bUser.user) {
                                var name = bUser.user.name ? bUser.user.name : (bUser.user.first_name || '');
                                userHtml += '<option value="' + bUser.user.id + '">' + name + '</option>';
                            }
                        });
                        $('#userFilter').html(userHtml);

                        var headerHtml = '<th class="text-center">S No.</th><th class="text-center">Status</th>';
                        $.each(res.sources, function(i, source) {
                            headerHtml += '<th class="text-center">' + source.name + '</th>';
                        });
                        headerHtml += '<th class="text-center">Total Amount</th><th class="text-center">Total Leads</th>';
                        $('#tableHeaderRow').html(headerHtml);

                        loadData();
                    }
                }
            });
        }
        
        function loadData() {
            var userId = $('#userFilter').val();
            var dateRange = $('#dateFilter').val();
            var sortOrder = $('#sortFilter').val();
            
            $('#statsTableBody').html('<tr><td colspan="100" class="text-center" style="padding: 100px 0;"><div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"><span class="visually-hidden">Loading...</span></div><h5 class="mt-3 text-muted">Loading Data...</h5></td></tr>');
            
            $.ajax({
                url: "{{ box_route('reports.status_stats_ajax') }}",
                type: 'GET',
                data: {
                    user_id: userId,
                    date_range: dateRange,
                    sort: sortOrder
                },
                success: function(response) {
                    if (response.status) {
                        $('#statsTableBody').html(response.html);
                    }
                }
            });
        }
        
        // Clear the date filter by default to fetch all records
        $('#dateFilter').val('');
        
        loadCommonFilters();
        
        $('#userFilter').on('change', function() {
            loadData();
        });
        
        $('#dateFilter').on('apply.daterangepicker change', function(ev, picker) {
            loadData();
        });
        
        $('#dateFilter').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            loadData();
        });
        
        $('.sort-item').on('click', function() {
            $('#sortFilter').val($(this).data('sort'));
            $('#sortLabel').text($(this).text().trim());
            loadData();
        });
    });
</script>
@endpush
@endsection
