@extends('admin.layouts.master')

@section('title', 'Edit Offer')

@section('content')
    <div class="col-lg-6 d-flex justify-content-center align-items-center">
        <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">

            <form id="offerForm" class="row g-3 myform" method="POST" action="{{ route('dashboard.offers.update', $offer->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="col-12 text-center mb-5">
                    <h1>تعديل العرض</h1>
                </div>

                {{-- Current Offer Image --}}
                <div class="col-12">
                    <label class="form-label">الصورة الحالية</label>
                    <img src="{{ asset('storage/images/offers/' . $offer->image) }}" alt="Offer Image" class="img-fluid rounded mb-3" width="200">
                </div>

                {{-- New Image Upload --}}
                <div class="col-12">
                    <label class="form-label">استبدال الصورة</label>
                    <input type="file" name="image" accept="image/*" class="dropify @error('image') is-invalid @enderror">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Discount Type --}}
                <div class="col-12">
                    <label class="form-label">نوع الخصم</label>
                    <select name="type" class="form-control @error('type') is-invalid @enderror">
                        <option value="percentage" {{ $offer->type == 'percentage' ? 'selected' : '' }}>نسبة مئوية (%)</option>
                        <option value="fixed" {{ $offer->type == 'fixed' ? 'selected' : '' }}>قيمة ثابتة (جنيه)</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Discount Value --}}
                <div class="col-12">
                    <label class="form-label">قيمة الخصم</label>
                    <input type="number" name="value" class="form-control @error('value') is-invalid @enderror" value="{{ $offer->value }}">
                    @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Minimum Books Required --}}
                <div class="col-12">
                    <label class="form-label">عدد الكتب الأدنى لتطبيق العرض</label>
                    <input type="number" name="minimum_books" class="form-control @error('minimum_books') is-invalid @enderror" value="{{ $offer->minimum_books }}">
                    @error('minimum_books')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn btn-lg btn-block btn-dark lift text-uppercase">تحديث العرض</button>
                </div>
            </form>

        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('.dropify').dropify(); // ✅ Initialize image uploader

        $('#offerForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: '{{ route('dashboard.offers.update', $offer->id) }}',
                type: "POST",
                dataType: "json",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    Swal.fire('تم تحديث العرض بنجاح', '', 'success').then(() => {
                        window.location.href = "{{ route('dashboard.offers') }}";
                    });
                },
                error: function(xhr) {
                    let errorMessage = xhr.responseJSON.error || xhr.responseText;
                    Swal.fire({ icon: 'error', title: 'خطأ', html: errorMessage });
                }
            });
        });
    });
</script>
@endsection
