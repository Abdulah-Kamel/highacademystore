@extends('user.layouts.master')
@section('title')
    High Academy Store - المتجر
@endsection
@section('content')
    <!-- Shop Start -->
    <div class="container-fluid pt-5">
        <div class="row px-xl-5 pt-5">
            <!-- Shop Product Start -->
            <div class="col-12 mt-5">
                <div class="col-12 pb-1">
                    {{-- <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                    </div> --}}
                    {{-- <div class="ml-2"> --}}
                    <form action="{{ route('user.shop') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-12 d-flex flex-wrap justify-content-center align-items-center gap-2 mb-4">
                                <input name="title" class="form-control btn-lg rounded-pill w-100 search_input"
                                    style="border: 1px solid #ccc;"
                                    @if (request('main_category_id') == 13) placeholder="ابحث عن اسم المنتج"
                                       @else
                                           placeholder="ابحث عن اسم الكتاب" @endif
                                    value="{{ request('title') }}">
                            </div>
                            @if (!request('color') && !request('size'))
                                @if (request('main_category_id') != 13)
                                    <div class="col-sm-6">
                                        <select name="main_category_id" class="form-select dropdown btn-lg rounded-pill"
                                            id="main-category-select"
                                            style="margin-bottom: 5px; border:1px solid #ffd700; text-align:left">
                                            <option value="">القسم</option>
                                            @foreach ($main_categories as $main_category)
                                                <option value="{{ $main_category->id }}"
                                                    {{ request('main_category_id') == $main_category->id ? 'selected' : '' }}>
                                                    {{ $main_category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                @if (request('main_category_id') != 13)
                                    <div class="col-sm-6">
                                        <select name="stage_id" class="form-select dropdown btn-lg rounded-pill"
                                            id="stage-select"
                                            style="margin-bottom: 5px; border:1px solid #ffd700; text-align:left">
                                            <option value="">المرحلة التعليمية</option>
                                            @foreach ($stages as $stage)
                                                <option value="{{ $stage->id }}"
                                                    {{ request('stage_id') == $stage->id ? 'selected' : '' }}>
                                                    {{ $stage->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-sm-6">
                                        <select name="slider_id" class="form-select dropdown btn-lg rounded-pill"
                                            id="slider-select"
                                            style="margin-bottom: 5px; border:1px solid #ffd700; text-align:left"
                                            {{ !request('stage_id') ? 'disabled' : '' }}>
                                            <option value="">الصف الدراسي</option>
                                            @foreach ($sliders as $slider)
                                                <option value="{{ $slider->id }}"
                                                    data-stage-id="{{ $slider->stage_id }}"
                                                    {{ request('slider_id') == $slider->id ? 'selected' : '' }}>
                                                    {{ $slider->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-sm-6">
                                        <select name="category_id" class="form-select dropdown btn-lg rounded-pill"
                                            style="margin-bottom: 5px; border:1px solid #ffd700; text-align:left">
                                            <option value="">المواد</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-sm-6">
                                        <select name="brand_id" class="form-select dropdown btn-lg rounded-pill"
                                            style="margin-bottom: 5px; border:1px solid #ffd700; text-align:left">
                                            <option value="">المدرسين</option>
                                            @foreach ($teachers as $teacher)
                                                <option value="{{ $teacher->id }}"
                                                    {{ request('brand_id') == $teacher->id ? 'selected' : '' }}>
                                                    {{ $teacher->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            @endif
                            <div class="col-12">
                                <button type="submit" class="btn-light  mb-5 rounded-pill px-5 py-2 text-bold"
                                    style="border: none;">بحث
                                </button>
                            </div>

                        </div>
                    </form>

                    {{--
                    </div> --}}
                    {{--
                </div> --}}
                </div>
                <div class="row pb-3 g-5">
                    @include('user.layouts.product')
                </div>
                <div class="row">
                    <div class="col-12 d-flex justify-content-center mt-4">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
            <!-- Shop Product End -->
        </div>
    </div>
    <!-- Shop End -->
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stageSelect = document.getElementById('stage-select');
            const sliderSelect = document.getElementById('slider-select');
            
            // Store all slider options for filtering
            const allSliders = Array.from(sliderSelect.options);

            // Function to filter sliders based on selected stage
            function filterSliders(selectedStageId) {
                // Clear current options except the first one
                sliderSelect.innerHTML = '<option value="">الصف الدراسي</option>';
                
                if (selectedStageId) {
                    // Enable slider select
                    sliderSelect.disabled = false;
                    
                    // Filter and add matching sliders
                    const filteredSliders = allSliders.filter(option => 
                        option.dataset.stageId === selectedStageId
                    );
                    filteredSliders.forEach(option => {
                        sliderSelect.appendChild(option.cloneNode(true));
                    });
                } else {
                    // Disable slider select when no stage is selected
                    sliderSelect.disabled = true;
                    sliderSelect.value = '';
                }
            }

            // Handle stage selection change
            stageSelect.addEventListener('change', function() {
                const selectedStageId = this.value;
                filterSliders(selectedStageId);
            });

            // Initialize on page load
            const initialStageId = stageSelect.value;
            if (initialStageId) {
                filterSliders(initialStageId);
            } else {
                sliderSelect.disabled = true;
            }
        });
    </script>
@endsection
<!--Start of Tawk.to Script-->
<script type="text/javascript">
    var Tawk_API = Tawk_API || {},
        Tawk_LoadStart = new Date();
    (function() {
        var s1 = document.createElement("script"),
            s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/6831a51ce923fe1912297913/1is0upujk';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
</script>
<!--End of Tawk.to Script-->
