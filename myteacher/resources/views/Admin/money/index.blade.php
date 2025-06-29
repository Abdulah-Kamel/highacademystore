@extends('Admin.layout.index')
@section('title')
    دفع فلوس الشهر
@endsection
@section('content')
    @php
        \Carbon\Carbon::setLocale('ar');
        $monthName = \Carbon\Carbon::now()->translatedFormat('F');
    @endphp
    <div class="text-center">
        <h4>دفع فلوس شهر {{$monthName}}</h4>
    </div>
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table id="dataTable" class="datatables-basic table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>الحالة</th>
                    <th>ألاجراء</th>
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
                ajax: "{{ route('money.show',$id) }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'status', name: 'status'},
                    {data: 'actions', name: 'actions'},
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
        $('#dataTable').on('click', '#btnPay', function () {
            var studentId = $(this).data('id');
            var recordId = $(this).data('extra');
            var url = "{{route('money.store')}}"
            // url = url.replace(':studentId', studentId);
            $.ajax({
                url: url,
                type: 'Post',
                data: {
                    student_id: studentId,
                    recordId: recordId,
                    is_paid: 1,
                    month: '{{ \Carbon\Carbon::now()->locale("ar")->translatedFormat("F") }}',
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    toastr.options = {
                        positionClass: 'toast-top-left',

                    };
                    toastr.success(response.success)
                    myTable.ajax.reload();
                }
            });
        });
        $('#dataTable').on('click', '#btnNotPay', function () {
            var recordId = $(this).data('id');
            var url = "{{route('money.update',':recordId')}}"
            url = url.replace(':recordId', recordId);
            $.ajax({
                url: url,
                type: 'Put',
                data: {
                    recordId: recordId,
                    is_paid: 0,
                    month: '{{ \Carbon\Carbon::now()->locale("ar")->translatedFormat("F") }}',
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    toastr.options = {
                        positionClass: 'toast-top-left',

                    };
                    toastr.success(response.success)
                    myTable.ajax.reload();
                }
            });
        });
    </script>
@endsection
