@extends('admin.layouts.master')
@section('title')
    الأسئلة الشائعة
@endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">إدارة الأسئلة الشائعة</h6>
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">الأسئلة الشائعة</h5>
                    <div class="d-flex gap-2">
                        <form action="{{ route('dashboard.faqs.cleanup-duplicates') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('هل تريد تنظيف الترتيبات المكررة؟')">
                                <i class="fa fa-broom me-2"></i>تنظيف المكررات
                            </button>
                        </form>
                        <a href="{{ route('dashboard.faqs.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>إضافة سؤال جديد
                        </a>
                    </div>
                </div>

                <table class="table table-hover align-middle mb-0" id="faqsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>السؤال</th>
                            <th>الإجابة</th>
                            <th>الترتيب</th>
                            <th>الحالة</th>
                            <th>العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data will be loaded via AJAX --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#faqsTable').DataTable({
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
                "url": "{{ route('dashboard.faqs.datatable') }}",
                "type": "GET"
            },
            "columns": [
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'question',
                    name: 'question'
                },
                {
                    data: 'answer',
                    name: 'answer'
                },
                {
                    data: 'display_order',
                    name: 'display_order'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json"
            }
        });
    });

    function deleteFaq(id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'لن تتمكن من استرجاع هذا السؤال والإجابة بعد الحذف!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("dashboard.faqs.destroy", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'تم الحذف!',
                                response.message,
                                'success'
                            );
                            $('#faqsTable').DataTable().ajax.reload();
                        } else {
                            Swal.fire(
                                'خطأ!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'خطأ!',
                            'حدث خطأ أثناء حذف السؤال والإجابة',
                            'error'
                        );
                    }
                });
            }
        });
    }
</script>
@endsection
