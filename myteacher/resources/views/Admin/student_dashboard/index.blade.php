@extends('Admin.layout.index')
@section('title')
    الصفحة الرئيسية
@endsection
@section('content')
    <div class="card bg-transparent shadow-none my-4 border-0">
        <div class="card-body row p-0 pb-3">
            <div class="col-12 col-md-8 card-separator">
                <h3>مرحبا بك , {{\Illuminate\Support\Facades\Auth::guard('student')->user()->name}} 👋🏻 </h3>
                <div class="col-12 col-lg-7">
                    <p>تابع مستواك وتقدمك بشكل يومي حتي تحصل علي نتائج أفضل دائما !</p>
                </div>
                <div class="d-flex justify-content-between flex-wrap gap-3 me-5">
                    <div class="d-flex align-items-center gap-3 me-4 me-sm-0">
          <span class="bg-label-primary p-2 rounded">
            <i class='ti ti-device-laptop ti-xl'></i>
          </span>
                        <div class="content-right">
                            <p class="mb-0">الملفات الدراسية </p>
                            <h4 class="text-primary mb-0">{{\App\Models\MaterialPdf::count()}}</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
          <span class="bg-label-info p-2 rounded">
            <i class='ti ti-bulb ti-xl'></i>
          </span>
                        <div class="content-right">
                            <p class="mb-0">الفيديوهات الدراسية </p>
                            <h4 class="text-info mb-0">{{\App\Models\MaterialVideo::count()}}</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
          <span class="bg-label-warning p-2 rounded">
            <i class='ti ti-file-analytics ti-xl'></i>
          </span>
                        <div class="content-right">
                            <p class="mb-0">امتحاناتك </p>
                            <h4 class="text-warning mb-0">{{count(\Illuminate\Support\Facades\Auth::guard('student')->user()->exams)}}</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
          <span class="bg-label-danger p-2 rounded">
            <i class='ti ti-discount-check ti-xl'></i>
          </span>
                        <div class="content-right">
                            <p class="mb-0">عدد مرات الغياب </p>
                            <h4 class="text-warning mb-0">{{count(\Illuminate\Support\Facades\Auth::guard('student')->user()->getCountAttendance)}}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4 g-4">
        <div class="col-12 col-xl-4 col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">الفيديوهات الاكثر مشاهدة</h5>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="topCourses" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach($mostWatchedVideos as $video)
                            <li class="d-flex mb-4 pb-1 align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-video ti-md"></i></span>
                                </div>
                                <div class="row w-100 align-items-center">
                                    <div class="col-sm-8 col-lg-12 col-xxl-8 mb-1 mb-sm-0 mb-lg-1 mb-xxl-0">
                                        <p class="mb-0 fw-medium">{{ $video->material_video->title }}</p>
                                    </div>
                                    <div class="col-sm-4 col-lg-12 col-xxl-4 d-flex justify-content-sm-end justify-content-md-start justify-content-xxl-end">
                                        <div class="badge bg-label-secondary">{{ $video->views_count }} مشاهدة</div>
                                    </div>
                                </div>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4 col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">الامتحانات الخاصة بك</h5>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="topCourses" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach(\Illuminate\Support\Facades\Auth::guard('student')->user()->exams as $exam)
                            <li class="d-flex mb-4 pb-1 align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-file-analytics ti-md"></i></span>
                                </div>
                                <div class="row w-100 align-items-center">
                                    <div class="col-sm-8 col-lg-12 col-xxl-8 mb-1 mb-sm-0 mb-lg-1 mb-xxl-0">
                                        <p class="mb-0 fw-medium">{{ $exam->title }}</p>
                                    </div>
                                    <div class="col-sm-4 col-lg-12 col-xxl-4 d-flex justify-content-sm-end justify-content-md-start justify-content-xxl-end">
                                        <div class="badge bg-label-secondary">{{ $exam->degree }}</div>
                                    </div>
                                </div>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>
        </div>

    </div>
@endsection
