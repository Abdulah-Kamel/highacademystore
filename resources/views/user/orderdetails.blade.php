    @extends('user.layouts.master')

    @section('title', 'تفاصيل الطلب')

    @section('content')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" />
        <style>
            /* … your existing styles … */
        </style>

        <div class="container">
            <div class="row mt-5 pt-5 text-center">
                <div class="col-md-12">
                    <h5 class="section-title position-relative text-uppercase mb-3">
                        <span class="pr-3">تفاصيل الطلب</span>
                    </h5>
                </div>

                {{-- Tracking Steps (unchanged) --}}
                @if ($order->status !== 'cancelled')
                    <div class="col-12 mt-2" dir="rtl">
                        {{-- … your order-tracker UL … --}}
                    </div>
                @endif

                {{-- Barcode (unchanged) --}}
                @if ($order->barcode)
                    <div class="col-12 mt-2" dir="rtl">
                        {{-- … barcode card … --}}
                    </div>
                @endif

                {{-- Ordered Items Table (unchanged) --}}
                <div class="col-12 mt-2" dir="rtl">
                    {{-- … your table of orderDetails … --}}
                </div>

                {{-- Order Meta & Shipping Details --}}
                <div class="col-12 mt-2" dir="rtl">
                    <div class="card shadow-sm w-100 p-4 p-md-5">
                        <h3 class="text-end">تفاصيل الطلب</h3>
                        <ul class="list-group list-group-flush">
                            {{-- Order ID, Name, Mobile --}}
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>رقم الطلب</strong><span>{{ $order->id }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>اسم الطالب</strong><span>{{ $order->name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>رقم الطالب</strong><span>{{ $order->mobile }}</span>
                            </li>

                            {{-- Shipping Method --}}
                            @php
                                $ship = optional($order->shipping);
                                // determine type label
                                $typeLabel = match ($ship->type) {
                                    'post' => 'مكتب بريد',
                                    'home' => 'توصيل لباب البيت',
                                    'branch' => 'استلام من المكتبة',
                                    default => '-',
                                };
                            @endphp
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>طريقة الشحن</strong>
                                <span>{{ $ship->name ?? '-' }} ({{ $typeLabel }})</span>
                            </li>

                            {{-- Shipping Address --}}
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>عنوان الشحن</strong>
                                <span>{{ $ship->address ?? '-' }}</span>
                            </li>

                            {{-- Shipping Phones --}}
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>أرقام التواصل</strong>
                                <span>{{ $ship->phones ? implode(' - ', $ship->phones) : '-' }}</span>
                            </li>

                            {{-- Total Paid --}}
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>إجمالي المدفوع</strong>
                                <span>{{ number_format($order->total, 2) }} جنيه</span>
                            </li>

                            {{-- Payment Method --}}
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>وسيلة الدفع</strong>
                                <span>{{ $order->method }}</span>
                            </li>

                            {{-- Detailed Address --}}
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>العنوان</strong>
                                <span>{{ $order->address }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>العنوان التفصيلي</strong>
                                <span>{{ $order->address2 }}</span>
                            </li>

                            {{-- Order Timestamp --}}
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>توقيت الطلب</strong>
                                <span>{{ $order->created_at->format('Y-m-d H:i') }}</span>
                            </li>

                            {{-- Order Status --}}
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>حالة الطلب</strong>
                                @switch($order->status)
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
                            </li>

                            {{-- Delivery Estimate (on success) --}}
                            @if ($order->status === 'success')
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>تاريخ الاستلام المتوقع</strong>
                                    @if ($ship->type === 'home')
                                        <span class="text-success">خلال 3 أيام عمل</span>
                                    @else
                                        <span class="text-success">من 3 إلى 5 أيام عمل</span>
                                    @endif
                                </li>
                            @endif
                        </ul>

                        {{-- Edit Button for new/reserved --}}
                        @if (in_array($order->status, ['new', 'reserved']))
                            <div class="text-center mt-3">
                                <a href="{{ route('user.order.edit', $order->id) }}" class="btn btn-info">
                                    <i class="fas fa-edit me-1"></i> تعديل بيانات الطلب
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('js')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    @endsection
