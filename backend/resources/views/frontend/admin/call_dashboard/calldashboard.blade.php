@extends('layouts.main')
@section('content')
@section('title', 'Call dashboard')
<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">
						<div class="page-header">
							<div class="row align-items-center ">
								<div class="col-md-4">
									<h3 class="page-title">Call Admin Dashboard</h3>
								</div>
								<div class="col-md-8 float-end ms-auto">
									<div class="d-flex title-head allus">
                                        <div class="form-sort me-2 ms-0" style="position:relative;">
                                            <i class="ti ti-user" style="position:absolute; left:10px; top:12px; z-index:10;"></i>
                                            <select class="form-control" id="user-filter" style="padding-left:35px; border:1px solid #e8ebf3; margin-top: 4px; min-height: 32px; padding-bottom: 0; padding-top: 0;">
                                                @if(isset($canViewAll) && $canViewAll)
                                                    <option value="">All Users</option>
                                                @endif
                                                @foreach($users as $u)
                                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
										<div class="daterange-picker d-flex align-items-center justify-content-center">
											<div class="form-sort me-2 ms-0">
												<i class="ti ti-calendar"></i>
												<input type="text" class="form-control  date-range bookingrange">
											</div>
                                            <a href="{{ route('call_logs.index', ['box_slug' => request()->route('box_slug')]) }}" class="btn btn-primary btn-sm me-2 d-flex align-items-center" style="white-space: nowrap;"><i class="ti ti-list me-1"></i> Call Logs</a>
											<div class="head-icons mb-0">
												<a href="leads-dashboard.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
													<i class="ti ti-refresh-dot"></i>
												</a>
												<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
													<i class="ti ti-chevrons-up"></i>
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				

				
				
				
				<div class="row call-rec" id="dashboard-columns">
				    <div class="col-sm-4" id="col-today">
						<div class="card">
						    <div class="card-body ps-2 pe-2">
							    <h4 class="page-title mb-3">Today &nbsp; <span style="font-size:15px; font-weight:400;" id="today-label">31-Dec-2025</span></h4>
								<div class="row">
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="ti ti-phone fs-16"></i> Total Calls</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="ti ti-phone fs-16"></i> Calls Duration</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-success"><i class="fe fe-phone-incoming fs-16"></i>&nbsp; Incomming</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="fe fe-phone-incoming fs-16"></i>&nbsp; Incomming Mins.</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-secondary"><i class="fe fe-phone-outgoing fs-16"></i>&nbsp; Outgoing</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-secondary"><i class="fe fe-phone-outgoing fs-16"></i>&nbsp; Outgoing Mins.</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-danger"><i class="fe fe-phone-missed fs-16"></i>&nbsp; Missed</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-primary"><i class="fe fe-slash fs-16"></i>&nbsp; Rejected</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-primary"><i class="fe fe-phone-off fs-16"></i>&nbsp; Never Attended</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-primary"><i class="fe fe-phone-off fs-16"></i>&nbsp; Not Pickup</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="fe fe-user fs-16"></i> Unique Clients</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="fa-regular fa-clock fs-16"></i> Working Hours</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="fe fe-phone fs-16"></i> Connected Calls</p>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					
					
					
					<div class="col-sm-4" id="col-yesterday">
						<div class="card">
						    <div class="card-body ps-2 pe-2">
							    <h4 class="page-title mb-3">Yesterday &nbsp; <span style="font-size:15px; font-weight:400;" id="yesterday-label">30-Dec-2025</span></h4>
								<div class="row">
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="ti ti-phone fs-16"></i> Total Calls</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="ti ti-phone fs-16"></i> Calls Duration</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-success"><i class="fe fe-phone-incoming fs-16"></i>&nbsp; Incomming</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="fe fe-phone-incoming fs-16"></i>&nbsp; Incomming Mins.</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-secondary"><i class="fe fe-phone-outgoing fs-16"></i>&nbsp; Outgoing</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-secondary"><i class="fe fe-phone-outgoing fs-16"></i>&nbsp; Outgoing Mins.</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-danger"><i class="fe fe-phone-missed fs-16"></i>&nbsp; Missed</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-primary"><i class="fe fe-slash fs-16"></i>&nbsp; Rejected</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-primary"><i class="fe fe-phone-off fs-16"></i>&nbsp; Never Attended</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-primary"><i class="fe fe-phone-off fs-16"></i>&nbsp; Not Pickup</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="fe fe-user fs-16"></i> Unique Clients</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="fa-regular fa-clock fs-16"></i> Working Hours</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="fe fe-phone fs-16"></i> Connected Calls</p>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					
					<div class="col-sm-4" id="col-last-week">
						<div class="card">
						    <div class="card-body ps-2 pe-2">
							    <h4 class="page-title mb-3">Last Week &nbsp; <span style="font-size:15px; font-weight:400;" id="last-week-label">22-Dec-2025 to 28-Dec-2025</span></h4>
								<div class="row">
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="ti ti-phone fs-16"></i> Total Calls</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="ti ti-phone fs-16"></i> Calls Duration</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-success"><i class="fe fe-phone-incoming fs-16"></i>&nbsp; Incomming</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="fe fe-phone-incoming fs-16"></i>&nbsp; Incomming Mins.</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-secondary"><i class="fe fe-phone-outgoing fs-16"></i>&nbsp; Outgoing</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-secondary"><i class="fe fe-phone-outgoing fs-16"></i>&nbsp; Outgoing Mins.</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-danger"><i class="fe fe-phone-missed fs-16"></i>&nbsp; Missed</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-primary"><i class="fe fe-slash fs-16"></i>&nbsp; Rejected</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-primary"><i class="fe fe-phone-off fs-16"></i>&nbsp; Never Attended</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13 text-primary"><i class="fe fe-phone-off fs-16"></i>&nbsp; Not Pickup</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="fe fe-user fs-16"></i> Unique Clients</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="fa-regular fa-clock fs-16"></i> Working Hours</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-sm-6 col-6">
										<div class="card">
											<div class="card-body p-2">
												<div class="d-flex align-items-center justify-content-between">
													<div>
														<h2 class="mb-1">0</h2>
														<p class="fs-13"><i class="fe fe-phone fs-16"></i> Connected Calls</p>
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let isCustomRangeActive = false;
        let customStart = null;
        let customEnd = null;

        // Load data on page load
        loadDashboardData();

        // On User change
        $('#user-filter').on('change', function() {
            if (isCustomRangeActive && customStart && customEnd) {
                loadDashboardData(customStart, customEnd);
            } else {
                loadDashboardData();
            }
        });

        // On Date change
        // Assuming bookingrange is using daterangepicker
        $('.bookingrange').on('apply.daterangepicker', function(ev, picker) {
            isCustomRangeActive = true;
            customStart = picker.startDate.format('YYYY-MM-DD');
            customEnd = picker.endDate.format('YYYY-MM-DD');
            loadDashboardData(customStart, customEnd);
        });

        // Add clear date event if needed
        $('.bookingrange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            isCustomRangeActive = false;
            customStart = null;
            customEnd = null;
            loadDashboardData();
        });

        function populateColumn(colId, data) {
            const col = document.getElementById(colId);
            if (!col || !data) return;
            const h2s = col.querySelectorAll('.card-body .row .col-sm-6 h2');
            
            // Map the stats to the correct h2 index
            const statsOrder = [
                data.total_calls,
                data.calls_duration,
                data.incoming,
                data.incoming_duration,
                data.outgoing,
                data.outgoing_duration,
                data.missed,
                data.rejected,
                data.never_attended,
                data.not_pickup,
                data.unique_clients,
                data.working_hours,
                data.connected_calls
            ];

            h2s.forEach((h2, index) => {
                if(statsOrder[index] !== undefined) {
                    h2.innerText = statsOrder[index];
                }
            });
        }

        function loadDashboardData(dateFrom = null, dateTo = null) {
            let userId = document.getElementById('user-filter').value;
            let url = "{{ route('call.dashboard.data', ['box_slug' => request()->route('box_slug')]) }}?user_id=" + userId;
            
            if (dateFrom && dateTo) {
                url += "&date_from=" + dateFrom + "&date_to=" + dateTo;
            }

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.custom) {
                        // Custom range - Hide yesterday/lastweek, expand today
                        document.getElementById('col-yesterday').style.display = 'none';
                        document.getElementById('col-last-week').style.display = 'none';
                        document.getElementById('col-today').className = 'col-sm-12';
                        
                        document.querySelector('#col-today h4').innerHTML = `Custom Range &nbsp; <span style="font-size:15px; font-weight:400;">${dateFrom} to ${dateTo}</span>`;
                        populateColumn('col-today', data.custom);

                    } else if (data.today) {
                        // Default view
                        document.getElementById('col-yesterday').style.display = 'block';
                        document.getElementById('col-last-week').style.display = 'block';
                        document.getElementById('col-today').className = 'col-sm-4';

                        document.querySelector('#col-today h4').innerHTML = `Today &nbsp; <span style="font-size:15px; font-weight:400;">${data.today_label}</span>`;
                        document.getElementById('yesterday-label').innerText = data.yesterday_label;
                        document.getElementById('last-week-label').innerText = data.last_week_label;

                        populateColumn('col-today', data.today);
                        populateColumn('col-yesterday', data.yesterday);
                        populateColumn('col-last-week', data.last_week);
                    }
                })
                .catch(error => console.error("Error fetching call dashboard data:", error));
        }
    });
</script>
@endsection