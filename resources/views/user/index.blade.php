@extends('user.layouts.master')

@section('title')
High Academy Store
@endsection

@section('content')
    <!-- Carousel Start -->
    <div class="container-fluid mb-3">
        <div class="row px-xl-5">
            <div class="col-lg-8  col-sm-8">
                <div id="header-carousel" class="carousel slide carousel-fade mb-30 mb-lg-0" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#header-carousel" data-slide-to="0" class="active"></li>
                        <li data-target="#header-carousel" data-slide-to="1"></li>
                        <li data-target="#header-carousel" data-slide-to="2"></li>
                    </ol>
                    <div class="carousel-inner">
                        <div class="carousel-item position-relative active" style="height: 350px;">
                            <img class="position-absolute w-100 h-100" src="{{ asset('/front') }}/img/carousel-1.jpg"
                                style="object-fit: cover;">
                            <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                                <div class="p-3" style="max-width: 700px;">
                                    <h1 class="display-4 text-white mb-3 animate__animated animate__fadeInDown">الصف
                                        الاول الثانوي </h1>
                                    <p class="mx-md-5 px-5 animate__animated animate__bounceIn">جميع الكتب والمذكرات
                                        الخاصه بالصف الاول الثانوي </p>
                                    <a class="btn btn-outline-light py-2 px-4 mt-3 animate__animated animate__fadeInUp"
                                        href="{{ route('user.shop') }}"> جميع الكتب </a>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item position-relative" style="height: 350px;">
                            <img class="position-absolute w-100 h-100" src="{{ asset('/front') }}/img/carousel-2.jpg"
                                style="object-fit: cover;">
                            <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                                <div class="p-3" style="max-width: 700px;">
                                    <h1 class="display-4 text-white mb-3 animate__animated animate__fadeInDown"> الصف
                                        الثاني الثانوي </h1>
                                    <p class="mx-md-5 px-5 animate__animated animate__bounceIn">جميع الكتب والمذكرات
                                        الخاصه بالصف الثاني الثانوي </p>
                                    <a class="btn btn-outline-light py-2 px-4 mt-3 animate__animated animate__fadeInUp"
                                        href="{{ route('user.shop') }}"> جميع الكتب </a>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item position-relative" style="height: 350px;">
                            <img class="position-absolute w-100 h-100" src="{{ asset('/front') }}/img/carousel-3.jpg"
                                style="object-fit: cover;">
                            <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                                <div class="p-3" style="max-width: 700px;">
                                    <h1 class="display-4 text-white mb-3 animate__animated animate__fadeInDown">الصف
                                        الثالث الثانوي </h1>
                                    <p class="mx-md-5 px-5 animate__animated animate__bounceIn"> جميع الكتب والمذكرات
                                        الخاصه بالصف الثالث الثانوي</p>
                                    <a class="btn btn-outline-light py-2 px-4 mt-3 animate__animated animate__fadeInUp"
                                        href="{{ route('user.shop') }}">جميع الكتب </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12" style="display:inline">
                <div class="product-offer mb-30" style="height: 159px;">
                    <img class="img-fluid" src="{{ asset('/front') }}/img/offer-1.jpg" alt="">
                    <div class="offer-text">
                        <h6 class="text-white text-uppercase"> 20% وفر </h6>
                        <h3 class="text-white mb-3">أكواد المدرسين</h3>
                        <a href="{{ route('user.shop') }}" class="btn btn-primary">أشتري الان </a>
                    </div>
                </div>
                <div class="product-offer mb-30" style="height: 159px;">
                    <img class="img-fluid" src="{{ asset('/front') }}/img/offer-2.jpg" alt="">
                    <div class="offer-text">
                        <h6 class="text-white text-uppercase">عروض حصرية</h6>
                        <h3 class="text-white mb-3">مذكراتنا الحصريه </h3>
                        <a href="{{ route('user.shop') }}" class="btn btn-primary"> أشتري الان </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->

    <!-- new book  Start -->
    <div class="container-fluid pt-5 pb-3">
        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-5"><span class="bg-secondary pr-3">الكتب
                المضافه حديثا </span></h2>
        <div class="row px-xl-5">
            @include('user.layouts.product')
        </div>
    </div>
    <!-- new book End -->

    <!-- Categories Start -->
    <div class="container-fluid pt-5">
        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3">كتب
                المدرسين الاونلاين</span></h2>
        <div class="row px-xl-5 pb-3">
            @foreach ($teachers as $item)
                <div class="col-lg-3 col-md-4 col-sm-6 pb-1">
                    <a class="text-decoration-none" href="{{ route('user.shop') }}">
                        <div class="cat-item d-flex align-items-center mb-4">
                            <div class="overflow-hidden" style="width: 100px; height: 100px;">
                                <img class="img-fluid" src="{{ $item->image_path }}" alt="">
                            </div>
                            <div class="flex-fill pl-3">
                                <h6> {{ $item->title }} </h6>
                                <small class="text-body">{{ $item->description }} </small>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach

        </div>
    </div>
    <!-- Categories End -->

    <!-- Products Start -->
    <div class="container-fluid pt-5 pb-3">
        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3">جميع
                الكتب داخل متجرنا</span></h2>
        <div class="row px-xl-5">
            @include('user.layouts.product')
        </div>
    </div>
    <!-- Products End -->

    <!-- Offer Start -->
    <div class="container-fluid pt-5 pb-3">
        <div class="row px-xl-5">
            <div class="col-md-6">
                <div class="product-offer mb-30" style="height: 300px;">
                    <img class="img-fluid" src="{{ asset('/front') }}/img/offer-1.jpg" alt="">
                    <div class="offer-text">
                        <h6 class="text-white text-uppercase">وفر 20 جنيها </h6>
                        <h3 class="text-white mb-3">عرض خاص </h3>
                        <a href="" class="btn btn-primary">أشتري الان </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="product-offer mb-30" style="height: 300px;">
                    <img class="img-fluid" src="{{ asset('/front') }}/img/offer-2.jpg" alt="">
                    <div class="offer-text">
                        <h6 class="text-white text-uppercase">أكسترا توفير </h6>
                        <h3 class="text-white mb-3">عرض خاص جديد </h3>
                        <a href="" class="btn btn-primary">أشتري الان </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Offer End -->

    <!-- Featured Start -->
    <div class="container-fluid pt-5">
        <div class="row px-xl-5 pb-3">
            <div class="col-lg-3 col-md-6 col-sm-12 pb-1 right">
                <div class="d-flex align-items-center bg-light mb-4" style="padding: 30px;">
                    <h1 class="fa fa-check text-primary m-0 mr-3"></h1>
                    <h5 class="font-weight-semi-bold m-0 "> جميع كتب الثانويه العامه</h5>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 pb-1 right">
                <div class="d-flex align-items-center bg-light mb-4" style="padding: 30px;">
                    <h1 class="fa fa-shipping-fast text-primary m-0 mr-2"></h1>
                    <h5 class="font-weight-semi-bold m-0 ">ًالشحن لجميع المحافظات</h5>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 pb-1 right">
                <div class="d-flex align-items-center bg-light mb-4" style="padding: 30px;">
                    <h1 class="fas fa-exchange-alt text-primary m-0 mr-3"></h1>
                    <h5 class="font-weight-semi-bold m-0 ">أسترجاع لمدة 14 يومًا</h5>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 pb-1 right">
                <div class="d-flex align-items-center bg-light mb-4" style="padding: 30px;">
                    <h1 class="fa fa-phone-volume text-primary m-0 mr-3"></h1>
                    <h5 class="font-weight-semi-bold m-0 ">دعم كامل علي مدار اليوم </h5>
                </div>
            </div>
        </div>
    </div>
    <!-- Featured End -->
    <!-- Vendor Start -->
    <div class="container-fluid py-5">
        <div class="row px-xl-5">
            <div class="col">
                <div class="owl-carousel vendor-carousel">
                    <div class="bg-light p-4">
                        <img src="{{ asset('/front') }}/img/vendor-1.jpg" alt="">
                    </div>
                    <div class="bg-light p-4">
                        <img src="{{ asset('/front') }}/img/vendor-2.jpg" alt="">
                    </div>
                    <div class="bg-light p-4">
                        <img src="{{ asset('/front') }}/img/vendor-3.jpg" alt="">
                    </div>
                    <div class="bg-light p-4">
                        <img src="{{ asset('/front') }}/img/vendor-4.jpg" alt="">
                    </div>
                    <div class="bg-light p-4">
                        <img src="{{ asset('/front') }}/img/vendor-5.jpg" alt="">
                    </div>
                    <div class="bg-light p-4">
                        <img src="{{ asset('/front') }}/img/vendor-6.jpg" alt="">
                    </div>
                    <div class="bg-light p-4">
                        <img src="{{ asset('/front') }}/img/vendor-7.jpg" alt="">
                    </div>
                    <div class="bg-light p-4">
                        <img src="{{ asset('/front') }}/img/vendor-8.jpg" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Vendor End -->
@endsection
