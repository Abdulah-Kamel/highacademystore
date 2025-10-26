@extends('admin.layouts.master')
@section('title')
    عرض السؤال والإجابة
@endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">عرض السؤال والإجابة</h6>
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
                <div class="row">
                    <div class="col-md-8 mx-auto">
                        <div class="faq-details">
                            <div class="mb-4">
                                <h3 class="text-primary mb-3">
                                    <i class="fa fa-question-circle me-2"></i>السؤال
                                </h3>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-0 fs-5">{{ $faq->question }}</p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-success mb-3">
                                    <i class="fa fa-check-circle me-2"></i>الإجابة
                                </h3>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-0" style="white-space: pre-wrap;">{{ $faq->answer }}</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h5 class="text-muted">الترتيب</h5>
                                        <p class="mb-0">
                                            <span class="badge bg-info">{{ $faq->display_order }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h5 class="text-muted">الحالة</h5>
                                        <p class="mb-0">
                                            {!! $faq->status_badge !!}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h5 class="text-muted">تاريخ الإنشاء</h5>
                                        <p class="mb-0">{{ $faq->created_at->format('Y/m/d H:i') }}</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h5 class="text-muted">آخر تحديث</h5>
                                        <p class="mb-0">{{ $faq->updated_at->format('Y/m/d H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('dashboard.faqs') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-right me-2"></i>العودة للقائمة
                            </a>
                            <div>
                                <a href="{{ route('dashboard.faqs.edit', $faq) }}" class="btn btn-primary me-2">
                                    <i class="fa fa-edit me-2"></i>تعديل
                                </a>
                                <button class="btn btn-danger" onclick="deleteFaq({{ $faq->id }})">
                                    <i class="fa fa-trash me-2"></i>حذف
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    function deleteFaq(id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'لن تتمكن من استرجاع هذا السؤال والإجابة بعد الحذف!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("dashboard.faqs.destroy", $faq) }}',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'تم الحذف!',
                                response.message,
                                'success'
                            ).then(() => {
                                window.location.href = '{{ route("dashboard.faqs") }}';
                            });
                        } else {
                            Swal.fire(
                                'خطأ!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'خطأ!',
                            'حدث خطأ أثناء حذف السؤال والإجابة',
                            'error'
                        );
                    }
                });
            }
        });
    }
</script>
@endsection
