@extends('Admin.layout.index')
@section('title')
    الحصص
@endsection
<link rel="stylesheet" href="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/select2/select2.css" />
@section('content')
    <button type="button" id="add" class="btn btn-success mb-2">اضف حصة جديد</button>
    <div class="card">

        <div class="card-datatable table-responsive pt-0">
            <table id="dataTable" class="datatables-basic table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>تبدء في</th>
                    <th>تنتهي في</th>
                    <th>الصف الدراسي</th>
                    <th>عدد الطلاب</th>
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
                ajax: "{{ route('sessions.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'start_from', name: 'start_from'},
                    {data: 'start_to', name: 'start_to'},
                    {data: 'grade_id', name: 'grade_id'},
                    {data: 'studentNumber', name: 'studentNumber'},
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
                text: 'هل انت متاكد من حذف هذه الحصة',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم',
                cancelButtonText: 'لا',
            }).then((result) => {
                if (result.isConfirmed) {
                    var sessionId = $(this).data('id');
                    var url = "{{route('sessions.destroy','sessionId')}}"
                    url = url.replace('sessionId', sessionId);
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
            var gradeId = $(this).data('id');
            var url = "{{route('sessions.edit',':gradeId')}}"
            url = url.replace(':gradeId', gradeId);
            $.ajax({
                url: url,
                success: function (response) {
                    $('.bodyModel').html(response.html);
                    $('.modal-title').text('تعديل الحصة ')
                    $('#basicModal').modal('show');
                }
            });
        });
        $("#add").on('click', function () {
            $.ajax({
                url: "{{ route('sessions.create')}}",
                success: function (response) {
                    $('.bodyModel').html(response.html);
                    $('.modal-title').text('اضف حصة جديد')
                    $('#basicModal').modal('show');
                }
            });
        });
    </script>
@endsection
