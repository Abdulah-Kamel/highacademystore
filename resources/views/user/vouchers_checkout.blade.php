@extends('user.layouts.master')

@section('title')
{{$coupon->name}}
@endsection

@section('book-image',$coupon->image)


@section('content')

    <!-- Shop Detail Start -->
    <div class="container-fluid pb-5 pt-5">
        <div class="row px-xl-5 pt-5 mt-5">
            <div class="col-lg-5 mb-30">
                <div id="product-carousel" class="carousel slide" data-ride="carousel">
                    <!-- Carousel inner with images -->
                    <div class="carousel-inner bg-light">
                        <div class="carousel-item active">
                            <img class="w-100 h-100" src="{{ $coupon->image_path}}" alt="Coupon picture">
                        </div>
                       
                    </div>

                   
                </div>
            </div>

            <div class="col-lg-7 h-auto mb-30">
               <div class="h-100 bg-light p-30">
    <h2 class="fw-bold h2 text-end mb-4">{{ $coupon->name }}</h2>

    <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
            <p class='h4 font-weight-bold'>{{ $coupon->price }}</p>
            <p class='h4 font-weight-bold'>سعر الكوبون</p>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <p class='h4 font-weight-bold'>{{ $request->qty }}</p>
            <p class='h4 font-weight-bold'>العدد المطلوب</p>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <p class='h4 font-weight-bold'>{{ ($request->qty * $coupon->price) * 0.01 + 2.5 }}</p>
            <p class='h4 font-weight-bold'>رســوم الخدمة </p>
        </div>
        <?php
        $price = $request->qty * $coupon->price + (($request->qty * $coupon->price) * 0.01 + 2.5);
        ?>
        <div class="d-flex justify-content-between mb-2">
            <p class='h4 font-weight-bold'>{{ $price }}</p>
            <p class='h4 font-weight-bold'>الاجمــــــالي</p>
        </div>

        <!-- تعديل الأزرار لتصبح متجاوبة -->
        <div class="row g-2">
        <div class="col-12">
        <form id='PayForm'>
            @csrf
            <input type="hidden" name="id" value="{{ $coupon->id }}" />
            <input type="hidden" name="qty" value="{{ $request->qty }}" />
            <input type="hidden" id="payTypeInput" name="type" value="" /> <!-- إضافة الحقل -->
            <button type="button" id="PayButton" pay-type="fawry" class="btn btn-outline-info w-100 PayButton">
                <img src="https://logos-download.com/wp-content/uploads/2023/02/Fawry_Logo.png" height="30px" /> FawryPay
            </button>
        </form>
        <br>
        <!-- E Wallets -->
        <!--<div class="accordion" id="accordionExample">-->
        <!--    <div class="accordion-item">-->
        <!--        <h2 class="accordion-header">-->
        <!--            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"-->
        <!--                data-bs-target="#collapseThree" aria-expanded="false"-->
        <!--                aria-controls="collapseThree"-->
        <!--                style="padding-top:7px; padding-bottom:7px; padding-left:5px">-->
        <!--                <img src="https://i.ibb.co/27xZPJN/Picsart-24-06-10-11-22-37-597.png"-->
        <!--                    height="25px"></img>-->
        <!--                <h6 style="margin-top:11px; margin-left:10px; ">E - Wallets</h6>-->

        <!--            </button>-->
        <!--        </h2>-->
        <!--        <div id="collapseThree" class="accordion-collapse collapse"-->
        <!--            data-bs-parent="#accordionExample">-->
        <!--            <div class="accordion-body">-->
        <!--                <center>-->
        <!--                    <form id="ManualPayForm" enctype="multipart/form-data">-->
        <!--                        <input type="hidden" name="id" value="{{ $coupon->id }}" />-->
        <!--                        <input type="hidden" name="qty" value="{{ $request->qty }}" />-->
        <!--                        <input type="hidden" id="manualPayTypeInput" name="type" value="" />-->
                    
        <!--                        @csrf-->
        <!--                        <strong>-->
        <!--                            <p>قم بتحويل مبلغ-->
        <!--                                <span id="all"></span> علي رقم-->
        <!--                            </p>-->
        <!--                            <h3>-->
        <!--                                01093014817-->
        <!--                            </h3>-->
        <!--                            <p>وقم بكتابة الرقم المحول منه</p>-->
        <!--                            <input class="form-control" type="number" id="account" name="account"-->
        <!--                                pattern="\d{11}" minlength="11" maxlength="11" placeholder="الرقم المحول منه" -->
        <!--                                required />-->
        <!--                            <br>-->
        <!--                        </strong>-->
                                <!--- reciept image-->
        <!--                        <div class="mt-3" style="width: 90%;">-->
        <!--                            <div class="image-input">-->
        <!--                                <strong>-->
        <!--                                    <p>ارفع صورة من ايصال التحويل</p>-->
        <!--                                </strong>-->
        <!--                                <div class="preview">-->
        <!--                                    <img id="file-ip-1-preview">-->
        <!--                                </div>-->
        <!--                                <label for="file-ip-1">اختر صورة</label>-->
        <!--                                <input type="file" name="screenshot" id="file-ip-1" accept="image/*"-->
        <!--                                    onchange="showPreview(event);">-->
        <!--                            </div>-->
        <!--                        </div>-->
                                <!-- end recipt image-->
        <!--                        <p>سيتم مراجعة عملية الدفع يدويا واخبارك بعد تاكيد الاوردر</p>-->
                    
        <!--                        <button type="button" id="ManualPayButton" pay-type="manual" class="btn btn-outline-info w-100 PayButton">-->
        <!--                            ارفع صوره الايصال-->
        <!--                        </button>-->
        <!--                    </form>-->
        <!--                </center>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
        
    </div>
</div>


    </div>
</div>

            </div>
        </div>
    </div>
    <!-- Shop Detail End -->
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function showPreview(event) {
                if (event.target.files.length > 0) {
                    var src = URL.createObjectURL(event.target.files[0]);
                    var preview = document.getElementById("file-ip-1-preview");
                    preview.src = src;
                    preview.style.display = "block";
                }
            }
    $(document).ready(function () {
        // عند الضغط على أي زر من الأزرار
        $('#PayButton').click(function () {
            const payType = $(this).attr('pay-type'); // اجلب نوع الدفع
            $('#payTypeInput').val(payType); // قم بتعيين نوع الدفع في الحقل المخفي

            const formData = $('#PayForm').serialize(); // اجلب بيانات النموذج

            // تعطيل جميع الأزرار
            $('#PayButton').prop('disabled', true);

            // إرسال الطلب باستخدام AJAX
            $.ajax({
                url: "{{ route('user.buy.vouchers.payment') }}", // رابط الراوت
                method: "POST",
                data: formData,
                success: function (response) {
                    if (response.redirect_url) {
                        // التوجيه إلى الرابط عند النجاح
                        window.location.href = response.redirect_url;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'لم يتم الحصول على رابط الدفع. حاول مرة أخرى.',
                        });
                        // إعادة تفعيل الأزرار في حال الخطأ
                        $('#PayButton').prop('disabled', false);
                    }
                },
                error: function (xhr) {
                    // عرض الأخطاء من الرسالة JSON
                    const response = xhr.responseJSON;
                    let errorMessage = 'حدث خطأ غير متوقع. حاول مرة أخرى.'; // Default message

                    if (response) {
                        if (response.errors) { // Handle validation errors
                            errorMessage = '';
                            for (let key in response.errors) {
                                errorMessage += `${response.errors[key][0]}<br>`;
                            }
                        } else if (response.message) { // Handle server/catch block errors
                            errorMessage = response.message;
                        }
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        html: errorMessage, // عرض الأخطاء
                    });

                    // إعادة تفعيل الأزرار في حال الخطأ
                    $('#PayButton').prop('disabled', false);
                }
            });
        });
        $('#ManualPayButton').click(function () {
            const payType = $(this).attr('pay-type'); // اجلب نوع الدفع
            $('#manualPayTypeInput').val(payType); // قم بتعيين نوع الدفع في الحقل المخفي

            // const formData = $('#ManualPayForm').serialize(); // اجلب بيانات النموذج
            var form = $('#ManualPayForm')[0];
            var formData = new FormData(form);


            // تعطيل جميع الأزرار
            $('#ManualPayButton').prop('disabled', true);

            // إرسال الطلب باستخدام AJAX
            $.ajax({
                url: "{{ route('user.buy.vouchers.payment.manual') }}", // رابط الراوت
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.code == 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'نجاح',
                            text: response.message,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'لم يتم الحصول على رابط الدفع. حاول مرة أخرى.',
                        });
                        // إعادة تفعيل الأزرار في حال الخطأ
                        $('#ManualPayButton').prop('disabled', false);
                    }
                },
                error: function (xhr) {
                    // عرض الأخطاء من الرسالة JSON
                    const response = xhr.responseJSON;
                    let errorMessage = 'حدث خطأ غير متوقع. حاول مرة أخرى.'; // Default message

                    if (response) {
                        if (response.errors) { // Handle validation errors
                            errorMessage = '';
                            for (let key in response.errors) {
                                errorMessage += `${response.errors[key][0]}<br>`;
                            }
                        } else if (response.message) { // Handle server/catch block errors
                            errorMessage = response.message;
                        }
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        html: errorMessage, // عرض الأخطاء
                    });

                    // إعادة تفعيل الأزرار في حال الخطأ
                    $('#ManualPayButton').prop('disabled', false);
                }
            });
        });
    });
</script>





@endsection
