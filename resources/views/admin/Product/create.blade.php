@extends('admin.layouts.master')
@section('title')
Create product
@endsection
@section('content')
<div class="col-lg-6 d-flex justify-content-center align-items-center">
    <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">

        <form id="form" class="row g-3 myform" {{-- method="POST" action="{{ route('dashboard.store.product') }}" --}}
            enctype="multipart/form-data">
            @csrf
            <div class="col-12 text-center mb-5">
                <h1>اضافه كتاب</h1>
            </div>
            @foreach (config('translatable.locales') as $locale)
                    <div class="col-6">
                        <label class="form-label">الاسم {{ $locale }}</label>
                        <input type="text" name="name:{{ $locale }}" id="name:{{ $locale }}"
                            data-validation="required" data-validation-required="required"
                            class="form-control form-control-lg @error('name:{{ $locale }}') is-invalid @enderror"
                            placeholder="...">
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
                        placeholder="...">
                </div>
                @error('description:{{ $locale }}')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @endforeach
            <div class="col-12">
                <label class="form-label">السعر</label>
                <input type="text" name="price" id="price" data-validation="required" data-validation-required="required"
                    class="form-control form-control-lg @error('price') is-invalid @enderror" placeholder="...">
            </div>
            @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            {{-- <div class="col-12 d-none">
                <label class="form-label">have offer</label>
                <select class="form-control show-tick ms select2 @error('have_offer') is-invalid @enderror"
                    id="have_offer" data-validation="required" data-validation-required="required" name="have_offer" data-placeholder="Select">
                    <option></option>
                    <option value="0" selected>No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
            @error('have_offer')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror --}}
            {{-- <div id="offer_fields" style="display: none;">
                <div class="col-12">
                    <label class="form-label">offer type</label>
                    <select class="form-control show-tick ms select2 @error('offer_type') is-invalid @enderror"
                        name="offer_type" data-placeholder="Select" id="offer_type" data-validation="required" data-validation-required="required">
                        <option></option>
                        <option value="percentage">percentage</option>
                        <option value="value" selected>value</option>
                    </select>
                </div>
                @error('offer_type')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="col-12 mt-3">
                    <label class="form-label">offer value</label>
                    <input type="text" name="offer_value" value="0" id="offer_value" data-validation="required" data-validation-required="required"
                        class="form-control form-control-lg @error('offer_value') is-invalid @enderror"
                        placeholder="...">
                </div>
                @error('offer_value')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 mt-3">
                <label class="form-label">final price</label>
                <input type="text" name="final_price" id="final_price2"  data-validation="required" data-validation-required="required"
                    class="form-control form-control-lg @error('final_price') is-invalid @enderror"
                    placeholder="...">
            </div>
            @error('final_price')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            <p id="final_price"></p> --}}
            <div class="col-12">
                <label class="form-label">الماده</label>
                <select class="form-control show-tick ms select2 @error('category_id') is-invalid @enderror"
                    name="category_id" id="category_id" data-validation="required" data-validation-required="required">
                    <option value="">Select Your Option</option>
                    @foreach ($categories as $category)
                    {{-- @if ($category->is_parent == 1) --}}
                        <option value="{{$category->id}}">{{$category->title}}</option>
                    {{-- @endif --}}
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">الصف الدراسي</label>
                <select class="form-control show-tick ms select2 @error('brand_id') is-invalid @enderror"
                    name="slider_id" id="slider_id" data-validation="required" data-validation-required="required">
                    <option value="">Select Your Option</option>
                    @foreach ($sliders as $slider)
                    <option value="{{$slider->id}}">{{$slider->title}}</option>
                    @endforeach
                </select>
            </div>
            @error('brand_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror            
            <div class="col-12">
                <label class="form-label">المدرس</label>
                <select class="form-control show-tick ms select2 @error('brand_id') is-invalid @enderror"
                    name="brand_id" id="brand_id">
                    <option value="">Select Your Option</option>
                    @foreach ($brands as $brand)
                    <option value="{{$brand->id}}">{{$brand->title}}</option>
                    @endforeach
                </select>
            </div>
            @error('brand_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6>الصوره</h6>
                        <input type="file" name="photo" class="dropify @error('photo') is-invalid @enderror">
                    </div>
                </div>
            </div>
            @error('photo')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="col-12 text-center mt-4">
                <button id="submit" type="submit"
                    class="btn btn-lg btn-block btn-dark lift text-uppercase">Save</button>
            </div>
        </form>

    </div>
</div>
@endsection
@section('js')
{{-- <script>
     $('#category_id').change(function(e) {
        var category_id = $(this).val();
        var url = "{{ route('dashboard.getChildByParentID', ':id') }}";
        url = url.replace(':id', category_id);

        if(category_id != null) {
            $.ajax({
                url:url,
                type:"POST",
                data:{
                    _token:"{{csrf_token()}}",
                    category_id:category_id,
                },
                success:function(response) {
                    var html_option="<option value=''>--- Child Category</option>";
                    if(response.status) {
                        $('#child_cat_div').removeClass('d-none');
                        $.each(response.data,function(id,title) {
                            html_option += "<option value='"+id+"'>"+title+"</option>"
                        });
                    } else {
                        $('#child_cat_div').addClass('d-none');
                    }
                    $('#child_cat_id').html(html_option);
                },
            });
        };
    });
</script> --}}
{{-- <script>
    var selectElement = document.getElementById("have_offer");
    var offer_fields = document.getElementById("offer_fields");

    selectElement.onchange = function() {
        if (selectElement.value === "1") {
            offer_fields.style.display = "block";
        } else {
            $('#offer_value').val(0);
            offer_fields.style.display = "none";
        }
    }

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
    $('#final_price2').val(finalPrice);
    }
    offerTypeSelect.addEventListener("change", calculateFinalPrice);
    priceInput.addEventListener("input", calculateFinalPrice);
    offerValueInput.addEventListener("input", calculateFinalPrice);
</script> --}}

<script>
    $.validate({
        form: 'form'
    });
    $(document).ready(function() {

    $('#form').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: '{{route('dashboard.store.product')}}',
            type: "POST",
            dataType: "json",
            data: formData,
            contentType: false,
            processData: false,

            success: function(response) {
                $("#form")[0].reset();
                $('.dropify-clear').click();
                console.log(response);
                Swal.fire('Data has been saved successfully', '', 'success');

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
