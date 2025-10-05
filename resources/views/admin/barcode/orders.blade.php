@extends('admin.layouts.master')
@section('title')
    barcode orders
@endsection
@section('content')
    <style>
        table th,
        tr,
        td {
            font-size: 20px;
        }

        .back-btn {
            margin-bottom: 20px;
        }

        .shipping-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>

    <!-- Back Button and Shipping Info -->
    <div class="row mb-3 mt-5">
        <div class="col-12">
            <a href="{{ route('dashboard.orders.barcode') }}" class="btn btn-secondary back-btn">
                <i class="fa fa-arrow-right"></i> العودة لاختيار طريقة الشحن
            </a>
        </div>
    </div>

    @if (request('shipping') && request('shipping') !== 'all')
        @php
            $shippingType = request('shipping');
            $typeLabels = [
                'branch' => 'استلام من الفرع',
                'post' => 'البريد المصري',
                'home' => 'التوصيل للمنزل',
            ];
            $typeIcons = [
                'branch' => 'fa-building',
                'post' => 'fa-envelope',
                'home' => 'fa-home',
            ];
        @endphp
        <div class="shipping-info">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5><i class="fa {{ $typeIcons[$shippingType] ?? 'fa-truck' }}"></i>
                        {{ $typeLabels[$shippingType] ?? $shippingType }}</h5>
                    <p class="mb-0">عرض طلبات {{ $typeLabels[$shippingType] ?? $shippingType }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fa fa-barcode fa-3x"></i>
                </div>
            </div>
        </div>
    @else
        <div class="shipping-info">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5><i class="fa fa-list"></i> جميع الطلبات</h5>
                    <p class="mb-0">عرض طلبات جميع طرق الشحن</p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fa fa-barcode fa-3x"></i>
                </div>
            </div>
        </div>
    @endif

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">الطلبات والباركود</h6>
                <div class="dropdown morphing scale-left">
                    <a href="#" class="card-fullscreen" data-bs-toggle="tooltip" title="Card Full-Screen"><i
                            class="icon-size-fullscreen"></i></a>
                    <a href="#" class="more-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i
                            class="fa fa-ellipsis-h"></i></a>
                    <ul class="dropdown-menu shadow border-0 p-2">
                        <li><a class="dropdown-item" href="#">File Info</a></li>
                        <li><a class="dropdown-item" href="#">Copy to</a></li>
                        <li><a class="dropdown-item" href="#">Move to</a></li>
                        <li><a class="dropdown-item" href="#">Rename</a></li>
                        <li><a class="dropdown-item" href="#">Block</a></li>
                        <li><a class="dropdown-item" href="#">Delete</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-hover align-middle mb-0" id="myTable">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>الاسم</th>
                            <th>الباركود</th>
                            <th>أضافه الباركود</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "الكل"]
                ],
                "paging": true,
                "pageLength": 10,
                "stateSave": true,
                "stateDuration": -1,
                'scrollX': true,
                "processing": true,
                "serverSide": true,
                "sort": false,
                "ajax": {
                    "url": "{{ route('dashboard.orders.datatable') }}",
                    "type": "GET",
                    "data": function(d) {
                        d.state = "success";
                        @if (request('shipping') && request('shipping') !== 'all')
                            d.shipping = "{{ request('shipping') }}";
                        @endif
                    }
                },
                "columns": [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'barcode',
                        name: 'barcode'
                    },
                    {
                        data: 'admin_addbarcode',
                        name: 'admin_addbarcode',
                    },
                ],
            });
        });
    </script>
@endsection
