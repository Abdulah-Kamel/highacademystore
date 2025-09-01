@extends('admin.layouts.master')
@section('title')
    voucher Orders
@endsection
@section('content')
    <style>
        table th,
        tr,
        td {
            font-size: 20px:
        }
    </style>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">الطلبات</h6>
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
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle form-control text-right statefilter" type="button"
                        id="stateFilter" data-bs-toggle="dropdown" aria-expanded="false">
                        نوع حالة العملية
                    </button>
                    <div class="dropdown-menu w-100" aria-labelledby="stateFilter">
                        <li>
                            <a class="dropdown-item" href="{{ route('dashboard.voucher_order') }}">كل الحالات</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('dashboard.voucher_order') }}?state=success">
                                طلب ناجح
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('dashboard.voucher_order') }}?state=cancelled">
                                تم الإلغاء </a>
                        </li>
                    </div>
                </div>
                <br>
                <table class="table table-hover align-middle mb-0" id="myTable" dir="rtl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم العميل</th>
                            <th>البريد الإلكتروني</th>
                            <th>رقم الهاتف</th>
                            <th>الكوبون</th>
                            <th>عدد الكوبونات المطلوبة</th>
                            <th>وسيلة الدفع</th>
                            <th>رقم حساب الدفع</th>
                            <th>ايصال الدفع</th>
                            <th>حالة العملية</th>
                            <th>التفاصيل</th>
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
            var table = $('#myTable').DataTable({
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
                    "url": "{{ route('dashboard.voucher_order.datatable') }}",
                    "type": "GET",
                    "data": function(d) {
                        d.state = "{{ request()->query('state') !== null ? request()->query('state') : '' }}";
                    }
                },
                "columns": [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'customer_email',
                        name: 'customer_email'
                    },
                    {
                        data: 'customer_phone',
                        name: 'customer_phone'
                    },
                    {
                        data: 'coupon',
                        name: 'coupon'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'method',
                        name: 'method'
                    },
                    {
                        data: 'account',
                        name: 'account'
                    },
                    {
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'state',
                        name: 'state'
                    },
                    {
                        data: 'details',
                        name: 'details'
                    },
                ]
            });

            // تشغيل الفلترة عند تغيير قيمة القائمة
            $('#stateFilter').on('change', function() {
                table.draw();
            });
        });
    </script>
@endsection
