@extends('layouts.main')
@section('title', 'WhatsApp Dashboard')
@section('content')


<div class="page-wrapper">
			<div class="content" id="whatsapp-integration-content">

				<!-- <div class="row">
					<div class="col-md-12">
						<div class="page-header">
							<div class="row align-items-center ">
								<div class="col-md-4">
									<h3 class="page-title">Dashboard</h3>
								</div>
								<div class="col-md-8 float-end ms-auto">
									<div class="d-flex title-head">
										<div class="daterange-picker d-flex align-items-center justify-content-center">
											<div class="form-sort me-2">
												<i class="ti ti-calendar"></i>
												<input type="text" class="form-control  date-range bookingrange">
											</div>
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
				</div> -->
				
				
				
				
				
				
				
				<!-- Breadcrumb -->
				 <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
					 <div class="my-auto mb-2">
						 <h2 class="mb-1">ZipZapGo </h2>
                         @if(session('success'))
                            <div class="alert alert-success mt-2 py-1 px-2 small">
                                <strong>Success!</strong> {{ session('success') }}
                            </div>
                         @endif
					 </div>
					  <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
					  
					    <div class="d-flex align-items-center flex-wrap mb-1" id="wa-status-container">
						     <span class="me-2 ac-con fw-medium">WhatsApp Business API Status : 
                                 <span id="wa-status-badge" class="badge @if(count($accounts) > 0) bg-success @else bg-warning @endif">
                                    @if(count($accounts) > 0) 
                                        {{ count($accounts) }} ACCOUNT{{ count($accounts) > 1 ? 'S' : '' }} CONNECTED 
                                    @else 
                                        PENDING 
                                    @endif
                                 </span>
                             </span>
                             @if(count($accounts) > 0)
                             <button type="button" id="btnSyncMeta" onclick="syncMetaAccount()" class="btn btn-sm btn-outline-primary me-2" title="Re-sync Account from Meta">
                                <i class="ti ti-refresh-dot me-1"></i> Re-Sync Meta
                             </button>
                             <button type="button" id="btnCheckHealth" class="btn btn-sm btn-outline-info me-2" title="Refresh Live Status">
                                <i class="ti ti-refresh me-1"></i> Check Health
                             </button>
                             @endif
							 <a href="javascript:void(0);" onclick="launchWhatsAppLogin()" class="btn bg-success btn-sm me-2 btn-connect-wa"><i class="ti ti-plug-connected me-1"></i>Add Account </a>
                             @if($whatsappConnected)
                             <a href="javascript:void(0);" onclick="confirmDisconnect()" class="btn bg-danger btn-sm me-2"><i class="ti ti-plug-off me-1"></i>Disconnect</a>
                             @endif
							 <span class="me-2 ms-3 fw-medium plan-bt">Plan : <span class="badge bg-purple">FREE</span></span>
						 </div>
					  
					  
						<!--  <div class="me-2 mb-2">
							 <div class="dropdown">
								 <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
									 <i class="ti ti-file-export me-1"></i>Export
								 </a>
								 <ul class="dropdown-menu  dropdown-menu-end p-3">
									 <li>
										 <a href="javascript:void(0);" class="dropdown-item rounded-1"><i class="ti ti-file-type-pdf me-1"></i>Export as PDF </a>
									 </li>
									 <li>
										 <a href="javascript:void(0);" class="dropdown-item rounded-1"><i class="ti ti-file-type-xls me-1"></i>Export as Excel  </a>
									 </li>
								 </ul>
							 </div>
						 </div>
						 <div class="mb-2">
							 <div class="input-icon w-120 position-relative">
								 <span class="input-icon-addon">
									 <i class="ti ti-calendar text-gray-9"></i>
								 </span>
								 <input type="text" class="form-control yearpicker" value="2025" />
							 </div>
						 </div> -->
						 <div class="ms-2 head-icons">
							 <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
								 <i class="ti ti-chevrons-up"></i>
							 </a>
						 </div>
					 </div> 
				 </div>
				 <!-- /Breadcrumb -->

				 <!-- Welcome Wrap -->
				<div class="card border-0" style="background:#23cc62;">
					<div class="card-body pb-1">
						 <div class="d-flex align-items-center mb-3">
							 <span class="avatar avatar-xl flex-shrink-0">
								 <img src="{{ asset('assets/img/profiles/avatar-31.jpg') }}" class="rounded-circle" alt="img" />
							 </span>
							 <div class="ms-3">
								 <h3 class="text-white">Hey {{ Auth::user()->first_name ?? Auth::user()->name }}, Welcome to ZipZapGo  </h3>
								 <!-- <p>You have  <span class="text-primary text-decoration-underline">21 </span> Pending Approvals &  <span class="text-primary text-decoration-underline">14 </span> Leave Requests </p> -->
							 </div>
						 </div>
                         
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex align-items-center flex-wrap mb-1">
                                <a href="#" class="btn btn-light btn-md me-2 mb-2" data-bs-toggle="modal" data-bs-target="#add_project"><i class="ti ti-square-rounded-plus me-1"></i>Schedule Live Demo </a>
                                <a href="#" class="btn btn-light btn-md me-2 mb-2" data-bs-toggle="modal" data-bs-target="#add_project"><i class="ti ti-square-rounded-plus me-1"></i>Setup Guide </a>
                                <a href="#" class="btn btn-light btn-md mb-2" data-bs-toggle="modal" data-bs-target="#add_leaves"><i class="ti ti-square-rounded-plus me-1"></i>Watch Tutorials </a>
                            </div>
                            <div class="mb-1">
                <input type="text" class="MyDatePicker form-control" placeholder="MM/DD/YYYY" readonly>
                            </div>  
                            

                        </div>
						 
					</div>









				 </div>
				 <!-- /Welcome Wrap -->
				
                @if(str_contains(config('app.url'), 'localhost'))
				<div class="alert alert-warning d-flex align-items-center mb-4 border-warning shadow-sm" role="alert">
					<i class="ti ti-alert-triangle me-3 fs-24 text-warning"></i>
					<div class="flex-grow-1">
						<h6 class="alert-heading mb-1 fw-bold text-dark">Local Environment Detected</h6>
						<p class="mb-2 small">Your APP_URL is set to <code>{{ config('app.url') }}</code>. Meta (Facebook) cannot send real-time webhooks to a local address.</p>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-dark py-1" data-bs-toggle="modal" data-bs-target="#fixWebhookModal">
                                <i class="ti ti-tool me-1"></i> How to Fix This
                            </button>
                            <a href="{{ box_route('whatsapp.tester.prefixed') }}" class="btn btn-sm btn-outline-dark py-1">
                                <i class="ti ti-test-pipe me-1"></i> Use Local Simulator
                            </a>
                        </div>
					</div>
				</div>
                @endif
				
				
				
				
				
				
				
				
				
				
				
				<!-- Health & Connectivity Row -->
				<div class="row" id="health-monitor-row" style="display: none;">
					<div class="col-12">
						<div class="card border-info shadow-sm mb-4">
							<div class="card-header bg-soft-info d-flex justify-content-between align-items-center">
								<div class="card-title mb-0">
									<i class="ti ti-activity me-1"></i> Live Connection & Webhook Health Monitor
								</div>
								<button type="button" class="btn-close" onclick="$('#health-monitor-row').slideUp()"></button>
							</div>
							<div class="card-body">
								<div id="health-results-container" class="row">
									<!-- Results injected via JS -->
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /Health & Connectivity Row -->

				<!-- WhatsApp Stats Row -->
				<div class="row">
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card flex-fill">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between">
									<div class="stats-info">
										<p class="mb-1 text-gray-9">Total Sent Messages</p>
										<h4 class="mb-0">{{ number_format($totalSent ?? 0) }}</h4>
									</div>
									<div class="stats-icon bg-info-light">
										<i class="ti ti-send fs-30 text-info"></i>
									</div>
								</div>
								<div class="d-flex align-items-center justify-content-between mt-3">
									<p class="mb-0 fs-12 text-muted">Successful Broadcasts</p>
									<span class="badge bg-success-light text-success fs-10">+{{ number_format($totalSent ?? 0) }}</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card flex-fill">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between">
									<div class="stats-info">
										<p class="mb-1 text-gray-9">Incoming Messages</p>
										<h4 class="mb-0">{{ number_format($totalReceived ?? 0) }}</h4>
									</div>
									<div class="stats-icon bg-success-light">
										<i class="ti ti-message-2-down fs-30 text-success"></i>
									</div>
								</div>
								<div class="d-flex align-items-center justify-content-between mt-3">
									<p class="mb-0 fs-12 text-muted">Direct Replies</p>
									<span class="badge bg-success-light text-success fs-10">+{{ number_format($totalReceived ?? 0) }}</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card flex-fill">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between">
									<div class="stats-info">
										<p class="mb-1 text-gray-9">Active Chats</p>
										<h4 class="mb-0">{{ number_format($totalChats ?? 0) }}</h4>
									</div>
									<div class="stats-icon bg-warning-light">
										<i class="ti ti-users-group fs-30 text-warning"></i>
									</div>
								</div>
								<div class="d-flex align-items-center justify-content-between mt-3">
									<p class="mb-0 fs-12 text-muted">Unique Leads</p>
									<span class="badge bg-warning-light text-warning fs-10">+{{ number_format($totalChats ?? 0) }}</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card flex-fill">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between">
									<div class="stats-info">
										<p class="mb-1 text-gray-9">AI Response Credits</p>
										<h4 class="mb-0">{{ number_format($box->ai_credits ?? 0) }}</h4>
									</div>
									<div class="stats-icon bg-danger-light">
										<i class="ti ti-robot fs-30 text-danger"></i>
									</div>
								</div>
								<div class="d-flex align-items-center justify-content-between mt-3">
									<p class="mb-0 fs-12 text-muted">Left in Wallet</p>
									<span class="badge bg-danger-light text-danger fs-10">Low</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card flex-fill">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between">
									<div class="stats-info">
										<p class="mb-1 text-gray-9">Daily Limit Left</p>
										<h4 class="mb-0">{{ is_numeric($limitRemaining ?? 'Unknown') ? number_format($limitRemaining) : ($limitRemaining ?? 'Unknown') }}</h4>
									</div>
									<div class="stats-icon bg-purple-light">
										<i class="ti ti-chart-pie fs-30 text-purple"></i>
									</div>
								</div>
								<div class="d-flex align-items-center justify-content-between mt-3">
									<p class="mb-0 fs-12 text-muted">Total Limit: {{ is_numeric($dailyLimit ?? 'Unknown') ? number_format($dailyLimit) : ($dailyLimit ?? 'Unknown') }}</p>
									<span class="badge bg-purple-light text-purple fs-10">Resets Daily</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /WhatsApp Stats Row -->

				<div class="row">
				
				    <div class="col-md-7">
                        @if(count($accounts) > 0)
                        <!-- Connected Accounts List -->
					    <div class="card mb-4 shadow-sm border-success">
							<div class="card-header justify-content-between d-flex align-items-center bg-soft-success">
								<div class="card-title mb-0">
									<span class="avatar rounded-circle bg-success text-white mb-0 me-2">
											 <i class="fa-brands fa-whatsapp fs-16"></i>
										 </span> Active WhatsApp Account
								</div>
                                <div class="d-flex gap-2">
                                    @if(count($allAccounts) > 1)
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#selectAccountModal">
                                        <i class="ti ti-switch-horizontal me-1"></i> Switch Account
                                    </button>
                                    @endif
                                    <a href="javascript:void(0);" onclick="launchWhatsAppLogin()" class="btn btn-sm btn-outline-primary"><i class="ti ti-plus me-1"></i> Add Another</a>
                                </div>
							</div>
							<div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover table-nowrap mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>WABA Account</th>
                                                <th><i class="ti ti-coin me-1"></i> Currency</th>
                                                <th>Phone Numbers</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($accounts as $account)
                                            @php
                                                $isActive = $account->is_active;
                                            @endphp
                                            <tr class="{{ $isActive ? 'bg-soft-success-light' : '' }}">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="ms-2">
                                                            <div class="fw-bold text-dark">
                                                                {{ $account->name }} 
                                                                @if($isActive)
                                                                    <span style="display:none;" class="badge bg-success ms-1">ACTIVE</span>
                                                                @endif
                                                            </div>
                                                            <small class="text-muted d-block">WABA ID: {{ $account->waba_id }}</small>
                                                            <!-- @if($account->currency)
                                                                <small class="text-muted d-block mt-1">
                                                                    <i class="ti ti-coin me-1"></i> Currency: <span class="fw-bold text-dark">{{ $account->currency }}</span>
                                                                </small>
                                                            @endif -->
                                                            @if(!empty($box->whatsapp_token['business_name']) || $box->whatsapp_business_portfolio_id)
                                                                <div class="small text-muted mt-1">
                                                                    <i class="ti ti-briefcase me-1"></i> Portfolio: 
                                                                    <span class="fw-medium text-secondary">{{ $box->whatsapp_token['business_name'] ?? 'ID: '.$box->whatsapp_business_portfolio_id }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                   @if($account->currency)
                                                                <small class="text-muted d-block mt-1 text-center">
                                                                    <span class="fw-bold text-dark">{{ $account->currency }}</span>
                                                                </small>
                                                    @endif

                                                </td>
                                                <td>
                                                    @php
                                                        $phones = $phoneNumbers->where('target_id', $account->id);
                                                    @endphp
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @forelse($phones as $phone)
                                                            @php
                                                                $isPending = $phone->status !== 'CONNECTED';
                                                            @endphp
                                                            <div class="d-flex flex-column gap-1">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <span class="badge bg-info text-white" data-bs-toggle="tooltip" title="ID: {{ $phone->whatsapp_id }} | Status: {{ $phone->status }}">
                                                                        <i class="ti ti-phone me-1"></i> {{ $phone->display_phone_number }}
                                                                    </span>
                                                                    <a href="javascript:void(0)" onclick="disconnectSingle('{{ $account->waba_id }}', '{{ $phone->whatsapp_id }}')" class="text-danger ms-2" title="Remove this number">
                                                                        <i class="ti ti-circle-x fs-16"></i>
                                                                    </a>
                                                                </div>
                                                                @if($isPending)
                                                                    <a href="https://business.facebook.com/wa/manage/phone-numbers/?waba_id={{ $account->waba_id }}" target="_blank" class="btn btn-xs btn-outline-warning py-0 px-1" style="font-size: 10px;">
                                                                        <i class="ti ti-external-link"></i> Complete Verification
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        @empty
                                                            <small class="text-muted">No numbers linked</small>
                                                        @endforelse
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge @if($account->account_status == 'ACTIVE') bg-success text-success @else bg-warning-light text-warning @endif">
                                                        <i class="ti ti-circle-filled fs-10 me-1"></i> {{ $account->account_status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a href="{{ box_route('whatsapp.create_template') }}?waba_id={{ $account->waba_id }}" class="btn btn-sm btn-primary"><i class="ti ti-plus me-1"></i> Create Template</a>
                                                        @if(!$isActive)
                                                            <button onclick="selectActiveAccount('{{ $account->waba_id }}', '{{ $phones->first()->whatsapp_id ?? '' }}')" class="btn btn-sm btn-outline-success">
                                                                <i class="ti ti-check me-1"></i> Activate
                                                            </button>
                                                        @else
                                                            <button onclick="deactivateAccount('{{ $account->waba_id }}', '{{ $phones->first()->whatsapp_id ?? '' }}')" class="btn btn-sm btn-outline-warning">
                                                                <i class="ti ti-x me-1"></i> Deactivate
                                                            </button>
                                                        @endif
                                                        <a href="javascript:void(0);" onclick="disconnectAccount('{{ $account->waba_id }}', '{{ $account->name }}')" class="btn btn-sm btn-outline-danger">
                                                            <i class="ti ti-trash me-1"></i> Disconnect
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
							</div>
						</div>
                        @endif

					    <div class="card">
							<div class="card-header justify-content-between">
								<div class="card-title">
									<span class="avatar rounded-circle bg-success mb-2">
											 <i class="fa-brands fa-whatsapp fs-16"></i>
										 </span> Setup FREE WhatsApp Business Account
								</div>
							</div>
							<div class="card-body">
								<div class="accordion accordion-solid-success" id="accordionWarningSolidExample">
									<div class="accordion-item">
										<h2 class="accordion-header" id="headingWarningSolidOne">
											<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseWarningSolidOne" aria-expanded="true" aria-controls="collapseWarningSolidOne">
												<i class="fa fa-check-circle me-2" aria-hidden="true"></i> WhatsApp API Configuration
											</button>
										</h2>
										<div id="collapseWarningSolidOne" class="accordion-collapse collapse show" aria-labelledby="headingWarningSolidOne" data-bs-parent="#accordionWarningSolidExample">
											<div class="accordion-body text-dark">
                                                @if(count($accounts) == 0)
												<div class="text-center p-4">
													<h5 class="mb-3">Click on Continue With Facebook to apply for WhatsApp Business API</h5>
													<p class="mb-2 fs-15">Requirements to apply for WhatsApp Business API</p>
													<p class="mb-2 fs-15">• A Registered Business & Working Website.</p>
													<p class="fs-15"><a class="text-primary" href="#"><i class="fa-solid fa-book"></i> How to apply for WhatsApp Business API?</a></p>
													<img style="border-radius:10px;" src="{{ asset('assets/img/api-video.jpg') }}">
													<div class="d-flex align-items-center flex-wrap mb-2 mt-3 justify-content-center">
														 <a href="javascript:void(0);" onclick="launchWhatsAppLogin()" class="btn btn-md text-light me-2 btn-connect-wa" style="background:#1877F2;"><i class="fa-brands fa-facebook-f me-1"></i> Continue With Facebook </a>
													 </div>
												</div>
                                                @else
                                                <div class="text-center p-3">
                                                    <div class="avatar avatar-lg bg-success-light rounded-circle mb-2">
                                                        <i class="ti ti-check fs-20 text-success"></i>
                                                    </div>
                                                    <h6>WhatsApp API is Integrated</h6>
                                                    <p class="text-muted small">You have successfully connected {{ count($accounts) }} account(s).</p>
                                                    <a href="javascript:void(0);" onclick="launchWhatsAppLogin()" class="btn btn-sm btn-primary">Connect Another Portfolio</a>
                                                </div>
                                                @endif


											</div>
										</div>
									</div>
									<div class="accordion-item ">
										<h2 class="accordion-header" id="headingWarningSolidTwo">
											<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseWarningSolidTwo" aria-expanded="false" aria-controls="collapseWarningSolidTwo">
												<i class="fa-solid fa-user me-2"></i> Increase your messaging limit & get your display name approved
											</button>
										</h2>
										<div id="collapseWarningSolidTwo" class="accordion-collapse collapse" aria-labelledby="headingWarningSolidTwo" data-bs-parent="#accordionWarningSolidExample">
											<div class="accordion-body text-dark">
												<p class="mb-2 fs-15">Complete your KYC to boost your messaging limit to 2000 and display name approval</p>
												<p class="mb-2 fs-15">• Legal/Trade Name on GST Certificate and Facebook Business Manager should match</p>
												<p class="mb-2 fs-15">• Ensure you have an active website prior to applying for KYC</p>
												<p class="mb-2 fs-15">• Use director's Aadhaar card listed on your GST document</p>
												<p class="mb-2 fs-15">For more details please refer to help doc.</p>
												<div class="d-flex align-items-center flex-wrap mb-2 mt-3">
													 <a href="#" class="btn btn-light btn-md me-2" data-bs-toggle="modal" data-bs-target="#kyc_modal">Start KYC </a>
													 
												 </div>
											</div>
										</div>
									</div>
									<div class="accordion-item">
										<h2 class="accordion-header" id="headingWarningSolidThree">
											<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseWarningSolidThree" aria-expanded="false" aria-controls="collapseWarningSolidThree">
												<i class="fa-regular fa-user me-2"></i> Setup Your Profile
											</button>
										</h2>
										<div id="collapseWarningSolidThree" class="accordion-collapse collapse" aria-labelledby="headingWarningSolidThree" data-bs-parent="#accordionWarningSolidExample">
											<div class="accordion-body text-dark">
												<p class="mb-2 fs-15">Customize your WhatsApp profile, to help understand your customers well.</p>
												<p class="fs-15"><a class="text-primary" href="#"><i class="fa-solid fa-book"></i> How to Edit Business Profile?</a></p>
												<div class="d-flex align-items-center flex-wrap mb-2 mt-3">
													 <a href="#" class="btn btn-light btn-md me-2" data-bs-toggle="modal" data-bs-target="#edit_profile_modal" onclick="openEditProfileModal()">Edit Profile </a>
													 
												 </div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						
						
						
						
						
						
						
						
						
						
						<div class="col-md-12 d-flex">
						        
								 <div class="card flex-fill">
								    <div class="card-header justify-content-between">
								<div class="card-title">
									<span class="avatar rounded-circle bg-primary">
											 <i class="fa fa-line-chart fs-16" aria-hidden="true"></i>
										 </span> Setup account to run Ads
								</div>
							</div>
									 <div class="card-body">

										 <div class="d-flex align-items-center justify-content-between">
											 <div class="">
												 <h6 class="fs-15 fw-bold text-default mb-3 callout callout-info">Setup your Meta ad account to run ads and reach customer </h6>
											 </div>
											 <div class="">
											  <a class="btn btn-danger btn-md" href="#">Setup </a>
											 </div>
										 </div>
									 </div>
								 </div>
						</div>
						
						
						
						<div class="col-md-12 d-flex">
						        
								 <div class="card flex-fill">
								    <div class="card-header justify-content-between">
										<div class="card-title">
											<span class="avatar rounded-circle bg-primary">
													 <i class="fa-solid fa-book fs-16"></i>
												 </span> Platform Walkthrough & Tutorials
										</div>
									</div>
									 <div class="card-body">
									    
										<div class="row mb-4">
										    <div class="col-md-6">
											    <img src="{{ asset('assets/img/profiles/avatar-02.jpg') }}" alt="User Avatar">
											</div>
										</div>
										<div class="row mb-3">
											<div class="col-md-6">
											    <h5 class="mb-3">Template</h5>
												<ul>
												<li class="mb-1"><a href="#"><i class="fa-solid fa-book me-1"></i> How to Create WhatsApp Template Message?</a></li>
												<li class="mb-1"><a href="#"><a href="#"><i class="fa-solid fa-book me-1"></i> Use chatbot parameters for leads</a></li>
												<li class="mb-1"><a href="#"><i class="fa-solid fa-book me-1"></i> Add Quick Reply to WhatsApp Template Message</a></li>
												<li class="mb-1"><a href="#"><i class="fa-solid fa-book me-1"></i> Message formatting guideline (Bold, Italic & more)</a></li>
												</ul>
											</div>
											<div class="col-md-6">
											    <h5 class="mb-3">Campaign</h5>
												<ul>
												<li class="mb-1"><a href="#"><i class="fa-solid fa-book me-1"></i> Audience segregation for WhatsApp Broadcast</a></li>
												<li class="mb-1"><a href="#"><a href="#"><i class="fa-solid fa-book me-1"></i> Upgrade WhatsApp Tier Limit</a></li>
												<li class="mb-1"><a href="#"><i class="fa-solid fa-book me-1"></i> Import upto 2 lakh contacts in one go</a></li>
												
												</ul>
											</div>
											
										</div>
										<div class="row mt-5">
											<div class="col-md-6">
											    <h5 class="mb-3">Live Chat & Attribute</h5>
												<ul>
												<li class="mb-1"><a href="#"><i class="fa-solid fa-book me-1"></i> Add user attributes manually</a></li>
												<li class="mb-1"><a href="#"><a href="#"><i class="fa-solid fa-book me-1"></i> Add/Remove Tag & update attribute</a></li>
												<li class="mb-1"><a href="#"><i class="fa-solid fa-book me-1"></i> Send & Generate media link</a></li>
												<li class="mb-1"><a href="#"><i class="fa-solid fa-book me-1"></i> Chat Profile & First Message Tag</a></li>
												<li class="mb-1"><a href="#"><i class="fa-solid fa-book me-1"></i> How to create & add tags to contacts</a></li>
												</ul>
											</div>
											<div class="col-md-6">
											    <h5 class="mb-3">Chatbot & Integration</h5>
												<ul>
												<li class="mb-1"><a href="#"><i class="fa-solid fa-book me-1"></i> Setup Welcome WhatsApp Chatbot</a></li>
												<li class="mb-1"><a href="#"><a href="#"><i class="fa-solid fa-book me-1"></i> Create WhatsApp Button for Free</a></li>
												<li class="mb-1"><a href="#"><i class="fa-solid fa-book me-1"></i> Create WhatsApp Link for Free</a></li>
											
												</ul>
											</div>
											
										</div>
										
										
									 </div>
								 </div>
						</div>
						
						
						
					  
								

					
					
					
					
					
					</div>
					<div class="col-md-5">
					   <div class="row flex-fill">
							<div class="col-md-12 d-flex">
								 <div class="card flex-fill">
									 <div class="card-body">

										 <div class="d-flex align-items-center justify-content-between">
											 <div class="">
												 <h6 class="fs-15 fw-medium text-default mb-3">Advertisement Credits </h6>
												 <h3 class="mb-0"><span>₹</span> 5,000</h3>
											 </div>
											 <div class="">
											  <a class="btn btn-light btn-sm" href="#">Buy Credits </a>
											 </div>
										 </div>
									 </div>
								 </div>
							</div>
							
							<div class="col-md-12 d-flex">
								 <div class="card flex-fill">
									 <div class="card-body">

										 
											 <div class="">
												 <h6 class="fs-15 fw-medium text-default mb-3">Free Service Conversation </h6>
											
									
											<p class="fs-12 fw-semibold mb-0 text-muted">0<span class="float-end fs-10 fw-normal">Unlimited</span></p>
											<div class="progress progress-xs mb-4 progress-animate" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
												<div class="progress-bar bg-primary" style="width: 100%"></div>
											</div>
											
									
									
								
											 </div>
											 <div class="d-flex align-items-center justify-content-between">
											 <div class="">
												 <h6 class="fs-15 fw-medium text-default mb-3">WhatsApp Conversation Credits (WCC) </h6>
												 <h3 class="mb-0"><span>₹</span> 50.00</h3>
											 </div>
											 <div class="">
											  <a class="btn bg-success btn-sm" href="#">Buy More </a>
											 </div>
										 </div>
									 </div>
								 </div>
							</div>
							
							
							<div class="col-md-12 d-flex">
								 <a href="{{ box_route('whatsapp.link_generator') }}" class="card flex-fill text-decoration-none">
									 <div class="card-body text-dark">
										 <span class="avatar rounded-circle bg-success mb-2">
											 <i class="fa-solid fa-share fs-16 text-white"></i>
										 </span>
										 <h6 class="fs-18 fw-medium text-default mb-1"><strong>Customize WhatsApp Link</strong></h6>
										 <p class="fs-16">Create shareable links & QR for your WA business number </p>
									 </div>
								 </a>
							 </div>
							 
							 <div class="col-md-12 d-flex">
								 <a href="{{ box_route('whatsapp.button_generator') }}" class="card flex-fill text-decoration-none">
									 <div class="card-body text-dark">
										 <span class="avatar rounded-circle bg-success mb-2">
											 <i class="fa-brands fa-whatsapp fs-16 text-white"></i>
										 </span>
										 <h6 class="fs-18 fw-medium text-default mb-1"><strong>WhatsApp Website Button</strong></h6>
										 <p class="fs-16">Drive WhatsApp sales with personalised CTAs </p>
									 </div>
								 </a>
							 </div>
							 
							 <!-- Developer API Integration -->
							 <div class="col-md-12 d-flex">
								 <div class="card shadow-sm border-0 bg-white mb-4">
									 <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
										 <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
											 <div class="avatar bg-danger text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
												 <i class="ti ti-code fs-4"></i>
											 </div>
											 Developer API
										 </h5>
										 <div>
											 <button type="button" class="btn btn-sm btn-outline-danger me-2" onclick="manageApiTemplates()">
												 <i class="ti ti-layout-grid-add me-1"></i> Templates
											 </button>
											 <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#apiDocsModal">
												 <i class="ti ti-file-text me-1"></i> Guidelines
											 </button>
										 </div>
									 </div>
									 <div class="card-body">
										 <div class="mb-3">
											 <label class="form-label text-dark fw-medium">API Token (Bearer)</label>
											 <div class="input-group">
												 <input type="password" class="form-control" id="apiTokenInput" value="{{ $box->whatsapp_api_token ?? 'Not Generated Yet' }}" readonly>
												 <button class="btn btn-outline-secondary" type="button" onclick="toggleTokenVisibility()">
													 <i class="ti ti-eye" id="tokenEyeIcon"></i>
												 </button>
												 <button class="btn btn-primary" type="button" onclick="generateApiToken()">
													 <i class="ti ti-refresh"></i> Generate
												 </button>
											 </div>
											 <small class="text-muted mt-1 d-block">Use this token to authenticate your API requests.</small>
										 </div>
										 
										 <div class="mb-2">
											 <label class="form-label text-dark fw-medium">Webhook Forwarding URL</label>
											 <div class="input-group">
												 <input type="url" class="form-control" id="webhookUrlInput" placeholder="https://your-domain.com/webhook" value="{{ $box->whatsapp_webhook_url }}">
												 <button class="btn btn-success" type="button" onclick="saveWebhookUrl()">
													 <i class="ti ti-device-floppy"></i> Save
												 </button>
											 </div>
											 <div class="d-flex justify-content-between align-items-center mt-2">
												 <small class="text-muted">We will forward incoming messages to this URL.</small>
												 <button type="button" class="btn btn-sm btn-outline-info" onclick="viewRecentWebhooks()">
													 <i class="ti ti-history me-1"></i> View Recent Payloads
												 </button>
											 </div>
										 </div>
										 
										 <hr class="my-4">
										 
										 <div class="mb-2">
											 <label class="form-label text-dark fw-medium mb-2">Available API Endpoints</label>
											 
											 <div class="d-flex align-items-center mb-2">
												 <span class="badge bg-primary me-2" style="min-width: 50px;">POST</span>
												 <div class="input-group input-group-sm">
													 <input type="text" class="form-control text-muted" value="{{ url('/api/v1/whatsapp/send') }}" readonly>
													 <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ url('/api/v1/whatsapp/send') }}')" title="Copy URL">
														 <i class="ti ti-copy"></i>
													 </button>
												 </div>
											 </div>
											 <small class="text-muted d-block mb-3 ms-5 ps-2">Use this to send WhatsApp text and template messages.</small>

											 <div class="d-flex align-items-center mb-2">
												 <span class="badge bg-success me-2" style="min-width: 50px;">GET</span>
												 <div class="input-group input-group-sm">
													 <input type="text" class="form-control text-muted" value="{{ url('/api/v1/whatsapp/messages') }}" readonly>
													 <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ url('/api/v1/whatsapp/messages') }}')" title="Copy URL">
														 <i class="ti ti-copy"></i>
													 </button>
												 </div>
											 </div>
											 <small class="text-muted d-block ms-5 ps-2">Use this to fetch the history of incoming and outgoing messages.</small>
										 </div>
									 </div>
								 </div>
							 </div>
							 <!-- /Developer API Integration -->
							
							
							
							
							
							
							
							
							
							
							
						</div>
					</div>
					
					
					
					
					
					

				</div>
			</div>
		</div>

<!-- /Select Account Modal -->

<!-- Fix Webhook Modal -->
<div class="modal fade" id="fixWebhookModal" tabindex="-1" aria-labelledby="fixWebhookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="fixWebhookModalLabel"><i class="ti ti-broadcast me-2"></i>Setting up Webhooks for Local Development</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted">Meta requires a public <strong>HTTPS</strong> URL to send message notifications. Since you are on localhost, you need a tunnel.</p>
                
                <div class="mb-4">
                    <h6 class="fw-bold mb-2"><span class="badge bg-primary me-2">Step 1</span> Install Ngrok</h6>
                    <p class="small mb-2">If you haven't already, download and install <a href="https://ngrok.com/download" target="_blank">Ngrok</a>.</p>
                    <div class="bg-light p-2 rounded border">
                        <code class="text-dark">brew install ngrok/ngrok/ngrok</code> <small class="text-muted">(Mac)</small>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-2"><span class="badge bg-primary me-2">Step 2</span> Start a Tunnel</h6>
                    <p class="small mb-2">Run this command in your terminal to start a tunnel to your local server (default XAMPP port is usually 80 or 8080):</p>
                    <div class="bg-light p-2 rounded border position-relative">
                        <code class="text-dark" id="ngrokCmd">ngrok http 80</code>
                        <button class="btn btn-xs btn-outline-secondary position-absolute end-0 top-50 translate-middle-y me-2" onclick="copyToClipboard('ngrok http 80')">Copy</button>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-2"><span class="badge bg-primary me-2">Step 3</span> Update your .env File</h6>
                    <p class="small mb-2">Copy the <strong>Forwarding</strong> URL from Ngrok (e.g., <code>https://your-id.ngrok-free.app</code>) and update your <code>.env</code> file:</p>
                    <div class="bg-light p-3 rounded border">
                        <div class="text-danger"># Open .env and find APP_URL</div>
                        <code class="text-dark">APP_URL=https://your-id.ngrok-free.app</code>
                    </div>
                </div>

                <div class="mb-0">
                    <h6 class="fw-bold mb-2"><span class="badge bg-primary me-2">Step 4</span> Refresh Cache & Sync</h6>
                    <p class="small mb-2">Run this command and then click **Sync Webhook** in the Health Monitor:</p>
                    <div class="bg-light p-2 rounded border">
                        <code class="text-dark">php artisan optimize</code>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="window.location.reload()">I've Updated Everything</button>
            </div>
        </div>
    </div>
</div>
<!-- /Fix Webhook Modal -->
<div class="modal fade" id="selectAccountModal" tabindex="-1" aria-labelledby="selectAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="selectAccountModalLabel">Select Active WhatsApp Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">Choose which WhatsApp Business Account and Phone Number should be used for this dashboard. You can switch this at any time.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" class="form-check-input" id="selectAllAccounts" checked>
                                </th>
                                <th>Account Details</th>
                                <th>Phone Number</th>
                            </tr>
                        </thead>
                        <tbody id="accountSelectorBody">
                            @foreach($allAccounts as $acc)
                                @php
                                    $accPhones = $allPhoneNumbers->where('target_id', $acc->id);
                                @endphp
                                @foreach($accPhones as $phone)
                                <tr class="account-row" data-waba="{{ $acc->waba_id }}" data-phone="{{ $phone->whatsapp_id }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input account-checkbox" {{ $acc->is_active && $phone->is_active ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $acc->name }}</div>
                                        <small class="text-muted">ID: {{ $acc->waba_id }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-primary">{{ $phone->display_phone_number }}</div>
                                        <small class="text-muted">Verified: {{ $phone->name_status ?: 'Yes' }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAccountSelection()">Save Selection</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4 text-center">
                <div class="mb-3 text-danger">
                    <i class="ti ti-alert-triangle fs-40" style="font-size: 50px;"></i>
                </div>
                <h4 class="modal-title mb-2" id="confirmTitle">Confirm Action</h4>
                <p class="text-muted mb-4" id="confirmMessage" style="font-size: 16px;">Are you sure you want to proceed?</p>
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-light px-5 py-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-5 py-2" id="confirmBtn">Confirm Disconnect</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function syncMetaAccount() {
        const btn = $('#btnSyncMeta');
        const originalHtml = btn.html();
        btn.html('<i class="ti ti-loader fa-spin me-1"></i> Syncing...');
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ box_route('whatsapp.sync_meta') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if(response.success) {
                    if (typeof showToast === 'function') {
                        showToast(response.message, 'success');
                    } else {
                        showToast(response.message, 'info');
                    }
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    if (typeof showToast === 'function') {
                        showToast(response.message, 'danger');
                    } else {
                        showToast('Error: ' + response.message, 'danger');
                    }
                }
            },
            error: function() {
                if (typeof showToast === 'function') {
                    showToast('Something went wrong while syncing.', 'danger');
                } else {
                    showToast('Something went wrong while syncing.', 'danger');
                }
            },
            complete: function() {
                btn.html(originalHtml);
                btn.prop('disabled', false);
            }
        });
    }
</script>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
<script>
    // Global Confirmation Helper
    window.showConfirm = function(title, message, btnText, onConfirm) {
        $('#confirmTitle').text(title);
        $('#confirmMessage').text(message);
        $('#confirmBtn').text(btnText).off('click').on('click', function() {
            $('#confirmActionModal').modal('hide');
            onConfirm();
        });
        $('#confirmActionModal').modal('show');
    };

    window.fbAsyncInit = function() {
        FB.init({
            appId            : '{{ config('services.facebook.client_id') }}',
            autoLogAppEvents : true,
            xfbml            : true,
            version          : 'v22.0'
        });
    };

    function saveAccountSelection() {
        const selectedWabas = [];
        const selectedPhones = {};
        
        $('.account-checkbox:checked').each(function() {
            const row = $(this).closest('.account-row');
            const wabaId = row.data('waba');
            const phoneId = row.data('phone');
            selectedWabas.push(wabaId);
            selectedPhones[wabaId] = phoneId;
        });

        if (selectedWabas.length === 0) {
            showToast('Please select at least one account to connect.', 'info');
            return;
        }

        if (!confirm('Save selected accounts? Only the checked accounts will be kept in your dashboard.')) return;
        
        const url = '{{ route("whatsapp.select_account", ["box_slug" => $box->slug]) }}';
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                waba_ids: selectedWabas,
                phone_ids: selectedPhones
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    $('#selectAccountModal').modal('hide');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast(response.message, 'info');
                }
            },
            error: function() {
                showToast('An error occurred while saving selection.', 'info');
            }
        });
    }

    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('select_account') === 'true') {
            $('#selectAccountModal').modal('show');
            // Clean up the URL
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        $('#selectAllAccounts').on('change', function() {
            $('.account-checkbox').prop('checked', $(this).is(':checked'));
        });
    });

    // Single account activation (from dashboard list)
    function selectActiveAccount(wabaId, phoneId) {
        saveAccountSelectionExplicit([wabaId], {[wabaId]: phoneId}, true);
    }

    function deactivateAccount(wabaId, phoneId) {
        saveAccountSelectionExplicit([wabaId], {[wabaId]: phoneId}, false);
    }

    function saveAccountSelectionExplicit(wabaIds, phoneIds, activeStatus = true) {
        const url = '{{ route("whatsapp.select_account", ["box_slug" => $box->slug]) }}';
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                waba_ids: wabaIds,
                phone_ids: phoneIds,
                preserve_others: true,
                set_active: activeStatus
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                }
            }
        });
    }

    let embeddedSignupWabaId = null;
    let embeddedSignupPhoneId = null;

    // Listen for Meta's cross-origin message from the popup
    window.addEventListener('message', (event) => {
        if (event.origin !== 'https://www.facebook.com' && event.origin !== 'https://web.facebook.com') return;
        
        try {
            const payload = typeof event.data === 'string' ? JSON.parse(event.data) : event.data;
            if (payload && payload.type === 'WA_EMBEDDED_SIGNUP') {
                console.log('Received WA_EMBEDDED_SIGNUP message:', payload);
                if (payload.data) {
                    embeddedSignupWabaId = payload.data.waba_id || payload.data.whatsapp_business_account_id || null;
                    embeddedSignupPhoneId = payload.data.phone_number_id || payload.data.phone_id || null;
                }
            }
        } catch(e) {
            // Ignore parse errors for irrelevant messages
        }
    });


    // Health Monitor Logic
    $('#btnCheckHealth').on('click', function() {
        const btn = $(this);
        const icon = btn.find('i');
        const monitorRow = $('#health-monitor-row');
        const resultsContainer = $('#health-results-container');
        
        btn.prop('disabled', true);
        icon.addClass('fa-spin ti-refresh').removeClass('ti-activity');
        
        monitorRow.slideDown();
        resultsContainer.html('<div class="col-12 text-center p-4"><div class="spinner-border text-info mb-2" role="status"></div><div>Verifying connections with Meta Graph API...</div></div>');

        $.ajax({
            url: "{{ box_route('whatsapp.business.checkStatus') }}",
            type: "GET",
            success: function(res) {
                btn.prop('disabled', false);
                icon.removeClass('fa-spin');
                
                if (res.success) {
                    let html = '';
                    const accounts = res.data.accounts || [];
                    
                    if (accounts.length === 0) {
                        html = '<div class="col-12 text-center p-4 text-muted">No active accounts found to check.</div>';
                    } else {
                        accounts.forEach(acc => {
                            let statusClass = acc.account_status === 'ACTIVE' ? 'bg-success' : 'bg-warning';
                            let healthClass = acc.health_status === 'HEALTHY' ? 'text-success' : 'text-warning';
                            
                            html += `
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 bg-light h-100">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-0 fw-bold">${acc.name}</h6>
                                                <small class="text-muted">ID: ${acc.waba_id}</small>
                                            </div>
                                            <span class="badge ${statusClass}">${acc.account_status}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-3 mb-2 small">
                                            <span>Health: <strong class="${healthClass}">${acc.health_status || 'UNKNOWN'}</strong></span>
                                            <span>Payment: <strong class="${acc.payment_status === 'OK' ? 'text-success' : 'text-danger'}">${acc.payment_status || 'OK'}</strong> ${acc.payment_status !== 'OK' ? `<a href="https://business.facebook.com/billing_hub/payment_settings?business_id=${acc.business_id}" target="_blank" class="btn btn-xs btn-link p-0 text-decoration-none ms-1" style="font-size: 10px;"><i class="ti ti-credit-card"></i> Fix Payment</a>` : ''}</span>
                                            <span>Enabled: <strong>${acc.is_enabled ? 'YES' : 'NO'}</strong></span>
                                            <span>Webhook: <strong class="${acc.webhook_subscribed ? 'text-success' : 'text-danger'}">${acc.webhook_subscribed ? 'ACTIVE' : 'NOT FOUND'}</strong></span>
                                            <button class="btn btn-sm btn-link p-0 text-decoration-none ms-2" onclick="syncWebhook('${acc.waba_id}')">Force Sync Webhook</button>
                                        </div>
                                        ${acc.health_details ? `<div class="alert alert-warning py-1 px-2 mb-2 small"><i class="ti-info-alt me-1"></i> ${acc.health_details}</div>` : ''}
                                        
                                        <div class="mt-2 border-top pt-2">
                                            <div class="fw-medium mb-1 small text-muted">Phone Connectivity:</div>
                                            ${(acc.phone_numbers || []).map(p => `
                                                <div class="d-flex flex-column mb-1 p-2 rounded border ${p.is_connected ? 'bg-soft-success' : 'bg-soft-danger'}">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="fw-bold small"><i class="ti ti-phone me-1"></i>${p.display_phone_number}</span>
                                                        <div class="d-flex align-items-center gap-1">
                                                            ${!p.is_connected ? `<a href="https://business.facebook.com/wa/manage/phone-numbers/?waba_id=${acc.waba_id}" target="_blank" class="btn btn-xs btn-warning py-0 px-1 me-1" style="font-size: 9px; height: 18px; line-height: 16px;">Verify Now</a>` : ''}
                                                            <span class="badge ${p.is_connected ? 'bg-success' : 'bg-danger'} py-0 px-1" style="font-size: 10px;">${p.status}</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between gap-2 mt-1" style="font-size: 9px;">
                                                        <span title="Tier">Tier: <strong>${p.limit_tier.replace('TIER_', '')}</strong></span>
                                                        <span title="Daily Limit">Limit: <strong>${p.max_limit}</strong></span>
                                                        <span title="Sent Last 24h">Sent: <strong>${p.sent_today}</strong></span>
                                                        <span title="Remaining">Remaining: <strong class="${p.remaining < 50 ? 'text-danger' : 'text-success'}">${p.remaining}</strong></span>
                                                    </div>
                                                </div>
                                            `).join('')}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    resultsContainer.html(html);
                    
                    // Update main badge
                    if (res.data.active_accounts > 0) {
                        $('#wa-status-badge').removeClass('bg-warning').addClass('bg-success').html(res.data.active_accounts + ' ACCOUNT' + (res.data.active_accounts > 1 ? 'S' : '') + ' CONNECTED');
                    }
                    
                    showToast('Connectivity check complete.', 'success');
                } else {
                    resultsContainer.html(`<div class="col-12 alert alert-danger">${res.message}</div>`);
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                icon.removeClass('fa-spin');
                resultsContainer.html(`<div class="col-12 alert alert-danger">Status check failed. Please ensure your API token is valid.</div>`);
            }
        });
    });

    // Function to Sync Webhook
    window.syncWebhook = function(wabaId) {
        const btn = $(event.target);
        const originalText = btn.text();

        window.showConfirm('Sync Webhook', 'This will attempt to register your current APP_URL as the webhook callback for this WABA. Continue?', 'Yes, Sync Webhook', function() {
            btn.prop('disabled', true).text('Syncing...');
            
            $.ajax({
                url: "{{ box_route('whatsapp.business.registerWebhook', ['wabaId' => 'WABA_ID']) }}".replace('WABA_ID', wabaId),
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    webhook_url: "{{ config('app.url') }}/webhook/whatsapp"
                },
                success: function(res) {
                    btn.prop('disabled', false).text(originalText);
                    if (res.success) {
                        showToast('Webhook registered successfully!', 'success');
                        $('#btnCheckHealth').click(); // Refresh health view
                    } else {
                        showToast(res.message || 'Failed to register webhook.', 'error');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text(originalText);
                    showToast('Error syncing webhook. Ensure your token is valid.', 'error');
                }
            });
        });
    };
    window.disconnectSingle = function(wabaId, phoneId) {
        showConfirm('Remove Phone Number', 'Are you sure you want to remove this specific phone number from your CRM? This action cannot be undone.', 'Confirm Disconnect', function() {
            const url = '{{ route("whatsapp.disconnect_single", ["box_slug" => $box->slug]) }}';
            
            showToast('Removing number...', 'info');
            
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    waba_id: wabaId,
                    phone_id: phoneId
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showToast(response.message, 'danger');
                    }
                },
                error: function() {
                    showToast('Failed to remove phone number.', 'danger');
                }
            });
        });
    };

    function disconnectAccount(wabaId, name) {
        showConfirm('Disconnect Account', 'Are you sure you want to disconnect "' + name + '"? This will remove this specific account and its numbers from your CRM.', 'Confirm Disconnect', function() {
            const url = '{{ route("whatsapp.disconnect_single", ["box_slug" => $box->slug]) }}';
            
            showToast('Disconnecting account...', 'info');
            
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    waba_id: wabaId
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showToast(response.message, 'danger');
                    }
                },
                error: function() {
                    showToast('Failed to disconnect account.', 'danger');
                }
            });
        });
    }

    window.copyToClipboard = function(text) {
        if (!navigator.clipboard) {
            // Fallback for older browsers
            var textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand("copy");
            document.body.removeChild(textArea);
            showToast('Copied to clipboard!', 'success');
            return;
        }
        navigator.clipboard.writeText(text).then(() => {
            showToast('Copied to clipboard!', 'success');
        }).catch(err => {
            console.error('Could not copy text: ', err);
        });
    };

    function confirmDisconnect() {
        showConfirm('Full Disconnect', 'Are you sure you want to fully disconnect your WhatsApp & Facebook account? This will stop all messaging until you reconnect.', 'Confirm Full Reset', function() {
            const disconnectUrl = '{{ route("whatsapp.disconnect", ["box_slug" => $box->slug]) }}';
            
            showToast('Disconnecting account...', 'info');
            
            $.ajax({
                url: disconnectUrl,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    if (data.success) {
                        showToast('WhatsApp account disconnected successfully.', 'success');
                        // Partial reload of the integration container
                        reloadIntegrationStatus();
                    } else {
                        showToast('Error: ' + data.message, 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error disconnecting:', error);
                    showToast('An error occurred while disconnecting.', 'danger');
                }
            });
        });
    }

    function launchWhatsAppLogin() {
        const appId = '{{ config('services.facebook.client_id') }}';
        const configId = '{{ config('services.whatsapp.config_id') }}';
        const boxSlug = '{{ $box->slug ?? "" }}' || "{{ Request::segment(1) }}";
        
        // Save context so callback knows where to go back
        localStorage.setItem('last_tenant_slug', boxSlug);
        localStorage.setItem('last_auth_url', window.location.href);
        
        // Use the STATIC callback URL whitelisted in Meta Dashboard
        const redirectUri = window.location.origin + '/auth/facebook/callback';
        
        // Pass boxSlug in state to maintain context
        const state = encodeURIComponent('box_slug=' + boxSlug + '&display=popup&type=whatsapp');
        
        const oauthUrl = 'https://www.facebook.com/v22.0/dialog/oauth?client_id=' + appId + '&config_id=' + configId + '&redirect_uri=' + encodeURIComponent(redirectUri) + '&state=' + state + '&response_type=code&display=popup';

        const popup = window.open(oauthUrl, 'WhatsAppLogin', 'width=600,height=700');

        // Listen for message from the callback popup
        window.addEventListener('message', function(event) {
            if (event.origin !== window.location.origin) return;
            
            if (event.data === 'facebook-auth-success' || (event.data && event.data.type === 'facebook-auth-success')) {
                if (typeof showToast === 'function') {
                    showToast('✓ WhatsApp connected successfully!', 'success');
                } else {
                    showToast('✓ WhatsApp connected successfully!', 'success');
                }
                
                // Delay reload so user can see the toast
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
        }, false);
    }

    function reloadIntegrationStatus(showSelector = false) {
        // Reload only the integration status container
        const currentUrl = window.location.href;
        $('#whatsapp-integration-content').css('opacity', '0.5');
        
        $.get(currentUrl, function(html) {
            const newContent = $(html).find('#whatsapp-integration-content').html();
            $('#whatsapp-integration-content').html(newContent).css('opacity', '1');
            console.log('Integration status reloaded via AJAX');
            
            if (showSelector) {
                setTimeout(() => {
                    $('#selectAccountModal').modal('show');
                }, 500);
            }
        }).fail(function() {
            window.location.reload(); // Fallback to hard reload if AJAX fails
        });
    }


    $(document).ready(function() {
        @if(session('show_whatsapp_selector'))
            $('#selectAccountModal').modal('show');
        @endif
    });

function toggleTokenVisibility() {
    var input = document.getElementById("apiTokenInput");
    var icon = document.getElementById("tokenEyeIcon");
    if (input.type === "password") {
        input.type = "text";
        icon.className = "ti ti-eye-off";
    } else {
        input.type = "password";
        icon.className = "ti ti-eye";
    }
}

function generateApiToken() {
    if (confirm("Generate New Token?\nThis will invalidate any existing API token for this Box.\n\nAre you sure?")) {
        $.ajax({
            url: '{{ box_route("whatsapp.api.generate_token") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#apiTokenInput').val(response.token);
                    showToast('Generated! ' + response.message, 'success');
                }
            },
            error: function() {
                showToast('Error: Could not generate token.', 'danger');
            }
        });
    }
}

function saveWebhookUrl() {
    var url = $('#webhookUrlInput').val();
    $.ajax({
        url: '{{ box_route("whatsapp.api.save_webhook") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            webhook_url: url
        },
        success: function(response) {
            if (response.success) {
                showToast('Saved! ' + response.message, 'success');
            }
        },
        error: function(xhr) {
            showToast('Error: ' + (xhr.responseJSON.message || 'Could not save webhook URL.'), 'danger');
        }
    });
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        if (typeof showToast === 'function') {
            showToast('✓ Copied to clipboard!', 'success');
        } else {
            showToast('Copied to clipboard!', 'success');
        }
    });
}
</script>

<!-- API Docs Modal -->
<div class="modal fade" id="apiDocsModal" tabindex="-1" aria-labelledby="apiDocsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="apiDocsModalLabel"><i class="ti ti-api me-2"></i>Developer API Guidelines</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-0">
                <div class="p-4 bg-white mb-3 shadow-sm border-bottom">
                    <h6 class="fw-bold mb-3"><i class="ti ti-send text-primary me-2"></i>1. Sending a Message</h6>
                    <p class="text-muted small mb-2">Send a text or template message to any number. Endpoint: <code>POST {{ url('/api/v1/whatsapp/send') }}</code></p>
                    
                    <div class="nav-align-top">
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#curl-send" aria-controls="curl-send" aria-selected="true">cURL</button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#php-send" aria-controls="php-send" aria-selected="false">PHP (Guzzle)</button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#node-send" aria-controls="node-send" aria-selected="false">Node.js (Axios)</button>
                            </li>
                        </ul>
                        <div class="tab-content border-0 p-0">
                            <!-- cURL -->
                            <div class="tab-pane fade show active" id="curl-send" role="tabpanel">
<div class="position-relative">
<pre class="bg-dark text-light p-3 rounded small mb-0"><code>curl -X POST {{ url('/api/v1/whatsapp/send') }} \
-H "Authorization: Bearer YOUR_API_TOKEN" \
-H "Content-Type: application/json" \
-d '{
  "phone": "919876543210",
  "type": "text",
  "message": "Hello from BizzCRM API!"
}'</code></pre>
</div>
                            </div>
                            <!-- PHP -->
                            <div class="tab-pane fade" id="php-send" role="tabpanel">
<pre class="bg-dark text-light p-3 rounded small mb-0"><code>$client = new \GuzzleHttp\Client();
$response = $client->post('{{ url('/api/v1/whatsapp/send') }}', [
    'headers' => [
        'Authorization' => 'Bearer YOUR_API_TOKEN',
        'Content-Type' => 'application/json',
    ],
    'json' => [
        'phone' => '919876543210',
        'type' => 'template',
        'template_name' => 'hello_world',
        'language' => 'en_US'
    ]
]);</code></pre>
                            </div>
                            <!-- Node -->
                            <div class="tab-pane fade" id="node-send" role="tabpanel">
<pre class="bg-dark text-light p-3 rounded small mb-0"><code>const axios = require('axios');
axios.post('{{ url('/api/v1/whatsapp/send') }}', {
    phone: '919876543210',
    type: 'text',
    message: 'Hello from Node.js!'
}, {
    headers: { 'Authorization': `Bearer YOUR_API_TOKEN` }
});</code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-white shadow-sm">
                    <h6 class="fw-bold mb-3"><i class="ti ti-download text-success me-2"></i>2. Receiving Webhooks</h6>
                    <p class="text-muted small mb-3">When you set a Webhook Forwarding URL, we will send an HTTP POST request to your URL whenever a new message or status update arrives from Meta.</p>
                    
                    <div class="bg-dark text-light p-3 rounded small">
<pre class="mb-0"><code>// Example payload sent to your webhook URL
{
  "object": "whatsapp_business_account",
  "entry": [
    {
      "id": "1234567890",
      "changes": [
        {
          "value": {
            "messages": [
              {
                "from": "919876543210",
                "id": "wamid.HBgLOTE5...",
                "text": { "body": "Hi there!" },
                "type": "text"
              }
            ]
          },
          "field": "messages"
        }
      ]
    }
  ]
}</code></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /API Docs Modal -->

<!-- Recent Webhooks Modal -->
<div class="modal fade" id="recentWebhooksModal" tabindex="-1" aria-labelledby="recentWebhooksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="recentWebhooksModalLabel"><i class="ti ti-history me-2"></i>Recent Incoming Webhook Payloads</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div id="recentWebhooksLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Fetching recent payloads...</p>
                </div>
                <div id="recentWebhooksContainer" class="d-none">
                    <!-- Dynamic content injected here -->
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewRecentWebhooks() {
    $('#recentWebhooksModal').modal('show');
    $('#recentWebhooksLoading').removeClass('d-none');
    $('#recentWebhooksContainer').addClass('d-none').html('');
    
    $.ajax({
        url: '{{ box_route("whatsapp.api.recent_incoming") }}',
        type: 'GET',
        success: function(response) {
            $('#recentWebhooksLoading').addClass('d-none');
            $('#recentWebhooksContainer').removeClass('d-none');
            
            if(response.messages && response.messages.length > 0) {
                let html = '<div class="accordion" id="webhookAccordion">';
                response.messages.forEach((msg, index) => {
                    let previewText = msg.text ? msg.text : (msg.media ? '[Media Attached]' : 'Message');
                    if (previewText.length > 50) previewText = previewText.substring(0, 50) + '...';
                    
                    html += `
                        <div class="accordion-item mb-2 border rounded shadow-sm">
                            <h2 class="accordion-header" id="heading${index}">
                                <button class="accordion-button ${index === 0 ? '' : 'collapsed'} bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}" aria-expanded="${index === 0 ? 'true' : 'false'}" aria-controls="collapse${index}">
                                    <div class="d-flex justify-content-between w-100 pe-3">
                                        <span class="fw-bold"><i class="ti ti-brand-whatsapp text-success me-2"></i> From: ${msg.sender}</span>
                                        <span class="text-muted small">${msg.received_at}</span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse${index}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" aria-labelledby="heading${index}" data-bs-parent="#webhookAccordion">
                                <div class="accordion-body bg-light p-3">
                                    <p class="small fw-bold mb-1">Simulated Webhook Payload forwarded to your URL:</p>
                                    <pre class="bg-dark text-light p-3 rounded small mb-0" style="max-height: 300px; overflow-y: auto;"><code>${JSON.stringify(msg.raw_json_simulated, null, 2)}</code></pre>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                $('#recentWebhooksContainer').html(html);
            } else {
                $('#recentWebhooksContainer').html('<div class="alert alert-info"><i class="ti ti-info-circle me-2"></i> No recent incoming messages found to show as payload examples.</div>');
            }
        },
        error: function() {
            $('#recentWebhooksLoading').addClass('d-none');
            $('#recentWebhooksContainer').removeClass('d-none').html('<div class="alert alert-danger">Error loading recent webhooks.</div>');
        }
    });
}
</script>
<!-- /Recent Webhooks Modal -->

<!-- API Templates Manager Modal -->
<div class="modal fade" id="apiTemplatesModal" tabindex="-1" aria-labelledby="apiTemplatesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="apiTemplatesModalLabel"><i class="ti ti-layout-grid-add me-2"></i>API Templates Manager</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i class="ti ti-info-circle me-2"></i>
                    <strong>Map Variables & Assign Chatbots:</strong> Set custom variable names (e.g. <code>customer_name</code> instead of <code>\{\{1\}\}</code>) to easily pass them in your API payloads. You can also assign a Chatbot that will automatically trigger when a customer replies to an API message sent using that template.
                </div>
                
                <div id="apiTemplatesLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Loading templates...</p>
                </div>
                
                <div id="apiTemplatesContainer" class="d-none">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered bg-white shadow-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Template Name</th>
                                    <th>Variables to Map</th>
                                    <th style="width: 250px;">Assign Chatbot</th>
                                    <th class="text-center" style="width: 120px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="apiTemplatesTableBody">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let globalChatbots = [];

function manageApiTemplates() {
    $('#apiTemplatesModal').modal('show');
    $('#apiTemplatesLoading').removeClass('d-none');
    $('#apiTemplatesContainer').addClass('d-none');
    
    $.ajax({
        url: '{{ box_route("whatsapp.api.templates") }}',
        type: 'GET',
        success: function(response) {
            $('#apiTemplatesLoading').addClass('d-none');
            $('#apiTemplatesContainer').removeClass('d-none');
            
            if(response.success) {
                globalChatbots = response.chatbots || [];
                renderApiTemplatesTable(response.templates);
            }
        },
        error: function() {
            $('#apiTemplatesLoading').addClass('d-none');
            showToast('Failed to load API templates.', 'danger');
        }
    });
}

function renderApiTemplatesTable(templates) {
    let tbody = $('#apiTemplatesTableBody');
    tbody.empty();
    
    if (templates.length === 0) {
        tbody.append('<tr><td colspan="4" class="text-center text-muted py-4">No approved templates found.</td></tr>');
        return;
    }
    
    templates.forEach(tpl => {
        let varsHtml = '';
        if (tpl.variables.length === 0) {
            varsHtml = '<span class="text-muted fst-italic">No variables</span>';
        } else {
            varsHtml = '<div class="row g-2">';
            tpl.variables.forEach(v => {
                let aliasValue = (tpl.api_config && tpl.api_config.aliases && tpl.api_config.aliases[v]) ? tpl.api_config.aliases[v] : '';
                varsHtml += `
                    <div class="col-12 d-flex align-items-center">
                        <span class="badge bg-secondary me-2">\{\{${v}\}\}</span>
                        <i class="ti ti-arrow-right me-2 text-muted"></i>
                        <input type="text" class="form-control form-control-sm alias-input" data-var="${v}" placeholder="e.g. customer_name" value="${aliasValue}">
                    </div>
                `;
            });
            varsHtml += '</div>';
        }
        
        let chatbotOptions = '<option value="">-- None --</option>';
        globalChatbots.forEach(cb => {
            let selected = (tpl.api_config && tpl.api_config.chatbot_id == cb.id) ? 'selected' : '';
            chatbotOptions += `<option value="${cb.id}" ${selected}>${cb.name}</option>`;
        });
        
        let row = `
            <tr data-id="${tpl.id}">
                <td><strong class="text-primary">${tpl.name}</strong><br><small class="text-muted">${tpl.category} | ${tpl.language}</small></td>
                <td>${varsHtml}</td>
                <td>
                    <select class="form-select form-select-sm chatbot-select">
                        ${chatbotOptions}
                    </select>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-success w-100 save-tpl-btn" onclick="saveApiTemplate(${tpl.id}, this)">
                        <i class="ti ti-device-floppy"></i> Save
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function saveApiTemplate(templateId, btn) {
    let row = $(btn).closest('tr');
    let aliases = {};
    row.find('.alias-input').each(function() {
        let v = $(this).data('var');
        let val = $(this).val().trim();
        if(val) {
            aliases[v] = val;
        }
    });
    
    let chatbotId = row.find('.chatbot-select').val();
    
    $(btn).html('<i class="ti ti-loader ti-spin"></i>').prop('disabled', true);
    
    let configUrl = '{{ box_route("whatsapp.api.templates.config", ":id") }}'.replace(':id', templateId);
    
    $.ajax({
        url: configUrl,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            aliases: aliases,
            chatbot_id: chatbotId
        },
        success: function(response) {
            $(btn).html('<i class="ti ti-check"></i> Saved').removeClass('btn-success').addClass('btn-outline-success');
            setTimeout(() => {
                $(btn).html('<i class="ti ti-device-floppy"></i> Save').removeClass('btn-outline-success').addClass('btn-success').prop('disabled', false);
            }, 2000);
        },
        error: function(xhr) {
            $(btn).html('<i class="ti ti-device-floppy"></i> Save').prop('disabled', false);
            showToast('Error saving configuration.', 'danger');
        }
    });
}
</script>
<!-- /API Templates Manager Modal -->

<!-- KYC Modal -->
<div class="modal fade" id="kyc_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="ti ti-shield-check me-2"></i> Business Verification (KYC)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-3">To increase your messaging limit and get an official business account (Green Tick), you must complete Business Verification directly through Meta.</p>
                <div class="bg-light p-3 rounded mb-3">
                    <h6 class="fw-bold mb-2">Before you begin:</h6>
                    <ul class="mb-0 fs-14 text-dark">
                        <li class="mb-1">Ensure your legal business name exactly matches your GST/Registration document.</li>
                        <li class="mb-1">Have a working website that shows your business name.</li>
                        <li>Have a valid utility bill or bank statement in your business name.</li>
                    </ul>
                </div>
                <div class="alert alert-warning py-2 px-3 small border-warning">
                    <i class="ti ti-alert-triangle me-1"></i> Verification can only be done securely inside your Meta Business Manager.
                </div>
                <div class="text-center mt-4">
                    @if(count($accounts) > 0)
                        @php
                            $firstAccount = $accounts->first();
                        @endphp
                        <a href="https://business.facebook.com/wa/manage/home/?waba_id={{ $firstAccount->waba_id }}" target="_blank" class="btn btn-primary w-100">
                            <i class="ti ti-external-link me-1"></i> Go to Meta Security Center
                        </a>
                    @else
                        <a href="https://business.facebook.com/settings/security/business_verification" target="_blank" class="btn btn-primary w-100">
                            <i class="ti ti-external-link me-1"></i> Go to Meta Security Center
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="edit_profile_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="ti ti-user-edit me-2"></i> Edit WhatsApp Business Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editProfileForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Phone Number</label>
                        <select class="form-select" id="profilePhoneNumber" onchange="fetchBusinessProfile()">
                            <option value="">-- Select Number --</option>
                            @foreach($accounts as $account)
                                @php
                                    $phones = isset($phoneNumbers) ? $phoneNumbers->where('target_id', $account->id) : collect();
                                @endphp
                                @foreach($phones as $phone)
                                    <option value="{{ $phone->whatsapp_id }}">{{ $phone->display_phone_number }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <div id="profileLoading" class="text-center py-4" style="display:none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Fetching profile from Meta...</p>
                    </div>

                    <div id="profileFormFields" style="display:none;">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description (About the Business)</label>
                                <textarea class="form-control" id="profileDescription" rows="3" placeholder="Enter business description"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" id="profileAddress" placeholder="e.g., 123 Main St, City, Country">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="profileEmail" placeholder="contact@business.com">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Website 1</label>
                                <input type="url" class="form-control" id="profileWebsite1" placeholder="https://www.example.com">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Website 2 (Optional)</label>
                                <input type="url" class="form-control" id="profileWebsite2" placeholder="https://shop.example.com">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Industry/Vertical</label>
                                <select class="form-select" id="profileVertical">
                                    <option value="UNDEF">Not Specified</option>
                                    <option value="APPAREL">Apparel</option>
                                    <option value="BANKING">Banking</option>
                                    <option value="BEAUTY">Beauty</option>
                                    <option value="EDUCATION">Education</option>
                                    <option value="ENTERTAINMENT">Entertainment</option>
                                    <option value="FINANCE">Finance</option>
                                    <option value="GROCERY">Grocery</option>
                                    <option value="HEALTH">Health</option>
                                    <option value="HOTEL">Hotel</option>
                                    <option value="NONPROFIT">Nonprofit</option>
                                    <option value="PROF_SERVICES">Professional Services</option>
                                    <option value="RETAIL">Retail</option>
                                    <option value="TRAVEL">Travel</option>
                                    <option value="OTHER">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">About Text (Status)</label>
                                <input type="text" class="form-control" id="profileAbout" placeholder="Hey there! I am using WhatsApp." maxlength="139">
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-primary" id="btnSaveProfile" onclick="saveBusinessProfile()">
                                <i class="ti ti-device-floppy me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openEditProfileModal() {
    $('#profilePhoneNumber').val('');
    $('#profileFormFields').hide();
    $('#profileLoading').hide();
}

function fetchBusinessProfile() {
    let phoneId = $('#profilePhoneNumber').val();
    if(!phoneId) {
        $('#profileFormFields').hide();
        return;
    }
    
    $('#profileFormFields').hide();
    $('#profileLoading').show();
    
    $.ajax({
        url: '{{ box_route("whatsapp.profile.get") }}',
        type: 'GET',
        data: { phone_number_id: phoneId },
        success: function(res) {
            $('#profileLoading').hide();
            if(res.success) {
                $('#profileDescription').val(res.data.description || '');
                $('#profileAddress').val(res.data.address || '');
                $('#profileEmail').val(res.data.email || '');
                $('#profileVertical').val(res.data.vertical || 'UNDEF');
                $('#profileAbout').val(res.data.about || '');
                
                $('#profileWebsite1').val('');
                $('#profileWebsite2').val('');
                if(res.data.websites && res.data.websites.length > 0) {
                    $('#profileWebsite1').val(res.data.websites[0] || '');
                    if(res.data.websites.length > 1) {
                        $('#profileWebsite2').val(res.data.websites[1] || '');
                    }
                }
                $('#profileFormFields').show();
            } else {
                showToast('Error fetching profile: ' + (res.error || 'Unknown error'), 'danger');
            }
        },
        error: function(xhr) {
            $('#profileLoading').hide();
            showToast('Failed to fetch profile.', 'danger');
        }
    });
}

function saveBusinessProfile() {
    let phoneId = $('#profilePhoneNumber').val();
    if(!phoneId) return;
    
    let btn = $('#btnSaveProfile');
    btn.prop('disabled', true).html('<i class="ti ti-loader ti-spin me-1"></i> Saving...');
    
    let websites = [];
    if($('#profileWebsite1').val().trim() !== '') websites.push($('#profileWebsite1').val().trim());
    if($('#profileWebsite2').val().trim() !== '') websites.push($('#profileWebsite2').val().trim());
    
    let payload = {
        _token: '{{ csrf_token() }}',
        phone_number_id: phoneId,
        description: $('#profileDescription').val(),
        address: $('#profileAddress').val(),
        email: $('#profileEmail').val(),
        vertical: $('#profileVertical').val(),
        about: $('#profileAbout').val(),
        websites: websites
    };
    
    $.ajax({
        url: '{{ box_route("whatsapp.profile.update") }}',
        type: 'POST',
        data: payload,
        success: function(res) {
            btn.prop('disabled', false).html('<i class="ti ti-device-floppy me-1"></i> Save Changes');
            if(res.success) {
                showToast('Profile updated successfully!', 'success');
                $('#edit_profile_modal').modal('hide');
            } else {
                showToast('Error updating profile: ' + (res.error || 'Unknown error'), 'danger');
            }
        },
        error: function(xhr) {
            btn.prop('disabled', false).html('<i class="ti ti-device-floppy me-1"></i> Save Changes');
            showToast('Failed to update profile.', 'danger');
        }
    });
}
</script>
@endpush
