@extends('Admin.layout.index')
@section('title')
    الامتحانات
@endsection
@section('content')
    <button type="button" id="add" class="btn btn-success mb-2">اضف امتحان جديد</button>
    <div class="card">

        <div class="card-datatable table-responsive pt-0">
            <table id="dataTable" class="datatables-basic table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>عنوان الامتحان</th>
                    <th>درجة الامتحان</th>
                    <th>الصف الدراسي</th>
                    <th>التاريخ</th>
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
                ajax: "{{ route('exams.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'title', name: 'title'},
                    {data: 'degree', name: 'degree'},
                    {data: 'grade_id', name: 'grade_id'},
                    {data: 'date', name: 'date'},
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
                text: 'هل انت متاكد من حذف هذا الامتحان',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم',
                cancelButtonText: 'لا',
            }).then((result) => {
                if (result.isConfirmed) {
                    var examId = $(this).data('id');
                    var url = "{{route('exams.destroy','examId')}}"
                    url = url.replace('examId', examId);
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
            var examId = $(this).data('id');
            var url = "{{route('exams.edit',':examId')}}"
            url = url.replace(':examId', examId);
            $.ajax({
                url: url,
                success: function (response) {
                    $('.bodyModel').html(response.html);
                    $('.modal-title').text('تعديل الامتحان ')
                    $('#basicModal').modal('show');
                }
            });
        });
        $('#dataTable').on('click', '#btnReport', function () {
            var examId = $(this).data('id');
            var url = "{{route('exams-reports.show',':examId')}}"
            url = url.replace(':examId', examId);
            $.ajax({
                url: url,
                success: function (response) {
                    $('.bodyModel').html(response.html);
                    $('.modal-title').text('تقرير الامتحان')
                    $('#basicModal').modal('show');
                }
            });
        });
        $("#add").on('click', function () {
            $.ajax({
                url: "{{ route('exams.create')}}",
                success: function (response) {
                    $('.bodyModel').html(response.html);
                    $('.modal-title').text('اضف امتحان جديد')
                    $('#basicModal').modal('show');
                }
            });
        });
    </script>
@endsection
