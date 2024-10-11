@extends('user.layouts.master')

@section('title')
online shop
@endsection

@section('content')
<!-- Shop Detail Start -->
<div class="container-fluid pb-5">
    <div class="row px-xl-5">
        <div class="col-lg-5 mb-30">
            <div id="product-carousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner bg-light">
                    <div class="carousel-item active">
                        <img class="w-100 h-100" src="{{ $product->image_path }}" alt="Image">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 h-auto mb-30" style="text-align:right">
            <div class="h-100 bg-light p-30">
                <h3>{{ $product->name }}</h3>

                <h3 class="font-weight-semi-bold mb-4"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="currentColor" class="bi bi-cash-coin text-xl-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8m5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0" />
                        <path
                            d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195z" />
                        <path
                            d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083q.088-.517.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1z" />
                        <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 6 6 0 0 1 3.13-1.567" />
                    </svg> <span> {{ $product->price }} </span> </h3>
                <p class="mb-4">{{ $product->description }}
                </p>

                <p class="mb-4">اسم المدرس:{{ $product->brands->title }}
                </p>
                <p class="mb-4">اسم الماده:{{ $product->category->title }}
                </p>
                <div class="d-flex justify-content-end pt-2">
                    @if ($product->state == 0)
                    <h4 class="text-danger">عذرا الكتاب غير متوفر حاليا</h4>
                    @else
                    @auth
                    <a class="add_to_cart btn btn-success overflow-hidden" id="add_to_cart{{ $product->id}}"
                        data-quantity="1" data-product-id="{{ $product->id}}">
                        اضافة الى السلة</a>
                    @else
                    <a href="{{ route('user.login.user') }}" class="nav-item nav-link btn btn-success"> تسجيل دخول </a>
                    @endauth
                    @endif
                    <!--<a href="{{ route('user.card') }}" class="btn btn-dark" style="margin-left:80px ">أذهب اللي-->
                    <!--    صفحه الدفع </a>-->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Shop Detail End -->
@endsection