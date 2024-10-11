@extends('admin.layouts.master')
@section('title')
    Create Brand
@endsection
@section('content')
    <div class="col-lg-6 d-flex justify-content-center align-items-center">
        <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">

            <form id="form" class="row g-3 formvalidate" {{-- method="POST" action="{{ route('dashboard.store.teachers') }}" --}} enctype="multipart/form-data">
                @csrf
                <div class="col-12 text-center mb-5">
                    <h1>Create Teachers</h1>
                </div>
                @foreach (config('translatable.locales') as $locale)
                    <div class="col-6">
                        <label class="form-label">الاسم {{ $locale }}</label>
                        <input type="text" name="title:{{ $locale }}" id="title:{{ $locale }}"
                            data-validation="required" data-validation-required="required"
                            class="form-control form-control-lg @error('title:{{ $locale }}') is-invalid @enderror"
                            placeholder="...">
                    </div>
                    @error('title:{{ $locale }}')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                @endforeach

                @foreach (config('translatable.locales') as $locale)
                    <div class="col-6">
                        <label class="form-label">الوصف {{ $locale }}</label>
                        <input type="text" name="description:{{ $locale }}" id="description:{{ $locale }}"
                            data-validation="required" data-validation-required="required"
                            class="form-control form-control-lg @error('description:{{ $locale }}') is-invalid @enderror"
                            placeholder="...">
                    </div>
                    @error('description:{{ $locale }}')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                @endforeach

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h6>الصوره</h6>
                                <input type="file" name="photo" class="dropify @error('photo') is-invalid @enderror">
                            </div>
                        </div>
                    </div>
                    @error('photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                    url: '{{ route('dashboard.store.teachers') }}',
                    type: "POST",
                    dataType: "json",
                    data: formData,
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        $("#form")[0].reset();
                        $('.dropify-clear').click();
                        console.log(response);
                        Swal.fire('Data has been saved successfully', '', 'success');
                    },

                    error: function(xhr, status, error) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '<br>';
                        });
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
