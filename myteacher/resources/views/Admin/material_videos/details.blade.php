@extends('Admin.layout.index')
@section('title')
@endsection
@section('content')
    <div class="card g-3 mt-5">
        <div class="card-body row g-3">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-2 gap-1">
                    <div class="me-1">
                        <h5 class="mb-1">{{$video->title}}</h5>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-label-danger">{{$video->chapters->name}}</span>
                    </div>
                </div>
                <div class="card academy-content shadow-none border">
                    <div class="p-2">
                        <div class="cursor-pointer">
                            <video class="w-100" poster="{{asset('').$video->image}}" id="plyr-video-player" playsinline
                                   controls>
                                <source src="{{asset('').$video->video}}" type="video/mp4"/>
                            </video>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-4">
                <div class="accordion stick-top accordion-bordered" id="courseContent">
                    @foreach($chapters as $index => $chapter)
                        <div class="accordion-item mb-0">
                            <div class="accordion-header" id="heading{{ $index }}">
                                <button type="button" class="accordion-button bg-lighter rounded-0"
                                        data-bs-toggle="collapse" data-bs-target="#chapter{{ $index }}"
                                        aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                                        aria-controls="chapter{{ $index }}">
                <span class="d-flex flex-column">
                    <span class="h5 mb-1">{{ $chapter->name }}</span>
                </span>
                                </button>
                            </div>
                            <div id="chapter{{ $index }}"
                                 class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                                 data-bs-parent="#courseContent">
                                <div class="accordion-body py-3 border-top">
                                    @foreach($chapter->materialsVideos as $videoIndex => $video)
                                        <div class="form-check d-flex align-items-center mb-3">
                                            <label for="defaultCheck{{ $index }}-{{ $videoIndex }}"
                                                   class="form-check-label ms-3">
                                               <a href="{{route('students_material-videos-details.show',$video->id)}}"> <span class="mb-0 h6">{{ $videoIndex + 1 }}. {{ $video->title }}</span></a>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
@endsection
