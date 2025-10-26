@extends('admin.layouts.master')
@section('title')
    تعديل المحافظة
@endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">تعديل المحافظة</h6>
                <div class="dropdown morphing scale-left">
                    <a href="#" class="card-fullscreen" data-bs-toggle="tooltip" title="Card Full-Screen"><i
                            class="icon-size-fullscreen"></i></a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('dashboard.governorates.update', $governorate->id) }}" method="POST" id="governorateForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="governorate_name_ar" class="form-label">الاسم باللغة العربية <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('governorate_name_ar') is-invalid @enderror"
                                       id="governorate_name_ar"
                                       name="governorate_name_ar"
                                       value="{{ old('governorate_name_ar', $governorate->governorate_name_ar) }}"
                                       placeholder="أدخل اسم المحافظة بالعربية"
                                       required>
                                @error('governorate_name_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="governorate_name_en" class="form-label">الاسم باللغة الإنجليزية <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('governorate_name_en') is-invalid @enderror"
                                       id="governorate_name_en"
                                       name="governorate_name_en"
                                       value="{{ old('governorate_name_en', $governorate->governorate_name_en) }}"
                                       placeholder="Enter governorate name in English"
                                       required>
                                @error('governorate_name_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="home_shipping_price" class="form-label">
                                    سعر التوصيل للمنزل (جنيه) <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('home_shipping_price') is-invalid @enderror"
                                       id="home_shipping_price"
                                       name="home_shipping_price"
                                       value="{{ old('home_shipping_price', $governorate->home_shipping_price ?? $governorate->price) }}"
                                       placeholder="0.00"
                                       step="0.01"
                                       min="0"
                                       required>
                                @error('home_shipping_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="post_shipping_price" class="form-label">
                                    سعر التوصيل لمكتب البريد (جنيه) <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('post_shipping_price') is-invalid @enderror"
                                       id="post_shipping_price"
                                       name="post_shipping_price"
                                       value="{{ old('post_shipping_price', $governorate->post_shipping_price ?? $governorate->price) }}"
                                       placeholder="0.00"
                                       step="0.01"
                                       min="0"
                                       required>
                                @error('post_shipping_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('dashboard.governorates.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-right me-2"></i>العودة للقائمة
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-2"></i>تحديث المحافظة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    // Form validation
    document.getElementById('governorateForm').addEventListener('submit', function(e) {
        const nameAr = document.getElementById('governorate_name_ar').value.trim();
        const nameEn = document.getElementById('governorate_name_en').value.trim();
        const homePrice = document.getElementById('home_shipping_price').value;
        const postPrice = document.getElementById('post_shipping_price').value;

        if (nameAr === '') {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب إدخال اسم المحافظة بالعربية'
            });
            return false;
        }

        if (nameEn === '') {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب إدخال اسم المحافظة بالإنجليزية'
            });
            return false;
        }

        if (homePrice === '' || parseFloat(homePrice) < 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب إدخال سعر التوصيل للمنزل بشكل صحيح'
            });
            return false;
        }

        if (postPrice === '' || parseFloat(postPrice) < 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب إدخال سعر التوصيل لمكتب البريد بشكل صحيح'
            });
            return false;
        }

        return true;
    });
</script>
@endsection
