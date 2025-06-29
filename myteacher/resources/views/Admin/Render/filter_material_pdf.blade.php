@foreach($pdfs as $pdf)
    <div class="col-sm-6 col-lg-4">
        <div class="card p-2 h-100 shadow-none border">
            <div class="card-body p-3 pt-2">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-label-primary">{{$pdf->chapters->name}}</span>
                </div>
                <a href="" class="h5">{{$pdf->title}}</a>
                <br>
                <div class="d-flex flex-column flex-md-row gap-2 text-nowrap mt-3">
                    <a class="app-academy-md-50 btn btn-label-secondary me-md-2 d-flex align-items-center"
                       href="{{$pdf->file}}" download>
                        <i class="ti ti-rotate-clockwise-2 align-middle scaleX-n1-rtl  me-2 mt-n1 ti-sm"></i><span>تحميل</span>
                    </a>
                    <a class="app-academy-md-50 btn btn-label-primary d-flex align-items-center"
                       href="{{$pdf->file}}">
                        <span class="me-2">مشاهدة</span><i
                            class="ti ti-chevron-right scaleX-n1-rtl ti-sm"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach
