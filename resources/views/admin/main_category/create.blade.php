@extends('admin.layouts.master')

@section('title', 'Create Main Category')

@section('content')
    <center>
        <div class="col-lg-6 d-flex justify-content-center align-items-center mt-4">
            <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">
                <form id="main-category-form" class="row g-3">
                    @csrf
                    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Ensure CSRF token is available --}}

                    <div class="col-12 text-center mb-5">
                        <h1>Create Main Category</h1>
                    </div>
                    <span id="output"></span>

                    <div class="col-12">
                        <label class="form-label">اسم القسم </label>
                        <input type="text" name="name" id="name" required
                            class="form-control form-control-lg @error('name') is-invalid @enderror"
                            placeholder="أدخل اسم القسم">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">صوره القسم </label>
                        <input type="file" name="icon_image" id="icon_image"
                            class="form-control form-control-lg @error('name') is-invalid @enderror" placeholder="خطئ"">
                        @error('icon_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 text-center mt-4">
                        <button id="submit" type="submit"
                            class="btn btn-lg btn-block btn-dark lift text-uppercase">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </center>
@endsection

@section('js')

    <script>
        $(document).ready(function() {
            $('#main-category-form').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                // ✅ Log form data to the console before sending
                console.log("Form Data Before Sending:");
                for (let [key, value] of formData.entries()) {
                    console.log(key + ": " + value);
                }

                $.ajax({
                    url: '{{ route('dashboard.store.main_categories') }}', // ✅ Ensure this is the correct route
                    type: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // ✅ Add CSRF token
                    },
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        console.log("Success Response:", response);
                        $("#main-category-form")[0].reset();
                        Swal.fire('تم حفظ البيانات بنجاح', '', 'success');

                        // ✅ Optional: Refresh page after 1 second
                        setTimeout(() => location.reload(), 1000);
                    },

                    error: function(xhr) {
                        console.error("Full AJAX Error Response:", xhr);

                        let errorMessage = 'حدث خطأ غير متوقع!';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                errorMessage = '';
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    errorMessage += value[0] + '<br>';
                                });
                            } else if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                        } else {
                            errorMessage = xhr.responseText;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ في التحقق من صحة البيانات',
                            html: errorMessage,
                        });
                    }
                });
            });
        });
    </script>

@endsection
