@extends('user.layouts.master')
@section('title')
    High Academy Store - الصفحه الرئيسية
@endsection
@section('content')
    <!-- Carousel Start -->
    <div class="container-fluid mb-3 mt-5 pt-5">
        <div class="row px-xl-5 pt-5 g-1 align-items-center">


            <div class="col-lg-6 col-12" style="padding-right: 10px !important;">
              <div id="header-carousel" class="carousel slide carousel-fade mb-30 mb-lg-0" data-ride="carousel" data-interval="2000">

                    <ol class="carousel-indicators">
                        @if (isset($offers) && $offers->count() > 0)
                            @foreach ($offers as $index => $offer)
                                <li data-target="#header-carousel" data-slide-to="{{ $index }}"
                                    class="{{ $index === 0 ? 'active' : '' }}"></li>
                            @endforeach
                        @endif
                    </ol>
                    <div class="carousel-inner">
                        @if (isset($offers) && $offers->count() > 0)
                            @foreach ($offers as $index => $offer)
                                @if (!empty($offer->image_path))
                                    <!-- Ensure the offer has an image -->
                                    <div class="carousel-item d-flex justify-content-center position-relative {{ $index === 0 ? 'active' : '' }}"
                                        style="height: 350px;">
                                        <img class="position-absolute"
                                            src="{{ asset('storage/images/offers/' . $offer->image) }}" alt="carousel image"
                                            style="object-fit: cover;max-width:400px;">
                                        <div class="carousel-caption d-flex flex-column align-items-center justify-content-end mb-4"
                                            style="background-color: rgba(0, 0, 0, 0);">
                                            <div class="p-3" style="max-width: 700px;">
                                                <a class="btn btn-outline-light py-2 px-4 mt-3 animate__animated animate__fadeInUp"
                                                    href="{{ route('user.shop') }}"> جميع الكتب </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="carousel-item position-relative active" style="height: 350px;">
                                <img class="position-absolute w-100 h-100" src="{{ asset('default-carousel-image.jpg') }}"
                                    alt="Default carousel image" style="object-fit: cover;">
                                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                                    <div class="p-3" style="max-width: 700px;">
                                        <h5 class="text-white">No offers available</h5>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <a class="carousel-control-prev" href="#header-carousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#header-carousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>


            <!-- Categories Start -->
            <div class="col-lg-6 col-12" style="padding: 0 10px 0 15px !important;">
                <!--<h2 class="section-title position-relative text-uppercase"><span class="bg-secondary">الاقسام-->
                </span></h2>
                <div class="row justify-content-center align-items-center gx-1 gy-4">
                    @include('user.layouts.categories')
                </div>
            </div>
            <!-- Categories End -->



            <!--        <div class="col-lg-4 col-12" style="display:inline">-->
            <!--            <div class="product-offer mb-30" style="height: 159px;">-->
            <!--                <img class="img-fluid" src="{{ asset('/front') }}/img/offer-1.jpg" alt=" offer image">-->
            <!--                <div class="offer-text">-->
            <!--                    <h6 class="text-white text-uppercase"> 20% وفر </h6>-->
            <!--                    <h3 class="text-white mb-3">أكواد المدرسين</h3>-->
            <!--                    <a href="{{ route('user.evouchers') }}" class="btn btn-primary">أشتري الان </a>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--            <div class="product-offer mb-30" style="height: 159px;">-->
            <!--                <img class="img-fluid" src="{{ asset('/front') }}/img/offer-2.jpg" alt=" offer image">-->
            <!--                <div class="offer-text">-->
            <!--                    <h6 class="text-white text-uppercase">عروض حصرية</h6>-->
            <!--                    <h3 class="text-white mb-3">مذكراتنا الحصريه </h3>-->
            <!--                    <a href="{{ route('user.shop') }}" class="btn btn-primary"> أشتري الان </a>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            <!-- Carousel End -->

            <!-- Categories Start -->
            <!--<div class="container-fluid pt-5 pb-3">-->
            <!--    <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-5"><span class="bg-secondary pr-3">الاقسام-->
            <!--        </span></h2>-->
            <!--    <div class="row justify-content-center align-items-center px-xl-5 g-5">-->
            <!--        @include('user.layouts.categories')-->
            <!--    </div>-->
            <!--</div>-->
            <!-- Categories End -->

            <!-- Most Ordered Products Section -->
            <div class="container-fluid pt-5 pb-3">
                <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-5">
                    <span class="bg-secondary">الأكثر طلبًا</span>
                </h2>
                <div class="row px-xl-5 g-5">
                    @include('user.layouts.product_most_buyed')
                </div>
            </div>
            <!-- Most Ordered Products Section End -->

            <!-- Categories Start -->
            <div class="container-fluid pt-5">
                <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary">كتب
                        المدرسين الاونلاين</span></h2>
                <div class="row px-xl-5 g-2 pb-3">
                    @if (isset($main_categories))
                        @foreach ($teachers as $item)
                            <div class="col-lg-3 col-md-4 col-sm-6 pb-1">
                                <a class="text-decoration-none"
                                    href="{{ route('user.shop') }}?brand_id={{ $item->id }}">
                                    <div class="cat-item d-flex align-items-center mb-4">
                                        <div class="overflow-hidden" style="width: 100px; height: 100px;">
                                            <img class="img-fluid lazy" data-src="{{ $item->image_path }}"
                                                alt=" صوره المدرس {{ $item->name }}">
                                        </div>
                                        <div class="flex-fill pl-3">
                                            <h6> {{ $item->title }} </h6>
                                            <small class="text-body">{{ $item->description }} </small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @else
                        <p>Error: $teachers is missing!</p>
                    @endif


                </div>
            </div>
            <!-- Categories End -->

            <!-- new book  Start -->
            <div class="container-fluid pt-5 pb-3">
                <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-5"><span class="bg-secondary">المنتجات
                        المضافه حديثا </span></h2>
                <div class="row px-xl-5 g-5">
                    @include('user.layouts.product')
                </div>
            </div>
            <!-- new book End -->

            <!-- Offer Start -->
            <div class="container-fluid pt-5 pb-3">
                <div class="row px-xl-5 g-5">
                    <div class="col-md-6">
                        <div class="product-offer mb-30" style="height: 300px;">
                            <img class="img-fluid" src="{{ asset('/front') }}/img/offer-1.jpg" alt=" offer image">
                            <div class="offer-text">
                                <h6 class="text-white text-uppercase">وفر 20 جنيها </h6>
                                <h3 class="text-white mb-3">عرض خاص </h3>
                                <a href="" class="btn btn-primary">أشتري الان </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="product-offer mb-30" style="height: 300px;">
                            <img class="img-fluid" src="{{ asset('/front') }}/img/offer-2.jpg" alt=" offer image">
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
                <div class="row px-xl-5 g-5 pb-3">
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
                <div class="row px-xl-5 g-5">
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
        <style>
            img.lazy {
                filter: blur(10px);
                transition: filter 0.3s;
            }

            img.lazy:not([src]) {
                background-color: #f0f0f0;
                height: 300px;
            }

            .carousel .carousel-indicators li {
                background-color: #000;
                border-radius: 50%
            }

            .carousel .carousel-indicators li.active {
                background-color: #578FCA;
                border-radius: 50%;
                width: 15px;
            }

            /* .carousel .carousel-control-prev {
                background-color: #a7cdf6;
            } */
            .carousel .carousel-control-prev-icon {
                background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23000' viewBox='0 0 8 8'%3E%3Cpath d='M5.25 0l-4 4 4 4 1.5-1.5-2.5-2.5 2.5-2.5-1.5-1.5z'/%3E%3C/svg%3E") !important;
            }

            .carousel .carousel-control-next-icon {
                background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23000' viewBox='0 0 8 8'%3E%3Cpath d='M2.75 0l-1.5 1.5 2.5 2.5-2.5 2.5 1.5 1.5 4-4-4-4z'/%3E%3C/svg%3E") !important;
            }

            .bg-light-blue {
                background-color: #578FCA;
            }
        </style>
        <!-- Lazy Loading Script -->
        <script>
            
            document.addEventListener("DOMContentLoaded", function() {
                const lazyImages = document.querySelectorAll("img.lazy");

                if ("IntersectionObserver" in window) {
                    const observer = new IntersectionObserver((entries, observer) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const img = entry.target;
                                img.src = img.dataset.src;
                                img.classList.remove("lazy");
                                observer.unobserve(img);
                            }
                        });
                    });

                    lazyImages.forEach(img => {
                        observer.observe(img);
                    });
                } else {
                    // Fallback for older browsers
                    lazyImages.forEach(img => {
                        img.src = img.dataset.src;
                        img.classList.remove("lazy");
                    });
                }
            });
        </script>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/683185022dd3111911d08f8d/1is0mv50j';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
<!--End of Tawk.to Script-->