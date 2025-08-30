@extends('user.layouts.master')
@section('title')
    صفحه الدفع
@endsection

@php
    $discountSetting = DB::table('discount_settings')->first();
@endphp


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

        .edit_btn {
            background-color: #007bff;
            color: #fff;
        }

        .edit_btn:hover {
            background-color: #0056b3;
            color: #fff;
        }

        .bg-warning,
        .btn-primary {
            background-color: #e99239 !important;
        }

        /* ... your existing styles ... */
    </style>

    <div class="container-fluid pt-5">
        <div class="container">
            <div class="row pt-5 mt-5" style="text-align:center">
                <div class="col-md-12">
                    <h5 class="section-title position-relative text-uppercase mb-3">
                        <span class="pr-3">تفاصيل الدفع</span>
                    </h5>

                    <!-- Display a success flash, if any -->
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <!-- Display an error flash, if any -->
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row col-12">
                        <div class="col-md-6 col-12">
                            <table class="table table-hover border" dir="rtl">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">وصف المنتج</th>
                                        <th scope="col">سعر المنتج</th>
                                        {{-- <th scope="col">لون المنتج</th>
                                        <th scope="col">حجم المنتج</th> --}}
                                        <th scope="col">العدد</th>
                                        {{-- <th scope="col">الاجمالي</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    <form id="order">
                                        @foreach (Cart::instance('shopping')->content() as $item)
                                            <tr>
                                                <input type="hidden" name="product_id[]" value="{{ $item->id }}">
                                                <input type="hidden" name="amount[]" value="{{ $item->qty }}">
                                                <input type="hidden" name="price[]" value="{{ $item->price }}">
                                                <input type="hidden" name="size[]" value="{{ $item->options->size }}">
                                                <input type="hidden" name="color[]" value="{{ $item->options->color }}">
                                                <input type="hidden" name="total_price[]" value="{{ $item->subtotal() }}">
                                                <td>
                                                    <a href="{{ route('user.product.show', $item->id) }}"
                                                        class="nav-link text-dark">
                                                        {{ $item->name }}
                                                    </a>
                                                </td>
                                                <td>{{ number_format($item->price, 2) }} جنيه</td>
                                                {{-- <td>{{ $item->options->color }} </td>
                                                <td>{{ $item->options->size }} </td> --}}
                                                <td>{{ $item->qty }}</td>
                                                {{-- <td>{{ $item->subtotal() }} جنيه</td> --}}
                                            </tr>
                                        @endforeach
                                    </form>
                                </tbody>
                            </table>
                        </div>

                        {{-- START LOCATION AND TOTAL --}}
                        <div class="col-md-6 col-12">
                            @php
                                // Original subtotal as float
                                $cartSubtotal = (float) str_replace(',', '', Cart::subtotal());

                                // Check if there's a discount in the session
$discountAmount = session()->has('applied_discount')
    ? session('applied_discount')['amount']
                                    : 0;

                                // Subtotal after discount (before shipping)
                                $preShippingTotal = max($cartSubtotal - $discountAmount, 0);
                            @endphp

                            {{-- Hidden input: discounted subtotal (before shipping) --}}
                            <input type="hidden" name="all_total" value="{{ $preShippingTotal }}" id="all_total">

                            {{-- Show the discount line if discount is applied --}}
                            @if ($discountAmount > 0)
                                <div class="alert alert-success" style="text-align: center;">
                                    تم تطبيق الخصم بنجاح! <br>
                                    رمز الكوبون: <strong>{{ session('applied_discount')['code'] }}</strong> <br>
                                    قيمة الخصم: <strong>{{ $discountAmount }} جنيه</strong>
                                </div>
                            @endif


                            <form id="location-data">
                                @csrf
                                <div class="form-group">
                                    <label for="governorates">اختر المحافظة</label>
                                    <select class="form-control" id="governorates" name="government"
                                        onchange="calculateTotal()">
                                        <option value="">اختر المحافظة</option>
                                        @foreach ($governoratesData as $governorate)
                                            <option value="{{ $governorate['id'] }}"
                                                gov-price="{{ $governorate['price'] }}">
                                                {{ $governorate['governorate_name_ar'] }}
                                            </option>
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
                                    <input class="form-control" id="user_name" name="user_name" placeholder="اسم المستلم"
                                        required />
                                </div>

                                <div class="form-mobile">
                                    <label for="mobile">رقم الموبايل</label>
                                    <input class="form-control" type="number" id="mobile" name="mobile"
                                        pattern="\d{11}" minlength="11" maxlength="11" placeholder="رقم موبايل المستلم"
                                        required />
                                </div>
                                <div class="form-mobile">
                                    <label for="temp_mobile">رقم الموبايل الاحتياطي</label>
                                    <input class="form-control" type="number" id="temp_mobile" name="temp_mobile"
                                        pattern="\d{11}" minlength="11" maxlength="11" placeholder="رقم موبايل الاحتياطي"
                                        required />
                                </div>
                                {{-- <div class="form-group">
                                    <label for="near_post">اسم اقرب مكتب بريد</label>
                                    <input class="form-control" id="near_post" name="near_post"
                                        placeholder="اسم اقرب مكتب بريد" />
                                </div> --}}

                            </form>
                            <!-- [ADDED] Discount Form here in the checkout -->
                            @if ($discountSetting && $discountSetting->discount_enabled)
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <form action="{{ route('user.checkout.applyDiscount') }}" method="POST"
                                            class="d-flex">
                                            @csrf
                                            <input type="text" name="coupon_code" class="form-control"
                                                placeholder="ادخل كود الخصم"
                                                @if (session()->has('applied_discount')) value="{{ session('applied_discount')['code'] }}" @endif>
                                            <button type="submit" class="btn edit_btn ms-2">
                                                تطبيق
                                            </button>
                                        </form>

                                    </div>
                                    {{-- <div class="col-md-12 mt-2">
                            @if (session()->has('applied_discount'))
                                <form action="{{ route('user.checkout.removeDiscount') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        إزالة الخصم
                                    </button>
                                </form>
                            @endif
                        </div> --}}
                                </div>
                            @endif



                            <div class="col-12 mb-4 mt-4 p-3 bg-light rounded-3 shadow border border-primary">
                                <div class="form-group">
                                    <label for="shipping_method" class="h5 text-primary fw-bold">اختر طريقة
                                        الاستلام</label>
                                    <select class="form-control" id="shipping_method" name="shipping_method_id">
                                        <option value="">اختر طريقة الشحن</option>
                                        {{-- Shipping options will be populated by JavaScript --}}
                                    </select>
                                    <!-- Shipping method details -->
                                    <div id="shipping_info" class="mt-3" style="display: none;">
                                        <p id="fee_row" class="fs-5"><strong>رسوم الخدمه:</strong> + <span
                                                id="shipping_fee">0.00</span> جنيه</p>
                                        <p id="address_row" class="fs-5"><strong>العنوان:</strong> <span
                                                id="shipping_address">---</span></p>
                                        <p id="phones_row" class="fs-5"><strong>أرقام الهاتف:</strong> <span
                                                id="shipping_phones">---</span></p>
                                    </div>

                                </div>
                            </div>

                            <div id="nearpost_wrapper" class="col-12 mt-4 mb-5" style="display: none; margin-top: 1rem;">
                                <label for="near_post" class="fs-6"><strong>اسم أقرب مكتب بريد</strong></label>
                                <input type="text" id="near_post" name="near_post" class="form-control"
                                    placeholder="ادخل اسم مكتب البريد" />
                            </div>

                            <!-- [END ADDED] -->
                            <div class="col-12"
                                style="display:flex; justify-content:space-between; flex-direction: row-reverse;">
                                <div style="text-align: right">
                                    <h3>مصاريف الشحن</h3>
                                </div>
                                <h3 id="delivery"></h3>
                            </div>
                            <div class="col-12"
                                style="display:flex; justify-content:space-between; flex-direction: row-reverse;">
                                <h3>الاجمالي</h3>
                                <h3 id="all"></h3>
                            </div>
                        </div>
                        {{-- END LOCATION AND TOTAL --}}



                        {{-- START PAYMENT --}}
                        <div class="accordion hidden" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour"
                                        style="padding-top:7px; padding-bottom:7px; padding-left:5px">
                                        <img src="https://logos-download.com/wp-content/uploads/2023/02/Fawry_Logo.png"
                                            height="40px">
                                        <h6 style="margin-top:11px; margin-left:10px;">Fawry Pay</h6>
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <center>
                                            <h4>يتم اضافة رسوم 1% + 2.5 جنيه للدفع بفوري باي</h4>
                                            <button type="button" class="btn btn-success" id="fawry">اضغط لاكمال
                                                عملية
                                                الدفع</button>
                                        </center>
                                    </div>
                                </div>
                            </div>
                            {{-- More payment items ... --}}
                        </div>
                        {{-- END PAYMENT --}}
                    </div>

                    <div class="col-12 mt-5">
                        <div class="mb-5 col-12">
                            <h5 class="section-title position-relative text-uppercase mb-3">
                                <span class=" pr-3">
                                    لو قابلتك اي مشكله تواصل معنا عن طريق صفحتنا علي الفيس بوك او الواتساب الخاص بنا
                                </span>
                            </h5>

                            <button class="btn btn-block btn-success font-weight-bold col-6">
                                <a href="https://wa.me/+201060683708" target="_blank" class="text-white"
                                    style="text-decoration: none">
                                    الواتساب بتاعنا
                                </a>
                            </button>

                            <button class="btn btn-block font-weight-bold col-6" style="background-color: #1877F2;">
                                <a href="https://www.facebook.com/highacademy2?mibextid=ZbWKwL" class="text-white"
                                    style="text-decoration: none;">
                                    بيدج الفيس بوك
                                </a>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hidden paragraph to store discounted subtotal for JavaScript --}}
        <p id="total" style="display: none;">{{ $preShippingTotal }}</p>
        <!-- Checkout End -->
    @endsection

    @section('js')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

        <!-- Import Sweet Alert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.6/dist/sweetalert2.all.min.js"></script>

        @php
            $productTax = 0;
            $productSlowTax = 0;
            foreach (Cart::instance('shopping')->content() as $product) {
                $productTax += $product->qty * $product->model->tax;
                $productSlowTax += $product->qty * $product->model->slowTax;
            }
        @endphp

        <script>
            // Wait for the document to be fully loaded
            document.addEventListener('DOMContentLoaded', function() {

                // --- Get data and elements from the page ---
                const allShippingMethods = @json($shippingMethods);
                const governoratesSelect = document.getElementById('governorates');
                const shippingSelect = document.getElementById('shipping_method');


                // new: update the details panel
                function updateShippingInfo() {
                    const sel = shippingSelect.value;
                    const m = allShippingMethods.find(x => x.id == sel);
                    const info = document.getElementById('shipping_info');
                    const wrapper = document.getElementById('nearpost_wrapper');
                    const npInput = document.getElementById('near_post');
                    const fee_row = document.getElementById('fee_row');

                    if (!m) {
                        // nothing selected → hide the whole panel
                        wrapper.style.display = 'none';
                        info.style.display = 'none';

                        return;
                    }


                    // now toggle the near-post field only for "post" type
                    if (m.type === 'post') {
                        wrapper.style.display = 'block';
                        npInput.required = true;
                    } else {
                        wrapper.style.display = 'none';
                        // also clear it to avoid stale value
                        npInput.required = false;
                        npInput.value = ''; // clear out any old value
                    }

                    document.getElementById('shipping_fee').innerText = Number(m.fee).toFixed(2);

                    // show/hide address & phones rows
                    const addr = document.getElementById('address_row');
                    const phone = document.getElementById('phones_row');
                    if (m.type === 'branch') {
                        fee_row.style.display = 'block';

                        addr.style.display = 'block';
                        phone.style.display = 'block';
                        document.getElementById('shipping_address').innerText = m.address;
                        document.getElementById('shipping_phones').innerText = (m.phones || []).join(', ');
                    } else {
                        fee_row.style.display = 'none';
                        addr.style.display = 'none';
                        phone.style.display = 'none';
                    }

                    // finally show the container & recalc
                    info.style.display = 'block';
                    calculateTotal();
                }



                function updateShippingOptions() {
                    const prev = shippingSelect.value;
                    shippingSelect.innerHTML = '<option value="">اختر طريقة الشحن</option>';
                    allShippingMethods.forEach(m => {
                        const isPickup = m.type === 'branch';
                        if (!isPickup || (governoratesSelect.value && m.government == governoratesSelect
                                .value)) {
                            const opt = document.createElement('option');
                            opt.value = m.id;
                            const label = {
                                branch: 'استلام من المكتبة',
                                home: 'شحن لباب البيت',
                                post: 'شحن لمكتب بريد'
                            } [m.type] + ' — ' + m.name;
                            opt.textContent = label;
                            shippingSelect.appendChild(opt);
                        }
                    });
                    if ([...shippingSelect.options].some(o => o.value === prev)) {
                        shippingSelect.value = prev;
                    }
                    // recalc & refresh info
                    calculateTotal();
                    updateShippingInfo();
                }

                // hook them up:
                governoratesSelect.addEventListener('change', updateShippingOptions);
                shippingSelect.addEventListener('change', updateShippingInfo);

                // initial bootstrap:
                updateShippingOptions();
            });
        </script>

        <script>
            const shippingMethods = @json($shippingMethods);
            const governoratesData = @json($governoratesData);
            const shippingSelect = document.getElementById('shipping_method');

            function setupPaymentHandler(buttonId, routeUrl, extraFormId = null) {
                $(buttonId).click(function() {
                    const btn = $(this);
                    const originalText = btn.text(); // Store original button text
                    btn.prop('disabled', true).text('جاري المعالجة...');

                    const selectedMethod = shippingMethods
                        .find(m => m.id == shippingSelect.value);
                    const nearPostInput = document.getElementById('near_post');

                    if (selectedMethod?.type === 'post' &&
                        !nearPostInput.value.trim()
                    ) {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'يرجى إدخال اسم أقرب مكتب بريد قبل المتابعة'
                        });
                        btn.prop('disabled', false).text(originalText); // Restore original text
                        return; // abort
                    }

                    var formData = new FormData();

                    // اجمع بيانات الطلب
                    $('#order').serializeArray().forEach(function(field) {
                        formData.append(field.name, field.value);
                    });

                    // اجمع بيانات الشحن
                    formData.append('shipping_method', $('#shipping_method').val());
                    $('#location-data').serializeArray().forEach(function(field) {
                        formData.append(field.name, field.value);
                    });
                    // 3) always append near_post now:
                    formData.append('near_post', nearPostInput.value.trim());


                    if (extraFormId) {
                        $(extraFormId).serializeArray().forEach(function(field) {
                            formData.append(field.name, field.value);
                        });
                        var fileInput = $(extraFormId).find('input[type="file"]');
                        if (fileInput.length > 0 && fileInput[0].files.length > 0) {
                            formData.append(fileInput.attr('name'), fileInput[0].files[0]);
                        }
                    }

                    $.ajax({
                        url: routeUrl,
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.url) {
                                window.location.href = response.url;
                            } else if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.msg,
                                    showConfirmButton: false,
                                });
                                setTimeout(function() {
                                    window.location.href = '/';
                                }, 500);
                            } else {
                                btn.prop('disabled', false).text(originalText); // Restore original text
                                Swal.fire({
                                    icon: 'error',
                                    title: response.msg || 'An unknown error occurred.',
                                    showConfirmButton: false,
                                });
                            }
                        },
                        error: function(jqXHR) {
                            btn.prop('disabled', false).text(originalText); // Restore original text
                            var errorMessage = jqXHR.responseJSON && jqXHR.responseJSON.msg ?
                                jqXHR.responseJSON.msg :
                                'خطا اثناء التنفيذ';
                            Swal.fire({
                                icon: 'error',
                                title: errorMessage,
                                showConfirmButton: false,
                            });
                        }
                    });
                });
            }

            setupPaymentHandler('#credit_card', "{{ route('cards.pay') }}");
            setupPaymentHandler('#fawry', "{{ route('fawry.pay') }}");
            setupPaymentHandler('#wallet', "{{ route('fawry.wallet.pay') }}", '#ewallets-form');
            setupPaymentHandler('#insta-pay', "{{ route('manual.pay') }}", '#instapay-form');

            function calculateTotal() {
                const discountedSubtotal = parseFloat(document.getElementById("total").innerText.trim()) || 0;
                const shippingMethodId = parseInt(document.getElementById("shipping_method").value);
                const governorateId = parseInt(document.getElementById("governorates").value);

                const method = shippingMethods.find(m => m.id === shippingMethodId);
                let grandTotal = discountedSubtotal;

                // إيجاد طريقة الشحن من الـ ID
                if (!method) return;
                let deliveryFee = Number(method.fee);
                // لو استلام من المكتبة → التوصيل = صفر
                // if (method.type === 'branch') {
                //     deliveryFee = 0;
                // } else {
                // نحسب سعر المحافظة (لو متاح)
                const gov = governoratesData.find(g => g.id == governorateId);
                const govPrice = Number(gov?.price);

                const taxFast = {{ $productTax }};
                const taxNormal = {{ $productSlowTax }};
                console.log("deliveryFee", deliveryFee, "govPrice", govPrice, "taxFast", taxFast, "taxNormal", taxNormal);
                if (method.type === 'home') {
                    deliveryFee += govPrice + taxFast;
                } else if (method.type === 'post') {
                    deliveryFee += govPrice + taxNormal;
                }
                // }

                grandTotal = discountedSubtotal + deliveryFee;

                // تحديث الواجهة
                document.querySelectorAll("#delivery").forEach(el => {
                    el.innerText = `جنيه ${deliveryFee.toFixed(2)}`;
                });
                document.querySelectorAll("#all").forEach(el => {
                    el.innerText = `جنيه ${grandTotal.toFixed(2)}`;
                });
            }


            document.getElementById('shipping_method').addEventListener('change', function() {
                calculateTotal();
            });
        </script>
        <?php
        $addressId;
        $citiId;
        if ($orders !== null) {
            foreach ($governoratesData as $governorate) {
                if ($orders->governorate == $governorate['governorate_name_ar']) {
                    $addressId = $governorate['id'];
                }
            }
            foreach ($citiesData as $cities) {
                if ($orders->city == $cities['city_name_ar']) {
                    $citiId = $cities['id'];
                }
            }
        }
        ?>
        <script>
            var accordion = document.getElementById('accordionExample');
            accordion.classList.add('hidden');

            document.addEventListener('DOMContentLoaded', function() {
                const citiesData = @json($citiesData);

                document.getElementById('governorates').addEventListener('change', function() {
                    const governorateId = this.value;
                    const citiesSelect = document.getElementById('cities');
                    citiesSelect.innerHTML = '<option value="">اختر المدينة</option>';

                    if (governorateId) {
                        citiesSelect.disabled = false;
                        const filteredCities = citiesData.filter(city => city.governorate_id == governorateId);
                        filteredCities.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.id;
                            option.textContent = city.city_name_ar;
                            citiesSelect.appendChild(option);
                        });
                    } else {
                        citiesSelect.disabled = true;
                    }

                    updateFormState();
                    calculateTotal();
                });

                document.getElementById('cities').addEventListener('change', function() {
                    updateFormState();
                    calculateTotal();
                });

                function updateFormState() {
                    const governorate = document.getElementById('governorates').value;
                    const city = document.getElementById('cities').value;
                    const shippingMethodId = document.getElementById('shipping_method').value;
                    const selectedMethod = shippingMethods.find(m => m.id == shippingMethodId);

                    let show = false;
                    if (selectedMethod) {
                        // Show for branch pickup, or for other types if location is selected
                        if (selectedMethod.type === 'branch') {
                            show = true;
                        } else if (governorate && city) {
                            show = true;
                        }
                    }

                    if (show) {
                        accordion.classList.remove('hidden');
                    } else {
                        accordion.classList.add('hidden');
                    }
                }

                calculateTotal();
                updateFormState();
                document.getElementById('shipping_method').addEventListener('change', updateFormState);
            });
        </script>

        @if ($orders !== null)
            <script>
                Swal.fire({
                    title: "سهلناها عليك, جبنا بيانات الشحن من طلبك السابق",
                    icon: "success",
                    confirmButtonText: "حسنا",
                    showCloseButton: true
                }).then((result) => {
                    document.getElementById('governorates').value = "{{ $addressId ?? '' }}";
                    document.getElementById('governorates').dispatchEvent(new Event('change'));
                    document.getElementById('cities').value = "{{ $citiId ?? '' }}";
                    document.getElementById('cities').dispatchEvent(new Event('change'));
                    document.getElementById('address').value = "{{ $orders->address }}";
                    document.getElementById('user_name').value = "{{ $orders->name }}";
                    document.getElementById('mobile').value = "{{ $orders->mobile }}";
                    document.getElementById('temp_mobile').value = "{{ $orders->temp_mobile }}";
                    document.getElementById('near_post').value = "{{ $orders->near_post }}";
                });
            </script>
        @endif

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