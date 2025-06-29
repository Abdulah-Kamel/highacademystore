<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="card-title mb-0">
            <h4 class="mb-0">اوائل الامتحان</h4>
        </div>
    </div>
    <div class="card-body">
        <ul class="list-unstyled mb-0">
            @forelse($students as $student)
                <li class="mb-3 pb-1">
                    <div class="d-flex align-items-start">
                        <div class="badge bg-label-secondary p-2 me-3 rounded"><i
                                class="ti ti-user"></i></div>
                        <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">{{$student->name}}</h6>
                            </div>
                            <div class="me-2">
                                <h6 class="mb-0">{{@$student->grades->name}}</h6>
                            </div>
                            <div class="d-flex align-items-center">
                                <p class="mb-0 badge bg-label-success">+{{$student->student_degree}}</p>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
            @endforelse

        </ul>
    </div>
</div>
