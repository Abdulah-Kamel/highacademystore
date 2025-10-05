@extends('admin.layouts.master')
@section('title')
    أضافه الباركود
@endsection
@section('content')

    <div class="col-lg-12 d-flex justify-content-center align-items-center">
        <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">

            <form id="form" class="row g-3 myform">
                @csrf
                <div class="col-12 text-center mb-5">
                    <h1>أضافه الباركود</h1>
                </div>

                <div class="col-12">
                    <label class="form-label">الباركود</label>
                    <input type="text" name="barcode" id="barcode"
                        value="{{ $order->barcode }}"
                        data-validation-required="required"
                        class="form-control form-control-lg @error('barcode') is-invalid @enderror" placeholder="...">
                </div>
                @error('barcode')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <input type="text" name="id" class="form-control form-control-lg d-none"
                        value="{{$order->id}}">

                <div class="col-12 text-center mt-4">
                    <button id="submit" type="submit"
                        class="btn btn-lg btn-block btn-dark lift text-uppercase">
                        <span class="btn-text">حفظ التعديلات</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="loading-text d-none">جاري الحفظ...</span>
                    </button>
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
        let submitBtn = $('#submit');
        
        // Show loading state
        submitBtn.prop('disabled', true);
        submitBtn.find('.btn-text').addClass('d-none');
        submitBtn.find('.spinner-border').removeClass('d-none');
        submitBtn.find('.loading-text').removeClass('d-none');

        $.ajax({
            url: '{{route('dashboard.orders.addbarcode')}}',
            type: "POST",
            dataType: "json",
            data: formData,
            contentType: false,
            processData: false,

            success: function(response) {
                // Hide loading state
                resetButton();
                
                if(response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'نجح الحفظ',
                        text: response.msg || 'تم حفظ الباركود بنجاح',
                        confirmButtonText: 'موافق'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: response.msg || 'حدث خطأ أثناء الحفظ',
                        confirmButtonText: 'موافق'
                    });
                }
            },

            error: function(xhr, status, error) {
                // Hide loading state
                resetButton();
                
                let errorMessage = '';
                
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    // Validation errors
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        errorMessage += value[0] + '<br>';
                    });
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في التحقق من البيانات',
                        html: errorMessage,
                        confirmButtonText: 'موافق'
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.msg) {
                    // Server error with message
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: xhr.responseJSON.msg,
                        confirmButtonText: 'موافق'
                    });
                } else {
                    // Generic error
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في الاتصال',
                        text: 'حدث خطأ أثناء الاتصال بالخادم. يرجى المحاولة مرة أخرى.',
                        confirmButtonText: 'موافق'
                    });
                }
            }
        });
        
        function resetButton() {
            submitBtn.prop('disabled', false);
            submitBtn.find('.btn-text').removeClass('d-none');
            submitBtn.find('.spinner-border').addClass('d-none');
            submitBtn.find('.loading-text').addClass('d-none');
        }
    });
});
</script>
@endsection
