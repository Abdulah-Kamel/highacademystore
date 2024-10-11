@extends('user.layouts.master')

@section('title')
online shop
@endsection

@section('content')
<!-- Checkout Start -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<style>
    .hidden {
        display: none;
    }

    .container {
        font-family: "Cairo", sans-serif !important;
    }

    .image-input {
        border-radius: 15px;
        padding: 20px;
        background: #fff;
    }

    .image-input input {
        display: none;
    }

    .image-input label {
        display: block;
        width: 100px;
        height: 45px;
        line-height: 40px;
        text-align: center;
        background: #b32da1;
        color: #fff;
        font-size: 15px;
        font-family: "Open Sans", sans-serif;
        text-transform: Uppercase;
        font-weight: 600;
        border-radius: 15px;
        cursor: pointer;
    }

    .image-input img {
        width: 100%;
        display: none;

        margin-bottom: 30px;
    }

    .image-input p {
        color: #858585;
    }

    .bg-warning,
    .btn-primary {
        background-color: #e99239 !important;
    }

    .text-primary,
    text-warning {
        color: #e99239 !important;
    }

    .btn-primary {
        border: none;
    }
</style>
<div class="container ">
    <div class="row  " style="text-align:center">
        <div class="col-md-12">
            <h5 class="section-title position-relative text-uppercase mb-3"><span class="pr-3">تفاصيل
                    الدفع </span></h5>

            <div>
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif


                <div class="row col-12">

                    <div class="col-md-6 col-12">
                        <table class="table" dir="rtl" style="border:1px solid #000; border-radius:9px">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">وصف المنتج</th>
                                    <th scope="col">سعر المنتج</th>
                                    <th scope="col">العدد</th>
                                    <th scope="col">الاجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                <form id="order">
                                    @foreach (Cart::instance('shopping')->content() as $item)
                                    <tr>

                                        <input type="hidden" name="product_id[]" value="{{ $item->id }}">
                                        <input type="hidden" name="amount[]" value="{{ $item->qty }}">
                                        <input type="hidden" name="price[]" value="{{ $item->price }}">
                                        <input type="hidden" name="total_price[]" value="{{ $item->subtotal() }}">

                                        <td><a href="{{ route('user.product.show', $item->id) }}"
                                                class="nav-link text-dark">{{
                                                $item->name }}</a></td>
                                        <td> {{ number_format($item->price, 2) }} جنيه</td>
                                        <td>{{ $item->qty }}
                                        </td>
                                        <td> {{ $item->subtotal() }} جنيه</td>
                                    </tr>
                                    @endforeach
                                </form>
                                <input type="hidden" name="all_total"
                                    value="{{ number_format((float) str_replace(',', '', Cart::subtotal()), 2) }}"
                                    id="all_total">
                            </tbody>
                        </table>

                    </div>

                    {{-- START LOCATION AND TOTAL --}}
                    <div class="col-md-6 col-12">
                        <form id="location-data">
                            @csrf
                            <div class="form-group">
                                <label for="governorates">اختر المحافظة</label>
                                <select class="form-control" id="governorates" name="government"
                                    onchange="calculateTotal()">
                                    <option value="">اختر المحافظة</option>
                                    @foreach($governoratesData as $governorate)
                                    <option value="{{ $governorate['id'] }}" gov-price="{{ $governorate['price'] }}">{{
                                        $governorate['governorate_name_ar'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cities">اختر المدينة</label>
                                <select class="form-control" id="cities" name="city" disabled>
                                    <option value="">اختر المدينة</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="address">العنوان التفصيلي</label>
                                <input class="form-control" id="address" name="address"
                                    placeholder="العنوان التفصيلي" />
                            </div>
                            
                            <div class="form-group">
                                <label for="user_name">الاسم ثلاثي (كما في البطاقة)</label>
                                <input class="form-control" id="user_name" name="user_name"
                                    placeholder="اسم المستلم"  required/>
                            </div>
                            
                            <div class="form-mobile">
                                <label for="address">رقم الموبايل</label>
                                <input class="form-control" type="number" id="mobile" name="mobile" pattern="\d{11}" minlength="11" maxlength="11"
                                    placeholder="رقم موبايل المستلم"  required/>
                            </div>

                            <div class="col-12"
                                style="display:flex; justify-content:space-between;   flex-direction: row-reverse;">
                                <div style="text-align: right">
                                    <h3>مصاريف الشحن</h3>
                                    <p>التوصيل لاقرب مكتب بريد</p>
                                </div>
                                <h3 id="delivery"></h3>
                            </div>
                            
                            
                            <div class="col-12"
                                style="display:flex; justify-content:space-between;   flex-direction: row-reverse;">
                                <div style="text-align: right">
                                    <h4>شحن سريع</h4>
                                    <p>التوصيل لباب البيت (يضاف 30 جنيه)</p>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="flexSwitchCheckDefault" name="fast_ship">
                                </div>
                            </div>
                            <div class="col-12"
                                style="display:flex; justify-content:space-between;   flex-direction: row-reverse;">
                                <h3>الاجمالي</h3>
                                <h3 id="all"></h3>
                            </div>
                        </form>
                    </div>
                    {{-- END LOCATION AND TOTAL --}}

                    {{-- START PAYMENT --}}
                    <div class="accordion" id="accordionExample">



                        {{-- <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne"
                                    style="padding-top:7px; padding-bottom:7px; padding-left:5px">
                                    <img src="https://i.ibb.co/Jddgqxx/Picsart-24-06-09-18-53-16-771.png"
                                        height="30px"></img>

                                    <h6 style="margin-top:11px; margin-left:10px; ">Credit Card</h6>
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <center>
                                        <h4>يتم اضافة 4% رسوم اضافية للدفع بالفيزا</h4>
                                        <button class="btn btn-success" id="credit_card">اضغط لاكمال عملية
                                            الدفع</button>
                                    </center>
                                </div>
                            </div>
                        </div>
                        --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour"
                                    style="padding-top:7px; padding-bottom:7px; padding-left:5px">
                                    <img src="https://www.pikpng.com/pngl/m/577-5776412_fawry-pay-logo-fawry-clipart.png"
                                        height="40px"></img>
                                    <h6 style="margin-top:11px; margin-left:10px; ">Fawry Pay</h6>

                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <center>

                                        <h4>يتم اضافة رسوم 1% + 2.5 جنيه للدفع بفوري باي</h4>
                                        <button class="btn btn-success" id="fawry">اضغط لاكمال عملية
                                            الدفع</button>

                                    </center>
                                </div>
                            </div>
                        </div>
                        {{--
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"
                                    style="padding-top:7px; padding-bottom:7px; padding-left:5px">
                                    <img src="https://i.ibb.co/zn47xr3/Picsart-24-06-09-19-03-30-701.png"
                                        height="13px"></img>
                                    <h6 style="margin-top:11px; margin-left:10px; ">insta pay</h6>
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <center>
                                        <form id="instapay-form">
                                            @csrf
                                            <strong>
                                                <p>قم بتحويل مبلغ
                                                    <span id="all"></span> علي يوزر
                                                </p>
                                                <h3>ahmed.allam4452</h3>
                                                <p>وقم بكتابة اليوزر النيم المحول منه</p>
                                                <input class="form-control" name="account"
                                                    placeholder="يوزر انستا باي المحول منه" dir="rtl"></input>
                                                <input type="hidden" name="method" value="instapay" />
                                                <br>
                                            </strong>
                                            <!--- reciept image-->
                                            <div class="mt-3" style="width: 90%;">
                                                <div class="image-input">
                                                    <strong>
                                                        <p>ارفع صورة من ايصال التحويل</p>
                                                    </strong>
                                                    <div class="preview">
                                                        <img id="file-ip-1-preview">
                                                    </div>
                                                    <label for="file-ip-1">اختر صورة</label>
                                                    <input type="file" name="screenshot" id="file-ip-1" accept="image/*"
                                                        onchange="showPreview(event);">
                                                </div>
                                            </div>
                                            <!-- end recipt image-->
                                            <p>سيتم مراجعة عملية الدفع يدويا واخبارك بعد تاكيد الاوردر</p>

                                            <button class="btn btn-success" id="insta-pay">تاكيد الدفع</button>
                                        </form>


                                    </center>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree"
                                    style="padding-top:7px; padding-bottom:7px; padding-left:5px">
                                    <img src="https://i.ibb.co/27xZPJN/Picsart-24-06-10-11-22-37-597.png"
                                        height="40px"></img>
                                    <h6 style="margin-top:11px; margin-left:10px; ">E - Wallets</h6>

                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <center>
                                        <form id="ewallets-form">
                                            @csrf
                                            <strong>
                                                <p>قم بتحويل مبلغ <span id="all"></span> جنيه علي رقم</p>
                                                <h3>01093014817</h3>
                                                <p>وقم بكتابة الرقم المحول منه</p>
                                                <input class="form-control" placeholder="رقم الموبايل المحول منه"
                                                    dir="rtl" name="account"></input>
                                                <input type="hidden" name="method" value="E-Wallets" />
                                                <br>
                                            </strong>
                                            <!--- reciept image-->
                                            <div class="mt-3" style="width: 90%;">
                                                <div class="image-input">
                                                    <strong>
                                                        <p>ارفع صورة من ايصال التحويل</p>
                                                    </strong>
                                                    <div class="preview">
                                                        <img id="file-ip-2-preview">
                                                    </div>
                                                    <label for="file-ip-2">اختر صورة</label>
                                                    <input type="file" name="screenshot" id="file-ip-2" accept="image/*"
                                                        onchange="showPreview2(event);">
                                                </div>
                                            </div>
                                            <!-- end recipt image-->
                                            <p>سيتم مراجعة عملية الدفع يدويا واخبارك بعد تاكيد الاوردر</p>
                                            <button class="btn btn-success" id="ewallets">تاكيد الدفع</button>
                                    </center>
                                </div>
                            </div>
                        </div>
                        --}}
                    </div>
                    {{-- END PAYMENT --}}


                </div>
                </form>
            </div>
        </div>
        <div class="col-12 mt-5">

            <div class="mb-5 col-12">
                <h5 class="section-title position-relative text-uppercase mb-3"><span class=" pr-3">
                        لو قابلتك اي مشكله تواصل معنا عن طريق صفختنا علي الفيس بوك او الواتساب الخاص بينا </span></h5>

                <button class="btn btn-block btn-success font-weight-bold col-6 ">
                    <a href="https://wa.me/+201060683708" target="_blank" class="text-white"
                        style="text-decoration: none">الواتساب بتاعنا
                    </a>
                </button>

                <button class="btn btn-block font-weight-bold col-6" style=" background-color:	#1877F2;">
                    <a href="https://www.facebook.com/highacademy2?mibextid=ZbWKwL" class="text-white"
                        style="text-decoration: none; background-color:	#1877F2;"> بيدج
                        الفيس بوك</a>
                </button>
            </div>

        </div>
    </div>
</div>
<p id="total" style="display: none">${{ number_format((float) str_replace(',', '', Cart::subtotal()), 2) }}</p>


<!-- Checkout End -->
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
    integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
</script>


<!-- Import Sweet Alert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.6/dist/sweetalert2.all.min.js"></script>

<script>
    function calculateTotal() {
        var total = parseFloat(document.getElementById("total").innerText.substring(1)); // يتم استخراج القيمة الإجمالية من النص وتحويلها إلى رقم عشري
        var governoratesSelect = document.getElementById("governorates");
        var selectedOption = governoratesSelect.selectedOptions[0];
        var taxValue = parseFloat(selectedOption.getAttribute('gov-price'));
        var bookFee = {{ Cart::instance('shopping')->count() }} * 15;
        var fastShippingFee = document.getElementById("flexSwitchCheckDefault").checked ? 30 : 0; // تحقق من حالة الـ checkbox

        var allElements = document.querySelectorAll("#all");
        var deliveryElements = document.querySelectorAll("#delivery");

        var totalDelivery = taxValue + bookFee + fastShippingFee;
        var grandTotal = total + totalDelivery;

        allElements.forEach(function(element) {
            element.innerHTML = "جنيه " + grandTotal.toFixed(2);
        });

        deliveryElements.forEach(function(element) {
            element.innerHTML = "جنيه " + totalDelivery.toFixed(2);
        });
    }

    document.getElementById("flexSwitchCheckDefault").addEventListener('change', calculateTotal);
</script>



<script>
    var accordion = document.getElementById('accordionExample');
    accordion.classList.add('hidden');
    document.addEventListener('DOMContentLoaded', function() {
        const citiesData = @json($citiesData);

        document.getElementById('governorates').addEventListener('change', function () {
            const governorateId = this.selectedOptions[0].value;
            const citiesSelect = document.getElementById('cities');

            // تفريغ القائمة الثانية
            citiesSelect.innerHTML = '<option value="">اختر المدينة</option>';

            if (governorateId) {
                citiesSelect.disabled = false;
                const filteredCities = citiesData.filter(city => city.governorate_id === governorateId);

                filteredCities.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.id;
                    option.textContent = city.city_name_ar;
                    citiesSelect.appendChild(option);
                });
            } else {
                citiesSelect.disabled = true;
            }

            // تحديث حالة الكورديون بعد تغيير المحافظة
            updateFormState();
        });

        document.getElementById('cities').addEventListener('change', function () {
            // تحديث حالة الكورديون بعد تغيير المدينة
            updateFormState();
        });

        function updateFormState() {
            var governorate = document.getElementById('governorates').value;
            var city = document.getElementById('cities').value;
            var accordion = document.getElementById('accordionExample');

            if (governorate === "" || city === "") {
                accordion.classList.add('hidden');
            } else {
                accordion.classList.remove('hidden');
            }
        }
    });
</script>

<script>
    function disablebtn(btnSelector) {
var btn = $(btnSelector);
btn.prop('disabled', true);
btn.text('انتظر قليلا ...');
}

function enablebtn(btnSelector) {
var btn = $(btnSelector);
btn.prop('disabled', false);
btn.text('تاكيد الدفع');
}

function notialert(AlertType, AlertText, redirectLink) {
Swal.fire({
    text: AlertText,
    icon: AlertType,
    buttonsStyling: false,
    confirmButtonText: "Ok",
    customClass: {
        confirmButton: "btn btn-primary"
    }
}).then((result) => {
    if (result.isConfirmed && AlertType === 'success' && redirectLink) {
      if(redirectLink == "refresh"){
        location.reload(true);
      }else{
        window.location.href = redirectLink;
      }
    }
});
}



$(document).on('submit', function(event) {
event.preventDefault();
});

//credit card

$('#credit_card').click(function() {



disablebtn("#credit_card");
var cardForm = new FormData($('#order')[0]);
var LocationForm = new FormData($('#location-data')[0]);

for (var pair of LocationForm.entries()) {
    cardForm.append(pair[0], pair[1]);
}

$.ajax({
url: "{{route('cards.pay')}}",
type: "post",
data: cardForm,
processData: false,
contentType: false,
success: function(response) {
if(response.url){
 window.location.href = response.url;
}else{
    enablebtn("#credit_card");
     notialert('error', "خطا اثناء التنفيذ");
}
},
error: function(jqXHR, textStatus, errorThrown) {
    enablebtn("#credit_card");
    var errorMessage = jqXHR.responseJSON && jqXHR.responseJSON.msg ? jqXHR.responseJSON.msg : 'خطا اثناء التنفيذ';
    notialert('error', errorMessage);
}
});

});

//fawry

$('#fawry').click(function() {



disablebtn("#fawry");
var cardForm = new FormData($('#order')[0]);
var LocationForm = new FormData($('#location-data')[0]);

for (var pair of LocationForm.entries()) {
    cardForm.append(pair[0], pair[1]);
}

$.ajax({
url: "{{route('fawry.pay')}}",
type: "post",
data: cardForm,
processData: false,
contentType: false,
success: function(response) {
    if(response.url){
 window.location.href = response.url;
}else{
    enablebtn("#fawry");
     notialert('error', "خطا اثناء التنفيذ");
}
},
error: function(jqXHR, textStatus, errorThrown) {
    enablebtn("#fawry");
    var errorMessage = jqXHR.responseJSON && jqXHR.responseJSON.msg ? jqXHR.responseJSON.msg : 'خطا اثناء التنفيذ';
    notialert('error', errorMessage);
}
});

});

// INSTAPAY FORM

$('#insta-pay').click(function() {



disablebtn("#insta-pay");
var cardForm = new FormData($('#order')[0]);
var formData = new FormData($('#instapay-form')[0]);
var LocationForm = new FormData($('#location-data')[0]);

for (var pair of formData.entries()) {
    cardForm.append(pair[0], pair[1]);
}

for (var pair of LocationForm.entries()) {
    cardForm.append(pair[0], pair[1]);
}

$.ajax({
url: "{{route('manual.pay')}}",
type: "post",
data: cardForm,
processData: false,
contentType: false,
success: function(response) {
    enablebtn("#insta-pay");
    notialert('success', response.msg, '/');
},
error: function(jqXHR, textStatus, errorThrown) {
    enablebtn("#insta-pay");
    var errorMessage = jqXHR.responseJSON && jqXHR.responseJSON.msg ? jqXHR.responseJSON.msg : 'خطا اثناء التنفيذ';
    notialert('error', errorMessage);
}
});

});


// ewallets FORM

$('#ewallets').click(function() {



disablebtn("#ewallets");
var cardForm = new FormData($('#order')[0]);
var formData = new FormData($('#ewallets-form')[0]);
var LocationForm = new FormData($('#location-data')[0]);

for (var pair of formData.entries()) {
    cardForm.append(pair[0], pair[1]);
}

for (var pair of LocationForm.entries()) {
    cardForm.append(pair[0], pair[1]);
}

$.ajax({
url: "{{route('manual.pay')}}",
type: "post",
data: cardForm,
processData: false,
contentType: false,
success: function(response) {
    enablebtn("#ewallets");
    notialert('success', response.msg, '/');
},
error: function(jqXHR, textStatus, errorThrown) {
    enablebtn("#ewallets");
    var errorMessage = jqXHR.responseJSON && jqXHR.responseJSON.msg ? jqXHR.responseJSON.msg : 'خطا اثناء التنفيذ';
    notialert('error', errorMessage);
}
});

});
</script>

<script type="text/javascript">
    function showPreview(event) {
            if (event.target.files.length > 0) {
                var src = URL.createObjectURL(event.target.files[0]);
                var preview = document.getElementById("file-ip-1-preview");
                preview.src = src;
                preview.style.display = "block";
            }
        }

        function showPreview2(event) {
            if (event.target.files.length > 0) {
                var src = URL.createObjectURL(event.target.files[0]);
                var preview = document.getElementById("file-ip-2-preview");
                preview.src = src;
                preview.style.display = "block";
            }
        }

</script>

@endsection
