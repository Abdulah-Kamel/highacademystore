@extends('admin.layouts.master')

@section('title')
    تعديل المدينة
@endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">تعديل المدينة</h6>
                <div class="dropdown morphing scale-left">
                    <a href="#" class="card-fullscreen" data-bs-toggle="tooltip" title="عرض ملء الشاشة">
                        <i class="icon-size-fullscreen"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('dashboard.cities.update', $city->id) }}" method="POST" id="cityEditForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="governorate_id" class="form-label">
                                    المحافظة <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('governorate_id') is-invalid @enderror"
                                        id="governorate_id"
                                        name="governorate_id"
                                        required>
                                    <option value="">اختر المحافظة</option>
                                    @foreach ($governorates as $governorate)
                                        <option value="{{ $governorate->id }}"
                                            {{ (int) old('governorate_id', $city->governorate_id) === (int) $governorate->id ? 'selected' : '' }}>
                                            {{ $governorate->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('governorate_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name_ar" class="form-label">
                                    الاسم باللغة العربية <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('name_ar') is-invalid @enderror"
                                       id="name_ar"
                                       name="name_ar"
                                       value="{{ old('name_ar', $city->name_ar) }}"
                                       placeholder="أدخل اسم المدينة بالعربية"
                                       required>
                                @error('name_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name_en" class="form-label">
                                    الاسم باللغة الإنجليزية <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('name_en') is-invalid @enderror"
                                       id="name_en"
                                       name="name_en"
                                       value="{{ old('name_en', $city->name_en) }}"
                                       placeholder="City name in English"
                                       required>
                                @error('name_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check mt-4">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="status"
                                       name="status"
                                       value="1"
                                       @checked(old('status', $city->status))>
                                <label class="form-check-label" for="status">
                                    المدينة نشطة؟
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('dashboard.cities.index', ['governorate' => $city->governorate_id]) }}"
                           class="btn btn-secondary">
                            <i class="fa fa-arrow-right me-2"></i>العودة للقائمة
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-2"></i>تحديث المدينة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.getElementById('cityEditForm').addEventListener('submit', function (event) {
            const governorate = document.getElementById('governorate_id').value.trim();
            const nameAr = document.getElementById('name_ar').value.trim();
            const nameEn = document.getElementById('name_en').value.trim();

            if (!governorate) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'يرجى اختيار المحافظة'
                });
                return;
            }

            if (!nameAr) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'يرجى إدخال اسم المدينة بالعربية'
                });
                return;
            }

            if (!nameEn) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'يرجى إدخال اسم المدينة بالإنجليزية'
                });
            }
        });
    </script>
@endsection
