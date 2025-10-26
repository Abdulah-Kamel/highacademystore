@extends('admin.layouts.master')
@section('title')
    تعديل السؤال والإجابة
@endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">تعديل السؤال والإجابة</h6>
                <div class="dropdown morphing scale-left">
                    <a href="#" class="card-fullscreen" data-bs-toggle="tooltip" title="Card Full-Screen"><i
                            class="icon-size-fullscreen"></i></a>
                    <a href="#" class="more-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i
                            class="fa fa-ellipsis-h"></i></a>
                    <ul class="dropdown-menu shadow border-0 p-2">
                        <li><a class="dropdown-item" href="#">File Info</a></li>
                        <li><a class="dropdown-item" href="#">Copy to</a></li>
                        <li><a class="dropdown-item" href="#">Move to</a></li>
                        <li><a class="dropdown-item" href="#">Rename</a></li>
                        <li><a class="dropdown-item" href="#">Block</a></li>
                        <li><a class="dropdown-item" href="#">Delete</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('dashboard.faqs.update', $faq) }}" method="POST" id="faqForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="question" class="form-label">السؤال <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('question') is-invalid @enderror"
                                       id="question"
                                       name="question"
                                       value="{{ old('question', $faq->question) }}"
                                       placeholder="أدخل السؤال هنا"
                                       required>
                                @error('question')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="display_order" class="form-label">الترتيب</label>
                                <input type="number"
                                       class="form-control @error('display_order') is-invalid @enderror"
                                       id="display_order"
                                       name="display_order"
                                       value="{{ old('display_order', $faq->display_order) }}"
                                       placeholder="اتركه فارغ للترتيب التلقائي"
                                       min="0">
                                @error('display_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">سيتم ترتيب الأسئلة حسب هذا الرقم (الأصغر أولاً)</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="answer" class="form-label">الإجابة <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('answer') is-invalid @enderror"
                                  id="answer"
                                  name="answer"
                                  rows="6"
                                  placeholder="أدخل الإجابة هنا"
                                  required>{{ old('answer', $faq->answer) }}</textarea>
                        @error('answer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror"
                                id="status"
                                name="status"
                                required>
                            <option value="">اختر الحالة</option>
                            <option value="active" {{ old('status', $faq->status) == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ old('status', $faq->status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('dashboard.faqs') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-right me-2"></i>العودة للقائمة
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-2"></i>تحديث السؤال والإجابة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    // Auto-resize textarea
    document.getElementById('answer').addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Form validation
    document.getElementById('faqForm').addEventListener('submit', function(e) {
        const question = document.getElementById('question').value.trim();
        const answer = document.getElementById('answer').value.trim();

        if (question === '') {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب إدخال السؤال'
            });
            return false;
        }

        if (answer === '') {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب إدخال الإجابة'
            });
            return false;
        }

        return true;
    });
</script>
@endsection
