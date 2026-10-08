<header class="header_area">
            <nav class="navbar navbar-expand-lg menu_one menu_four">
                <div class="container">
                    <a class="navbar-brand sticky_logo" href="{{ route('home') }}"><img src="{{ asset('assets/frontend/main/img/logo-white.png') }}" alt="logo" style="height: 43px;"><img src="{{ asset('assets/frontend/main/img/logo-black.png') }}" alt="" style="height: 43px;"></a>
                    <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="menu_toggle">
                            <span class="hamburger">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                            <span class="hamburger-cross">
                                <span></span>
                                <span></span>
                            </span>
                        </span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav menu w_menu pl_100 me-auto ms-auto">
                            <li class="nav-item dropdown submenu mega_menu mega_menu_two">
                                <a class="nav-link dropdown-toggle" href="{{ route('home') }}" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Home
                                </a>
                            </li>
                            <li class="nav-item"><a title="Features" class="nav-link" href="{{ route('frontend.features') }}">Key Features</a></li>
                            <li class="nav-item"><a title="Pricing" class="nav-link" href="{{ route('frontend.pricing') }}">Pricing </a></li>
                            <li class="nav-item"><a title="FAQ" class="nav-link" href="{{ route('frontend.faq') }}">FAQ </a></li>
                            <!-- <li class="nav-item"><a title="Contact  02" class="nav-link" href="/contact">Contact</a></li> -->
                        </ul>
                        <a href="{{ route('login') }}" class="app_btn btn_hover">Sign in <i class="ti-arrow-right"></i></a>
                    </div>
                </div>
            </nav>
        </header>
