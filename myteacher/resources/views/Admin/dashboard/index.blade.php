@extends('Admin.layout.index')
@section('title')
    الرئيسية
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <a href="{{route('grades.index')}}">
                        <div class="d-flex justify-content-between">
                            <h4 class="d-block mb-1 text-muted">الصفوف الدراسية</h4>
                            <h3 class="card-text text-success">+{{$data['grades']}}</h3>
                        </div>
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">

                        </div>
                        <div class="col-4">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <a href="{{route('sessions.index')}}">
                        <div class="d-flex justify-content-between">
                            <h4 class="d-block mb-1 text-muted">الحصص</h4>
                            <h3 class="card-text text-success">+{{$data['sessions']}}</h3>
                        </div>
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">

                        </div>
                        <div class="col-4">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <a href="{{route('students.index')}}">
                        <div class="d-flex justify-content-between">
                            <h4 class="d-block mb-1 text-muted">الطلاب</h4>
                            <h3 class="card-text text-success">+{{$data['students']}}</h3>
                        </div>
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">

                        </div>
                        <div class="col-4">

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-lg-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <a href="{{route('exams.index')}}">
                        <div class="d-flex justify-content-between">
                            <h4 class="d-block mb-1 text-muted">الامتحانات</h4>
                            <h3 class="card-text text-success">+{{$data['exams']}}</h3>
                        </div>
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">

                        </div>
                        <div class="col-4">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--/ Monthly Campaign State -->

        <!-- Source Visit -->
        <div class="col-xl-3 col-md-6 order-2 order-lg-1 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title mb-0">
                        <h4 class="mb-0" style="font-size: 18px;font-weight: bold; color: #6b69ce">أكثر الطلاب غيابا في الشهر الحالي</h4>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @forelse($absentStudents as $ABStudent)
                            <li class="mb-3 pb-1">
                                <div class="d-flex align-items-start">
                                    <div class="badge bg-label-secondary p-2 me-3 rounded"><i
                                            class="ti ti-user"></i></div>
                                    <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
                                        <div class="me-2">
                                            <h6 class="mb-0">{{$ABStudent->name}}</h6>
                                        </div>
                                        <div class="me-2">
                                            <h6 class="mb-0">{{@$ABStudent->grades->name}}</h6>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <p class="mb-0 badge bg-label-danger">+{{$ABStudent->total_absences}}</p>

                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                        @endforelse

                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 order-2 order-lg-1 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title mb-0">
                        <h4 class="mb-0" style="font-size: 17px;font-weight: bold; color: #6b69ce"> أكثر الطلاب حضورا في الشهر الحالي</h4>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @forelse($attendingStudents as $ATStudent)
                            <li class="mb-3 pb-1">
                                <div class="d-flex align-items-start">
                                    <div class="badge bg-label-secondary p-2 me-3 rounded"><i
                                            class="ti ti-user"></i></div>
                                    <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
                                        <div class="me-2">
                                            <h6 class="mb-0">{{$ATStudent->name}}</h6>
                                        </div>
                                        <div class="me-2">
                                            <h6 class="mb-0">{{@$ATStudent->grades->name}}</h6>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <p class="mb-0 badge bg-label-success">+{{$ATStudent->total_absences}}</p>

                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty

                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 order-2 order-lg-1 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title mb-0">
                        <h4 class="mb-0" style="font-size: 14px;font-weight: bold; color: #6b69ce">الطلاب الذي  لم يدفعوا فلوس الشهر الحالي</h4>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @forelse($notPaidStudents as $PAStudent)
                            <li class="mb-3 pb-1">
                                <div class="d-flex align-items-start">
                                    <div class="badge bg-label-secondary p-2 me-3 rounded"><i
                                            class="ti ti-user"></i></div>
                                    <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
                                        <div class="me-2">
                                            <h6 class="mb-0">{{$PAStudent->name}}</h6>
                                        </div>
                                        <div class="me-2">
                                            <h6 class="mb-0">{{@$PAStudent->grades->name}}</h6>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty

                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 order-2 order-lg-1 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title mb-0">
                        <h4 class="mb-0" style="font-size: 18px;font-weight: bold; color: #6b69ce">الطلاب الاونلاين حاليا</h4>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0" id="students-list">



                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
