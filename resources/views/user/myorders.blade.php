@extends('user.layouts.master')

@section('title')
online shop
@endsection

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<style>
    .hidden {
        display: none;
    }

    .container {
        font-family: "Cairo", sans-serif !important;
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
<div class="container">
    <div class="row text-center">
        <div class="col-md-12">
            <h5 class="section-title position-relative text-uppercase mb-3"><span class="pr-3">طلبــــــاتي</span></h5>
        </div>
        {{-- Start orders --}}
        <div class="row mx-auto" style="width: 100%;">
            @if (count($orders) == 0)
            <h3 class="mt-5">لا يوجد اي طلبات لعرضها</h3>
            @endif

            @foreach ($orders as $o)
            <div class="col-md-6">

                <div class="card mt-3 mx-auto" style="width: 100%;" dir="rtl">
                    <div class="card-header text-end">
                        <div class="d-flex justify-content-between mb-2">
                            <strong class="title">
                                {{$o->code}}
                            </strong>
                            @switch($o->status)
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
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <strong class="title">عنوان الشحن</strong>
                            <strong>{{$o->address}}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <strong class="title">المبلغ المدفوع</strong>
                            <strong>{{$o->total}}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <strong class="title">طريقة الدفع</strong>
                            <strong>{{$o->method}}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <strong class="title">توقيت الطلب</strong>
                            <strong>{{$o->created_at}}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <strong class="title">نوع الشحن</strong>
                            @if ($o->is_fastDelivery == 1)
                            <strong class="text-success"> شحن سريع (لحد باب البيت) </strong>
                            @else
                            <strong class="text-danger"> شحن عادي (لاقرب مكتب بريد)</strong>
                            @endif
                        </div>

                        @if ($o->status == "success")
                        <div class="d-flex justify-content-between mb-2">
                            <strong class="title">تاريخ الاستلام</strong>
                            @if ($o->is_fastDelivery == 1)
                            <strong class="text-success"> خلال 3 ايام</strong>
                            @else
                            <strong class="text-success">من 3 الي 5 ايام عمل</strong>
                            @endif
                        </div>
                        @endif
                    </div>

                    <a href="{{route('user.order.details', $o->id)}}" style="text-decoration: none">
                        <div class="card-footer text-muted">
                            <center>
                                <h5>انقر لعرض تفاصبل الطلب</h5>
                            </center>
                        </div>
                    </a>
                </div>

            </div>
            @endforeach

        </div>
        {{-- end orders --}}

    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
    integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
</script>
@endsection