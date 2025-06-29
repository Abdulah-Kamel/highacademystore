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
                            <th scope="col">المنتج</th>
                            <th scope="col">السعر</th>
                            <th scope="col">الكميه</th>
                            <th scope="col">المبلغ الاجملي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderDetails as $item)
                            <tr>
                                <th scope="row">{{ $item->products->short_name }}</th>
                                <td>{{ $item->price }}</td>
                                <td>{{ $item->amout }}</td>
                                <td>{{ $item->total_price }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <th scope="row">الاجمـــالي</th>
                            <td></td>
                            <td></td>
                            <td>{{ $order->amount }}</td>
                        </tr>
                    </tbody>
                </table>
                <h3>تفاصيل الطلب</h3>
                <form method="post" action="{{ route('dashboard.updateOrder', $order->id) }}">
                    @csrf
                    @method('put')
                    <table class="table">
                        <tbody>
                            <tr>
                                <th scope="row">رقم الطلب</th>

                                <td>{{ $order->id }}</td>
                            </tr>
                            <tr>
                                <th scope="row">الاسم</th>

                                <td><input class="form-control" value="{{ $order->name }}" name="name"></td>
                            </tr>
                            <tr>
                                <th scope="row">رقم الموبايل</th>
                                <td><input class="form-control" value="{{ $order->mobile }}" name="mobile"></td>
                            </tr>

                            <tr>
                                <th scope="row">العنوان</th>
                                <td><input class="form-control" value="{{ $order->address }}" name="address"></td>
                            </tr>

                            <tr>
                                <th scope="row">العنوان التفصيلي</th>
                                <td><input class="form-control" value="{{ $order->address2 }}" name="address2"></td>
                            </tr>

                            <tr>
                                <th>نوع الشحن</th>
                                <td>
                                    <select name="shipping_method" class="form-control" required>
                                        @foreach (\App\Models\ShippingMethod::all() as $method)
                                            <option value="{{ $method->id }}"
                                                {{ $order->shipping_method == $method->id ? 'selected' : '' }}>
                                                {{ $method->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>



                            <tr>
                                <th scope="row">اقرب مكتب بريد</th>
                                <td><input class="form-control" value="{{ $order->near_post }}" name="near_post"></td>
                            </tr>

                            <tr>
                                <th scope="row">قيمة الكتب</th>

                                <td>{{ $order->amount }} جنيه</td>
                            </tr>
                            <tr>
                                <th scope="row">رسوم الشحن</th>
                                <td>{{ $order->delivery_fee }} جنيه</td>
                            </tr>

                            <tr>
                                <th scope="row">اجمالي المدفوع</th>
                                <td>{{ $order->total }} جنيه</td>
                            </tr>
                            <tr>
                                <th scope="row">وسيلة الدفع</th>
                                <td>{{ $order->method }}</td>
                            </tr>
                            <tr>
                                <th scope="row">رقم الحساب المحول منه</th>
                                <td>{{ $order->account }}</td>

                            <tr>
                                <th scope="row">حالة الطلب</th>
                                <td>
                                    @switch($order->status)
                                        @case('new')
                                            <h2 class='badge bg-warning text-dark'>طلب جديد</h2>
                                        @break

                                        @case('success')
                                            <h2 class='badge bg-success'>طلب ناجح</h2>
                                        @break

                                        @case('cancelled')
                                            <h2 class='badge bg-danger'>طلب ملغي</h2>
                                        @break

                                        @case('pending')
                                            <h2 class='badge bg-info'>طلب معلق</h2>
                                        @break

                                        @case('reserved')
                                            <h2 class='badge bg-info'>طلب محجوز</h2>
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
                    <button type="submit" class="form-control "> تعديل الطلب</button>
                </form>
                <div class="col-12">
                    @if ($order->status == 'new')
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
                                    url: "{{ route('dashboard.changestate') }}",
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
                                    url: "{{ route('dashboard.changestate') }}",
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
