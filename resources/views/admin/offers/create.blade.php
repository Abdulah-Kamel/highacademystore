@extends('admin.layouts.master')

@section('title', 'Create Offer')

@section('content')
    <div class="col-lg-6 d-flex justify-content-center align-items-center">
        <div class="card shadow-sm w-100 p-4 p-md-5" style="max-width: 64rem;">

            <form id="offerForm" class="row g-3 myform" method="POST" action="{{ route('dashboard.store.offers') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="col-12 text-center mb-5">
                    <h1>إضافة عرض جديد</h1>
                </div>

                {{-- Offer Image --}}
                <div class="col-12">
                    <label class="form-label">الصورة</label>
                    <input type="file" name="image" accept="image/*" class="dropify @error('image') is-invalid @enderror">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Discount Type --}}
                <div class="col-12">
                    <label class="form-label">نوع الخصم</label>
                    <select name="type" id="discountType" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="percentage">نسبة مئوية (%)</option>
                        <option value="fixed">مبلغ ثابت (EGP)</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Discount Value --}}
                <div class="col-12">
                    <label class="form-label">قيمة الخصم</label>
                    <input type="number" name="value" id="discountValue" class="form-control @error('value') is-invalid @enderror" required min="1" placeholder="أدخل قيمة الخصم">
                    @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Minimum Books for Offer --}}
                <div class="col-12">
                    <label class="form-label">عدد الكتب المطلوب للحصول على الخصم</label>
                    <input type="number" name="minimum_books" class="form-control @error('minimum_books') is-invalid @enderror" required min="1" placeholder="أدخل عدد الكتب المطلوبة">
                    @error('minimum_books')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn btn-lg btn-block btn-dark lift text-uppercase">حفظ العرض</button>
                </div>
            </form>

        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('.dropify').dropify(); // ✅ Initialize Dropify for file preview

        $('#offerForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: '{{ route('dashboard.store.offers') }}',
                type: "POST",
                dataType: "json",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    Swal.fire('تم حفظ العرض بنجاح', '', 'success').then(() => {
                        window.location.href = "{{ route('dashboard.offers') }}";
                    });
                },
                error: function(xhr) {
                    let errorMessage = xhr.responseJSON?.error || xhr.responseText;
                    Swal.fire({ icon: 'error', title: 'خطأ', html: errorMessage });
                }
            });
        });
    });
</script>
@endsection
