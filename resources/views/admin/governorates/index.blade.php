@extends('admin.layouts.master')
@section('title')
    إدارة المحافظات
@endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">إدارة المحافظات</h6>
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
                    <h5 class="card-title mb-0">المحافظات</h5>
                    <a href="{{ route('dashboard.governorates.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-2"></i>إضافة محافظة جديدة
                    </a>
                </div>

                <table class="table table-hover align-middle mb-0" id="governoratesTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم بالعربية</th>
                            <th>الاسم بالإنجليزية</th>
                            <th>أسعار الشحن</th>
                            <th>عدد المدن</th>
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
        $('#governoratesTable').DataTable({
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "الكل"]
            ],
            "pageLength": 10,
            "stateSave": true,
            "stateDuration": -1,
            'scrollX': true,
            "processing": true,
            "serverSide": true,
            "sort": false,
            "ajax": {
                "url": "{{ route('dashboard.governorates.datatable') }}",
                "type": "GET"
            },
            "columns": [
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name_ar',
                    name: 'name_ar'
                },
                {
                    data: 'name_en',
                    name: 'name_en'
                },
                {
                    data: 'shipping_price',
                    name: 'shipping_price'
                },
                {
                    data: 'cities_count',
                    name: 'cities_count'
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

    function deleteGovernorate(id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'لن تتمكن من استرجاع هذه المحافظة بعد الحذف!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("dashboard.governorates.destroy", ":id") }}'.replace(':id', id),
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
                            $('#governoratesTable').DataTable().ajax.reload();
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
                            'حدث خطأ أثناء حذف المحافظة',
                            'error'
                        );
                    }
                });
            }
        });
    }
</script>
@endsection
