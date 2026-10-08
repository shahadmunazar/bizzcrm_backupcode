@extends('layouts.main')
@section('title', 'Tracking History')
@section('content')
<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									<h4 class="page-title">Tracking History</h4>
								</div>
								<div class="col-4 text-end">
									<div class="head-icons">
										<a href="companies.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

						<div class="card ">
							<div class="card-header">
								<!-- Search -->
								<div class="row align-items-center">
									<div class="col-sm-3">
										<div class="icon-form mb-3 mb-sm-0">
											<div class="">
												<input type="date" id="filter_date" class="form-control" value="{{ date('Y-m-d') }}">
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="icon-form mb-3 mb-sm-0">
											<div class="">
												<select id="filter_user_id" class="form-control select">
													<option value="">Select User</option>
													@foreach($boxUsers as $boxUser)
														<option value="{{ $boxUser->id }}">{{ $boxUser->user->name ?? 'Unknown' }}</option>
													@endforeach
												</select>
											</div>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">

											
											
											
											<!-- <div class="dropdown me-2">
											<a href="javascript:void(0);" class="btn btn-dark btn-lg" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></a>
											
											
												
												<div class="dropdown-menu  dropdown-menu-end">
													<ul>
														<li>
															<a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-file-check"></i> Reports</a>
														</li>
													</ul>
												</div>
											</div> -->
											
											
											
											
										</div>
									</div>
								</div>
								<!-- /Search -->
							</div>
							<div class="card-body">
								

								<div class="history-map">
								    <div class="row">
										<div class="col-md-8">
											<div id="map" style="height: 500px; width: 100%; z-index: 1;"></div>
										</div>
										<div class="col-md-4">
											<div id="history-list" style="height: 500px; overflow-y: auto;">
												<!-- Dynamic history items will be appended here -->
												<div class="text-center p-3 text-muted">
													Please select a user and date to load history.
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
        
		
		<!-- Add Beat -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Create Beat</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="company.html">
					<div class="card">
						<div class="card-body">
							<div class="row">
							
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Beat Name <span class="text-danger"> *</span></label>
										<input type="text" class="form-control">
									</div>									
								</div>
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Add Town/City</label>
										<input type="email" class="form-control">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Add Territory</label>
										<input type="text" class="form-control">
									</div>									
								</div>
							</div>
                        </div>
                    </div>						
					<div class="card">
						<div class="card-body">
							<div class="row">			
								<h5 class="mb-2">Assign Staff</h5>
								<div class="col-md-12">
									<div class="mb-3">
										<select class="select" multiple>
											<option value="Shitender Kumar">Shitender Kumar</option>
											<option value="Shefali Sharma">Shefali Sharma</option>
											<option value="Munish Malhotra">Munish Malhotra</option>
										</select>
									</div>									
								</div>
					        </div>
                        </div>
                    </div>
					
					<div class="card">
						<div class="card-body">
							<div class="row">	
							
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Customer Level</label>
										<select class="select">
											<option disabled selected>Select Customer Level</option>
											<option>Primary Customer</option>
											<option>secondary Customer</option>
										</select>
									</div>
									<div class="mb-3">
									    <label class="col-form-label">Secondary Customer</label>
										<select class="select">
											<option value="" selected>Select Secondary Customer</option>
											<option value="">Ganesh Tradors</option>
											<option>Medizia Biotech</option>
										</select>
									</div>
                                    <div class="mb-3">
										<label class="col-form-label">Add Customer</label>
										<select class="select">
											<option selected>0 Customer Selected</option>
											<option>0</option>
											<option>1</option>
										</select>
									</div> 
									<div class="mb-3">
										<label class="col-form-label">Add Lead</label>
										<select class="select">
											<option selected>0 Lead Selected</option>
											<option>0</option>
											<option>1</option>
										</select>
									</div> 


									
								</div>
								
							</div>
							
					    </div>
                    </div>
                    <div class="card">
						<div class="card-body">
								
                            <div class="row">   
								<h4 class="mb-2">Add Frequency</h4>
								<div class="col-md-6">
								    
									<div class="mb-3">
										<select class="select">
											<option value="Weekly">Weekly</option>
											<option value="Monthly">Monthly</option>
										</select>
									</div>									
								</div>

								<div class="col-md-6">
									<div class="mb-3">
										<input type="text" placeholder="Enter Frequency" class="form-control">
									</div>									
								</div>
								
								<div class="col-md-12">
									<div class="mb-3">
									    <input type="text" placeholder="Define TC in a day" class="form-control">
									</div>
								</div>
								
								
							</div>	
								
					</div>
					</div>
			        <div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary">Create</button>
					</div>
							
			</div>
		</div>
					
				</form>
			</div>
		</div>
		<!-- /Add Company -->
		
		
		

		<!-- Edit Company -->
		<!-- Edit Company -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Edit Beat List</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="company.html">
					<div class="card">
						<div class="card-body">
							<div class="row">
							
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Beat Name <span class="text-danger"> *</span></label>
										<input type="text" class="form-control">
									</div>									
								</div>
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Add Town/City</label>
										<input type="email" class="form-control">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Add Territory</label>
										<input type="text" class="form-control">
									</div>									
								</div>
							</div>
                        </div>
                    </div>						
					<div class="card">
						<div class="card-body">
							<div class="row">			
								<h5 class="mb-2">Assign Staff</h5>
								<div class="col-md-12">
									<div class="mb-3">
										<select class="select" multiple>
											<option value="Shitender Kumar">Shitender Kumar</option>
											<option value="Shefali Sharma">Shefali Sharma</option>
											<option value="Munish Malhotra">Munish Malhotra</option>
										</select>
									</div>									
								</div>
					        </div>
                        </div>
                    </div>
					
					<div class="card">
						<div class="card-body">
							<div class="row">	
							
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Customer Level</label>
										<select class="select">
											<option disabled>Select Customer Level</option>
											<option>Primary Customer</option>
											<option>secondary Customer</option>
										</select>
									</div>
									<div class="mb-3">
									    <label class="col-form-label">Secondary Customer</label>
										<select class="select">
											<option value="" disabled>Select Secondary Customer</option>
											<option value="">Ganesh Tradors</option>
											<option>Medizia Biotech</option>
										</select>
									</div>
                                    <div class="mb-3">
										<label class="col-form-label">Add Customer</label>
										<select class="select">
											<option>0 Customer Selected</option>
											<option>0</option>
											<option>1</option>
										</select>
									</div> 
									<div class="mb-3">
										<label class="col-form-label">Add Lead</label>
										<select class="select">
											<option>0 Lead Selected</option>
											<option>0</option>
											<option>1</option>
										</select>
									</div> 


									
								</div>
								
							</div>
							
					    </div>
                    </div>
                    <div class="card">
						<div class="card-body">
								
                            <div class="row">   
								<h4 class="mb-2">Add Frequency</h4>
								<div class="col-md-6">
								    
									<div class="mb-3">
										<select class="select">
											<option value="Weekly">Weekly</option>
											<option value="Monthly">Monthly</option>
										</select>
									</div>									
								</div>

								<div class="col-md-6">
									<div class="mb-3">
										<input type="text" placeholder="Enter Frequency" class="form-control">
									</div>									
								</div>
								
								<div class="col-md-12">
									<div class="mb-3">
									    <input type="text" placeholder="Define TC in a day" class="form-control">
									</div>
								</div>
								
								
							</div>	
								
					</div>
					</div>
			        <div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
							
			</div>
		</div>
					
				</form>
			</div>
		</div>
		<!-- /Edit Company -->
		<!-- /Edit Company -->
		 
		
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
		<div class="modal fade custom-modal" id="company_detail">
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
		</div>
		<!-- /Company Detail -->

	</div>
	
@endsection

@push('scripts')
<script>
    var map;
    var routeLine = null;
    var markers = [];

    function initMapGoogle() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 20.5937, lng: 78.9629},
            zoom: 5
        });

        // Initialize select2 for typeahead functionality
        $('#filter_user_id').select2({
            width: '100%',
            placeholder: "Select User"
        });

        $('#filter_user_id, #filter_date').on('change', function() {
            loadHistory();
        });
    }

    function loadHistory() {
        var boxUserId = $('#filter_user_id').val();
        var date = $('#filter_date').val();

        if (!boxUserId) {
            $('#history-list').html('<div class="text-center p-3 text-muted">Please select a user to view history.</div>');
            return;
        }

        $('#history-list').html('<div class="text-center p-3 text-muted">Loading...</div>');

        $.ajax({
            url: "{{ route('fieldstracking.tracking-history.data', ['box_slug' => request()->route('box_slug')]) }}",
            type: "GET",
            data: {
                boxuser_id: boxUserId,
                date: date
            },
            success: function(response) {
                if (response.status) {
                    var data = response.data;
                    $('#history-list').empty();
                    
                    // Clear existing map layers
                    if (routeLine) {
                        routeLine.setMap(null);
                    }
                    markers.forEach(function(marker) {
                        marker.setMap(null);
                    });
                    markers = [];

                    if (data.length === 0) {
                        $('#history-list').html('<div class="text-center p-3 text-muted">No tracking history found for selected date.</div>');
                        return;
                    }

                    var latlngs = [];
                    var bounds = new google.maps.LatLngBounds();
                    var infoWindow = new google.maps.InfoWindow();

                    $.each(data, function(index, item) {
                        // Append to list
                        var dateObj = new Date(item.recorded_at);
                        var formattedDate = dateObj.toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute:'2-digit', second:'2-digit' });
                        
                        var html = `
                        <div class="card border border-light mb-2" style="display: inline-block; width: 100%; border:1px solid #eee!important;">
                            <div class="card-body">
                                <div class="mb-2">
                                    <p class="fs-14 mb-3 lh-1">${formattedDate}</p>
                                    <h4 class="mt-0 text-capitalize">${item.action ? item.action.replace('_', ' ') : 'Location Update'}</h4>
                                    ${item.network_status ? '<small class="text-muted">Network: ' + item.network_status + '</small>' : ''}
                                </div>
                            </div>
                        </div>`;
                        $('#history-list').append(html);

                        // Map points
                        if (item.latitude && item.longitude) {
                            var point = new google.maps.LatLng(parseFloat(item.latitude), parseFloat(item.longitude));
                            latlngs.push(point);
                            bounds.extend(point);

                            // Add marker
                            var popupText = `<b>${formattedDate}</b><br/>${item.action ? item.action.replace('_', ' ') : 'Location Update'}`;
                            var marker = new google.maps.Marker({
                                position: point,
                                map: map,
                                title: item.action
                            });

                            marker.addListener('click', function() {
                                infoWindow.setContent(popupText);
                                infoWindow.open(map, marker);
                            });

                            markers.push(marker);
                        }
                    });

                    if (latlngs.length > 0) {
                        routeLine = new google.maps.Polyline({
                            path: latlngs,
                            geodesic: true,
                            strokeColor: '#FF0000',
                            strokeOpacity: 1.0,
                            strokeWeight: 4,
                            icons: [{
                                icon: { path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW },
                                offset: '100%',
                                repeat: '100px'
                            }]
                        });
                        routeLine.setMap(map);
                        map.fitBounds(bounds);
                    } else {
                        $('#history-list').append('<div class="text-center p-3 text-warning">No valid GPS coordinates found in this history.</div>');
                    }
                }
            },
            error: function() {
                $('#history-list').html('<div class="text-center p-3 text-danger">Error loading tracking history.</div>');
            }
        });
    }

    // Load API dynamically
    if (typeof google === 'object' && typeof google.maps === 'object') {
        initMapGoogle();
    } else {
        var apiKey = 'AIzaSyCix326VJYzGXyMzF7_yTD_vbn5DBMHZL4';
        var script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=' + apiKey + '&callback=initMapGoogle';
        document.head.appendChild(script);
    }
</script>
@endpush
