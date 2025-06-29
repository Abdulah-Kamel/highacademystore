@extends('user.layouts.master')

@section('title')
    تعديل بيانات الطلب
@endsection

@section('content')
    <div class="container py-5 mt-5">
        <div class="row pt-5 justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-dropdown-menu ">
                        <h4 class="mb-0 text-white">تعديل بيانات الطلب #{{ $order->id }}</h4>
                    </div>
                    <div class="card-body">
                        <form id="orderEditForm" action="{{ route('user.order.update', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">الاسم الكامل</label>
                                    <input type="text" name="name" class="form-control" value="{{ $order->name }}"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">رقم الهاتف</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $order->mobile }}"
                                        required>
                                </div>
                            </div>



                            <div class="mb-3">
                                <label class="form-label">العنوان بالتفصيل</label>
                                <textarea name="address" class="form-control" rows="3" required>{{ $order->address }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">عنوان إضافي</label>
                                <input type="text" name="address2" class="form-control" value="{{ $order->address2 }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">أقرب مكتب بريد</label>
                                <input type="text" name="near_post" class="form-control" value="{{ $order->near_post }}" placeholder="مثال: بجوار مسجد - أمام مدرسة - إلخ">
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('user.order.details', $order->id) }}" class="btn btn-secondary">رجوع</a>
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-save me-1"></i> حفظ التغييرات
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Order Summary (Read-only) -->
                <div class="card mt-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">ملخص الطلب</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>المنتج</th>
                                        <th>الكمية</th>
                                        <th>السعر</th>
                                        <th>المجموع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->orderDetails as $item)
                                        <tr>
                                            <td>{{ $item->products->name }}</td>
                                            <td>{{ $item->amout }}</td>
                                            <td>{{ $item->price }} جنيه</td>
                                            <td>{{ $item->total_price }} جنيه</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">الإجمالي:</th>
                                        <th>{{ $order->total }} جنيه</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Handle form submission
            $('#orderEditForm').on('submit', function(e) {
                e.preventDefault(); // This is crucial

                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                const originalBtnText = submitBtn.html();

                // Validate required fields
                let isValid = true;
                form.find('[required]').each(function() {
                    if (!$(this).val()) {
                        $(this).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'الرجاء ملء جميع الحقول المطلوبة',
                        confirmButtonText: 'حسناً'
                    });
                    return;
                }

                // Show loading state
                submitBtn.prop('disabled', true)
                    .html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جاري الحفظ...'
                    );

                // Add loading overlay
                const loadingOverlay = $(
                    '<div class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background-color: rgba(0,0,0,0.5); z-index: 9999;">' +
                    '<div class="spinner-border text-light" role="status">' +
                    '<span class="visually-hidden">جاري التحميل...</span>' +
                    '</div></div>');
                $('body').append(loadingOverlay);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: form.serialize() + '&_method=PUT',
                    // In your edit.blade.php, update the AJAX success handler:
                    success: function(response) {
                        loadingOverlay.remove();
                        if (response.success && response.redirect) {
                            // Redirect immediately without showing a success message
                            window.location.href = response.redirect;
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم بنجاح',
                                text: response.message || 'تم تحديث الطلب بنجاح',
                                timer: 2000,
                                showConfirmButton: false,
                                timerProgressBar: true
                            }).then(() => {
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        loadingOverlay.remove();
                        let errorMessage = 'حدث خطأ ما. يرجى المحاولة مرة أخرى.';

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors)[0][0];

                            // Clear previous error states
                            $('.is-invalid').removeClass('is-invalid');
                            $('.invalid-feedback').remove();

                            // Add new error states
                            Object.keys(errors).forEach(field => {
                                const input = $(`[name="${field}"]`);
                                input.addClass('is-invalid');
                                input.after(
                                    `<div class="invalid-feedback">${errors[field][0]}</div>`
                                );
                            });
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: errorMessage,
                            confirmButtonText: 'حسناً'
                        });
                    }
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });

            // Remove error state on input
            $('input, select, textarea').on('input change', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            });
        });
    </script>
@endpush
