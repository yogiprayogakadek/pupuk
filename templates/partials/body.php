<body class="text-left">
    <!-- Pre Loader Strat  -->
    <div class="loadscreen" id="preloader">
        <div class="loader spinner-bubble spinner-bubble-primary"></div>
    </div>
    <div class="app-admin-wrap layout-sidebar-large clearfix">
        <div class="main-header">
            <div class="logo">
                <a href="/">
                    <!-- <img src="{{asset('assets/images/logo.png')}}" alt="" /> -->
                </a>
            </div>

            <div class="menu-toggle">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div style="margin: auto"></div>

            <div class="header-part-right">
                <!-- User avatar dropdown -->
                <div class="dropdown">
                    <div class="user col align-self-end">
                        Welcome, <strong>Yogi</strong>!
                        <!-- <img src="{{asset('assets/uploads/users/default.png')}}" id="userDropdown" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" /> -->

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                            <div class="dropdown-header">
                                <i class="i-Lock-User mr-1"></i> Yogi
                            </div>
                            <a class="dropdown-item">Account settings</a>
                            <a class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" href="javascript::void(0);">Sign
                                out</a>
                            <!-- <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="side-content-wrap">
            <div class="sidebar-left open rtl-ps-none" data-perfect-scrollbar data-suppress-scroll-x="true">
                <div class="navigation-left">
                    <li class="nav-item active">
                        <a class="nav-item-hold" href="javascript::void(0);">
                            <i class="nav-icon i-Bar-Chart"></i>
                            <span class="nav-text">Dashoard</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                </div>
            </div>

            <div class="sidebar-overlay"></div>
        </div>