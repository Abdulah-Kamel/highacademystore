@extends('Admin.layout.index')
@section('title')
    تسجيل الدرجات
@endsection
<link rel="stylesheet" href="{{asset('Admin')}}/vuexy-html-admin-template/assets/vendor/libs/select2/select2.css"/>
@section('content')
    <h3 style="text-align: center">{{$exam->title}} ({{$exam->degree}}) - {{$grade->name}} </h3>
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <form id="formAdd" action="{{route('exams-students.store')}}" method="post">
                @csrf
                <table id="dataTable" class="datatables-basic table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>درجة الطالب</th>
                    </tr>
                    </thead>
                </table>
                <input type="hidden" name="exam_id" value="{{$exam->id}}">
                <div class="text-center">
                    <button class="btn btn-success" type="submit"> تسجيل</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
            integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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
                ajax: "{{ route('exams-students.show',$exam->id) }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {
                        data: 'student_degree', name: 'actions',
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

        $(document).ready(function () {
            $('#formAdd').on('submit', function (e) {
                var url = $(this).attr('action');
                e.preventDefault();
                $.ajax({
                    url: url,
                    method: "POST",
                    data: new FormData(this),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (response) {
                        if (response.success) {
                            toastr.options = {
                                positionClass: 'toast-top-left',
                            };
                            toastr.success(response.success)

                            myTable.ajax.reload();
                        }
                        if (response.error) {
                            toastr.options = {
                                positionClass: 'toast-top-left',
                            };
                            toastr.error(response.error)
                            myTable.ajax.reload();
                        }
                    },
                    error: function (xhr, status, error) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function (key, value) {
                            errorMessage += value[0] + '<br>';
                        });
                        Swal.fire({
                            icon: 'error',
                            title: '',
                            html: errorMessage,
                        });
                    }
                })
            })
        });
    </script>
@endsection

