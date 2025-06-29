@extends('min_admin.layouts.master')
@section('title')
    اكواد {{$coupon->name}}
@endsection
@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">    اكواد {{$coupon->name}}</h6>
                <div class="dropdown morphing scale-left">
                    <a href="#" class="card-fullscreen" data-bs-toggle="tooltip" title="ملء الشاشة"><i
                            class="icon-size-fullscreen"></i></a>
                    <a href="#" class="more-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i
                            class="fa fa-ellipsis-h"></i></a>
                    <ul class="dropdown-menu shadow border-0 p-2">
                        <li><a class="dropdown-item" href="#">معلومات الملف</a></li>
                        <li><a class="dropdown-item" href="#">نسخ إلى</a></li>
                        <li><a class="dropdown-item" href="#">نقل إلى</a></li>
                        <li><a class="dropdown-item" href="#">إعادة تسمية</a></li>
                        <li><a class="dropdown-item" href="#">حظر</a></li>
                        <li><a class="dropdown-item" href="#">حذف</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
               <a href="{{ route('dashboard.mini.vouchers.add', $coupon->id) }}" class="btn btn-primary btn-lg btn-block">اضافة كود جديد</a>

                <br><br>
                <table class="table table-hover align-middle mb-0" id="couponsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الكوبون</th>
                            <th>ألصورة</th>
                            <th>الحالة</th>
                            <th>العمليات</th>
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

    $('#couponsTable').DataTable({
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "الكل"]
        ],
        "paging": true,
        "pageLength": 10,
        "stateSave": true,
        "stateDuration": -1,
        "processing": true,
        "serverSide": true,
        'scrollX': true,
        "sort": false,
        "ajax": {
            "url": "{{ route('dashboard.mini.vouchers.datatable', $coupon->id) }}",
            "type": "GET"
        },
        "columns": [
            {
                data: 'id',
                name: 'id'
            },
            {
                data: 'code',
                name: 'code'
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
                data: 'operation',
                name: 'operation',
                orderable: false
            }
        ],

    });
});

$(document).on('click', '.delete_btn', function(e) {
    e.preventDefault();

    var voucher_id = $(this).attr('voucher_id');

    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: "لا يمكن التراجع عن هذا الإجراء!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم، احذف!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route('dashboard.mini.vouchers.destroy') }}',
                type: "POST",
                dataType: "json",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id': voucher_id
                },
                success: function(data) {
                    Swal.fire({
                        title: 'تم الحذف!',
                        text: 'تم حذف الكوبون بنجاح.',
                        icon: 'success',
                        confirmButtonText: 'موافق'
                    }).then(() => {
                        // إعادة تحميل الصفحة بعد النقر على "موافق"
                        $('#couponsTable').DataTable().ajax.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire('خطأ!', 'حدث خطأ أثناء الحذف.', 'error');
                }
            });
        }
    });
});


   </script>
@endsection
