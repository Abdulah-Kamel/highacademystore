<style>
    .container {
        font-family: "Cairo", sans-serif !important;
    }

    .bg-warning,
    .btn-primary {
        background-color: #e99239 !important;
    }

    .text-primary,
    .text-warning {
        color: #e99239 !important;
    }

    .btn-primary {
        border: none;
    }

    .logo-text {
        font-size: 4vw;
    }

    .avatar {
        height: 70px;
        width: 80px;
    }

    .dropdown {
        position: relative;
    }

    .dropdown-menu {
        position: absolute !important;
        top: 100%;
        /* Position it below the avatar */
        left: 50%;
        transform: translateX(-50%);
        z-index: 1050;
        /* Ensure it's above the navbar */
        display: none;
        /* Initially hidden */
    }

    .dropdown:hover .dropdown-menu {
        display: block;
        /* Show the dropdown on hover */
    }


    @media screen and (max-width: 500px) {
        .logo-text {
            font-size: 4vw;
        }
    }

    @media screen and (min-width: 768px) {
        .logo-text {
            font-size: 2vw;
        }
    }
</style>
@php
    $lastSegment = request()->segment(count(request()->segments()));
@endphp


<div class="container-fluid bg-dark mb-30 position-fixed top-0" style="z-index: 999;">
    <div class="row px-xl-5 pb-2 py-lg-0 justify-content-center align-items-center">
        <div class="col-lg-8">
            <nav class="navbar navbar-expand-lg  bg-dark navbar-dark py-2 py-lg-0 px-0">
                <a href="/" class="text-decoration-none">
                    <span class="text-uppercase text-dark bg-light px-1 logo-text"
                        style="padding-top: 5px;padding-bottom: 5px;">High</span>
                    <span class="text-uppercase text-light bg-warning px-1 ml-n1 logo-text"
                        style="padding-top: 5px;padding-bottom: 5px;">Academey Store</span>
                </a>
                <div class="navbar-nav d-flex flex-row align-items-center d-lg-none d-block me-3">

                    @auth
                        <a href="{{ route('user.cart') }}" class="btn px-0">
                            <i class="fas fa-shopping-cart text-warning"></i>
                            <span class="badge text-secondary border border-secondary rounded-circle"
                                style="padding-bottom: 2px;">{{ Cart::instance('shopping')->count() }}</span>
                        </a>
                    @endauth

                    @auth
                        <div class="dropdown d-flex">
                            <a class="avatar btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <img src="{{ Auth()->user()->profile_image ? asset('storage/images/user/' . Auth()->user()->profile_image) : asset('storage/images/pngegg.png') }}" alt="Logo"
                                    class="rounded-circle w-100 h-100">
                                <i class="fa-solid fa-caret-down text-white"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li> <a href="{{ route('user.myaccount') }}"
                                        class="dropdown-item @if ($lastSegment == 'myaccount') active @endif">حسابى</a>
                                </li>
                              <li> <a href="{{ route('user.shipping') }}"
                                        class="dropdown-item @if ($lastSegment == 'shipping') active @endif">بيانات الشحن</a>
                                </li>
                                <li> <a href="{{ route('user.orders.user') }}"
                                        class="dropdown-item @if ($lastSegment == 'myorders') active @endif">طلباتي</a>
                                </li>
                                <li> <a href="{{ route('user.vochers.user') }}"
                                        class="dropdown-item @if ($lastSegment == 'myvouchers') active @endif">اكوادي</a>
                                </li>
                                <li> <a href="https://egyptpost.gov.eg/ar-eg//Home/EServices/Track-And-Trace"
                                        class="dropdown-item">تتبع شحنتك </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li> <a href="{{ route('user.logout') }}" class="dropdown-item">تسجيل خروج </a></li>
                            </ul>
                        </div>


                    @endauth
                </div>
                <button type="button" class="navbar-toggler col-12" data-toggle="collapse"
                    data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>


                <div class="collapse navbar-collapse justify-content-center" id="navbarCollapse">
                    <div class="navbar-nav py-0">
                        <a href="{{ route('user.home') }}"
                            class="nav-item nav-link @if ($lastSegment == 'ar') active @endif">الرئيسية </a>
                        <a href="{{ route('user.shop') }}"
                            class="nav-item nav-link @if ($lastSegment == 'shop') active @endif">المتجر</a>

                        {{-- <a href="detail.html" class="nav-item nav-link">تفاصيل الدفع </a> --}}


                        <a href="{{ route('user.fqa') }}" class="nav-item nav-link">الاسئلة الشائعة</a>
                        <a href="{{ route('user.contact') }}" class="nav-item nav-link">تواصل معنا </a>

                        @auth
                        @else
                            <a href="{{ route('user.login.user') }}" class="nav-item nav-link"> <i
                                    class="bi bi-box-arrow-in-left"></i> تسجيل دخول </a>
                        @endauth
                    </div>
                    <div class="navbar-nav d-lg-block">
                        @auth
                            <a href="{{ route('user.cart') }}" class="btn px-0 ml-3">
                                <i class="fas fa-shopping-cart text-warning"></i>
                                <span class="badge text-secondary border border-secondary rounded-circle"
                                    style="padding-bottom: 2px;">{{ Cart::instance('shopping')->count() }}</span>
                            </a>
                        @endauth
                    </div>
                </div>
                <div>

                </div>
            </nav>
        </div>
        <form action="{{ route('user.shop') }}" method="GET" class="col-lg-3 mb-0 position-realtive">
            <div class="row justify-content-center align-items-center">
                <div class="col-12 d-flex justify-content-center align-items-start flex-wrap">
                    <input name="title" class="form-controller btn-lg fs-6 mx-auto rounded-pill search_input"
                        style="border: none; width:100%;outline: none; height:40px;" placeholder="ابحث عن اسم الكتاب"
                        value="{{ request('title') }}">
                    <button type="submit" class="btn-light rounded-pill text-bold position-absolute top-50 end-0"
                        style="border: none; width:50px; height:30px;transform: translate(-50%, -50%);background-color:#578FCA !important;color:white;"><i
                            class="fa-solid fa-magnifying-glass"></i></button>
                </div>

            </div>
        </form>
        <div class="col-1 d-lg-block d-none">
            @auth
                <div class="dropdown">
                    <a class="avatar btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ Auth()->user()->profile_image ? asset('storage/images/user/' . Auth()->user()->profile_image) : asset('storage/images/pngegg.png') }}" alt="Logo"
                            class="rounded-circle w-100 h-100">
                        {{-- <i class="fa-solid fa-caret-down text-white"></i> --}}
                    </a>
                    <ul class="dropdown-menu">
                        <li> <a href="{{ route('user.myaccount') }}"
                            class="dropdown-item @if ($lastSegment == 'myaccount') active @endif">حسابى</a>
                        </li>
                         <li> <a href="{{ route('user.shipping') }}"
                                        class="dropdown-item @if ($lastSegment == 'shipping') active @endif">بيانات الشحن</a>
                                </li>
                        <li> <a href="{{ route('user.orders.user') }}"
                                class="dropdown-item @if ($lastSegment == 'myorders') active @endif">طلباتي</a>
                        </li>
                        <li> <a href="{{ route('user.vochers.user') }}"
                                class="dropdown-item @if ($lastSegment == 'myvouchers') active @endif">اكوادي</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li> <a href="{{ route('user.logout') }}" class="dropdown-item">تسجيل خروج </a></li>
                    </ul>
                </div>

            @endauth
        </div>

    </div>
</div>
