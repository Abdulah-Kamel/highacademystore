<form id="formEdit" action="{{route('sessions.update',$obj->id)}}" method="post">
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
            <label for="nameEx7" class="form-label">تبدء في</label>
            <input class="form-control" name="start_from" placeholder="ادخل الاسم" type="time"
                   value="{{$obj->start_from}}"
                   data-validation="required">
        </div>
        <div class="col-sm-12">
            <label for="nameEx7" class="form-label">تنتهي في</label>
            <input class="form-control" name="start_to" placeholder="ادخل الاسم" type="time" value="{{$obj->start_to}}"
                   data-validation="required">
        </div>
        <div class="col-sm-12">
            <label for="nameEx7" class="form-label">الصف الدراسي</label>
            <select id="nameEx7" name="grade_id" class="form-control" data-placeholder="اختر الصف الدراسي">

                @foreach($grades as $grade)
                    <option
                        value="{{$grade->id}}" {{$grade->id == $obj->grade_id ? 'selected' : ''}}>{{$grade->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-12">
            <label for="nameEx7" class="form-label"> الايام</label>
            <select id="nameEx7" name="days[]" class="select2 form-select" multiple data-placeholder="اختر اليوم">
                @foreach($days as $day)
                    <option value="{{$day['english']}}" {{in_array($day['english'] , $sessionDays) ? 'selected' : ''}} >{{$day['arabic']}}</option>
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
@include('Admin.sessions.jsSession')

