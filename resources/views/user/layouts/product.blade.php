@foreach ($products as $item)
<div class="col-lg-3 col-md-4 col-6 pb-1">
    <div class="product-item bg-light mb-4 position-relative">
        @if ($item->state == 0)
        <div class="ribbon-wrapper">
            <div class="ribbon">غير متاح</div>
        </div>
        @endif
        <div class="product-img position-relative overflow-hidden">
            <img class="img-fluid w-100" src="{{ $item->image_path }}" alt="">
            <div class="product-action">
                @auth
                @if ($item->state == 1)
                <a class="add_to_cart btn btn-outline-dark btn-square overflow-hidden" id="add_to_cart{{$item->id}}"
                    data-quantity="1" data-product-id="{{$item->id}}">
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
            </div>
        </div>

        <div class="text-center py-4" style="padding: 5px; max-height: 250px; min-height:200px;">
            <!--<h6>-->
            <a class="h6 fw-lighter text-decoration-none " href="{{ route('user.product.show', $item->id) }}">
                {{ $item->name }}
            </a>
            <!--</h6>-->
            <div class="d-flex align-items-center justify-content-center mt-2">
                <h5 class="pt-2 pe-2 ps-2 pb-2 rounded" style="background-color: #e99239; border-radius:15px"> السعر:
                    {{ $item->price }} جنية </h5>
            </div>
            <div class="d-flex align-items-center justify-content-center mb-1">
                <small class="fa fa-star text-primary mr-1"></small>
                <small class="fa fa-star text-primary mr-1"></small>
                <small class="fa fa-star text-primary mr-1"></small>
                <small class="fa fa-star text-primary mr-1"></small>
                <small class="fa fa-star text-primary mr-1"></small>
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
