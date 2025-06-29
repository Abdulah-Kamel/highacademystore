@extends('user.layouts.master')

@section('title')
    {{ $product->name }}
@endsection
<!--@section('description', $product->description)-->
<?php
$sliders = $product->sliders->title ?? 'غير محدد';
$category = $product->category->title ?? 'غير محدد';
$brands = $product->brands->title ?? 'غير محدد';
?>
@section('keywords',
    'اسم الكتاب: ' .
    $product->name .
    ' ' .
    'الصف الدراسي: ' .
    $sliders.
    ' ' .
    'اسم
    المادة: ' .
    $category .
    ' ' .
    'كتاب ' .
    $brands .
    ' ' .
    'السعر: ' .
    $product->price)

@section('book-image', $product->image_path)
<style>
    .color-option {
        display: inline-block;
        padding: 10px 15px;
        border: 2px solid #ccc;
        border-radius: 8px;
        margin: 5px;
        cursor: pointer;
        font-weight: bold;
        text-align: center;
        transition: all 0.3s ease;
    }

    .color-option.active {
        border-color: #090909;
        background-color: #16b63b;
        color: white;
    }

    .color-option input {
        display: none;
    }

    .size-option {
        display: inline-block;
        padding: 10px 20px;
        border: 2px solid #090909;
        border-radius: 8px;
        margin: 5px;
        cursor: pointer;
        font-weight: bold;
        color: #33373d;
        background-color: white;
        transition: all 0.3s ease;
    }

    .size-option.active {
        background-color: #16b63b;
        color: white;
    }

    .size-option input {
        display: none;
    }

    .color-option,
    .size-option {
        margin-inline-end: 0 !important;
    }
</style>
@php
    if (!function_exists('getOfferPrice')) {
        function getOfferPrice($product)
        {
            if ($product->have_offer == 1) {
                if ($product->offer_type == 'percentage') {
                    return $product->price - ($product->price * $product->offer_value) / 100;
                } else {
                    return $product->price - $product->offer_value;
                }
            }
        }
    }
@endphp
@section('content')
    <!-- Shop Detail Start -->
    <section class="container-fluid py-5">
        <section class="row px-xl-5 py-5">
            <section class="col-lg-5 mb-30">
                <section id="product-carousel" class="carousel slide" data-ride="carousel">
                    <!-- Carousel inner with images -->
                    <section class="carousel-inner bg-light">
                        <section class="carousel-item active">
                            <img class="w-100 h-100" src="{{ $product->image_path }}" alt="product picture">
                        </section>
                        @foreach ($product->images as $image)
                            <section class="carousel-item">
                                <img class="w-100 h-100" src="{{ asset('storage/' . $image->image_path) }}"
                                     alt="slider Image">
                            </section>
                        @endforeach
                    </section>

                    <!-- Thumbnails -->
                    <section class="carousel-thumbnails d-flex justify-content-center mt-4">
                        <img src="{{ $product->image_path }}" data-bs-target="#product-carousel" data-bs-slide-to="0"
                             class="active" width="100" height="100" alt="Main Thumb">
                        @foreach ($product->images as $index => $image)
                            <img src="{{ asset('storage/' . $image->image_path) }}" data-bs-target="#product-carousel"
                                 data-bs-slide-to="{{ $index + 1 }}" width="100" height="100"
                                 alt="Thumb {{ $index + 1 }}">
                        @endforeach
                    </section>
                </section>
            </section>

            <section class="col-lg-7 h-auto mb-30" style="text-align:right">
                <section class="h-100 bg-light p-30">
                    <h2 class="fw-bold h2 text-end mb-4">{{ $product->name }}</h2>

                    <p class="font-weight-semi-bold mb-4 h3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                             height="16" fill="currentColor" class="bi bi-cash-coin text-xl-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                  d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8m5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0"/>
                            <path
                                d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195z"/>
                            <path
                                d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083q.088-.517.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1z"/>
                            <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 6 6 0 0 1 3.13-1.567"/>
                        </svg>
                        <span> السعر:
                            <span
                                class="{{ $product->have_offer == 1 ? 'text-decoration-line-through' : '' }}">{{ $product->price }}</span>
                            {{ getOfferPrice($product) }} جنية </span></p>
                    <p class="mb-4">{{ $product->description }}
                    </p>

                    <p class="mb-4">اسم المدرس:{{ $product->brands->title ?? 'غير محدد' }}
                    </p>
                    <p class="mb-4">اسم الماده:{{ $product->category->title ?? 'غير محدد' }}
                    </p>
                    <p class="mb-4">الصف الدراسي :{{ $product->sliders->title ?? 'غير محدد' }}
                    </p>
                    <section class="d-flex flex-column align-items-end pt-3">
                        @if ($product->state == 0 || $product->quantity == 0)
                            <h4 class="text-danger fw-bold">❌ عذراً، الكتاب غير متوفر حالياً</h4>
                        @else
                            @auth
                                <div class="d-flex flex-column gap-3 w-100">
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        {{-- الألوان المتاحة --}}
                                        @if(!empty($product->colors))
                                            <div class="d-flex flex-wrap" id="colorChoices">
                                                @foreach($product->colors as $color)
                                                    <label class="color-option mb-0">
                                                        <input type="radio" name="selected_color{{$product->id}}"
                                                               value="{{ $color }}">
                                                        {{ $color }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        {{-- الأحجام المتاحة --}}
                                        @if(!empty($product->sizes))
                                            <div class="d-flex flex-wrap" id="sizeChoices">
                                                @foreach ($product->sizes as $size)
                                                    <label class="size-option mb-0">
                                                        <input type="radio" name="selected_size{{$product->id}}"
                                                               value="{{ $size }}">
                                                        {{ $size }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    {{-- الكمية --}}
                                    @if ($product->max_qty_for_order<=10)
                                    <div>
                                        <p class="text-black fw-bold">باقي {{$product->max_qty_for_order}} وحدات في المخزون</p>
                                    </div>
                                    @endif
                                    <div class="d-flex align-items-center justify-content-end gap-3">
                                        <!-- زر الإضافة إلى السلة -->
                                        <a class="add_to_cart btn btn-success fw-bold px-2 sm:px-4 py-2"
                                           id="add_to_cart{{ $product->id }}"
                                           data-quantity="1"
                                           data-product-id="{{ $product->id }}">
                                            🛒 إضافة إلى السلة
                                        </a>
                                        <!-- حقل تحديد الكمية -->
                                            <input type="number" name="quantity" id="quantity{{ $product->id }}"
                                                   data-max-quantity="{{$product->max_qty_for_order}}"
                                                   placeholder="الكمية" max="{{$product->max_qty_for_order}}"
                                                   class="form-control px-3 text-center border-primary fw-bold"
                                                   dir="rtl" min="1" style="max-width:150px">


                                    </div>


                                </div>

                            @else
                                <!-- زر تسجيل الدخول -->
                                <a href="{{ route('user.login.user') }}" class="btn btn-success fw-bold px-4 py-2">🔑
                                    تسجيل دخول</a>
                            @endauth
                        @endif
                    </section>

                </section>
            </section>
        </section>
    </section>
    <!-- Shop Detail End -->
    <script>
        document.querySelectorAll('.carousel-thumbnails img').forEach(function (thumbnail, index) {
            thumbnail.addEventListener('click', function () {
                document.querySelector('.carousel-thumbnails img.active').classList.remove('active');
                this.classList.add('active');
            });
        });

        // Update active thumbnail when carousel slide changes
        const product_carousel = document.querySelector('#product-carousel');
        product_carousel.addEventListener('slid.bs.carousel', function (e) {
            const active_index = e.to;
            document.querySelector('.carousel-thumbnails img.active').classList.remove('active');
            document.querySelector(`.carousel-thumbnails img[data-bs-slide-to="${active_index}"]`).classList.add(
                'active');
        });

        setInterval(function () {
            $('#quantity{{ $product->id }}').on('change', function () {
                $('#add_to_cart{{ $product->id }}').data('quantity', $(this).val());
            });
        }, 1000)
    </script>
    <script>
        document.querySelectorAll('#colorChoices .color-option input').forEach(input => {
            input.addEventListener('change', function () {
                var color_value = $(this).val();
                document.querySelectorAll('#colorChoices .color-option').forEach(label => {
                    label.classList.remove('active');
                });
                this.closest('.color-option').classList.add('active');
            });
        });
    </script>
    <script>
        document.querySelectorAll('#sizeChoices .size-option input').forEach(input => {
            input.addEventListener('change', function () {
                var size_value = $(this).val();
                document.querySelectorAll('#sizeChoices .size-option').forEach(label => {
                    label.classList.remove('active');
                });
                this.closest('.size-option').classList.add('active');
            });
        });

    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(`input[name="quantity"]`).on('input', function () {
            var maxQty = $(this).data('max-quantity');
            var currentVal = parseInt($(this).val());

            if (currentVal > maxQty) {
                Swal.fire({
                    icon: "info",
                    title: "تنبيه",
                    text: `لا يمكنك طلب أكثر من ${maxQty} من هذا المنتج.`,
                    confirmButtonText: "حسناً",
                    timer: 3000
                });
                // ✅ هنا التعديل الصحيح لقيمة الـ input
                $(this).val(maxQty);
            }
        });


    </script>

@endsection
