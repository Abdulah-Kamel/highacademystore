    @extends('user.layouts.master')

    @section('title', 'تفاصيل الطلب')

    @section('content')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" />
        <style>
            /* … your existing styles … */
            
            /* Order Tracker Styles */
            .hh-grayBox {
                margin-bottom: 20px;
                padding: 35px;
                margin-top: 20px;
            }
            .pt45{padding-top:45px;}
            .order-tracking{
                text-align: center;
                width: 33.33%;
                position: relative;
                display: block;
            }
            .order-tracking .is-complete{
                display: block;
                position: relative;
                border-radius: 50%;
                height: 30px;
                width: 30px;
                border: 0px solid #AFAFAF;
                background-color: #f7be16;
                margin: 0 auto;
                transition: background 0.25s linear;
                -webkit-transition: background 0.25s linear;
                z-index: 2;
            }
            .order-tracking .is-complete:after {
                display: block;
                position: absolute;
                content: '';
                height: 14px;
                width: 7px;
                top: -2px;
                bottom: 0;
                left: 5px;
                margin: auto 0;
                border: 0px solid #AFAFAF;
                border-width: 0px 2px 2px 0;
                transform: rotate(45deg);
                opacity: 0;
            }
            .order-tracking.completed .is-complete{
                border-color: #27aa80;
                border-width: 0px;
                background-color: #27aa80;
            }
            .order-tracking.completed .is-complete:after {
                border-color: #fff;
                border-width: 0px 3px 3px 0;
                width: 7px;
                left: 11px;
                opacity: 1;
            }
            .order-tracking p {
                color: #A4A4A4;
                font-size: 16px;
                margin-top: 8px;
                margin-bottom: 0;
                line-height: 20px;
            }
            .order-tracking p span{font-size: 14px;}
            .order-tracking.completed p{color: #000;}
            .order-tracking::before {
                content: '';
                display: block;
                height: 3px;
                width: calc(100% - 40px);
                background-color: #f7be16;
                top: 13px;
                position: absolute;
                left: calc(-50% + 20px);
                z-index: 0;
            }
            .order-tracking:first-child:before{display: none;}
            .order-tracking.completed:before{background-color: #27aa80;}

            /* Barcode Section adjustments */
            .barcode-section h4 { font-weight: 700; }
            .barcode-note { color: #333; font-size: 15px; margin: 6px 0 2px; }
            .barcode-link { color: #0d6efd; font-weight: 700; text-decoration: underline; }
            .hl-underline { position: relative; display: inline-block; }
            .hl-underline::after { content: ''; position: absolute; left: 0; right: 0; bottom: -2px; height: 8px; background: rgba(40,167,69,.25); border-radius: 4px; z-index: -1; }


        </style>

        <div class="container">
            <div class="row justify-content-center mt-5 pt-5 text-center">
                <div class="col-md-12">
                    <h5 class="section-title position-relative text-uppercase mb-3">
                        <span class="pr-3">تفاصيل الطلب</span>
                    </h5>
                </div>

                {{-- Tracking Steps --}}
                @if ($order->status !== 'cancelled')

                            <div class="col-12 hh-grayBox pt45 pb20 card shadow-sm">
                                <div class="row justify-content-between">
                                    <div class="order-tracking {{ $order->status === 'new' || $order->status === 'reserved' || $order->status === 'success' ? 'completed' : '' }}">
                                        <span class="is-complete"></span>
                                        <p>قيد المراجعة<br><span>{{ $order->created_at->format('M d, Y') }}</span></p>
                                    </div>
                                    <div class="order-tracking {{ $order->status === 'reserved' || $order->status === 'success' ? 'completed' : '' }}">
                                        <span class="is-complete"></span>
                                        <p>قيد التجهيز<br><span>{{ $order->status === 'reserved' || $order->status === 'success' ? $order->updated_at->format('M d, Y') : 'قريباً' }}</span></p>
                                    </div>
                                    <div class="order-tracking {{ $order->status === 'success' ? 'completed' : '' }}">
                                        <span class="is-complete"></span>
                                        <p>قيد التوصيل<br><span>{{ $order->status === 'success' ? $order->updated_at->format('M d, Y') : 'قريباً' }}</span></p>
                                    </div>
                                </div>
                            </div>
                @endif

                {{-- Barcode --}}
                @if ($order->barcode)
                    <div class="col-12 mt-2" dir="rtl">
                        <div class="card shadow-sm">
                            <div class="card-body text-center barcode-section">
                                <h4 class="mb-2">اعرف شحنتك وصلت لفين</h4>
                                <div class="barcode-note">انسخ البار كود دا</div>
                                <div class="barcode-code">
                                    <strong class="fs-5">
                                        {{ $order->barcode }}
                                    </strong>
                                    <a class="barcode-link" href="https://egyptpost.gov.eg/ar-eg/home/eservices/track-and-trace/" target="_blank">واضغط هنا</a>
                                </div>
                                <div class="mt-2">
                                    <span class="hl-underline">عشان تشوف شحنتك بقت فين 🛵</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Ordered Items Table --}}
                <div class="col-12 mt-2" dir="rtl">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-end mb-4">تفاصيل المنتجات</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">المنتج</th>
                                            <th scope="col">الكمية</th>
                                            <th scope="col">السعر</th>
                                            <th scope="col">اللون</th>
                                            <th scope="col">المقاس</th>
                                            <th scope="col">الإجمالي</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->orderDetails as $detail)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if ($detail->products && $detail->products->image)
                                                            <img src="{{ asset('storage/images/products/' . $detail->products->image) }}" 
                                                                 alt="{{ $detail->products->name }}" 
                                                                 class="me-3" 
                                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-0">{{ $detail->products->name ?? 'منتج محذوف' }}</h6>
                                                            @if ($detail->products && $detail->products->short_name)
                                                                <small class="text-muted">{{ $detail->products->short_name }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $detail->amout }}</td>
                                                <td>{{ number_format($detail->price, 2) }} جنيه</td>
                                                <td>{{ $detail->color ?? '-' }}</td>
                                                <td>{{ $detail->size ?? '-' }}</td>
                                                <td>{{ number_format($detail->total_price, 2) }} جنيه</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="5" class="text-end">الإجمالي:</th>
                                            <th>{{ number_format($order->total, 2) }} جنيه</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
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
