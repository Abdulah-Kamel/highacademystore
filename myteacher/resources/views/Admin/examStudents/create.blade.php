<form id="formAdd" action="{{route('exams-students.store')}}" method="post">
    @csrf
    <div class="row">
        <div class="col mb-12">
            <label for="nameEx7" class="form-label">درجة الطالب</label>
            <input class="form-control" name="student_degree" placeholder="سجل درجه الطالب" type="text" value=""
                   data-validation="required">
        </div>
    </div>
    <div class="col mb-12 mt-3">

        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
            اغلاق
        </button>
        <button type="submit" class="btn btn-primary">حفظ</button>
    </div>
</form>
@include("Admin.examStudents.jsStudentDegree")
