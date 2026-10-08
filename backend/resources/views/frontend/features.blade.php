@extends('layouts.frontend.main')
@section('content');
@section('title', 'Key Features')
@section('description', 'Discover how ZipZapGo empowers your business with lead management, WhatsApp API integration, and call center solutions.')
<section class="breadcrumb_area">
            <img class="breadcrumb_shap" src="{{ asset('assets/frontend/main/img/breadcrumb/banner_bg.png') }}" alt="">
            <div class="container">
                <div class="breadcrumb_content text-center">
                    <h1 class="f_p f_700 f_size_50 w_color l_height50 mb_20">Key Features </h1>
                </div>
            </div>
        </section>
        <section class="stratup_service_area sec_pad bg_color key-features">
            <div class="container">
                <div class="sec_title mb_70 wow fadeInUp" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                    <h2 class="f_p f_size_30 l_height40 f_600 t_color text-center">Key Points for Lead Management </h2>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="stratup_service_img">
                            <img class="" src="{{ asset('assets/frontend/main/img/crm-gif.gif') }}" alt="">
                            <!-- <img class="" src="main/img/reminder.gif" alt="" /> -->
                        </div>
                    </div>
                    <div class="col-lg-6 d-flex align-items-center">
                        <div class="stratup_service_info">
                            <div class="startup_service_item">
                                <div class="icon"><i class="ti-check-box"></i></div>
                                <h3>Automate Leads Capture </h3>
                                <p>Embrace efficiency by transitioning from manual data entry on papers and Excel sheets to automated lead capture systems. Streamline your workflow and eliminate the risk of errors associated with traditional methods. </p>
                            </div>
                            <div class="startup_service_item">
                                <div class="icon icon_two"><i class="ti-reload"></i></div>
                                <h3>Auto Lead Assignments </h3>
                                <p>Bid farewell to the time-consuming task of manually assigning leads. Optimize your team's productivity by implementing automatic lead assignments, ensuring a swift and seamless distribution of leads among your sales representatives. </p>
                            </div>
                            <div class="startup_service_item">
                                <div class="icon icon_three"><i class="icon-bell"></i></div>
                                <h3>Set Automatic Reminders </h3>
                                <p>Never miss a follow-up opportunity again. Utilize automated reminders to prompt timely follow-ups, enhancing your engagement with potential clients. This feature ensures that valuable leads are consistently nurtured.</p>
                            </div>
                            <div class="startup_service_item">
                                <div class="icon icon_four"><i class="ti-layout-tab-window"></i></div>
                                <h3>Performance Reports in One Go</h3>
                                <p>Empower your decision-making process with comprehensive performance reports that can be generated effortlessly in just three clicks. Save time and gain valuable insights into your lead management strategies for future improvements.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="stratup_service_area sec_pad key-features">
            <div class="container">
                <div class="sec_title mb_70 wow fadeInUp" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                    <h2 class="f_p f_size_30 l_height40 f_600 t_color text-center">Key Points for WhatsApp API Integration</h2>
                </div>
                <div class="row">
                    <div class="col-lg-6 d-flex align-items-center">
                        <div class="stratup_service_info">
                            <div class="startup_service_item">
                                <div class="icon"><i class="ti-email"></i></div>
                                <h3>Send Welcome Messages </h3>
                                <p>Enhance customer experience by sending personalized welcome messages promptly. Make a positive first impression without wasting valuable time, setting the tone for a fruitful interaction. </p>
                            </div>
                            <div class="startup_service_item">
                                <div class="icon icon_two"><i class="ti-announcement"></i></div>
                                <h3>Schedule Campaigns </h3>
                                <p>Optimize your outreach efforts by scheduling campaigns with filtered data on specific days. This strategic approach allows you to target your audience effectively and maximize the impact of your WhatsApp marketing initiatives.</p>
                            </div>
                            <div class="startup_service_item">
                                <div class="icon icon_three"><i class="icon-layers"></i></div>
                                <h3>Manage Templates </h3>
                                <p>Streamline your communication by efficiently managing WhatsApp templates within your CRM. This ensures consistency in messaging and facilitates a more organized and professional interaction with your audience. </p>
                            </div>
                            <div class="startup_service_item">
                                <div class="icon icon_four"><i class="ti-clipboard"></i></div>
                                <h3>Add Activities</h3>
                                <p>Elevate customer engagement by setting follow-up reminders seamlessly during live chat interactions. This proactive approach ensures that potential leads are nurtured effectively, even in the midst of ongoing conversations.
                                </p>
                            </div>
                            <div class="startup_service_item">
                                <div class="icon icon_four"><i class="icon-screen-tablet"></i></div>
                                <h3>Instant Reports</h3>
                                <p>Stay informed about the performance of your scheduled campaigns with instant reports. This real-time feedback allows you to adapt and optimize your strategies promptly, maximizing the effectiveness of your WhatsApp marketing efforts.
                                </p>
                            </div>
                            <div class="startup_service_item">
                                <div class="icon icon_four"><i class="icon-screen-tablet"></i></div>
                                <h3>Chat Bots</h3>
                                <p>Enhance customer interaction during holidays and off-hours by implementing chat bots. This innovative solution ensures continuous engagement, potentially converting more leads even when human interaction is limited.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" style="text-align:right;">
                        <div class="stratup_service_img">
                            <img class="" src="{{ asset('assets/frontend/main/img/wapp-api.gif') }}" alt="">
                            <img class="" src="{{ asset('assets/frontedn/main/img/report.gif') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="stratup_service_area sec_pad bg_color key-features">
            <div class="container">
                <div class="sec_title mb_70 wow fadeInUp" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                    <h2 class="f_p f_size_30 l_height40 f_600 t_color text-center">Key Points for Call Center Solution </h2>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="stratup_service_img">
                            <img class="" src="{{ asset('assets/frontend/main/img/report.gif') }}main/img/call-center-1.gif" alt="">
                            <img class="" src="{{ asset('assets/frontend/main/img/teamwork.gif') }}" alt="">
                        </div>
                    </div>
                    <div class="col-lg-6 d-flex align-items-center">
                        <div class="stratup_service_info">
                            <div class="startup_service_item">
                                <div class="icon"><i class="icon-badge"></i></div>
                                <h3>Auto Dialing </h3>
                                <p>Boost efficiency by scheduling automatic calls to leads, particularly during times when staff availability is limited. This automated approach ensures a consistent outreach without straining your resources. </p>
                            </div>
                            <div class="startup_service_item">
                                <div class="icon icon_two"><i class="icon-bell"></i></div>
                                <h3>Recordings </h3>
                                <p>Elevate productivity through call recordings, serving as valuable resources for training purposes. Learn from past interactions, refine strategies, and enhance the overall performance of your call center team. </p>
                            </div>
                            <div class="startup_service_item">
                                <div class="icon icon_three"><i class="icon-layers"></i></div>
                                <h3>Click to Call </h3>
                                <p>Streamline manual call processes with the convenience of click-to-call functionality. Improve the speed and ease of communication, fostering a more responsive and agile approach to customer interactions.</p>
                            </div>
                            <div class="startup_service_item">
                                <div class="icon icon_four"><i class="icon-screen-tablet"></i></div>
                                <h3>Instant Alerts for Missed Calls</h3>
                                <p>Prioritize responsiveness by implementing a system that delivers instant alerts upon the occurrence of missed incoming calls. This proactive feature ensures that no potential opportunities or client inquiries go unnoticed, allowing for timely follow-up actions.</p>
                            </div>
                            <div class="startup_service_item">
                                <div class="icon icon_four"><i class="icon-screen-tablet"></i></div>
                                <h3>Team Management and Automated Call Routing</h3>
                                <p>Optimize your call center's workflow by implementing advanced team management features. Automate call routing to seamlessly redirect calls when a team member is occupied with another conversation. This intelligent system ensures that incoming calls are efficiently distributed among available resources, maintaining a consistent and responsive customer service experience.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

@endsection
