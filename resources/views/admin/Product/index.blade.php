@extends('admin.layouts.master')
@section('title')
    products
@endsection
@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">productss</h6>
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
                <!-- Filter Section -->
                <div class="row mb-3 gy-4">
                    <div class="col-md-3">
                        <label for="name_filter" class="form-label">الاسم</label>
                        <input type="text" class="form-control" id="name_filter" placeholder="ابحث بالاسم">
                    </div>
                    <div class="col-md-3">
                        <label for="main_category_filter" class="form-label">القسم الرئيسي</label>
                        <select class="form-select" id="main_category_filter">
                            <option value="">الكل</option>
                            @foreach (\App\Models\MainCategory::all() as $mainCategory)
                                @if ($mainCategory->name != 'اكواد مدرسين الاونلاين')
                                    <option value="{{ $mainCategory->id }}">{{ $mainCategory->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="status_filter" class="form-label">الحالة</label>
                        <select class="form-select" id="status_filter">
                            <option value="">الكل</option>
                            <option value="0">نشط</option>
                            <option value="1">غير نشط (مخفى)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="best_seller_filter" class="form-label">الأكثر مبيعاً</label>
                        <select class="form-select" id="best_seller_filter">
                            <option value="">الكل</option>
                            <option value="1">نعم</option>
                            <option value="0">لا</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary me-2" id="filter_btn">بحث</button>
                        <button class="btn btn-secondary" id="reset_btn">إعادة تعيين</button>
                    </div>
                </div>

                <table class="table table-hover align-middle mb-0" id="myTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الوصف</th>
                            <th>الاسم المختصر</th>
                            <th>السعر</th>
                            <th>الكمية</th>
                            <th>القسم الرئيسي</th>
                            <th>الماده</th>
                            <th>الصف الدراسي</th>
                            <th>المدرس</th>
                            <th>الأكثر مبيعًا</th>
                            <th>الحاله</th>
                            <th>هل المنتج متوفر؟</th>
                            <th>الصوره</th>
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
            // Initialize DataTable with server-side processing
            var table = $('#myTable').DataTable({
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
                "searching": false,
                "ajax": {
                    "url": "{{ route('dashboard.product.datatable') }}",
                    "type": "GET",
                    "data": function(d) {
                        d.name = $('#name_filter').val();
                        d.main_category_id = $('#main_category_filter').val();
                        d.status = $('#status_filter').val();
                        d.best_seller = $('#best_seller_filter').val();
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
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'short_name',
                        name: 'short_name'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'main_category_id',
                        name: 'main_category_id'
                    },
                    {
                        data: 'category_id',
                        name: 'category_id'
                    },
                    {
                        data: 'slider_id',
                        name: 'slider_id'
                    },
                    {
                        data: 'brand_id',
                        name: 'brand_id'
                    },
                    {
                        data: 'best_seller',
                        name: 'best_seller'
                    },
                    {
                        data: 'is_deleted',
                        name: 'is_deleted'
                    },
                    {
                        data: 'state',
                        name: 'state'
                    },
                    {
                        data: 'photo',
                        name: 'photo'
                    },
                    {
                        data: 'operation',
                        name: 'operation',
                        orderable: false
                    }
                ],


            });

            // Handle filter button click
            $('#filter_btn').on('click', function() {
                table.ajax.reload(null, false); // Don't reset paging
            });

            // Handle reset button click
            $('#reset_btn').on('click', function() {
                $('#name_filter').val('');
                $('#category_filter').val('');
                $('#main_category_filter').val(''); // أضف هذا
                $('#status_filter').val('');
                $('#best_seller_filter').val('');
                table.ajax.reload(null, false);
            });



            // Handle Enter key in filter inputs
            $('.form-control, .form-select').keypress(function(e) {
                if (e.which == 13) {
                    table.ajax.reload(null, false);
                }
            });
        })
    </script>
    <script>
        $(document).ready(function() {
            // Handle show/hide toggle
            $(document).on('click', '.toggle-visibility-btn', function(e) {
                e.preventDefault();
                var product_id = $(this).data('id');
                var action = $(this).data('action');
                var actionText = action === 'hide' ? 'إخفاء' : 'إظهار';

                Swal.fire({
                    title: `هل أنت متأكد من ${actionText} هذا المنتج؟`,
                    text: `هل أنت متأكد من ${actionText} هذا المنتج؟`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: action === 'hide' ? "#ffc107" : "#0dcaf0",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: `نعم، ${actionText} المنتج`,
                    cancelButtonText: "إلغاء"
                }).then((result) => {
                    if (result.isConfirmed) {
                        toggleProductVisibility(product_id, action);
                    }
                });
            });

            // Handle force delete
            $(document).on('click', '.force-delete-btn', function(e) {
                e.preventDefault();
                var product_id = $(this).data('id');

                Swal.fire({
                    title: "هل أنت متأكد من حذف هذا المنتج نهائياً؟",
                    text: "هل أنت متأكد من حذف هذا المنتج نهائياً؟ هذا الإجراء لا يمكن التراجع عنه!",
                    icon: "error",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "نعم، احذف نهائياً",
                    cancelButtonText: "إلغاء"
                }).then((result) => {
                    if (result.isConfirmed) {
                        performDelete(product_id, 'force');
                    }
                });
            });

            // Function to toggle product visibility (show/hide)
            function toggleProductVisibility(product_id, action) {
                $.ajax({
                    url: "{{ route('dashboard.product.soft-delete') }}",
                    type: "POST",
                    dataType: "json",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': product_id,
                        'action': action
                    },
                    success: function(response) {
                        if (response.success) {
                            const message = action === 'hide' ?
                                "تم إخفاء المنتج بنجاح" :
                                "تم إظهار المنتج بنجاح";

                            Swal.fire("نجاح!", message, "success");
                            $('#myTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire("خطأ!", response.message, "error");
                        }
                    },
                    error: function(xhr) {
                        Swal.fire("خطأ!", "حدث خطأ أثناء محاولة تغيير حالة المنتج.", "error");
                        console.log(xhr.responseText);
                    }
                });
            }

            // Function to perform the actual delete
            function performDelete(product_id, deleteType) {
                const url = deleteType === 'soft' ?
                    "{{ route('dashboard.product.soft-delete') }}" :
                    "{{ route('dashboard.product.force-delete') }}";

                $.ajax({
                    url: url,
                    type: "POST",
                    dataType: "json",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': product_id
                    },
                    success: function(response) {
                        if (response.success) {
                            const message = deleteType === 'soft' ?
                                "تم اخفاء المنتج بنجاح" :
                                "تم حذف المنتج نهائياً بنجاح";

                            Swal.fire("نجاح!", message, "success");
                            $('#myTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire("خطأ!", response.message, "error");
                        }
                    },
                    error: function(xhr) {
                        Swal.fire("خطأ!", "حدث خطأ أثناء محاولة تنفيذ العملية.", "error");
                        console.log(xhr.responseText);
                    }
                });
            }
        });
    </script>

    {{-- <script>
    $('.myDataTable').addClass('nowrap').dataTable({
      responsive: true,
      searching: true,
      paging: true,
      ordering: true,
      info: false,
    });
    $('#myDataTable_no_filter').addClass('nowrap').dataTable({
      responsive: true,
      searching: false,
      paging: false,
      ordering: false,
      info: false,
    });
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
      $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
    });

    $(document).ready(function() {
    $('.delete_btn').click(function(e) {
        e.preventDefault();
        var product_id = $(this).attr('product_id');
        $.ajax({
            url: '{{route('dashboard.product.destroy')}}',
            type: "POST",
            dataType: "json",
            data: {
                '_token': "{{csrf_token()}}",
                'id': product_id
            },
            success: function(data) {
                $('.productrow'+data.id).remove();
                Swal.fire('Data has been Deletd successfully', '', 'success');
            }
        });
    });
});
</script> --}}
@endsection
