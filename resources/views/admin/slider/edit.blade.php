@extends('admin.layouts.master')
@section('title')
update sliders
@endsection
@section('content')
<center>
    <div class="col-lg-6 d-flex justify-content-center align-items-center mt-4">
        <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">

            <form id="form" class="row g-3" {{-- method="POST" action="{{ route('dashboard.store.slider') }}" --}}
                enctype="multipart/form-data">
                @csrf
                <div class="col-12 text-center mb-5">
                    <h1>Edit slider</h1>
                </div>
                @foreach (config('translatable.locales') as $locale)
                <div class="col-12">
                    <label class="form-label">الاسم {{ $locale }}</label>
                    <input type="text" name="title:{{ $locale }}" id="title:{{ $locale }}" data-validation="required"
                        data-validation-required="required"
                        class="form-control form-control-lg @error('title:{{ $locale }}') is-invalid @enderror"
                        value="{{ $slider->translate($locale)->title }}" placeholder="...">
                </div>
                @error('title:{{ $locale }}')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror


                <div class="col-12">
                    <label class="form-label">المرحلة الدراسية</label>
                    <select name="stage_id" id="stage" data-validation="required" data-validation-required="required"
                        class="form-control form-control-lg @error('stage_id') is-invalid @enderror">
                        <option value="">اختار مرحلة</option>
                        @foreach ($stages as $s){
                        <option value="{{ $s->id }}" @if (isset($slider->stage->id) && $slider->stage->id == $s->id)
                            selected
                            @endif>{{ $s->title }}</option>
                        }

                        @endforeach
                    </select>
                </div>
                @error('stage')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                @endforeach
                <!-- @foreach (config('translatable.locales') as $locale)
            <div class="col-6">
                <label class="form-label">الوصف {{ $locale }}</label>
                <input type="text" name="description:{{ $locale }}" id="description:{{ $locale }}"
                    data-validation="required" data-validation-required="required"
                    class="form-control form-control-lg @error('description:{{ $locale }}') is-invalid @enderror"
                    value="{{$slider->translate($locale)->description}}" placeholder="...">
            </div>
            @error('description:{{ $locale }}')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @endforeach
            <div class="col-12">
                <label class="form-label">is active</label>
                <select class="form-control show-tick ms select2 @error('is_active') is-invalid @enderror"
                name="is_active" data-placeholder="Select" id="is_active" data-validation="required" data-validation-required="required">
                  <option></option>
                  <option value="0" {{(($slider->is_active == '0') ? 'selected' : '')}}>disactive</option>
                  <option value="1" {{(($slider->is_active == '1') ? 'selected' : '')}}>active</option>
                </select>
            </div>
            @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="col-12">
                {{-- <img src="{{ $slider->image_path }}" class="avatar rounded me-2"
                                alt="profile-image" style="height:120px;width:150px"> --}}
                <div class="card">
                    <div class="card-body">
                        <h6>الصوره</h6>
                        <input type="file" data-default-file="{{ $slider->image_path }}" name="photo" class="dropify @error('photo') is-invalid @enderror">
                    </div>
                </div>
            </div>
            @error('photo')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror -->
                <div class="col-6">
                    <input type="text" name="slider_id" class="form-control form-control-lg d-none"
                        value="{{$slider->id}}">
                </div>
                <div class="col-12 text-center mt-4">
                    <button id="submit" type="submit"
                        class="btn btn-lg btn-block btn-dark lift text-uppercase">Update</button>
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
            url: '{{route('dashboard.slider.update')}}',
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
