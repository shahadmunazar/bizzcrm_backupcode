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
								<div class="col-4">
									<h4 class="page-title">Integrations</h4>
								</div>
								<div class="col-8 text-end">
									<div class="head-icons">
										<a href="payments.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
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

							<!-- <div class="card-header">

								<div class="row align-items-center">
									<div class="col-sm-4">
										<div class="icon-form mb-3 mb-sm-0">
											<span class="form-icon"><i class="ti ti-search"></i></span>
											<input type="text" class="form-control" placeholder="Search">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end mb-3 mb-sm-0">
											<div class="dropdown me-2">
												<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
												<div class="dropdown-menu  dropdown-menu-end">
													<ul>
														<li>
															<a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-file-type-pdf text-danger me-1"></i>Export
																as PDF</a>
														</li>
														<li>
															<a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-file-type-xls text-green me-1"></i>Export
																as Excel </a>
														</li>
													</ul>
												</div>
											</div>
										</div>
									</div>
								</div>

							</div> -->

							<div class="card-body">
								<div class="card-body" style="padding-left:0;padding-right:0;">
								<ul class="nav nav-tabs mb-3 border-bottom-0" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" data-bs-toggle="tab" role="tab" href="#home1" aria-selected="true">Lead Sources</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" data-bs-toggle="tab" role="tab" href="#about1" aria-selected="false">Import/Export</a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane show active text-muted" id="home1" role="tabpanel">
										<div class="row">
								<div class="col-md-12" style="margin-top:25px;">
									<form action="" autocomplete="off" class="form-horizontal">
										<input name="searchtext" placeholder="Search here..." class="form-control" type="text">
									</form>

								</div>
							</div>
						<div class="row gy-5 gx-md-4 gy-lg-0 gx-xxl-5">
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3">
									<i class="fa-brands fa-facebook-f"></i> Facebook Connect
									@if(isset($facebookConnected) && $facebookConnected)
										<span class="badge bg-success ms-2">Connected</span>
									@else
										<span class="badge bg-danger ms-2">Not Connected</span>
									@endif
								</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								@if(isset($facebookConnected) && $facebookConnected)
									<div class="alert alert-success alert-dismissible fade show" role="alert">
										<strong>✓ Connected!</strong> Facebook is connected and ready to sync forms.
										<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
									</div>
								@else
									<div class="alert alert-warning alert-dismissible fade show" role="alert">
										<strong>⚠ Not Connected</strong> Please click "Configure" to connect your Facebook account.
										<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
									</div>
								@endif
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" onclick="launchFacebookLogin()" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm" id="configure-facebook-btn">
										<i class="fa-solid fa-plug"></i> {{ isset($facebookConnected) && $facebookConnected ? 'Reconnect' : 'Configure' }}
									</a>
									<a href="javascript:void(0);" onclick="checkFacebookConnection()" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm" id="check-connection-btn">
										<i class="fa-solid fa-check-circle"></i> Check Status
									</a>
									@if(isset($facebookConnected) && $facebookConnected)
										<a href="javascript:void(0);" onclick="getBusinessIntegration()" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm me-2" id="get-integration-btn">
											<i class="fa-solid fa-download"></i> Get Integration Details
										</a>
										<a href="javascript:void(0);" onclick="syncFacebookForms()" class="fw-bold text-decoration-none link-primary btn btn-warning btn-sm" id="sync-forms-btn">
											<i class="fa-solid fa-sync"></i> Sync Forms
										</a>
									@endif
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3">
									<i class="fa-brands fa-whatsapp"></i> Connect WhatsApp Business API
									@if(isset($whatsappConnected) && $whatsappConnected)
										<span class="badge bg-success ms-2">Connected</span>
									@else
										<span class="badge bg-danger ms-2">Not Connected</span>
									@endif
								</h4>
								<p class="mb-3">Connect your WhatsApp Business account to send and receive messages</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" onclick="launchWhatsAppLogin()" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm" id="configure-whatsapp-btn">
										<i class="fa-solid fa-plug"></i> {{ isset($whatsappConnected) && $whatsappConnected ? 'Reconnect' : 'Configure' }}
									</a>
									<a href="javascript:void(0);" onclick="checkWhatsAppConnection()" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm" id="check-whatsapp-connection-btn">
										<i class="fa-solid fa-check-circle"></i> Check Status
									</a>
									@if(isset($whatsappConnected) && $whatsappConnected && !empty($whatsappAccounts))
										<a href="javascript:void(0);" onclick="registerWhatsAppWebhook()" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm me-2" id="register-webhook-btn">
											<i class="fa-solid fa-plug"></i> Register Webhook
										</a>
										<a href="javascript:void(0);" onclick="checkWhatsAppWebhookStatus()" class="fw-bold text-decoration-none link-primary btn btn-warning btn-sm" id="check-webhook-btn">
											<i class="fa-solid fa-circle-check"></i> Check Webhook
										</a>
									@endif
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>




							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/bizz-crm-icon.png"> ZipZapGo Default</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($templates['ZipZapGo Default']) && isset($lastLeads[$templates['ZipZapGo Default']])) {{ $lastLeads[$templates['ZipZapGo Default']]->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/bizz-icon.png"> Bizz Digital</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($templates['Bizz Digital']) && isset($lastLeads[$templates['Bizz Digital']])) {{ $lastLeads[$templates['Bizz Digital']]->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><i class="fa-solid fa-circle-nodes"></i> Web Hook</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($templates['Web Hook']) && isset($lastLeads[$templates['Web Hook']])) {{ $lastLeads[$templates['Web Hook']]->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/pk-icon.png"> Pharma Khabar</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($lastLeads['Pharma Khabar'])) {{ $lastLeads['Pharma Khabar']->format('d-M-Y H:i') }} @elseif(isset($templates['Pharma Khabar']) && isset($lastLeads[$templates['Pharma Khabar']])) {{ $lastLeads[$templates['Pharma Khabar']]->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>

							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/af-icon.png"> Adflair Media</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($lastLeads['Adflair Media'])) {{ $lastLeads['Adflair Media']->format('d-M-Y H:i') }} @elseif(isset($templates['Adflair Media']) && isset($lastLeads[$templates['Adflair Media']])) {{ $lastLeads[$templates['Adflair Media']]->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><i class="fa-brands fa-facebook-f"></i> Facebook</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($lastLeads['Facebook'])) {{ $lastLeads['Facebook']->format('d-M-Y H:i') }} @elseif(isset($templates['Facebook']) && isset($lastLeads[$templates['Facebook']])) {{ $lastLeads[$templates['Facebook']]->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>

							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/dc-icon.png"> Dawa Charcha</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($lastLeads['Dawa Charcha'])) {{ $lastLeads['Dawa Charcha']->format('d-M-Y H:i') }} @elseif(isset($templates['Dawa Charcha']) && isset($lastLeads[$templates['Dawa Charcha']])) {{ $lastLeads[$templates['Dawa Charcha']]->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/franch-express-icon.png"> Franchis Express</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($templates['Franchis Express']) && isset($lastLeads[$templates['Franchis Express']])) {{ $lastLeads[$templates['Franchis Express']]->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/indiamart-icon.png"> India Mart Via Email</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($templates['India Mart']) && isset($lastLeads[$templates['India Mart']])) {{ $lastLeads[$templates['India Mart']]->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>

							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/indiamart-icon.png"> India Mart Via API</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($indiaMartApiLastLead)) {{ $indiaMartApiLastLead->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/jd-icon.png"> Just Dial</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($templates['Just Dial']) && isset($lastLeads[$templates['Just Dial']])) {{ $lastLeads[$templates['Just Dial']]->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/ntm-icon.png"> Next Tech Mart</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/pbc-icon.png"> Pharma Biz Connect</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($lastLeads['Pharma Biz Connect'])) {{ $lastLeads['Pharma Biz Connect']->format('d-M-Y H:i') }} @elseif(isset($templates['Pharma Biz Connect']) && isset($lastLeads[$templates['Pharma Biz Connect']])) {{ $lastLeads[$templates['Pharma Biz Connect']]->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/pharma-buffet-icon.png"> Pharma Buffet</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($lastLeads['Pharma Buffet'])) {{ $lastLeads['Pharma Buffet']->format('d-M-Y H:i') }} @elseif(isset($templates['Pharma Buffet']) && isset($lastLeads[$templates['Pharma Buffet']])) {{ $lastLeads[$templates['Pharma Buffet']]->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><i class="fa-solid fa-building"></i> Pharma Business</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($lastLeads['Pharma Business'])) {{ $lastLeads['Pharma Business']->format('d-M-Y H:i') }} @elseif(isset($templates['Pharma Business']) && isset($lastLeads[$templates['Pharma Business']])) {{ $lastLeads[$templates['Pharma Business']]->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/pharma-flair-icon.png"> Pharma Flair</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($lastLeads['Pharma Flair'])) {{ $lastLeads['Pharma Flair']->format('d-M-Y H:i') }} @elseif(isset($templates['Pharma Flair']) && isset($lastLeads[$templates['Pharma Flair']])) {{ $lastLeads[$templates['Pharma Flair']]->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><i class="fa-solid fa-users"></i> Pharma Franchise club</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($lastLeads['Pharma Franchise club'])) {{ $lastLeads['Pharma Franchise club']->format('d-M-Y H:i') }} @elseif(isset($templates['Pharma Franchise club']) && isset($lastLeads[$templates['Pharma Franchise club']])) {{ $lastLeads[$templates['Pharma Franchise club']]->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/pf-kart-icon.png"> Pharma Franchise Kart</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($lastLeads['Pharma Franchise Kart'])) {{ $lastLeads['Pharma Franchise Kart']->format('d-M-Y H:i') }} @elseif(isset($templates['Pharma Franchise Kart']) && isset($lastLeads[$templates['Pharma Franchise Kart']])) {{ $lastLeads[$templates['Pharma Franchise Kart']]->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/pf-mark-icon.png"> Pharma Franchise Mart</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<p class="small text-muted mb-2">Last Lead on : @if(isset($lastLeads['Pharma Franchise Mart'])) {{ $lastLeads['Pharma Franchise Mart']->format('d-M-Y H:i') }} @elseif(isset($templates['Pharma Franchise Mart']) && isset($lastLeads[$templates['Pharma Franchise Mart']])) {{ $lastLeads[$templates['Pharma Franchise Mart']]->format('d-M-Y H:i') }} @else - @endif</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/pf-india-icon.png"> Pharma Franchisee India</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/p-hooper-icon.png"> Pharma Hopers</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/p-infotech-icon.png"> Pharma Info Tech</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/p-mart-icon.png"> Pharma Mart</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/p-vends-icon.png"> Pharma Vends</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/p-view-icon.png"> Pharma View</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/p-express-icon.png"> Pharma Xpress</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><i class="fa-solid fa-list-ol"></i> Top 10 Suppliers</h4>
								<p class="mb-3">Receive new leads the Lead Ads source in your account</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/trade-india-icon.png"> Trade India</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/tm-icon.png"> Trade Myntra</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><img src="../../assets/img/iconsapi/t-xl-icon.png"> Trade XL</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><i class="fa-solid fa-globe"></i> TruWeb Media</h4>
								<p class="mb-3">Receive new leads from the Lead Ads source in your account</p>
								<div class="action-btns text-right">
								    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="fw-bold text-decoration-none link-primary btn btn-primary btn-sm">Configure</a>
									<a href="integration-instriction.html" class="fw-bold text-decoration-none link-primary btn btn-secondary btn-sm">Instrictions</a>
									<a href="label-maping.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Map Field</a>
									<a href="javascript:void(0);" class="fw-bold text-decoration-none link-primary btn btn-info btn-sm">Sample Data</a>
								</div>
								</div>
							</div>






























						</div>
									</div>
									<div class="tab-pane text-muted" id="about1" role="tabpanel">
										<div class="row gy-5 gx-md-4 gy-lg-0 gx-xxl-5 justify-content-center">
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><i class="fa fa-file-text-o" aria-hidden="true"></i> Import Clients from CSV</h4>
								<p class="mb-3">Upload a CSV file here so I can help you parse and import it?</p>
								<div class="action-btns text-right">
								    <a href="bulk-upload.html" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Bulk upload</a>
								</div>
								</div>
							</div>
							<div class="col-11 col-sm-6 col-lg-6 lead-col">
							    <div class="lead-inner">
								<h4 class="mb-3"><i class="fa fa-file-text-o" aria-hidden="true"></i> Export Client List</h4>
								<p class="mb-3">Export Client List – Download, Manage, and Review All Client Records in a Single File</p>
								<div class="action-btns text-right">
								    <a href="#" class="fw-bold text-decoration-none link-primary btn btn-success btn-sm">Configure</a>
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




		<!-- Add  -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">ZipZapGo</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form id="mailFilterForm" action="{{ route('settings.mail_filters.store', ['box_slug' => request()->route('box_slug') ?? ($box->slug ?? 'default')]) }}" method="POST">
					@csrf
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Name <span class="text-danger"> *</span></label>
										<input type="text" class="form-control" name="name" id="filter_name" required>
									</div>
								</div>
								
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label d-flex justify-content-between align-items-center">
											<span>Connected Email (Inbox) <span class="text-danger"> *</span></span>
											<a href="{{ route('google.connect.box', ['box_slug' => request()->route('box_slug') ?? ($box->slug ?? 'default')]) }}" class="text-primary fw-bold" style="font-size:12px;">
												<i class="ti ti-plus"></i> Connect Gmail
											</a>
										</label>
										<select class="select form-control" name="inbox_id" id="inbox_id" required>
											<option value="">--Select--</option>
										</select>
									</div>
								</div>

								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Template <span class="text-danger">*</span></label>
										<select class="select form-control" name="template_id" id="template_id" required>
											<option value="">--Select--</option>
										</select>
									</div>
								</div>

								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="col-form-label d-flex justify-content-between align-items-center">
											<span>Lead Source <span class="text-danger"> *</span></span>
											<a href="javascript:void(0);" onclick="quickAddSource()" class="text-primary fw-bold" style="font-size:12px;">
												<i class="ti ti-plus"></i> Add
											</a>
										</label>
										<select class="select form-control" name="lead_source_id" id="lead_source_id" required>
											<option value="">--Select--</option>
										</select>
									</div>
								</div>

								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">From </label>
										<input type="text" class="form-control" name="from" id="from">
									</div>
								</div>
								
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Subject </label>
										<input type="text" class="form-control" name="subject" id="subject">
									</div>
								</div>

								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="col-form-label">Lead Status  </label>
										<select class="select form-control" name="lead_status_id" id="lead_status_id">
											<option value="">--Select--</option>
										</select>
									</div>
								</div>

								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="col-form-label">Round Robin Lead Allocation</label>
										<select class="select form-control" name="round_robin" id="round_robin">
											<option value="0">No</option>
											<option value="1">Yes</option>
										</select>
									</div>
								</div>

								<div class="col-md-6" id="assigned_to_div">
									<div class="mb-3 ">
										<label class="col-form-label">Assigned To</label>
										<select class="select form-control" name="assigned_to" id="assigned_to">
											<option value="">--Select--</option>
										</select>
									</div>
								</div>

								<div class="col-md-12 d-none" id="round_robin_users_div">
									<div class="mb-3 ">
										<label class="col-form-label">Round Robin Users <span class="badge bg-info ms-1 robin-count-badge">0 Selected</span></label>
										<select class="select form-control" name="round_robin_users[]" id="round_robin_users" multiple="multiple">
										</select>
									</div>
								</div>

								<div class="col-md-12 d-none" id="shifts_config_div">
									<div class="mb-3">
										<label class="col-form-label">Enable Round Robin Shifts</label>
										<select name="enable_shifts" id="enable_shifts" class="form-select select">
											<option value="No">No</option>
											<option value="Yes">Yes</option>
										</select>
									</div>
									
									<div id="shifts_container" class="mb-4" style="display:none;">
										<h6 class="fw-bold mb-3 border-bottom pb-2">User Shifts Configuration</h6>
										<div id="shifts_list"></div>
									</div>
								</div>

								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="col-form-label">Status</label>
										<select class="select form-control" name="status" id="status">
											<option value="Active">Active</option>
											<option value="Inactive">Inactive</option>
										</select>
									</div>
								</div>

								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Has Words (Multiple keywords/phrases)</label>
										<select class="form-control" name="has_words[]" id="has_words" multiple="multiple">
										</select>
										<small class="text-muted">Type a word and press Enter to add it as a filter criteria.</small>
									</div>
								</div>

							</div>
						</div>
					</div>
					<div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary">Submit</button>
					</div>
				</form>
			</div>
		</div>
		<!-- /Add Company -->
		<!-- Preview Project -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom justify-content-between">
				<h4>Payment for Invoice<span class=" text-purple ms-2">#274738</span> </h4>
				<div class="d-flex align-items-center">
					<div class="toggle-header-popup">
						<div class="dropdown table-action me-2">
							<a href="#" class="btn btn-dropdowns dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
								Download
							</a>
							<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item " href="#">
									Download
								</a>
								<a class="dropdown-item " href="#">
									Download PDF
								</a>
							</div>
						</div>
					</div>
					<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
						<i class="ti ti-x"></i>
					</button>
				</div>

			</div>
			<div class="offcanvas-body p-4">
				<form action="payments.html">
					<div class="card">
						<div class="card-body">
							<div class="details-propsal">
								<h4>Proposal From & To</h4>
								<div class="row">
									<div class="col-lg-6 col-12">
										<div class="proposal-to">
											<h3>CRMS</h3>
											<span>3338 Marcus Street </span>
											<span>Birmingham, AL 35211</span>
											<span>Phone : +1 98789 78788 </span>
											<span>Email : <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="2b42454d446b4e534a465b474e05484446">[email&#160;protected]</a></span>
										</div>
									</div>
									<div class="col-lg-6 col-12">
										<div class="proposal-to">
											<h3>NovaWave LLC </h3>
											<span>994 Martine Ranch Suite 900</span>
											<span>Candacefort New Hampshire</span>
											<span>Phone : +1 58478 74646</span>
											<span>Email : <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="452c2b232a05203d24283529206b2b2031">[email&#160;protected]</a></span>
										</div>
									</div>
								</div>
							</div>
							<hr>
							<div class="details-propsal details-propsals">
								<h4>Payment Details</h4>
								<ul>
									<li>
										<h5>Payment Date</h5>
										<h6>13 May 2024</h6>
									</li>
									<li>
										<h5>Payment Method</h5>
										<h6>Cash</h6>
									</li>
									<li>
										<h5>Total Amount</h5>
										<h6>$96</h6>
									</li>
								</ul>
							</div>
							<div class="details-propsal">
								<div class="d-flex align-items-center justify-content-between">
									<h4>Invoice Details</h4>
									<h6 class="d-flex"><span class="text-danger"> Amount Due : </span> $100</h6>
								</div>
								<ul class="m-0 border-0">
									<li>
										<h5>Invoice Number</h5>
										<h6 class="mb-0"><span class="badge badge-purple-light d-inline-flex">#1254057</span></h6>
									</li>
									<li>
										<h5>Invoice Date</h5>
										<h6 class="mb-0">13 May 2024</h6>
									</li>
									<li>
										<h5>Invoice Amount</h5>
										<h6 class="mb-0">$196</h6>
									</li>
									<li>
										<h5>Payment Amount</h5>
										<h6 class="mb-0">$96</h6>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<!-- /Preview Project -->

		<!-- Delete Invoices -->
		<div class="modal fade" id="delete_payments" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Payments?</h4>
							<p class="mb-0">Are you sure you want to remove <br> invoice you selected.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="payments.html" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Invoices -->
@endsection


<style>
.lead-col {
	margin-top: 15px!important;
}
.lead-source-wrap {
	background: #f2f2f7;
	width: 100%;
	display: inline-block;
}

.lead-inner h4 i {
	font-size: 22px;
	margin-right: 5px;
}
.lead-inner h4 {
	font-size: 17px;
}
.action-btns.text-right a {
	font-weight: 500;
	color: #fff !important;
	margin-bottom:5px;
}
.lead-inner p {
	font-size: 15px;
	margin-bottom: 12px;
	color: #4b4b4b;
}
.lead-inner {
	background: #ffffff;
	padding: 12px;
	height:100%;
	box-shadow:0 0 9px 0 #c7c7c7;
	border-radius:5px;
}
.lead-inner:hover {
	background: #fff4f4;
}
.lead-source-tab-box {
	background: #fff;
	padding: 15px;
	width: 100%;
	display: inline-block;
	margin-bottom: 30px;
}

</style>

{{-- Facebook SDK + login helper --}}
<div id="fb-root"></div>
@push('scripts')
<script>
    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "https://connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    // Check and redirect from 127.0.0.1 to localhost BEFORE SDK loads
    // This prevents Facebook SDK from initializing with invalid domain
    (function() {
        if (window.location.hostname === '127.0.0.1') {
            const localhostUrl = window.location.href.replace('127.0.0.1', 'localhost');
            console.warn('⚠️ Facebook doesn\'t accept IP addresses (127.0.0.1) in App Domains.');
            console.warn('🔄 Redirecting to localhost for Facebook compatibility...');
            window.location.replace(localhostUrl); // Use replace() to avoid adding to history
            return;
        }
    })();

    window.fbAsyncInit = function () {
        // Only initialize SDK if on HTTPS (production or local HTTPS)
        // On HTTP (localhost), we'll use direct OAuth redirect instead
        const isHttps = window.location.protocol === 'https:';

        if (isHttps) {
            FB.init({
                appId: '{{ config('services.facebook.client_id') }}',
                cookie: true,
                xfbml: true,
                version: 'v22.0'
            });

            console.log('Facebook SDK initialized (HTTPS)');
        } else {
            console.log('Running on HTTP - Facebook SDK not initialized (will use OAuth redirect)');
        }

        // Debug: Log current domain
        console.log('Current domain:', window.location.hostname);
        console.log('Current URL:', window.location.href);
        console.log('Facebook App ID:', '{{ config('services.facebook.client_id') }}');
    };

    function launchFacebookLogin() {
        const appId = '{{ config('services.facebook.client_id') }}';
        const boxSlug = '{{ $box->slug ?? "" }}' || "{{ Request::segment(1) }}";
        localStorage.setItem('last_tenant_slug', boxSlug);
        localStorage.setItem('last_auth_url', window.location.href);
        const saveUrl = '{{ route('settings.integrations.facebook.save', ['box_slug' => $box->slug ?? ':box_slug']) }}'.replace(':box_slug', boxSlug);
        
        // Use the STATIC callback URL whitelisted in Meta Dashboard
        const redirectUri = window.location.origin + '/auth/facebook/callback';
        
        // Pass boxSlug in state to maintain context
        const state = encodeURIComponent(`box_slug=${boxSlug}&display=popup`);
        
        const oauthUrl = `https://www.facebook.com/v22.0/dialog/oauth?client_id=${appId}&redirect_uri=${encodeURIComponent(redirectUri)}&state=${state}&scope=public_profile,email,pages_show_list,pages_read_engagement,pages_manage_metadata,pages_manage_ads,ads_management,leads_retrieval&auth_type=rerequest&response_type=code&display=popup`;
        
        console.log('--- OAuth Debug Info ---');
        console.log('Static Redirect URI:', redirectUri);
        console.log('OAuth URL:', oauthUrl);
        console.log('------------------------');

        const popup = window.open(oauthUrl, 'FacebookLogin', 'width=600,height=700');

        // Listen for message from the callback popup
        const messageHandler = function(event) {
            if (event.data.type === 'facebook-auth-success') {
                window.removeEventListener('message', messageHandler);
                // The server already saved the token, we just need to reload
                alert('✓ Facebook connected successfully!');
                window.location.reload();
            } else if (event.data.type === 'facebook-auth-error') {
                window.removeEventListener('message', messageHandler);
                alert('Facebook Error: ' + event.data.error);
            }
        };

        window.addEventListener('message', messageHandler);

        const checkPopup = setInterval(() => {
            if (popup.closed) {
                clearInterval(checkPopup);
            }
        }, 1000);
    }

    function launchWhatsAppLogin() {
        const appId = '{{ config('services.facebook.client_id') }}';
        const configId = '{{ config('services.whatsapp.config_id') }}';
        const boxSlug = '{{ $box->slug ?? "" }}' || "{{ Request::segment(1) }}";
        localStorage.setItem('last_tenant_slug', boxSlug);
        localStorage.setItem('last_auth_url', window.location.href);
        const saveUrl = '{{ route('settings.integrations.facebook.save', ['box_slug' => $box->slug ?? ':box_slug']) }}'.replace(':box_slug', boxSlug);
        
        // Use the STATIC callback URL whitelisted in Meta Dashboard
        const redirectUri = window.location.origin + '/auth/facebook/callback';
        
        // Pass boxSlug in state to maintain context
        const state = encodeURIComponent(`box_slug=${boxSlug}&display=popup&type=whatsapp`);
        
        const oauthUrl = `https://www.facebook.com/v22.0/dialog/oauth?client_id=${appId}&config_id=${configId}&redirect_uri=${encodeURIComponent(redirectUri)}&state=${state}&response_type=code`;

        const popup = window.open(oauthUrl, 'WhatsAppLogin', 'width=600,height=700');

        // Listen for message from the callback popup
        const messageHandler = function(event) {
            if (event.data.type === 'facebook-auth-success') {
                window.removeEventListener('message', messageHandler);
                // The server already saved the token, we just need to reload
                alert('✓ WhatsApp connected successfully!');
                window.location.reload();
            } else if (event.data.type === 'facebook-auth-error') {
                window.removeEventListener('message', messageHandler);
                alert('WhatsApp Error: ' + event.data.error);
            }
        };

        window.addEventListener('message', messageHandler);

        const checkPopup = setInterval(() => {
            if (popup.closed) {
                clearInterval(checkPopup);
            }
        }, 1000);
    }


    // Check Facebook Connection Status
    function checkFacebookConnection() {
        const url = '{{ route('facebook.checkConnection', ['box_slug' => request()->route('box_slug')]) }}';

        const btn = document.getElementById('check-connection-btn');
        const originalHtml = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking...';

        fetch(url, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.connected) {
                    alert('✓ Facebook is Connected!\n\nToken is saved and ready to use.');
                } else {
                    alert('⚠ Facebook is Not Connected\n\nPlease click "Configure" to connect your Facebook account.');
                }
            } else {
                alert('Failed to check connection: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error checking connection:', error);
            alert('An error occurred while checking connection status.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }

    // Get Business Integration Details Function
    function getBusinessIntegration() {
        const url = '{{ route('facebook.businessIntegration', ['box_slug' => request()->route('box_slug')]) }}';

        const btn = document.getElementById('get-integration-btn');
        const originalHtml = btn.innerHTML;

        // Disable button and show loading
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Fetching...';

        fetch(url, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const integration = data.data?.integration || data.data?.saved_in;
                let message = 'Business Integration Details Saved Successfully!\n\n';
                if (integration) {
                    message += 'Integration ID: ' + (integration.id || 'N/A') + '\n';
                    message += 'Name: ' + (integration.name || 'N/A') + '\n';
                    message += 'App ID: ' + (integration.app_id || 'N/A') + '\n';
                    if (integration.created_time) {
                        message += 'Created: ' + new Date(integration.created_time).toLocaleString() + '\n';
                    }
                }
                alert(message);
            } else {
                alert('Failed to get integration details: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error getting integration details:', error);
            alert('An error occurred while getting integration details.');
        })
        .finally(() => {
            // Re-enable button
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }

    // Sync Facebook Forms Function
    function syncFacebookForms() {
        const syncUrl = '{{ route('facebook.sync-forms', ['box_slug' => request()->route('box_slug')]) }}';

        const btn = document.getElementById('sync-forms-btn');
        const originalHtml = btn.innerHTML;

        // Disable button and show loading
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Syncing...';

        fetch(syncUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Forms synced successfully!\n\nPages: ' + (data.data?.pages_count || 0) + '\nForms: ' + (data.data?.forms_count || 0) + '\n\nView all forms on the Facebook Forms page.');
                // Optionally redirect to Facebook Forms page
                const facebookFormsUrl = '{{ route('settings.integrations.facebook.form', ['box_slug' => request()->route('box_slug')]) }}';
                if (confirm('Would you like to view all synced forms?')) {
                    window.location.href = facebookFormsUrl;
                }
            } else {
                alert('Failed to sync forms: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error syncing forms:', error);
            alert('An error occurred while syncing forms.');
        })
        .finally(() => {
            // Re-enable button
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }

    // Check WhatsApp Connection Status
    function checkWhatsAppConnection() {
        const url = '{{ route('whatsapp.business.checkConnection', ['box_slug' => request()->route('box_slug')]) }}';

        const btn = document.getElementById('check-whatsapp-connection-btn');
        if (!btn) {
            console.error('Check WhatsApp connection button not found');
            return;
        }

        const originalHtml = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking...';

        fetch(url, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.connected) {
                    let message = '✓ WhatsApp Business is Connected!\n\n';
                    message += 'Total Accounts: ' + (data.data?.total_accounts || 0) + '\n';
                    message += 'Active Accounts: ' + (data.data?.active_accounts || 0) + '\n';
                    message += 'Connected Phones: ' + (data.data?.connected_phone_numbers || 0);
                    alert(message);
                } else {
                    alert('⚠ WhatsApp Business is Not Connected\n\nPlease click "Configure" to connect your WhatsApp Business account.');
                }
            } else {
                alert('Failed to check connection: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error checking WhatsApp connection:', error);
            alert('An error occurred while checking connection status.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }

    // Register WhatsApp Webhook
    function registerWhatsAppWebhook() {
        @if(isset($whatsappAccounts) && !empty($whatsappAccounts))
            // If multiple accounts, let user choose
            const accounts = @json($whatsappAccounts);
            if (accounts.length === 0) {
                alert('No active WhatsApp Business accounts found. Please connect an account first.');
                return;
            }

            let wabaId = accounts[0].waba_id; // Default to first account
            if (accounts.length > 1) {
                const accountList = accounts.map((acc, idx) => `${idx + 1}. ${acc.name || acc.waba_id}`).join('\n');
                const choice = prompt(`Multiple accounts found. Enter account number (1-${accounts.length}):\n\n${accountList}`);
                if (choice && choice >= 1 && choice <= accounts.length) {
                    wabaId = accounts[choice - 1].waba_id;
                } else {
                    return; // User cancelled
                }
            }

            const webhookUrl = '{{ config('app.url') }}/webhook/whatsapp';
            const verifyToken = '{{ config('services.whatsapp.webhook_verify_token') ?: 'BizzWhatsAppWebhook2026' }}';

            if (!confirm(`Register webhook for account: ${accounts.find(a => a.waba_id === wabaId)?.name || wabaId}?\n\nWebhook URL: ${webhookUrl}\nVerify Token: ${verifyToken}`)) {
                return;
            }

            const url = '{{ route('whatsapp.business.registerWebhook', ['box_slug' => request()->route('box_slug'), 'wabaId' => ':wabaId']) }}'.replace(':wabaId', wabaId);

            const btn = document.getElementById('register-webhook-btn');
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Registering...';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    webhook_url: webhookUrl,
                    verify_token: verifyToken
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✓ Webhook registered successfully!\n\nYour webhook is now configured to receive WhatsApp Business events from Meta.\n\nMake sure to also configure the webhook in Meta Dashboard:\n1. Go to Meta for Developers → Your App → WhatsApp → Configuration\n2. Set Webhook URL: ' + webhookUrl + '\n3. Set Verify Token: ' + verifyToken + '\n4. Click "Verify and Save"');
                } else {
                    alert('Failed to register webhook: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error registering webhook:', error);
                alert('An error occurred while registering webhook.');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        @else
            alert('No active WhatsApp Business accounts found. Please connect an account first.');
        @endif
    }

    // Check WhatsApp Webhook Status
    function checkWhatsAppWebhookStatus() {
        @if(isset($whatsappAccounts) && !empty($whatsappAccounts))
            const accounts = @json($whatsappAccounts);
            if (accounts.length === 0) {
                alert('No active WhatsApp Business accounts found.');
                return;
            }

            let wabaId = accounts[0].waba_id; // Default to first account
            if (accounts.length > 1) {
                const accountList = accounts.map((acc, idx) => `${idx + 1}. ${acc.name || acc.waba_id}`).join('\n');
                const choice = prompt(`Multiple accounts found. Enter account number (1-${accounts.length}):\n\n${accountList}`);
                if (choice && choice >= 1 && choice <= accounts.length) {
                    wabaId = accounts[choice - 1].waba_id;
                } else {
                    return; // User cancelled
                }
            }

            const url = '{{ route('whatsapp.business.checkWebhook', ['box_slug' => request()->route('box_slug'), 'wabaId' => ':wabaId']) }}'.replace(':wabaId', wabaId);

            const btn = document.getElementById('check-webhook-btn');
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking...';

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const status = data.subscribed ? '✓ Connected' : '⚠ Not Connected';
                    let message = `Webhook Status: ${status}\n\n`;
                    message += `Account: ${accounts.find(a => a.waba_id === wabaId)?.name || wabaId}\n`;
                    message += `Message: ${data.message || 'N/A'}`;
                    if (data.data && data.data.subscribed_fields) {
                        message += `\n\nSubscribed Fields: ${data.data.subscribed_fields.join(', ')}`;
                    }
                    alert(message);
                } else {
                    alert('Failed to check webhook status: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error checking webhook status:', error);
                alert('An error occurred while checking webhook status.');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        @else
            alert('No active WhatsApp Business accounts found.');
        @endif
    }
</script>
@endpush

@push('scripts')
<script>
    // Manual Token Entry (Workaround for Facebook Login issues)
    function showManualTokenEntry() {
        const token = prompt('Enter WhatsApp Business Access Token:\n\n' +
            'To get your access token:\n' +
            '1. Go to Meta for Developers → Your App\n' +
            '2. Go to Tools → Graph API Explorer\n' +
            '3. Select your app and generate a User Access Token\n' +
            '4. Add permissions: whatsapp_business_management, whatsapp_business_messaging\n' +
            '5. Copy the generated token and paste it here\n\n' +
            'Note: This is a temporary workaround. For production, use OAuth flow.');
        
        if (!token || token.trim() === '') {
            return;
        }

        // Save token manually
        const saveUrl = '{{ route('settings.integrations.facebook.save', ['box_slug' => request()->route('box_slug')]) }}';
        
        // Create a fake authorization flow by directly saving the token
        fetch(saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                access_token: token.trim(),
                type: 'whatsapp',
                manual_entry: true
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✓ Token saved successfully!\n\nWhatsApp Business API is now connected.');
                window.location.reload();
            } else {
                alert('Failed to save token: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error saving token:', error);
            alert('An error occurred while saving token.');
        });
    }

    $(document).ready(function() {
        // Fetch Mail Filter Configuration Form Options
        var isFormDataLoaded = false;
        
        function loadFormData() {
            if(isFormDataLoaded) return;
            
            $.ajax({
                url: '{{ route('settings.mail_filters.form_data', ['box_slug' => request()->route('box_slug') ?? ($box->slug ?? 'default')]) }}',
                type: 'GET',
                success: function(res) {
                    if (res.success) {
                        var d = res.data;
                        
                        // Populate Gmail Inboxes
                        d.gmailInboxes.forEach(function(i) {
                            $('#inbox_id').append(new Option(i.email, i.id));
                        });
                        
                        // Populate Templates
                        d.leadSourceTemplates.forEach(function(i) {
                            $('#template_id').append(new Option(i.name, i.id));
                        });
                        
                        // Populate Sources
                        d.leadSources.forEach(function(i) {
                            $('#lead_source_id').append(new Option(i.name, i.id));
                        });
                        
                        // Populate Status
                        d.leadsStatus.forEach(function(i) {
                            $('#lead_status_id').append(new Option(i.name, i.id));
                        });
                        
                        // Populate Users
                        $('#assigned_to').empty().append(new Option('--Select--', ''));
                        $('#round_robin_users').empty();
                        d.boxUsers.forEach(function(i) {
                            $('#assigned_to').append(new Option(i.name + ' (' + i.email + ')', i.id));
                            $('#round_robin_users').append(new Option(i.name, i.id));
                        });

                        window.globalData = { users: d.boxUsers };
                        isFormDataLoaded = true;
                    }
                }
            });
        }

        // Toggle Round Robin Fields
        $('#round_robin').on('change', function() {
            if ($(this).val() == '1') {
                $('#round_robin_users_div').removeClass('d-none');
                $('#shifts_config_div').removeClass('d-none');
                $('#assigned_to_div').addClass('d-none');
            } else {
                $('#round_robin_users_div').addClass('d-none');
                $('#shifts_config_div').addClass('d-none');
                $('#assigned_to_div').removeClass('d-none');
            }
        });

        // Handle enable shifts toggle
        $('#enable_shifts').on('change', function() {
            if ($(this).val() == 'Yes') {
                $('#shifts_container').show();
            } else {
                $('#shifts_container').hide();
            }
        });

        // Dynamic Shifts List when users are selected
        $(document).on('change', '#round_robin_users', function () {
            let selectedIds = $(this).val() || [];
            let listContainer = $('#shifts_list');

            // Update badge count
            $('.robin-count-badge').text(selectedIds.length + ' Selected');

            // Remove rows that are no longer selected
            listContainer.find('.user-shift-row').each(function () {
                let userId = $(this).data('user-id').toString();
                if (!selectedIds.includes(userId)) {
                    $(this).remove();
                }
            });

            // Add rows for new selections
            selectedIds.forEach(userId => {
                if (listContainer.find(`.user-shift-row[data-user-id="${userId}"]`).length === 0) {
                    let user = globalData.users.find(u => u.id == userId);
                    if (user) {
                        let newRow = `
                            <div class="card border mb-2 user-shift-row" data-user-id="${userId}">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-medium text-dark">${user.name} (${user.email})</span>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="shifts[${userId}][is_default]" value="1" id="default_${userId}">
                                            <label class="form-check-label small" for="default_${userId}">Default</label>
                                        </div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small mb-1">Start Time</label>
                                            <input type="time" name="shifts[${userId}][start]" class="form-control form-control-sm" value="09:00">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small mb-1">End Time</label>
                                            <input type="time" name="shifts[${userId}][end]" class="form-control form-control-sm" value="18:00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        listContainer.append(newRow);
                    }
                }
            });
        });

        // Form trigger functionality to dynamically set Template based on clicked card
        $(document).on('click', '.btn-primary', function() {
            if ($(this).text().trim() === 'Configure') {
                var titleMatch = $(this).closest('.lead-inner').find('h4').text().trim();
                
                // Load data immediately if not loaded yet
                if(!isFormDataLoaded) {
                    loadFormData();
                }
                
                // Reset form
                $('#mailFilterForm')[0].reset();
                $('#filter_name').val(titleMatch + ' - Filter');
                
                // Delay slightly to ensure AJAX population if it was just called
                setTimeout(function() {
                    $('#template_id option').each(function() {
                        if ($(this).text().trim().toLowerCase() === titleMatch.toLowerCase()) {
                            $(this).prop('selected', true);
                        }
                    });
                    
                    if($.fn.select2) {
                        $('#template_id, #inbox_id, #lead_source_id, #lead_status_id, #assigned_to, #status, #round_robin, #round_robin_users, #enable_shifts').select2({
                            dropdownParent: $('#offcanvas_add'),
                            width: '100%',
                            placeholder: '--Select--'
                        });

                        // Has words as tags
                        $('#has_words').select2({
                            tags: true,
                            tokenSeparators: [','],
                            dropdownParent: $('#offcanvas_add'),
                            width: '100%',
                            placeholder: 'Add keywords...'
                        });

                        $('#template_id').trigger('change');
                    }
                }, 500);
            }
        });

        // Initialize call
        loadFormData();

        // AJAX Form Submission for Mail Filters
        $('#mailFilterForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = form.serializeArray();
            var hasWords = [];
            
            $('#has_words option:selected').each(function() {
                hasWords.push($(this).val());
            });
            
            formData = formData.filter(item => item.name !== 'has_words[]');
            formData.push({ name: 'has_words', value: hasWords.join(',') });

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('.custom-btn-close').trigger('click');
                    } else {
                        alert(response.message || 'Error saving mail filter configuration.');
                    }
                },
                error: function(xhr) {
                    try {
                        let res = JSON.parse(xhr.responseText);
                        alert('Error: ' + (res.message || 'Invalid input data.'));
                    } catch(e) {
                        alert('An unexpected error occurred while saving.');
                    }
                }
            });
        });
    });

    function quickAddSource() {
        var sourceName = prompt('Enter new Lead Source name:');
        if (!sourceName || sourceName.trim() === '') return;
        
        $.ajax({
            url: '{{ route('store.source', ['box_slug' => request()->route('box_slug') ?? ($box->slug ?? 'default')]) }}',
            type: 'POST',
            data: {
                name: sourceName,
                type: 'source',
                status: 1,
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                if(res.success && res.data) {
                    var newOption = new Option(res.data.name, res.data.id, false, true);
                    $('#lead_source_id').append(newOption).trigger('change');
                    alert('Lead Source added successfully.');
                } else {
                    alert(res.message || 'Failed to add source.');
                }
            },
            error: function(xhr) {
                try {
                    let err = JSON.parse(xhr.responseText);
                    alert('Error: ' + err.message);
                } catch(e) {
                    alert('Error creating source');
                }
            }
        });
    }

</script>
@endpush
