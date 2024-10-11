@foreach ($products as $item)
@php
    // Search for the product in the cart
    $cartItem = Cart::search(function ($cartItem, $rowId) use ($item) {
        return $cartItem->id === $item->id;
    })->first(); // Get the first result if found
    // cart count
    $cartCount = Cart::instance('shopping')->count();
@endphp
<div class="col-lg-4 col-md-6 col-12 pb-1">
    <div
        class="product-item bg-light mb-4 position-relative d-flex flex-column justify-content-between rounded-5"
        style="min-height: 100%; border-radius: 20px;"
    >
        @if ($item->state == 0)
        <div class="ribbon-wrapper">
            <div class="ribbon">غير متاح</div>
        </div>
        @endif
        <div class="product-img ">
          <div class="overflow-hidden w-100" style="border-top-left-radius: 20px;border-top-right-radius: 20px;">
            <a href="{{ route('user.product.show', $item->id) }}">

                <img
                class="img-fluid w-100"
                src="{{ $item->image_path }}"
                alt=""
                style="max-height: 300px; border-top-left-radius: 20px;border-top-right-radius: 20px;"
                />
            </a>
          </div>
            {{-- <div class="product-action">
                @auth @if ($item->state == 1)
                <a
                    class="add_to_cart btn btn-outline-dark btn-square overflow-hidden"
                    id="add_to_cart{{$item->id}}"
                    data-quantity="1"
                    data-product-id="{{$item->id}}"
                >
                    <i class="fas fa-cart-plus"></i
                ></a>
                @else
                <a
                    class="btn btn-outline-dark btn-square overflow-hidden"
                    href="{{ route('user.product.show', $item->id) }}"
                >
                    <i class="fas fa-eye"></i
                ></a>
                @endif @else
                <a
                    class="btn btn-outline-dark btn-square overflow-hidden"
                    href="{{ route('user.product.show', $item->id) }}"
                >
                    <i class="fas fa-eye"></i
                ></a>
                @endauth
            </div> --}}
         <div class="mt-3 p-2 text-center">  
            <a class="h6 fw-bold text-decoration-none lh-base text-center m-0" href="{{ route('user.product.show', $item->id) }}">
                {{ $item->name }}
            </a>
         </div>
        </div>
<div class="container">
    <div class="row">
        <div class="col-12 text-center">
            @if ($item->state == 0)
           <div class=" rounded-pill bg-danger bg-gradient-danger px-2 py-1">
            <p class="text-white m-0">
                للاسف الكتاب غير متوفر حاليا وسيتوفر بتاريخ
                15/10/2024
            </p>
           </div>
        @endif

        </div>

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
     

<div
class="d-flex flex-column align-items-center justify-content-between px-2 py-4"
>
<div
    class="row gy-4 align-items-center justify-content-center w-100 mt-4 px-sm-4 px-md-0"
>
    <div class="col-sm-6 col-12">
        <div
            class="d-flex flex-column align-items-center align-items-sm-start  price "
        >
            <h5 class="fs-5 fw-bold mb-0">
                <span>EGP</span>
                <span
                    class="text-primary"
                    >{{ $item->price }}</span
                >
            </h5>
            @if ($item->state == 0)
         <div class="position-relative">
            <h6
            class="text-muted fs-5 mb-0 text-decoration-line-through"
        >
            <span>EGP</span>
            <span
                class="text-primary"
                >{{ $item->price + 20 }}</span
            >
        </h6>
        <!-- Tooltip for price -->
        <span
            class="tooltip-text"
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
            "
        >
            خصم 20% لفتره محدودة
        </span>
         </div>
            @endif
        </div>
    </div>
    <div class="col-sm-6 col-12">
        @auth
        <div class="d-flex justify-content-center justify-content-sm-end align-items-center g-2">
            <button class="cart-delete border-0 text-white px-3 py-2 rounded-circle" style="background-color: #d2d5d6" >
                <i class="fa-solid fa-minus"></i>
            </button>
            <span class="fw-bold text-white bg-primary mx-2 px-4 py-1 rounded-pill text-black">{{$cartCount}}</span>
            <button class="add_to_cart border-0 text-white px-3 py-2 rounded-circle" style="background-color: #1c2b30" id="add_to_cart{{$item->id}}"
                data-quantity="1"
                data-product-id="{{$item->id}}">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
        @else
        <div class="d-flex justify-content-center justify-content-sm-end align-items-center g-2">
            <button class="login border-0 text-white px-3 py-2 rounded-circle" style="background-color: #d2d5d6" >
                <i class="fa-solid fa-minus"></i>
            </button>
            <span class="fw-bold text-white bg-primary mx-2 px-4 py-1 rounded-pill text-black">1</span>
            <button class="login border-0 text-white px-3 py-2 rounded-circle" style="background-color: #1c2b30" >
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
        @endauth
    </div>
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
</style>

@endforeach
<script>

    const products = @json($products);
    console.log(products);


   

  
</script>
