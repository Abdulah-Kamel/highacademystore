@extends('admin.layouts.master')
@section('title')
    update sliders
@endsection
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <center>
        <div class="col-12 d-flex justify-content-center align-items-center mt-2">
            <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">

                <h3>الكتب المطلوبة</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">الكوبون</th>
                            <th scope="col">السعر</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $coupon->name }}</td>
                            <td>{{ $coupon->price }} جنيه</td>
                        </tr>
                    </tbody>
                </table>

                <h3>معلومات العميل</h3>
                <table class="table">
                    <tbody>
                        <tr>
                            <th scope="row">اسم العميل</th>
                            <td>{{ $order->user_name ?? $order->user->name ?? 'غير متوفر' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">البريد الإلكتروني</th>
                            <td>{{ $order->user_email ?? $order->user->email ?? 'غير متوفر' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">رقم الهاتف</th>
                            <td>{{ $order->user_phone ?? $order->user->phone ?? 'غير متوفر' }}</td>
                        </tr>
                    </tbody>
                </table>

                <h3>تفاصيل الطلب</h3>
                <table class="table">
                    <tbody>
                        <tr>
                            <th scope="row">رقم الطلب</th>

                            <td>{{ $order->id }}</td>
                        </tr>
                        <tr>
                            <th scope="row">الكمية</th>
                            <td>{{ $order->quantity }}</td>
                        </tr>
                        <tr>
                            <th scope="row">السعر الاجمالي</th>
                            <td>{{$coupon->price * $order->quantity  }}</td>
                        </tr>
                        <tr>
                            <th scope="row">وسيلة الدفع</th>
                            <td>{{ $order->method }}</td>
                        </tr>
                        <tr>
                            <th scope="row">رقم الحساب المحول منه</th>
                            <td>{{ $order->account }}</td>
                        </tr>
                        <tr>
                            <th scope="row">صوره التحويل</th>
                            <td>
                                <img src="{{ asset('images/reciept/') . '/' . $order->image }}" alt="image" width="200px">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">حالة الطلب</th>
                            <td>
                                @switch($order->state)
                                    @case('pending')
                                        <h2 class='badge bg-warning text-dark'>منتظر التحقق</h2>
                                    @break

                                    @case('success')
                                        <h2 class='badge bg-success'>طلب ناجح</h2>
                                    @break

                                    @case('cancelled')
                                        <h2 class='badge bg-danger'>طلب ملغي</h2>
                                    @break

                                    @default
                                        <h2 class='badge bg-secondary'>حالة غير معروفة</h2>
                                @endswitch
                            </td>

                        </tr>
                        <tr>
                            <th scope="row">وقت الطلب</th>
                            <td>{{ $order->created_at }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="col-12">
                    @if ($order->state == 'pending')
                        <button class="btn btn-success col-3 confirmorder" id="accept">تاكيد الطلب</button>
                        <button class="btn btn-danger col-3 deleteorder" id="cancle">رفض الطلب</button>
                    @endif

                </div>
            </div>
        </div>
    @endsection

    @section('js')
        <!--import jquery -->
        <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>

        <script>
            /***** DELETE order ******/
            $('.deleteorder').on("click", function() {

                var itemId = {{ $order->id }};
                var csrf = $('meta[name="csrf-token"]').attr('content');
                Swal.fire({
                    title: "هل انت متأكد",
                    text: "سيتم حذف الطلب",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "متأكد",
                    cancelButtonText: "الغاء",
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "جاري الحذف",
                            text: "يتم الان الحذف ",
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                $.ajax({
                                    url: "{{ route('dashboard.voucher_order.changestate') }}",
                                    type: "POST",
                                    contentType: "application/json",
                                    data: JSON.stringify({
                                        _token: csrf,
                                        id: itemId,
                                        state: 2
                                    }),
                                    success: function(data) {
                                        Swal.fire({
                                            title: "تم الحذف",
                                            text: "تم الحذف پنجاح",
                                            icon: "success",
                                        }).then(() => {
                                            location.reload(true);

                                        });
                                    },
                                    error: function(error) {
                                        console.error("Error:", error);
                                        Swal.fire({
                                            title: "خطأ",
                                            text: "خطأ اثناء الحذف",
                                            icon: "error",
                                        });
                                    },
                                });
                            },
                        });
                    }
                });
            });




            /***** Accept order ******/
            $('.confirmorder').on("click", function() {

                var itemId = {{ $order->id }};
                var csrf = $('meta[name="csrf-token"]').attr('content');
                Swal.fire({
                    title: "هل انت متأكد",
                    text: "سيتم تاكيد الطلب",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#808000",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "متأكد",
                    cancelButtonText: "الغاء",
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "جاري التاكيد",
                            text: "يتم الان التاكيد ",
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                $.ajax({
                                    url: "{{ route('dashboard.voucher_order.changestate') }}",
                                    type: "POST",
                                    contentType: "application/json",
                                    data: JSON.stringify({
                                        _token: csrf,
                                        id: itemId,
                                        state: 1
                                    }),
                                    success: function(data) {
                                        Swal.fire({
                                            title: "تم التاكيد",
                                            text: "تم التاكيد پنجاح",
                                            icon: "success",
                                        }).then(() => {
                                            location.reload(true);

                                        });
                                    },
                                    error: function(error) {
                                        console.error("Error:", error);
                                        Swal.fire({
                                            title: "خطأ",
                                            text: "خطأ اثناء التاكيد",
                                            icon: "error",
                                        });
                                    },
                                });
                            },
                        });
                    }
                });
            });
        </script>
    @endsection
