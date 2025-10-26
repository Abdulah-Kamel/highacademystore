<style>
    .sidebar {
        position: fixed;
        top: 0;
        right: -350px;
        width: 350px;
        bottom: 0;
        height: 100%;
        background-color: #343a40;
        transition: right 0.3s ease-in-out;
        /* padding-top: 20px; */
        z-index: 1050;
        overflow-y: auto;
    }

    .sidebar.show {
        right: 0;
    }

    .nav-item a {
        color: white !important;
    }

    .sidebar .close-btn {
        position: absolute;
        top: 10px;
        left: 10px;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #000;
        cursor: pointer;
        background-color: white;
        border-radius: 50%;
        padding: 5px 10px 5px 10px;
    }

    .sidebar .nav-link {
        display: block;
        padding: 10px;
        border-radius: 8px;
        /* Rounded corners */
        transition: background-color 0.3s ease-in-out;
    }

    .sidebar .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.2);
        /* Light grayish overlay */
    }

    .search-mobile {
        display: none;
    }

    .main-nav {
        justify-content: space-between;
    }

    @media screen and (max-width: 500px) {
        .logo-text {
            font-size: 4.5vw;
        }

        .main-nav {
            justify-content: center;
        }

        .search-mobile {
            display: block;
        }

        .sidebar {
            right: -250px;
            width: 250px;
        }
    }

    .bg-blue {
        background-color: #578FCA;
    }

    .border-blue {
        border-color: #578FCA;
    }

    .avatar {
        height: 100px;
        width: 100px;
        object-fit: cover;
        border: 3px solid white;
    }


    .logo-container {
        background-color: rgba(255, 255, 255, 0.2);
        display: block;
        padding: 10px;
        /* border-radius: 8px; */
        transition: background-color 0.3s ease-in-out;
    }

    .nav-item {
        cursor: pointer;
    }
      .bg-warning,
        .btn-primary {
            background-color: #e99239 !important;
        }

        .text-primary,
        text-warning {
            color: #e99239 !important;
        }

        .btn-primary {
            border: none;
        }
</style>
@php
    $lastSegment = request()->segment(count(request()->segments()));
@endphp

<nav class="navbar bg-dark navbar-dark p-0 py-3  fixed-top shadow-md">
    <div class="container-fluid px-2 justify-content-center">
        <div class="row align-items-center w-100">
            <div class="col-12 d-flex main-nav">
                <a class="navbar-brand me-auto" href="/">
                    <span class="text-uppercase text-dark bg-light px-1 logo-text">High</span>
                    <span class="text-uppercase text-light bg-warning px-1 ml-n1 logo-text">Academy Store</span>
                </a>
                <div class="d-flex g-2">
                    <a href="{{ route('user.cart') }}" class="btn px-0 mr-1">
                        <i class="fas fa-shopping-cart text-warning"></i>
                        <span class="badge text-white border border-white rounded-circle"
                            style="padding-bottom: 2px;">{{ Cart::instance('shopping')->count() }}</span>
                    </a>
                    <button type="button" class="navbar-toggler ms-auto" id="sidebarToggle">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </div>
            <div class="col-12 search">
                @if ($lastSegment == 'ar')
                    <div class="ms-auto search-mobile mt-2">
                        <form id="searchId" action="{{ route('user.shop') }}" method="GET" class="mb-0">
                            <div class="input-group">
                                <span class="border-blue input-group-text bg-blue text-white"><i
                                        class="fa-solid fa-magnifying-glass"></i></span>
                                <input name="title" type="text" class="form-control border-blue"
                                    style="color:#7a7a7a" placeholder="ابحث عن اسم الكتاب"
                                    value="{{ request('title') }}">
                                <button type="submit" class="btn bg-blue text-white">Search</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</nav>

<div class="sidebar" id="sidebar">
    <button class="close-btn" id="closeSidebar">
        <i class="fa fa-times"></i>
    </button>
    @auth
        @auth
            <div class="w-100 mb-3 text-center logo-container">
                <a href="{{ route('user.myaccount') }}">
                    <img src="{{ Auth()->user()->profile_image ? asset('storage/images/user/' . Auth()->user()->profile_image) : asset('storage/images/pngegg.png') }}"
                        alt="User Profile" class="rounded-circle avatar">
                </a>
                <a href="{{ route('user.myaccount') }}" class="d-block mt-2 text-white text-decoration-none">
                    {{ Auth()->user()->name }}
                </a>
            </div>
        @endauth


    @endauth
    @guest
        <div class="w-100 mb-3 text-center logo-container">
            <img src="{{ asset('storage/images/pngegg.png') }}" alt="Logo" class="rounded-circle avatar">
            {{-- <i class="fa-solid fa-caret-down text-white"></i> --}}
        </div>
    @endguest
    <ul class="navbar-nav px-3 text-white">


        <li class="px-3 mb-2 d-lg-block d-none">
            @if ($lastSegment == 'ar')
                <form action="{{ route('user.shop') }}" method="GET" class="mb-0">
                    <div class="input-group">
                        <span class="border-blue input-group-text bg-blue text-white"><i
                                class="fa-solid fa-magnifying-glass"></i></span>
                        <input name="title" type="text" class="form-control border-blue" style="color:#7a7a7a"
                            placeholder="ابحث عن اسم الكتاب" value="{{ request('title') }}">
                        <button type="submit" class="btn bg-blue text-white">Search</button>
                </form>
</div>
@endif
</li>
<li class=" nav-item">
    <hr class="divider my-1">
</li>
<li class="nav-item d-flex align-items-center justify-content-center nav-link text-white"
    onclick="location.href='{{ route('user.home') }}'" style="cursor: pointer;">
    <a href="{{ route('user.home') }}" class="text-decoration-none">الرئيسية</a>
    <i class="fa-solid fa-house ms-1"></i>
</li>
<li class="nav-item">
    <hr class="divider my-1">
</li>
<li class="nav-item d-flex align-items-center justify-content-center nav-link text-white"
    onclick="location.href='{{ route('user.shop') }}'" style="cursor: pointer;">
    <a href="{{ route('user.shop') }}" class="text-decoration-none">المتجر</a>
    <i class="fa-solid fa-store ms-1"></i>
</li>

@auth
    <li class="nav-item">
        <hr class="divider my-1">
    </li>
    <li class="nav-item d-flex align-items-center justify-content-center nav-link text-white"
        onclick="location.href='{{ route('user.orders.user') }}'" style="cursor: pointer;">
        <a href="{{ route('user.orders.user') }}"
            class="text-decoration-none @if ($lastSegment == 'myorders') active @endif">طلباتي</a>
        <i class="fa-solid fa-box ms-1"></i>
    </li>
    <li class="nav-item">
        <hr class="divider my-1">
    </li>
    <li class="nav-item d-flex align-items-center justify-content-center nav-link text-white"
        onclick="location.href='{{ route('user.vochers.user') }}'" style="cursor: pointer;">
        <a href="{{ route('user.vochers.user') }}"
            class="text-decoration-none @if ($lastSegment == 'myvouchers') active @endif">أكوادي</a>
        <i class="fa-solid fa-gift ms-1"></i>
    </li>
    <li class="nav-item">
        <hr class="divider my-1">
    </li>
    <li class="nav-item d-flex align-items-center justify-content-center nav-link text-white"
        onclick="location.href='{{ route('user.shipping') }}'" style="cursor: pointer;">
        <a href="{{ route('user.shipping') }}"
            class="text-decoration-none @if ($lastSegment == 'shipping') active @endif">عناوين استلام شحنتك</a>
        <i class="fa-solid fa-truck-fast ms-1"></i>
    </li>
@endauth
<li class="nav-item">
    <hr class="divider my-1">
</li>
<li class="nav-item d-flex align-items-center justify-content-center nav-link text-white"
    onclick="location.href='{{ route('user.fqa') }}'" style="cursor: pointer;">
    <a href="{{ route('user.fqa') }}" class="text-decoration-none">الأسئلة الشائعة</a>
    <i class="fa-solid fa-question-circle ms-1"></i>
</li>
<li class="nav-item">
    <hr class="divider my-1">
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle text-white d-flex align-items-center justify-content-center" href="#" id="contactDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        تواصل معنا
        <i class="fa-solid fa-envelope ms-1"></i>
    </a>
    <ul class="dropdown-menu bg-dark" aria-labelledby="contactDropdown">
        <li>
            <a class="dropdown-item" href="https://www.facebook.com/highacademy2" target="_blank">
                <i class="fab fa-facebook-f me-2"></i>فيسبوك
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="https://wa.me/201550234324" target="_blank">
                <i class="fab fa-whatsapp me-2"></i>واتساب
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="https://www.whatsapp.com/channel/0029VbAlwWH8fewxAkAdCZ23" target="_blank">
                <i class="fab fa-whatsapp me-2"></i>قناة واتساب
            </a>
        </li>
    </ul>
</li>

@auth
    <li class="nav-item">
        <hr class="divider my-1">
    </li>
    <li class="nav-item d-flex align-items-center justify-content-center nav-link text-white mb-2"
        onclick="location.href='{{ route('user.logout') }}'" style="cursor: pointer;">
        <a href="{{ route('user.logout') }}" class="text-decoration-none">تسجيل خروج</a>
        <i class="fa-solid fa-sign-out-alt ms-1"></i>
    </li>
@else
    <li class="nav-item">
        <hr class="divider my-1">
    </li>
    <li class="nav-item d-flex align-items-center justify-content-center nav-link text-white"
        onclick="location.href='{{ route('user.login.user') }}'" style="cursor: pointer;">
        <a href="{{ route('user.login.user') }}" class="text-decoration-none">تسجيل دخول</a>
        <i class="fa-solid fa-sign-in-alt ms-1"></i>
    </li>
@endauth


</ul>
</div>
<script>
    const sidebar = document.getElementById("sidebar");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const closeSidebar = document.getElementById("closeSidebar");

    // Toggle sidebar when clicking the button
    sidebarToggle.addEventListener("click", function(event) {
        sidebar.classList.toggle("show"); // Toggle class instead of always adding
        event.stopPropagation(); // Prevent click from reaching the document
    });

    // Close sidebar when clicking the close button
    closeSidebar.addEventListener("click", function() {
        sidebar.classList.remove("show");
    });

    // Close sidebar when clicking outside of it
    document.addEventListener("click", function(event) {
        if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
            sidebar.classList.remove("show");
        }
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function handleScroll() {
            if ($(window).width() <= 992) { // Target tablets (<= 992px) and phones
                if ($(window).scrollTop() > 200) {
                    $("#searchId").fadeOut();
                    $('.search').hide();
                } else {
                    $('.search').show();
                    $("#searchId").fadeIn();
                }
            } else {
                 $('.search').show();
                $("#searchId").show(); // Ensure it's visible on larger screens
            }
        }

        $(window).on("scroll resize", handleScroll);
    });
</script>
