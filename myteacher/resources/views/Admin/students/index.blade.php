@extends('Admin.layout.index')
@section('title')
    الطلاب
@endsection
<link rel="stylesheet" href="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/select2/select2.css"/>
@section('content')
    <button type="button" id="add" class="btn btn-success mb-2">اضف طالب جديد</button>
    <div class="card">

        <div class="card-datatable table-responsive pt-0">
            <table id="dataTable" class="datatables-basic table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>رقم تليفون الطالب</th>
                    <th>رقم تليفون ولي الامر</th>
                    <th>الصف الدراسي</th>
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
                ajax: "{{ route('students.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'phone', name: 'phone'},
                    {data: 'parent_phone', name: 'parent_phone'},
                    {data: 'grade_id', name: 'grade_id'},
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
        $('#dataTable').on('click', '#btnDelete', function () {
            Swal.fire({
                text: 'هل انت متاكد من حذف هذه الطالب',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم',
                cancelButtonText: 'لا',
            }).then((result) => {
                if (result.isConfirmed) {
                    var studentId = $(this).data('id');
                    var url = "{{route('students.destroy','studentId')}}"
                    url = url.replace('studentId', studentId);
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        url: url,
                        method: 'Delete',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function (response) {
                            Swal.fire(
                                'تم',
                                response.success,
                                'success'
                            )
                            myTable.ajax.reload();
                        }
                    });
                }
            })
        });
        $('#dataTable').on('click', '#btnEdit', function () {
            var studentId = $(this).data('id');
            var url = "{{route('students.edit',':studentId')}}"
            url = url.replace(':studentId', studentId);
            $.ajax({
                url: url,
                success: function (response) {
                    $('.bodyModel').html(response.html);
                    $('.modal-title').text('تعديل الطالب ')
                    $('#basicModal').modal('show');
                }
            });
        });
        $('#dataTable').on('click', '#btnShow', function () {
            var studentId = $(this).data('id');
            var url = "{{route('students.show',':studentId')}}"
            url = url.replace(':studentId', studentId);
            $.ajax({
                url: url,
                success: function (response) {
                    $('.bodyModel').html(response.html);
                    $('.modal-title').text('تفاصيل الطالب ')
                    $('#basicModal').modal('show');
                }
            });
        });
        $("#add").on('click', function () {
            $.ajax({
                url: "{{ route('students.create')}}",
                success: function (response) {
                    $('.bodyModel').html(response.html);
                    $('.modal-title').text('اضف طالب جديد')
                    $('#basicModal').modal('show');
                }
            });
        });
    </script>
@endsection
