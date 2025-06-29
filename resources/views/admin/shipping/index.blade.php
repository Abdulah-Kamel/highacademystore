@extends('admin.layouts.master')

@section('title')
    طرق الشحن
@endsection

@section('content')
    <style>
        table th,
        tr,
        td {
            font-size: 20px;
        }
    </style>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="dropdown morphing scale-left">
                    <a href="#" class="card-fullscreen" data-bs-toggle="tooltip" title="Card Full-Screen">
                        <i class="icon-size-fullscreen"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center my-5">
                    <h2 class="card-title mb-0">طرق الشحن</h2>
                    <button onclick="location.href='{{ route('dashboard.shipping-methods.create') }}'"
                        class="btn btn-lg btn-primary mb-3">
                        إضافة طريقة شحن جديدة
                    </button>
                </div>

                <table class="table table-hover align-middle mb-0" id="shippingTable" dir="rtl">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>الاسم</th>
                            <th>المحافظة</th>
                            <th>النوع</th>
                            <th>العنوان</th>
                            <th>الأرقام</th>
                            <th>رسوم الخدمة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    {{-- Make sure you have included DataTables & SweetAlert scripts in your master layout or below --}}
    <script>
        $(document).ready(function() {
            $('#shippingTable').DataTable({
                lengthMenu: [
                    [10, 25, 50, 100, 200, -1],
                    [10, 25, 50, 100, 200, "الكل"]
                ],
                paging: true,
                pageLength: 10,
                stateSave: true,
                stateDuration: -1,
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: '{{ route('dashboard.shipping-methods.datatable') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'gov_name',
                        name: 'gov_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type_label',
                        name: 'type_label',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'phones_list',
                        name: 'phones_list',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'fee',
                        name: 'fee',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });

        function deleteShippingMethod(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من التراجع عن هذا!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، احذفه!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/dashboard/shipping-methods/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(resp => {
                        if (resp.ok) {
                            $('#shippingTable').DataTable().ajax.reload();
                            Swal.fire('تم الحذف!', 'تم حذف طريقة الشحن.', 'success');
                        } else {
                            Swal.fire('خطأ!', 'حدث خطأ أثناء الحذف.', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
