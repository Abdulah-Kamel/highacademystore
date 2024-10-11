@extends('admin.layouts.master')
@section('title')
Edit category
@endsection
@section('content')
<div class="col-lg-6 d-flex justify-content-center align-items-center">
    <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">

        <form id="form" class="row g-3" {{-- method="POST" action="{{ route('dashboard.store.category') }}" --}}
            enctype="multipart/form-data">
            @csrf
            <div class="col-12 text-center mb-5">
                <h1>Edit category</h1>
            </div>
            @foreach (config('translatable.locales') as $locale)
            <div class="col-6">
                <label class="form-label">الاسم {{ $locale }}</label>
                <input type="text" name="title:{{ $locale }}" id="title:{{ $locale }}" data-validation="required"
                    data-validation-required="required"
                    class="form-control form-control-lg @error('title:{{ $locale }}') is-invalid @enderror"
                    value="{{ $category->translate($locale)->title }}" placeholder="...">
            </div>
            @error('title:{{ $locale }}')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @endforeach
            {{-- <div class="col-6">
                <label class="form-label">Is Parent : </label>
                <input id="is_parent" type="checkbox" name="is_parent" value="1" {{$category->is_parent==1 ? 'checked' :
                ''}}> Yes
            </div>
            <div class="col-12 {{$category->is_parent==1 ? 'd-none' : ''}}" id="parent_cat_div">
                <label class="form-label">Parent ID</label>
                <select class="form-control show-tick ms select2 @error('parent_id') is-invalid @enderror"
                    name="parent_id">
                    <option value="">Select Your Option</option>
                    @foreach ($cats as $cat)
                    <option value="{{$cat->id}}" {{$cat->id == $category->parent_id ? 'selected' : ''}} >{{$cat->title}}
                    </option>
                    @endforeach
                </select>
            </div>
            @error('parent_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror --}}
            <div class="col-12">
                {{-- <img src="{{ $category->image_path }}" class="avatar rounded me-2" alt="profile-image" --}}
                    {{-- style="height:120px;width:150px"> --}}
                <div class="card">
                    <div class="card-body">
                        <h6>الصوره</h6>
                        <input type="file" data-default-file="{{ $category->image_path }}" name="photo" class="dropify @error('photo') is-invalid @enderror">
                    </div>
                </div>
            </div>
            @error('photo')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="col-6">
                <input type="text" name="category_id" class="form-control form-control-lg d-none"
                    value="{{$category->id}}">
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
    $('#is_parent').change(function(e) {
        e.preventDefault();
        var is_checked = $('#is_parent').prop('checked');
        if (is_checked) {
            $('#parent_cat_div').addClass('d-none');
            $('#parent_cat_div').val('');
        } else {
            $('#parent_cat_div').removeClass('d-none');
        }
    });
</script>
<script>
    $.validate({
        form: 'form'
    });
    $(document).ready(function() {
    $('#form').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: '{{route('dashboard.category.update')}}',
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
