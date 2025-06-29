@extends('min_admin.layouts.master')
@section('title')
    إضافة كود جديد
@endsection
@section('content')
    <div class="col-lg-12 d-flex justify-content-center align-items-center">
        <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">

            <form id="form" class="row g-3 myform" enctype="multipart/form-data">
                @csrf
                <div class="col-12 text-center mb-5">
                    <h1>اضافة كود جديد</h1>
                    <p>{{$coupon->name}}</p>
                </div>

                <div class="col-12">
                    <label class="form-label">كود الكوبون</label>
                    <input type="text" name="code" id="code" 
                        data-validation-required="required"
                        class="form-control form-control-lg @error('code') is-invalid @enderror" placeholder="كود الاشتراك">
                </div>
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                
                <div class="col-12">
                    <label class="form-label">الصورة</label>
                    <input type="file" name="image" accept="image/*"
                        class="dropify @error('image') is-invalid @enderror">
                </div>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror


                <div class="col-12 text-center mt-4">
                    <button id="submit" type="submit"
                        class="btn btn-lg btn-block btn-dark lift text-uppercase">اضافة</button>
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
    let submitButton = $('#form button[type="submit"]');
    submitButton.prop('disabled', true);

    $.ajax({
        url: '{{ route('dashboard.mini.vouchers.store', $coupon->id) }}',
        type: "POST",
        dataType: "json",
        data: formData,
        contentType: false,
        processData: false,

        success: function(response) {
            $("#form")[0].reset();
            $('.dropify-clear').click();
            console.log(response);
            Swal.fire('تم حفظ البيانات بنجاح', '', 'success');
            
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
