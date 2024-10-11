@extends('user.layouts.master')

@section('title')
online shop
@endsection

@section('content')
<!-- Checkout Start -->

<div class="container ">
    <div class="row  " style="text-align:center">
        <div class="col-md-12">
            <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">تفاصيل
                    الدفع </span></h5>

            <div>
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                <form method="POST" action="{{ route('test.checkout.store') }}" enctype='multipart/form-data'>
                    @csrf
                    @include('user.partials._errors')
                    <table class="table table-hover  style=" border: 2px solid #dee2e6; overflow: scroll;">
                        <thead style="overflow: scroll;">
                            <tr>
                                <th>صورة المنتج </th>
                                <th>الوصف</th>
                                <th>السعر </th>
                                <th>الكميه </th>
                                <th>المجموع </th>
                            </tr>
                        </thead>
                        <tbody class="bg-warning">
                            @foreach (Cart::instance('shopping')->content() as $item)
                            <tr>
                                <input type="hidden" name="product_id[]" value="{{ $item->id }}">
                                <input type="hidden" name="amount[]" value="{{ $item->qty }}">
                                <input type="hidden" name="price[]" value="{{ $item->price }}">
                                <input type="hidden" name="total_price[]" value="{{ $item->subtotal() }}">
                                <td><img src="{{ $item->model->image_path }}" style="width: 100px" class="img-thumbnail"
                                        alt="">
                                </td>
                                <td><a href="{{ route('user.product.show', $item->id) }}" class="nav-link text-dark">{{
                                        $item->name }}</a></td>
                                <td>$ {{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->qty }}
                                </td>
                                <td>$ {{ $item->subtotal() }}</td>
                            </tr>
                            @endforeach
                            <input type="hidden" name="all_total"
                                value="{{ number_format((float) str_replace(',', '', Cart::subtotal()), 2) }}"
                                id="all_total">
                        </tbody>
                    </table>
                    <div class="col-12 container">
                        <p>يتم أضافة رسوم شحن خاصة بكل محافظه يرجي اختيار المحافظة :</p>
                        <select id="tax_value" name="ship" onchange="calculateTotal()"
                            class="btn border border-4 form-select" aria-label="Default select">
                            <option selected="">أختر المحافظة </option>
                            <option value="60">أسيوط</option>
                            <option value="60">أسوان</option>
                            <option value="60">الأقصر </option>
                            <option value="60">البحر الاحمر </option>
                            <option value="55">البحيرة </option>
                            <option value="60"> بني سويف </option>
                            <option value="55">بورسعيد </option>
                            <option value="55">الجيزة </option>
                            <option value="60">جنوب سيناء </option>
                            <option value="55">الدقهلية </option>
                            <option value="55">دمياط </option>
                            <option value="60">سوهاج </option>
                            <option value="55">السويس </option>
                            <option value="55">الشرقيه </option>
                            <option value="60">شمال سيناء </option>
                            <option value="55">الغربيه </option>
                            <option value="60">الفيوم </option>
                            <option value="55">القاهرة </option>
                            <option value="55">القليوبيه </option>
                            <option value="60">الوادي الجديد </option>
                            <option value="60">قنا </option>
                            <option value="60">مطروح </option>
                            <option value="55">المنوفية </option>
                            <option value="55">الغردقة </option>



                        </select>
                        <p id="all" class="lead">الاجمالي بعد اضافه رسوم الشحن: </p>
                        <h6> أذا قمت بالتحويل عن طريق فودافون كاش ادخل الرقم المحول منه </h6>
                        <input name="cash_number" class="input-group input-group-lg btn-outline-warning">
                        <p> </p>
                        <h6>أذا قمت بالتحويل عن طريق انستا باي ادخل الرقم او اليوزر نيم المحول منه</h6>
                        <input name="instapay" class="input-group input-group-lg btn-outline-warning">
                        <p> </p>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6>برجاء ادخال الصورة الخاصه بتفاصيل الدفع </h6>
                                    <input type="file" name="image"
                                        class="dropify @error('image') is-invalid @enderror  form-control ">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary text-light mx-5 col-6 mt-5 mb-5">أكد عمليه الدفع
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-12 ">
            <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3"> أدفع
                    الان </span></h5>
            <div class="bg-light p-30 mb-5">
                <div class="border-bottom">
                    <h6 class="mb-3"> أدفع عن طريق فودافون كاش أو انستا باي </h6>
                    <div class="d-flex justify-content-between">
                        <p> 01093014817 </p>
                        <p>فودافون كاش </p>
                    </div>

                    <div class="d-flex justify-content-between">
                        <p>ahmed.allam4452</p>
                        <p>انستا باي</p>
                    </div>


                </div>


            </div>
            <div class="mb-5" col-lg-12>
                <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">
                        لو قابلتك اي مشكله تواصل معنا عن طريق صفختنا علي الفيس بوك او الواتساب الخاص بينا </span></h5>
                <div class="bg-light p-30">



                </div>
                <button class="btn btn-block btn-primary font-weight-bold py-3">
                    <a href="https://wa.me/+201060683708" target="_blank" style="color:black">الواتساب بتاعنا
                        ♥</a>
                </button>

                <button class="btn btn-block btn-primary font-weight-bold py-3">
                    <a href="https://www.facebook.com/highacademy2?mibextid=ZbWKwL" style="color:black"> بيدج
                        الفيس بوك ♥</a>
                </button>
                <div class="col-md-6 bg-light p-5 my-4 float-end">
                    <div class="row mx-1">
                        <div class="col-md-12 d-flex justify-content-between align-items-center">
                            <p><strong>الاجمالى:</strong></p>
                            <p id="total">${{ number_format((float) str_replace(',', '', Cart::subtotal()), 2) }}</p>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<!-- Checkout End -->
@endsection

@section('js')
<script>
    function calculateTotal() {
        var total = parseFloat(document.getElementById("total").innerText.substring(1)); // يتم استخراج القيمة الإجمالية من النص وتحويلها إلى رقم عشري
        var taxValue = parseFloat(document.getElementById("tax_value").value); // يتم الحصول على القيمة المحددة من العنصر <select> وتحويلها إلى رقم عشري

        var allElement = document.getElementById("all");
        var alltotal = document.getElementById("all_total");
        allElement.innerHTML = "total: $" + (total + taxValue).toFixed(2);
        alltotal.value = (total + taxValue).toFixed(2);
    }
</script>

@endsection