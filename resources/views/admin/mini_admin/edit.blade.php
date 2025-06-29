@extends('admin.layouts.master')
@section('title')
edit Min Admin
@endsection
@section('content')
<center>
    <div class="col-lg-6 d-flex justify-content-center align-items-center mt-4">
        <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">

            <form id="form" class="row g-3"
                enctype="multipart/form-data">
                @csrf
                <div class="col-12 text-center mb-5">
                    <h1>edit Min Admin</h1>
                </div>
                <span id="output"></span>
                <div class="col-12">
                    <label class="form-label">الاسم </label>
                    <input type="text" name="name" id="name"
                        value="{{$miniAdmin->name}}"
                        class="form-control form-control-lg @error('name') is-invalid @enderror"
                        placeholder="...">
                </div>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="col-12">
                    <label class="form-label">البريد الالكتروني</label>
                    <input type="email" name="email" id="email" 
                        value="{{$miniAdmin->email}}"
                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                        placeholder="...">
                </div>
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="col-12">
                    <label class="form-label">كلمة السر</label>
                    <input type="password" name="password" id="password"
                    value="{{$miniAdmin->password}}"
                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                        placeholder="...">
                </div>
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <input type="text" name="min_admin_id" class="form-control form-control-lg d-none"
                        value="{{$miniAdmin->id}}">

                <div class="col-12 text-center mt-4">
                    <button id="submit" type="submit"
                        class="btn btn-lg btn-block btn-dark lift text-uppercase">Save</button>
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
                url: '{{route('dashboard.minadmin.update')}}',
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
