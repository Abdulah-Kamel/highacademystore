@extends('Admin.layout.index')
@section('title')
    تسجيل الحضور
@endsection
@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <form id="formAdd" action="{{route('attendances.store')}}" method="post">
                @csrf
                <table id="dataTable" class="datatables-basic table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الحالة</th>
                    </tr>
                    </thead>
                </table>
                <input type="hidden" name="session_id" value="{{$id}}">
                <div class="text-center">
                    <button class="btn btn-success" type="submit">  تسجيل الحضور</button>
                </div>
            </form>
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
                ajax: "{{ route('attendances.show',$id) }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'status', name: 'status'},
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
