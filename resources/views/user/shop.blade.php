@extends('user.layouts.master')

@section('title')
online shop
@endsection

@section('content')
<!-- Shop Start -->
<div class="container-fluid">
    <div class="row px-xl-5">
        <!-- Shop Product Start -->
        <div class="col-12">
            <div class="col-12 pb-1">
                {{-- <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                    </div> --}}
                    {{-- <div class="ml-2"> --}}
                      <form action="{{ route('user.shop') }}" method="GET">
    <div class="row">
        <div class="col-12">
            <input name="title" class="form-controller btn-lg" style="border: none; margin-bottom: 5px;" placeholder="ابحث عن اسم الكتاب" value="{{ request('title') }}">
        </div>
          <div class="col-12">
            <select name="stage_id" class="form-select dropdown btn-lg" id="stage-select" style="margin-bottom: 5px; border:1px solid #ffd700; text-align:left">
                <option value="">المرحلة التعليمية</option>
                @foreach ($stages as $stage)
                <option value="{{ $stage->id }}" {{ request('stage_id') == $stage->id ? 'selected' : '' }}>{{ $stage->title }}</option>
                @endforeach
            </select>
        </div>
        
         <div class="col-12">
            <select name="slider_id" class="form-select dropdown btn-lg" id="slider-select" style="margin-bottom: 5px; border:1px solid #ffd700; text-align:left">
                <option value="">الصف الدراسي</option>
                @foreach ($sliders as $slider)
                <option value="{{ $slider->id }}" data-stage-id="{{ $slider->stage_id }}" {{ request('slider_id') == $slider->id ? 'selected' : '' }}>{{ $slider->title }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="col-12">
            <select name="category_id" class="form-select dropdown btn-lg" style="margin-bottom: 5px; border:1px solid #ffd700; text-align:left">
                <option value="">المواد</option>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <select name="brand_id" class="form-select dropdown btn-lg" style="margin-bottom: 5px; border:1px solid #ffd700; text-align:left">
                <option value="">المدرسين</option>
                @foreach ($teachers as $teacher)
                <option value="{{ $teacher->id }}" {{ request('brand_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->title }}</option>
                @endforeach
            </select>
        </div>
      
       
        <div class="col-12">
            <button type="submit" class="btn-light btn-lg mb-5" style="border: none;">بحث</button>
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
        </div>
        <!-- Shop Product End -->
    </div>
</div>
<!-- Shop End -->
@endsection

@section("js")
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const stageSelect = document.getElementById('stage-select');
    const sliderSelect = document.getElementById('slider-select');
    const allSliders = Array.from(sliderSelect.options);

    stageSelect.addEventListener('change', function() {
        const selectedStageId = this.value;
        sliderSelect.innerHTML = '<option value="">الصف الدراسي</option>'; // Reset the slider options

        if (selectedStageId) {
            const filteredSliders = allSliders.filter(option => option.dataset.stageId === selectedStageId);
            filteredSliders.forEach(option => sliderSelect.appendChild(option));
        }
    });
});

</script>
@endsection
