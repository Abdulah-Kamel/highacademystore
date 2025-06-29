@extends('user.layouts.master')

@section('title', 'متابعة طلبــــــاتي')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        crossorigin="anonymous">
    <style>
        .order-status {
            font-size: 14px;
            font-weight: bold;
        }

        @media (min-width:576px) {
            .order-status {
                font-size: 1.2rem;
            }
        }

        .text-primary {
            color: #e99239 !important;
        }

        .bg-warning,
        .btn-primary {
            background-color: #e99239 !important;
            border: none;
        }
    </style>

    <div class="container pt-5">
        <div class="col-md-12 mt-5 text-center">
            <h5 class="section-title position-relative text-uppercase mb-3"><span class="pr-3">طلبــــــاتي</span></h5>
        </div>

        <div class="row mx-auto text-center justify-content-center align-items-center my-3 g-3">
            <div class="col">
                <div class="d-flex flex-column align-items-center">
                    <div class="d-flex align-items-center justify-content-center bg-secondary text-white rounded-circle"
                        style="width: 80px; height: 80px; font-size: 20px;">
                        {{ $reservedCount }}
                    </div>
                    <h4 class="mt-2 order-status">الطلبات المحجوزة</h4>
                </div>
            </div>
            <div class="col">
                <div class="d-flex flex-column align-items-center">
                    <div class="d-flex align-items-center justify-content-center bg-success text-white rounded-circle"
                        style="width: 80px; height: 80px; font-size: 20px;">
                        {{ $successCount }}
                    </div>
                    <p class="mt-2 order-status">الطلبات الناجحة</p>
                </div>
            </div>

            <div class="col">
                <div class="d-flex flex-column align-items-center">
                    <div class="d-flex align-items-center justify-content-center bg-danger text-white rounded-circle"
                        style="width: 80px; height: 80px; font-size: 20px;">
                        {{ $cancelledCount }}
                    </div>
                    <p class="mt-2 order-status">الطلبات الملغية</p>
                </div>
            </div>

            <div class="col">
                <div class="d-flex flex-column align-items-center">
                    <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle"
                        style="width: 80px; height: 80px; font-size: 20px;">
                        {{ $successPercentage }}%
                    </div>
                    <p class="mt-2 order-status">نسبة النجاح</p>
                </div>
            </div>
        </div>

        @if ($orders->isEmpty())
            <h3 class="text-center">لا يوجد أي طلبات حالياً</h3>
        @endif

        <div class="row g-4">
            @foreach ($orders as $o)
                @php $ship = optional($o->shipping); @endphp
                <div class="col-md-6">
                    <div class="card h-100" dir="rtl">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>#{{ $o->id }}</strong>
                            @switch($o->status)
                                @case('new')
                                    <span class="badge bg-warning text-dark">طلب جديد</span>
                                @break

                                @case('success')
                                    <span class="badge bg-success">طلب ناجح</span>
                                @break

                                @case('cancelled')
                                    <span class="badge bg-danger">طلب ملغي</span>
                                @break

                                @case('reserved')
                                    <span class="badge bg-info">طلب محجوز</span>
                                @break

                                @default
                                    <span class="badge bg-secondary">غير معروف</span>
                            @endswitch
                        </div>

                        <div class="card-body">
                            {{-- Shipping Method --}}
                            <div class="d-flex justify-content-between mb-2">
                                <strong>طريقة الشحن</strong>
                                <span class="text-primary">
                                    {{ $ship->name ?? '-' }}
                                    (
                                    @if ($ship->type === 'post')
                                        مكتب بريد
                                    @elseif($ship->type === 'home')
                                        توصيل لباب البيت
                                    @elseif($ship->type === 'branch')
                                        استلام من المكتبة
                                    @else
                                        -
                                    @endif
                                    )
                                </span>
                            </div>

                            {{-- Shipping Address --}}
                            <div class="d-flex justify-content-between mb-2">
                                <strong>عنوان الشحن</strong>
                                <span>{{ $ship->address ?? '-' }}</span>
                            </div>

                            {{-- Shipping Phones --}}
                            <div class="d-flex justify-content-between mb-2">
                                <strong>أرقام التواصل</strong>
                                <span>{{ $ship->phones ? implode(' - ', $ship->phones) : '-' }}</span>
                            </div>

                            {{-- Order Total --}}
                            <div class="d-flex justify-content-between mb-2">
                                <strong>المبلغ المدفوع</strong>
                                <span>{{ number_format($o->total, 2) }} جنيه</span>
                            </div>

                            {{-- Payment Method --}}
                            <div class="d-flex justify-content-between mb-2">
                                <strong>طريقة الدفع</strong>
                                <span>{{ $o->method }}</span>
                            </div>

                            {{-- Order Date --}}
                            <div class="d-flex justify-content-between mb-2">
                                <strong>توقيت الطلب</strong>
                                <span>{{ $o->created_at->format('Y-m-d H:i') }}</span>
                            </div>

                            {{-- Delivery Estimate (only on success) --}}
                            @if ($o->status === 'success')
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>تاريخ الاستلام المتوقع</strong>
                                    @if ($ship->type === 'home')
                                        <span class="text-success">خلال 3 أيام عمل</span>
                                    @else
                                        <span class="text-success">من 3 إلى 5 أيام عمل</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('user.order.details', $o->id) }}"
                            class="card-footer text-center text-decoration-none">
                            <h5>انقر لعرض تفاصيل الطلب</h5>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
    </script>
@endsection
