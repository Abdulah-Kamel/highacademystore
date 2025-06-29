<form id="formEdit" action="{{route('students.update',$obj->id)}}" method="post">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-sm-12">
            <div class="col mb-12">
                <label for="nameEx7" class="form-label">الاسم</label>
                <input class="form-control" name="name" placeholder="ادخل الاسم" type="text" value="{{$obj->name}}"
                       data-validation="required">
            </div>
        </div>
        <div class="col-sm-12">
            <label for="nameEx7" class="form-label">رقم تليفون الطالب</label>
            <input class="form-control" name="phone" placeholder="ادخل الاسم" type="text" value="{{$obj->phone}}"
                   data-validation="required">
        </div>
        <div class="col-sm-12">
            <label for="nameEx7" class="form-label">رقم تليفون ولي الامر</label>
            <input class="form-control" name="parent_phone" placeholder="ادخل الاسم" type="text" value="{{$obj->parent_phone}}"
                   data-validation="required">
        </div>
        <div class="col-sm-12">
            <label for="nameEx7" class="form-label">العنوان</label>
            <input class="form-control" name="address" placeholder="ادخل الاسم" type="text" value="{{$obj->address}}"
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
        <div class="col-sm-12 sessions">
            <label for="nameEx7" class="form-label"> الحصة</label>
            <select id="nameEx7" name="sessionsIDS[]" class="select2 form-select" multiple
                    data-placeholder="اختر الحصة">
                @foreach($sessions as $session)
                    <option
                        value="{{$session->id}}" {{in_array($session->id , $studentSessionsIDS) ? 'selected'  : ''}}>{{$session->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col mb-12 mt-3">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
            اغلاق
        </button>
        <button type="submit" class="btn btn-primary">حفظ</button>
    </div>
</form>
@include('Admin.students.jsStudent')

