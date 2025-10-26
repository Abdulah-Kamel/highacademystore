@extends('user.layouts.master')

@section('title')
    الأسئلة الشائعة
@endsection

@section('content')
    <h1>الأسئلة الشائعة</h1>
    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="accordion mt-5" id="accordionExample" dir="rtl" style="text-align: right;">
            @forelse(\App\Models\Faq::active()->ordered()->get() as $index => $faq)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $index }}">
                        <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse{{ $index }}"
                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                            aria-controls="collapse{{ $index }}">
                            <span style="flex: 1; text-align: right;">
                                {{ $faq->question }}
                            </span>
                        </button>
                    </h2>
                    <div id="collapse{{ $index }}"
                         class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                         aria-labelledby="heading{{ $index }}"
                         data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            {!! nl2br(e($faq->answer)) !!}
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle fa-2x mb-3"></i>
                        <h4>لا توجد أسئلة شائعة حالياً</h4>
                        <p>سيتم إضافة الأسئلة الشائعة قريباً.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
