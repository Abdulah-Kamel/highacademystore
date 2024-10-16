@extends('admin.layouts.master')
@section('title')
orders
@endsection
@section('content')

<style>
    table th,
    tr,
    td {
        font-size: 20px:
    }
    .filter-btn{
        justify-content: space-between;
    }
    @media only screen and (max-width: 445px) {
        .filter-btn{
            justify-content: center;
          
        }
    }
    @media only screen and (max-width: 600px) {
        .filter-btn{
            div:not(:last-child) {
                margin-bottom: 15px;
            }
        }
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
            <select id="stateFilter" class="form-control">
    <option value="">كل الحالات</option>
    <option value="success">طلب ناجح</option>
    <option value="cancelled">تم الإلغاء</option>
</select>
<br>
            <table class="table table-hover align-middle mb-0" id="myTable" dir="rtl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الرقم</th>
                        <th>عنوان الشحن</th>
                        <th>نوع الشحن</th>
                        <th>اجمالي المدفوع</th>
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
<script src="https://cdn.datatables.net/2.3.5/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.5/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.5/js/buttons.print.min.js"></script>
<script>
    $(document).ready(function () {
        var table = $('#myTable').DataTable({
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "الكل"]],
            "processing": true,
            "serverSide": true,
            "sort": false,
            "ajax": {
                "url": "{{ route('dashboard.orders.datatable') }}",
                "type": "GET",
                "data": function (d) {
                    d.state = $('#stateFilter').val(); // إضافة قيمة الفلتر
                }
            },
            "columns": [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'phone', name: 'phone' },
                // { data: 'email', name: 'email' },
                { data: 'address', name: 'address' },
                { data: 'fast_ship', name: 'fast_ship' },
                { data: 'total', name: 'total' },
                { data: 'method', name: 'method' },
                { data: 'account', name: 'account' },
                { data: 'image', name: 'image' },
                { data: 'state', name: 'state' },
                { data: 'details', name: 'details' },
            ],
"dom": '<"d-flex filter-btn flex-wrap align-items-center" lfB>rtip',
            "buttons": [
                {
                    extend: 'excelHtml5',
                    text: 'تصدير إلى Excel',
                    title: 'اخر الطلبات',
                    titleAttr: 'Excel',
                    className: 'btn btn-success',
                    excelStyles: {
                    sheetName: 'الطلبات',
                }
                },
                {
                    extend: 'print',
                    text: 'طباعة',
                    titleAttr: 'طباعة',
                    className: 'btn btn-primary',
                }
            ],
            scrollX: true,
        });

        // تشغيل الفلترة عند تغيير قيمة القائمة
        $('#stateFilter').on('change', function () {
            table.draw();
        });
    });
</script>

@endsection
