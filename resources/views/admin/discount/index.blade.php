@extends('admin.layouts.master')
@section('title', 'القسائم')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>قائمة القسائم</h4>
            <div>

                <a href="{{ route('dashboard.discount.create') }}" class="btn btn-primary">إضافة قسيمة جديدة</a>
                <button id="toggleDiscountFeature" class="btn btn-warning">
                    @if ($discountSetting && $discountSetting->discount_enabled)
                        تعطيل الخصم
                    @else
                        تفعيل الخصم
                    @endif
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table" id="couponsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>رمز القسيمة</th>
                        <th>قيمة الخصم (بالجنيه)</th>
                        <th>حد الاستخدام</th>
                        <th>عدد الاستخدامات</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            const table = $('#couponsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.discount.datatable') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'discount',
                        name: 'discount'
                    },
                    {
                        data: 'usage_limit',
                        name: 'usage_limit'
                    },
                    {
                        data: 'used',
                        name: 'used'
                    },
                    {
                        data: 'operation',
                        name: 'operation',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Toggle Discount Feature Button
            $('#toggleDiscountFeature').click(function() {
                $.ajax({
                    url: "{{ route('dashboard.discount.toggle') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        // Update button text based on the new setting
                        if (response.discount_enabled) {
                            $('#toggleDiscountFeature').text('تعطيل الخصم');
                        } else {
                            $('#toggleDiscountFeature').text('تفعيل الخصم');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('خطأ!', xhr.responseJSON?.error || 'حدث خطأ ما!', 'error');
                    }
                });
            });


            // Delete Coupon
            $('#couponsTable').on('click', '.delete_btn', function() {
                const couponId = $(this).data('id');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "لن تتمكن من التراجع عن هذا الإجراء!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'نعم، احذفه!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('dashboard.discount.destroy') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: couponId
                            },
                            success: function(response) {
                                Swal.fire('تم الحذف!', response.message, 'success');
                                table.ajax.reload(); // Reload DataTable after deletion
                            },
                            error: function(xhr) {
                                Swal.fire('خطأ!', xhr.responseJSON?.error ||
                                    'حدث خطأ ما!', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
