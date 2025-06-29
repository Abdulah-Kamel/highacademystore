@extends('admin.layouts.master')

@section('title', 'إنشاء قسيمة خصم')

@section('content')
    <div class="col-lg-6 d-flex justify-content-center align-items-center">
        <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">
            <form id="couponForm" class="row g-3 myform" method="POST" action="{{ route('dashboard.discount.store') }}">
                @csrf

                <div class="col-12 text-center mb-5">
                    <h1>إضافة قسيمة خصم جديدة</h1>
                </div>

                {{-- Coupon Code --}}
                <div class="col-12">
                    <label class="form-label">رمز القسيمة</label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" required
                        placeholder="أدخل رمز القسيمة">
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Fixed Discount Amount --}}
                <div class="col-12">
                    <label class="form-label">قيمة الخصم (بالجنيه)</label>
                    <input type="number" name="discount" class="form-control @error('discount') is-invalid @enderror"
                        required min="1" step="0.01" placeholder="أدخل قيمة الخصم">
                    @error('discount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Usage Limit (Optional) --}}
                <div class="col-12">
                    <label class="form-label">حد الاستخدام (اختياري)</label>
                    <input type="number" name="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror"
                        min="1" placeholder="أدخل حد الاستخدام إن وجد">
                    @error('usage_limit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn btn-lg btn-block btn-dark lift text-uppercase">حفظ القسيمة</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#couponForm').submit(function(e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: '{{ route('dashboard.discount.store') }}',
                    type: "POST",
                    dataType: "json",
                    data: formData,
                    success: function(response) {
                        Swal.fire('تم حفظ القسيمة بنجاح', '', 'success').then(() => {
                            window.location.href = "{{ route('dashboard.discount') }}";
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = xhr.responseJSON?.error || xhr.responseText;
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            html: errorMessage
                        });
                    }
                });
            });
        });
    </script>
@endsection
