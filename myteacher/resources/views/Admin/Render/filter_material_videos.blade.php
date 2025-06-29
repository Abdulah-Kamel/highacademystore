@foreach($videos as $video)
    <div class="col-sm-6 col-lg-4">
        <div class="card p-2 h-100 shadow-none border">
            <div class="rounded-2 text-center mb-3">
                <a href="">
                    <img class="img-fluid" src="{{asset('').$video->image}}" alt="tutor image 1"/></a>
            </div>
            <div class="card-body p-3 pt-2">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-label-primary">{{$video->chapters->name}}</span>
                </div>
                <a href="" class="h5">{{$video->title}}</a>
                <br>
                <div class="d-flex flex-column flex-md-row gap-2 text-nowrap">
                    <a class="app-academy-md-50 btn btn-label-secondary me-md-2 d-flex align-items-center"
                       href="{{$video->video}}" download>
                        <i class="ti ti-rotate-clockwise-2 align-middle scaleX-n1-rtl  me-2 mt-n1 ti-sm"></i><span>تحميل</span>
                    </a>
                    <a class="app-academy-md-50 btn btn-label-primary d-flex align-items-center"
                       href="{{route('students_material-videos-details.show',$video->id)}}">
                        <span class="me-2">مشاهدة</span><i
                            class="ti ti-chevron-right scaleX-n1-rtl ti-sm"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach
