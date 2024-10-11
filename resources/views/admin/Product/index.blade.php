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
            <table class="table table-hover align-middle mb-0"  id="myTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الوصف</th>
                        <th>السعر</th>
                        {{-- <th>offer value</th>
                        <th>offer price</th> --}}
                        <th>الماده</th>
                        <th>الصف الدراسي</th>
                        <th>المدرس</th>
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
    $(document).ready(function () {

        $('#myTable').DataTable({
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "الكل"] ],
            "processing": true,
            "serverSide": true,
            "sort": false,
            "ajax": {
                "url": "{{ route('dashboard.product.datatable') }}",
                "type": "GET"
            },
            "columns": [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'description', name: 'description' },
                { data: 'price', name: 'price' },
                // { data: 'offer_type', name: 'offer_type' },
                // { data: 'ProfitPercent', name: 'ProfitPercent' },
                { data: 'category_id', name: 'category_id' },
                { data: 'slider_id', name: 'slider_id' },
                { data: 'brand_id', name: 'brand_id' },
                { data: 'photo', name: 'photo' },
                { data: 'operation', name: 'operation', orderable: false }
            ],

        });
    })
</script>
<script>
    $(document).ready(function() {
        $(document).on('click','.delete_btn',function(e) {
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
                    $('.brandrow'+data.id).remove();
                    Swal.fire('Data has been Deletd successfully', '', 'success');
                }
            });
        });
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
