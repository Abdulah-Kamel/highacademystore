@extends('min_admin.layouts.master')
@section('title')
    تعديل كوبون
@endsection
@section('content')

    <div class="col-lg-12 d-flex justify-content-center align-items-center">
        <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">

            <form id="form" class="row g-3 myform" enctype="multipart/form-data">
                @csrf

                <div class="col-12 text-center mb-5">
                    <h1>تعديل كوبون</h1>
                </div>

                <div class="col-12">
                    <label class="form-label">اسم الكوبون</label>
                    <input type="text" name="name" id="name" value="{{ $coupon->name }}" data-validation="required"
                        data-validation-required="required"
                        class="form-control form-control-lg @error('name') is-invalid @enderror" placeholder="...">
                </div>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="col-12">
                    <label class="form-label">السعر</label>
                    <input type="text" name="price" id="price" value="{{ $coupon->price }}" data-validation="required"
                        data-validation-required="required"
                        class="form-control form-control-lg @error('price') is-invalid @enderror" placeholder="...">
                </div>
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                 <div class="col-12">
                    <label class="form-label">النوع</label>
                    <select type="text" name="type" id="type" data-validation="required"
                        data-validation-required="required"
                        class="form-control form-control-lg @error('type') is-invalid @enderror" placeholder="...">
                        <option value="weekly" @if($coupon->type == "weekly") selected @endif</option>اسبوعي</option>
                        <option value="monthly" @if($coupon->type == "monthly") selected @endif>شهري</option>
                        <option value="package" @if($coupon->type == "package") selected @endif>باقة</option>
                        </select>
                </div>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                
                <div class="col-12">
                    <label class="form-label">الصورة</label>
                    <input type="file" name="image" accept="image/*"
                        class="dropify @error('image') is-invalid @enderror" data-default-file="{{ $coupon->image_path }}">
                </div>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="col-12 text-center mt-4">
                    <button id="submit" type="submit"
                        class="btn btn-lg btn-block btn-dark lift text-uppercase">حفظ التعديلات</button>
                </div>
            </form>

        </div>
    </div>
@endsection

@section('js')
    <script>
        $.validate({
            form: 'form'
        });

        $(document).ready(function() {

            $('#form').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                let submitButton = $('#form button[type="submit"]'); // تحديد الزر الذي يتم الضغط عليه

                // تعطيل الزر عند إرسال النموذج
                submitButton.prop('disabled', true);

                $.ajax({
                    url: '{{ route('dashboard.mini.coupons.update', $coupon->id) }}', // تعديل المسار للتحديث
                    type: "POST",
                    dataType: "json",
                    data: formData,
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        Swal.fire('تم حفظ التعديلات بنجاح', '', 'success');

                        // إعادة تمكين الزر بعد نجاح العملية
                        submitButton.prop('disabled', false);
                    },

                    error: function(xhr, status, error) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '<br>';
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ في التحقق',
                            html: errorMessage,
                        });

                        // إعادة تمكين الزر في حالة حدوث خطأ
                        submitButton.prop('disabled', false);
                    }

                });
            });

        });
    </script>
@endsection
