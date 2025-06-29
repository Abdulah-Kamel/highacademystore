@extends('Admin.layout.index')
@section('title')
    المستخدمين
@endsection
@section('content')
    <button type="button" id="add" class="btn btn-success mb-2">اضف مستخدم جديد</button>
    <div class="card">

        <div class="card-datatable table-responsive pt-0">
            <table id="dataTable" class="datatables-basic table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>الصورة</th>
                    <th>البريد الالكتروني</th>
                    <th>الحالة</th>
                    <th>رقم الهاتف</th>
                    <th>الاجراء</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{asset('Admin')}}/form-validator/jquery.form-validator.min.js"></script>
    <script>
        $.validate({
            modules: 'security, date',
            lang: 'ar'

        });
    </script>
    <script>

        var myTable;
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $(function () {
            myTable = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admins.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'image', name: 'image'},
                    {data: 'email', name: 'email'},
                    {data: 'status', name: 'status'},
                    {data: 'phone', name: 'phone'},
                    {
                        data: 'actions', name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ],
                language: {
                    {{--"sProcessing": "{{trans('dataTable.sProcessing')}}",--}}
                        {{--"sLengthMenu": "{{trans('dataTable.sLengthMenu')}}",--}}
                    "sZeroRecords": "لا يوجد بيانات",
                    {{--"sInfo": "{{trans('dataTable.sInfo')}}",--}}
                        {{--"sInfoEmpty": "{{trans('dataTable.sInfoEmpty')}}",--}}
                        {{--"sInfoFiltered": "{{trans('dataTable.sInfoFiltered')}}",--}}
                        {{--"sInfoPostFix": "",--}}
                    "sSearch": "بحث",
                    {{--"sUrl": "",--}}
                    "oPaginate": {
                        "sFirst": "{{trans('dataTable.sFirst')}}",
                        "sPrevious": "السابق",
                        "sNext": "التالي",
                        "sLast": "{{trans('dataTable.sLast')}}"
                    },
                },
                buttons: [
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: [1, 3, 4], // Column index which needs to export
                        }
                    }
                ],
            });

        });
        $('#dataTable').on('click','#btnDelete',function() {
            Swal.fire({
                text: 'هل انت متاكد من حذف هذا المستخدم',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم',
                cancelButtonText: 'لا',
            }).then((result) => {
                if (result.isConfirmed) {
                    var adminId = $(this).data('id');
                    var url = "{{route('admins.destroy','adminId')}}"
                    url = url.replace('adminId',adminId);
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        url: url,
                        method: 'Delete',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function (response) {
                            Swal.fire(
                                'تم' ,
                                response.success,
                                'success'
                            )
                            myTable.ajax.reload();
                        }
                    });
                }
            })
        });
        $('#dataTable').on('click','#btnEdit',function() {
            var adminId = $(this).data('id');
            var url = "{{route('admins.edit',':adminId')}}"
            url = url.replace(':adminId',adminId);
            $.ajax({
                url: url,
                success: function (response) {
                    $('.body').html(response.html);
                    $('#onboardHorizontalImageModal').modal('show');
                }
            });
        });
        $("#add").on('click', function () {
            $.ajax({
                url: "{{ route('admins.create')}}",
                success: function (response) {
                    $('.body').html(response.html);
                    $('#onboardHorizontalImageModal').modal('show');
                }
            });
        });
    </script>
@endsection
