
@foreach ($coupons as $item)
<div class="col-xxl-5-cols col-xl-3 col-lg-4 col-12 col-sm-6 pb-1">

    <div class="product-item bg-light mb-4 position-relative d-flex flex-column justify-content-between rounded-5"
        style="min-height: 100%; border-radius: 20px;">

        <!-- Ribbon for Unavailable Items -->
        @if ($item->vouchers->where('is_used', 0)->count() == 0)
            <div class="ribbon-wrapper">
                <div class="ribbon">غير متاح</div>
            </div>
        @endif
        <!-- Product Image and Name -->

            <div class="product-img">
                <div class="overflow-hidden w-100 position-relative"
                    style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                    <img src="{{ $item->image_path }}" class="img-fluid" alt="{{ $item->name }}" />
                </div>
                <div class="p-2 text-center fw-bold">
                    <p class="font-size fs-sm-5 text-black fw-bold m-0">
                        {{ $item->name }}
                    </p>
                </div>
            </div>

        <!-- Product Price and Buttons -->
        <div class="d-flex flex-column flex-wrap align-items-center justify-content-between py-2">
            <div class="row align-items-center justify-content-center w-100 mt-2 px-sm-4 px-md-0">

                <!-- Product Price -->
                <div class="col-md-6 col-12">
                    <div class="d-flex flex-column align-items-center align-items-md-start price">
                        <h5 class="fs-5 fw-bold mb-0">
                            <span>EGP</span>
                            <span class="text-primary" id="price{{ $item->id }}" data-price="{{ $item->price }}">{{ $item->price }}</span>
                        </h5>
                    </div>
                </div>

                <!-- Quantity Control Buttons -->
                <div class="col-md-6 col-12">
                    @if ($item->vouchers->where('is_used', 0)->count() !== 0)
                        <div class="d-flex btns justify-content-center justify-content-lg-end align-items-center g-2">
                            <button class="count-btn border-0 text-white px-2 px-md-3 px-lg-2 px-xxl-2 py-1 py-md-2 rounded-circle"
                                style="background-color: #d2d5d6"
                                onclick="decreaseQuantity({{ $item->id }}, event)">
                                <i class="fa-solid fa-minus"></i>
                            </button>
                            <span class="count-num fw-bold text-white bg-primary mx-2 px-2 px-md-4 px-xxl-2 py-1 rounded-pill text-black"
                                id="quantity{{ $item->id }}">1</span>
                            <button class="count-btn border-0 text-white px-md-3 px-lg-2 px-xxl-1 py-1 py-md-2 rounded-circle"
                                style="background-color: #1c2b30"
                                onclick="increaseQuantity({{ $item->id }}, event)">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Buy Now or Login Button -->
                @if ($item->vouchers->where('is_used', 0)->count() !== 0)
                    @auth
                        <div class="col-12">
                           @if ($errors->has('qty') && old('id') == $item->id)
    <div class="alert alert-danger">
        {{ $errors->first('qty') }}
    </div>
@endif

                            <div class="mt-1 add-btn-container d-flex justify-content-center align-items-center w-100">
                                <form method="POST" action="{{ route('user.buy.vouchers') }}">
                                    @csrf
                                    <input type="hidden" id="qty{{ $item->id }}" name="qty" value="1" />
                                    <input type="hidden" name="id" value="{{ $item->id }}" />
                                    <button class="btn add-btn btn-square text-black px-4 py-4 rounded w-100"
                                        data-quantity="1">
                                        شراء الان
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                    @guest
                        <div class="col-12">
                            <div class="mt-1 add-btn-container d-flex justify-content-center align-items-center">
                                <a href="{{ route('user.login.user') }}" class="btn add-btn text-black px-4 py-2 rounded w-100">
                                    سجل دخول للاستمرار
                                </a>
                            </div>
                        </div>
                    @endguest
                @endif

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
    
      .product-item:hover{
        cursor: pointer;
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
.product-image{
     max-height:300px;
}
 @media (min-width: 1200px){
     .product-image{
     max-height:auto;
     height:500px;
     }
 }
 @media (min-width: 1400px){
     .product-image{
     min-height:700px;
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
    padding-left: 0.5rem !important; /* Bootstrap's spacing scale: 2 = 0.5rem */
    padding-right: 0.5rem !important;
  }
   .font-size{
    font-size:1.5rem !important;
}   
}

/* Ensure it overrides Bootstrap */
.col-xxl-* {
  box-sizing: border-box !important; /* Override for specificity */
}

.font-size{
    font-size:1.2rem;
}

@media (max-width: 600px){
 .font-size{
    font-size:1rem;
}   
.product-image{
     max-height:242px !important;
}

}
</style>
<!-- Lazy Loading Script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
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

<script>
    function increaseQuantity(id, event) {
        event.stopPropagation(); // Prevent the click event from bubbling to the link
        let quantity = parseInt($(`#quantity${id}`).text());
        let price = parseFloat($(`#price${id}`).data('price'));

        quantity += 1;
        $(`#quantity${id}`).html(quantity);
        $(`#add_to_cart${id}`).data('quantity', quantity);

        // Update the total price
        $(`#price${id}`).html((price * quantity).toFixed(2));

        // Update the hidden input value
        $(`#qty${id}`).val(quantity);
    }

    function decreaseQuantity(id, event) {
        event.stopPropagation(); // Prevent the click event from bubbling to the link
        let quantity = parseInt($(`#quantity${id}`).text());
        let price = parseFloat($(`#price${id}`).data('price'));

        if (quantity > 1) {
            quantity -= 1;
            $(`#quantity${id}`).html(quantity);
            $(`#add_to_cart${id}`).data('quantity', quantity);

            // Update the total price
            $(`#price${id}`).html((price * quantity).toFixed(2));

            // Update the hidden input value
            $(`#qty${id}`).val(quantity);
        }
    }
</script>


@endforeach
