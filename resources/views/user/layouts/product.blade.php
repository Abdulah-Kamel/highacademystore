@foreach ($products as $item)
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
    <div class="col-xxl-5-cols col-xl-3 col-lg-4  col-6 pb-1">

        <div class="product-item bg-light mb-4 position-relative d-flex flex-column justify-content-between rounded-5"
            style="min-height: 100%; border-radius: 20px;">
            {{-- Debug state value --}}
            @if(config('app.debug'))
                <div style="position: absolute; top: 5px; left: 5px; background: red; color: white; padding: 2px 5px; font-size: 10px; z-index: 10;">
                    State: {{ $item->state }}
                </div>
            @endif
            @if ($item->state == 0)
                <div class="ribbon-wrapper">
                    <div class="ribbon">غير متاح</div>
                </div>
            @elseif($item->state == 2)
                <div class="ribbon-wrapper">
                    <div class="ribbon">احجز الان</div>
                </div>
            @elseif($item->state == 3)
                <div class="ribbon-wrapper">
                    <div class="ribbon bg-info">سيتوفر قريبا</div>
                </div>
            @else
            @endif
            <a href="{{ route('user.product.show', $item->id) }}" class="text-decoration-none ">
                <div class="product-img ">
                    <div class="overflow-hidden w-100 position-relative"
                        style="border-top-left-radius: 20px;border-top-right-radius: 20px;">


                        <img class="img-fluid w-100 lazy product-image" data-src="{{ $item->image_path }}"
                            alt="صوره المنتج {{ $item->name }}" />


                        {{-- <div class="product-action">
            @auth
                @if ($item->state == 1 && $item->quantity > 0)
                    <a class="add_to_cart btn btn-outline-dark btn-square overflow-hidden"
                        id="add_to_cart{{ $item->id }}" data-quantity="1" data-product-id="{{ $item->id }}">
                        <i class="fas fa-cart-plus"></i></a>
                @else
                    <a class="btn btn-outline-dark btn-square overflow-hidden"
                        href="{{ route('user.product.show', $item->id) }}">
                        <i class="fas fa-eye"></i></a>
                @endif
            @else
                <a class="btn btn-outline-dark btn-square overflow-hidden"
                    href="{{ route('user.product.show', $item->id) }}">
                    <i class="fas fa-eye"></i></a>
            @endauth
        </div> --}}
                    </div>
                    <div class="mt-3 p-2 text-center fw-bold text-decoration-none lh-base text-center m-0">
                        <p class="font-size fs-sm-5 text-black fw-bold text-decoration-none lh-base text-center m-0">
                            {{ $item->name }}
                        </p>
                        <p class="mt-2 font-size fw-lighter text-black">
                            {{ $item->category ? $item->category->title . ' - ' : '' }}
                            {{ $item->brands ? $item->brands->title . ' - ' : '' }}
                            {{ $item->sliders->title ?? '' }}
                        </p>
                    </div>
                </div>
            </a>

            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                         @if ($item->commit != null)
                            <div class=" rounded-2 bg-danger bg-gradient-danger px-2 py-1">
                                <p class="text-white m-0">
                                    {{ $item->commit }}
                                </p>
                            </div>
                        @endif
                        @if($item->state == 3)
                          <div class="mt-3">
                            <a href="https://www.whatsapp.com/channel/0029VbAlwWH8fewxAkAdCZ23"
                            class= "bg-success text-white px-2 py-1 rounded-2 "
                            target="_blank"
                            rel="noopener noreferrer">
                            <i class="fab fa-whatsapp fa-lg"></i>
                            تابع معانا
                             </a>
                          </div>
                        @endif
                    </div>
                    {{--                    <div class="col-12 text-center mt-3"> --}}
                    {{--                        <div class="d-flex justify-content-center gap-3 flex-wrap"> --}}
                    {{--                            @if (!empty($item->colors)) --}}
                    {{--                                <div> --}}
                    {{--                                    <select id="colorSelect{{ $item->id }}" --}}
                    {{--                                            class="form-select border-primary fw-bold text-center" --}}
                    {{--                                            style="max-width: 200px;" name="color" data-product-id="{{ $item->id }}"> --}}
                    {{--                                        <option value="" disabled selected>اختر اللون</option> --}}
                    {{--                                        @foreach ($item->colors as $color) --}}
                    {{--                                            <option value="{{ $color }}">{{ $color }}</option> --}}
                    {{--                                        @endforeach --}}
                    {{--                                    </select> --}}
                    {{--                                </div> --}}
                    {{--                            @endif --}}

                    {{--                            @if (!empty($item->sizes)) --}}
                    {{--                                <div> --}}
                    {{--                                    <select id="sizeSelect{{ $item->id }}" --}}
                    {{--                                            class="form-select border-primary fw-bold text-center" --}}
                    {{--                                            style="max-width: 200px;" name="color" data-product-id="{{ $item->id }}">> --}}
                    {{--                                        <option value="" disabled selected>اختر الحجم</option> --}}
                    {{--                                        @foreach ($item->sizes as $size) --}}
                    {{--                                            <option value="{{ $size }}">{{ $size }}</option> --}}
                    {{--                                        @endforeach --}}
                    {{--                                    </select> --}}
                    {{--                                </div> --}}
                    {{--                            @endif --}}
                    {{--                        </div> --}}
                    {{--                    </div> --}}
                    {{-- <!--<h6>-->
    <a
        class="h6 fw-bold text-decoration-none lh-base"
        href="{{ route('user.product.show', $item->id) }}"
    >
        {{ $item->name }}
    </a>
    @endif --}}
                    <!--</h6>-->
                </div>
            </div>


            <div class="d-flex flex-column flex-wrap align-items-center justify-content-between px-2 py-4">
                <div class="row gy-4 align-items-center justify-content-center w-100 mt-4 px-sm-4 px-md-0">
                    <div class="col-md-6 col-12">
                        <div class="d-flex flex-column align-items-center align-items-md-start  price ">
                            @if ($item->have_offer == 1)
                                <h5 class="fs-5 fw-bold mb-0">
                                    <span>EGP</span>
                                    <span class="text-primary">
                                        {{ getOfferPrice($item) }}</span>
                                </h5>
                                <div class="position-relative">
                                    <h6 class="text-muted fs-5 mb-0 text-decoration-line-through">
                                        <span>EGP</span>
                                        <span class="text-primary">{{ $item->price }}</span>
                                    </h6>
                                    <!-- Tooltip for price -->
                                    <span class="tooltip-text"
                                        style="
                visibility: hidden;
                background-color: rgba(0, 0, 0, 0.75);
                color: #fff;
                text-align: center;
                border-radius: 5px;
                padding: 5px;
                position: absolute;
                z-index: 1;
                bottom: 120%; /* Position above */
                left: 50%;
                transform: translateX(-50%);
                white-space: nowrap;
                opacity: 0;
                transition: opacity 0.3s ease;
            ">
                                        @if ($item->offer_type == 'percentage')
                                            خصم {{ $item->offer_value }} % لفتره محدودة
                                        @else
                                            خصم {{ $item->offer_value }} جنيه لفتره محدودة
                                        @endif
                                    </span>
                                </div>
                            @else
                                <h5 class="fs-5 fw-bold mb-0">
                                    <span>EGP</span>
                                    <span class="text-primary">
                                        {{ $item->price }}</span>
                                </h5>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        @auth
                            @if ($item->state == 1 && $item->quantity > 0)
                                <div
                                    class="d-flex btns justify-content-center justify-content-lg-end align-items-center g-2">
                                    <button
                                        class="count-btn  border-0 text-white px-2 px-md-3 px-lg-2 px-xxl-2  py-1 py-md-2 rounded-circle"
                                        style="background-color: #d2d5d6"
                                        onclick="decreaseQuantity({{ $item->id }},event)">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                    <span
                                        class="count-num fw-bold text-white bg-primary mx-2 px-2 px-md-4 px-xxl-2 py-1 rounded-pill text-black"
                                        id="quantity{{ $item->id }}">0</span>
                                    <button
                                        class="count-btn border-0 text-white px-md-3 px-lg-2 px-xxl-1 py-1 py-md-2  rounded-circle"
                                        style="background-color: #1c2b30"
                                        onclick="increaseQuantity({{ $item->id }},event , {{ $item->max_qty_for_order }})">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            @elseif($item->state == 2)
                                <div
                                    class="d-flex btns justify-content-center justify-content-lg-end align-items-center g-2">
                                    <button
                                        class="count-btn  border-0 text-white px-2 px-md-3 px-lg-2 px-xxl-2  py-1 py-md-2 rounded-circle"
                                        style="background-color: #d2d5d6"
                                        onclick="decreaseQuantity({{ $item->id }},event)">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                    <span
                                        class="count-num fw-bold text-white bg-primary mx-2 px-2 px-md-4 px-xxl-2 py-1 rounded-pill text-black"
                                        id="quantity{{ $item->id }}">0</span>
                                    <button
                                        class="count-btn border-0 text-white px-md-3 px-lg-2 px-xxl-1 py-1 py-md-2  rounded-circle"
                                        style="background-color: #1c2b30"
                                        onclick="increaseQuantity({{ $item->id }},event , {{ $item->max_qty_for_order }})">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            @endif
                        @endauth

                        @guest
                            <div class="d-flex btns justify-content-center justify-content-lg-end align-items-center g-2">
                                <button
                                    class="login count-btn  border-0 text-white px-2 px-md-3 px-lg-2 px-xxl-2 py-1 py-md-2 rounded-circle"
                                    style="background-color: #d2d5d6">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <span
                                    class="count-num fw-bold text-white bg-primary  mx-2 px-2 px-md-4 px-xxl-2 py-1 rounded-pill text-black"
                                    id="quantity{{ $item->id }}">0</span>
                                <button
                                    class="login count-btn border-0 text-white px-md-3 px-lg-2 px-xxl-2 py-1 py-md-2 rounded-circle"
                                    style="background-color: #1c2b30">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        @endguest
                    </div>
                    @auth
                        @if ($item->state == 1 && $item->quantity > 0)

                            <div class="col-12">
                                <div
                                    class="mt-1 add-btn-container d-flex justify-content-center align-items-center w-100">
                                    <a class="add_to_cart btn add-btn btn-square text-black px-4 py-4 rounded w-100"
                                       id="add_to_cart{{ $item->id }}" data-quantity="1"
                                       data-product-id="{{ $item->id }}">
                                        اضافة الى السلة
                                    </a>
                                </div>
                            </div>
                        @elseif($item->state == 2)
                            <div class="col-12">
                                <div
                                    class="mt-1 add-btn-container d-flex justify-content-center align-items-center w-100">
                                    <a class="add_to_cart btn add-btn btn-square text-black px-4 py-4 rounded w-100"
                                       id="add_to_cart{{ $item->id }}" data-quantity="1"
                                       data-product-id="{{ $item->id }}">
                                        احجز الان
                                    </a>
                                </div>
                            </div>
                        @elseif($item->state == 3)
                            <div class="col-12">
                                <div class="mt-1 add-btn-container d-flex justify-content-center align-items-center w-100">
                                    <button class="btn btn-info btn-square text-white px-2 py-4 rounded w-100" disabled>
                                        سيتوفر قريبا
                                    </button>
                                </div>
                            </div>
                        @endauth
                    @endauth
                    @guest
                        <div class="col-12">
                            <div class="mt-1 add-btn-container d-flex justify-content-center align-items-center">
                                <button class="btn add-btn text-black px-4 py-2 rounded w-100 login">اضافة الى
                                    السلة
                                </button>
                            </div>
                        </div>
                    @endguest
            </div>
        </div>
    </div>
</div>

<style>
    .ribbon-wrapper {
        z-index: 3;
        position: absolute;
        top: 0;
        right: 0;
        overflow: hidden;
        width: 75px;
        height: 75px;
    }

    .ribbon {
        font-size: 12px;
        font-weight: bold;
        color: white;
        text-align: center;
        line-height: 20px;
        transform: rotate(45deg);
        position: absolute;
        padding: 4px 0;
        top: 10px;
        right: -25px;
        width: 100px;
        background-color: red;
    }

    .add-btn-container {
        overflow: hidden;
    }

    .product-item:hover .add-btn {
        transform: translateY(0);
    }

    .add-btn {
        transform: translateY(180%);
        background-color: #e99239;
        color: #000 !important;
        transition: background-color .3s, transform .5s;

        &:hover {
            background-color: #e67d15;
        }
    }

    .product-item .add-btn:hover {
        color: #fff !important;
    }

    @media (max-width: 991px) {
        .product-item .add-btn {
            transform: translateY(0);
            font-size: 14px;
        }
    }

    img.lazy {
        filter: blur(10px);
        transition: filter 0.3s;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
    }

    img.lazy:not([src]) {
        background-color: #f0f0f0;
        height: 300px;
    }

    .product-image {
        max-height: 300px;
    }

    @media (min-width: 1200px) {
        .product-image {
            max-height: auto;
            height: 500px;
        }
    }

    @media (min-width: 1600px) {
        .product-image {
            max-height: auto;
            min-height: 500px;
        }
    }


    /* Define the XXL breakpoint */
    @media (min-width: 1400px) {

        /* Dynamic column widths for XXL screens */
        .col-xxl-1 {
            flex: 0 0 auto;
            width: 8.333333%;
            max-width: 8.333333%;
        }

        .col-xxl-2 {
            flex: 0 0 auto;
            width: 16.666667%;
            max-width: 16.666667%;
        }

        .col-xxl-3 {
            flex: 0 0 auto;
            width: 25%;
            max-width: 25%;
        }

        .col-xxl-4 {
            flex: 0 0 auto;
            width: 33.333333%;
            max-width: 33.333333%;
        }

        .col-xxl-5 {
            flex: 0 0 auto;
            width: 41.666667%;
            max-width: 41.666667%;
        }

        .col-xxl-6 {
            flex: 0 0 auto;
            width: 50%;
            max-width: 50%;
        }

        .col-xxl-7 {
            flex: 0 0 auto;
            width: 58.333333%;
            max-width: 58.333333%;
        }

        .col-xxl-8 {
            flex: 0 0 auto;
            width: 66.666667%;
            max-width: 66.666667%;
        }

        .col-xxl-9 {
            flex: 0 0 auto;
            width: 75%;
            max-width: 75%;
        }

        .col-xxl-10 {
            flex: 0 0 auto;
            width: 83.333333%;
            max-width: 83.333333%;
        }

        .col-xxl-11 {
            flex: 0 0 auto;
            width: 91.666667%;
            max-width: 91.666667%;
        }

        .col-xxl-12 {
            flex: 0 0 auto;
            width: 100%;
            max-width: 100%;
        }

        .col-xxl-5-cols {
            flex: 0 0 auto;
            width: 20%;
            max-width: 20%;
        }

        .px-xxl-2 {
            padding-left: 0.5rem !important;
            /* Bootstrap's spacing scale: 2 = 0.5rem */
            padding-right: 0.5rem !important;
        }

        .font-size {
            font-size: 1.5rem !important;
        }
    }

    /* Ensure it overrides Bootstrap */
    .col-xxl- * {
        box-sizing: border-box !important;
        /* Override for specificity */
    }

    .font-size {
        font-size: 1.2rem;
    }

    @media (max-width: 600px) {
        .font-size {
            font-size: 1rem;
        }

        .product-image {
            max-height: 242px !important;
        }

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function increaseQuantity(id, event, maxQty) {
        event.stopPropagation(); // منع انتشار الحدث

        let quantityElement = $(`#quantity${id}`);
        let quantity = parseInt(quantityElement.text()) || 0; // منع NaN

        // التحقق من الحد الأقصى قبل الزيادة
        if (maxQty === 0) {
            Swal.fire({
                icon: "error",
                title: "الكمية نفذت",
                text: "عفواً، لقد نفذت الكمية المتاحة للحجز لهذا المنتج.",
                confirmButtonText: "حسناً",
                timer: 3000
            });
            return;
        } else if (quantity >= maxQty) {
            Swal.fire({
                icon: "info",
                title: "تنبيه",
                text: `لا يمكنك طلب أكثر من ${maxQty} من هذا المنتج.`,
                confirmButtonText: "حسناً",
                timer: 3000
            });
            return;
        }
        // زيادة الكمية
        quantity += 1;
        quantityElement.html(quantity);
        // تحديث بيانات الزر الخاص بالسلة
        let cartButton = $(`#add_to_cart${id}`);
        cartButton.data("quantity", quantity);
        cartButton.attr("data-quantity", quantity); // تحديث DOM أيضًا

        console.log(`✅ المنتج ID: ${id} | الكمية الجديدة: ${quantity} | الحد الأقصى: ${maxQty}`);
    }


    function decreaseQuantity(id, event) {
        event.stopPropagation(); // Prevent the click event from bubbling to the link
        let quantity = parseInt($(`#quantity${id}`).text());
        if (quantity > 0) {
            quantity -= 1;
            $(`#quantity${id}`).html(quantity);
            $(`#add_to_cart${id}`).data('quantity', quantity);
        }
    }
</script>
@endforeach
