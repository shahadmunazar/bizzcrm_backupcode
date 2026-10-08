@extends('layouts.main')
@section('title', 'WhatsApp Chat')
@section('content')
<div class="main-chat-blk">
@push('css')
<style>
    /* Force 100vh App Layout to prevent 24000px page height bugs */
    html, body {
        height: 100vh !important;
        overflow: hidden !important;
    }
    /* Target the outer main-wrapper to act as the master flex column */
    .main-wrapper {
        height: 100vh !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
    }
    
    /* Target the yield container to take up remaining screen space */
    .main-chat-blk {
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        height: 0 !important; /* Prevents overflow */
        overflow: hidden !important;
    }

    /* Target the accidentally nested inner main-wrapper */
    .main-chat-blk > .main-wrapper {
        flex: 1 !important;
        height: auto !important; /* overriding the 100vh from above */
        display: flex !important;
        flex-direction: column !important;
    }

    .footer {
        display: none !important;
    }
    .page-wrapper.chat-page-wrapper {
        flex: 1 !important;
        height: 0 !important; 
        overflow: hidden !important;
    }
    .content {
        height: 100% !important;
        display: flex !important;
    }
    #chat-window-main {
        height: 100% !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
        flex: 1 !important;
    }
    .chat-messages-body {
        flex: 1 !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
    }
    .messages#chat-messages-body {
        flex: 1 !important;
    }
    .chat-message-container {
        position: relative;
    }
    .reaction-bar {
        position: absolute;
        top: -40px;
        background: #f0f2f5;
        border-radius: 20px;
        padding: 5px 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        display: none;
        z-index: 100;
        white-space: nowrap;
    }
    .chat-message-container:hover .reaction-bar {
        display: flex;
        gap: 5px;
    }
    .reaction-bar.show {
        display: flex !important;
        gap: 5px;
    }
    .chats-right .reaction-bar {
        right: 50px;
    }
    .chats:not(.chats-right) .reaction-bar {
        left: 50px;
    }
    .reaction-emoji {
        cursor: pointer;
        font-size: 1.2rem;
        transition: transform 0.2s;
        padding: 2px;
    }
    .reaction-emoji:hover {
        transform: scale(1.3);
    }
    .message-reaction-badge {
        position: absolute;
        bottom: -10px;
        background: #fff;
        border-radius: 10px;
        padding: 2px 5px;
        font-size: 0.8rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15);
        border: 1px solid #e9edef;
        z-index: 10;
        cursor: pointer;
    }
    .chats-right .message-reaction-badge {
        right: 0;
    }
    .chats:not(.chats-right) .message-reaction-badge {
        left: 0;
    }
    
    /* Message Actions Dropdown */
    .message-content {
        padding-right: 32px !important;
    }
    .message-dropdown-trigger {
        position: absolute;
        top: 0px;
        right: 6px;
        width: 22px;
        height: 22px;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.9) !important;
        border-radius: 50% !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15) !important;
        color: #54656f !important;
        border: none !important;
        outline: none !important;
        cursor: pointer;
        padding: 0 !important;
        z-index: 12;
        text-decoration:none;
    }
    .message-dropdown-trigger:hover {
        background: #ffffff !important;
        color: #008069 !important;
    }
    .chat-message-container:hover .message-dropdown-trigger {
        display: flex;
    }
    .message-actions-dropdown .dropdown-menu {
        border-radius: 10px !important;
        border: none !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
        padding: 6px 0 !important;
        font-size: 0.9rem !important;
    }
    .message-actions-dropdown .dropdown-item {
        color: #3b3b3b !important;
        transition: background 0.15s;
    }
    .message-actions-dropdown .dropdown-item:hover {
        background-color: #f5f6f6 !important;
    }
    .message-actions-dropdown .dropdown-item i {
        color: #54656f !important;
    }
    .message-actions-dropdown .dropdown-item.text-danger i {
        color: #dc3545 !important;
    }
    
    /* WhatsApp-style replies custom CSS */
    .chat-footer {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 8px;
        padding: 10px 20px 15px 20px !important;
    }
    .reply-preview-container {
        background: #f0f2f5;
        border-left: 4px solid #027eb5;
        padding: 8px 12px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        width: 100%;
        position: relative;
    }
    .reply-quote-block {
        background: rgba(0, 0, 0, 0.05);
        border-left: 4px solid #027eb5;
        border-radius: 4px;
        padding: 6px 10px;
        margin-bottom: 6px;
        cursor: pointer;
        display: block;
    }
    .reply-quote-title {
        font-weight: 600;
        font-size: 0.8rem;
        color: #027eb5;
        margin-bottom: 2px;
    }
    .reply-quote-body {
        font-size: 0.8rem;
        color: #54656f;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-all;
    }
    @keyframes highlightFlash {
        0% { background-color: rgba(2, 126, 181, 0.25); }
        50% { background-color: rgba(2, 126, 181, 0.25); }
        100% { background-color: transparent; }
    }
    .highlight-message {
        animation: highlightFlash 2s ease-in-out;
    }
</style>
@endpush
<div class="main-wrapper">
<div class="page-wrapper chat-page-wrapper">
    <div class="content">
        <!-- Sidebar -->
        <div class="sidebar-group left-sidebar chat_sidebar">
		<div id="chats" class="left-sidebar-wrap sidebar active slimscroll">
           <div class="slimscroll-active-sidebar">
            <div class="left-chat-title all-chats d-flex justify-content-between align-items-center">
				<div class="setting-title-head">
                <h4 class="fw-bold">All Chats</h4>
				</div>
				<div class="add-section">
									<ul>
										<li>
											<div class="chat-action-btns">
												<div class="chat-action-col">
													<a class="#" href="#" data-bs-toggle="dropdown" aria-expanded="false">
														<i class="bx bx-dots-vertical-rounded"></i>
													</a>
													<div class="dropdown-menu dropdown-menu-end">
														<a href="javascript:void(0);" class="dropdown-item " data-bs-toggle="modal" data-bs-target="#new-chat"><span><i class="bx bx-message-rounded-add"></i></span>New
															Chat </a>
														<a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#new-group"><span><i class="bx bx-user-circle"></i></span>Create
															Group</a>
														<a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#invite-other"><span><i class="bx bx-user-plus"></i></span>Invite Others</a>
                                                        <div class="dropdown-divider"></div>
                                                        <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#upload-campaign-modal"><span><i class="bx bx-upload"></i></span>Upload Campaign</a>
													</div>
												</div>
											</div>
										</li>
									</ul>
								
			    </div>


            </div>
            
			
			 
		   
			
            <div class="sidebar-body chat-body p-3" id="chatsidebar">
				<div class="chat-searchg">
					<input type="text" class="form-control" placeholder="Search..." id="sidebar-search">
				</div>
			<div class="card-body">
                <ul class="nav nav-tabs nav-tabs-bottom mb-3" id="chat-tabs" style="">
                    <li class="nav-item"><a class="nav-link ps-1 pe-1 fs-13 active" href="#bottom-tab1" data-bs-toggle="tab">Active (<span id="count-active">0</span>)</a></li>
                    <li class="nav-item"><a class="nav-link ps-1 pe-1 fs-13 text-success fw-bold" href="#bottom-tab-unread" data-bs-toggle="tab">Unread (<span id="count-unread">0</span>)</a></li>
                    <li class="nav-item"><a class="nav-link ps-1 pe-1 fs-13" href="#bottom-tab2" data-bs-toggle="tab">Inactive (<span id="count-inactive">0</span>)</a></li>
                    <li class="nav-item"><a class="nav-link ps-1 pe-1 fs-13" href="#bottom-tab3" data-bs-toggle="tab">Not Contacted (<span id="count-notcontacted">0</span>)</a></li>
                </ul>

                <div class="tab-content pt-3">
                    <div class="tab-pane show active" id="bottom-tab1">
                        <ul class="user-list list-unstyled m-0" id="chat-leads-list">
                            <li class="p-5 text-center text-muted">
                                <i class="bx bx-loader-alt bx-spin fs-1 mb-3"></i>
                                <p>Loading chats...</p>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-pane" id="bottom-tab-unread">
                        <ul class="user-list list-unstyled m-0" id="unread-leads-list">
                            <li class="p-5 text-center text-muted">
                                <i class="bx bx-loader-alt bx-spin fs-1 mb-3"></i>
                                <p>Loading chats...</p>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-pane" id="bottom-tab2">
                        <ul class="user-list list-unstyled m-0" id="inactive-leads-list">
                            <li class="p-5 text-center text-muted">
                                <i class="bx bx-loader-alt bx-spin fs-1 mb-3"></i>
                                <p>Loading chats...</p>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-pane" id="bottom-tab3">
                        <ul class="user-list list-unstyled m-0" id="not-contacted-leads-list">
                            <li class="p-5 text-center text-muted">
                                <i class="bx bx-loader-alt bx-spin fs-1 mb-3"></i>
                                <p>Loading chats...</p>
                            </li>
                        </ul>
                    </div>
   			    </div>
			</div>

			</div>
			
			</div>
			
			</div>
			
			
			<!-- / Chats sidebar -->
		</div>
		<!-- /Sidebar group -->

        <!-- Chat Window -->
        <div class="chat chat-messages contentdis" id="chat-window-main">
            <!-- No Chat Selected Placeholder -->
            <div id="no-chat-selected" class="flex-grow-1 d-flex flex-column align-items-center justify-content-center text-center p-5 {{ $activeLead ? 'd-none' : '' }}" style="background: #f8f9fa;">
                <img src="{{ asset('assets/img/whatsapp/empty-chat.png') }}" style="max-width: 250px; opacity: 0.6; margin-bottom: 20px;">
                <h3>Your WhatsApp Messages</h3>
                <p class="text-muted">Select a contact from the left list to start chatting.</p>
            </div>
       
            <div id="active-chat-section" class="flex-grow-1 d-flex flex-column {{ $activeLead ? '' : 'd-none' }}" style="height: 100%;">
                <div class="chat-header">
					
				    <div class="user-details">
						<div class="back-chat-list vb .d-sm-block d-md-none d-lg-none d-xl-none">
									<ul class="list-inline mt-2 me-2">
										<li class="list-inline-item">
											<a class="text-muted px-0 left_sides back-chat-list" href="#" data-chat="open">
												<i class="fas fa-arrow-left"></i>
											</a>
										</li>
									</ul>
					    </div>
						<div class="avatar ms-1 me-2">
							<div id="active-chat-avatar" class="avatar-title rounded-circle bg-primary text-white" style="width:40px; height:40px; display:flex; align-items:center; justify-content:center;"></div>
						</div>
						<div class="mt-1">
							<h5 id="active-chat-name" class="mb-0 fw-bold"></h5>
							<small class="text-success pulse">Online</small>
						</div>
					
					</div>
					
					<div class="chat-options ">
								<ul class="list-inline">
									<li class="list-inline-item">
										<a href="javascript:void(0)" class="btn btn-outline-light chat-search-btn" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Search">
											<i class="bx bx-search"></i>
										</a>
									</li>
									<li class="list-inline-item">
										<a href="video-call.html" class="btn btn-outline-light" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Video Call">
											<i class="bx bx-video"></i>
										</a>
									</li>
									<li class="list-inline-item">
										<a href="audio-call.html" class="btn btn-outline-light" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Voice Call">
											<i class="bx bx-phone"></i>
										</a>
									</li>
									<!-- <li class="list-inline-item dream_profile_menu">
										<a href="javascript:void(0)" class="btn btn-outline-light not-chat-user" >
											<i class="bx bx-info-circle"></i>
										</a>
									</li> -->

                                    <li class="list-inline-item">
										<a href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_leadinfo" class="btn btn-outline-light not-chat-user"  data-bs-placement="bottom" title="Lead Infomation">
											<i class="bx bx-info-circle"></i>
										</a>
									</li>
									
									<li class="list-inline-item">
										<a href="javascript:void(0)" id="btn-convert-lead" class="btn btn-outline-light" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Convert to Lead">
											<i class="fa-regular fa-user"></i>
										</a>
									</li>
									
									<li class="list-inline-item">
										<a href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add" class="btn btn-outline-light not-chat-user"  data-bs-placement="bottom" title="Add New Activity">
											<i class="fa-solid fa-circle-plus"></i>
										</a>
									</li>
									
									
									<li class="list-inline-item">
										<a class="btn btn-outline-light no-bg" href="#" data-bs-toggle="dropdown">
											<i class="bx bx-dots-vertical-rounded"></i>
										</a>
										<div class="dropdown-menu dropdown-menu-end">
											<a href="#" class="dropdown-item "><span><i class="bx bx-x"></i></span>Close
												Chat </a>
											<a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#mute-notification"><span><i class="bx bx-volume-mute"></i></span>Mute Notification</a>
											<a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#disappearing-messages"><span><i class="bx bx-time-five"></i></span>Disappearing Message</a>
											<a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#clear-chat"><span><i class="bx bx-brush-alt"></i></span>Clear Message</a>
											<a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#change-chat"><span><i class="bx bx-trash-alt"></i></span>Delete Chat</a>
											<a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#report-user"><span><i class="bx bx-dislike"></i></span>Report</a>
											<a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#block-user"><span><i class="bx bx-block"></i></span>Block</a>
										</div>
									</li>
								</ul>
					</div>
					
					
					
					
					
            </div>

            <!-- Search Messages Bar -->
            <div id="chat-search-bar" class="p-2 border-bottom bg-white d-none shadow-sm" style="z-index: 10;">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bx bx-search text-muted"></i></span>
                    <input type="text" id="chat-message-search-input" class="form-control border-start-0" placeholder="Search messages in this chat..." style="border-radius: 0;">
                    <button class="btn btn-outline-light text-muted border-start-0 bg-white" id="close-chat-search"><i class="bx bx-x"></i></button>
                </div>
            </div>

                <div class="chat-messages-body chat-body" style="flex: 1; display: flex; flex-direction: column; overflow: hidden; padding: 0; position: relative;">
                    <!-- Pinned Message Bar -->
                    <div id="pinned-message-bar" class="d-none" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid #e9edef; padding: 8px 16px; display: flex; align-items: center; justify-content: space-between; z-index: 10; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                        <div id="pinned-bar-content" style="display: flex; align-items: center; flex: 1; min-width: 0;">
                            <i class="bx bxs-pin me-2 text-muted" style="font-size: 1.1rem; transform: rotate(45deg);"></i>
                            <span id="pinned-bar-text" style="font-size: 0.9rem; color: #111b21; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 500;">Pinned Message</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <button type="button" id="unpin-message-btn" class="btn btn-link text-muted p-1" style="font-size: 1.1rem; line-height: 1; border: none; background: transparent;"><i class="bx bx-x"></i></button>
                        </div>
                    </div>

					<div class="messages" id="chat-messages-body" style="flex-grow: 1; overflow-y: auto; overflow-x: hidden; background-color: #e5ddd5; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); padding: 20px; padding-bottom: 100px;">
				
							<div id="chat-messages-inner"></div>
					
					</div>

                    <!-- Floating Scroll to Bottom / New Message Badge -->
                    <div id="scroll-to-bottom-btn" class="d-none" style="position: absolute; bottom: 95px; right: 25px; z-index: 999; cursor: pointer;">
                        <button type="button" class="btn btn-light rounded-circle shadow-lg d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; border: 1px solid rgba(0,0,0,0.1); background-color: #ffffff; color: #54656f;">
                            <i class="bx bx-chevron-down fs-3"></i>
                        </button>
                        <span id="unread-scroll-badge" class="badge bg-success rounded-pill position-absolute d-none" style="top: -5px; right: -5px; font-size: 0.75rem; padding: 4px 7px;">0</span>
                    </div>
				</div>

                <div class="chat-footer">
                    <!-- Reply Preview Block -->
                    <div id="reply-preview-container" class="reply-preview-container d-none">
                        <div style="flex-grow: 1; min-width: 0; padding-right: 15px;">
                            <div id="reply-preview-sender" class="reply-quote-title">Sender Name</div>
                            <div id="reply-preview-text" class="reply-quote-body">Replying message text here...</div>
                        </div>
                        <button type="button" id="cancel-reply-btn" style="border: none; background: transparent; font-size: 1.1rem; color: #888; cursor: pointer; padding: 0 5px;"><i class="bx bx-x"></i></button>
                        <input type="hidden" name="reply_to_message_id" id="reply-to-message-id" value="">
                    </div>
                    
                    <form id="send-message-form">
                        <!-- Plus Action Button -->
                        <div class="action-col">
                            <a class="action-circle-whatsapp" href="#" data-bs-toggle="dropdown">
                                <i class="bx bx-plus"></i>
                            </a>
                            <input type="file" id="media-input" style="display: none;" accept="image/*,video/*,audio/*,application/pdf,.doc,.docx,.xls,.xlsx">
                            <input type="file" id="camera-input" style="display: none;" accept="image/*" capture="environment">
                            
                            <div class="dropdown-menu dropdown-menu-start shadow-sm border-0">
                                <a href="javascript:void(0);" class="dropdown-item media-upload-trigger" data-type="document"><span><i class="bx bx-file me-2"></i></span>Document</a>
                                <a href="javascript:void(0);" class="dropdown-item" id="open-live-camera"><span><i class="bx bx-camera me-2"></i></span>Camera</a>
                                <a href="javascript:void(0);" class="dropdown-item media-upload-trigger" data-type="image"><span><i class="bx bx-image me-2"></i></span>Gallery</a>
                                <a href="javascript:void(0);" class="dropdown-item media-upload-trigger" data-type="audio"><span><i class="bx bx-volume-full me-2"></i></span>Audio</a>
                                <a href="javascript:void(0);" class="dropdown-item" id="send-location-btn"><span><i class="bx bx-map me-2"></i></span>Location</a>
                                <a href="javascript:void(0);" class="dropdown-item" id="send-contact-btn"><span><i class="bx bx-user-pin me-2"></i></span>Contact</a>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="dropdown-item btn-send-template-trigger"><span><i class="bx bx-news me-2"></i></span>Template</a>
                            </div>
                        </div>

                        <!-- Rounded Input Group -->
                        <div class="whatsapp-input-group position-relative">
                            <!-- Quick Reply Autocomplete Container -->
                            <div id="quick-reply-autocomplete" class="dropdown-menu shadow-lg border-0" style="position: absolute; bottom: 100%; left: 0; width: 100%; max-height: 250px; overflow-y: auto; display: none; z-index: 1050; border-radius: 12px; margin-bottom: 5px;">
                                <!-- Items will be injected here -->
                            </div>

                            <input type="text" id="chat-input" class="form-control" placeholder="Type a message... (Use '/' for Quick Replies)" required autocomplete="off">
                            
                            <!-- Emoji Trigger Inside -->
                            <div class="emoji-trigger-whatsapp">
                                <i class="bx bx-smile fs-3"></i>
                            </div>

                            <!-- WhatsApp Media Picker (Emoji, GIF, Sticker) -->
                            <div id="modern-emoji-picker-container" class="whatsapp-media-picker d-none shadow-lg border">
                                <div class="picker-tabs-content">
                                    <!-- Emoji Tab -->
                                    <div id="tab-emoji" class="picker-tab-pane">
                                        <emoji-picker style="width: 100%; height: 100%; --num-columns: 8; --category-emoji-size: 1.5rem;"></emoji-picker>
                                    </div>
                                    <!-- GIF Tab -->
                                    <div id="tab-gif" class="picker-tab-pane d-none">
                                        <div class="gif-search-container">
                                            <input type="text" class="form-control rounded-pill" id="gif-search-input" placeholder="Search Tenor GIFs...">
                                        </div>
                                        <div class="gif-results" id="gif-results-container">
                                            <!-- GIFs will be loaded here -->
                                        </div>
                                    </div>
                                    <!-- Sticker Tab -->
                                    <div id="tab-sticker" class="picker-tab-pane d-none">
                                        <div class="sticker-results">
                                            <div class="sticker-item" data-sticker="https://cdn-icons-png.flaticon.com/512/2274/2274543.png"><img src="https://cdn-icons-png.flaticon.com/512/2274/2274543.png"></div>
                                            <div class="sticker-item" data-sticker="https://cdn-icons-png.flaticon.com/512/2274/2274556.png"><img src="https://cdn-icons-png.flaticon.com/512/2274/2274556.png"></div>
                                            <div class="sticker-item" data-sticker="https://cdn-icons-png.flaticon.com/512/2274/2274548.png"><img src="https://cdn-icons-png.flaticon.com/512/2274/2274548.png"></div>
                                            <div class="sticker-item" data-sticker="https://cdn-icons-png.flaticon.com/512/4148/4148651.png"><img src="https://cdn-icons-png.flaticon.com/512/4148/4148651.png"></div>
                                            <div class="sticker-item" data-sticker="https://cdn-icons-png.flaticon.com/512/4148/4148657.png"><img src="https://cdn-icons-png.flaticon.com/512/4148/4148657.png"></div>
                                            <div class="sticker-item" data-sticker="https://cdn-icons-png.flaticon.com/512/4148/4148674.png"><img src="https://cdn-icons-png.flaticon.com/512/4148/4148674.png"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="picker-tabs-footer">
                                    <a href="javascript:void(0);" class="picker-tab-btn active" data-tab="emoji"><i class="bx bx-smile"></i></a>
                                    <a href="javascript:void(0);" class="picker-tab-btn" data-tab="gif" style="font-weight: bold; font-size: 14px;">GIF</a>
                                    <a href="javascript:void(0);" class="picker-tab-btn" data-tab="sticker"><i class="bx bx-sticker"></i></a>
                                </div>
                            </div>
                        </div>

                        <!-- Mic/Send Button -->
                        <div class="send-col">
                            <button type="submit" class="send-btn-whatsapp">
                                <i class="bx bx-send"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Live Camera Modal -->
                <div class="modal fade" id="liveCameraModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden; background: #000;">
                            <div class="modal-header border-0 text-white" style="position: absolute; top: 0; width: 100%; z-index: 10; background: linear-gradient(to bottom, rgba(0,0,0,0.5), transparent);">
                                <h5 class="modal-title text-white"><i class="bx bx-camera me-2"></i> Live Camera</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" id="close-camera-btn"></button>
                            </div>
                            <div class="modal-body p-0 position-relative" style="min-height: 400px; display: flex; align-items: center; justify-content: center;">
                                <video id="camera-video" autoplay playsinline style="width: 100%; height: auto; max-height: 80vh;"></video>
                                <canvas id="camera-canvas" class="d-none"></canvas>
                                
                                <!-- Camera Controls -->
                                <div class="camera-controls" style="position: absolute; bottom: 30px; width: 100%; text-align: center; z-index: 10;">
                                    <button type="button" id="capture-photo-btn" class="btn btn-light rounded-circle shadow-lg" style="width: 70px; height: 70px; border: 5px solid rgba(0,0,0,0.2);">
                                        <i class="bx bx-camera fs-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Picker Modal -->
                <div class="modal fade" id="contactPickerModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                            <div class="modal-header bg-light border-0 py-3">
                                <h5 class="modal-title fw-bold"><i class="bx bx-user-pin me-2 text-primary"></i> Select Contact to Share</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-0">
                                <div class="p-3 bg-light border-bottom">
                                    <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                        <span class="input-group-text bg-white border-0"><i class="bx bx-search text-muted"></i></span>
                                        <input type="text" id="contact-search-input" class="form-control border-0" placeholder="Search by name or phone number...">
                                    </div>
                                </div>
                                <div class="table-responsive" style="max-height: 450px;">
                                    <table class="table table-hover align-middle mb-0" id="contact-picker-table">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th style="width: 40px;">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="selectAllContacts">
                                                    </div>
                                                </th>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Sec. Phone</th>
                                            </tr>
                                        </thead>
                                        <tbody id="contact-picker-list">
                                            <!-- Loaded via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-0 py-3 d-flex justify-content-between">
                                <span class="text-muted small">
                                    <span id="selected-contacts-count">0</span> contacts selected 
                                    <span class="mx-1">/</span> 
                                    <span id="total-contacts-count">0</span> total contacts
                                </span>
                                <div>
                                    <button type="button" class="btn btn-outline-secondary btn-sm me-2" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary btn-sm px-4" id="share-selected-contacts">Share Selected</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Media Preview Modal -->
                <div class="modal fade" id="mediaPreviewModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                            <div class="modal-header bg-primary text-white border-0">
                                <h5 class="modal-title text-white"><i class="bx bx-show me-2"></i> Preview Media</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4 text-center bg-light">
                                <div id="media-preview-container" class="mb-4 rounded overflow-hidden shadow-sm bg-white d-flex align-items-center justify-content-center" style="min-height: 200px; border: 1px solid #eee;">
                                    <div class="spinner-border text-primary" role="status"></div>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="form-label d-block text-start fw-bold mb-2">Message Caption</label>
                                    <input type="text" id="media-caption-input" class="form-control form-control-lg border-0 shadow-sm" placeholder="Type a message to send with your file..." style="border-radius: 10px;">
                                </div>
                            </div>
                            <div class="modal-footer border-0 bg-light p-3">
                                <button type="button" class="btn btn-label-secondary fw-bold" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                                <button type="button" id="confirm-media-send" class="btn btn-primary px-4 fw-bold" style="border-radius: 10px;">
                                    <i class="bx bx-paper-plane me-1"></i> Send Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pin Message Duration Modal -->
                <div class="modal fade" id="pinDurationModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 28px; background-color: #f0f2f5; padding: 24px;">
                            <div class="modal-body p-0 text-center">
                                <h5 class="fw-bold text-dark mb-2" style="font-size: 1.25rem; text-align: left; padding: 0 8px; font-family: inherit;">Choose how long your new pin lasts</h5>
                                <p class="text-muted mb-4" style="font-size: 0.95rem; text-align: left; padding: 0 8px;">You can unpin at any time.</p>
                                
                                <div class="d-flex flex-column gap-3">
                                    <button type="button" class="btn btn-light w-100 py-3 pin-duration-btn" data-duration="24_hours" style="border-radius: 25px; background-color: #e3e6e8; border: none; color: #111b21; font-weight: 500; font-size: 1.05rem; box-shadow: none;">24 hours</button>
                                    <button type="button" class="btn btn-light w-100 py-3 pin-duration-btn" data-duration="7_days" style="border-radius: 25px; background-color: #e3e6e8; border: none; color: #111b21; font-weight: 500; font-size: 1.05rem; box-shadow: none;">7 days</button>
                                    <button type="button" class="btn btn-light w-100 py-3 pin-duration-btn" data-duration="30_days" style="border-radius: 25px; background-color: #e3e6e8; border: none; color: #111b21; font-weight: 500; font-size: 1.05rem; box-shadow: none;">30 days</button>
                                    <button type="button" class="btn btn-light w-100 py-3" data-bs-dismiss="modal" style="border-radius: 25px; background-color: #e3e6e8; border: none; color: #111b21; font-weight: 500; font-size: 1.05rem; box-shadow: none;">Cancel</button>
                                </div>
                                <input type="hidden" id="pin-target-message-id" value="">
                            </div>
                        </div>
                    </div>
                </div>

		
										
						</div>				
										
									</div>
									<!-- <div class="tab-pane" id="bottom-tab2">
										<img src="../assets/img/whatsapp/empty-chat.png">
									</div>
									<div class="tab-pane" id="bottom-tab3">
										<img src="../assets/img/whatsapp/empty-chat.png">
									</div> -->
		
							
								
						



				<!-- Chat -->
				<?php /* <div class="chat chat-messages" id="chat-window-main">
					
					<!-- Placeholder shown when no chat is selected -->
					<div id="no-chat-selected" class="flex-grow-1 d-flex flex-column align-items-center justify-content-center {{ $activeLead ? 'd-none' : '' }}" style="background: #f8f9fa;">
						<div class="text-center">
							<img src="{{ asset('assets/img/whatsapp/empty-chat.png') }}" class="img-fluid mb-4" style="max-width: 300px; opacity: 0.7;">
							<h3 class="text-dark">Select a Chat to Start</h3>
							<p class="text-muted">Choose a contact from the left sidebar to view messages.</p>
						</div>
					</div>

					<!-- Main Chat Section (Hidden by default, shown via JS) -->
					<div id="active-chat-section" class="flex-grow-1 d-flex flex-column {{ $activeLead ? '' : 'd-none' }}" style="height: 100%; overflow: hidden;">
						<!-- Header -->
						<div class="chat-header border-bottom bg-white p-3">
							<div class="user-details d-flex align-items-center">
								<div class="d-lg-none me-2">
									<a class="text-muted left_sides" href="#"><i class="fas fa-arrow-left"></i></a>
								</div>
								<div class="avatar avatar-online me-2">
									<div id="active-chat-avatar-char" class="avatar-title rounded-circle bg-primary-light text-primary" style="width:45px; height:45px;">
										{{ $activeLead ? strtoupper(substr($activeLead->name ?? $activeLead->phone ?? 'U', 0, 1)) : '' }}
									</div>
								</div>
								<div>
									<h5 id="active-chat-name" class="mb-0">{{ $activeLead->name ?? $activeLead->phone ?? '' }}</h5>
									<small class="text-success">Online</small>
								</div>
							</div>
						</div>

						<!-- Message Body -->
						<div class="chat-body flex-grow-1 p-3" id="chat-messages-body" style="overflow-y: auto; background-color: #e5ddd5; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); padding-bottom: 100px !important;">
							<div class="messages" id="chat-messages-inner">
								@if($activeLead)
									@foreach($activeLead->whatsappMessages as $message)
										<!-- Content populated via AJAX -->
									@endforeach
								@endif
							</div>
						</div>

						<!-- Footer - FIXED AT BOTTOM -->
						<div class="chat-footer border-top bg-white p-3 shadow-lg" style="position: absolute; bottom: 0; width: 100%; z-index: 1000; height: 80px; display: flex; align-items: center;">
							<form id="send-message-form" class="d-flex align-items-center w-100">
								<div class="d-flex me-3">
									<a href="javascript:void(0);" class="btn btn-link text-muted emoji-toggle-btn p-1"><i class="bx bx-smile fs-3"></i></a>
									<div class="emoj-group-list-foot" id="emoji-picker" style="position: absolute; bottom: 85px; left: 20px; background: white; box-shadow: 0 0 15px rgba(0,0,0,0.2); border-radius: 12px; display: none; z-index: 2000; padding: 12px; width: 280px;">
										<ul class="list-unstyled d-flex flex-wrap m-0">
											<li style="cursor:pointer; font-size: 1.6rem; padding: 6px;">😀</li><li style="cursor:pointer; font-size: 1.6rem; padding: 6px;">😁</li><li style="cursor:pointer; font-size: 1.6rem; padding: 6px;">😂</li><li style="cursor:pointer; font-size: 1.6rem; padding: 6px;">🤣</li><li style="cursor:pointer; font-size: 1.6rem; padding: 6px;">😃</li>
											<li style="cursor:pointer; font-size: 1.6rem; padding: 6px;">😄</li><li style="cursor:pointer; font-size: 1.6rem; padding: 6px;">😅</li><li style="cursor:pointer; font-size: 1.6rem; padding: 6px;">😆</li><li style="cursor:pointer; font-size: 1.6rem; padding: 6px;">😉</li><li style="cursor:pointer; font-size: 1.6rem; padding: 6px;">😊</li>
											<li style="cursor:pointer; font-size: 1.6rem; padding: 6px;">😋</li><li style="cursor:pointer; font-size: 1.6rem; padding: 6px;">😎</li><li style="cursor:pointer; font-size: 1.6rem; padding: 6px;">😍</li><li style="cursor:pointer; font-size: 1.6rem; padding: 6px;">😘</li><li style="cursor:pointer; font-size: 1.6rem; padding: 6px;">😗</li>
										</ul>
									</div>
									<a href="javascript:void(0);" class="btn btn-link text-muted p-1"><i class="bx bx-plus fs-3"></i></a>
								</div>
								<input type="text" id="chat-input" class="form-control rounded-pill border-0 bg-light px-4" placeholder="Type a message..." required style="height: 50px; flex-grow: 1; font-size: 1rem;">
								<button class="btn btn-success rounded-circle ms-3 d-flex align-items-center justify-content-center" type="submit" style="width: 50px; height: 50px; flex-shrink: 0;">
									<i class="bx bx-paper-plane text-white fs-4"></i>
								</button>
							</form>
						</div>
					</div>
					</div>
				</div>
				</div>  */ ?>
				<!-- /Chat -->

				<!-- Right sidebar -->
					
			
			
				<!-- Right sidebar -->

			</div>
		</div>



</div>
</div>
@push('scripts')
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script>
        let currentLeadId = null;
        let activePinnedMessage = null;
        let lastMessageCount = 0;
        let unreadScrollCount = 0;
        let previousUnreadCounts = {};
        
        // Infinite scroll state variables
        let chatMessageOffset = 0;
        let hasMoreMessages = true;
        let isFetchingMessages = false;
        let isInitialScrolling = false; // Flag to prevent infinite scroll race condition during initial load
        // Prevent aggressive browser caching of AJAX GET requests
        $.ajaxSetup({ cache: false });
        
        let allLoadedMessages = [];

        // Toggle Media Picker Directly
        $(document).on('click', '.emoji-trigger-whatsapp', function(e) {
            e.preventDefault();
            e.stopPropagation();
            window.reactionMessageId = null; // Reset reaction mode when opening for input
            $('#modern-emoji-picker-container').toggleClass('d-none');
        });

        // Tab Switching Logic
        $(document).on('click', '.picker-tab-btn', function(e) {
            e.preventDefault();
            const tab = $(this).data('tab');
            $('.picker-tab-btn').removeClass('active');
            $(this).addClass('active');
            $('.picker-tab-pane').addClass('d-none');
            $(`#tab-${tab}`).removeClass('d-none');

            if (tab === 'gif' && $('#gif-results-container').is(':empty')) {
                loadTrendingGifs();
            }
        });

        function loadTrendingGifs() {
            const apiKey = 'LIVDSRZRA1TC'; // Public Tenor Key for Demo
            fetch(`https://g.tenor.com/v1/trending?key=${apiKey}&limit=10`)
                .then(res => res.json())
                .then(data => {
                    const html = data.results.map(g => `<img src="${g.media[0].tinygif.url}" class="gif-item" data-url="${g.media[0].gif.url}">`).join('');
                    $('#gif-results-container').html(html);
                });
        }

        $(document).on('input', '#gif-search-input', function() {
            const query = $(this).val();
            if (query.length < 2) return;
            const apiKey = 'LIVDSRZRA1TC';
            fetch(`https://g.tenor.com/v1/search?q=${query}&key=${apiKey}&limit=10`)
                .then(res => res.json())
                .then(data => {
                    const html = data.results.map(g => `<img src="${g.media[0].tinygif.url}" class="gif-item" data-url="${g.media[0].gif.url}">`).join('');
                    $('#gif-results-container').html(html);
                });
        });

        // Media Item Selection
        $(document).on('click', '.gif-item', function() {
            const url = $(this).data('url');
            // For now, insert URL into input. In production, this would trigger an image upload/send.
            const input = $('#chat-input');
            input.val(input.val() + ' ' + url + ' ');
            $('#modern-emoji-picker-container').addClass('d-none');
        });

        $(document).on('click', '.sticker-item', function() {
            const url = $(this).data('sticker');
            const input = $('#chat-input');
            input.val(input.val() + ' ' + url + ' ');
            $('#modern-emoji-picker-container').addClass('d-none');
        });

        // Quick Reply Autocomplete Logic
        let quickReplyDebounceTimer;
        const quickReplyAutocomplete = $('#quick-reply-autocomplete');
        
        $(document).on('keyup', '#chat-input', function(e) {
            const input = $(this);
            const val = input.val();
            
            // Allow hiding with Esc key
            if (e.key === 'Escape') {
                quickReplyAutocomplete.hide();
                return;
            }

            const query = val.trim();
            
            // Only search if there's text (e.g. at least 2 chars) to avoid unnecessary queries
            if (query.length >= 2) {
                clearTimeout(quickReplyDebounceTimer);
                quickReplyDebounceTimer = setTimeout(() => {
                    searchQuickReplies(query);
                }, 300); // 300ms debounce
            } else {
                quickReplyAutocomplete.hide();
            }
        });

        function searchQuickReplies(query) {
            $.ajax({
                url: `/{{ $box->slug }}/whatsapp/quick-replies/search`,
                type: 'GET',
                data: { q: query },
                success: function(response) {
                    if (response.success && response.replies.length > 0) {
                        let html = '';
                        response.replies.forEach(reply => {
                            html += `
                                <a href="javascript:void(0);" class="dropdown-item py-2 border-bottom quick-reply-item" data-message="${reply.message.replace(/"/g, '&quot;')}">
                                    <div class="fw-bold text-dark"><i class="bx bx-bolt-circle text-warning me-2"></i>${reply.title}</div>
                                    <div class="text-muted small text-truncate">${reply.message}</div>
                                </a>
                            `;
                        });
                        quickReplyAutocomplete.html(html).show();
                    } else {
                        quickReplyAutocomplete.hide();
                    }
                }
            });
        }

        $(document).on('click', '.quick-reply-item', function(e) {
            e.preventDefault();
            const messageToInsert = $(this).data('message');
            const input = $('#chat-input');
            
            // Replace the entire input with the selected message
            input.val(messageToInsert + ' ').focus();
            quickReplyAutocomplete.hide();
        });

        // Hide autocomplete when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.whatsapp-input-group').length) {
                quickReplyAutocomplete.hide();
            }
        });

        // Emoji Selection
        $(document).on('emoji-click', 'emoji-picker', function(e) {
            const emoji = e.detail.unicode;
            
            if (window.reactionMessageId) {
                // Reacting to a message
                window.reactToMessage(window.reactionMessageId, emoji);
                window.reactionMessageId = null;
            } else {
                // Inserting into input
                const input = $('#chat-input');
                input.val(input.val() + emoji);
                input.focus();
            }
            
            $('#modern-emoji-picker-container').addClass('d-none');
        });

        // Click Outside to Hide
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.emoji-trigger-whatsapp').length && !$(e.target).closest('#modern-emoji-picker-container').length && !$(e.target).closest('.reaction-bar').length) {
                $('#modern-emoji-picker-container').addClass('d-none');
                window.reactionMessageId = null; // Reset reaction mode
            }
        });

        function loadChat(leadId, leadName) {
            currentLeadId = leadId;
            chatMessageOffset = 0;
            hasMoreMessages = true;
            isFetchingMessages = false;
            isInitialScrolling = true;
            allLoadedMessages = [];
            
            // Show the active chat section and hide the placeholder
            $('#no-chat-selected').addClass('d-none');
            $('#active-chat-section').removeClass('d-none');
            $('.chat-footer').removeClass('d-none'); // Ensure chat footer is visible!
            
            $('#active-chat-name').text(leadName);
            
            let headerAvatar = '';
            if (/^[\+\d]/.test(leadName) || leadName === 'Unnamed') {
                headerAvatar = '<i class="fa-solid fa-user" style="font-size: 22px; line-height: 1;"></i>';
            } else {
                headerAvatar = leadName.charAt(0).toUpperCase();
            }
            $('#active-chat-avatar').html(headerAvatar);
            // Target the specific ID instead of the class to avoid deleting the container
            $('#chat-messages-inner').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
            
            lastMessageCount = 0;

            $.ajax({
                url: `/{{ $box->slug }}/whatsapp/chats/${leadId}/messages?offset=0&limit=20`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        allLoadedMessages = response.messages || [];
                        if (allLoadedMessages.length < 20) {
                            hasMoreMessages = false;
                        }
                        updatePinnedMessageBar(response.pinnedMessage);
                        renderMessages(allLoadedMessages, leadName, true, response.unreadCount || 0, true);
                        lastMessageCount = allLoadedMessages.length;
                        
                        const lead = response.lead;
                        if (lead) {
                            let detailName = lead.name;
                            if (!detailName || detailName.startsWith('New WhatsApp Contact')) {
                                detailName = lead.phone || 'Unnamed';
                            }
                            $('#sidebar-lead-name').html(`${detailName} <span class="star-icon"><i class="fa-solid fa-star"></i></span>`);
                            
                            let detailAvatar = '';
                            if (detailName === lead.phone || detailName === 'Unnamed') {
                                detailAvatar = '<i class="fa-solid fa-user" style="font-size: 22px; line-height: 1;"></i>';
                            } else {
                                detailAvatar = detailName.charAt(0).toUpperCase();
                            }
                            $('#sidebar-lead-avatar').html(detailAvatar);
                            $('#sidebar-lead-company').text(lead.company_name || 'N/A');
                            $('#sidebar-lead-address').text(lead.address || 'No Address');
                            $('#sidebar-lead-phone').text(lead.phone || 'N/A');
                            $('#sidebar-lead-created').text(moment(lead.created_at).format('DD MMM YYYY'));
                            $('#sidebar-lead-value').text('$' + (lead.value || '0'));
                            $('#sidebar-lead-source').text(lead.lead_source ? lead.lead_source.name : 'WhatsApp');
                            
                            const owner = lead.assigned_to_user || lead.assigned_to;
                            $('#sidebar-lead-owner-name').text(owner ? (owner.name || 'Assigned') : 'Unassigned');
                            if (owner && owner.avatar) {
                                $('#sidebar-lead-owner-img').attr('src', '/storage/' + owner.avatar).show();
                                $('#sidebar-lead-owner-img').siblings('.avatar-title').remove();
                            } else {
                                $('#sidebar-lead-owner-img').hide();
                                $('#sidebar-lead-owner-img').parent().find('.avatar-title').remove();
                                const init = owner ? (owner.name || 'U').charAt(0).toUpperCase() : 'U';
                                $('#sidebar-lead-owner-img').parent().append(`<div class="avatar-title rounded-circle bg-primary text-white" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">${init}</div>`);
                            }
                            
                            // Contacts list
                            const contactsContainer = $('#sidebar-lead-contacts-container');
                            if (contactsContainer.length) {
                                if (lead.contacts && lead.contacts.length > 0) {
                                    let cHtml = '';
                                    lead.contacts.forEach(function(c) {
                                        cHtml += `
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar avatar-md me-2">
                                                <div class="avatar-title rounded-circle bg-secondary text-white" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">${(c.name || 'C').charAt(0).toUpperCase()}</div>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-medium">${c.name || 'Unknown'}</p>
                                                ${c.phone ? `<p class="mb-0 text-muted small">${c.phone}</p>` : ''}
                                            </div>
                                        </div>`;
                                    });
                                    contactsContainer.html(cHtml);
                                } else {
                                    contactsContainer.html('<p class="text-muted small">No contacts added.</p>');
                                }
                            }
                            
                            const status = lead.lead_status || lead.status;
                            $('#sidebar-lead-status-btn').html((status ? status.name : 'New') + '<i class="ti ti-chevron-down ms-2"></i>');
                            
                            // Tags
                            if (lead.tags) {
                                const tags = lead.tags.split(',').map(t => t.trim()).filter(t => t);
                                let tagsHtml = '';
                                tags.forEach(t => {
                                    tagsHtml += `<a href="javascript:void(0);" class="badge badge-soft-success fw-medium me-2">${t}</a>`;
                                });
                                $('#sidebar-lead-tags').html(tagsHtml || '<span class="text-muted small">No tags</span>');
                            } else {
                                $('#sidebar-lead-tags').html('<span class="text-muted small">No tags</span>');
                            }

                            // Last Modified
                            if (lead.updated_at) {
                                $('#sidebar-lead-modified-at').text(moment(lead.updated_at).format('DD MMM YYYY, hh:mm a'));
                            }
                            
                            // Modified By
                            if (lead.updated_by) {
                                $('#sidebar-lead-modified-by').html(`
                                    <span class="avatar avatar-xs me-1"><div class="avatar-title rounded-circle bg-secondary text-white" style="width:20px;height:20px;font-size:10px;">${(lead.updated_by.name || 'U').charAt(0).toUpperCase()}</div></span>${lead.updated_by.name}
                                `);
                            } else {
                                $('#sidebar-lead-modified-by').html('<span class="text-muted small">System</span>');
                            }

                            // Priority
                            let priorityHtml = '<span><i class="ti ti-square-rounded-filled me-1 text-secondary circle"></i>None</span>';
                            if (lead.priority) {
                                const p = lead.priority.toLowerCase();
                                if (p === 'high') {
                                    priorityHtml = '<span><i class="ti ti-square-rounded-filled me-1 text-danger circle"></i>High</span>';
                                } else if (p === 'medium') {
                                    priorityHtml = '<span><i class="ti ti-square-rounded-filled me-1 text-warning circle"></i>Medium</span>';
                                } else {
                                    priorityHtml = `<span><i class="ti ti-square-rounded-filled me-1 text-success circle"></i>${lead.priority.charAt(0).toUpperCase() + lead.priority.slice(1)}</span>`;
                                }
                            }
                            $('#sidebar-lead-priority').html(priorityHtml);

                            loadPipelineStatus(leadId, lead.lead_status_id);
                            loadActivityHistory(leadId);
                        }
                    }
                },
                error: function() {
                    $('#chat-messages-inner').html('<div class="alert alert-danger mx-3 mt-3">Failed to load messages.</div>');
                }
            });
        }

        function loadPipelineStatus(leadId, currentStageId) {
            $.ajax({
                url: `/{{ $box->slug }}/whatsapp/leads/${leadId}/activity-data`,
                type: 'GET',
                success: function(response) {
                    if (response.success && response.leadStatuses) {
                        let html = '<ul>';
                        response.leadStatuses.forEach(function(stage) {
                            let statusClass = 'bg-pending opacity-50';
                            if (stage.id == currentStageId) {
                                statusClass = 'bg-primary border border-2 border-dark';
                            } else if (stage.name.toLowerCase().includes('contacted')) {
                                statusClass = 'bg-info opacity-50';
                            } else if (stage.name.toLowerCase().includes('closed')) {
                                statusClass = 'bg-success opacity-50';
                            }
                            
                            html += `<li><a href="javascript:void(0);" class="${statusClass}">${stage.name}</a></li>`;
                        });
                        html += '</ul>';
                        $('#sidebar-pipeline-status').html(html);
                    }
                }
            });
        }

        function loadActivityHistory(leadId, type = 'all') {
            $('#activity-history-list').html('<div class="text-center py-4"><span class="spinner-border spinner-border-sm text-primary"></span></div>');
            $.ajax({
                url: `/{{ $box->slug }}/whatsapp/leads/${leadId}/activities`,
                type: 'GET',
                data: { type: type },
                success: function(response) {
                    if (response.success) {
                        renderActivities(response.activities || response.data);
                    }
                }
            });
        }

        function renderActivities(activities) {
            let html = '';
            if (activities && activities.length > 0) {
                activities.forEach(function(act) {
                    const iconClass = getActivityIcon(act.type);
                    const bgClass = getActivityBg(act.type);
                    const time = moment(act.activity_date).format('hh:mm A, DD MMM');
                    const addedBy = act.added_by_user ? act.added_by_user.name : 'System';
                    
                    html += `
                        <div class="card border shadow-none mb-3" style="border-radius: 12px; overflow: hidden;">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 13px;">${act.subject || 'Activity logged'}</h6>
                                        </div>
                                        <div class="d-flex align-items-center mb-2" style="font-size: 11px; color: #888;">
                                            <span>${time}</span>
                                            <span class="mx-2">•</span>
                                            <span>By ${addedBy}</span>
                                        </div>
                                        ${act.description ? `<p class="mb-0 text-muted" style="font-size: 12px; line-height: 1.4; color: #666 !important;">${act.description}</p>` : ''}
                                    </div>
                                    <div class="ms-2">
                                        <span class="avatar avatar-sm rounded-circle ${bgClass}" style="width: 32px; height: 32px;">
                                            <i class="${iconClass}" style="font-size: 14px;"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                html = '<div class="text-center py-4"><i class="ti ti-info-circle fs-2 text-muted mb-2"></i><p class="text-muted small">No interaction history found.</p></div>';
            }
            $('#activity-history-list').html(html);
        }

        function getActivityBg(type) {
            switch(type.toLowerCase()) {
                case 'call': return 'bg-pending';
                case 'meeting': return 'bg-info';
                case 'email': return 'bg-secondary-success';
                case 'whatsapp': return 'bg-success';
                case 'note': return 'bg-orange';
                default: return 'bg-light';
            }
        }

        function getActivityIcon(type) {
            switch(type.toLowerCase()) {
                case 'call': return 'ti ti-phone';
                case 'meeting': return 'ti ti-calendar';
                case 'email': return 'ti ti-mail';
                case 'whatsapp': return 'ti ti-brand-whatsapp';
                case 'note': return 'ti ti-note';
                case 'history': return 'ti ti-history';
                default: return 'ti ti-activity';
            }
        }

        function updatePinnedMessageBar(pinnedMsg) {
            activePinnedMessage = pinnedMsg;
            const bar = $('#pinned-message-bar');
            if (pinnedMsg) {
                let text = pinnedMsg.text || '';
                if (!text && pinnedMsg.media && typeof pinnedMsg.media === 'object') {
                    const mType = pinnedMsg.media.type || 'media';
                    if (mType === 'image') text = '📷 Photo';
                    else if (mType === 'video') text = '📹 Video';
                    else if (mType === 'audio') text = '🎵 Audio';
                    else if (mType === 'document') text = '📄 Document';
                    else if (mType === 'sticker') text = '👾 Sticker';
                    else text = '📎 Attachment';
                }
                if (!text) {
                    text = 'Pinned Message';
                }
                $('#pinned-bar-text').text(text);
                bar.attr('data-pinned-message-id', pinnedMsg.id);
                bar.removeClass('d-none');
            } else {
                bar.addClass('d-none');
                bar.removeAttr('data-pinned-message-id');
            }
        }

        function isNearBottom() {
            var isNear = false;
            $('#chat-messages-body').each(function() {
                var container = this;
                // Only measure containers that have actual layout height
                if (container.clientHeight > 0) {
                    if ((container.scrollHeight - container.clientHeight - container.scrollTop) <= 150) {
                        isNear = true;
                    }
                }
            });
            return isNear;
        }

        function scrollToBottom(isSmooth = false) {
            var performScroll = function() {
                try {
                    // Force maximum scroll on all potential containers
                    $('#chat-messages-body, .messages, .chat-body, #chat-messages-inner').each(function() {
                        if (this.scrollHeight > 0) {
                            if (isSmooth && typeof this.scrollTo === 'function') {
                                this.scrollTo({ top: this.scrollHeight + 100000, behavior: 'smooth' });
                            } else {
                                this.scrollTop = this.scrollHeight + 100000;
                                $(this).scrollTop(this.scrollHeight + 100000);
                            }
                        }
                    });

                    // Target the exact last message and force all its parents to scroll
                    var messages = document.querySelectorAll('.message-row');
                    if (messages.length > 0) {
                        var lastMsg = messages[messages.length - 1];
                        var parent = lastMsg.parentElement;
                        while (parent && parent !== document.body) {
                            if (parent.scrollHeight > parent.clientHeight) {
                                parent.scrollTop = parent.scrollHeight + 100000;
                            }
                            parent = parent.parentElement;
                        }
                        
                        // Force window and body scroll if the layout is broken and extending full height
                        document.body.scrollTop = document.body.scrollHeight + 100000;
                        document.documentElement.scrollTop = document.documentElement.scrollHeight + 100000;
                        window.scrollTo(0, document.body.scrollHeight + 100000);
                        
                        // Fallback scrollIntoView natively if needed
                        lastMsg.scrollIntoView({ behavior: isSmooth ? 'smooth' : 'auto', block: 'end' });
                    }
                } catch(e) {
                    console.error("Scroll to bottom failed:", e);
                }
            };
            
            performScroll();
            
            if (typeof requestAnimationFrame !== 'undefined') {
                requestAnimationFrame(performScroll);
            }

            if (!isSmooth) {
                // Brutally enforce scroll for 3 seconds to defeat any layout thrashing
                var scrollInterval = setInterval(performScroll, 100);
                setTimeout(function() {
                    clearInterval(scrollInterval);
                    performScroll();
                    isInitialScrolling = false;
                }, 3000);
            } else {
                isInitialScrolling = false;
            }

            try {
                $('#chat-messages-inner img').each(function() {
                    if (!this.complete) {
                        this.addEventListener('load', performScroll, { once: true });
                    }
                });
            } catch(e) {}
        }



        function getDateSeparatorLabel(dateStr) {
            const messageDate = moment(dateStr).startOf('day');
            const today = moment().startOf('day');
            const yesterday = moment().subtract(1, 'days').startOf('day');
            
            if (messageDate.isSame(today, 'd')) {
                return 'Today';
            } else if (messageDate.isSame(yesterday, 'd')) {
                return 'Yesterday';
            } else if (today.diff(messageDate, 'days') < 7) {
                // Within last week: show day name (e.g., "Monday")
                return messageDate.format('dddd');
            } else {
                // Older: show standard date (e.g., "May 20, 2026")
                return messageDate.format('MMMM DD, YYYY');
            }
        }

        function renderMessages(messages, leadName, shouldScroll = false, unreadCount = 0, isInitialLoad = false) {
            const displayName = leadName || 'Unnamed';
            let html = '';
            let lastDateLabel = '';

            messages.forEach(function(msg, index) {
                // Determine if a date separator is needed
                const msgDateStr = msg.created_at || new Date().toISOString();
                const dateLabel = getDateSeparatorLabel(msgDateStr);
                
                if (dateLabel !== lastDateLabel) {
                    html += `
                        <div class="text-center my-3 date-separator">
                            <span class="badge rounded-pill bg-white text-dark shadow-sm px-3 py-2 fw-medium border" style="font-size: 0.75rem; color: #54656f !important; background-color: #ffffff !important; display: inline-block;">
                                ${dateLabel}
                            </span>
                        </div>
                    `;
                    lastDateLabel = dateLabel;
                }

                if (unreadCount > 0 && index === messages.length - unreadCount) {
                    html += `
                        <div class="text-center my-3 unread-separator">
                            <span class="badge rounded-pill bg-success text-white shadow-sm px-3 py-2 fw-medium" style="font-size: 0.75rem; display: inline-block;">
                                ${unreadCount} Unread Message${unreadCount > 1 ? 's' : ''}
                            </span>
                        </div>
                    `;
                }

                const isRight = msg.type === 'OUTGOING';
                const time = moment(msg.created_at).format('hh:mm A');
                const isStarred = msg.is_starred || false;
                let starIcon = isStarred ? '<span class="text-muted me-1" style="font-size: 0.8rem;">★</span>' : '';
                let messageText = msg.text || '';
                if (messageText === '[Image]' || messageText === '[Document]' || messageText === '[Video]') {
                    messageText = '';
                } else if (messageText === '[unsupported message received]') {
                    messageText = '<div class="d-flex align-items-center text-danger small"><i class="bx bx-error-circle me-1 fs-5"></i> Unsupported message type</div>';
                } else if (messageText === '[Location]') {
                    messageText = '<div class="d-flex align-items-center text-muted"><i class="bx bx-map me-1 fs-5"></i> Location shared</div>';
                } else if (messageText === '[Voice Message]') {
                    messageText = '<div class="d-flex align-items-center text-muted"><i class="bx bx-microphone me-1 fs-5"></i> Voice message</div>';
                } else if (messageText.indexOf('message received]') !== -1 && messageText.indexOf('[') === 0) {
                    const typeClean = messageText.replace('[', '').replace(' message received]', '');
                    messageText = `<div class="d-flex align-items-center text-muted small"><i class="bx bx-info-circle me-1 fs-5"></i> Unhandled ${typeClean} message</div>`;
                }
                
                if (msg.media && typeof msg.media === 'object') {
                    const media = msg.media;
                    let mediaUrl = media.path || '';
                    if (mediaUrl && !mediaUrl.startsWith('http') && !mediaUrl.startsWith('/storage')) {
                        mediaUrl = '/storage/' + mediaUrl;
                    }
                    // If it already starts with /storage, don't add it again
                    if (mediaUrl && mediaUrl.startsWith('/storage/storage')) {
                        mediaUrl = mediaUrl.substring(8);
                    }
                    
                    if (media.type === 'image') {
                        messageText = `
                            <div class="chat-img-group">
                                <a href="${mediaUrl}" target="_blank">
                                    <img src="${mediaUrl}" class="img-fluid rounded" style="max-height: 250px; cursor: pointer;" alt="image">
                                </a>
                                ${messageText ? `<div class="mt-2">${messageText}</div>` : ''}
                            </div>
                        `;
                    } else if (media.type === 'video') {
                        messageText = `
                            <div class="chat-video-group">
                                <video controls class="img-fluid rounded" style="max-height: 250px;">
                                    <source src="${mediaUrl}" type="${media.mime_type}">
                                    Your browser does not support the video tag.
                                </video>
                                ${messageText ? `<div class="mt-2">${messageText}</div>` : ''}
                            </div>
                        `;
                    } else if (media.type === 'audio') {
                        messageText = `
                            <div class="chat-audio-group">
                                <audio controls style="max-width: 100%;">
                                    <source src="${mediaUrl}" type="${media.mime_type}">
                                </audio>
                            </div>
                        `;
                    } else if (media.type === 'document') {
                        messageText = `
                            <div class="chat-doc-group">
                                <a href="${mediaUrl}" target="_blank" class="btn btn-outline-primary btn-sm d-flex align-items-center">
                                    <i class="bx bx-file me-1"></i> ${media.filename || 'Download Document'}
                                </a>
                                ${messageText ? `<div class="mt-2">${messageText}</div>` : ''}
                            </div>
                        `;
                    } else if (media.type === 'sticker') {
                        messageText = `<img src="${mediaUrl}" style="width: 100px; height: 100px;" alt="sticker">`;
                    }
                } else if (!messageText && msg.campaign && msg.campaign.template) {
                    const template = msg.campaign.template;
                    let body = '';
                    if (template.components) {
                        const bodyRow = template.components.find(c => c.type === 'BODY');
                        if (bodyRow) body = bodyRow.text;
                    }
                    body = body.replace(/\{\{1\}\}/g, displayName).replace(/\{\{2\}\}/g, 'some');
                    
                    messageText = `
                        <div class="template-msg">
                            <small class="text-primary fw-bold d-block mb-1"><i class="bx bxs-megaphone"></i> ${msg.campaign.name}</small>
                            <div style="white-space: pre-wrap;">${body}</div>
                        </div>
                    `;
                } else if (!messageText) {
                    messageText = '<i class="text-muted">Empty WhatsApp Message</i>';
                }

                let statusIcon = '';
                if (isRight) {
                    const status = (msg.status || '').toLowerCase();
                    if (status === 'read') {
                        statusIcon = '<i class="ti ti-checks text-info ms-1" title="Read"></i>';
                    } else if (status === 'delivered') {
                        statusIcon = '<i class="ti ti-checks text-muted ms-1" title="Delivered"></i>';
                    } else if (status === 'sent') {
                        statusIcon = '<i class="ti ti-check text-muted ms-1" title="Sent"></i>';
                    } else if (status === 'failed') {
                        const errorMsg = msg.error ? `Error: ${msg.error}` : 'Sending failed';
                        statusIcon = `<i class="ti ti-alert-circle text-danger ms-1" title="${errorMsg.replace(/"/g, '&quot;')}" data-bs-toggle="tooltip"></i>`;
                    }
                }

                const emojis = ['👍', '❤️', '😂', '😮', '😢', '🙏', '👏'];
                let reactionBarHtml = '<div class="reaction-bar shadow-sm border">';
                emojis.forEach(emoji => {
                    reactionBarHtml += `<span class="reaction-emoji" onclick="reactToMessage(${msg.id}, '${emoji}')">${emoji}</span>`;
                });
                reactionBarHtml += `<span class="reaction-emoji" onclick="openFullEmojiReactionPicker(${msg.id})" style="color: #888;"><i class="bx bx-plus-circle"></i></span>`;
                reactionBarHtml += '</div>';

                let currentReactionHtml = '';
                if (msg.user_reaction || msg.contact_reaction) {
                    let reactionsText = '';
                    if (msg.user_reaction && msg.contact_reaction) {
                        reactionsText = msg.user_reaction === msg.contact_reaction 
                            ? msg.user_reaction + ' 2' 
                            : msg.user_reaction + msg.contact_reaction;
                    } else {
                        reactionsText = msg.user_reaction || msg.contact_reaction;
                    }
                    currentReactionHtml += `<div class="message-reaction-badge shadow-sm" onclick="reactToMessage(${msg.id}, '${msg.user_reaction || msg.contact_reaction}')">${reactionsText}</div>`;
                }

                const replyTo = msg.reply_to || msg.replyTo;
                let replyQuoteHtml = '';
                if (replyTo) {
                    const replySender = replyTo.type === 'OUTGOING' ? 'You' : displayName;
                    let replyText = replyTo.text || '';
                    if (!replyText && replyTo.media && typeof replyTo.media === 'object') {
                        const mType = replyTo.media.type || 'media';
                        if (mType === 'image') replyText = '📷 Photo';
                        else if (mType === 'video') replyText = '📹 Video';
                        else if (mType === 'audio') replyText = '🎵 Audio';
                        else if (mType === 'document') replyText = '📄 Document';
                        else if (mType === 'sticker') replyText = '👾 Sticker';
                        else replyText = '📎 Attachment';
                    }
                    if (!replyText) {
                        replyText = 'WhatsApp Message';
                    }
                    replyQuoteHtml = `
                        <div class="reply-quote-block" onclick="scrollToMessage(${replyTo.id})">
                            <div class="reply-quote-title">${replySender}</div>
                            <div class="reply-quote-body">${replyText}</div>
                        </div>
                    `;
                }

                const isMsgPinned = msg.is_pinned || (activePinnedMessage && activePinnedMessage.id === msg.id);
                let pinIcon = '';
                if (isMsgPinned) {
                    pinIcon = '<i class="bx bxs-pin ms-1 text-muted" style="font-size: 0.85rem; transform: rotate(45deg);" title="Pinned"></i>';
                }

                 html += `
                     <div class="chats ${isRight ? 'chats-right' : ''} chat-message-container" data-text="${messageText.replace(/<[^>]*>/g, '').toLowerCase()}" data-message-id="${msg.id}">
                        ${reactionBarHtml}
                        ${!isRight ? `<div class="chat-avatar"><div class="avatar-title rounded-circle bg-secondary text-white" style="width:40px;height:40px;">${displayName.charAt(0).toUpperCase()}</div></div>` : ''}
                        <div class="chat-content" style="position: relative;">
                            <div class="chat-profile-name ${isRight ? 'justify-content-end' : ''}">
                                <h6>${isRight ? '' : displayName} <span class="${isRight ? '' : 'ms-1'} small">${starIcon}${time}</span> ${statusIcon} ${pinIcon}</h6>
                            </div>
                            <div class="message-content shadow-sm" style="max-width: none; position: relative;">
                                ${replyQuoteHtml}
                                ${messageText}
                                ${isRight && (msg.status === 'Failed' || msg.status === 'failed') && msg.error ? `<div class="text-danger mt-1" style="font-size: 0.65rem; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 2px;">${msg.error}</div>` : ''}
                                ${currentReactionHtml}
                                
                                <!-- Dropdown Actions Menu -->
                                <div class="dropdown message-actions-dropdown">
                                    <button class="btn btn-link p-0 message-dropdown-trigger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bx bx-chevron-down"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px; padding: 6px 0; min-width: 180px;">
                                        <li><a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="replyToMessage(${msg.id})"><i class="bx bx-reply me-2"></i> Reply</a></li>
                                        <li><a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="openReactionPicker(${msg.id})"><i class="bx bx-smile me-2"></i> React</a></li>
                                        <li><a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="toggleStarMessage(${msg.id})"><i class="bx ${isStarred ? 'bx-star' : 'bxs-star'} me-2"></i> ${isStarred ? 'Unstar' : 'Star'}</a></li>
                                        <li><a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="${isMsgPinned ? 'unpinActiveMessage()' : `pinMessage(${msg.id})`}"><i class="bx bx-pin me-2"></i> ${isMsgPinned ? 'Unpin' : 'Pin'}</a></li>
                                        <li><a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="forwardMessage(${msg.id})"><i class="bx bx-share me-2"></i> Forward</a></li>
                                        <li><a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="copyMessageText(${msg.id})"><i class="bx bx-copy me-2"></i> Copy</a></li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li><a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="reportMessage(${msg.id})"><i class="bx bx-error-circle me-2"></i> Report</a></li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li><a class="dropdown-item py-2 px-3 d-flex align-items-center text-danger" href="javascript:void(0);" onclick="deleteMessage(${msg.id})"><i class="bx bx-trash me-2"></i> Delete</a></li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li><a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="selectMessageMode(${msg.id})"><i class="bx bx-check-circle me-2"></i> Select messages</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
					
                `;
            });

            if (activePinnedMessage) {
                html += `
                    <div class="text-center my-3 system-pinned-log">
                        <span class="badge rounded-pill bg-white text-dark shadow-sm px-3 py-2 fw-medium border" style="font-size: 0.75rem; color: #54656f !important; background-color: #ffffff !important; display: inline-block; font-family: inherit;">
                            You pinned a message
                        </span>
                    </div>
                `;
            }

            $('#chat-messages-inner').html(html || '<div class="text-center py-5 text-muted">No messages yet.</div>');
            if (shouldScroll) {
                let isSmooth = false;
                
                if (!isInitialLoad) {
                    if (window.forceNextScroll || isNearBottom()) {
                        isSmooth = true;
                    }
                }

                scrollToBottom(isSmooth);
                window.forceNextScroll = false;
            }

            // Lock/Unlock chat based on whether we have ever received at least one message from the customer, and the 24h window
            let isLocked = true;
            let lockReason = "Waiting for customer's reply. Normal messaging is locked...";
            let hasIncoming = false;

            if (messages && messages.length > 0) {
                for (let i = 0; i < messages.length; i++) {
                    if (messages[i].type === 'INCOMING') {
                        hasIncoming = true;
                        break;
                    }
                }
            }

            if (hasIncoming) {
                isLocked = false;
            } else {
                isLocked = true;
                lockReason = "Waiting for customer's reply. Normal messaging is locked...";
            }

            const chatInput = $('#chat-input');
            const submitBtn = $('.send-btn-whatsapp');
            const actionBtn = $('.action-circle-whatsapp');
            const emojiTrigger = $('.emoji-trigger-whatsapp');

            if (isLocked) {
                chatInput.prop('disabled', true)
                         .val('')
                         .attr('placeholder', lockReason)
                         .addClass('bg-light');
                submitBtn.prop('disabled', true).addClass('opacity-50').css('cursor', 'not-allowed');
                
                // Do NOT disable the action button entirely because they need to send Templates!
                actionBtn.removeClass('pe-none opacity-50').css('cursor', 'pointer');
                
                // Disable non-template options
                $('.dropdown-item:not(.btn-send-template-trigger)').addClass('pe-none opacity-50');
                
                emojiTrigger.addClass('pe-none opacity-50');
            } else {
                // Re-enable everything
                $('.dropdown-item').removeClass('pe-none opacity-50');
                chatInput.prop('disabled', false)
                         .attr('placeholder', 'Type a message...')
                         .removeClass('bg-light');
                submitBtn.prop('disabled', false).removeClass('opacity-50').css('cursor', 'pointer');
                actionBtn.removeClass('pe-none opacity-50').css('pointer-events', 'auto').css('cursor', 'pointer');
                emojiTrigger.removeClass('pe-none opacity-50');
            }
        }
        function generateSidebarHtml(leads, activeBgClass) {
            let html = '';
            leads.forEach(function(lead) {
                let displayName = lead.name;
                if (!displayName || displayName.startsWith('New WhatsApp Contact')) {
                    displayName = lead.phone || 'Unnamed';
                }
                let lastMsg = 'No messages yet';
                let time = '';
                
                if (lead.whatsapp_messages && lead.whatsapp_messages[0]) {
                    const last = lead.whatsapp_messages[0];
                    lastMsg = last.text || '';
                    
                    if (last.media && typeof last.media === 'object') {
                        const mediaType = last.media.type || 'file';
                        const icon = mediaType === 'image' ? '🖼️ Photo' : (mediaType === 'video' ? '🎥 Video' : (mediaType === 'audio' ? '🎵 Audio' : '📄 Document'));
                        lastMsg = icon + (lastMsg ? ': ' + lastMsg : '');
                    } else if (lastMsg === '[unsupported message received]') {
                        lastMsg = '⚠️ Unsupported message';
                    } else if (lastMsg === '[Location]') {
                        lastMsg = '📍 Location shared';
                    } else if (lastMsg === '[Voice Message]') {
                        lastMsg = '🎵 Voice message';
                    } else if (lastMsg.indexOf('message received]') !== -1 && lastMsg.indexOf('[') === 0) {
                        const typeClean = lastMsg.replace('[', '').replace(' message received]', '');
                        lastMsg = `📎 ${typeClean.charAt(0).toUpperCase() + typeClean.slice(1)} message`;
                    } else if (!lastMsg && last.campaign && last.campaign.template) {
                        lastMsg = '📢 Template Sent';
                    } else if (!lastMsg) {
                        lastMsg = 'Message received';
                    }
                    time = moment(last.created_at).fromNow();
                }

                const isMuted = lead.whatsapp_chat_setting && lead.whatsapp_chat_setting.muted_until && moment(lead.whatsapp_chat_setting.muted_until).isAfter(moment());
                const muteIcon = isMuted ? '<i class="bx bx-volume-mute text-muted ms-1" style="font-size: 0.9rem;"></i>' : '';
                const unread = (lead.unread_count > 0 && !isMuted) ? `<div class="new-message-count">${lead.unread_count}</div>` : '';
                
                let avatarChar = '';
                if (displayName === lead.phone || displayName === 'Unnamed') {
                    avatarChar = '<i class="fa-solid fa-user" style="font-size: 22px; line-height: 1;"></i>';
                } else {
                    avatarChar = displayName.charAt(0).toUpperCase();
                }
                const hasPinned = lead.pinned_count > 0;
                const pinIcon = hasPinned ? '<i class="bx bxs-pin text-muted" style="font-size: 0.95rem; transform: rotate(45deg);" title="Pinned Message"></i>' : '';
                
                html += `
                    <li class="user-list-item ${currentLeadId == lead.id ? 'active bg-light' : ''}" data-lead-id="${lead.id}" style="cursor:pointer;">
                        <a href="javascript:void(0);" onclick="loadChat(${lead.id}, '${displayName.replace(/'/g, "\\'")}')" class="d-flex align-items-center text-decoration-none text-dark">
                            <div class="avatar me-3">
                                <div class="avatar-title rounded-circle ${activeBgClass} text-white" style="width:45px; height:45px; display:flex; align-items:center; justify-content:center;">${avatarChar}</div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-0 fw-bold text-truncate">${displayName}${muteIcon}</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">${time}</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="mb-0 text-muted small text-truncate">${lastMsg.length > 30 ? lastMsg.substring(0, 30) + '...' : lastMsg}</p>
                                    <div class="d-flex align-items-center gap-1">
                                        ${pinIcon}
                                        ${(lead.unread_count > 0 && !isMuted) ? `<span class="badge bg-success rounded-circle">${lead.unread_count}</span>` : ''}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                `;
            });
            return html;
        }

        let currentSidebarTab = 'active';
        let sidebarOffset = 0;
        let hasMoreSidebarLeads = true;
        let isFetchingSidebar = false;
        let isPollingSidebar = false;

        function getListIdForTab(tab) {
            if (tab === 'active') return 'chat-leads-list';
            if (tab === 'unread') return 'unread-leads-list';
            if (tab === 'inactive') return 'inactive-leads-list';
            if (tab === 'notContacted') return 'not-contacted-leads-list';
        }

        function getEmptyMessageForTab(tab) {
            if (tab === 'active') return '<p>No active chats.</p>';
            if (tab === 'unread') return '<i class="bx bx-check-double fs-1 d-block mb-2 text-success"></i><p>No unread chats.</p>';
            if (tab === 'inactive') return '<p>No inactive chats.</p>';
            if (tab === 'notContacted') return '<p>No uncontacted leads.</p>';
        }

        function fetchSidebar(append = false, isPolling = false) {
            if (isFetchingSidebar || (isPolling && isPollingSidebar)) return;
            
            if (isPolling) isPollingSidebar = true;
            else isFetchingSidebar = true;

            // If polling, we refresh visible items (offset 0, limit = current offset or 20)
            let fetchOffset = isPolling ? 0 : sidebarOffset;
            let fetchLimit = isPolling ? Math.max(20, sidebarOffset) : 20;

            if (!append && !isPolling) {
                const listId = getListIdForTab(currentSidebarTab);
                $(`#${listId}`).html('<li class="p-5 text-center text-muted"><i class="bx bx-loader-alt bx-spin fs-1 mb-3"></i><p>Loading chats...</p></li>');
            }

            $.ajax({
                url: `/{{ $box->slug }}/whatsapp/chats/sidebar?tab=${currentSidebarTab}&offset=${fetchOffset}&limit=${fetchLimit}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        let bgClass = 'bg-primary';
                        if (currentSidebarTab === 'unread') bgClass = 'bg-success';
                        else if (currentSidebarTab === 'inactive') bgClass = 'bg-secondary';
                        else if (currentSidebarTab === 'notContacted') bgClass = 'bg-warning';

                        const html = generateSidebarHtml(response.leads || [], bgClass);
                        const listId = getListIdForTab(currentSidebarTab);

                        if (append && !isPolling) {
                            $(`#${listId}`).append(html);
                        } else {
                            $(`#${listId}`).html(html || `<li class="p-5 text-center text-muted">${getEmptyMessageForTab(currentSidebarTab)}</li>`);
                        }

                        if (!isPolling) {
                            sidebarOffset = response.offset;
                            hasMoreSidebarLeads = response.hasMore;
                        }

                        // Update Counts
                        if (response.counts) {
                            $('#count-active').text(response.counts.active);
                            $('#count-inactive').text(response.counts.inactive);
                            $('#count-notcontacted').text(response.counts.notContacted);
                            $('#count-unread').text(response.counts.unread);
                        }

                        // Auto-load first chat if none is selected
                        if (!currentLeadId && response.leads && response.leads.length > 0) {
                            const firstLeadToLoad = response.leads[0];
                            let firstDisplayName = firstLeadToLoad.name;
                            if (!firstDisplayName || firstDisplayName.startsWith('New WhatsApp Contact')) {
                                firstDisplayName = firstLeadToLoad.phone || 'Unnamed';
                            }
                            loadChat(firstLeadToLoad.id, firstDisplayName);
                        }
                    }
                },
                complete: function() {
                    if (isPolling) isPollingSidebar = false;
                    else isFetchingSidebar = false;
                    
                    $('#sidebar-infinite-loader').remove();
                }
            });
        }

        let isPollingChat = false;

        function pollChat() {
            if (!currentLeadId || isPollingChat) return;
            isPollingChat = true;
            
            let afterIdParam = '';
            if (allLoadedMessages.length > 0) {
                const lastMsg = allLoadedMessages[allLoadedMessages.length - 1];
                afterIdParam = `?after_id=${lastMsg.id}`;
            } else {
                afterIdParam = '?offset=0&limit=20';
            }
            
            $.ajax({
                url: `/{{ $box->slug }}/whatsapp/chats/${currentLeadId}/messages${afterIdParam}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        updatePinnedMessageBar(response.pinnedMessage);
                        
                        if (response.messages && response.messages.length > 0) {
                            let isAtBottom = isNearBottom();
                            if (window.forceNextScroll) {
                                isAtBottom = true;
                            }
                            
                            let hasNewMessages = false;
                            let newMessagesOnly = [];

                            response.messages.forEach(function(newMsg) {
                                const index = allLoadedMessages.findIndex(m => m.id === newMsg.id);
                                if (index > -1) {
                                    // Update existing message details (including reactions)
                                    allLoadedMessages[index] = newMsg;
                                    
                                    // Direct DOM update to prevent flickering
                                    const msgEl = $(`.chat-message-container[data-message-id="${newMsg.id}"]`);
                                    if (msgEl.length > 0) {
                                        let badgeHtml = '';
                                        if (newMsg.user_reaction || newMsg.contact_reaction) {
                                            let reactionsText = '';
                                            if (newMsg.user_reaction && newMsg.contact_reaction) {
                                                reactionsText = newMsg.user_reaction === newMsg.contact_reaction 
                                                    ? newMsg.user_reaction + ' 2' 
                                                    : newMsg.user_reaction + newMsg.contact_reaction;
                                            } else {
                                                reactionsText = newMsg.user_reaction || newMsg.contact_reaction;
                                            }
                                            badgeHtml = `<div class="message-reaction-badge shadow-sm" onclick="reactToMessage(${newMsg.id}, '${newMsg.user_reaction || newMsg.contact_reaction}')">${reactionsText}</div>`;
                                        }
                                        msgEl.find('.message-reaction-badge').remove();
                                        if (badgeHtml) {
                                            msgEl.find('.message-content').append(badgeHtml);
                                        }
                                    }
                                } else {
                                    allLoadedMessages.push(newMsg);
                                    newMessagesOnly.push(newMsg);
                                    hasNewMessages = true;
                                }
                            });
                            
                            if (hasNewMessages) {
                                if (isAtBottom) {
                                    renderMessages(allLoadedMessages, $('#active-chat-name').text(), true, 0);
                                    unreadScrollCount = 0;
                                    $('#scroll-to-bottom-btn').addClass('d-none');
                                } else {
                                    const newCount = newMessagesOnly.length;
                                    unreadScrollCount += newCount;
                                    
                                    renderMessages(allLoadedMessages, $('#active-chat-name').text(), false, unreadScrollCount);
                                    
                                    // Show floating button and update badge
                                    $('#scroll-to-bottom-btn').removeClass('d-none');
                                    $('#unread-scroll-badge').text(unreadScrollCount).removeClass('d-none');
                                }
                            }
                            
                            lastMessageCount = allLoadedMessages.length;
                        }
                    }
                },
                complete: function() {
                    isPollingChat = false;
                }
            });
        }

        // Interval Polling for Real-time Replies (Optimized to reduce server load)
        setInterval(function() {
            if (document.hidden) return; // Pause polling when tab is inactive
            if (currentLeadId) {
                pollChat();
            }
        }, 5000); // Poll active chat messages every 5 seconds

        setInterval(function() {
            if (document.hidden) return; // Pause polling when tab is inactive
            fetchSidebar(false, true); // Poll sidebar silently
        }, 15000); // Poll sidebar chat list every 15 seconds

        $(document).ready(function() {
            // Fetch sidebar instantly on page load
            fetchSidebar();

            // Tab Switching Logic
            $('#chat-tabs a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr("href");
                if (target === '#bottom-tab1') currentSidebarTab = 'active';
                else if (target === '#bottom-tab-unread') currentSidebarTab = 'unread';
                else if (target === '#bottom-tab2') currentSidebarTab = 'inactive';
                else if (target === '#bottom-tab3') currentSidebarTab = 'notContacted';

                sidebarOffset = 0;
                hasMoreSidebarLeads = true;
                fetchSidebar();
            });

            // Infinite Scroll for Sidebar Tabs
            $('#bottom-tab1, #bottom-tab-unread, #bottom-tab2, #bottom-tab3').on('scroll', function() {
                const container = this;
                if (container.scrollHeight - container.clientHeight - container.scrollTop <= 50) {
                    if (hasMoreSidebarLeads && !isFetchingSidebar) {
                        const listId = getListIdForTab(currentSidebarTab);
                        $(`#${listId}`).append('<li id="sidebar-infinite-loader" class="text-center py-2"><span class="spinner-border spinner-border-sm text-primary"></span></li>');
                        fetchSidebar(true);
                    }
                }
            });
            
            // Scroll to bottom floating button handler
            $(document).on('click', '#scroll-to-bottom-btn', function(e) {
                e.preventDefault();
                scrollToBottom();
                isInitialScrolling = false;
                unreadScrollCount = 0;
                $(this).addClass('d-none');
                $('#unread-scroll-badge').addClass('d-none').text('0');
            });

            // Listen for scroll events on the messages container
            $('#chat-messages-body').on('scroll', function() {
                const container = this;
                const isAtBottom = container.scrollHeight - container.clientHeight - container.scrollTop <= 150;
                
                if (isAtBottom) {
                    $('#scroll-to-bottom-btn').addClass('d-none');
                    unreadScrollCount = 0;
                } else if (unreadScrollCount === 0) {
                    $('#scroll-to-bottom-btn').removeClass('d-none');
                    $('#unread-scroll-badge').addClass('d-none');
                }
                
                // If we are currently doing our initial auto-scroll to the bottom, ignore infinite scroll triggers
                if (isInitialScrolling) {
                    return;
                }

                // Infinite scroll logic (loading older messages)
                if (container.scrollTop <= 10 && hasMoreMessages && !isFetchingMessages && currentLeadId) {
                    isFetchingMessages = true;
                    chatMessageOffset += 20;
                    
                    // Show a tiny loading indicator at the top
                    $('#chat-messages-inner').prepend('<div id="infinite-scroll-loader" class="text-center py-2"><span class="spinner-border spinner-border-sm text-primary"></span></div>');
                    
                    const oldScrollHeight = container.scrollHeight;
                    
                    $.ajax({
                        url: `/{{ $box->slug }}/whatsapp/chats/${currentLeadId}/messages?offset=${chatMessageOffset}&limit=20`,
                        type: 'GET',
                        success: function(response) {
                            $('#infinite-scroll-loader').remove();
                            if (response.success && response.messages) {
                                if (response.messages.length < 20) {
                                    hasMoreMessages = false;
                                }
                                if (response.messages.length > 0) {
                                    // Prepend the new messages to allLoadedMessages
                                    allLoadedMessages = response.messages.concat(allLoadedMessages);
                                    
                                    // Re-render
                                    renderMessages(allLoadedMessages, $('#active-chat-name').text(), false, 0);
                                    
                                    // Restore scroll position
                                    const newScrollHeight = container.scrollHeight;
                                    container.scrollTop = newScrollHeight - oldScrollHeight;
                                }
                            }
                            isFetchingMessages = false;
                        },
                        error: function() {
                            $('#infinite-scroll-loader').remove();
                            isFetchingMessages = false;
                        }
                    });
                }
            });

            // Convert to Lead Handler
            $(document).on('click', '#btn-convert-lead', function(e) {
                e.preventDefault();
                if (!currentLeadId) {
                    toastr.warning('No active contact selected.');
                    return;
                }
                
                // Show modal to select assigned user
                $('#convertLeadModal').modal('show');
            });
            
            $(document).on('submit', '#convert-lead-form', function(e) {
                e.preventDefault();
                if (!currentLeadId) return;

                const btn = $(this).find('button[type="submit"]');
                const assignedTo = $('#convert_assigned_to').val();

                btn.prop('disabled', true).addClass('opacity-50').html('<span class="spinner-border spinner-border-sm me-1"></span> Converting...');

                $.ajax({
                    url: `/{{ $box->slug }}/whatsapp/chats/${currentLeadId}/convert`,
                    type: 'POST',
                    data: {
                        assigned_to: assignedTo,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        btn.prop('disabled', false).removeClass('opacity-50').html('Convert to Lead');
                        if (response.success) {
                            $('#convertLeadModal').modal('hide');
                            toastr.success(response.message || 'Successfully converted to Lead!');
                            // Refresh activity history and pipeline status in sidebar
                            if (typeof loadPipelineStatus === 'function') {
                                loadPipelineStatus(currentLeadId, response.lead.lead_status_id);
                            }
                            if (typeof loadActivityHistory === 'function') {
                                loadActivityHistory(currentLeadId);
                            }
                            // Trigger sidebar sync if needed
                            if (typeof fetchSidebar === 'function') {
                                fetchSidebar();
                            }
                        } else {
                            toastr.error(response.message || 'Failed to convert contact.');
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).removeClass('opacity-50').html('Convert to Lead');
                        let errorMsg = 'Failed to convert contact.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastr.error(errorMsg);
                    }
                });
            });

            // Chat Search Toggle
            $(document).on('click', '.chat-search-btn', function() {
                $('#chat-search-bar').toggleClass('d-none');
                $('#chat-message-search-input').focus();
            });

            $(document).on('click', '#close-chat-search', function() {
                $('#chat-search-bar').addClass('d-none');
                $('#chat-message-search-input').val('').trigger('keyup');
            });

            $(document).on('keyup', '#chat-message-search-input', function() {
                const query = $(this).val().toLowerCase();
                if (query === '') {
                    $('.chat-message-container').show();
                } else {
                    $('.chat-message-container').each(function() {
                        const text = $(this).data('text');
                        if (text && text.indexOf(query) !== -1) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
            });
            @if($activeLead)
                loadChat({{ $activeLead->id }}, '{{ addslashes($activeLead->name) }}');
            @endif

            // Activity Log Logic
            let currentLeadIdForActivity = null;

            window.loadActivityDropdowns = function(leadId) {
                currentLeadIdForActivity = leadId;
                // Fetch Lead Statuses and Users for the modal
                $.ajax({
                    url: `/{{ $box->slug }}/whatsapp/leads/${leadId}/activity-data`,
                    type: 'GET',
                    success: function(res) {
                        if (res.success) {
                            // Populate Lead Status
                            let statusHtml = '<option value="">Select Lead Status</option>';
                            res.leadStatuses.forEach(s => {
                                statusHtml += `<option value="${s.id}" ${res.lead.lead_status_id == s.id ? 'selected' : ''}>${s.name}</option>`;
                            });
                            $('#activity_lead_status_id').html(statusHtml);

                            // Populate Users
                            let usersHtml = '<option value="">Select User</option>';
                            res.users.forEach(u => {
                                usersHtml += `<option value="${u.id}" ${res.lead.assigned_to == u.id ? 'selected' : ''}>${u.name}</option>`;
                            });
                            $('#assigned_to_assigned_to').html(usersHtml);
                            $('#followup_task_assign_to').html(usersHtml);

                            // Populate Activity Settings
                            let settingsHtml = '<option value="">Select Activity</option>';
                            res.activitySettings.forEach(s => {
                                settingsHtml += `<option value="${s.id}">${s.title}</option>`;
                            });
                            $('#activity_setting_id').html(settingsHtml);
                        }
                    }
                });
            };

            // Activity Tab Filtering
            $(document).on('click', '#activityTabsModern button', function() {
                const type = $(this).data('act-type');
                if (currentLeadId) {
                    loadActivityHistory(currentLeadId, type);
                }
            });

            $(document).on('click', '.btn-add-activity', function() {
                if (!currentLeadId) {
                    showToast('Please select a lead first.', 'error');
                    return;
                }
                loadActivityDropdowns(currentLeadId);
                $('#offcanvas_add').offcanvas('show');
            });

            // Activity Form Type Switching Logic
            $('input[name="type"]').on('change', function() {
                const type = $(this).val();
                $('.field-call, .field-meeting, .field-email, .field-whatsapp, .field-note, .field-general').hide();
                
                if (type === 'call') $('.field-call').show();
                else if (type === 'meeting') $('.field-meeting').show();
                else if (type === 'email') $('.field-email').show();
                else if (type === 'whatsapp') $('.field-whatsapp').show();
                else if (type === 'note') $('.field-note').show();
                else if (type === 'general') $('.field-general').show();
            });

            const boxSlug = '{{ $box->slug }}';

            // Handle Add Activity Form Submission
            $('#addActivityForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('lead_id', currentLeadIdForActivity);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: `/{{ $box->slug }}/whatsapp/leads/${currentLeadIdForActivity}/activities/store`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message || 'Activity logged successfully', 'success');
                            $('#offcanvas_add').offcanvas('hide');
                            $('#addActivityForm')[0].reset();
                        } else {
                            showToast(response.message || 'Failed to save activity', 'error');
                        }
                    },
                    error: function(xhr) {
                        showToast('Error saving activity. Check if you filled all required fields.', 'error');
                    }
                });
            });

            // Handle Activity Setting change - load Activity Types dynamically
            $(document).on('change', '#activity_setting_id', function() {
                const settingId = $(this).val();
                const $activityType = $('#activity_type');
                $activityType.empty().append('<option value="">Select Sub-Type</option>');

                if (settingId) {
                    $.ajax({
                        url: "{{ route('activities.types', ['box_slug' => $box->slug]) }}",
                        type: 'GET',
                        data: { activity_setting_id: settingId },
                        success: function(res) {
                            if (res.success && res.data) {
                                res.data.forEach(function(type) {
                                    $activityType.append('<option value="' + type.id + '">' + type.title + '</option>');
                                });
                            }
                        }
                    });
                }
            });

            // Toggle follow-up task section
            $('#followuptask_checkbox').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#followupTaskSection').slideDown();
                } else {
                    $('#followupTaskSection').slideUp();
                }
            });

            // Consolidated Send Message Handler
            $(document).on('submit', '#send-message-form', function(e) {
                e.preventDefault();
                const leadId = currentLeadId;
                const message = $('#chat-input').val().trim();
                if (!message || !leadId) return;

                // Double-safeguard check based on input lock state
                if ($('#chat-input').prop('disabled')) {
                    showToast("Normal messaging is locked. Please send a template or wait for the customer's reply.", 'error');
                    $('#quick_whatsapp_modal').one('shown.bs.modal', function() {
                        setTimeout(function() {
                            $('#quick_wa_template_id').select2('open');
                        }, 100);
                    });
                    $('.btn-send-template-trigger').first().trigger('click');
                    return;
                }

                const btn = $(this).find('button');
                btn.prop('disabled', true);


                const replyToMessageId = $('#reply-to-message-id').val();

                $.ajax({
                    url: '/{{ $box->slug }}/whatsapp/chats/send',
                    type: 'POST',
                    data: {
                        lead_id: leadId,
                        text: message,
                        reply_to_message_id: replyToMessageId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#chat-input').val('');
                            $('#reply-preview-container').addClass('d-none');
                            $('#reply-to-message-id').val('');
                            pollChat(); // Sync immediately
                        } else {
                            const errorMsg = typeof response.message === 'string' ? response.message : 'Failed to send via Meta API';
                            showToast(errorMsg, 'error');
                            if (response.error_code === '131047' || errorMsg.toLowerCase().includes('24h') || errorMsg.toLowerCase().includes('24 hours')) {
                                $('#quick_whatsapp_modal').one('shown.bs.modal', function() {
                                    setTimeout(function() {
                                        $('#quick_wa_template_id').select2('open');
                                    }, 100);
                                });
                                $('.btn-send-template-trigger').first().trigger('click');
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        showToast('Error sending message. Check console.', 'error');
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                    }
                });
            });

            // Media Upload Handlers
            let pendingMediaFile = null;

            $(document).on('click', '.media-upload-trigger', function() {
                const type = $(this).data('type');
                const $mediaInput = $('#media-input');
                
                // Dynamically change what the system file picker shows based on the button clicked
                if (type === 'image') {
                    $mediaInput.attr('accept', 'image/*,video/*');
                } else if (type === 'audio') {
                    $mediaInput.attr('accept', 'audio/*');
                } else if (type === 'document') {
                    // Allow ALL file types when Document is clicked
                    $mediaInput.attr('accept', '*/*');
                }
                
                $mediaInput.data('forced-type', type).trigger('click');
            });

            // LIVE CAMERA LOGIC
            let cameraStream = null;

            $('#open-live-camera').on('click', function() {
                if (!currentLeadId) {
                    showToast('Please select a chat first.', 'error');
                    return;
                }
                $('#liveCameraModal').modal('show');
                startCamera();
            });

            async function startCamera() {
                try {
                    cameraStream = await navigator.mediaDevices.getUserMedia({ 
                        video: { facingMode: "environment" }, 
                        audio: false 
                    });
                    const video = document.getElementById('camera-video');
                    video.srcObject = cameraStream;
                } catch (err) {
                    console.error("Camera error:", err);
                    showToast("Could not access camera. Please ensure you have given permission.", 'error');
                    $('#liveCameraModal').modal('hide');
                }
            }

            function stopCamera() {
                if (cameraStream) {
                    cameraStream.getTracks().forEach(track => track.stop());
                    cameraStream = null;
                }
            }

            $('#close-camera-btn, #liveCameraModal').on('hidden.bs.modal', function() {
                stopCamera();
            });

            $('#capture-photo-btn').on('click', function() {
                const video = document.getElementById('camera-video');
                const canvas = document.getElementById('camera-canvas');
                const context = canvas.getContext('2d');

                // Set canvas dimensions to match video
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                // Draw video frame to canvas
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Convert canvas to blob
                canvas.toBlob(function(blob) {
                    const file = new File([blob], `camera_capture_${Date.now()}.jpg`, { type: "image/jpeg" });
                    
                    stopCamera();
                    $('#liveCameraModal').modal('hide');

                    // Pass to preview
                    pendingMediaFile = file;
                    $('#media-preview-container').html('<div class="spinner-border text-primary" role="status"></div>');
                    $('#media-caption-input').val($('#chat-input').val());
                    $('#mediaPreviewModal').modal('show');

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#media-preview-container').html(`<img src="${e.target.result}" class="img-fluid" style="max-height: 400px; width: auto;">`);
                    };
                    reader.readAsDataURL(file);
                }, 'image/jpeg', 0.9);
            });

            $(document).on('change', '#media-input', function() {
                const file = this.files[0];
                if (!file || !currentLeadId) return;

                pendingMediaFile = file;
                const type = file.type;
                const reader = new FileReader();

                $('#media-preview-container').html('<div class="spinner-border text-primary" role="status"></div>');
                $('#media-caption-input').val($('#chat-input').val()); // Pre-fill caption from current input
                $('#mediaPreviewModal').modal('show');

                reader.onload = function(e) {
                    if (type.startsWith('image/')) {
                        $('#media-preview-container').html(`<img src="${e.target.result}" class="img-fluid" style="max-height: 400px; width: auto;">`);
                    } else if (type.startsWith('video/')) {
                        $('#media-preview-container').html(`<video controls class="img-fluid" style="max-height: 400px;"><source src="${e.target.result}" type="${type}"></video>`);
                    } else if (type.startsWith('audio/')) {
                        $('#media-preview-container').html(`<div class="p-5"><i class="bx bx-volume-full fs-1 text-primary mb-2"></i><br>Audio File: ${file.name}<br><audio controls class="mt-3"><source src="${e.target.result}" type="${type}"></audio></div>`);
                    } else {
                        $('#media-preview-container').html(`<div class="p-5 text-center"><i class="bx bx-file fs-1 text-primary mb-2"></i><br><strong>${file.name}</strong><br><small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small></div>`);
                    }
                };

                reader.readAsDataURL(file);
            });

            $('#confirm-media-send').on('click', function() {
                if (!pendingMediaFile || !currentLeadId) return;
                
                const btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Sending...');

                const replyToId = $('#reply-to-message-id').val();

                const formData = new FormData();
                formData.append('file', pendingMediaFile);
                formData.append('lead_id', currentLeadId);
                formData.append('caption', $('#media-caption-input').val());
                if (replyToId) {
                    formData.append('reply_to_message_id', replyToId);
                }
                formData.append('_token', '{{ csrf_token() }}');

                // Show loading state in chat
                $('#chat-messages-inner').append(`
                    <div class="chats chats-right" id="media-uploading-loader">
                        <div class="chat-content">
                            <div class="message-content shadow-sm bg-light">
                                <div class="d-flex align-items-center">
                                    <div class="spinner-border spinner-border-sm me-2 text-primary" role="status"></div>
                                    <span>Sending ${pendingMediaFile.name}...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                scrollToBottom();
                $('#mediaPreviewModal').modal('hide');

                $.ajax({
                    url: `/{{ $box->slug }}/whatsapp/chats/send-media`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#media-uploading-loader').remove();
                        if (response.success) {
                            $('#chat-input').val('');
                            $('#reply-preview-container').addClass('d-none');
                            $('#reply-to-message-id').val('');
                            loadChat(currentLeadId, $('#active-chat-name').text());
                        } else {
                            showToast('Upload failed: ' + (response.message || 'Unknown error'), 'error');
                            const errorMsg = typeof response.message === 'string' ? response.message : '';
                            if (response.error_code === '131047' || errorMsg.toLowerCase().includes('24h') || errorMsg.toLowerCase().includes('24 hours')) {
                                $('#quick_whatsapp_modal').one('shown.bs.modal', function() {
                                    setTimeout(function() {
                                        $('#quick_wa_template_id').select2('open');
                                    }, 100);
                                });
                                $('.btn-send-template-trigger').first().trigger('click');
                            }
                        }
                    },
                    error: function() {
                        $('#media-uploading-loader').remove();
                        showToast('Upload failed due to a server error.', 'error');
                    },
                    complete: function() {
                        btn.prop('disabled', false).html('<i class="bx bx-paper-plane me-1"></i> Send Now');
                        pendingMediaFile = null;
                        $('#media-input').val('');
                        $('#camera-input').val('');
                    }
                });
            });

            let map, marker;

            // Location sending handler
            $(document).on('click', '#send-location-btn', function(e) {
                e.preventDefault();
                if (!currentLeadId) {
                    showToast('Please select a chat first.', 'error');
                    return;
                }
                
                $('#locationPickerModal').modal('show');
            });
            
            $('#locationPickerModal').on('shown.bs.modal', function () {
                if (!map) {
                    map = L.map('map-container').setView([28.6139, 77.2090], 13); // Default to Delhi
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);
                    
                    marker = L.marker([28.6139, 77.2090], {draggable: true}).addTo(map);
                    
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            map.setView([lat, lng], 15);
                            marker.setLatLng([lat, lng]);
                        });
                    }
                } else {
                    map.invalidateSize();
                }
            });
            
            $('#confirm-location-send').on('click', function() {
                if (!marker) return;
                
                const pos = marker.getLatLng();
                const mapUrl = `https://www.google.com/maps?q=${pos.lat},${pos.lng}`;
                const locationType = $('input[name="locationType"]:checked').val();
                
                let messageText = '';
                
                if (locationType === 'live') {
                    const duration = $('input[name="liveDuration"]:checked').val() || '1 hour';
                    messageText = `🟢 Live Location Shared (Expires in ${duration}): \nTrack here: ${mapUrl}`;
                } else {
                    messageText = `📍 Shared Location: \n${mapUrl}`;
                }
                
                $('#chat-input').val(messageText);
                $('#send-message-form').submit();
                
                $('#locationPickerModal').modal('hide');
            });
            
            // Toggle duration options based on location type
            $(document).on('change', 'input[name="locationType"]', function() {
                if ($(this).val() === 'live') {
                    $('#liveDurationOptions').slideDown(200);
                } else {
                    $('#liveDurationOptions').slideUp(200);
                }
            });

            // Contact sending handler
            let contactOffset = 0;
            let contactSearch = '';
            let isContactLoading = false;
            let hasMoreContacts = true;
            let selectedContacts = new Map(); // Store as Map to keep track of details by ID

            $(document).on('click', '#send-contact-btn', function(e) {
                e.preventDefault();
                if (!currentLeadId) {
                    showToast('Please select a chat first.', 'error');
                    return;
                }
                
                $('#contactPickerModal').modal('show');
                selectedContacts.clear();
                updateSelectedCount();
                resetAndLoadContacts();
            });

            function resetAndLoadContacts() {
                contactOffset = 0;
                contactSearch = $('#contact-search-input').val();
                hasMoreContacts = true;
                $('#contact-picker-list').empty();
                loadContactsForPicker();
            }

            function loadContactsForPicker() {
                if (isContactLoading || !hasMoreContacts) return;
                
                isContactLoading = true;
                const list = $('#contact-picker-list');
                if (contactOffset === 0) {
                    list.html('<tr><td colspan="4" class="text-center p-4"><div class="spinner-border text-primary" role="status"></div></td></tr>');
                } else {
                    list.append('<tr id="contact-load-spinner"><td colspan="4" class="text-center p-2"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');
                }
                
                $.ajax({
                    url: `/{{ $box->slug }}/whatsapp/fetch-contacts`,
                    type: 'GET',
                    data: {
                        search: contactSearch,
                        offset: contactOffset
                    },
                    success: function(response) {
                        isContactLoading = false;
                        $('#contact-load-spinner').remove();
                        if (contactOffset === 0) list.empty();

                        if (response.success) {
                            renderContacts(response.data);
                            hasMoreContacts = response.has_more;
                            contactOffset += 100;
                            $('#total-contacts-count').text(response.total_count || 0);
                        } else {
                            if (contactOffset === 0) list.html('<tr><td colspan="4" class="text-center text-danger p-4">Failed to load.</td></tr>');
                        }
                    },
                    error: function() {
                        isContactLoading = false;
                        $('#contact-load-spinner').remove();
                        if (contactOffset === 0) list.html('<tr><td colspan="4" class="text-center text-danger p-4">Error connecting to server.</td></tr>');
                    }
                });
            }

            function renderContacts(contacts) {
                const list = $('#contact-picker-list');

                if (contacts.length === 0 && contactOffset === 0) {
                    list.append('<tr><td colspan="4" class="text-center p-4 text-muted">No leads found.</td></tr>');
                    return;
                }

                contacts.forEach(contact => {
                    const isChecked = selectedContacts.has(contact.id) ? 'checked' : '';
                    const row = `
                        <tr class="contact-row" style="cursor:pointer;" data-id="${contact.id}">
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input contact-checkbox" type="checkbox" value="${contact.id}" 
                                        data-name="${contact.name || 'Unknown'}" 
                                        data-phone="${contact.phone || ''}"
                                        data-sec="${contact.sec_phone || ''}" ${isChecked}>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <div class="avatar-title rounded-circle bg-soft-primary text-primary" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center; font-size:12px;">
                                            ${(contact.name || 'U').substring(0, 1).toUpperCase()}
                                        </div>
                                    </div>
                                    <span class="fw-medium">${contact.name || 'Unknown'}</span>
                                </div>
                            </td>
                            <td>${contact.phone || '-'}</td>
                            <td>${contact.sec_phone || '-'}</td>
                        </tr>
                    `;
                    list.append(row);
                });
            }

            // Selection Logic
            $(document).on('click', '.contact-row', function(e) {
                if ($(e.target).is('input')) return; // Don't trigger twice if checkbox itself clicked
                const cb = $(this).find('.contact-checkbox');
                cb.prop('checked', !cb.prop('checked')).trigger('change');
            });

            $(document).on('change', '.contact-checkbox', function() {
                const id = $(this).val();
                if ($(this).is(':checked')) {
                    selectedContacts.set(id, {
                        name: $(this).data('name'),
                        phone: $(this).data('phone'),
                        sec: $(this).data('sec')
                    });
                } else {
                    selectedContacts.delete(id);
                }
                updateSelectedCount();
            });

            function updateSelectedCount() {
                $('#selected-contacts-count').text(selectedContacts.size);
            }

            // Search functionality with debounce
            let searchTimeout;
            $('#contact-search-input').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    resetAndLoadContacts();
                }, 500);
            });

            // Infinite Scroll logic
            $('#contactPickerModal .table-responsive').on('scroll', function() {
                const container = $(this);
                if (container.scrollTop() + container.innerHeight() >= container[0].scrollHeight - 50) {
                    loadContactsForPicker();
                }
            });

            $('#share-selected-contacts').on('click', function() {
                if (selectedContacts.size === 0) {
                    showToast('Please select at least one contact.', 'error');
                    return;
                }

                let fullText = `📇 *CONTACT LIST* (${selectedContacts.size})\n`;
                fullText += `━━━━━━━━━━━━━━━━━\n`;
                
                selectedContacts.forEach(contact => {
                    fullText += `\n👤 *${contact.name}*`;
                    fullText += `\n📞 ${contact.phone}`;
                    if (contact.sec) {
                        fullText += `\n📱 ${contact.sec}`;
                    }
                    fullText += `\n`;
                });
                
                fullText += `\n━━━━━━━━━━━━━━━━━`;

                $('#contactPickerModal').modal('hide');
                $('#chat-input').val(fullText);
                $('#send-message-form').submit();
            });

            // Chat Options Handlers
            $(document).on('click', '.dropdown-item:contains("Close Chat")', function(e) {
                e.preventDefault();
                currentLeadId = null;
                $('.chat-user-list li').removeClass('active');
                $('#chat-user-name').text('Select a chat');
                $('#chat-user-status').text('Offline');
                $('#chat-messages-inner').html('<div class="h-100 d-flex align-items-center justify-content-center text-muted"><div class="text-center"><i class="bx bx-chat fs-1 mb-2"></i><p>Select a contact to start chatting</p></div></div>');
                $('.chat-footer').addClass('d-none');
            });

            $('#confirm-clear-chat').on('click', function() {
                if (!currentLeadId) return;
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Clearing...');

                $.ajax({
                    url: `/${boxSlug}/whatsapp/chats/clear`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        lead_id: currentLeadId
                    },
                    success: function(response) {
                        btn.prop('disabled', false).html('Clear Messages');
                        if (response.success) {
                            $('#clear-chat').modal('hide');
                            fetchMessages(currentLeadId);
                        }
                    }
                });
            });

            $('#confirm-delete-chat').on('click', function() {
                if (!currentLeadId) return;
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Deleting...');

                $.ajax({
                    url: `/${boxSlug}/whatsapp/chats/delete`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        lead_id: currentLeadId
                    },
                    success: function(response) {
                        btn.prop('disabled', false).html('Delete Chat');
                        if (response.success) {
                            $('#change-chat').modal('hide');
                            location.reload(); // Refresh to update list
                        }
                    }
                });
            });

            $('#confirm-block-user').on('click', function() {
                if (!currentLeadId) return;
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Blocking...');

                $.ajax({
                    url: `/${boxSlug}/whatsapp/chats/block`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        lead_id: currentLeadId
                    },
                    success: function(response) {
                        btn.prop('disabled', false).html('Block');
                        if (response.success) {
                            $('#block-user').modal('hide');
                            location.reload();
                        }
                    }
                });
            });

            $('#confirm-mute').on('click', function() {
                if (!currentLeadId) return;
                const value = $('input[name="mute_duration"]:checked').val();
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Applying...');

                $.ajax({
                    url: `/${boxSlug}/whatsapp/chats/update-settings`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        lead_id: currentLeadId,
                        type: 'mute',
                        value: value
                    },
                    success: function(response) {
                        btn.prop('disabled', false).html('Mute');
                        if (response.success) {
                            $('#mute-notification').modal('hide');
                            showToast('Chat muted successfully', 'success');
                        }
                    }
                });
            });

            $('#confirm-disappearing').on('click', function() {
                if (!currentLeadId) return;
                const value = $('input[name="disappearing_timer"]:checked').val();
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Applying...');

                $.ajax({
                    url: `/${boxSlug}/whatsapp/chats/update-settings`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        lead_id: currentLeadId,
                        type: 'disappearing',
                        value: value
                    },
                    success: function(response) {
                        btn.prop('disabled', false).html('Apply');
                        if (response.success) {
                            $('#disappearing-messages').modal('hide');
                            showToast('Disappearing messages set successfully', 'success');
                        }
                    }
                });
            });

            $('#confirm-report').on('click', function() {
                showToast('Contact reported. Thank you for your feedback.', 'success');
                $('#report-user').modal('hide');
            });

@include("whatsapp.partials.upload_campaign_js")

            $(document).on('click', '.btn-send-template-trigger', function() {
                if (!currentLeadId) {
                    showToast('Please select a chat first.', 'warning');
                    return;
                }
                
                const leadName = $('#active-chat-name').text() || 'Lead';
                
                // Create a temporary element and trigger the quick whatsapp modal logic
                $('<button>')
                    .addClass('d-none btn-quick-whatsapp')
                    .data('lead-id', currentLeadId)
                    .data('lead-name', leadName)
                    .appendTo('body')
                    .trigger('click')
                    .remove();
            });

        });

        // ==========================================
        // Sidebar Chat Search Filter
        // ==========================================
        $('#sidebar-search').on('keyup', function() {
            var searchTerm = $(this).val().toLowerCase();
            
            // Iterate over all chat list items across all tabs
            $('.user-list-item').each(function() {
                var $item = $(this);
                // Extract the text content from the item, which includes Name, Phone Number, and recent message text
                var itemText = $item.text().toLowerCase();
                
                if (itemText.indexOf(searchTerm) > -1) {
                    // Match found: ensure it's visible (by default these are display: block or list-item, we just remove d-none)
                    $item.removeClass('d-none');
                } else {
                    // No match: hide the item completely
                    $item.addClass('d-none');
                }
            });
        });
        // ==========================================
        // Message Reactions Logic
        // ==========================================
        window.reactToMessage = function(messageId, emoji) {
            // Check if removing existing reaction using local state
            const msgEl = $(`.chat-message-container[data-message-id="${messageId}"]`);
            const msgIdx = allLoadedMessages.findIndex(m => m.id === messageId);
            if (msgIdx > -1) {
                const currentReaction = allLoadedMessages[msgIdx].user_reaction;
                if (currentReaction === emoji) {
                    emoji = ""; // Send empty string to remove
                }
            }
            
            $.ajax({
                url: `/{{ $box->slug }}/whatsapp/messages/react`,
                type: 'POST',
                data: {
                    message_id: messageId,
                    emoji: emoji,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if(response.success) {
                        // Find the message in allLoadedMessages to update its user_reaction
                        const msgIdx = allLoadedMessages.findIndex(m => m.id === messageId);
                        if (msgIdx > -1) {
                            allLoadedMessages[msgIdx].user_reaction = emoji || null;
                            
                            // Re-calculate the combined badge
                            const updatedMsg = allLoadedMessages[msgIdx];
                            let badgeHtml = '';
                            if (updatedMsg.user_reaction || updatedMsg.contact_reaction) {
                                let reactionsText = '';
                                if (updatedMsg.user_reaction && updatedMsg.contact_reaction) {
                                    reactionsText = updatedMsg.user_reaction === updatedMsg.contact_reaction 
                                        ? updatedMsg.user_reaction + ' 2' 
                                        : updatedMsg.user_reaction + updatedMsg.contact_reaction;
                                } else {
                                    reactionsText = updatedMsg.user_reaction || updatedMsg.contact_reaction;
                                }
                                badgeHtml = `<div class="message-reaction-badge shadow-sm" onclick="reactToMessage(${messageId}, '${updatedMsg.user_reaction || updatedMsg.contact_reaction}')">${reactionsText}</div>`;
                            }
                            
                            msgEl.find('.message-reaction-badge').remove();
                            if (badgeHtml) {
                                msgEl.find('.message-content').append(badgeHtml);
                            }
                        }
                    } else {
                        toastr.error(response.message || 'Failed to send reaction');
                    }
                },
                error: function() {
                    toastr.error('Failed to communicate with server');
                }
            });
        };

        // Double-click to react (Double tap simulation like Instagram/WhatsApp)
        $(document).on('dblclick', '.message-content', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const container = $(this).closest('.chat-message-container');
            const messageId = container.data('message-id');
            if (!messageId) return;
            
            // Toggle/add default reaction 👍
            const defaultEmoji = '👍';
            reactToMessage(messageId, defaultEmoji);
        });

        window.replyToMessage = function(msgId) {
            const msg = allLoadedMessages.find(m => m.id === msgId);
            if (msg) {
                const senderName = msg.type === 'OUTGOING' ? 'You' : ($('#active-chat-name').text() || 'Contact');
                
                let text = msg.text || '';
                if (!text && msg.media && typeof msg.media === 'object') {
                    const mType = msg.media.type || 'media';
                    if (mType === 'image') text = '📷 Photo';
                    else if (mType === 'video') text = '📹 Video';
                    else if (mType === 'audio') text = '🎵 Audio';
                    else if (mType === 'document') text = '📄 Document';
                    else if (mType === 'sticker') text = '👾 Sticker';
                    else text = '📎 Attachment';
                }
                if (!text) {
                    text = 'WhatsApp Message';
                }

                $('#reply-preview-sender').text(senderName);
                $('#reply-preview-text').text(text);
                $('#reply-to-message-id').val(msg.id);
                $('#reply-preview-container').removeClass('d-none');
                $('#chat-input').focus();
            }
        };

        $(document).on('click', '#cancel-reply-btn', function() {
            $('#reply-preview-container').addClass('d-none');
            $('#reply-to-message-id').val('');
        });

        window.scrollToMessage = function(targetMsgId) {
            const element = $(`.chat-message-container[data-message-id="${targetMsgId}"]`);
            if (element.length) {
                const container = $('#chat-messages-body');
                const targetScrollTop = element.offset().top - container.offset().top + container.scrollTop() - 100;
                
                container.animate({
                    scrollTop: targetScrollTop
                }, 500);
                
                element.addClass('highlight-message');
                setTimeout(() => {
                    element.removeClass('highlight-message');
                }, 2000);
            } else {
                toastr.info('Message is in older history');
            }
        };

        window.toggleStarMessage = function(msgId) {
            $.ajax({
                url: `/{{ $box->slug }}/whatsapp/messages/star`,
                type: 'POST',
                data: {
                    message_id: msgId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        const isStarred = response.is_starred;
                        toastr.success(isStarred ? 'Message starred successfully!' : 'Message unstarred successfully!');
                        
                        // Update local cache
                        const msgIdx = allLoadedMessages.findIndex(m => m.id === msgId);
                        if (msgIdx > -1) {
                            allLoadedMessages[msgIdx].is_starred = isStarred;
                        }
                        
                        // Re-render chat area to apply changes
                        renderMessages(allLoadedMessages, $('#active-chat-name').text(), false, 0);
                    } else {
                        toastr.error('Failed to toggle star on message');
                    }
                },
                error: function() {
                    toastr.error('Failed to star message');
                }
            });
        };

        window.pinMessage = function(msgId) {
            $('#pin-target-message-id').val(msgId);
            $('#pinDurationModal').modal('show');
        };

        window.unpinActiveMessage = function() {
            if (!currentLeadId) return;
            $.ajax({
                url: `/{{ $box->slug }}/whatsapp/messages/unpin`,
                type: 'POST',
                data: {
                    lead_id: currentLeadId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Message unpinned successfully!');
                        pollChat();
                        fetchSidebar();
                    } else {
                        toastr.error(response.message || 'Failed to unpin message');
                    }
                },
                error: function() {
                    toastr.error('Failed to unpin message');
                }
            });
        };

        $(document).on('click', '.pin-duration-btn', function() {
            const msgId = $('#pin-target-message-id').val();
            const duration = $(this).data('duration');
            if (!msgId) return;

            $.ajax({
                url: `/{{ $box->slug }}/whatsapp/messages/pin`,
                type: 'POST',
                data: {
                    message_id: msgId,
                    duration: duration,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#pinDurationModal').modal('hide');
                        toastr.success('Message pinned successfully!');
                        pollChat();
                        fetchSidebar();
                    } else {
                        toastr.error(response.message || 'Failed to pin message');
                    }
                },
                error: function() {
                    toastr.error('Failed to pin message');
                }
            });
        });

        $(document).on('click', '#unpin-message-btn', function(e) {
            e.stopPropagation();
            window.unpinActiveMessage();
        });

        $(document).on('click', '#pinned-message-bar', function() {
            const pinnedId = $(this).attr('data-pinned-message-id');
            if (pinnedId) {
                scrollToMessage(pinnedId);
            }
        });

        window.forwardMessage = function(msgId) {
            const msg = allLoadedMessages.find(m => m.id === msgId);
            if (msg) {
                toastr.info('Forward message: ' + (msg.text || '[Media]'));
            }
        };

        window.copyMessageText = function(msgId) {
            const msg = allLoadedMessages.find(m => m.id === msgId);
            if (msg && msg.text) {
                navigator.clipboard.writeText(msg.text).then(function() {
                    toastr.success('Message copied to clipboard!');
                }, function() {
                    toastr.error('Failed to copy message');
                });
            } else {
                toastr.warning('No text to copy in this message');
            }
        };

        window.reportMessage = function(msgId) {
            toastr.warning('Message reported. Thank you.');
        };

        window.deleteMessage = function(msgId) {
            if (confirm('Are you sure you want to delete this message?')) {
                $.ajax({
                    url: `/{{ $box->slug }}/whatsapp/messages/${msgId}/delete`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Message deleted successfully.');
                            
                            // Remove from allLoadedMessages and DOM
                            allLoadedMessages = allLoadedMessages.filter(m => m.id !== msgId);
                            $(`.chat-message-container[data-message-id="${msgId}"]`).fadeOut(400, function() {
                                $(this).remove();
                                if (allLoadedMessages.length === 0) {
                                    $('#chat-messages-inner').html('<div class="text-center py-5 text-muted">No messages yet.</div>');
                                }
                            });
                        } else {
                            toastr.error(response.message || 'Failed to delete message.');
                        }
                    },
                    error: function() {
                        toastr.error('Failed to delete message from server.');
                    }
                });
            }
        };

        window.selectMessageMode = function(msgId) {
            toastr.info('Message selection mode enabled.');
        };

        window.openReactionPicker = function(messageId) {
            // Find the message container and toggle the reaction bar visibility
            const msgEl = $(`.chat-message-container[data-message-id="${messageId}"]`);
            const rxBar = msgEl.find('.reaction-bar');
            
            const wasShown = rxBar.hasClass('show');
            $('.reaction-bar').removeClass('show'); // Hide other open reaction bars
            
            if (!wasShown) {
                rxBar.addClass('show');
                
                // Set up a click-outside handler to close it
                setTimeout(() => {
                    $(document).on('click.reactionBarClose', function(e) {
                        if (!$(e.target).closest('.reaction-bar').length) {
                            rxBar.removeClass('show');
                            $(document).off('click.reactionBarClose');
                        }
                    });
                }, 50);
            }
        };

        window.openFullEmojiReactionPicker = function(messageId) {
            window.reactionMessageId = messageId;
            // Hide the quick reaction bar
            const msgEl = $(`.chat-message-container[data-message-id="${messageId}"]`);
            msgEl.find('.reaction-bar').removeClass('show');
            
            // Re-use the existing emoji picker
            const pickerContainer = $('#modern-emoji-picker-container');
            pickerContainer.removeClass('d-none');
            
            // Optionally, switch to the emoji tab
            $('.picker-tab-btn[data-tab="emoji"]').click();
        };

    </script>
@endpush
<!-- Add Activity Offcanvas -->
<div class="offcanvas offcanvas-end offcanvas-large actv-lead" tabindex="-1" id="offcanvas_add" aria-labelledby="offcanvas_add_label">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="offcanvas_add_label">Add New Activity</h5>
        <button type="button" class="btn-close custom-btn-close border p-1" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="addActivityForm">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Activity Type <span class="text-danger">*</span></label>
                    <div class="d-flex flex-wrap gap-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="type_call" value="call" checked>
                            <label class="form-check-label" for="type_call">Call</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="type_meeting" value="meeting">
                            <label class="form-check-label" for="type_meeting">Meeting</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="type_whatsapp" value="whatsapp">
                            <label class="form-check-label" for="type_whatsapp">WhatsApp</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="type_email" value="email">
                            <label class="form-check-label" for="type_email">Email</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="type_note" value="note">
                            <label class="form-check-label" for="type_note">Note</label>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 field-call field-meeting field-whatsapp field-general">
                    <div class="mb-3">
                        <label class="form-label">Activity</label>
                        <select class="form-control" id="activity_setting_id" name="activity_setting_id">
                            <option value="">Select Activity</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6 field-call field-meeting field-whatsapp field-general">
                    <div class="mb-3">
                        <label class="form-label">Activity Sub-Type</label>
                        <select class="form-control" id="activity_type" name="activity_type">
                            <option value="">Select Sub-Type</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Lead Status</label>
                        <select class="form-control" id="activity_lead_status_id" name="lead_status_id">
                            <option value="">Select Status</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Assigned To</label>
                        <select class="form-control" id="assigned_to_assigned_to" name="assigned_to">
                            <option value="">Select User</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter subject (optional)">
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Description / Remarks</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Activity Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="activity_date" name="activity_date" required value="{{ date('Y-m-d\TH:i') }}">
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="followuptask_checkbox" name="followup_task">
                        <label class="form-check-label" for="followuptask_checkbox">Create a follow-up task</label>
                    </div>
                </div>

                <div id="followupTaskSection" style="display:none;" class="col-md-12 border-top pt-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Task Type</label>
                            <select class="form-control" id="followup_task_type" name="followup_task_type">
                                <option value="call">Call</option>
                                <option value="meeting">Meeting</option>
                                <option value="task">General Task</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Task Date</label>
                            <input type="datetime-local" class="form-control" id="followup_task_date" name="followup_task_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Assign To</label>
                            <select class="form-control" id="followup_task_assign_to" name="followup_task_assign_to">
                                <option value="">Select User</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="button" class="btn btn-light me-2" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Activity</button>
            </div>
        </form>
    </div>
</div>

<!-- Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Location Picker Modal -->
<div class="modal fade" id="locationPickerModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title text-white"><i class="bx bx-map me-2"></i> Select Location</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="map-container" style="height: 350px; width: 100%;"></div>
                <div class="p-3 bg-light border-top">
                    <div class="d-flex mb-3 gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="locationType" id="locTypeCurrent" value="current" checked>
                            <label class="form-check-label fw-bold" for="locTypeCurrent">Send Current Location</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="locationType" id="locTypeLive" value="live">
                            <label class="form-check-label fw-bold text-success" for="locTypeLive">Share Live Location</label>
                        </div>
                    </div>
                    
                    <div id="liveDurationOptions" style="display: none;" class="mb-2">
                        <label class="form-label small text-muted mb-1">Select Duration:</label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" name="liveDuration" id="dur15m" value="15 minutes">
                            <label class="btn btn-outline-success btn-sm rounded-pill" for="dur15m">15 minutes</label>
                            
                            <input type="radio" class="btn-check" name="liveDuration" id="dur1h" value="1 hour" checked>
                            <label class="btn btn-outline-success btn-sm rounded-pill" for="dur1h">1 hour</label>
                            
                            <input type="radio" class="btn-check" name="liveDuration" id="dur8h" value="8 hours">
                            <label class="btn btn-outline-success btn-sm rounded-pill" for="dur8h">8 hours</label>
                        </div>
                    </div>
                    
                    <p class="text-muted small mb-0 mt-2"><i class="bx bx-info-circle me-1"></i> Drag the map marker to precisely adjust the location.</p>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light p-3">
                <button type="button" class="btn btn-label-secondary fw-bold" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                <button type="button" id="confirm-location-send" class="btn btn-primary px-4 fw-bold" style="border-radius: 10px;">
                    <i class="bx bx-paper-plane me-1"></i> Send Location
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Mute Notification Modal -->
<!-- Convert Lead Modal -->
<div class="modal fade" id="convertLeadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title text-white"><i class="bx bx-user-plus me-2"></i>Convert to Lead</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="convert-lead-form">
                <div class="modal-body p-4 bg-light">
                    <p class="text-muted mb-3">Please select the user to assign this lead to.</p>
                    <div class="mb-3">
                        <label for="convert_assigned_to" class="form-label fw-semibold">Assigned To</label>
                        <select class="form-select" id="convert_assigned_to" name="assigned_to" required>
                            @if(isset($users))
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ Auth::id() == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            @else
                                <option value="{{ Auth::id() }}" selected>{{ Auth::user()->name }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Convert to Lead</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="mute-notification" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Mute Notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <p>How long do you want to mute notifications for this chat?</p>
                <div class="list-group list-group-flush text-start mt-3">
                    <label class="list-group-item d-flex align-items-center cursor-pointer">
                        <input class="form-check-input me-3" type="radio" name="mute_duration" value="8_hours" checked>
                        8 Hours
                    </label>
                    <label class="list-group-item d-flex align-items-center cursor-pointer">
                        <input class="form-check-input me-3" type="radio" name="mute_duration" value="1_week">
                        1 Week
                    </label>
                    <label class="list-group-item d-flex align-items-center cursor-pointer">
                        <input class="form-check-input me-3" type="radio" name="mute_duration" value="always">
                        Always
                    </label>
                    <label class="list-group-item d-flex align-items-center cursor-pointer">
                        <input class="form-check-input me-3" type="radio" name="mute_duration" value="off">
                        Never Mute (Off)
                    </label>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-mute">Mute</button>
            </div>
        </div>
    </div>
</div>

<!-- Disappearing Messages Modal -->
<div class="modal fade" id="disappearing-messages" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Disappearing Messages</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <i class="bx bx-time-five fs-1 text-primary mb-3"></i>
                <p>For more privacy and storage, all new messages will disappear from this chat for everyone after the selected duration.</p>
                <div class="list-group list-group-flush text-start mt-3">
                    <label class="list-group-item d-flex align-items-center cursor-pointer">
                        <input class="form-check-input me-3" type="radio" name="disappearing_timer" value="off" checked>
                        Off
                    </label>
                    <label class="list-group-item d-flex align-items-center cursor-pointer">
                        <input class="form-check-input me-3" type="radio" name="disappearing_timer" value="24_hours">
                        24 Hours
                    </label>
                    <label class="list-group-item d-flex align-items-center cursor-pointer">
                        <input class="form-check-input me-3" type="radio" name="disappearing_timer" value="7_days">
                        7 Days
                    </label>
                    <label class="list-group-item d-flex align-items-center cursor-pointer">
                        <input class="form-check-input me-3" type="radio" name="disappearing_timer" value="90_days">
                        90 Days
                    </label>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-disappearing">Apply</button>
            </div>
        </div>
    </div>
</div>

<!-- Clear Chat Modal -->
<div class="modal fade" id="clear-chat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bx bx-brush-alt fs-1 text-warning"></i>
                </div>
                <h5 class="fw-bold mb-2">Clear Chat?</h5>
                <p class="text-muted mb-4">Messages will be removed from this device only. This action cannot be undone.</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-warning fw-bold" id="confirm-clear-chat">Clear Messages</button>
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Chat Modal (change-chat as per data-bs-target) -->
<div class="modal fade" id="change-chat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bx bx-trash fs-1 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">Delete Chat?</h5>
                <p class="text-muted mb-4">Are you sure you want to delete this entire chat history?</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-danger fw-bold" id="confirm-delete-chat">Delete Chat</button>
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report User Modal -->
<div class="modal fade" id="report-user" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Report Contact</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-3">The last 5 messages from this contact will be forwarded to WhatsApp. No one in this chat will be notified.</p>
                <div class="mb-3">
                    <label class="form-label fw-bold">Reason for reporting</label>
                    <select class="form-control" id="report-reason">
                        <option value="spam">Spam</option>
                        <option value="abuse">Abuse or Harassment</option>
                        <option value="inappropriate">Inappropriate Content</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="block-also" checked>
                    <label class="form-check-label" for="block-also">Block contact and delete chat</label>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-report">Report</button>
            </div>
        </div>
    </div>
</div>





<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_leadinfo" aria-labelledby="offcanvas_lead_info">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="offcanvas_lead_infp">Lead Information</h5>
        <button type="button" class="btn-close custom-btn-close border p-1" data-bs-dismiss="offcanvas" aria-label="Close"><i class="ti ti-x"></i></button>
    </div>
    <div class="offcanvas-body">
         <div class="right-sidebar-wrap active">
						<div class="slimscroll">
							
							<div class="sidebar-body">
								
								
<div class="accordion lea-accordion" id="accordionExample">
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
        Lead Detail
      </button>
    </h2>
    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
      <div class="accordion-body">
            <div class="card">
							<div class="card-body pb-2">
								<div class="d-flex align-items-center justify-content-between flex-wrap">
									<div class="d-flex align-items-center mb-2">
										<div class="avatar avatar-xxl me-3 flex-shrink-0 border p-2">
											<h6 class="text-default fw-medium" id="sidebar-lead-avatar" style="width: 100%;height: 100%;align-items: center;display: flex;justify-content: center;border-radius: 50%;">HT</h6>
										</div>
										<div>
											<h5 class="mb-1" id="sidebar-lead-name">Lead Name <span class="star-icon"><i class="fa-solid fa-star"></i></span></h5>
											<p class="mb-1 text-muted small"><i class="ti ti-phone me-1"></i><span id="sidebar-lead-phone">N/A</span></p>
											<p class="mb-1 text-muted small"><i class="ti ti-building me-1"></i><span id="sidebar-lead-company">Company</span></p>
											<p class="mb-0 text-muted small"><i class="ti ti-map-pin-pin me-1"></i><span id="sidebar-lead-address">Address</span></p>
										</div>
									</div>
									<div class="contacts-action">
										<span class="badge badge-light"><i class="ti ti-lock"></i>Private</span>
										<div class="dropdown mb-2">
											<a href="#" class="bg-success text-white py-1 px-2" data-bs-toggle="dropdown" aria-expanded="false" id="sidebar-lead-status-btn">
												Closed<i class="ti ti-chevron-down ms-2"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-right">
												<a class="dropdown-item" href="javascript:void(0);"><span>Closed</span></a>
												<a class="dropdown-item" href="javascript:void(0);"><span>Opened</span></a>
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
									<li class="row mb-3"><span class="col-6">Date Created</span><span class="col-6" id="sidebar-lead-created">10 Jan 2024</span></li>
									<li class="row mb-3"><span class="col-6">Value</span><span class="col-6" id="sidebar-lead-value">$0</span></li>
									<li class="row mb-3"><span class="col-6">Source</span><span class="col-6" id="sidebar-lead-source">WhatsApp</span></li>
								</ul>
								<h6 class="mb-3 fw-semibold">Owner</h6>
								<div class="d-flex align-items-center mb-3">
									<div class="avatar avatar-md me-2">
										<img id="sidebar-lead-owner-img" src="../assets/img/profiles/avatar-21.jpg" alt="Image">
									</div>
									<p id="sidebar-lead-owner-name">No Owner</p>
								</div>
								<hr>
								<h6 class="mb-3 fw-semibold">Tags</h6>
								<div id="sidebar-lead-tags">
                                    <a href="javascript:void(0);" class="badge badge-soft-success fw-medium me-2">Collab</a>
                                </div>
								<hr>
								<h6 class="mb-3 fw-semibold">Priority</h6>
								<div class="priority-info">
									<div class="dropdown">
										<a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><span id="sidebar-lead-priority"><span><i class="ti ti-square-rounded-filled me-1 text-danger circle"></i>High</span></span><i class="ti ti-chevron-down me-1"></i></a>
										<div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item" href="javascript:void(0);">
												<span><i class="ti ti-square-rounded-filled me-1 text-danger circle"></i>High</span>
											</a>
											<a class="dropdown-item" href="javascript:void(0);">
												<span><i class="ti ti-square-rounded-filled me-1 text-success circle"></i>Low</span>
											</a>
										</div>
									</div>
								</div>
								<hr>
								<div class="d-flex align-items-center justify-content-between flex-wrap">
									<h6 class="mb-3 fw-semibold">Contacts</h6>
									<a href="javascript:void(0);" class="link-purple mb-3 fw-medium" data-bs-toggle="modal" data-bs-target="#add_contact"><i class="ti ti-circle-plus me-1"></i>Add New</a>
								</div>
                                <div id="sidebar-lead-contacts-container">
                                </div>
								<hr>
								<ul>
									<li class="row mb-3"><span class="col-6">Last Modified</span><span class="col-6" id="sidebar-lead-modified-at"></span></li>
									<li class="row mb-0"><span class="col-6">Modified By</span>
										<span class="col-6" id="sidebar-lead-modified-by">
										</span>
									</li>
								</ul>
							</div>
						</div>
			
			
			
			
			
			
      </div>
    </div>
  </div>

  </div>

  
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
        Activities
      </button>
    </h2>
    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
      <div class="accordion-body p-2">
		  <div class="card mb-3 shadow-none border-0">
							<div class="card-body p-0 pb-2">
								<h6 class="fw-semibold mb-3">Lead Pipeline Status</h6>
								<div class="pipeline-list" id="sidebar-pipeline-status">
									<ul>
                                        <!-- Dynamically loaded -->
									</ul>
								</div>
								<div class="d-flex justify-content-between align-items-center mb-3">
                                    <ul class="nav nav-tabs nav-tabs-bottom border-0 activity-tabs-modern" id="activityTabsModern" role="tablist" style="margin-bottom: 0;">
                                        <li class="nav-item" role="presentation">
                                            <button data-bs-toggle="tab" data-bs-target="#tab-activities" data-act-type="all" class="nav-link active py-1 px-2 small">Activities</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button data-bs-toggle="tab" data-bs-target="#tab-notes" data-act-type="note" class="nav-link py-1 px-2 small">Notes</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button data-bs-toggle="tab" data-bs-target="#tab-calls" data-act-type="call" class="nav-link py-1 px-2 small">Calls</button>
                                        </li>
                                    </ul>
                                    <button class="btn-add-new-activity btn-add-activity">
                                        <i class="ti ti-circle-plus me-1"></i>Add New
                                    </button>
                                </div>
							</div>
						</div>

							<div id="activity-history-list" class="contact-activity">
                                <!-- Dynamically loaded cards -->
                                <p class="text-muted text-center py-3">Select a lead to view activities</p>
							</div>
      </div>
    </div>
  </div>
		
										
							</div>			
							
						</div>
					</div>


    </div>
</div>










<!-- Block User Modal -->
<div class="modal fade" id="block-user" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bx bx-block fs-1 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">Block Contact?</h5>
                <p class="text-muted mb-4">Blocked contacts will no longer be able to call you or send you messages.</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-danger fw-bold" id="confirm-block-user">Block</button>
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>



@include("whatsapp.partials.upload_campaign_modal")





@include("whatsapp.partials.leads_whatsapp_modal")
@include("whatsapp.partials.leads_whatsapp_js")

@endsection