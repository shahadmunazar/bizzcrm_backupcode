@extends('layouts.frontend.main')
@section('content')
@section('title', 'Home')
@section('description', 'Empower Your Business with zigZapgo: Where Efficiency Meets Innovation!')
    <section class="new_startup_banner_area">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.4s">
                    <div class="new_startup_img">
                        <div class="line line_one"><img src="{{ asset('assets/frontend/main/img/new/line_01.png') }}" alt=""></div>
                        <div class="line line_two"><img src="{{ asset('assets/frontend/main/img/new/line_02.png') }}" alt=""></div>
                        <div class="line line_three"><img src="{{ asset('assets/frontend/main/img/new/line_03.png') }}" alt=""></div>
                        <img class="main-hero-img" src="{{ asset('assets/frontend/main/img/hero-img-new.png') }}" alt="">
                    </div>
                </div>
                <div class="col-lg-6 d-flex align-items-center">
                    <div class="new_startup_content">
                        <h2 class="f_700 f_size_40 l_height50 w_color mb_20 wow fadeInRight" data-wow-delay="0.3s">
                            Empower Your Business with <span>zigZapgo:</span> Where Efficiency Meets Innovation!</h2>
                        <p class="f_400 w_color l_height28 wow fadeInRight" data-wow-delay="0.4s">Designed with a focus on empowering small businesses, marketers, and solopreneurs, our solution is tailored to meet the unique needs of your dynamic ventures. We understand the intricacies of your operations and have crafted a comprehensive platform to enhance your efficiency and effectiveness.</p>
                        <div class="action_btn d-flex align-items-center mt_40 wow fadeInRight" data-wow-delay="0.6s">
                            <a href="{{ route('register') }}" class="btn_hover app_btn" style="margin-right:10px;">Sign up free <i class="ti-arrow-right"></i></a>
                            <a href="#" class="btn_hover app_btn">Watch video <i class="ti-control-play"></i></a>
                        </div>
                    </div>
                </div>
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
                            <img class="" src="{{ asset('assets/frontend/main/img/report.gif') }}" alt="">
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
                            <img class="" src="{{ asset('assets/frontend/main/img/call-center-1.gif') }}" alt="">
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
        <section class="erp_customer_logo_area sec_pad">
            <div class="container">
                <div class="hosting_title erp_title text-center">
                    <h2>Our Clients <span class="icon_heart"></span> Trusted By Great Brands </h2>
                    <p>Our clients are trusted by great brands across various industries. We take pride in the strong relationships we've built with well-known and reputable companies, earning their trust through our commitment to delivering quality products/services and maintaining a high level of professionalism. </p>
                </div>
                <ul class="list-unstyled animation_inner">
                    <li class="wow slideInnew2" data-wow-delay="0.2s"><img src="{{ asset('assets/frontend/main/img/p1.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="0.4s"><img src="{{ asset('assets/frontend/main/img/p2.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="0.6s"><img src="{{ asset('assets/frontend/main/img/p3.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="0.7s"><img src="{{ asset('assets/frontend/main/img/p4.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="0.9s"><img src="{{ asset('assets/frontend/main/img/p5.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="1s">  <img src="{{ asset('assets/frontend/main/img/p6.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="1.1s"><img src="{{ asset('assets/frontend/main/img/p7.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="1.2s"><img src="{{ asset('assets/frontend/main/img/p8.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="1s">  <img src="{{ asset('assets/frontend/main/img/p9.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="1.4s"><img src="{{ asset('assets/frontend/main/img/p10.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="1.5s"><img src="{{ asset('assets/frontend/main/img/p11.jpg.html') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="1.6s"><img src="{{ asset('assets/frontend/main/img/p12.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="1.7s"><img src="{{ asset('assets/frontend/main/img/p13.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="1.8s"><img src="{{ asset('assets/frontend/main/img/p14.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="1.9s"><img src="{{ asset('assets/frontend/main/img/p15.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="2s">  <img src="{{ asset('assets/frontend/main/img/p16.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="2.2s"><img src="{{ asset('assets/frontend/main/img/p17.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="2.4s"><img src="{{ asset('assets/frontend/main/img/p18.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="2.6s"><img src="{{ asset('assets/frontend/main/img/p19.jpg') }}" alt=""></li>
                    <li class="wow slideInnew2" data-wow-delay="2.7s"><img src="{{ asset('assets/frontend/main/img/p20.jpg') }}" alt=""></li>
                </ul>
                <div class="text-center">
                    <a href="#" class="er_btn">More Customers </a>
                </div>
            </div>
        </section>
        <section class="event_fact_area sec_pad">
            <div class="container">
                <div class="hosting_title security_title text-center">
                    <h2><span>Thoroughly vetted and validated by over</span> 100 businesses in India through rigorous trials and testing. </h2>
                    <p>Our platform stands as a testament to our commitment to empowering businesses worldwide, providing a robust solution that has consistently delivered tangible results.</p>
                </div>
                <div class="seo_fact_info">
                    <div class="seo_fact_item wow fadeIn">
                        <div class="text">
                            <div class="counter one">100 </div>
                            <p><strong style="font-size: 20px;color: #6357e2;">+</strong><br>Happy Clients </p>
                        </div>
                    </div>
                    <div class="seo_fact_item wow fadeIn" data-wow-delay="0.3s">
                        <div class="text">
                            <div class="counter two">3</div>
                            <p><strong style="font-size: 20px;color: #6357e2;">Millions</strong><br>Leads managed </p>
                        </div>
                    </div>
                    <div class="seo_fact_item wow fadeIn" data-wow-delay="0.6s">
                        <div class="text">
                            <div class="counter three">30 </div>
                            <p><strong style="font-size: 20px;color: #6357e2;">+</strong><br>Auto Fetch Sources </p>
                        </div>
                    </div>
                    <div class="seo_fact_item last wow fadeIn" data-wow-delay="0.9s">
                        <div class="text">
                            <div class="counter four">200 </div>
                            <p><strong style="font-size: 20px;color: #6357e2;">%</strong><br>Sale Growth </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="testimonial_area_four sec_pad">
            <div class="testimonial_shap_img" style="background: url(main/img/new/testimonial_bg_shap.png) no-repeat bottom left;"></div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 d-flex align-items-center">
                        <div class="testimonial_title">
                            <h6>Testimonials </h6>
                            <h2 class="f_p f_size_30 l_height45 f_600 t_color">Loved by businesses, <br>and individuals
                                across <br>the globe. </h2>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="stratup_testimonial_info d-flex align-items-center">
                            <div class="testimonial_slider_four owl-carousel">
                                <div class="item">
                                    <div class="author_img">
                                        <img src="{{ asset('assets/frontend/main/img/new/member_01.jpg') }}" alt="">
                                    </div>
                                    <p>zigZapgo has truly transformed the way we manage our customer relationships. Its intuitive interface and powerful features have streamlined our processes, allowing us to focus more on building meaningful connections with our clients. A game-changer for our business!</p>
                                    <h5>Mr. Harish Managing</h5>
                                    <h6>Director (Monark Group)</h6>
                                </div>
                                <div class="item">
                                    <div class="author_img">
                                        <img src="{{ asset('assets/frontend/main/img/new/member_01.jpg') }}" alt="">
                                    </div>
                                    <p>We've tried several CRM solutions, but zigZapgo stands out for its user-friendly design and robust functionality. It's helped us centralize our customer data, automate tasks, and ultimately enhance our overall efficiency. Highly recommended for businesses of all sizes.</p>
                                    <h5>Mr. Varun Roy Managing</h5>
                                    <h6>Director (Maac Chandigarh)</h6>
                                </div>
                                <div class="item">
                                    <div class="author_img">
                                        <img src="{{ asset('assets/frontend/main/img/new/member_01.jpg') }}" alt="">
                                    </div>
                                    <p>zigZapgo has exceeded our expectations in every aspect. From lead management to advanced reporting, this CRM solution offers a comprehensive set of tools that have significantly improved our sales and customer service efforts. An indispensable asset for any business looking to grow.</p>
                                    <h5>Mr. Varesh Khanna</h5>
                                    <h6>Owner (Ronish Bioceuticals)</h6>
                                </div>
                                <div class="item">
                                    <div class="author_img">
                                        <img src="{{ asset('assets/frontend/main/img/new/member_01.jpg') }}" alt="">
                                    </div>
                                    <p>The flexibility of zigZapgo is unparalleled. It seamlessly adapts to our unique business needs, providing customizable features that ensure a perfect fit for our operations. The support team is also top-notch, making the transition smooth and hassle-free.</p>
                                    <h5>Mr. Neeraj Sharda</h5>
                                    <h6>Owner (Spa Group of Companies)</h6>
                                </div>
                                <div class="item">
                                    <div class="author_img">
                                        <img src="{{ asset('assets/frontend/main/img/new/member_01.jpg') }}" alt="">
                                    </div>
                                    <p>We've been using zigZapgo for several months now, and the impact on our productivity is remarkable. The automation features have saved us countless hours, and the real-time analytics have given us valuable insights into our customer interactions. A must-have for any company serious about customer relationship management.</p>
                                    <h5>Mr. Chetan Kohli</h5>
                                    <h6>Managing Partner (Medsyn Lab Biotech)</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @endsection
