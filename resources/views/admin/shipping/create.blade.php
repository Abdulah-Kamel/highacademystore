@extends('admin.layouts.master')
@section('title')
    {{ isset($shippingMethod) ? 'تعديل طريقة الشحن' : 'إضافة طريقة شحن' }}
@endsection
@section('content')
    <div class="my-5 d-flex justify-content-center align-items-center">
        <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">

            <form class="row g-3 myform" method="POST"
                action="{{ isset($shippingMethod)
                    ? route('dashboard.shipping-methods.update', $shippingMethod->id)
                    : route('dashboard.shipping-methods.store') }}">
                @csrf
                @if (isset($shippingMethod))
                    @method('PUT')
                @endif

                <div class="col-12 text-center mb-5">
                    <h1>{{ isset($shippingMethod) ? 'تعديل طريقة الشحن' : 'إضافة طريقة شحن' }}</h1>
                </div>

                {{-- Name --}}
                <div class="col-12">
                    <label class="form-label">الاسم</label>
                    <input type="text" name="name" class="form-control form-control-lg"
                        value="{{ old('name', $shippingMethod->name ?? '') }}" required>
                </div>

                {{-- Type --}}
                <div class="col-12">
                    <label class="form-label">نوع الشحن</label>
                    <select name="type" class="form-control form-control-lg" required>
                        <option value="">اختر النوع</option>
                        <option value="post" {{ old('type', $shippingMethod->type ?? '') == 'post' ? 'selected' : '' }}>
                            مكتب
                            بريد</option>
                        <option value="home" {{ old('type', $shippingMethod->type ?? '') == 'home' ? 'selected' : '' }}>
                            توصيل
                            لباب البيت</option>
                        <option value="branch"{{ old('type', $shippingMethod->type ?? '') == 'branch' ? 'selected' : '' }}>
                            استلام
                            من المكتبة</option>
                    </select>
                </div>

                {{-- Fee --}}
                <div class="col-12">
                    <label class="form-label">رسوم الخدمة (جنيه)</label>
                    <input type="number" name="fee" step="0.01" class="form-control form-control-lg"
                        value="{{ old('fee', $shippingMethod->fee ?? '0.00') }}">
                </div>

                {{-- Governorate --}}
                <div class="col-12">
                    <label class="form-label">المحافظة</label>
                    <select name="government" class="form-control form-control-lg">
                        <option value="">اختر المحافظة</option>
                        @foreach ($govs as $g)
                            <option value="{{ $g['id'] }}"
                                {{ old('government', $shippingMethod->government ?? '') == $g['id'] ? 'selected' : '' }}>
                                {{ $g['governorate_name_ar'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Address --}}
                <div class="col-12">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="address" class="form-control form-control-lg"
                        value="{{ old('address', $shippingMethod->address ?? '') }}">
                </div>

                {{-- Phones --}}
                <div class="col-12">
                    <label class="form-label">أرقام الهاتف</label>
                    <input type="text" id="phones" name="phones" class="form-control form-control-lg"
                        placeholder="أضف رقماً ثم اضغط Enter"
                        value="{{ old('phones', isset($shippingMethod) ? implode(',', $shippingMethod->phones) : '') }}">
                </div>

                {{-- Submit --}}
                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn btn-lg btn-block btn-dark lift text-uppercase">
                        {{ isset($shippingMethod) ? 'تعديل' : 'حفظ' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection

@section('js')
    <!-- Tags input plugin (e.g. jquery-tags-input) -->
    <script>
        $(function() {
            $('#phones').tagsInput({
                width: '100%',
                height: '75px',
                interactive: true,
                defaultText: 'أضف رقم',
                removeWithBackspace: true,
                minChars: 1,
                maxChars: 15,
                placeholderColor: '#666'
            });
        });
    </script>
@endsection
