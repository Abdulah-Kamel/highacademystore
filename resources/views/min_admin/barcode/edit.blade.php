@extends('min_admin.layouts.master')
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
        $.ajax({
            url: '{{route('dashboard.order.addbarcode')}}',
            type: "POST",
            dataType: "json",
            data: formData,
            contentType: false,
            processData: false,

            success: function(response) {
                console.log(response);
                Swal.fire('Data has been Updated successfully', '', 'success');
            },

            error: function(xhr, status, error) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = '';
                $.each(errors, function(key, value) {
                    errorMessage += value[0] + '<br>';
                });
                console.log(errorMessage);
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: errorMessage,
                });
            }

        });
    });
});
</script>
@endsection
