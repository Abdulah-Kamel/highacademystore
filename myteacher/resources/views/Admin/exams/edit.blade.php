<form id="formEdit" action="{{route('exams.update',$obj->id)}}" method="post" enctype=multipart/form-data>
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-sm-12">
            <label for="nameEx7" class="form-label">عنوان الامتحان</label>
            <input class="form-control" name="title" placeholder="ادخل العنوان" type="text" value="{{$obj->title}}"
                   data-validation="required">
        </div>
        <div class="col-sm-12">
            <label for="nameEx7" class="form-label">تاريخ الامتحان</label>
            <input class="form-control" name=date placeholder="ادخل العنوان" type="date" value="{{$obj->date}}"
                   data-validation="required">
        </div>
        <div class="col-sm-12">
            <label for="nameEx7" class="form-label">درجة الامتحان</label>
            <input class="form-control" name=degree placeholder="ادخل الدرجة" type="number" value="{{$obj->degree}}"
                   data-validation="required">
        </div>
        <div class="col-sm-12">
            <label for="grade_id" class="form-label">الصف الدراسي</label>
            <select id="grade_id" name="grade_id" class="form-control" data-placeholder="اختر الصف الدراسي">
                @foreach($grades as $grade)
                    <option
                        value="{{$grade->id}}" {{$grade->id == $obj->grade_id ? 'selected' : ''}}>{{$grade->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="mb-3 mt-3">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
            اغلاق
        </button>
        <button type="submit" class="btn btn-primary">حفظ</button>
    </div>
</form>
@include("Admin.exams.jsExam")


