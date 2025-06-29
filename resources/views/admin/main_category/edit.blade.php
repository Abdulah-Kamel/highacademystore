@extends('admin.layouts.master')

@section('title', 'Edit Main Category')

@section('content')
    <center>
        <div class="col-lg-6 d-flex justify-content-center align-items-center mt-4">
            <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">
                <form id="main-category-form" class="row g-3" enctype="multipart/form-data">
                    @csrf
                    @method('post') 
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    <input type="hidden" name="category_id" id="category_id" value="{{ $category->id }}">

                    <div class="col-12 text-center mb-5">
                        <h1>Edit Main Category</h1>
                    </div>
                    <span id="output"></span>

                    {{-- ✅ Category Name --}}
                    <div class="col-12">
                        <label class="form-label">اسم القسم </label>
                        <input type="text" name="name" id="name" value="{{ $category->name }}" required
                            class="form-control form-control-lg @error('name') is-invalid @enderror"
                            placeholder="أدخل اسم القسم">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ✅ Image Upload --}}
                    <div class="col-12">
                        <label class="form-label">صوره القسم </label>
                        <input type="file" name="icon_image" id="icon_image"
                            class="form-control form-control-lg @error('icon_image') is-invalid @enderror">
                        @error('icon_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ✅ Show Existing Image OR FontAwesome Icon --}}
                    <div class="col-12 text-center mt-3">
                        <label class="form-label">الصورة الحالية / الأيقونة</label>
                        <div id="preview-container">
                            @if ($category->icon_image)
                                <img id="preview-image" src="{{ asset('storage/' . $category->icon_image) }}"
                                    alt="Current Icon" class="img-fluid" style="max-width: 100px;">
                            @elseif ($category->icon)
                                <i id="preview-icon" class="{{ $category->icon }} fa-5x text-primary"></i>
                            @else
                                <p class="text-muted">لا توجد صورة أو أيقونة</p>
                            @endif
                        </div>
                    </div>

                    {{-- ✅ Submit Button --}}
                    <div class="col-12 text-center mt-4">
                        <button id="submit" type="submit"
                            class="btn btn-lg btn-block btn-dark lift text-uppercase">Update</button>
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
                let categoryId = $('#category_id').val(); 

                console.log("Form Data Before Sending:");
                for (let [key, value] of formData.entries()) {
                    console.log(key + ": " + value);
                }

                $.ajax({
                    url: '{{ url('dashboard/main_categories/update') }}/' +
                    categoryId,
                    type: "POST", 
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-HTTP-Method-Override': 'PUT' 
                    },
                    data: formData,
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        Swal.fire('تم تحديث البيانات بنجاح', '', 'success');
                        setTimeout(() => window.location.href =
                            '{{ route('dashboard.main_categories') }}', 1000);
                    },
                    error: function(xhr) {
                        console.error("Full AJAX Error Response:", xhr);
                    }
                });

            });
        });
    </script>

@endsection
