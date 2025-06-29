<style>
    .category_text {
        font-size: 0.7rem;
    }

    .categ_img {
        width: 100px;
        height: 100px;
    }

    .categ_img_code {
        width: 100px;
        height: 100px;
    }

    @media (min-width: 576px) {
        .category_text {
            font-size: 1rem;
        }

        .categ_img_code {
            transform: scale(1.2);
        }

    }
</style>
@if(isset($main_categories))
@foreach ($main_categories as $category)
<div class="h-100 col-6">
    <div class="w-auto py-3 rounded-2 bg-white category">
        @if ($category->name == 'اكواد مدرسين الاونلاين')
            <a href="{{ route('user.evouchers') }}"
                class="d-flex flex-column justify-content-center align-items-center gap-3 text-decoration-none text-black text-center">

                @if ($category->icon_image)
                    <img src="{{ asset('storage/' . $category->icon_image) }}" alt="{{ $category->name }}"
                        class="img-fluid categ_img_code">
                @endif
                <strong class="category_text">{{ $category->name }}</strong>
            </a>
        @else
            <a href="{{ route('user.shop', ['main_category_id' => $category->id]) }}"
                class="d-flex flex-column justify-content-center align-items-center gap-3 text-decoration-none text-black text-center">

                @if ($category->icon_image)
                    <img src="{{ asset('storage/' . $category->icon_image) }}" alt="{{ $category->name }}"
                        class="img-fluid categ_img">
                @endif
                <strong class="category_text">{{ $category->name }}</strong>
            </a>
        @endif
    </div>
</div>
@endforeach

@else
    <p>Error: $main_categories is missing!</p>
@endif
