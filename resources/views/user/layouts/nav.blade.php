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
</style>
<div class="container-fluid bg-dark mb-30">
    <div class="row px-xl-5">
        <div class="col-lg-3 d-none d-lg-block">
            <a class="btn d-flex align-items-center justify-content-between bg-warning w-100 m-2" data-toggle="collapse"
                href="#navbar-vertical" style="height: 40px; padding: 0 10px;">
                <h6 class="text-dark m-0"><i class="fa fa-bars mr-2"></i>المواد</h6>
                <i class="fa fa-angle-down text-dark"></i>
            </a>
            <nav class="collapse position-absolute navbar navbar-vertical navbar-light align-items-start p-0 bg-light"
                id="navbar-vertical" style="width: calc(100% - 30px); z-index: 999;">
                <div class="navbar-nav w-100">
                    @foreach ($categories as $item)
                    <a href="" class="nav-item nav-link"> {{ $item->title }} </a>
                    @endforeach
                </div>
            </nav>
        </div>
        <div class="col-lg-9">
            <nav class="navbar navbar-expand-lg bg-dark navbar-dark py-3 py-lg-0 px-0">
                <a href="" class="text-decoration-none d-block d-lg-none">
                    <span class="h1 text-uppercase text-dark bg-light px-1"
                        style="font-size: 21px; padding-top: 5px;padding-bottom: 5px;">High</span>
                    <span class="h1 text-uppercase text-light bg-warning px-1 ml-n1"
                        style="font-size: 21px; padding-top: 5px;padding-bottom: 5px;">Academey Store</span>
                </a>
              
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>


                <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                    <div class="navbar-nav m-auto py-0">
                        <a href="{{ route('user.home') }}" class="nav-item nav-link active">الرئيسية </a>
                        <a href="{{ route('user.shop') }}" class="nav-item nav-link">المتجر</a>
                        <a href="https://egyptpost.gov.eg/ar-eg//Home/EServices/Track-And-Trace"
                            class="nav-item nav-link">تتبع شحنتك </a>
                        {{-- <a href="detail.html" class="nav-item nav-link">تفاصيل الدفع </a> --}}

                        <a href="{{ route('user.contact') }}" class="nav-item nav-link">تواصل معنا </a>

                   
                    </div>
                    <div class="navbar-nav ml-auto d-lg-flex align-items-center justify-content-end">
                        @auth
                        <a href="{{ route('user.orders.user') }}" class="nav-item nav-link">طلبياتي</a>
                        {{-- <a href="{{ route('user.card') }}" class="nav-item nav-link">سلة المشتريات </a> --}}
                        <a href="{{ route('user.logout') }}" class="nav-item nav-link"> تسجيل خروج </a>
                        @else
                        <a href="{{ route('user.login.user') }}" class="nav-item nav-link"> <i
                                class="bi bi-box-arrow-in-left"></i> تسجيل دخول </a>
                        @endauth
                    </div>
                    <div class="navbar-nav ml-auto   d-lg-block">
                        @auth
                        <a href="{{ route('user.cart') }}" class="btn px-0 ml-3">
                            <i class="fas fa-shopping-cart text-warning"></i>
                            <span class="badge text-secondary border border-secondary rounded-circle"
                                style="padding-bottom: 2px;">{{Cart::instance('shopping')->count()}}</span>
                        </a>
                        @endauth
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>
