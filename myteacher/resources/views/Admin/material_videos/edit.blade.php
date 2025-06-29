<form id="formEdit" action="{{route('material-videos.update',$obj->id)}}" method="post" enctype=multipart/form-data>
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-sm-12">
            <label for="nameEx7" class="form-label">اسم الفيديو </label>
            <input class="form-control" name="title" placeholder="ادخل اسم الملف" type="text" value="{{$obj->title}}"
                   data-validation="required">
        </div>
        <div class="col-sm-12">
            <label for="nameEx7" class="form-label"> صورة الفيديو </label>
            <input class="form-control" name="image" type="file">
        </div>
        <div class="col-sm-12">
            <label for="nameEx7" class="form-label"> الفيديو </label>
            <input class="form-control" name="video" type="file">
        </div>
        <br>
        <div class="col-sm-12">
            <label for="nameEx7" class="form-label">اسم الفصل الدراسي </label>
            <select class="form-control" name="chapter_id">
                <option value=" ">اختر الفصل الدراسي</option>
                @foreach($chapters as $chapter)
                    <option
                        value="{{$chapter->id}}" {{$chapter->id == $obj->chapter_id ? 'selected' : ''}}>{{$chapter->name}}</option>
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
@include("Admin.material_videos.jsMaterialVideo")


