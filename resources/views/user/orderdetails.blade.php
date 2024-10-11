@extends('user.layouts.master')

@section('title')
order details
@endsection

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<style>
    .container {
        font-family: "Cairo", sans-serif !important;
    }
       .bg-warning,
    .btn-primary
   {
        background-color: #e99239 !important;
    }

.text-primary,.text-warning{
    color: #e99239 !important;
}
    .btn-primary {
        border: none;
    }
</style>
<div class="container ">
    <div class="row  " style="text-align:center">
        <div class="col-md-12">
            <h5 class="section-title position-relative text-uppercase mb-3"><span class="pr-3">تفاصيل الطلب</span></h5>
        </div>
        {{-- Start order --}}

        <div class="col-12 d-flex justify-content-center align-items-center mt-2" dir="rtl">
            <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">
                <h3 class="text-end">الكتب المطلوبة</h3>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col" class="text-end">المنتج</th>
                                <th scope="col" class="text-end">السعر</th>
                                <th scope="col" class="text-end">الكمية</th>
                                <th scope="col" class="text-end">المبلغ الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderDetails as $item)
                            <tr>
                                <td class="text-end">{{ $item->products->name }}</td>
                                <td class="text-end">{{ $item->price }}</td>
                                <td class="text-end">{{ $item->amout }}</td>
                                <td class="text-end">{{ $item->total_price }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td class="text-end">الإجمالي</td>
                                <td class="text-end"></td>
                                <td class="text-end"></td>
                                <td class="text-end"><strong>{{ $order->amount }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- end order --}}


        {{-- Start order details --}}

        <div class="col-12 d-flex justify-content-center align-items-center mt-2" dir="rtl">
            <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">
                <h3 class="text-end">تفاصيل الطلب </h3>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between mb-2">
                            <strong class="title">قيمة الكتب</strong>
                            <strong>{{$order->amount}} جنيه</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between mb-2">
                            <strong class="title">رسوم الشحن</strong>
                            <strong>{{$order->delivery_fee}} جنيه</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between mb-2">
                            <strong class="title">نوع الشحن</strong>
                            <strong> @if ($order->is_fastDelivery == 1)
                                شحن سريع (لحد باب البيت)
                                @elseif ($order->is_fastDelivery == 0)
                                شحن عادي (لاقرب مكتب بريد)
                                @endif</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between mb-2">
                            <strong class="title">اجمالي المدفوع</strong>
                            <strong>{{$order->total}} جنيه</strong>
                        </li>

                        <li class="list-group-item d-flex justify-content-between mb-2">
                            <strong>وسيلة الدفع</strong>
                            <strong>{{$order->method}}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between mb-2">
                            <strong>الحساب المحول منه</strong>
                            <strong>{{$order->account}}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between mb-2">
                            <strong>العنوان</strong>
                            <strong>{{$order->address}}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between mb-2">
                            <strong>العنوان التفصيلي</strong>
                            <strong>{{$order->address2}}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between mb-2">
                            <strong>توقيت الطلب</strong>
                            <strong>{{$order->created_at}}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between mb-2">
                            <strong>حالة الطلب</strong>
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

                            @default
                            <h2 class='badge bg-secondary'>حالة غير معروفة</h2>
                            @endswitch
                        </li>

                        @if ($order->status == "success")
                        <li class="list-group-item d-flex justify-content-between mb-2">
                            <strong class="title">تاريخ الاستلام</strong>
                            @if ($order->is_fastDelivery == 1)
                            <strong class="text-success"> خلال 3 ايام</strong>
                            @else
                            <strong class="text-success">من 3 الي 5 ايام عمل</strong>
                            @endif
                </div>
                @endif
                </li>
                </ul>

            </div>
        </div>
    </div>

    {{-- end order details --}}

</div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
    integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
</script>
@endsection