@extends('layouts.main')
@section('title', 'WhatsApp Chat')
@section('content')

	<div class="page-wrapper">
		<div class="content">

			<div class="row">
				<div class="col-md-12">

					<!-- Page Header -->
					<div class="page-header">
						<div class="row align-items-center">
							<div class="col-4 ft">
								<h4 class="page-title">WhatsApp Chat</h4>
							</div>
							<div class="col-8 text-end">
								<div class="head-icons">
									<a href="javascript:void(0);" onclick="window.location.reload();" data-bs-toggle="tooltip"
										data-bs-placement="top" data-bs-original-title="Refresh">
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

					<div class="card shadow-none border-0 mb-0">
						<div class="card-body p-0">
							<div class="whatsapp-chat-container">




								<!-- sidebar group -->
								<div class="sidebar-group left-sidebar chat_sidebar">

									<!-- Chats sidebar -->
									<div id="chats" class="left-sidebar-wrap chat-message-sidebar active slimscroll">


										<div class="slimscroll-active-sidebar">

											<!-- Left Chat Title -->
											<div
												class="left-chat-title all-chats d-flex justify-content-between align-items-center">
												<div class="setting-title-head">
													<h4> All Chats</h4>
												</div>
												<div class="add-section">
													<ul>
														<li><a href="javascript:void(0);" class="user-chat-search-btn"><i
																	class="bx bx-search"></i></a></li>
														<li>
															<div class="chat-action-btns">
																<div class="chat-action-col">
																	<a class="#" href="#" data-bs-toggle="dropdown"
																		aria-expanded="false">
																		<i class="bx bx-dots-vertical-rounded"></i>
																	</a>
																	<div class="dropdown-menu dropdown-menu-end">
																		<a href="javascript:void(0);" class="dropdown-item "
																			data-bs-toggle="modal"
																			data-bs-target="#new-chat"><span><i
																					class="bx bx-message-rounded-add"></i></span>New
																			Chat </a>
																		<a href="javascript:void(0);" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#new-group"><span><i
																					class="bx bx-user-circle"></i></span>Create
																			Group</a>
																		<a href="javascript:void(0);" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#invite-other"><span><i
																					class="bx bx-user-plus"></i></span>Invite
																			Others</a>
																	</div>
																</div>
															</div>
														</li>
													</ul>
													<!-- Chat Search -->
													<div class="user-chat-search">
														<form>
															<span class="form-control-feedback"><i
																	class="bx bx-search"></i></span>
															<input type="text" name="chat-search" placeholder="Search"
																class="form-control">
															<div class="user-close-btn-chat"><i class="fa fa-close"></i>
															</div>
														</form>
													</div>
													<!-- /Chat Search -->
												</div>
											</div>
											<!-- /Left Chat Title -->


											<div class="sidebar-body chat-body p-3" id="chatsidebar">
												<div class="chat-searchg">

													<form action="#" class="dropdown">
														<input type="text" class="form-control"
															placeholder="Search Or Start New Chat">
													</form>
												</div>
												<div class="card-body">
													<ul class="nav nav-tabs nav-tabs-bottom mb-3">
														<li class="nav-item"><a class="nav-link ps-2 pe-2 active"
																href="#bottom-tab1" data-bs-toggle="tab">Active (2)</a></li>
														<li class="nav-item"><a class="nav-link ps-2 pe-2"
																href="#bottom-tab2" data-bs-toggle="tab">Inactive (5)</a>
														</li>
														<li class="nav-item"><a class="nav-link ps-2 pe-2"
																href="#bottom-tab3" data-bs-toggle="tab">Not Contacted (0)</a>
														</li>
													</ul>

													<div class="tab-content pt-3">
														<div class="tab-pane show active" id="bottom-tab1">
															<ul class="user-list">
																<li class="user-list-item">
																	<a href="javascript:void(0);">
																		<div class="avatar avatar-online">
																			<img src="assets/img/profiles/avatar-02.jpg"
																				class="rounded-circle" alt="image">
																		</div>
																		<div class="users-list-body">
																			<div>
																				<h5>Mark Villiams</h5>
																				<p>Have you called them?</p>
																			</div>
																			<div class="last-chat-time">
																				<small class="text-muted">10:20 PM</small>
																				<div class="chat-pin">
																					<i class="bx bx-pin me-2"></i>
																					<i class="bx bx-check-double"></i>
																				</div>
																			</div>
																		</div>
																	</a>
																</li>
																<li class="user-list-item">
																	<a href="javascript:void(0);">
																		<div class="avatar ">
																			<img src="assets/img/profiles/avatar-01.jpg"
																				class="rounded-circle" alt="image">
																		</div>
																		<div class="users-list-body">
																			<div>
																				<h5>Elizabeth Sosa</h5>
																				<p><span class="animate-typing-col">Typing
																						<span class="dot"></span>
																						<span class="dot"></span>
																						<span class="dot"></span>
																					</span>
																				</p>
																			</div>
																			<div class="last-chat-time">
																				<small class="text-muted">Yesterday</small>
																				<div class="chat-pin">
																					<i class="bx bx-pin"></i>
																				</div>
																			</div>
																		</div>
																	</a>
																</li>
																<li class="user-list-item">
																	<a href="javascript:void(0);">
																		<div class="avatar avatar-online">
																			<img src="assets/img/profiles/avatar-05.jpg"
																				class="rounded-circle" alt="image">
																		</div>
																		<div class="users-list-body">
																			<div>
																				<h5>Michael Howard</h5>
																				<p>Thank you</p>
																			</div>
																			<div class="last-chat-time">
																				<small class="text-muted">10:20 PM</small>
																				<div class="chat-pin">
																					<i class="bx bx-pin me-2"></i>
																					<i
																						class="bx bx-check-double green-check"></i>
																				</div>
																			</div>
																		</div>
																	</a>
																</li>



																<li class="user-list-item">
																	<a href="javascript:void(0);">
																		<div class="avatar avatar-online">
																			<img src="assets/img/profiles/avatar-03.jpg"
																				class="rounded-circle" alt="image">
																		</div>
																		<div class="users-list-body">
																			<div>
																				<h5>Horace Keene</h5>
																				<p>Have you called them?</p>
																			</div>
																			<div class="last-chat-time">
																				<small class="text-muted">Just Now</small>
																				<div class="new-message-count">11</div>
																			</div>
																		</div>
																	</a>
																</li>
																<li class="user-list-item">
																	<a href="javascript:void(0);">
																		<div class="avatar avatar-online">
																			<img src="assets/img/profiles/avatar-03.jpg"
																				class="rounded-circle" alt="image">
																		</div>
																		<div class="users-list-body">
																			<div>
																				<h5>Hollis Tran</h5>
																				<p><i class="bx bx-video me-1"></i>Video</p>
																			</div>
																			<div class="last-chat-time">
																				<small class="text-muted">Yesterday</small>
																				<div class="chat-pin">
																					<i class="bx bx-check"></i>
																				</div>
																			</div>
																		</div>
																	</a>
																</li>
																<li class="user-list-item">
																	<a href="javascript:void(0);">
																		<div class="avatar">
																			<img src="assets/img/profiles/avatar-04.jpg"
																				class="rounded-circle" alt="image">
																		</div>
																		<div class="users-list-body">
																			<div>
																				<h5>James Albert</h5>
																				<p><i class="bx bx-file me-1"></i>Project
																					Tools.doc</p>
																			</div>
																			<div class="last-chat-time">
																				<small class="text-muted">10:20 PM</small>

																			</div>
																		</div>
																	</a>
																</li>
																<li class="user-list-item">
																	<a href="javascript:void(0);">
																		<div class="avatar avatar-online">
																			<img src="assets/img/profiles/avatar-09.jpg"
																				class="rounded-circle" alt="image">
																		</div>
																		<div class="users-list-body">
																			<div>
																				<h5>Debra Jones</h5>
																				<p><i
																						class="bx bx-microphone me-1"></i>Audio
																				</p>
																			</div>
																			<div class="last-chat-time">
																				<small class="text-muted">12:30 PM</small>
																				<div class="chat-pin">
																					<i
																						class="bx bx-check-double green-check"></i>
																				</div>
																			</div>
																		</div>
																	</a>
																</li>
																<li class="user-list-item">
																	<a href="javascript:void(0);">
																		<div class="avatar ">
																			<img src="assets/img/profiles/avatar-07.jpg"
																				class="rounded-circle" alt="image">
																		</div>
																		<div class="users-list-body">
																			<div>
																				<h5>Dina Brown</h5>
																				<p>Have you called them?</p>
																			</div>
																			<div class="last-chat-time">
																				<small class="text-muted">Yesterday</small>
																				<div class="chat-pin">
																					<i class="bx bx-microphone-off"></i>
																				</div>
																			</div>
																		</div>
																	</a>
																</li>
																<li class="user-list-item">
																	<a href="javascript:void(0);">
																		<div class="avatar avatar-online">
																			<img src="assets/img/profiles/avatar-08.jpg"
																				class="rounded-circle" alt="image">
																		</div>
																		<div class="users-list-body">
																			<div>
																				<h5>Judy Mercer</h5>
																				<p class="missed-call-col"><i
																						class="bx bx-phone-incoming me-1"></i>Missed
																					Call</p>
																			</div>
																			<div class="last-chat-time">
																				<small class="text-muted">25/July/23</small>
																			</div>
																		</div>
																	</a>
																</li>
																<li class="user-list-item">
																	<a href="javascript:void(0);">
																		<div class="avatar">
																			<img src="assets/img/profiles/avatar-06.jpg"
																				class="rounded-circle" alt="image">
																		</div>
																		<div class="users-list-body">
																			<div>
																				<h5>Richard Ohare</h5>
																				<p><i class="bx bx-image-alt me-1"></i>Photo
																				</p>
																			</div>
																			<div class="last-chat-time">
																				<small class="text-muted">27/July/23</small>
																				<div class="chat-pin">
																					<i class="bx bx-check-double"></i>
																				</div>
																			</div>
																		</div>
																	</a>
																</li>

															</ul>



















														</div>
														<div class="tab-pane" id="bottom-tab2">
															<img src="assets/img/whatsapp/empty-chat.png">
														</div>
														<div class="tab-pane" id="bottom-tab3">
															<img src="assets/img/whatsapp/empty-chat.png">
														</div>
													</div>
												</div>


											</div>

										</div>

									</div>
									<!-- / Chats sidebar -->
								</div>
								<!-- /Sidebar group -->

								<!-- Chat -->
								<div class="chat chat-messages" id="middle">
									<div class="slimscroll">
										<div class="chat-header">
											<div class="user-details">
												<div class="d-lg-none">
													<ul class="list-inline mt-2 me-2">
														<li class="list-inline-item">
															<a class="text-muted px-0 left_sides" href="#" data-chat="open">
																<i class="fas fa-arrow-left"></i>
															</a>
														</li>
													</ul>
												</div>
												<figure class="avatar ms-1">
													<img src="assets/img/profiles/avatar-02.jpg" class="rounded-circle"
														alt="image">
												</figure>
												<div class="mt-1">
													<h5>Mark Villiams</h5>
													<small class="last-seen">
														Last Seen at 07:15 PM
													</small>
												</div>
											</div>
											<div class="chat-options ">
												<ul class="list-inline">
													<li class="list-inline-item">
														<a href="javascript:void(0)"
															class="btn btn-outline-light chat-search-btn"
															data-bs-toggle="tooltip" data-bs-placement="bottom"
															title="Search">
															<i class="bx bx-search"></i>
														</a>
													</li>
													<li class="list-inline-item">
														<a href="video-call.html" class="btn btn-outline-light"
															data-bs-toggle="tooltip" data-bs-placement="bottom"
															title="Video Call">
															<i class="bx bx-video"></i>
														</a>
													</li>
													<li class="list-inline-item">
														<a href="audio-call.html" class="btn btn-outline-light"
															data-bs-toggle="tooltip" data-bs-placement="bottom"
															title="Voice Call">
															<i class="bx bx-phone"></i>
														</a>
													</li>
													<li class="list-inline-item dream_profile_menu">
														<a href="javascript:void(0)"
															class="btn btn-outline-light not-chat-user">
															<i class="bx bx-info-circle"></i>
														</a>
													</li>
													<li class="list-inline-item">
														<a class="btn btn-outline-light no-bg" href="#"
															data-bs-toggle="dropdown">
															<i class="bx bx-dots-vertical-rounded"></i>
														</a>
														<div class="dropdown-menu dropdown-menu-end">
															<a href="#" class="dropdown-item "><span><i
																		class="bx bx-x"></i></span>Close
																Chat </a>
															<a href="#" class="dropdown-item" data-bs-toggle="modal"
																data-bs-target="#mute-notification"><span><i
																		class="bx bx-volume-mute"></i></span>Mute
																Notification</a>
															<a href="#" class="dropdown-item" data-bs-toggle="modal"
																data-bs-target="#disappearing-messages"><span><i
																		class="bx bx-time-five"></i></span>Disappearing
																Message</a>
															<a href="#" class="dropdown-item" data-bs-toggle="modal"
																data-bs-target="#clear-chat"><span><i
																		class="bx bx-brush-alt"></i></span>Clear Message</a>
															<a href="#" class="dropdown-item" data-bs-toggle="modal"
																data-bs-target="#change-chat"><span><i
																		class="bx bx-trash-alt"></i></span>Delete Chat</a>
															<a href="#" class="dropdown-item" data-bs-toggle="modal"
																data-bs-target="#report-user"><span><i
																		class="bx bx-dislike"></i></span>Report</a>
															<a href="#" class="dropdown-item" data-bs-toggle="modal"
																data-bs-target="#block-user"><span><i
																		class="bx bx-block"></i></span>Block</a>
														</div>
													</li>
												</ul>
											</div>
											<!-- Chat Search -->
											<div class="chat-search">
												<form>
													<span class="form-control-feedback"><i class="bx bx-search"></i></span>
													<input type="text" name="chat-search" placeholder="Search Chats"
														class="form-control">
													<div class="close-btn-chat"><i class="fa fa-close"></i></div>
												</form>
											</div>
											<!-- /Chat Search -->
										</div>
										<div class="chat-body">
											<div class="messages">
												<div class="chats">
													<div class="chat-avatar">
														<img src="assets/img/profiles/avatar-02.jpg"
															class="rounded-circle dreams_chat" alt="image">
													</div>
													<div class="chat-content">
														<div class="chat-profile-name">
															<h6>Mark Villiams<span>8:16 PM</span></h6>
															<div class="chat-action-btns ms-3">
																<div class="chat-action-col">
																	<a class="#" href="#" data-bs-toggle="dropdown">
																		<i class="bx bx-dots-horizontal-rounded"></i>
																	</a>
																	<div
																		class="dropdown-menu chat-drop-menu dropdown-menu-end">
																		<a href="#"
																			class="dropdown-item message-info-left"><span><i
																					class="bx bx-info-circle"></i></span>Message
																			Info
																		</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-share"></i></span>Reply</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-smile"></i></span>React</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#forward-message"><span><i
																					class="bx bx-reply"></i></span>Forward</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-star"></i></span>Star
																			Message</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#report-user"><span><i
																					class="bx bx-dislike"></i></span>Report</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#delete-message"><span><i
																					class="bx bx-trash"></i></span>Delete</a>
																	</div>
																</div>
															</div>
														</div>
														<div class="message-content">
															Hello <a href="javascript:void(0);">@Alex</a> Thank you for the
															beautiful
															web design ahead schedule.
															<div class="emoj-group">
																<ul>
																	<li class="emoj-action"><a href="javascript:void(0);"><i
																				class="bx bx-smile"></i></a>
																		<div class="emoj-group-list">
																			<ul>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-01.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-02.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-03.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-04.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-05.svg"
																							alt="Icon"></a></li>
																				<li class="add-emoj"><a
																						href="javascript:void(0);"><i
																							class="bx bx-plus"></i></a></li>
																			</ul>
																		</div>
																	</li>
																	<li><a href="#" data-bs-toggle="modal"
																			data-bs-target="#forward-message"><i
																				class="bx bx-share"></i></a></li>
																</ul>
															</div>
														</div>
													</div>
												</div>
												<div class="chat-line">
													<span class="chat-date">Today, July 24</span>
												</div>
												<div class="chats chats-right">
													<div class="chat-content">
														<div class="chat-profile-name text-end">
															<h6>Alex Smith<span>8:16 PM</span></h6>
															<div class="chat-action-btns ms-3">
																<div class="chat-action-col">
																	<a class="#" href="#" data-bs-toggle="dropdown">
																		<i class="bx bx-dots-horizontal-rounded"></i>
																	</a>
																	<div
																		class="dropdown-menu chat-drop-menu dropdown-menu-end">
																		<a href="#"
																			class="dropdown-item message-info-left"><span><i
																					class="bx bx-info-circle"></i></span>Message
																			Info
																		</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-share"></i></span>Reply</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-smile"></i></span>React</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#forward-message"><span><i
																					class="bx bx-reply"></i></span>Forward</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-star"></i></span>Star
																			Message</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#report-user"><span><i
																					class="bx bx-dislike"></i></span>Report</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#delete-message"><span><i
																					class="bx bx-trash"></i></span>Delete</a>
																	</div>
																</div>
															</div>
														</div>
														<div class="message-content ">
															<div class="emoj-group rig-emoji-group">
																<ul>
																	<li class="emoj-action"><a href="javascript:void(0);"><i
																				class="bx bx-smile"></i></a>
																		<div class="emoj-group-list">
																			<ul>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-01.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-02.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-03.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-04.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-05.svg"
																							alt="Icon"></a></li>
																				<li class="add-emoj"><a
																						href="javascript:void(0);"><i
																							class="bx bx-plus"></i></a></li>
																			</ul>
																		</div>
																	</li>
																	<li><a href="#" data-bs-toggle="modal"
																			data-bs-target="#forward-message"><i
																				class="bx bx-share"></i></a></li>
																</ul>
															</div>
															<div class="chat-voice-group">
																<ul>
																	<li><a href="javascript:void(0);"><span><img
																					src="assets/img/icons/play-01.svg"
																					alt="image"></span></a></li>
																	<li><img src="assets/img/icons/voice.svg" alt="image">
																	</li>
																	<li>0:05</li>
																</ul>
															</div>
														</div>
													</div>
													<div class="chat-avatar">
														<img src="assets/img/profiles/avatar-10.jpg"
															class="rounded-circle dreams_chat" alt="image">
													</div>
												</div>
												<div class="chats">
													<div class="chat-avatar">
														<img src="assets/img/profiles/avatar-02.jpg"
															class="rounded-circle dreams_chat" alt="image">
													</div>
													<div class="chat-content">
														<div class="chat-profile-name">
															<h6>Mark Villiams<span>8:16 PM</span><span class="check-star"><i
																		class="bx bxs-star"></i></span></h6>
															<div class="chat-action-btns ms-2">
																<div class="chat-action-col">
																	<a class="#" href="#" data-bs-toggle="dropdown">
																		<i class="bx bx-dots-horizontal-rounded"></i>
																	</a>
																	<div
																		class="dropdown-menu chat-drop-menu dropdown-menu-end">
																		<a href="#"
																			class="dropdown-item message-info-left"><span><i
																					class="bx bx-info-circle"></i></span>Message
																			Info
																		</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-share"></i></span>Reply</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-smile"></i></span>React</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#forward-message"><span><i
																					class="bx bx-reply"></i></span>Forward</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bxs-star"></i></span>Unstar
																			Message</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#report-user"><span><i
																					class="bx bx-dislike"></i></span>Report</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#delete-message"><span><i
																					class="bx bx-trash"></i></span>Delete</a>
																	</div>
																</div>
															</div>
														</div>
														<div class="message-content award-link chat-award-link">
															<a href="javascript:void(0);"
																class="mb-1">https://www.youtube.com/watch?v=GCmL3mS0Psk</a>
															<img src="assets/img/sending-img.png" alt="img">
															<div class="emoj-group">
																<ul>
																	<li class="emoj-action"><a href="javascript:void(0);"><i
																				class="bx bx-smile"></i></a>
																		<div class="emoj-group-list">
																			<ul>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-01.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-02.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-03.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-04.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-05.svg"
																							alt="Icon"></a></li>
																				<li class="add-emoj"><a
																						href="javascript:void(0);"><i
																							class="bx bx-plus"></i></a></li>
																			</ul>
																		</div>
																	</li>
																	<li><a href="#" data-bs-toggle="modal"
																			data-bs-target="#forward-message"><i
																				class="bx bx-share"></i></a></li>
																</ul>
															</div>
														</div>
													</div>
												</div>
												<div class="chats chats-right">
													<div class="chat-content">
														<div class="chat-profile-name justify-content-end">
															<h6>Alex Smith<span>8:16 PM</span></h6>
															<div class="chat-action-btns ms-3">
																<div class="chat-action-col">
																	<a class="#" href="#" data-bs-toggle="dropdown">
																		<i class="bx bx-dots-horizontal-rounded"></i>
																	</a>
																	<div
																		class="dropdown-menu chat-drop-menu dropdown-menu-end">
																		<a href="#"
																			class="dropdown-item message-info-left"><span><i
																					class="bx bx-info-circle"></i></span>Message
																			Info
																		</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-share"></i></span>Reply</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-smile"></i></span>React</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#forward-message"><span><i
																					class="bx bx-reply"></i></span>Forward</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-star"></i></span>Star
																			Message</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#report-user"><span><i
																					class="bx bx-dislike"></i></span>Report</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#delete-message"><span><i
																					class="bx bx-trash"></i></span>Delete</a>
																	</div>
																</div>
															</div>
														</div>
														<div class="message-content fancy-msg-box">
															<div class="emoj-group wrap-emoji-group ">
																<ul>
																	<li class="emoj-action"><a href="javascript:void(0);"><i
																				class="bx bx-smile"></i></a>
																		<div class="emoj-group-list">
																			<ul>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-01.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-02.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-03.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-04.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-05.svg"
																							alt="Icon"></a></li>
																				<li class="add-emoj"><a
																						href="javascript:void(0);"><i
																							class="bx bx-plus"></i></a></li>
																			</ul>
																		</div>
																	</li>
																	<li><a href="javascript:void(0);" data-bs-toggle="modal"
																			data-bs-target="#forward-message"><i
																				class="bx bx-share"></i></a></li>
																</ul>
															</div>
															<div class="download-col">
																<ul class="nav mb-0">
																	<li>
																		<div class="image-download-col">
																			<a href="assets/img/media/media-02.jpg"
																				data-fancybox="gallery" class="fancybox">
																				<img src="assets/img/media/media-02.jpg"
																					alt="">
																			</a>

																		</div>
																	</li>
																	<li>
																		<div class="image-download-col ">
																			<a href="assets/img/media/media-03.jpg"
																				data-fancybox="gallery" class="fancybox">
																				<img src="assets/img/media/media-03.jpg"
																					alt="">
																			</a>
																		</div>
																	</li>
																	<li>
																		<div class="image-download-col image-not-download">
																			<a href="assets/img/media/media-01.jpg"
																				data-fancybox="gallery" class="fancybox">
																				<img src="assets/img/media/media-01.jpg"
																					alt="">
																				<span>10+</span>
																			</a>
																		</div>
																	</li>
																</ul>
															</div>
														</div>
													</div>
													<div class="chat-avatar">
														<img src="assets/img/profiles/avatar-10.jpg"
															class="rounded-circle dreams_chat" alt="image">
													</div>
												</div>

												<div class="chats">
													<div class="chat-avatar">
														<img src="assets/img/profiles/avatar-02.jpg"
															class="rounded-circle dreams_chat" alt="image">
													</div>
													<div class="chat-content">
														<div class="chat-profile-name">
															<h6>Mark Villiams<span>8:16 PM</span></h6>
															<div class="chat-action-btns ms-3">
																<div class="chat-action-col">
																	<a class="#" href="#" data-bs-toggle="dropdown">
																		<i class="bx bx-dots-horizontal-rounded"></i>
																	</a>
																	<div
																		class="dropdown-menu chat-drop-menu dropdown-menu-end">
																		<a href="#"
																			class="dropdown-item message-info-left"><span><i
																					class="bx bx-info-circle"></i></span>Message
																			Info
																		</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-share"></i></span>Reply</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-smile"></i></span>React</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#forward-message"><span><i
																					class="bx bx-reply"></i></span>Forward</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-star"></i></span>Star
																			Message</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#report-user"><span><i
																					class="bx bx-dislike"></i></span>Report</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#delete-message"><span><i
																					class="bx bx-trash"></i></span>Delete</a>
																	</div>
																</div>
															</div>
														</div>
														<div class="message-content review-files">
															<p class="d-flex align-items-center">Please check and review the
																files<span class="ms-1 d-flex"><img
																		src="assets/img/icons/smile-chat.svg"
																		alt="Icon"></span></p>
															<div class="file-download d-flex align-items-center mb-0">
																<div
																	class="file-type d-flex align-items-center justify-content-center me-2">
																	<i class="bx bxs-file-doc"></i>
																</div>
																<div class="file-details">
																	<span class="file-name">Landing_page_V1.doc</span>
																	<ul>
																		<li>80 Bytes</li>
																		<li><a href="javascript:void(0);">Download</a></li>
																	</ul>
																</div>
															</div>
															<div class="emoj-group">
																<ul>
																	<li class="emoj-action"><a href="javascript:void(0);"><i
																				class="bx bx-smile"></i></a>
																		<div class="emoj-group-list">
																			<ul>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-01.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-02.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-03.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-04.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-05.svg"
																							alt="Icon"></a></li>
																				<li class="add-emoj"><a
																						href="javascript:void(0);"><i
																							class="bx bx-plus"></i></a></li>
																			</ul>
																		</div>
																	</li>
																	<li><a href="#" data-bs-toggle="modal"
																			data-bs-target="#forward-message"><i
																				class="bx bx-share"></i></a></li>
																</ul>
															</div>
														</div>
														<div class="like-chat-grp">
															<ul>
																<li class="like-chat"><a href="javascript:void(0);">2<img
																			src="assets/img/icons/like.svg" alt="Icon"></a>
																</li>
																<li class="comment-chat"><a href="javascript:void(0);">2<img
																			src="assets/img/icons/heart.svg" alt="Icon"></a>
																</li>
															</ul>
														</div>
													</div>
												</div>
												<div class="chats">
													<div class="chat-avatar">
														<img src="assets/img/profiles/avatar-02.jpg"
															class="rounded-circle dreams_chat" alt="image">
													</div>
													<div class="chat-content">
														<div class="chat-profile-name">
															<h6>Mark Villiams<span>8:16 PM</span></h6>
															<div class="chat-action-btns ms-3">
																<div class="chat-action-col">
																	<a class="#" href="#" data-bs-toggle="dropdown">
																		<i class="bx bx-dots-horizontal-rounded"></i>
																	</a>
																	<div
																		class="dropdown-menu chat-drop-menu dropdown-menu-end">
																		<a href="#"
																			class="dropdown-item message-info-left"><span><i
																					class="bx bx-info-circle"></i></span>Message
																			Info
																		</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-share"></i></span>Reply</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-smile"></i></span>React</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#forward-message"><span><i
																					class="bx bx-reply"></i></span>Forward</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-star"></i></span>Star
																			Message</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#report-user"><span><i
																					class="bx bx-dislike"></i></span>Report</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#delete-message"><span><i
																					class="bx bx-trash"></i></span>Delete</a>
																	</div>
																</div>
															</div>
														</div>
														<div class="message-content">
															Thank you for your support
															<div class="emoj-group">
																<ul>
																	<li class="emoj-action"><a href="javascript:void(0);"><i
																				class="bx bx-smile"></i></a>
																		<div class="emoj-group-list">
																			<ul>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-01.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-02.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-03.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-04.svg"
																							alt="Icon"></a></li>
																				<li><a href="javascript:void(0);"><img
																							src="assets/img/icons/emoj-icon-05.svg"
																							alt="Icon"></a></li>
																				<li class="add-emoj"><a
																						href="javascript:void(0);"><i
																							class="bx bx-plus"></i></a></li>
																			</ul>
																		</div>
																	</li>
																	<li><a href="javascript:void(0);" data-bs-toggle="modal"
																			data-bs-target="#forward-message"><i
																				class="bx bx-share"></i></a></li>
																</ul>
															</div>
														</div>
													</div>
												</div>

												<div class="chats">
													<div class="chat-avatar">
														<img src="assets/img/profiles/avatar-02.jpg"
															class="rounded-circle dreams_chat" alt="image">
													</div>
													<div class="chat-content chat-cont-type">
														<div class="chat-profile-name chat-type-wrapper">
															<p>Mark Villiams Typing...</p>
														</div>
													</div>
												</div>




												<div class="chats forward-chat-msg">
													<div class="chat-avatar">
														<img src="assets/img/profiles/avatar-02.jpg"
															class="rounded-circle dreams_chat" alt="image">
													</div>
													<div class="chat-content">
														<div class="chat-profile-name">
															<h6>Mark Villiams<span>8:16 PM</span></h6>
															<div class="chat-action-btns ms-3">
																<div class="chat-action-col">
																	<a class="#" href="#" data-bs-toggle="dropdown">
																		<i class="bx bx-dots-horizontal-rounded"></i>
																	</a>
																	<div
																		class="dropdown-menu chat-drop-menu dropdown-menu-end">
																		<a href="#"
																			class="dropdown-item message-info-left"><span><i
																					class="bx bx-info-circle"></i></span>Message
																			Info
																		</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-share"></i></span>Reply</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-smile"></i></span>React</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#forward-message"><span><i
																					class="bx bx-reply"></i></span>Forward</a>
																		<a href="#" class="dropdown-item"><span><i
																					class="bx bx-star"></i></span>Star
																			Message</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#report-user"><span><i
																					class="bx bx-dislike"></i></span>Report</a>
																		<a href="#" class="dropdown-item"
																			data-bs-toggle="modal"
																			data-bs-target="#delete-message"><span><i
																					class="bx bx-trash"></i></span>Delete</a>
																	</div>
																</div>
															</div>
														</div>
														<div class="message-content">
															Thank you for your support
														</div>
													</div>
												</div>

											</div>
										</div>
									</div>
									<div class="chat-footer">
										<form>
											<div class="smile-foot">
												<div class="chat-action-btns">
													<div class="chat-action-col">
														<a class="action-circle" href="#" data-bs-toggle="dropdown">
															<i class="bx bx-dots-vertical-rounded"></i>
														</a>
														<div class="dropdown-menu dropdown-menu-end">
															<a href="#" class="dropdown-item "><span><i
																		class="bx bx-file"></i></span>Document</a>
															<a href="#" class="dropdown-item"><span><i
																		class="bx bx-camera"></i></span>Camera</a>
															<a href="#" class="dropdown-item"><span><i
																		class="bx bx-image"></i></span>Gallery</a>
															<a href="#" class="dropdown-item"><span><i
																		class="bx bx-volume-full"></i></span>Audio</a>
															<a href="#" class="dropdown-item"><span><i
																		class="bx bx-map"></i></span>Location</a>
															<a href="#" class="dropdown-item"><span><i
																		class="bx bx-user-pin"></i></span>Contact</a>
														</div>
													</div>
												</div>
											</div>
											<div class="smile-foot emoj-action-foot">
												<a href="#" class="action-circle"><i class="bx bx-smile"></i></a>
												<div class="emoj-group-list-foot down-emoji-circle">
													<ul>
														<li><a href="javascript:void(0);">😀</a></li>
														<li><a href="javascript:void(0);">😁</a></li>
														<li><a href="javascript:void(0);">😂</a></li>
														<li><a href="javascript:void(0);">😃</a></li>
														<li><a href="javascript:void(0);">😄</a></li>
														<li><a href="javascript:void(0);">😅</a></li>
														<li><a href="javascript:void(0);">😆</a></li>
														<li><a href="javascript:void(0);">😇</a></li>
														<li><a href="javascript:void(0);">😈</a></li>
														<li><a href="javascript:void(0);">😉</a></li>
														<li><a href="javascript:void(0);">😊</a></li>
														<li><a href="javascript:void(0);">😋</a></li>
														<li><a href="javascript:void(0);">😌</a></li>
														<li><a href="javascript:void(0);">😍</a></li>
														<li><a href="javascript:void(0);">😎</a></li>
														<li><a href="javascript:void(0);">😏</a></li>
														<li><a href="javascript:void(0);">😐</a></li>
														<li><a href="javascript:void(0);">😑</a></li>
														<li><a href="javascript:void(0);">😒</a></li>
														<li><a href="javascript:void(0);">😓</a></li>
														<li><a href="javascript:void(0);">😔</a></li>
														<li><a href="javascript:void(0);">😕</a></li>
														<li><a href="javascript:void(0);">😖</a></li>
														<li><a href="javascript:void(0);">😗</a></li>
														<li><a href="javascript:void(0);">😘</a></li>
														<li><a href="javascript:void(0);">😙</a></li>
														<li><a href="javascript:void(0);">😚</a></li>
														<li><a href="javascript:void(0);">😛</a></li>
														<li><a href="javascript:void(0);">😜</a></li>
														<li><a href="javascript:void(0);">😝</a></li>
														<li><a href="javascript:void(0);">😞</a></li>
														<li><a href="javascript:void(0);">😟</a></li>
														<li><a href="javascript:void(0);">😠</a></li>
														<li><a href="javascript:void(0);">😡</a></li>
														<li><a href="javascript:void(0);">😢</a></li>
														<li><a href="javascript:void(0);">😣</a></li>
														<li><a href="javascript:void(0);">😤</a></li>
														<li><a href="javascript:void(0);">😥</a></li>
														<li><a href="javascript:void(0);">😦</a></li>
														<li><a href="javascript:void(0);">😧</a></li>
														<li><a href="javascript:void(0);">😨</a></li>
														<li><a href="javascript:void(0);">😩</a></li>
														<li><a href="javascript:void(0);">😪</a></li>
														<li><a href="javascript:void(0);">😫</a></li>
														<li><a href="javascript:void(0);">😬</a></li>
														<li><a href="javascript:void(0);">😭</a></li>
														<li><a href="javascript:void(0);">😮</a></li>
														<li><a href="javascript:void(0);">😯</a></li>
														<li><a href="javascript:void(0);">😰</a></li>
														<li><a href="javascript:void(0);">😱</a></li>
														<li><a href="javascript:void(0);">😲</a></li>
														<li><a href="javascript:void(0);">😳</a></li>
														<li><a href="javascript:void(0);">😴</a></li>
														<li class="add-emoj"><a href="javascript:void(0);"><i
																	class="bx bx-plus"></i></a></li>
													</ul>
												</div>
											</div>
											<div class="smile-foot">
												<a href="#" class="action-circle"><i class="bx bx-microphone-off"></i></a>
											</div>
											<input type="text" class="form-control chat_form"
												placeholder="Type your message here...">
											<div class="form-buttons">
												<button class="btn send-btn" type="submit">
													<i class="bx bx-paper-plane"></i>
												</button>
											</div>
										</form>
									</div>
								</div>
								<!-- /Chat -->

								<!-- Right sidebar -->
								<div class="right-sidebar right_sidebar_profile right-side-contact hide-right-sidebar"
									id="middle1">
									<div class="right-sidebar-wrap active">
										<div class="slimscroll">
											<div
												class="left-chat-title d-flex justify-content-between align-items-center border-bottom-0">
												<div class="fav-title mb-0">
													<h6>Lead Info</h6>
												</div>
												<div class="contact-close_call text-end">
													<a href="#" class="close_profile close-star">
														<i class="bx bxs-star"></i>
													</a>
													<a href="#" class="close_profile close-trash">
														<i class="bx bx-trash"></i>
													</a>
												</div>
											</div>
											<div class="sidebar-body">


												<div class="accordion lea-accordion" id="accordionExample">
													<div class="accordion-item">
														<h2 class="accordion-header">
															<button class="accordion-button" type="button"
																data-bs-toggle="collapse" data-bs-target="#collapseOne"
																aria-expanded="true" aria-controls="collapseOne">
																Lead Detail
															</button>
														</h2>
														<div id="collapseOne" class="accordion-collapse collapse show"
															data-bs-parent="#accordionExample">
															<div class="accordion-body">
																<div class="card">
																	<div class="card-body pb-2">
																		<div
																			class="d-flex align-items-center justify-content-between flex-wrap">
																			<div class="d-flex align-items-center mb-2">
																				<div
																					class="avatar avatar-xxl me-3 flex-shrink-0 border p-2">
																					<h6 class="text-default fw-medium">HT
																					</h6>
																				</div>
																				<div>
																					<h5 class="mb-1">Tremblay and Rath <span
																							class="star-icon"><i
																								class="fa-solid fa-star"></i></span>
																					</h5>
																					<p class="mb-1"><i
																							class="ti ti-building me-1"></i>Google
																						Inc</p>
																					<p class="mb-0"><i
																							class="ti ti-map-pin-pin me-1"></i>22,
																						Ave Street,
																						Newyork, USA</p>
																				</div>
																			</div>
																			<div class="contacts-action">
																				<span class="badge badge-light"><i
																						class="ti ti-lock"></i>Private</span>
																				<div class="dropdown mb-2">
																					<a href="#"
																						class="bg-success text-white py-1 px-2"
																						data-bs-toggle="dropdown"
																						aria-expanded="false">
																						Closed<i
																							class="ti ti-chevron-down ms-2"></i>
																					</a>
																					<div
																						class="dropdown-menu dropdown-menu-right">
																						<a class="dropdown-item"
																							href="javascript:void(0);"><span>Closed</span></a>
																						<a class="dropdown-item"
																							href="javascript:void(0);"><span>Opened</span></a>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>


																<div class="card">
																	<div class="card-body p-3">
																		<h6 class="mb-3 fw-semibold">Lead Information</h6>
																		<ul>
																			<li class="row mb-3"><span class="col-6">Date
																					Created</span><span class="col-6">10
																					Jan 2024, 10:00 am</span></li>
																			<li class="row mb-3"><span
																					class="col-6">Value</span><span
																					class="col-6">$25,11,145</span></li>
																			<li class="row mb-3"><span class="col-6">Due
																					Date</span><span class="col-6">20 Jan
																					2024, 10:00 am</span></li>
																			<li class="row mb-3"><span class="col-6">Follow
																					Up</span><span class="col-6">20 Jan
																					2024</span></li>
																			<li class="row mb-3"><span
																					class="col-6">Source</span><span
																					class="col-6">Google</span></li>
																		</ul>
																		<h6 class="mb-3 fw-semibold">Owner</h6>
																		<div class="d-flex align-items-center mb-3">
																			<div class="avatar avatar-md me-2">
																				<img src="assets/img/profiles/avatar-21.jpg"
																					alt="Image">
																			</div>
																			<p>Vaughan Lewis</p>
																		</div>
																		<hr>
																		<h6 class="mb-3 fw-semibold">Tags</h6>
																		<a href="javascript:void(0);"
																			class="badge badge-soft-success fw-medium me-2">Collab</a>
																		<a href="javascript:void(0);"
																			class="badge badge-soft-warning fw-medium">Rated</a>
																		<hr>
																		<h6 class="mb-3 fw-semibold">Projects</h6>
																		<a href="javascript:void(0);"
																			class="badge bg-light-300 text-default me-2 mb-2">Devops
																			Design</a>
																		<a href="javascript:void(0);"
																			class="badge bg-light-300 text-default mb-2">Margrate
																			Design</a>
																		<hr>
																		<h6 class="mb-3 fw-semibold">Priority</h6>
																		<div class="priority-info">
																			<div class="dropdown">
																				<a href="#" class="dropdown-toggle"
																					data-bs-toggle="dropdown"
																					aria-expanded="false"><span><i
																							class="ti ti-square-rounded-filled me-1 text-danger circle"></i>High</span><i
																						class="ti ti-chevron-down me-1"></i></a>
																				<div
																					class="dropdown-menu dropdown-menu-right">
																					<a class="dropdown-item"
																						href="javascript:void(0);">
																						<span><i
																								class="ti ti-square-rounded-filled me-1 text-danger circle"></i>High</span>
																					</a>
																					<a class="dropdown-item"
																						href="javascript:void(0);">
																						<span><i
																								class="ti ti-square-rounded-filled me-1 text-success circle"></i>Low</span>
																					</a>
																				</div>
																			</div>
																		</div>
																		<hr>
																		<div
																			class="d-flex align-items-center justify-content-between flex-wrap">
																			<h6 class="mb-3 fw-semibold">Contacts</h6>
																			<a href="javascript:void(0);"
																				class="link-purple mb-3 fw-medium"
																				data-bs-toggle="modal"
																				data-bs-target="#add_contact"><i
																					class="ti ti-circle-plus me-1"></i>Add
																				New</a>
																		</div>
																		<div class="d-flex align-items-center mb-0">
																			<div class="avatar avatar-md me-2">
																				<img src="assets/img/profiles/avatar-01.jpg"
																					alt="Image">
																			</div>
																			<p>Jessica Louise</p>
																		</div>
																		<hr>
																		<ul>
																			<li class="row mb-3"><span class="col-6">Last
																					Modified</span><span class="col-6">10
																					Jan 2024, 10:00 am</span></li>
																			<li class="row mb-0"><span
																					class="col-6">Modified By</span>
																				<span class="col-6">
																					<span class="avatar avatar-xs me-1"><img
																							src="assets/img/profiles/avatar-19.jpg"
																							class="avatar-xs"
																							alt="img"></span>Darlee
																					Robertson
																				</span>
																			</li>
																		</ul>
																	</div>
																</div>






															</div>
														</div>
													</div>
													<div class="accordion-item">
														<h2 class="accordion-header">
															<button class="accordion-button collapsed" type="button"
																data-bs-toggle="collapse" data-bs-target="#collapseTwo"
																aria-expanded="false" aria-controls="collapseTwo">
																Log Activities
															</button>
														</h2>
														<div id="collapseTwo" class="accordion-collapse collapse"
															data-bs-parent="#accordionExample">
															<div class="accordion-body">
																<form action="contacts.html">
																	<div class="row">
																		<div class="col-md-12">
																			<div class="mb-3">
																				<label class="col-form-label">Activity <span
																						class="text-danger">*</span></label>
																				<select required="" class="form-control">
																					<option value="call">Call</option>
																					<option value="meeting">Meeting</option>
																					<option value="general">General</option>
																				</select>
																			</div>
																		</div>
																		<div class="col-md-12">
																			<div class="mb-3">
																				<label class="col-form-label">Activity Type
																					<span
																						class="text-danger">*</span></label>
																				<select required="" class="form-control">
																					<option value="">--Select--</option>
																					<option value="meeting">New</option>
																				</select>
																			</div>
																		</div>
																		<div class="col-md-12">
																			<div class="mb-3">
																				<label class="col-form-label">Activity
																					Status <span
																						class="text-danger">*</span></label>
																				<select required="" class="form-control">
																					<option value="">--Select--</option>
																					<option value="Picked">Picked</option>
																					<option value="Not Picked">Not Picked
																					</option>
																					<option value="Not Reachable">Not
																						Reachable</option>
																				</select>
																			</div>
																		</div>
																		<div class="col-md-12">
																			<div class="mb-3">
																				<label class="col-form-label">Assigned To
																					<span
																						class="text-danger">*</span></label>
																				<select required="" class="form-control">
																					<option value="">Munish Kumar -
																						info@bizzdigital.com </option>
																					<option value="">Rajesh Kumar -
																						rajesh@bizzdigital.com </option>
																					<option value="">Vikas Rana -
																						vikasrana@bizzdigital.com </option>
																				</select>
																			</div>
																		</div>
																		<div class="col-md-12">
																			<div class="mb-3">
																				<label class="col-form-label">Lead Status
																					<span
																						class="text-danger">*</span></label>
																				<select required="" class="form-control">
																					<option value="">Call Not Picked
																					</option>
																					<option value="">Cold</option>
																					<option value="">Dead</option>
																					<option value="">Designing</option>
																					<option value="">Follow up</option>
																					<option value="">Hot</option>
																					<option value="">Interested</option>
																					<option value="">Matured</option>
																					<option value="">New</option>
																					<option value="">Not Interested</option>
																					<option value="">Not Relevant</option>
																					<option value="">Status Unknown</option>
																					<option value="">Warm</option>

																				</select>
																			</div>
																		</div>
																		<div class="col-md-12">
																			<div class="mb-3">
																				<label class="col-form-label">Remarks <span
																						class="text-danger">*</span></label>
																				<textarea class="form-control"
																					rows="5"></textarea>
																			</div>
																		</div>

																		<div class="col-md-12">
																			<div class="mb-3">
																				<div class="d-flex flex-wrap">
																					<div class="me-2">
																						<input type="checkbox" class=""
																							id="followup">
																						<label for="followup">Create a
																							follow-up task </label>
																					</div>
																				</div>
																			</div>
																		</div>
																		<div class="col-md-12">
																			<div
																				class="d-flex align-items-center justify-content-end">
																				<button
																					ng-disabled="mvm.formActivity.$invalid"
																					type="submit" class="btn btn-primary"
																					aria-disabled="false">Submit</button>
																			</div>
																		</div>
																</form>
															</div>
														</div>
													</div>
												</div>
												<div class="accordion-item">
													<h2 class="accordion-header">
														<button class="accordion-button collapsed" type="button"
															data-bs-toggle="collapse" data-bs-target="#collapseThree"
															aria-expanded="false" aria-controls="collapseThree">
															Activities
														</button>
													</h2>
													<div id="collapseThree" class="accordion-collapse collapse"
														data-bs-parent="#accordionExample">
														<div class="accordion-body">


															<div class="card mb-3">
																<div class="card-body pb-0">
																	<h4 class="fw-semibold mb-3">Lead Pipeline Status</h4>
																	<div class="pipeline-list">
																		<ul>
																			<li><a href="javascript:void(0);"
																					class="bg-pending">Not Contacted</a>
																			</li>
																			<li><a href="javascript:void(0);"
																					class="bg-info">Not Contacted</a></li>
																			<li><a href="javascript:void(0);"
																					class="bg-success">Closed</a></li>
																			<li><a href="javascript:void(0);"
																					class="bg-danger">Lost</a></li>
																		</ul>
																	</div>
																	<ul class="nav nav-tabs nav-tabs-bottom" role="tablist">
																		<li class="nav-item" role="presentation">
																			<a href="#" data-bs-toggle="tab"
																				data-bs-target="#activities"
																				class="nav-link active"><i
																					class="ti ti-alarm-minus me-1"></i>Activities</a>
																		</li>
																		<li class="nav-item" role="presentation">
																			<a href="#" data-bs-toggle="tab"
																				data-bs-target="#notes" class="nav-link"><i
																					class="ti ti-notes me-1"></i>Notes</a>
																		</li>
																		<li class="nav-item" role="presentation">
																			<a href="#" data-bs-toggle="tab"
																				data-bs-target="#calls" class="nav-link"><i
																					class="ti ti-phone me-1"></i>Calls</a>
																		</li>
																		<li class="nav-item" role="presentation">
																			<a href="#" data-bs-toggle="tab"
																				data-bs-target="#files" class="nav-link"><i
																					class="ti ti-file me-1"></i>Files</a>
																		</li>
																		<li class="nav-item" role="presentation">
																			<a href="#" data-bs-toggle="tab"
																				data-bs-target="#email" class="nav-link"><i
																					class="ti ti-mail-check me-1"></i>Email</a>
																		</li>
																	</ul>
																</div>
															</div>

															<!-- Tab Content -->
															<div class="tab-content pt-0">

																<!-- Activities -->
																<div class="tab-pane active show" id="activities">
																	<div class="card">
																		<div
																			class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
																			<h4 class="fw-semibold">Activities</h4>
																			<div>
																				<div class="form-sort">
																					<i class="ti ti-sort-ascending-2"></i>
																					<select class="select">
																						<option>Sort By Date</option>
																						<option>Ascending</option>
																						<option>Descending</option>
																					</select>
																				</div>
																			</div>
																		</div>
																		<div class="card-body">
																			<div class="contact-activity">
																				<div
																					class="badge badge-soft-purple fs-14 fw-normal shadow-none mb-3">
																					<i
																						class="ti ti-calendar-check me-1"></i>29
																					Aug 2023
																				</div>
																				<div class="card border shadow-none mb-3">
																					<div class="card-body p-3">
																						<div class="d-flex">
																							<span
																								class="avatar avatar-md flex-shrink-0 rounded me-2 bg-pending">
																								<i
																									class="ti ti-mail-code"></i>
																							</span>
																							<div>
																								<h6 class="fw-medium mb-1">
																									You sent 1 Message to
																									the
																									contact.</h6>
																								<p>10:25 pm</p>
																							</div>
																						</div>
																					</div>
																				</div>
																				<div class="card border shadow-none mb-3">
																					<div class="card-body p-3">
																						<div class="d-flex">
																							<span
																								class="avatar avatar-md flex-shrink-0 rounded me-2 bg-secondary-success">
																								<i class="ti ti-phone"></i>
																							</span>
																							<div>
																								<h6 class="fw-medium mb-1">
																									Denwar responded to your
																									appointment schedule
																									question by call at
																									09:30pm.</h6>
																								<p>09:25 pm</p>
																							</div>
																						</div>
																					</div>
																				</div>
																				<div class="card border shadow-none mb-3">
																					<div class="card-body p-3">
																						<div class="d-flex">
																							<span
																								class="avatar avatar-md flex-shrink-0 rounded me-2 bg-orange">
																								<i class="ti ti-notes"></i>
																							</span>
																							<div>
																								<h6 class="fw-medium mb-1">
																									Notes added by Antony
																								</h6>
																								<p class="mb-1">Please
																									accept my apologies for
																									the
																									inconvenience caused. It
																									would be much
																									appreciated if
																									it's possible to
																									reschedule to 6:00 PM,
																									or any other day
																									that week.</p>
																								<p>10.00 pm</p>
																							</div>
																						</div>
																					</div>
																				</div>
																				<div
																					class="badge badge-soft-purple fs-14 fw-normal shadow-none mb-3">
																					<i
																						class="ti ti-calendar-check me-1"></i>28
																					Feb 2024
																				</div>
																				<div class="card border shadow-none mb-3">
																					<div class="card-body p-3">
																						<div class="d-flex">
																							<span
																								class="avatar avatar-md flex-shrink-0 rounded me-2 bg-info">
																								<i
																									class="ti ti-user-pin"></i>
																							</span>
																							<div>
																								<h6
																									class="fw-medium mb-1 d-inline-flex align-items-center flex-wrap">
																									Meeting With <span
																										class="avatar avatar-xs mx-2"><img
																											src="assets/img/profiles/avatar-19.jpg"
																											alt="img"></span>
																									Abraham</h6>
																								<p>Schedueled on 05:00 pm
																								</p>
																							</div>
																						</div>
																					</div>
																				</div>
																				<div class="card border shadow-none mb-3">
																					<div class="card-body p-3">
																						<div class="d-flex">
																							<span
																								class="avatar avatar-md flex-shrink-0 rounded me-2 bg-secondary-success">
																								<i class="ti ti-notes"></i>
																							</span>
																							<div>
																								<h6 class="fw-medium mb-1">
																									Drain responded to your
																									appointment schedule
																									question.</h6>
																								<p>09:25 pm</p>
																							</div>
																						</div>
																					</div>
																				</div>
																				<div
																					class="badge badge-soft-purple fs-14 fw-normal shadow-none mb-3">
																					<i
																						class="ti ti-calendar-check me-1"></i>Upcoming
																					Activity
																				</div>
																				<div class="card border shadow-none mb-0">
																					<div class="card-body p-3">
																						<div class="d-flex">
																							<span
																								class="avatar avatar-md flex-shrink-0 rounded me-2 bg-info">
																								<i
																									class="ti ti-user-pin"></i>
																							</span>
																							<div>
																								<h6 class="fw-medium mb-1">
																									Product Meeting</h6>
																								<p class="mb-1">A product
																									team meeting is a
																									gathering of the
																									cross-functional product
																									team — ideally including
																									team
																									members from product,
																									engineering, marketing,
																									and
																									customer support.</p>
																								<p>25 Jul 2023, 05:00 pm</p>
																								<div class="upcoming-info">
																									<div class="row">
																										<div
																											class="col-sm-4">
																											<p>Reminder</p>
																											<div
																												class="dropdown">
																												<a href="#"
																													class="dropdown-toggle"
																													data-bs-toggle="dropdown"
																													aria-expanded="false"><i
																														class="ti ti-clock-edit me-1"></i>Reminder<i
																														class="ti ti-chevron-down ms-1"></i></a>
																												<div
																													class="dropdown-menu dropdown-menu-right">
																													<a class="dropdown-item"
																														href="javascript:void(0);">Remainder</a>
																													<a class="dropdown-item"
																														href="javascript:void(0);">1
																														hr</a>
																													<a class="dropdown-item"
																														href="javascript:void(0);">10
																														hr</a>
																												</div>
																											</div>
																										</div>
																										<div
																											class="col-sm-4">
																											<p>Task Priority
																											</p>
																											<div
																												class="dropdown">
																												<a href="#"
																													class="dropdown-toggle"
																													data-bs-toggle="dropdown"
																													aria-expanded="false"><i
																														class="ti ti-square-rounded-filled me-1 text-danger circle"></i>High<i
																														class="ti ti-chevron-down ms-1"></i></a>
																												<div
																													class="dropdown-menu dropdown-menu-right">
																													<a class="dropdown-item"
																														href="javascript:void(0);">
																														<i
																															class="ti ti-square-rounded-filled me-1 text-danger circle"></i>High
																													</a>
																													<a class="dropdown-item"
																														href="javascript:void(0);">
																														<i
																															class="ti ti-square-rounded-filled me-1 text-success circle"></i>Low
																													</a>
																												</div>
																											</div>
																										</div>
																										<div
																											class="col-sm-4">
																											<p>Assigned to
																											</p>
																											<div
																												class="dropdown">
																												<a href="#"
																													class="dropdown-toggle"
																													data-bs-toggle="dropdown"
																													aria-expanded="false"><img
																														src="assets/img/profiles/avatar-19.jpg"
																														alt="img"
																														class="avatar-xs">John<i
																														class="ti ti-chevron-down ms-1"></i></a>
																												<div
																													class="dropdown-menu dropdown-menu-right">
																													<a class="dropdown-item"
																														href="javascript:void(0);">
																														<img src="assets/img/profiles/avatar-19.jpg"
																															alt="img"
																															class="avatar-xs">John
																													</a>
																													<a class="dropdown-item"
																														href="javascript:void(0);">
																														<img src="assets/img/profiles/avatar-15.jpg"
																															alt="img"
																															class="avatar-xs">Peter
																													</a>
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
																<!-- /Activities -->

																<!-- Notes -->
																<div class="tab-pane fade" id="notes">
																	<div class="card">
																		<div
																			class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
																			<h4 class="fw-semibold">Notes</h4>
																			<div class="d-inline-flex align-items-center">
																				<div class="form-sort me-3">
																					<i class="ti ti-sort-ascending-2"></i>
																					<select class="select">
																						<option>Sort By Date</option>
																						<option>Ascending</option>
																						<option>Descending</option>
																					</select>
																				</div>
																				<a href="javascript:void(0);"
																					data-bs-toggle="modal"
																					data-bs-target="#add_notes"
																					class="link-purple fw-medium"><i
																						class="ti ti-circle-plus me-1"></i>Add
																					New</a>
																			</div>
																		</div>
																		<div class="card-body">
																			<div class="notes-activity">
																				<div class="card mb-3">
																					<div class="card-body">
																						<div
																							class="d-flex align-items-center justify-content-between pb-2">
																							<div
																								class="d-inline-flex align-items-center mb-2">
																								<span
																									class="avatar avatar-md me-2 flex-shrink-0">
																									<img src="assets/img/profiles/avatar-19.jpg"
																										alt="img">
																								</span>
																								<div>
																									<h6
																										class="fw-medium mb-1">
																										Darlee Robertson
																									</h6>
																									<p class="mb-0">15 Sep
																										2023, 12:10 pm</p>
																								</div>
																							</div>
																							<div class="mb-2">
																								<div class="dropdown">
																									<a href="#"
																										class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center"
																										data-bs-toggle="dropdown"
																										aria-expanded="false"><i
																											class="ti ti-dots-vertical"></i></a>
																									<div
																										class="dropdown-menu dropdown-menu-right">
																										<a class="dropdown-item"
																											href="javascript:void(0);"><i
																												class="ti ti-edit text-blue me-1"></i>Edit</a>
																										<a class="dropdown-item"
																											href="javascript:void(0);"><i
																												class="ti ti-trash text-danger me-1"></i>Delete</a>
																									</div>
																								</div>
																							</div>
																						</div>
																						<h5 class="fw-medium mb-1">Notes
																							added by Antony</h5>
																						<p>A project review evaluates the
																							success of an initiative and
																							identifies areas for
																							improvement. It can also
																							evaluate a current
																							project to determine whether
																							it's on the right track. Or, it
																							can
																							determine the success of a
																							completed project. </p>
																						<div
																							class="d-inline-flex align-items-center flex-wrap">
																							<div class="note-download me-3">
																								<div class="note-info">
																									<span
																										class="note-icon bg-secondary-success">
																										<i
																											class="ti ti-file-spreadsheet"></i>
																									</span>
																									<div>
																										<h6
																											class="fw-medium mb-1">
																											Project
																											Specs.xls</h6>
																										<p>365 KB</p>
																									</div>
																								</div>
																								<a
																									href="javascript:void(0);"><i
																										class="ti ti-arrow-down"></i></a>
																							</div>
																							<div class="note-download">
																								<div class="note-info">
																									<span class="note-icon">
																										<img src="assets/img/media/media-35.jpg"
																											alt="img">
																									</span>
																									<div>
																										<h6
																											class="fw-medium mb-1">
																											090224.jpg</h6>
																										<p>365 KB</p>
																									</div>
																								</div>
																								<a
																									href="javascript:void(0);"><i
																										class="ti ti-arrow-down"></i></a>
																							</div>
																						</div>
																						<div class="notes-editor">
																							<div class="note-edit-wrap">
																								<div class="summernote">
																									Write a new comment,
																									send your team
																									notification by typing @
																									followed by their name
																								</div>
																								<div
																									class="text-end note-btns">
																									<a href="javascript:void(0);"
																										class="btn btn-light add-cancel">Cancel</a>
																									<a href="javascript:void(0);"
																										class="btn btn-primary">Save</a>
																								</div>
																							</div>
																							<div class="text-end">
																								<a href="javascript:void(0);"
																									class="add-comment link-purple fw-medium"><i
																										class="ti ti-square-plus me-1"></i>Add
																									Comment</a>
																							</div>
																						</div>
																					</div>
																				</div>
																				<div class="card mb-3">
																					<div class="card-body">
																						<div
																							class="d-flex align-items-center justify-content-between pb-2">
																							<div
																								class="d-inline-flex align-items-center mb-2">
																								<span
																									class="avatar avatar-md me-2 flex-shrink-0">
																									<img src="assets/img/profiles/avatar-20.jpg"
																										alt="img">
																								</span>
																								<div>
																									<h6
																										class="fw-medium mb-1">
																										Sharon Roy</h6>
																									<p class="mb-0">18 Sep
																										2023, 09:52 am</p>
																								</div>
																							</div>
																							<div class="mb-2">
																								<div class="dropdown">
																									<a href="#"
																										class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center"
																										data-bs-toggle="dropdown"
																										aria-expanded="false"><i
																											class="ti ti-dots-vertical"></i></a>
																									<div
																										class="dropdown-menu dropdown-menu-right">
																										<a class="dropdown-item"
																											href="javascript:void(0);"><i
																												class="ti ti-edit text-blue me-1"></i>Edit</a>
																										<a class="dropdown-item"
																											href="javascript:void(0);"><i
																												class="ti ti-trash text-danger me-1"></i>Delete</a>
																									</div>
																								</div>
																							</div>
																						</div>
																						<h5 class="fw-medium mb-1">Notes
																							added by Antony</h5>
																						<p>A project plan typically contains
																							a list of the essential
																							elements of a project, such as
																							stakeholders, scope, timelines,
																							estimated cost and communication
																							methods. The project manager
																							typically lists the information
																							based on the assignment.</p>
																						<div
																							class="d-inline-flex align-items-center flex-wrap">
																							<div class="note-download me-3">
																								<div class="note-info">
																									<span
																										class="note-icon bg-secondary-success">
																										<i
																											class="ti ti-file-text"></i>
																									</span>
																									<div>
																										<h6
																											class="fw-medium mb-1">
																											Andrewpass.txt
																										</h6>
																										<p>365 KB</p>
																									</div>
																								</div>
																								<a
																									href="javascript:void(0);"><i
																										class="ti ti-arrow-down"></i></a>
																							</div>
																						</div>
																						<div class="reply-box">
																							<p>The best way to get a project
																								done faster is to start
																								sooner.
																								A goal without a timeline is
																								just a dream.The goal you
																								set
																								must be challenging. At the
																								same time, it should be
																								realistic and attainable,
																								not impossible to reach.</p>
																							<p>Commented by <span
																									class="text-purple">Aeron</span>
																								on 15 Sep
																								2023, 11:15 pm</p>
																							<a href="#" class="btn"><i
																									class="ti ti-arrow-back-up-double"></i>Reply</a>
																						</div>
																						<div class="notes-editor">
																							<div class="note-edit-wrap">
																								<div class="summernote">
																									Write a new comment,
																									send your team
																									notification by typing @
																									followed by their name
																								</div>
																								<div
																									class="text-end note-btns">
																									<a href="javascript:void(0);"
																										class="btn btn-light add-cancel">Cancel</a>
																									<a href="javascript:void(0);"
																										class="btn btn-primary">Save</a>
																								</div>
																							</div>
																							<div class="text-end">
																								<a href="javascript:void(0);"
																									class="add-comment link-purple fw-medium"><i
																										class="ti ti-square-plus me-1"></i>Add
																									Comment</a>
																							</div>
																						</div>
																					</div>
																				</div>
																				<div class="card mb-0">
																					<div class="card-body">
																						<div
																							class="d-flex align-items-center justify-content-between pb-2">
																							<div
																								class="d-inline-flex align-items-center mb-2">
																								<span
																									class="avatar avatar-md me-2 flex-shrink-0">
																									<img src="assets/img/profiles/avatar-21.jpg"
																										alt="img">
																								</span>
																								<div>
																									<h6
																										class="fw-medium mb-1">
																										Vaughan Lewis</h6>
																									<p class="mb-0">20 Sep
																										2023, 10:26 pm</p>
																								</div>
																							</div>
																							<div class="mb-2">
																								<div class="dropdown">
																									<a href="#"
																										class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center"
																										data-bs-toggle="dropdown"
																										aria-expanded="false"><i
																											class="ti ti-dots-vertical"></i></a>
																									<div
																										class="dropdown-menu dropdown-menu-right">
																										<a class="dropdown-item"
																											href="javascript:void(0);"><i
																												class="ti ti-edit text-blue me-1"></i>Edit</a>
																										<a class="dropdown-item"
																											href="javascript:void(0);"><i
																												class="ti ti-trash text-danger me-1"></i>Delete</a>
																									</div>
																								</div>
																							</div>
																						</div>
																						<p>Projects play a crucial role in
																							the success of organizations,
																							and
																							their importance cannot be
																							overstated. Whether it's
																							launching a
																							new product, improving an
																							existing</p>
																						<div class="notes-editor">
																							<div class="note-edit-wrap">
																								<div class="summernote">
																									Write a new comment,
																									send your team
																									notification by typing @
																									followed by their name
																								</div>
																								<div
																									class="text-end note-btns">
																									<a href="javascript:void(0);"
																										class="btn btn-light add-cancel">Cancel</a>
																									<a href="javascript:void(0);"
																										class="btn btn-primary">Save</a>
																								</div>
																							</div>
																							<div class="text-end">
																								<a href="javascript:void(0);"
																									class="add-comment link-purple fw-medium"><i
																										class="ti ti-square-plus me-1"></i>Add
																									Comment</a>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
																<!-- /Notes -->

																<!-- Calls -->
																<div class="tab-pane fade" id="calls">
																	<div class="card">
																		<div
																			class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
																			<h4 class="fw-semibold">Calls</h4>
																			<div class="d-inline-flex align-items-center">
																				<a href="javascript:void(0);"
																					data-bs-toggle="modal"
																					data-bs-target="#create_call"
																					class="link-purple fw-medium"><i
																						class="ti ti-circle-plus me-1"></i>Add
																					New</a>
																			</div>
																		</div>
																		<div class="card-body">
																			<div class="card mb-3">
																				<div class="card-body">
																					<div
																						class="d-sm-flex align-items-center justify-content-between pb-2">
																						<div
																							class="d-flex align-items-center mb-2">
																							<span
																								class="avatar avatar-md me-2 flex-shrink-0">
																								<img src="assets/img/profiles/avatar-19.jpg"
																									alt="img">
																							</span>
																							<p><span
																									class="text-dark fw-medium">Darlee
																									Robertson</span>
																								logged a call on 23 Jul
																								2023, 10:00 pm</p>
																						</div>
																						<div
																							class="d-inline-flex align-items-center mb-2">
																							<div class="dropdown me-2">
																								<a href="#"
																									class="bg-danger py-1"
																									data-bs-toggle="dropdown"
																									aria-expanded="false">Busy<i
																										class="ti ti-chevron-down ms-2"></i></a>
																								<div
																									class="dropdown-menu dropdown-menu-right">
																									<a class="dropdown-item"
																										href="javascript:void(0);">Busy</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);">No
																										Answer</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);">Unavailable</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);">Wrong
																										Number</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);">Left
																										Voice Message</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);">Moving
																										Forward</a>
																								</div>
																							</div>
																							<div class="dropdown">
																								<a href="#"
																									class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center"
																									data-bs-toggle="dropdown"
																									aria-expanded="false"><i
																										class="ti ti-dots-vertical"></i></a>
																								<div
																									class="dropdown-menu dropdown-menu-right">
																									<a class="dropdown-item"
																										href="javascript:void(0);"><i
																											class="ti ti-edit text-blue me-1"></i>Edit</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);"><i
																											class="ti ti-trash text-danger me-1"></i>Delete</a>
																								</div>
																							</div>
																						</div>
																					</div>
																					<p>A project review evaluates the
																						success of an initiative and
																						identifies areas for improvement. It
																						can also evaluate a current
																						project to determine whether it's on
																						the right track. Or, it can
																						determine the success of a completed
																						project. </p>
																				</div>
																			</div>
																			<div class="card mb-3">
																				<div class="card-body">
																					<div
																						class="d-sm-flex align-items-center justify-content-between pb-2">
																						<div
																							class="d-flex align-items-center mb-2">
																							<span
																								class="avatar avatar-md me-2 flex-shrink-0">
																								<img src="assets/img/profiles/avatar-20.jpg"
																									alt="img">
																							</span>
																							<p><span
																									class="text-dark fw-medium">Sharon
																									Roy</span> logged a
																								call on 28 Jul 2023, 09:00
																								pm</p>
																						</div>
																						<div
																							class="d-inline-flex align-items-center mb-2">
																							<div class="dropdown me-2">
																								<a href="#"
																									class="bg-pending py-1"
																									data-bs-toggle="dropdown"
																									aria-expanded="false">No
																									Answer<i
																										class="ti ti-chevron-down ms-2"></i></a>
																								<div
																									class="dropdown-menu dropdown-menu-right">
																									<a class="dropdown-item"
																										href="javascript:void(0);">Busy</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);">No
																										Answer</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);">Unavailable</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);">Wrong
																										Number</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);">Left
																										Voice Message</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);">Moving
																										Forward</a>
																								</div>
																							</div>
																							<div class="dropdown">
																								<a href="#"
																									class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center"
																									data-bs-toggle="dropdown"
																									aria-expanded="false"><i
																										class="ti ti-dots-vertical"></i></a>
																								<div
																									class="dropdown-menu dropdown-menu-right">
																									<a class="dropdown-item"
																										href="javascript:void(0);">
																										<i
																											class="ti ti-edit text-blue me-1"></i>Edit
																									</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);">
																										<i
																											class="ti ti-trash text-danger me-1"></i>Delete
																									</a>
																								</div>
																							</div>
																						</div>
																					</div>
																					<p>A project plan typically contains a
																						list of the essential elements of
																						a project, such as stakeholders,
																						scope, timelines, estimated cost
																						and communication methods. The
																						project manager typically lists the
																						information based on the assignment.
																					</p>
																				</div>
																			</div>
																			<div class="card mb-0">
																				<div class="card-body">
																					<div
																						class="d-sm-flex align-items-center justify-content-between pb-2">
																						<div
																							class="d-flex align-items-center mb-2">
																							<span
																								class="avatar avatar-md me-2 flex-shrink-0">
																								<img src="assets/img/profiles/avatar-21.jpg"
																									alt="img">
																							</span>
																							<p><span
																									class="text-dark fw-medium">Vaughan
																									Lewis</span> logged a
																								call on 30 Jul 2023, 08:00
																								pm</p>
																						</div>
																						<div
																							class="d-inline-flex align-items-center mb-2">
																							<div class="dropdown me-2">
																								<a href="#"
																									class="bg-pending py-1"
																									data-bs-toggle="dropdown"
																									aria-expanded="false">No
																									Answer<i
																										class="ti ti-chevron-down ms-2"></i></a>
																								<div
																									class="dropdown-menu dropdown-menu-right">
																									<a class="dropdown-item"
																										href="javascript:void(0);">Busy</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);">No
																										Answer</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);">Unavailable</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);">Wrong
																										Number</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);">Left
																										Voice Message</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);">Moving
																										Forward</a>
																								</div>
																							</div>
																							<div class="dropdown">
																								<a href="#"
																									class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center"
																									data-bs-toggle="dropdown"
																									aria-expanded="false"><i
																										class="ti ti-dots-vertical"></i></a>
																								<div
																									class="dropdown-menu dropdown-menu-right">
																									<a class="dropdown-item"
																										href="javascript:void(0);">
																										<i
																											class="ti ti-edit text-blue me-1"></i>Edit
																									</a>
																									<a class="dropdown-item"
																										href="javascript:void(0);">
																										<i
																											class="ti ti-trash text-danger me-1"></i>Delete
																									</a>
																								</div>
																							</div>
																						</div>
																					</div>
																					<p>Projects play a crucial role in the
																						success of organizations, and
																						their importance cannot be
																						overstated. Whether it's launching a
																						new
																						product, improving an existing</p>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
																<!-- /Calls -->

																<!-- Files -->
																<div class="tab-pane fade" id="files">
																	<div class="card">
																		<div class="card-header">
																			<h4 class="fw-semibold">Files</h4>
																		</div>
																		<div class="card-body">
																			<div class="card border mb-3">
																				<div class="card-body pb-0">
																					<div class="row align-items-center">
																						<div class="col-md-8">
																							<div class="mb-3">
																								<h4 class="fw-medium mb-1">
																									Manage Documents</h4>
																								<p>Send customizable quotes,
																									proposals and contracts
																									to
																									close deals faster.</p>
																							</div>
																						</div>
																						<div class="col-md-4 text-md-end">
																							<div class="mb-3">
																								<a href="#"
																									class="btn btn-primary"
																									data-bs-toggle="modal"
																									data-bs-target="#new_file">Create
																									Document</a>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																			<div class="card border shadow-none mb-3">
																				<div class="card-body pb-0">
																					<div class="row align-items-center">
																						<div class="col-md-8">
																							<div class="mb-3">
																								<h4 class="fw-medium mb-1">
																									Collier-Turner Proposal
																								</h4>
																								<p>Send customizable quotes,
																									proposals and contracts
																									to
																									close deals faster.</p>
																								<div
																									class="d-flex align-items-center">
																									<span
																										class="avatar avatar-md me-2 flex-shrink-0">
																										<img src="assets/img/profiles/avatar-21.jpg"
																											alt="img"
																											class="rounded-circle">
																									</span>
																									<div>
																										<span
																											class="fs-12">Owner</span>
																										<p>Vaughan Lewis</p>
																									</div>
																								</div>
																							</div>
																						</div>
																						<div class="col-md-4 text-md-end">
																							<div
																								class="mb-3 d-inline-flex align-items-center">
																								<span
																									class="badge badge-danger-light me-1">Proposal</span>
																								<span
																									class="badge bg-pending priority-badge me-1">Draft</span>
																								<div class="dropdown">
																									<a href="#"
																										class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center"
																										data-bs-toggle="dropdown"
																										aria-expanded="false"><i
																											class="ti ti-dots-vertical"></i></a>
																									<div
																										class="dropdown-menu dropdown-menu-right">
																										<a class="dropdown-item"
																											href="javascript:void(0);">
																											<i
																												class="ti ti-edit text-blue me-1"></i>Edit
																										</a>
																										<a class="dropdown-item"
																											href="javascript:void(0);">
																											<i
																												class="ti ti-trash text-danger me-1"></i>Delete
																										</a>
																										<a class="dropdown-item"
																											href="javascript:void(0);">
																											<i
																												class="ti ti-download text-info me-1"></i>Download
																										</a>
																									</div>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																			<div class="card border shadow-none mb-3">
																				<div class="card-body pb-0">
																					<div class="row align-items-center">
																						<div class="col-md-8">
																							<div class="mb-3">
																								<h4 class="fw-medium mb-1">
																									Collier-Turner Proposal
																								</h4>
																								<p>Send customizable quotes,
																									proposals and contracts
																									to
																									close deals faster.</p>
																								<div
																									class="d-flex align-items-center">
																									<span
																										class="avatar avatar-md me-2 flex-shrink-0">
																										<img src="assets/img/profiles/avatar-01.jpg"
																											alt="img"
																											class="rounded-circle">
																									</span>
																									<div>
																										<span
																											class="fs-12">Owner</span>
																										<p>Jessica Louise
																										</p>
																									</div>
																								</div>
																							</div>
																						</div>
																						<div class="col-md-4 text-md-end">
																							<div
																								class="mb-3 d-inline-flex align-items-center">
																								<span
																									class="badge badge-purple-light me-1">Quote</span>
																								<span
																									class="badge bg-success me-1">Sent</span>
																								<div class="dropdown">
																									<a href="#"
																										class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center"
																										data-bs-toggle="dropdown"
																										aria-expanded="false"><i
																											class="ti ti-dots-vertical"></i></a>
																									<div
																										class="dropdown-menu dropdown-menu-right">
																										<a class="dropdown-item"
																											href="javascript:void(0);">
																											<i
																												class="ti ti-edit text-blue me-1"></i>Edit
																										</a>
																										<a class="dropdown-item"
																											href="javascript:void(0);">
																											<i
																												class="ti ti-trash text-danger me-1"></i>Delete
																										</a>
																										<a class="dropdown-item"
																											href="javascript:void(0);">
																											<i
																												class="ti ti-download text-info me-1"></i>Download
																										</a>
																									</div>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																			<div class="card border shadow-none mb-0">
																				<div class="card-body pb-0">
																					<div class="row align-items-center">
																						<div class="col-md-8">
																							<div class="mb-3">
																								<h4 class="fw-medium mb-1">
																									Collier-Turner Proposal
																								</h4>
																								<p>Send customizable quotes,
																									proposals and contracts
																									to
																									close deals faster.</p>
																								<div
																									class="d-flex align-items-center">
																									<span
																										class="avatar avatar-md me-2 flex-shrink-0">
																										<img src="assets/img/profiles/avatar-22.jpg"
																											alt="img"
																											class="rounded-circle">
																									</span>
																									<div>
																										<span
																											class="fs-12">Owner</span>
																										<p>Dawn Merhca</p>
																									</div>
																								</div>
																							</div>
																						</div>
																						<div class="col-md-4 text-md-end">
																							<div
																								class="mb-3 d-inline-flex align-items-center">
																								<span
																									class="badge badge-danger-light me-1">Proposal</span>
																								<span
																									class="badge bg-pending priority-badge me-1">Draft</span>
																								<div class="dropdown">
																									<a href="#"
																										class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center"
																										data-bs-toggle="dropdown"
																										aria-expanded="false"><i
																											class="ti ti-dots-vertical"></i></a>
																									<div
																										class="dropdown-menu dropdown-menu-right">
																										<a class="dropdown-item"
																											href="javascript:void(0);">
																											<i
																												class="ti ti-edit text-blue me-1"></i>Edit
																										</a>
																										<a class="dropdown-item"
																											href="javascript:void(0);">
																											<i
																												class="ti ti-trash text-danger me-1"></i>Delete
																										</a>
																										<a class="dropdown-item"
																											href="javascript:void(0);">
																											<i
																												class="ti ti-download text-info me-1"></i>Download
																										</a>
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
																<!-- /Files -->

																<!-- Email -->
																<div class="tab-pane fade" id="email">
																	<div class="card">
																		<div
																			class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
																			<h4 class="fw-semibold">Email</h4>
																			<div class="d-inline-flex align-items-center">
																				<a href="javascript:void(0);"
																					class="link-purple fw-medium"
																					data-bs-toggle="tooltip"
																					data-bs-placement="left"
																					data-bs-custom-class="tooltip-dark"
																					data-bs-original-title="There are no email accounts configured, Please configured your email account in order to Send/ Create EMails"><i
																						class="ti ti-circle-plus me-1"></i>Create
																					Email</a>
																			</div>
																		</div>
																		<div class="card-body">
																			<div class="card border mb-0">
																				<div class="card-body pb-0">
																					<div class="row align-items-center">
																						<div class="col-md-8">
																							<div class="mb-3">
																								<h4 class="fw-medium mb-1">
																									Manage Emails</h4>
																								<p>You can send and reply to
																									emails directly via this
																									section.</p>
																							</div>
																						</div>
																						<div class="col-md-4 text-md-end">
																							<div class="mb-3">
																								<a href="#"
																									class="btn btn-primary"
																									data-bs-toggle="modal"
																									data-bs-target="#create_email">Connect
																									Account</a>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
																<!-- /Email -->

															</div>



														</div>
													</div>
												</div>
											</div>

										</div>

									</div>
								</div>
							</div>
							<!-- Right sidebar -->

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	</div>
	</div>
	</div>

@endsection