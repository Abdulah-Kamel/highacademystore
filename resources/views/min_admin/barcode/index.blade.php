@extends('min_admin.layouts.master')
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
                "url": "{{ route('dashboard.order.datatable') }}",
                "type": "GET",
                "data": function(d) {
                        d.state = "success";
                    }
            },
            "columns": [
                {
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
                    data: 'addbarcode',
                    name: 'addbarcode',
                },
            ],
        });
    })
</script>
@endsection
