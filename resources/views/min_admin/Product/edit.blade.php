@extends('admin.layouts.master')
@section('title')
    update products
@endsection
@section('content')
    <div class="col-lg-12 d-flex justify-content-center align-items-center">
        <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">

            <form id="form" class="row g-3" {{-- method="POST" action="{{ route('dashboard.store.product') }}" --}} enctype="multipart/form-data">
                @csrf
                <div class="col-12 text-center mb-5">
                    <h1>Edit product</h1>
                </div>
                @foreach (config('translatable.locales') as $locale)
                    <div class="col-6">
                        <label class="form-label">الاسم {{ $locale }}</label>
                        <input type="text" name="name:{{ $locale }}" id="name:{{ $locale }}"
                            data-validation="required" data-validation-required="required"
                            class="form-control form-control-lg @error('name:{{ $locale }}') is-invalid @enderror"
                            value="{{ $product->translate($locale)->name }}" placeholder="...">
                    </div>
                    @error('name:{{ $locale }}')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                @endforeach
                @foreach (config('translatable.locales') as $locale)
                    <div class="col-6">
                        <label class="form-label">الوصف {{ $locale }}</label>
                        <input type="text" name="description:{{ $locale }}" id="description:{{ $locale }}"
                            data-validation="required" data-validation-required="required"
                            class="form-control form-control-lg @error('description:{{ $locale }}') is-invalid @enderror"
                            value="{{ $product->translate($locale)->description }}" placeholder="...">
                    </div>
                    @error('description:{{ $locale }}')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                @endforeach
                <div class="col-12">
                    <label class="form-label">الاسم المختصر</label>
                    <input type="text" name="short_name" id="short_name"
                        class="form-control form-control-lg @error('short_name') is-invalid @enderror"
                        value="{{ $product->short_name }}" placeholder="...">
                </div>
                @error('short_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="col-12">
                    <label class="form-label">المنتج متوفر؟</label>
                    <select class="form-control show-tick ms select2 @error('state') is-invalid @enderror" id="mySelect"
                        name="state" id="state" data-validation="required" data-validation-required="required"
                        data-placeholder="Select">
                        <option></option>
                        <option value="0" {{ $product->state == 0 ? 'selected' : '' }}>No</option>
                        <option value="1" {{ $product->state == 1 ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
                @error('state')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="col-12">
                    <label class="form-label">التعليق (سيظهر عندما يكون المنتج غير متوفر)</label>
                    <input type="text" name="commit" id="commit"
                        class="form-control form-control-lg @error('commit') is-invalid @enderror"
                        value="{{ $product->commit }}" placeholder="...">
                </div>
                @error('commit')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror


                <div class="col-12">
                    <label class="form-label">حالة المنتج</label>

                    <select class="form-control show-tick ms select2 @error('best_seller') is-invalid @enderror"
                        name="is_deleted" id="is_deleted">
                        <option value="0" {{ $product->is_deleted == 0 ? 'selected' : '' }}>نشط</option>
                        <option value="1" {{ $product->is_deleted == 1 ? 'selected' : '' }}>غير نشط (محذوف)</option>
                    </select>
                </div>
                @error('is_deleted')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror


                <div class="col-12">
                    <label class="form-label">السعر</label>
                    <input type="text" name="price" id="price" data-validation="required"
                        data-validation-required="required"
                        class="form-control form-control-lg @error('price') is-invalid @enderror"
                        value="{{ $product->price }}" placeholder="...">
                </div>
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="col-12">
                    <label class="form-label">الكمية</label>
                    <input type="text" name="quantity" id="quantity"
                        class="form-control form-control-lg @error('quantity') is-invalid @enderror"
                        value="{{ $product->quantity }}" placeholder="...">
                </div>
                @error('quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="col-12">
                    <label class="form-label">قيمة الضريبة</label>
                    <input type="text" name="tax" id="tax" data-validation="required"
                        data-validation-required="required"
                        class="form-control form-control-lg @error('tax') is-invalid @enderror" value="{{ $product->tax }}"
                        placeholder="...">
                </div>
                @error('tax')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="col-12">
                    <label class="form-label">قيمة الضريبة (اذا كان التوصيل لاقرب مكتب بريد)</label>
                    <input type="text" name="slowTax" id="slowTax" data-validation="required"
                        data-validation-required="required"
                        class="form-control form-control-lg @error('slowTax') is-invalid @enderror"
                        value="{{ $product->slowTax }}" placeholder="...">
                </div>
                @error('slowTax')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="col-12">
                    <label class="form-label">have offer</label>
                    <select class="form-control show-tick ms select2 @error('have_offer') is-invalid @enderror"
                        id="mySelect" name="have_offer" id="have_offer" data-validation="required"
                        data-validation-required="required" data-placeholder="Select">
                        <option></option>
                        <option value="0" {{ $product->have_offer == 0 ? 'selected' : '' }}>No</option>
                        <option value="1" {{ $product->have_offer == 1 ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
                @error('have_offer')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="col-12">
                    <label class="form-label">Best Seller</label>
                    <select class="form-control show-tick ms select2 @error('best_seller') is-invalid @enderror"
                        name="best_seller" id="best_seller">
                        <option value="0" {{ $product->best_seller == 0 ? 'selected' : '' }}>No</option>
                        <option value="1" {{ $product->best_seller == 1 ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
                @error('best_seller')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="col-12" style="display: none" id="myHiddenElement1">
                    <label class="form-label">offer type</label>
                    <select class="form-control show-tick ms select2 @error('offer_type') is-invalid @enderror"
                        name="offer_type" data-placeholder="Select" id="offer_type" data-validation="required"
                        data-validation-required="required">
                        <option></option>
                        <option value="percentage" {{ $product->offer_type == 'percentage' ? 'selected' : '' }}>
                            percentage
                        </option>
                        <option value="value" {{ $product->offer_type == 'value' ? 'selected' : '' }}>value</option>
                    </select>
                </div>
                @error('offer_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="col-12" style="display: none" id="myHiddenElement2">
                    <label class="form-label">offer value</label>
                    <input type="text" name="offer_value" value="{{ $product->offer_value }}" id="offer_value"
                        data-validation="required" data-validation-required="required"
                        class="form-control form-control-lg @error('offer_value') is-invalid @enderror"
                        placeholder="...">
                </div>
                @error('offer_value')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <p id="final_price"></p>
                <div class="col-12">
                    <label class="form-label">القسم</label>
                    <select class="form-control show-tick ms select2 @error('category_id') is-invalid @enderror"
                        name="main_category_id" id="category_id" data-validation="required"
                        data-validation-required="required">
                        <option value="">Select Your Option</option>
                        @foreach ($main_categories as $category)
                            {{-- @if ($category->is_parent == 1) --}}
                            <option value="{{ $category->id }}"
                                {{ $category->id == $product->main_category_id ? 'selected' : '' }}>
                                {{ $category->name }}</option>
                            {{-- @endif --}}
                        @endforeach
                    </select>
                </div>
                @error('main_category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="col-12">
                    <label class="form-label">الماده</label>
                    <select class="form-control show-tick ms select2 @error('category_id') is-invalid @enderror"
                        name="category_id" id="category_id">
                        <option value="">Select Your Option</option>
                        @foreach ($categories as $category)
                            {{-- @if ($category->is_parent == 1) --}}
                            <option value="{{ $category->id }}"
                                {{ $category->id == $product->category_id ? 'selected' : '' }}>
                                {{ $category->title }}</option>
                            {{-- @endif --}}
                        @endforeach
                    </select>
                </div>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="col-12">
                    <label class="form-label">الصف الدراسي</label>
                    <select class="form-control show-tick ms select2 @error('brand_id') is-invalid @enderror"
                        name="slider_id" id="slider_id">
                        <option value="">Select Your Option</option>
                        @foreach ($sliders as $slider)
                            <option value="{{ $slider->id }}"
                                {{ $slider->id == $product->slider_id ? 'selected' : '' }}>
                                {{ $slider->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">المدرس</label>
                    <select class="form-control show-tick ms select2 @error('brand_id') is-invalid @enderror"
                        name="brand_id" id="brand_id">
                        <option value="">Select Your Option</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" {{ $brand->id == $product->brand_id ? 'selected' : '' }}>
                                {{ $brand->title }}</option>
                        @endforeach
                    </select>
                </div>
                @error('brand_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="col-12">
                    {{-- <img src="{{ $product->image_path }}" class="avatar rounded me-2" alt="profile-image"
                    style="height:120px;width:150px"> --}}
                    <div class="card">
                        <div class="card-body">
                            <h6>الصوره الرئيسية</h6>
                            <input type="file" data-default-file="{{ $product->image_path }}" name="photo"
                                class="dropify @error('photo') is-invalid @enderror">
                        </div>
                    </div>
                </div>
                @error('photo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <h6>الصور الفرعيه</h6>
                @foreach ($product->images as $image)
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <input type="file" data-default-file="{{ asset('storage/' . $image->image_path) }}"
                                    name="images" class="dropify @error('images') is-invalid @enderror">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="img_{{ $image->id }}"
                                        name="delete_images[]" value="{{ $image->id }}">
                                    <label class="form-check-label" for="img_{{ $image->id }}">حذف هذه الصوره</label>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
                @error('images')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h6> أضافه صوره جديده للصور الفرعيه</h6>
                            <input type="file" name="images[]" accept="image/*"
                                class="dropify @error('images') is-invalid @enderror" multiple>
                        </div>
                    </div>
                </div>
                @error('images')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="col-6">
                    <input type="text" name="product_id" class="form-control form-control-lg d-none"
                        value="{{ $product->id }}">
                </div>
                <div class="col-12 text-center mt-4">
                    <button id="submit" type="submit"
                        class="btn btn-lg btn-block btn-dark lift text-uppercase">Update</button>
                </div>
            </form>

        </div>
    </div>
@endsection
@section('js')
    <script>
        var child_cat_id = {{ $product->child_cat_id }}
        $('#category_id').change(function(e) {
            var category_id = $(this).val();
            var url = "{{ route('dashboard.getChildByParentID', ':id') }}";
            url = url.replace(':id', category_id);

            if (category_id != null) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        category_id: category_id,
                    },
                    success: function(response) {
                        var html_option = "<option value=''>--- Child Category</option>";
                        if (response.status) {
                            $('#child_cat_div').removeClass('d-none');
                            $.each(response.data, function(id, title) {
                                html_option += "<option value='" + id + "' " + (child_cat_id ==
                                    id ? 'selected' : '') + ">" + title + "</option>"
                            });
                        } else {
                            $('#child_cat_div').addClass('d-none');
                        }
                        $('#child_cat_id').html(html_option);
                    },
                });
            };

        });

        if (child_cat_id != null) {
            $('#category_id').change();
        }
    </script>

    <script>
        /* change price */
        var priceInput = document.getElementById("price");
        var offerTypeSelect = document.getElementById("offer_type");
        var offerValueInput = document.getElementById("offer_value");
        var finalPriceParagraph = document.getElementById("final_price");

        function calculateFinalPrice() {
            var price = priceInput.value;
            var offerType = offerTypeSelect.value;
            var offerValue = offerValueInput.value;
            var finalPrice;
            if (offerType === "percentage") {
                finalPrice = price - (price * offerValue / 100);
            } else {
                finalPrice = price - offerValue;
            }
            finalPriceParagraph.textContent = "Final Price: " + finalPrice;
        }
        offerTypeSelect.addEventListener("change", calculateFinalPrice);
        priceInput.addEventListener("input", calculateFinalPrice);
        offerValueInput.addEventListener("input", calculateFinalPrice);
    </script>

    <script>
        var selectElement = document.getElementById("mySelect");
        var hiddenElement1 = document.getElementById("myHiddenElement1");
        var hiddenElement2 = document.getElementById("myHiddenElement2");

        if (selectElement.value === "1") {
            hiddenElement1.style.display = "block";
            hiddenElement2.style.display = "block";
        } else {
            hiddenElement1.style.display = "none";
            hiddenElement2.style.display = "none";
        }
        selectElement.onchange = function() {
            if (selectElement.value === "1") {
                hiddenElement1.style.display = "block";
                hiddenElement2.style.display = "block";
            } else {
                $('#offer_value').val(0);
                hiddenElement1.style.display = "none";
                hiddenElement2.style.display = "none";
            }
        }

        $.validate({
            form: 'form'
        });
        $(document).ready(function() {
            $('#form').submit(function(e) {
                console.log("Form Data Before Sending:", this);
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    url: '{{ route('dashboard.mini.product.update') }}',
                    type: "POST",
                    dataType: "json",
                    data: formData,
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        console.log(response);
                        Swal.fire('Data has been Updated successfully', '', 'success');
                    },

                    error: function(xhr, status, error) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '<br>';
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorMessage,
                        });
                    }
                });
            });
        });
    </script>
@endsection
